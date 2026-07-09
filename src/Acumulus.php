<?php
/**
 * @noinspection LongLine
 * @noinspection SpellCheckingInspection Lots of spell check hints
 * @noinspection JsonEncodingApiUsageInspection
 * @noinspection HtmlDeprecatedAttribute
 * @noinspection HtmlDeprecatedTag
 * @noinspection XmlDeprecatedElement
 */

declare(strict_types=1);

namespace Siel\Whmcs\Acumulus;

use InvalidArgumentException;
use RuntimeException;
use Siel\Acumulus\Helpers\Container;
use SimpleXMLElement;
use Throwable;
use WHMCS\Config\Setting;
use WHMCS\Database\Capsule;

use function count;
use function get_class;
use function in_array;
use function is_scalar;
use function sprintf;

/**
 * Acumulus contains WHMCS Addon specific code.
 *
 * @noinspection PhpClassHasTooManyDeclaredMembersInspection
 */
class Acumulus
{
    public const Name = 'Acumulus';
    public const Version = '4.0';

    private static ?Acumulus $instance = null;

    /**
     * Returns the singleton instance.
     */
    public static function getInstance(): static
    {
        return static::$instance ??= new static();
    }

    protected Container $acumulusContainer;

    private function __construct()
    {
        $this->boot();
    }

    /**
     * Boots the extension.
     * This is the function to set up the environment of the extension like registering
     * new class loaders, etc.
     */
    public function boot(): void
    {
        if (!isset($this->acumulusContainer)) {
            $shopNameSpace = 'Whmcs';
            $language = $_SESSION['Language'] ?? $_SESSION['adminlang'] ?? Setting::getValue('Language');
            $this->acumulusContainer = new Container($shopNameSpace, $language);
        }
    }

    public function getName(): string
    {
        return self::Name;
    }

    public function getVersion(): string
    {
        return self::Version;
    }

    public function getAcumulusContainer(): Container
    {
        return $this->acumulusContainer;
    }

    /**
     * Function to return the configuration fields for the Acumulus module.
     */
    public function config(): array
    {
        $config = $this->get_config();

        //Check if any credentials are given or show the basic config.
        if ((!empty($config['acumulus_code'])) && (!empty($config['acumulus_username'])) && (!empty($config['acumulus_password']))) {
            // Construct the xml for the Acumulus API check

            $xml = $this->basicXml(false); //construct the basic xml without email on errors or warnings.
            $xml->addChild('format', 'xml');
            $xml_string = urlencode($xml->asXML());

            // Let's check the credentials against the Acumulus API.
            $url = 'https://api.sielsystems.nl/acumulus/stable/general/general_about.php';
            /** @noinspection DuplicatedCode */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);

            // Let's check if the results with the entered credentials are valid,
            // if so display the full config or show credentials mismatch.
            $xml = simplexml_load_string($response);
            if ((string) $xml->general->about) {
                $config_array = $this->constructFullConfigFields();
            } else {
                // the entered credentials are not valid.
                $config_array = $this->constructBasicConfigFields();
                $config_array['fields']['acumulus_credentials_check'] = [
                    'FriendlyName' => 'Credential check',
                    'Description' => '<font color="red"><b>The credentials are not correct.</b></font>',
                ];
            }
        } else {
            // one or more required credentials are not given.
            $config_array = $this->constructBasicConfigFields();
            $config_array['fields']['acumulus_credentials_check'] = [
                'FriendlyName' => 'Credential check',
                'Description' => '<font color="blue"><b>Please enter your credentials and click "Save Changes", to continue configuring the Acumulus module.</b></font>',
            ];
        }

        return $config_array;
    }

    /**
     * Performs custom actions on activating this module.
     * - Create DB table
     *
     * @return string[]
     *   Result of the activation: 2 strings keyed by 'status''and 'description'.
     */
    public function activate(): array
    {
        try {
            // @todo: move to AcumulusEntryManager.
            Capsule::schema()->create(
                'mod_acumulus_connect',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('token', 40);
                    $table->string('entryid', 20);
                    $table->timestamps();
                }
            );
            return [
                'status' => 'success',
                'description' => 'The Acumulus addon has been installed successfully, please continue by filling in the configuration data.',
            ];
        } catch (Throwable $e) {
            $this->logException($e);
            return [
                'status' => 'error',
                'description' => 'Installing the addon failed Unable to create table mod_acumulus_connect: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Performs custom actions on deactivating this module.
     *
     * - Remove Custom DB Table
     *
     * @return string[]
     *   Result of the deactivation: 2 strings keyed by 'status''and 'description'.
     * @todo
     *   Should we always drop the table or can we ask for confirmation?
     *
     */
    public function deactivate(): array
    {
        try {
            // @todo: move to AcumulusEntryManager.
            Capsule::schema()->drop('mod_acumulus_connect');
            return ['status' => 'success', 'description' => 'The Acumulus module has been deactivated successfully'];
        } catch (Throwable $e) {
            $this->logException($e);
            return ['status' => 'error', 'description' => 'Deactivating the module failed: ' . $e->getMessage()];
        }
    }

    /**
     * Performs custom actions on upgrading this module.
     */
    public function upgrade(array $vars): void
    {
        // Old version
        $version = $vars['version'];

        # Run SQL Updates for V1.x to V2.0
        if ($version < 2.0) {
            try {
                Capsule::schema()->table(
                    'mod_acumulus_connect',
                    function ($table) {
                        /** @var \Illuminate\Database\Schema\Blueprint $table */
                        $table->string('entryid', 20);
                        $table->timestamps();
                    }
                );
                logActivity(__FUNCTION__ . "The Acumulus module has been upgraded successfully from $version to " . self::Version);
            } catch (Throwable $e) {
                $this->logException($e);
            }
        } else {
            logActivity(
                __FUNCTION__ . "The Acumulus module has been upgraded successfully from $version to " . self::Version . ': no update actions were necessary'
            );
        }
    }

    /*
     * Module Additional functions. These functions are called by WHMCS based on
     * naming patterns.
     */
    /**
     * Return HTML to add to the sidebar.
     *
     * @todo: position, needed at all?
     */
    public function sidebar(array $vars): string
    {
        $version = $vars['version'];
        $lang = $vars['_lang'];

        $sidebar = '<span class="header"><img src="images/icons/addonmodules.png" class="absmiddle" width="16" height="16" alt="" />Acumulus</span>
        <ul class="menu">
                <li><a href="#">' . $lang['Version'] . ': ' . $version . '</a></li>';
        if (isset($_SESSION['acumulus_newversion'])) {
            $sidebar .= '<li><a STYLE="color: #FF0000; font-weight: bold;" href="https://forum.acumulus.nl/index.php/topic,4183.0.html" target="_blank">' . $lang['update available'] . '</a></li>';
        }
        $sidebar .= '</ul>';

        return $sidebar;
    }

    /**
     * Renders the main screen: the Acumulus send invoice(s) form.
     *
     * @param array $vars
     *   An array with all information that may be needed. It contains:
     *   - '_lang': the contents of the language file of the user's language.
     *   - 'module': name of this module
     *   - 'modulelink': internal link (part after http(s)example.com/admin/ to the
     *     page that should be output).
     *   - 'version': version of this module
     *   - 'acumulus_...': the complete config of this module.
     *
     * @noinspection PhpFunctionCyclomaticComplexityInspection
     */
    public function output(array $vars): void
    {
        global $_SESSION;
        $lang = $vars['_lang'];
        if (isset($_POST['action'])) {
            if (empty($_POST['resentinvoice']) && ($_POST['action'] === 'sendinvoice')) {
                echo "<br><h2>{$lang['No records found message']}</h2>";
                echo "<br><a href='addonmodules.php?module=acumulus_connect' class='btn btn-warning'>{$lang['Return']}</a>";

                return;
            }
            switch ($_POST['action']) {
                case 'sendinvoice':
                    $invoiceid = $_POST['resentinvoice'];
                    $searchon = $_POST['search_on'];
                    $invoices = [];
                    /** @noinspection DegradedSwitchInspection */
                    switch ($searchon) {
                        case 'invoiceno':
                            $collection = Capsule::table('tblinvoices')->select('id')->where('invoicenum', $invoiceid)->get();
                            if (count($collection) > 0) {
                                $invoices[] = $collection[0]->id;
                            }
                            break;
                        default:
                            $invoices[] = $invoiceid;
                    }
                    if (empty($invoices[0])) {
                        echo '<br><h2>' . $lang['No records found message'] . '</h2>';
                        echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $lang['Return'] . '</a>';

                        return;
                    }

                    echo $this->getInvoicesSummary($invoices, $vars);
                    echo '<form method="post" action="addonmodules.php?module=acumulus_connect">
                <input type="hidden" name="action" value="sendinvoicesnow">
                     ' . $lang['Resent Invoice'] . ' <input type="submit" value="' . $lang['Submit Invoice'] . '" class="btn-success" name="Submit" onclick="return confirm(\'' . $lang['Confirm Send single'] . '\')" />
                    <a href="addonmodules.php?module=acumulus_connect" class="btn">Return</a>
                </p>
                </form>';
                    break;
                case 'sendbatch':
                    $invoices = [];
                    $filterBy = $_POST['filterby'];
                    $filterBy2 = $_POST['filterby2'];
                    $dateFrom = toMySQLDate($_POST['datefrom']);
                    $dateTo = toMySQLDate($_POST['dateto']);

                    // @todo: optimize by building the query step by step.
                    switch ($filterBy) {
                        case 'Date Paid':
                            if ($filterBy2 === 'All Gateways') {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('datepaid', '>=', "$dateFrom 00:00:00")
                                    ->where('datepaid', '<=', "$dateTo 23:59:59")
                                    ->where('status', 'Paid')
                                    ->get();
                            } else {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('datepaid', '>=', "$dateFrom 00:00:00")
                                    ->where('datepaid', '<=', "$dateTo 23:59:59")
                                    ->where('status', 'Paid')
                                    ->where('paymentmethod', $filterBy2)
                                    ->get();
                            }
                            break;
                        case 'Unpaid Invoices':
                            if ($filterBy2 === 'All Gateways') {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('date', '>=', $dateFrom)
                                    ->where('date', '<=', $dateTo)
                                    ->where('status', 'Unpaid')
                                    ->get();
                            } else {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('date', '>=', $dateFrom)
                                    ->where('date', '<=', $dateTo)
                                    ->where('status', 'Unpaid')
                                    ->where('paymentmethod', $filterBy2)
                                    ->get();
                            }
                            break;
                        case 'Paid Invoices by invoicedate':
                            if ($filterBy2 === 'All Gateways') {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('date', '>=', $dateFrom)
                                    ->where('date', '<=', $dateTo)
                                    ->where('status', 'Paid')
                                    ->get();
                            } else {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('date', '>=', $dateFrom)
                                    ->where('date', '<=', $dateTo)
                                    ->where('status', 'Paid')
                                    ->where('paymentmethod', $filterBy2)
                                    ->get();
                            }
                            break;
                        default:
                            if ($filterBy2 === 'All Gateways') {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('date', '>=', $dateFrom)
                                    ->where('date', '<=', $dateTo)
                                    ->get();
                            } else {
                                $results = Capsule::table('tblinvoices')->select('id')
                                    ->where('date', '>=', $dateFrom)
                                    ->where('date', '<=', $dateTo)
                                    ->where('paymentmethod', $filterBy2)
                                    ->get();
                            }
                    }
                    foreach ($results as $result) {
                        $invoices[] = $result->id;
                    }

                    echo '<div class="infobox"><strong><span class="title">' . $lang['Batch import'] . '</span></strong><br />' . $lang['Batch import time warning'] . '</div>';
                    echo $this->getInvoicesSummary($invoices, $vars);
                    echo '<form method="post" action="addonmodules.php?module=acumulus_connect">
                <input type="hidden" name="action" value="sendinvoicesnow">
                     ' . $lang['Sent Above Invoices'] . '&nbsp;&nbsp;&nbsp; <input type="submit" value="' . $lang['Sent Invoices'] . '" class="btn-success" name="Submit" onclick="return confirm(\'' . $lang['Confirm Send'] . '\')" />
                    <a href="addonmodules.php?module=acumulus_connect" class="btn">' . $lang['Return'] . '</a>
                </p>
                </form>';
                    break;
                case 'sendinvoicesnow':
                    if (isset($_SESSION['acumulus_sendinvoices'])) {
                        foreach ($_SESSION['acumulus_sendinvoices'] as $invoiceid) {
                            $this->sendInvoice($vars, $invoiceid);
                        }
                        echo('<div class="infobox"><strong><span class="title">' . $lang['Check activity'] . '</span></strong><br />' . $lang['Check Import Acumulus'] . '</div>');
                        echo('<p>' . str_replace(
                                '$$',
                                '<a href="systemactivitylog.php">' . $lang['Activity Log'] . '</a>',
                                $lang['Send Result Message']
                            ) . '</p>');
                        unset($_SESSION['acumulus_sendinvoices']);
                    } else {
                        echo('<div class="errorbox"><strong><span class="title">' . $lang['No records found'] . '</span></strong><br />' . $lang['No records found message'] . '</div>');
                    }
                    break;
            }
        } else {
            $this->showModuleForm($vars);
        }
    }

    /**
     * Helper function to create the basic config array used to enter credentials.
     */
    public function constructBasicConfigFields(): array
    {
        return [
            // This is where the module name is defined!:
            'name' => self::Name,
            'description' => 'The Acumulus module connects to the Acumulus online financial administration application.',
            'version' => self::Version,
            'author' => 'SIEL',
            'language' => 'english',
            'fields' => [
                'acumulus_code' => [
                    'FriendlyName' => 'Contract code',
                    'Type' => 'text',
                    'Size' => '25',
                    'Description' => 'Enter the Acumulus contract code here.',
                    'Default' => '',
                ],
                'acumulus_username' => [
                    'FriendlyName' => 'Username',
                    'Type' => 'text',
                    'Size' => '25',
                    'Description' => 'Enter the Acumulus username here.',
                    'Default' => '',
                ],
                'acumulus_password' => [
                    'FriendlyName' => 'Password',
                    'Type' => 'password',
                    'Size' => '25',
                    'Description' => 'Enter the Acumulus password here.',
                ],
            ],
        ];
    }

    /**
     * Helper function to create the full config array used after credentials are
     * correct.
     */
    public function constructFullConfigFields(): array
    {
        $config = $this->get_config();
        $config_array = $this->constructBasicConfigFields();

        // Get the cost centers from Acumulus and put them in a comma separated
        // string.
        $stringCostCenters = '';
        foreach ($this->getCostCenters() as $costcenter) {
            $stringCostCenters .= $costcenter['costcenterid'] . ' ' . str_replace(',', ' ', $costcenter['costcentername']) . ',';
        }
        $stringCostCenters = rtrim($stringCostCenters, ',');

        // Get the invoice templates from Acumulus and put then in a comma separated
        // string.
        $stringTemplates = ',';
        foreach ($this->getTemplates() as $template) {
            $stringTemplates .= $template['invoicetemplateid'] . ' ' . str_replace(',', ' ', $template['invoicetemplatename']) . ',';
        }
        $stringTemplates = rtrim($stringTemplates, ',');

        // Get the Account numbers from Acumulus and put them in a comma separated
        // string.
        $acumulusAccountList = [];
        $accounts = $this->getAccounts();
        foreach ($accounts as $account) {
            $id = $account['accountid'];
            $name = '';
            $addBrackets = false;
            if (!empty($account['accountnumber'])) {
                $name .= $account['accountnumber'];
                $addBrackets = true;
            }
            if (!empty($account['accountdescription'])) {
                if ($addBrackets) {
                    $name .= ' (';
                }
                $name .= $account['accountdescription'];
                if ($addBrackets) {
                    $name .= ')';
                }
            }
            $acumulusAccountList[] = $id . ' ' . str_replace(',', ' ', $name);
        }
        $acumulusAccounts = implode(',', $acumulusAccountList);

        $config_array['fields']['acumulus_credentials_check'] = ['FriendlyName' => 'Credential check', 'Description' => 'Credentials are correct.'];

        // Features.
        $config_array['fields']['acumulus_features_hook_invoice_message1'] = [
            'FriendlyName' => '',
            'Description' => $this->newConfigSection('Features'),
        ];
        $config_array['fields']['acumulus_emailaspdf'] = [
            'FriendlyName' => 'Let Acumulus send invoice',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'When enabled imported invoices will be sent as PDF file using Acumulus.<br><i>* When importing invoices with the bulk import, this setting is ignored and no invoices will be sent by Acumulus.</i>',
            'Default' => 'no',
        ];

        $config_array['fields']['acumulus_customer_import_enabled'] = [
            'FriendlyName' => 'Import customer details',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'Import WHMCS customer details in Acumulus when an invoice is send to Acumulus.',
            'Default' => 'on',
        ];

        // Hook Variables.
        $config_array['fields']['acumulus_hook_invoice_create_enabled'] = [
            'FriendlyName' => 'Enable create hook',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'Send Invoice to Acumulus directly when a new invoice has been generated by the cron, order process, API, when converting a quote to an invoice, or when published a draft invoice with email.',
            'Default' => 'on',
        ];
        $config_array['fields']['acumulus_hook_invoice_paid_enabled'] = [
            'FriendlyName' => 'Enable paid hook',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'Update or Create Invoice in Acumulus directly when paid for.',
            'Default' => 'on',
        ];
        // @todo: correct to British English (like the WHMCS hook) but add an update function to retain value.
        $config_array['fields']['acumulus_hook_invoice_canceled_enabled'] = [
            'FriendlyName' => 'Enable Cancelled invoice hook',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'Create Credit invoice in Acumulus when a invoice is being cancelled.',
            'Default' => 'on',
        ];

        // Customer Variables.
        if ($config['acumulus_customer_import_enabled'] === 'on') {
            $config_array['fields']['acumulus_customer_message1'] = [
                'FriendlyName' => '',
                'Description' => $this->newConfigSection('Customer Settings'),
            ];

            $config_array['fields']['acumulus_customer_type'] = [
                'FriendlyName' => 'Customer Type',
                'Type' => 'dropdown',
                'Options' => 'Debtor,Creditor,Debtor/Creditor (neutral)',
                'Description' => 'Select under what type the customer needs to be registered in Acumulus.',
                'Default' => 'Debtor/Creditor (neutral)',
            ];
            $config_array['fields']['acumulus_customer_countryautoname'] = [
                'FriendlyName' => 'Customer country',
                'Type' => 'dropdown',
                'Options' => 'Use the same country as the customer in WHMCS,Automatic prefill based on country code,Automatic prefill based on country code including Nederland',
                'Description' => 'Select which customer country setting needs to be used in Acumulus.',
                'Default' => 'Use the same country as the customer in WHMCS',
            ];
            $config_array['fields']['acumulus_customer_overwriteifexists'] = [
                'FriendlyName' => 'Overwrite customer details',
                'Type' => 'yesno',
                'Size' => '25',
                'Description' => 'Overwrite customer contact details in Acumulus.',
                'Default' => 'on',
            ];
            $config_array['fields']['acumulus_customer_disableduplicates'] = [
                'FriendlyName' => 'Disable customer duplicates',
                'Type' => 'yesno',
                'Size' => '25',
                'Description' => 'Disable older instances of a contact in Acumulus when multiple contacts match the customer email.',
            ];
            $config_array['fields']['acumulus_whmcs_vatfield'] = [
                'FriendlyName' => 'TAX or VAT field',
                'Type' => 'dropdown',
                'Options' => implode(',', $this->getClientCustomFields()) . ',[VAT Number]',
                'Description' => 'WHMCS Vat field or Custom client field that represents the TAX ID or VAT number. The option [VAT Number] is the new vat field since WHMCS 7.7',
                'Default' => '[VAT Number]',
            ];
            $config_array['fields']['acumulus_whmcs_ibanfield'] = [
                'FriendlyName' => 'IBAN field',
                'Type' => 'dropdown',
                'Options' => implode(',', $this->getClientCustomFields()),
                'Description' => 'Custom client field that represents the clients IBAN number.',
                'Default' => '',
            ];
            // @todo: correct typo but add an update function to retain value.
            $config_array['fields']['acumulus_cusromer_mark'] = [
                'FriendlyName' => 'Client Mark',
                'Type' => 'text',
                'Size' => '40',
                'Description' => 'Extra label or mark. <i>(See manual for variables that can be used.)</i>',
                'Default' => 'WHMCS Klantnr: {USERID}',
            ];
        }

        // Standard Invoice Settings.
        $config_array['fields']['acumulus_invoice_message1'] = [
            'FriendlyName' => '',
            'Description' => $this->newConfigSection('Standard Invoice Settings'),
        ];

        $config_array['fields']['acumulus_invoice_default_costcenter'] = [
            'FriendlyName' => 'Default Costcenter',
            'Type' => 'dropdown',
            'Options' => $stringCostCenters,
            'Description' => 'The default costcenter the new invoices will be booked to.',
            'Default' => '',
        ];

        $config_array['fields']['acumulus_invoice_default_nature'] = [
            'FriendlyName' => 'Default nature',
            'Type' => 'dropdown',
            'Options' => 'Product,Service',
            'Description' => 'The default Nature on wich an invoice line is booked.',
            'Default' => 'Service',
        ];

        $config_array['fields']['acumulus_use_acumulus_invoice_numbering'] = [
            'FriendlyName' => 'Use Acumulus invoice numbering',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'When enabled, WHMCS ignores the invoice number and uses Acumulus sequential invoice numbering.<br>',
            'Default' => 'on',
        ];

        $config_array['fields']['acumulus_invoice_description'] = [
            'FriendlyName' => 'Invoice title',
            'Type' => 'text',
            'Size' => '40',
            'Description' => 'Overall description of the invoice, invoice title.<br><i>(See manual for variables that can be used.)</i>',
            'Default' => 'WHMCS Factuur: {INVOICENUMBER}',
        ];

        $config_array['fields']['acumulus_creditinvoice_description'] = [
            'FriendlyName' => 'Credit Invoice title',
            'Type' => 'text',
            'Size' => '40',
            'Description' => 'Overall description for the Credit invoice, credit invoice title.<br><i>(See manual for variables that can be used.)</i>',
            'Default' => 'Credit Factuur:  WHMCS: {INVOICENUMBER}',
        ];

        $config_array['fields']['acumulus_invoice_descriptiontext'] = [
            'FriendlyName' => 'Invoice extended description',
            'Type' => 'textarea',
            'Rows' => '4',
            'Cols' => '47',
            'Description' => 'Multiline field for extended description of the invoice. Content will appear on invoice and associated emails.<br><i>(See manual for variables that can be used.) note: tabs cannot be used here</i>',
            'Default' => '{INVOICENOTES}',
        ];

        $config_array['fields']['acumulus_invoice_invoicenotes'] = [
            'FriendlyName' => 'Invoice additional remarks',
            'Type' => 'textarea',
            'Rows' => '4',
            'Cols' => '47',
            'Description' => 'Multiline field for additional remarks.  Contents is placed in notes/comments section of the invoice. Content <b>will not appear</b> on the actual invoice or associated emails.<br><i>(See manual for variables that can be used.) (Use {TAB} for tabs)</i>',
            'Default' => '',
        ];

        $config_array['fields']['acumulus_invoice_template'] = [
            'FriendlyName' => 'Invoice Template',
            'Type' => 'dropdown',
            'Options' => $stringTemplates,
            'Description' => 'Name of the template that will be used by Acumulus. When omitted, the first available template in the contract will be selected.',
            'Default' => '',
        ];

        // Additional Invoice Settings.
        $config_array['fields']['acumulus_invoice_message2'] = [
            'FriendlyName' => '',
            'Description' => $this->newConfigSection('Additional Invoice Settings'),
        ];

        $config_array['fields']['acumulus_summarize_invoice'] = [
            'FriendlyName' => 'Summarize invoice lines',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'Combine all invoice lines to one total invoice line. <i>The field "Invoice line description" is used as the description on the invoice line.</i>',
            'Default' => '',
        ];

        $config_array['fields']['acumulus_invoice_correction'] = [
            'FriendlyName' => 'Invoice Correction',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'When enabled, the module will try to estimate the totals (WHMCS and ACUMULUS) and add a correction line when needed.',
            'Default' => '',
        ];
        if ($config['acumulus_invoice_correction'] === 'on') {
            $config_array['fields']['acumulus_invoice_correction_text'] = [
                'FriendlyName' => 'Invoice correction line description<br>',
                'Type' => 'text',
                'Size' => '40',
                'Description' => 'The text that will appear on the invoice if there is a correction needed.<br><i>(See manual for variables that can be used.)</i>',
                'Default' => 'WHMCS correction',
            ];
        }
        if ($config['acumulus_summarize_invoice'] === 'on') {
            $config_array['fields']['acumulus_summarization_text_taxed'] = [
                'FriendlyName' => 'Invoice line Summarization description<br>including TAX',
                'Type' => 'text',
                'Size' => '40',
                'Description' => 'The text that will be used on the summerized invoice line for items with tax.<br><i>(See manual for variables that can be used.)</i>',
                'Default' => 'Totaal WHMCS Factuur belast met BTW',
            ];

            $config_array['fields']['acumulus_summarization_text_untaxed'] = [
                'FriendlyName' => 'Invoice line Summarization description<br>excluding TAX',
                'Type' => 'text',
                'Size' => '40',
                'Description' => 'The text that will be used on the summarized invoice line for items without tax.<br><i>(See manual for variables that can be used.)</i>',
                'Default' => 'Totaal WHMCS Factuur zonder BTW',
            ];
        }

        $config_array['fields']['acumulus_invoice_use_last_paymentmethod'] = [
            'FriendlyName' => 'Use last payment method',
            'Type' => 'yesno',
            'Size' => '25',
            'Description' => 'When enabled, the module will change the account numbers invoice in Acumulus to use the last payment method.',
            'Default' => 'on',
        ];

        // Account number translation
        $config_array['fields']['acumulus_accountnumber_message1'] = [
            'FriendlyName' => '',
            'Description' => $this->newConfigSection('Account number translation'),
        ];

        foreach ($this->getWHMCSAccountNumbers() as $accountNumber) {
            $config_array['fields']['acumulus_AccountNumber_' . $accountNumber['module']] = [
                'FriendlyName' => 'WHMCS Payment Gateway: <b>' . $accountNumber['displayname'] . '</b>',
                'Type' => 'dropdown',
                'Options' => $acumulusAccounts,
                'Description' => 'Select the matching Acumulus AccountNumber.',
                'Default' => '',
            ];
        }

        // Send email as pdf from Acumulus.
        if ($config['acumulus_emailaspdf'] === 'on') {
            $config_array['fields']['acumulus_emailaspdf_message1'] = [
                'FriendlyName' => '',
                'Description' => $this->newConfigSection('Acumulus E-Mail Settings'),
            ];
            $config_array['fields']['acumulus_emailaspdf_message2'] = [
                'FriendlyName' => 'E-mail To',
                'Description' => 'The invoice will be send to the primary customer email address. WHMCS additional contacts will be ignored.',
            ];

            $config_array['fields']['acumulus_emailaspdf_emailbcc'] = [
                'FriendlyName' => 'E-mail BCC',
                'Type' => 'text',
                'Size' => '40',
                'Description' => 'Use valid email addresses. Multiple addresses can be used when separated with a comma or semicolon. If emailto is not set, the emailbcc will be ignored and skipped.',
                'Default' => '',
            ];
            $config_array['fields']['acumulus_emailaspdf_emailfrom'] = [
                'FriendlyName' => 'Email From',
                'Type' => 'text',
                'Size' => '40',
                'Description' => 'Use a single valid email address. If omitted, the email address of the invoice template, with fallback to the account owner will be used. Most pretty results are obtained when using fully configured invoice templates in Acumulus and leaving this option empty (recommended).',
                'Default' => '',
            ];
            $config_array['fields']['acumulus_emailaspdf_subject'] = [
                'FriendlyName' => 'E-mail Subject',
                'Type' => 'text',
                'Size' => '40',
                'Description' => 'ASCII-only allowed. Be sure to provide xml-escaped HTML entities for UTF-8 characters.<br>If omitted or left empty, the subject will be: Factuur [number] [description]<br><i>(See manual for variables that can be used.)</i>',
                'Default' => '',
            ];
            $config_array['fields']['acumulus_emailaspdf_message'] = [
                'FriendlyName' => 'E-mail Message',
                'Type' => 'textarea',
                'Rows' => '4',
                'Cols' => '47',
                'Description' => 'Currently, ASCII-only allowed. Mileage may vary when trying to submit multiple lines.<br>If omitted, the email text composed in the template will be used (recommended)<br><i>(See manual for variables that can be used.)</i>.',
                'Default' => '',
            ];
            $config_array['fields']['acumulus_emailaspdf_confirmreading'] = [
                'FriendlyName' => 'E-mail Confirm Reading',
                'Type' => 'yesno',
                'Size' => '25',
                'Description' => 'Ask the recipient to confirm the delivery of the email message.',
                'Default' => 'no',
            ];
        }

        // Warnings & Error Variables.
        $config_array['fields']['acumulus_warning_message2'] = [
            'FriendlyName' => '',
            'Description' => $this->newConfigSection('Warnings & Errors'),
        ];

        $config_array['fields']['acumulus_warning_email_address'] = [
            'FriendlyName' => 'Email address for warnings',
            'Type' => 'text',
            'Size' => '25',
            'Description' => 'Enter an email address where api warning messages should be sent to.<br> When omitted no warnings will be sent.',
            'Default' => '',
        ];

        $config_array['fields']['acumulus_error_email_address'] = [
            'FriendlyName' => 'Email address for errors',
            'Type' => 'text',
            'Size' => '25',
            'Description' => 'Enter an email address where api error messages should be sent to.<br>When omitted no errors will be sent.',
            'Default' => '',
        ];

        // API Variables.
        $config_array['fields']['acumulus_api_message1'] = [
            'FriendlyName' => '',
            'Description' => $this->newConfigSection('WHMCS API Settings'),
        ];

        return $config_array;
    }

    /**
     * Returns a (highly visible) section header.
     */
    public function newConfigSection(string $section): string
    {
        return "<h2 style='padding-top:3em;font-weight:bold;font-size:larger;'>$section</h2>";
    }

    /**
     * Helper function to show the Module form.
     *
     * The form is echoed to the output, not returned as a string.
     */
    public function showModuleForm(array $vars): void
    {
        $todaysdate = getTodaysDate();
        $lang = $vars['_lang'];

        $gateways = [];
        foreach ($this->getWHMCSAccountNumbers() as $gateway) {
            $gateways[] = $gateway['module'];
        }

        echo '<form method="post" action="addonmodules.php?module=acumulus_connect">
      <input type="hidden" name="action" value="sendinvoice">
      <p>
        <b>' . $lang['Single invoice "title'] . '</b>
      </p>
        <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
        <tbody>
           <tr><td colspan="2"><p>' . $lang['Single invoice detail text'] . '</p></td></tr>
           <tr>
            <td width="25%" class="fieldlabel">' . $lang['Invoice ID'] . ':</td>
            <td class="fieldarea">
             <input type="text" name="resentinvoice" size="30" value=""> <input type="submit" value="' . $lang['Sent Invoice'] . '">
            </td>
           </tr>
           <tr>
            <td width="25%" class="fieldlabel">' . $lang['Search on'] . ':</td>
            <td class="fieldarea">
             <select name="search_on">
                <option value="invoiceno" selected>' . $lang['Invoice No'] . '</option>
                <option value="invoiceid">' . $lang['Invoice ID'] . '</option>
             </select>
            </td>
           </tr>
        </tbody>
        </table>

        <br><br>
        <p>
           <b>' . $lang['Send multiple invoices header'] . '</b>
        </p>
    </form>


    <form method="post" action="addonmodules.php?module=acumulus_connect">
        <input type="hidden" name="action" value="sendbatch">
        <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
        <tbody>
           <tr><td colspan="2"><div class="infobox"><strong><span class="title">' . $lang['Batch import'] . '</span></strong><br />' . $lang['Batch import time warning'] . '</div> </td></tr>
           <tr><td colspan="2"><p>' . $lang['Batch import detail text'] . '</p></td></tr>
           <tr>
                <td class="fieldlabel">' . $lang['Filter By'] . '</td>
                <td class="fieldarea"><select name="filterby"><option>Invoice Date</option><option>Date Paid</option><option>Unpaid Invoices</option><option>Paid Invoices by invoicedate</option></select></td>
           </tr>
           <tr>
                <td class="fieldlabel">' . $lang['Payment Method'] . '</td>
                <td class="fieldarea"><select name="filterby2">
                        <option>All Gateways</option><option>' . implode('</option><option>', $gateways) . '</option></td>
           </tr>



           <tr>
                <td class="fieldlabel">' . $lang['Date Range'] . '</td>
                <td class="fieldarea"><input type="text" name="datefrom" value="' . $todaysdate . '" class="datepick" /> &nbsp;&nbsp;&nbsp;&nbsp;' . $lang['to'] . '&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="dateto" value="' . $todaysdate . '" class="datepick" /></td>
           </tr>
           <tr>
             <td colspan="2" align="center"><input type="submit" value="' . $lang['Submit invoices'] . '"></td>
           </tr>
        </tbody>
        </table>
    </form>
    <br><br>



      <input type="hidden" name="action" value="rechecklicense">
      <p>
        <b>' . $lang['License Information'] . '</b>
      </p>
        <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
        <tbody>
            <tr>
                <td>' . $lang['License Information text'] . ' <a href="mailto: whmcs@acumulus.nl"> whmcs@acumulus.nl</a>.</td>
            </tr>
        </tbody>
        </table>
    ';
    }

    /**
     * Return an HTML string with a summary of the invoices.
     */
    public function getInvoicesSummary(array $invoices, array $vars): string
    {
        global $_SESSION;
        $lang = $vars['_lang'];

        $totalInvoices = 0;
        $summaryLines = '';
        $sendInvoices = [];

        foreach ($invoices as $invoiceId) {
            // https://developers.whmcs.com/api-reference/getinvoice/
            $command = 'GetInvoice';
            $values['invoiceid'] = $invoiceId;
            $data = $this->localAPI($command, $values);
            $client = $this->getClient($data['userid']);

            // Check if invoice number exists or use the invoice id instead.
            $invoiceNumber = $data['invoicenum'] === '' ? $data['invoiceid'] : $data['invoicenum'];

            $data['clientname'] = !empty($client['companyname']) ? "{$client['companyname']} - {$client['firstname']} {$client['lastname']}" : "{$client['firstname']} {$client['lastname']}";
            $summaryLines .= '<tr>';
            $summaryLines .= "<td> <a href='invoices.php?action=edit&id={$data['invoiceid']}'>{$data['invoiceid']}</a></td>";
            $summaryLines .= "<td>$invoiceNumber</td>";
            $summaryLines .= "<td><a href='clientssummary.php?userid={$data['userid']}'>{$data['clientname']}</a></td>";
            $summaryLines .= '<td>' . fromMySQLDate($data['date']) . '</td>';
            $summaryLines .= '<td>' . fromMySQLDate($data['datepaid']) . '</td>';
            $summaryLines .= "<td>{$data['total']}</td>";
            $summaryLines .= "<td>{$data['paymentmethod']}</td>";
            if (strtolower($data['status']) === 'paid') {
                $summaryLines .= "<td><span class='textgreen'>{$lang['Paid']}</span></td>";
            } elseif (strtolower($data['status']) === 'unpaid') {
                $summaryLines .= "<td><span class='textred'>{$lang['Unpaid']}</span></td>";
            } elseif (strtolower($data['status']) === 'cancelled') {
                $summaryLines .= "<td><span>{$lang['Cancelled']}</span></td>";
            } else {
                $summaryLines .= "<td><span>{$data['status']}</span></td>";
            }
            $summaryLines .= '<tr>';
            ++$totalInvoices;
            $sendInvoices[] = $data['invoiceid'];
        }
        $summary = "<p>{$lang['Sent invoice summery']}.</p>";
        $summary .= "$totalInvoices {$lang['Records Found']}";
        $summary .= '<div class="tablebg">';
        $summary .= '<table id="sortabletbl1" class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">';
        $summary .= "<tr><th>{$lang['Invoice ID']}</th><th>{$lang['Invoice No']}</th><th>{$lang['Client Name']}</th><th>{$lang['Invoice Date']}</th><th>{$lang['Date Paid']}</th><th>Total</th><th>{$lang['Payment Method']}</th><th>{$lang['Status']}</th></tr>";
        $summary .= $summaryLines;
        $summary .= '</table></div>';

        $_SESSION['acumulus_sendinvoices'] = $sendInvoices;

        return $summary;
    }

    public function logException(Throwable $e): void
    {
        /**
         * @noinspection PhpStrictComparisonWithOperandsOfDifferentTypesInspection
         *   Returns the exception code as int in Exception but possibly as other
         *   type in Exception descendants (for example as string in PDOException).
         */
        if ($e->getCode() !== 'ACUMULUS') {
            $callingFunction = $e->getTrace()[0]['function'];
            $callingLine = $e->getLine();
            $message = get_class($e);
            if (!empty($e->getCode())) {
                $message .= ' ' . $e->getCode() . ': ';
            }
            $message .= ': ';
            $message .= $e->getMessage();
            $message .= " in $callingFunction:$callingLine";
            logActivity($message);
        }
    }

    /**
     * Wrapper around the WHMCS localApi() function that adds some error handling.
     * In case of an error:
     * - an error message is logged.
     * - A runtime exception is thrown.
     *
     * @return array
     *  Array with keys:
     *  - 'result': string: success or error.
     *  - 'message': string: optional, error message in case of error.
     *  - Other keys depend on the API function called, see
     *    {@link https://developers.whmcs.com/api/api-index/}.
     * @throws \RuntimeException
     */
    public function localAPI(string $command, array $values): array
    {
        $results = localAPI($command, $values);
        if ($results['result'] !== 'success') {
            $callingFunction = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
            $mainArg = '';
            if (count($values) >= 1) {
                reset($values);
                $value = current($values);
                $mainArg = is_scalar($value) ? key($values) . ': ' . $value : '...';
            }
            $message = "$callingFunction($mainArg): $command failed: {$results['result']}: {$results['message']}";
            logActivity($message);
            /** @noinspection PhpStrictTypeCheckingInspection */
            throw new RuntimeException($message, 'ACUMULUS');
        }

        return $results;
    }

    /**
     * Helper function to load the configuration data stored in the Database.
     */
    public function get_config(): array
    {
        // @todo: what if table is empty?
        $configRecords = Capsule::table('tbladdonmodules')->where('module', 'acumulus')->get(['setting', 'value']);
        $config = [];
        foreach ($configRecords as $record) {
            $config[$record->setting] = $record->value;
        }
        // Split composite values into their parts.
        $acumulusDefaultCostCenterParts = explode(' ', $config['acumulus_invoice_default_costcenter'], 2);
        $config['acumulus_invoice_default_costcenterid'] = $acumulusDefaultCostCenterParts[0];
        $config['acumulus_invoice_default_costcentername'] = $acumulusDefaultCostCenterParts[1];
        $acumulusInvoiceTemplateParts = explode(' ', $config['acumulus_invoice_template'], 2);
        $config['acumulus_invoice_templateid'] = $acumulusInvoiceTemplateParts[0];
        $config['acumulus_invoice_templatename'] = $acumulusInvoiceTemplateParts[1];
        // Loop through all account numbers in WHMCS and split them as well.
        foreach ($this->getWHMCSAccountNumbers() as $accountNumber) {
            $accountParts = explode(' ', $config['acumulus_AccountNumber_' . $accountNumber['module']], 2);
            $config['account_numbers'][$accountNumber['module']]['id'] = $accountParts[0];
            $config['account_numbers'][$accountNumber['module']]['name'] = $accountParts[1];
        }

        // Global WHMCS Settings.
        $config['TaxType'] = Capsule::table('tblconfiguration')->where('setting', 'TaxType')->value('value');

        return $config;
    }

    /**
     * WHMCS API Call to retrieve custom fields.
     */
    public function getClientCustomFields(): array
    {
        $results = [];
        foreach (Capsule::table('tblcustomfields')->where('type', 'client')->orderBy('fieldname')->get('fieldname') as $record) {
            $results[] = $record->fieldname;
        }

        return $results;
    }

    /**
     * WHMCS API Call to retrieve client details.
     */
    public function getClient(int $clientId): array
    {
        // https://developers.whmcs.com/api-reference/getclientsdetails/
        $command = 'GetClientsDetails';
        $values = ['clientid' => $clientId, 'stats' => false];
        $results = $this->localAPI($command, $values);

        return $results['client'];
    }

    /**
     * Retrieves a local WHMCS invoice.
     *
     * @param bool $expand
     *   Whether to expand the WHMCS invoice with customer data and some custom
     *   values, e.g. line totals.
     */
    public function getInvoice(int $invoiceId, bool $expand = true): array
    {
        // https://developers.whmcs.com/api-reference/getinvoice/
        $command = 'GetInvoice';
        $values = ['invoiceid' => $invoiceId];
        $results = $this->localAPI($command, $values);

        // Add some custom fields to the invoice.
        if ($expand) {
            $client = $this->getClient($results['userid']);
            $results = $this->expandInvoiceWithCustomValues($results, $client);
        }

        return $results;
    }

    /**
     * WHMCS API Call to retrieve the account numbers from WHMCS.
     *
     * @return array[]
     */
    public function getWHMCSAccountNumbers(): array
    {
        // https://developers.whmcs.com/api-reference/getpaymentmethods/
        $command = 'GetPaymentMethods';
        $values = [];
        $results = $this->localAPI($command, $values);

        return $results['paymentmethods']['paymentmethod'];
    }

    /**
     * Helper function to retrieve the current WHMCS version.
     *
     * @todo: use localApi WHMCSDetails.
     */
    public function getWHMCSVersion(): string
    {
        return Capsule::table('tblconfiguration')->where('setting', 'Version')->value('value');
    }

    /**
     * Helper function to Extend the invoice with custom values for tax etc.
     *
     * @noinspection PhpHalsteadMetricInspection
     */
    public function expandInvoiceWithCustomValues(array $invoice, array $client): array
    {
        $config = $this->get_config();

        // Add some custom tax amounts
        $invoice['custom']['subtotal_taxedItems_exclTax'] = 0.0;
        $invoice['custom']['subtotal_taxedItems_inclTax'] = 0.0;
        $invoice['custom']['subtotal_untaxedItems'] = 0.0;
        $invoice['custom']['total_tax_roundedPerItem'] = 0.0;
        $invoice['custom']['total_tax'] = 0.0;

        $convertedVatTax = $this->getVatType($config, $invoice, $client);
        $invoice['custom']['vattype'] = $convertedVatTax['vattype'];
        $invoice['custom']['taxrate'] = $convertedVatTax['taxrate'];
        /** @noinspection TypeUnsafeComparisonInspection (property is per the Acumulus API definition an int) */
        if ($invoice['custom']['taxrate'] == '-1' || $invoice['custom']['taxrate'] == '0') {
            $invoice['taxrate'] = '0';
        }

        // @todo: optimize (reverse if and foreach => merge inner else branches,
        //   because the upper one contains errors (setting price incl to 0?);
        //   use "... as &item" instead of accessing $item by $counter.
        $counter = 0;
        if ($config['TaxType'] === 'Exclusive') {
            foreach ($invoice['items']['item'] as $item) {
                /** @noinspection TypeUnsafeComparisonInspection  (property is named like a bool value) */
                if ($item['taxed'] == 1) {
                    $invoice['items']['item'][$counter]['custom_tax_unrounded'] = round(
                        ((float) $item['amount'] / 100) * (float) $invoice['taxrate'],
                        4
                    );  // (amount / 100) * Tax Rate
                    $invoice['items']['item'][$counter]['custom_tax_rounded'] = round(
                        ((float) $item['amount'] / 100) * (float) $invoice['taxrate'],
                        2
                    );  // (amount / 100) * Tax Rate
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = round(
                        ($item['amount'] + (((float) $item['amount'] / 100) * (float) $invoice['taxrate'])),
                        4
                    );   // amount + ((amount / 100) * Tax Rate)
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = round(
                        ($item['amount'] + round(
                                ((float) $item['amount'] / 100) * (float) $invoice['taxrate'],
                                2
                            )),
                        2
                    ); // amount + ((amount / 100) * Tax Rate)
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round((float) $item['amount'], 4);
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round((float) $item['amount'], 2);
                    $invoice['custom']['subtotal_taxedItems_exclTax'] += (float) $item['amount'];
                    $invoice['custom']['subtotal_taxedItems_inclTax'] += round(
                        ($item['amount'] + (((float) $item['amount'] / 100) * (float) $invoice['taxrate'])),
                        4
                    );   // amount + ((amount / 100) * Tax Rate)
                    $invoice['custom']['total_tax_roundedPerItem'] += round(
                        ((float) $item['amount'] / 100) * (float) $invoice['taxrate'],
                        2
                    );   // amount + ((amount / 100) * Tax Rate)
                    $invoice['custom']['total_tax'] += round(
                        ((float) $item['amount'] / 100) * (float) $invoice['taxrate'],
                        4
                    );   // amount + ((amount / 100) * Tax Rate)
                } else {
                    $invoice['items']['item'][$counter]['custom_tax_unrounded'] = 0.0;
                    $invoice['items']['item'][$counter]['custom_tax_rounded'] = 0.0;
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = 0.0;
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = 0.0;
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round((float) $item['amount'], 4);
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round((float) $item['amount'], 2);
                    $invoice['custom']['subtotal_untaxedItems'] += (float) $item['amount'];
                }
                $counter++;
            }
        } else {
            // Prices are set inclusive.
            foreach ($invoice['items']['item'] as $item) {
                /** @noinspection TypeUnsafeComparisonInspection property is named as if it is a bool */
                if ($item['taxed'] == 1) {
                    $invoice['items']['item'][$counter]['custom_tax_unrounded'] = round(
                        ((float) $item['amount'] / (100 + (float) $invoice['taxrate'])) * (float) $invoice['taxrate'],
                        4
                    );    // amount / (100 + Tax Rate)
                    $invoice['items']['item'][$counter]['custom_tax_rounded'] = round(
                        ((float) $item['amount'] / (100 + (float) $invoice['taxrate'])) * (float) $invoice['taxrate'],
                        2
                    );   // amount / (100 + Tax Rate)
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = round((float) $item['amount'], 4);
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = round((float) $item['amount'], 2);
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round(
                        ((float) $item['amount'] / (100 + (float) $invoice['taxrate'])) * 100,
                        4
                    );  // (amount / (100 + Tax Rate)) * 100
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round(
                        ((float) $item['amount'] / (100 + (float) $invoice['taxrate'])) * 100,
                        2
                    );  // (amount / (100 + Tax Rate)) * 100
                    $invoice['custom']['subtotal_taxedItems_exclTax'] += round(
                        ((float) $item['amount'] / (100 + (float) $invoice['taxrate'])) * 100,
                        4
                    );  // (amount / (100 + Tax Rate)) * 100 ;
                    $invoice['custom']['subtotal_taxedItems_inclTax'] += round((float) $item['amount'], 4);
                    $invoice['custom']['total_tax_roundedPerItem'] += round(
                        ((float) $item['amount'] / (100 + (float) $invoice['taxrate'])) * (float) $invoice['taxrate'],
                        2
                    );   // amount / (100 + Tax Rate);
                    $invoice['custom']['total_tax'] += round(
                        ((float) $item['amount'] / (100 + (float) $invoice['taxrate'])) * (float) $invoice['taxrate'],
                        4
                    );
                } else {
                    $invoice['items']['item'][$counter]['custom_tax_unrounded'] = 0.0;
                    $invoice['items']['item'][$counter]['custom_tax_rounded'] = 0.0;
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = round((float) $item['amount'], 4);
                    $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = round((float) $item['amount'], 2);
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round((float) $item['amount'], 4);
                    $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round((float) $item['amount'], 2);
                    $invoice['custom']['subtotal_untaxedItems'] += (float) $item['amount'];
                }
                $counter++;
            }
        }
        $invoice['custom']['subamountTaxRounded'] = round($invoice['custom']['subamountTax'], 2);

        // Calculate rounding corrections.
        if ($config['acumulus_invoice_correction'] === 'on') {
            $invoice = $this->estimateTotals($config, $invoice, $client);
        }

        return $invoice;
    }

    /**
     * Helper function to replace text with dynamic values.
     *
     * @noinspection PhpFunctionCyclomaticComplexityInspection
     */
    public function replaceVarsInText(?string $text, array $invoice, array $client): string
    {
        // @todo: A user got a 'TypeError: Argument 1 passed to
        //   $this->replaceVarsInText() must be of the type string, null
        //   given'. As it is unknown which call provoked it, I solved it like this.
        if ($text === null) {
            return '';
        }
        $vars = [
            '{USERID}' => $client['userid'] ?? '',
            '{FIRSTNAME}' => $client['firstname'] ?? '',
            '{LASTNAME}' => $client['lastname'] ?? '',
            '{FULLNAME}' => $client['fullname'] ?? '',
            '{COMPANYNAME}' => $client['companyname'] ?? '',
            '{ADDRESS1}' => $client['address1'] ?? '',
            '{ADDRESS2}' => $client['address2'] ?? '',
            '{CITY}' => $client['city'] ?? '',
            '{STATE}' => $client['state'] ?? '',
            '{POSTCODE}' => $client['postcode'] ?? '',
            '{COUNTRYCODE}' => $client['countrycode'] ?? '',
            '{COUNTRY}' => $client['countryname'] ?? '',
            '{PHONENUMBER}' => $client['phonenumber'] ?? '',
            '{CLIENT_CUSTOMFIELD1}' => $client['customfields1'] ?? '',
            '{CLIENT_CUSTOMFIELD2}' => $client['customfields2'] ?? '',
            '{CLIENT_CUSTOMFIELD3}' => $client['customfields3'] ?? '',
            '{CLIENT_CUSTOMFIELD4}' => $client['customfields4'] ?? '',
            '{CLIENT_CURRENCY}' => $client['currency_code'] ?? '',
            '{INVOICEID}' => $invoice['invoiceid'] ?? '',
            '{INVOICENUMBER}' => !empty($invoice['invoicenum']) ? $invoice['invoicenum'] : $invoice['invoiceid'],
            '{INVOICEDATE}' => $invoice['date'] ?? '',
            '{INVOICEDUE}' => $invoice['duedate'] ?? '',
            '{INVOICENOTES}' => $invoice['notes'] ?? '',
            '{INVOICESTATUS}' => $invoice['status'] ?? '',
        ];

        foreach ($vars as $key => $value) {
            $text = str_ireplace($key, $value, $text);
        }

        return $text;
    }

    /**
     * Helper function to retrieve the cost centers from Acumulus.
     */
    public function getCostCenters(): array
    {
        // Construct the basic xml without email on errors or warnings.
        $xml = $this->basicXml(false);
        $xml->addChild('format', 'xml');
        $xml_string = urlencode($xml->asXML());

        // Let's check the credentials against the Acumulus API.
        $url = 'https://api.sielsystems.nl/acumulus/stable/picklists/picklist_costcenters.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        // Load the xml result.
        $xml = simplexml_load_string($response, null, LIBXML_NOCDATA);
        // Convert XML to json and then convert to Array.
        $costcenters = json_decode(json_encode($xml->costcenters), true);
        // If there are more than 1 cost centers in Acumulus, keep returning the
        // same array construction.
        if (isset($costcenters['costcenter'][1])) {
            $costcenters = $costcenters['costcenter'];
        }

        return $costcenters;
    }

    /**
     * Helper function to retrieve the bank account numbers from Acumulus.
     */
    public function getAccounts(): array
    {
        $xml = $this->basicXml(false); //construct the basic xml without email on errors or warnings.
        $xml->addChild('format', 'xml');
        $xml_string = urlencode($xml->asXML());

        // Let's check the credentials against the Acumulus API.
        $url = 'https://api.sielsystems.nl/acumulus/stable/picklists/picklist_accounts.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        // Load the xml result.
        $xml = simplexml_load_string($response, null, LIBXML_NOCDATA);
        $rawAccounts = $xml->accounts;
        // Convert XML to json and then convert to Array.
        $jsonAccounts = json_encode($rawAccounts);
        $accounts = json_decode($jsonAccounts, true);
        // if there are more than 1 bank account in Acumulus, keep returning the
        // same array construction.
        if (isset($accounts['account'][1])) {
            $accounts = $accounts['account'];
        }

        return $accounts;
    }

    /**
     * Helper function to retrieve the invoice templates from Acumulus.
     */
    public function getTemplates(): array
    {
        // Construct the basic xml without email on errors or warnings.
        $xml = $this->basicXml(false);
        $xml->addChild('format', 'xml');
        $xml_string = urlencode($xml->asXML());

        // Let's check the credentials against the Acumulus API.
        $url = 'https://api.sielsystems.nl/acumulus/stable/picklists/picklist_invoicetemplates.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        // Load the xml result.
        $xml = simplexml_load_string($response, null, LIBXML_NOCDATA);
        $rawTemplates = $xml->invoicetemplates;
        // Convert XML to json and then convert to Array.
        $jsonTemplates = json_encode($rawTemplates);
        $templates = json_decode($jsonTemplates, true);
        // If there are more than 1 template in Acumulus, keep returning the same
        // array construction.
        if (isset($templates['invoicetemplate'][1])) {
            $templates = $templates['invoicetemplate'];
        }

        return $templates;
    }

    /**
     * Helper function to check if country is an EU member.
     *
     * @todo: replace with Acumulus API call
     */
    public function isCountryInEU(string $countryCode, string $date): bool
    {
        // $date is for future use like the brexit
        $eu_countries = [
            'BE',
            'BG',
            'CY',
            'DK',
            'DE',
            'EE',
            'FI',
            'FR',
            'GR',
            'HU',
            'IE',
            'IT',
            'HR',
            'LV',
            'LT',
            'LU',
            'MT',
            'NL',
            'AT',
            'PL',
            'PT',
            'RO',
            'SI',
            'SK',
            'ES',
            'CZ',
            'SE',
            'GB',
            'UK',
        ];
        // Convert string to date.
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $date = strtotime($date);

        // Add countries based on date
        // if ($date >= strtotime('2050-01-15')) {
        //     array_push($eu_countries, 'XX');
        // }

        // Remove countries based on date.
        if ($date >= strtotime('2021-01-01')) {
            $eu_countries = array_diff($eu_countries, ['UK']);
        }
        if ($date >= strtotime('2021-01-01')) {
            $eu_countries = array_diff($eu_countries, ['GB']);
        }

        return in_array($countryCode, $eu_countries);
    }

    /**
     * Helper function to calculate the vat type and tax rate by country, nature MOSS etc.
     */
    public function getVatType(array $config, array $invoice, array $client): array
    {
        /* Vattypes:
           1 	National 	Gewone nationale factuur 	DEFAULT
           2 	National reverse charge 	Verlegde BTW binnen Nederland
           3 	International reverse charge 	BTW-verlegd naar ondernemer in de EU. Een intracommunautaire levering.
           4 	Export outside EU (export) 	Een factuur voor goederen buiten de EU
           5 	Margin scheme 	Marge regeling voor 2e-hands production
           6 	Foreign VAT 	Buitenlandse BTW voor electronische diensten aan particulieren in de EU. Usage of countrycode mandatory
        */

        if (strtoupper($client['countrycode']) === 'NL') {
            // Invoice is National.
            $vatType = '1';
            $taxRate = $invoice['taxrate'];
        } elseif ($this->isCountryInEU(strtoupper($client['countrycode']), $invoice['date'])) {
            // Invoice is EU.
            if (strtotime($invoice['date']) < strtotime('2015-01-01')) {
                // factuur van voor 1 jan 2015 (pre MOSS).
                $vatType = '1';
                if (empty($client['companyname'])) {
                    // Particulier.
                    $taxRate = $invoice['taxrate'];
                } else {
                    // Bedrijf.
                    $taxRate = '0.00';
                }
            } elseif (empty($client['companyname'])) {
                // Particulier.
                // WHMCS zijn digitale diensten.
                $vatType = '6';
                $taxRate = $invoice['taxrate'];
            } else {
                // Bedrijf.
                $vatType = '3';
                $taxRate = '0.00';
            }
        } else {
            // Invoice is Outside EU (WORLD).
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (strtolower($config['acumulus_invoice_default_nature']) === 'service') {
                // The Default nature is a service (digitale diensten).
                if (empty($client['companyname'])) {
                    // particulier
                    // whmcs zijn digitale diensten.
                    $vatType = '4';
                    $taxRate = '0.00';  // btw-aangifte moet eventueel worden gedaan in het land van de afnemer.
                } else {
                    // bedrijf.
                    $vatType = '1';
                    $taxRate = '-1'; // btw-vrij.
                }
            } else {
                // The Default nature is a product.
                $vatType = '4';
                $taxRate = '0.00'; // btw-vrij
            }
        }

        return ['vattype' => $vatType, 'taxrate' => $taxRate];
    }

    /**
     * Helper function to send the constructed XML to Acumulus with curl.
     */
    public function sendInvoiceToAcumulus(array $config, array $invoice, SimpleXMLElement $xml): void
    {
        $url = 'https://api.sielsystems.nl/acumulus/stable/invoices/invoice_add.php';
        $xml_string = urlencode($xml->asXML());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $rawData = curl_exec($ch);
        $result = json_decode(json_encode((array) simplexml_load_string($rawData)), true);
        logModuleCall('acumulus_connect', 'Send Invoice to Acumulus', $xml->asXML(), $rawData, $result, $this->getReplaceVars($config));

        if (isset($result['status'])) {
            switch ($result['status']) {
                case '0':  // Success.
                    $resultStatus = 'Success';
                    $messages = '';
                    $this->setInvoiceToken($invoice, $result['invoice']['token'], $result['invoice']['entryid']);
                    break;
                case '1':  // Error(s).
                    $resultStatus = 'error(s)';
                    $messages = print_r($result['error'] ?? json_encode($result), true);
                    break;
                case '2':  // Success with warning(s).
                    $resultStatus = 'warning(s)';
                    $messages = print_r($result['warning'], true);
                    $this->setInvoiceToken($invoice, $result['invoice']['token'], $result['invoice']['entryid']);
                    break;
                default:
                    $resultStatus = 'error';
                    $messages = "Unknown status code {$result['status']}";
            }
        } else {
            // Exception/curl error.
            $resultStatus = 'exception';
            $messages = 'Error reaching acumulus API webservice';
        }
        logActivity(__FUNCTION__ . "({$invoice['invoiceid']}): $resultStatus $messages.");
        curl_close($ch);
    }

    /**
     * Gets the vars that should be hidden in the log
     */
    public function getReplaceVars(array $config): array
    {
        return [$config['acumulus_code'], $config['acumulus_username'], $config['acumulus_password']];
    }

    /**
     * Helper function to update the token table for unpaid invoices.
     *
     * @todo
     *   Always store token and entry-id: we need it when this invoice gets
     *   cancelled and, possible future addition, to have links to the acumulus pdf,
     *   packing slip and to visualise the status like we do in the other plugins.
     */
    public function setInvoiceToken(array $invoice, string $token, int $entryId): void
    {
        // Check if invoice id and invoice token are already stored and, if so, update.
        if (Capsule::table('mod_acumulus_connect')->where('id', $invoice['invoiceid'])->exists()) {
            // A token already exists, so lets update the token.
            $updateCount = Capsule::table('mod_acumulus_connect')
                ->where('id', $invoice['invoiceid'])
                ->update([
                    'token' => $token,
                    'entryid' => $entryId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            if ($updateCount > 0) {
                logActivity(__FUNCTION__ . "({$invoice['invoiceid']}): updated");
            } else {
                logActivity(__FUNCTION__ . "({$invoice['invoiceid']}): not updated");
            }
        } else {
            // No token exists, so let's add the token.
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (Capsule::table('mod_acumulus_connect')->insert([
                'id' => $invoice['invoiceid'],
                'token' => $token,
                'entryid' => $entryId,
                'created_at' => date('Y-m-d H:i:s'),
            ])) {
                logActivity(__FUNCTION__ . "({$invoice['invoiceid']}): inserted");
            } else {
                logActivity(__FUNCTION__ . "({$invoice['invoiceid']}): not inserted");
            }
        }
    }

    /**
     * Helper function to estimate the totals like Acumulus would calculate.
     */
    public function estimateTotals(array $config, array $invoice, array $client): array
    {
        $totalWhmcs = 0;
        $totalAcumulus = 0;

        foreach ($invoice['items']['item'] as $item) {
            $totalWhmcs += (float) $item ['custom_price_incl_tax_unrounded'];
            $totalAcumulus += (float) $item ['custom_price_incl_tax_rounded'];
        }
        $totalWhmcs = round($totalWhmcs, 2);
        $totalAcumulus = round($totalAcumulus, 2);

        $difference = $totalWhmcs - $totalAcumulus;

        /** @noinspection TypeUnsafeComparisonInspection actually: we compare a float here, which poses other problems as well */
        if ($difference != 0) {
            $correctionLine = [
                'id' => 'n/a',
                'type' => '',
                'relid' => '0',
                'description' => $this->replaceVarsInText($config['acumulus_invoice_correction_text'], $invoice, $client),
                'amount' => $difference,
                'taxed' => '0',
                'custom_tax_unrounded' => '0',
                'custom_tax_rounded' => '0',
                'custom_price_incl_tax_unrounded' => $difference,
                'custom_price_incl_tax_rounded' => $difference,
                'custom_price_excl_tax_unrounded' => $difference,
                'custom_price_excl_tax_rounded' => $difference,
            ];

            $invoice['items']['item'][] = $correctionLine;
        }

        return $invoice;
    }

    /**
     * Helper function to get the current payment status from Acumulus.
     */
    public function getPaymentStatus(array $config, string $token): array
    {
        $xml = $this->basicXml();
        $xml->addChild('token', $token);
        $url = 'https://api.sielsystems.nl/acumulus/stable/invoices/invoice_paymentstatus_get.php';
        $xml_string = urlencode($xml->asXML());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $rawData = curl_exec($ch);
        $result = json_decode(json_encode((array) simplexml_load_string($rawData)), true);
        logModuleCall('acumulus_connect', 'invoice_paymentstatus_get()', $xml->asXML(), $rawData, $result, $this->getReplaceVars($config));
        curl_close($ch);

        return $result['invoice'];
    }

    /**
     * Helper function to inverse the amounts for a credit invoice.
     */
    public function inverseInvoiceAmounts(array $invoice): array
    {
        $negativeItems = [];
        //Set invoice amounts  negative.
        foreach ($invoice['items']['item'] as $item) {
            $item['amount'] = number_format(0 - $item['amount'], 2);
            $negativeItems[] = $item;
        }
        $negativeInvoice = $invoice;
        $negativeInvoice['items']['item'] = $negativeItems;
        $negativeInvoice['subtotal'] = number_format(0 - $negativeInvoice['subtotal'], 2);
        $negativeInvoice['tax'] = number_format(0 - $negativeInvoice['tax'], 2);
        $negativeInvoice['total'] = number_format(0 - $negativeInvoice['total'], 2);
        $negativeInvoice['balance'] = number_format(0 - $negativeInvoice['balance'], 2);

        return ($negativeInvoice);
    }

    /**
     * Helper function to construct the basic XML.
     */
    public function basicXml(bool $includeWarnings = true): SimpleXMLElement
    {
        $config = $this->get_config();
        // Create The XML FILE.
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><myxml></myxml>');
        // Contract details
        $contract = $xml->addChild('contract');
        if ($contract === null) {
            $this->raiseLibxmlError();
        }
        $contract->addChild('contractcode', $config['acumulus_code']);
        $contract->addChild('username', $config['acumulus_username']);
        $contract->addChild('password', $config['acumulus_password']);
        if ($includeWarnings) {
            // Do not include child object if entry is empty.
            if (!empty($config['acumulus_warning_email_address'])) {
                $contract->addChild('emailonerror', $config['acumulus_warning_email_address']);
            }
            // Do not include child object if entry is empty.
            if (!empty($config['acumulus_error_email_address'])) {
                $contract->addChild('emailonwarning', $config['acumulus_error_email_address']);
            }
        }
        // The connector feedback code.
        $connector = $xml->addChild('connector');
        if ($connector === null) {
            $this->raiseLibxmlError();
        }
        $connector->addChild('application', 'WHMCS ' . $this->getWHMCSVersion());
        $connector->addChild('webkoppel', 'Acumulus ' . $config['version']);
        $connector->addChild('development', 'SIEL - Buro RaDer');
        $connector->addChild('remark', 'PHP ' . PHP_VERSION);
        $connector->addChild('sourceuri', 'https://github.com/SIELOnline/acumulus-for-WHMCS');

        return $xml;
    }

    /**
     * Helper function to prepare the customer data for the XML.
     *
     * @noinspection PhpFunctionCyclomaticComplexityInspection
     */
    public function xmlPrepareCustomerDetails(array $config, array $invoice, array $client): array
    {
        // Convert country code to country name.
        $ISO3166 = new ISO3166();
        try {
            $country = $ISO3166->getByAlpha2($client['countrycode'])['name'];
        } catch (InvalidArgumentException $e) {
            $this->logException($e);
            $country = '';
        }

        // Since WHMCS 7.7, WHMCS has a vat id field of its own. Before that, this
        // module supported a custom field to register this information. We still
        // support that custom field if no vat id has been set in the proper
        // WHMCS field and the name of the custom field has been set.
        $vatNr = $client['tax_id'];
        if (empty($vatNr) && !empty($config['acumulus_whmcs_vatfield'])) {
            // Get the id of the custom field corresponding the VAT title and loop
            // through the clients custom fields for the VAT value.
            $customFieldId = Capsule::table('tblcustomfields')->where('fieldname', $config['acumulus_whmcs_vatfield'])->value('id');
            foreach ($client['customfields'] as $val) {
                if ($val['id'] === $customFieldId) {
                    $vatNr = $val['value'];
                    break;
                }
            }
        }

        // Get the id of the custom field corresponding to the IBAN field and loop
        // through the clients custom fields for the IBAN value.
        $IBAN = '';
        if (!empty($config['acumulus_whmcs_ibanfield'])) {
            $customFieldId = Capsule::table('tblcustomfields')->where('fieldname', $config['acumulus_whmcs_ibanfield'])->first()->id;
            foreach ($client['customfields'] as $val) {
                if ($val['id'] === $customFieldId) {
                    $IBAN = $val['value'];
                    break;
                }
            }
        }

        $fullName = '';
        $prefix = '';
        if (!empty($client['firstname'])) {
            $fullName .= $client['firstname'];
            $prefix = ' ';
        }
        if (!empty($client['lastname'])) {
            $fullName .= $prefix . $client['lastname'];
        }

        // Set how the customer is being imported into Acumulus
        // 1 = debtor, 2 = creditor, 3 = debtor/creditor (neutral)
        $type = match ($config['acumulus_customer_type']) {
            'Debtor' => '1',
            'Creditor' => '2',
            default => '3',
        };

        // Set the status of the user to the same status as WHMCS.
        // 0 = Not active / disabled, 1 = Active
        $contactstatus = match ($client['status']) {
            'Closed', 'Inactive' => '0',
            default => '1',
        };

        // Use Automatic prefill of country name based on supplied country code,
        // Yes with Nederland or Leave the same as in WHMCS?.
        $countryautoname = match ($config['acumulus_customer_countryautoname']) {
            'Automatic prefill based on country code' => '1',
            'Automatic prefill based on country code including Nederland' => '2',
            default => '0',
        };

        $customerDetails['type'] = $type;
        $customerDetails['contactid'] = '';
        $customerDetails['contactyourid'] = $client['userid'] ?? '';
        $customerDetails['contactstatus'] = $contactstatus;
        if (!empty($client['companyname'])) {
            $customerDetails['companyname1'] = $client['companyname'];
            $customerDetails['companyname2'] = '';
        }
        $customerDetails['fullname'] = $fullName;
        $customerDetails['salutation'] = '';
        $customerDetails['address1'] = $client['address1'] ?? '';
        $customerDetails['address2'] = $client['address2'] ?? '';
        // Remove any whitespaces from the postcode
        $customerDetails['postalcode'] = (isset($client['postcode']))
            ? preg_replace('/\s+/', '', $client['postcode'])
            : '';
        $customerDetails['city'] = ($client['city'] ?? '') . ((!empty($client['state'])) ? ',  ' . $client['state'] : '');
        $customerDetails['country'] = $country;
        $customerDetails['countrycode'] = $client['countrycode'] ?? '';
        //Automatic prefill of countryname based on supplied countrycode ?
        $customerDetails['countryautoname'] = $countryautoname;
        $customerDetails['vatnumber'] = $vatNr;
        $customerDetails['telephone'] = $client['phonenumber'] ?? '';
        $customerDetails['fax'] = '';
        $customerDetails['email'] = $client['email'] ?? '';
        // 0 = No update made, 1 = Overwrite all customer contact details
        $customerDetails['overwriteifexists'] = (isset($config['acumulus_customer_overwriteifexists']) && $config['acumulus_customer_overwriteifexists'] === 'on') ? '1' : '0';
        $customerDetails['bankaccountnumber'] = $IBAN;
        $customerDetails['mark'] = $this->replaceVarsInText($config['acumulus_cusromer_mark'], $invoice, $client);
        // 0 = Leave older duplicate contacts as is, 1 = Mark duplicate contacts as disabled
        $customerDetails['disableduplicates'] = (isset($config['acumulus_customer_disableduplicates']) && $config['acumulus_customer_disableduplicates'] === 'on') ? '1' : '0';

        return $customerDetails;
    }

    /**
     * Helper function to prepare the invoice data for the XML.
     */
    public function xmlPrepareInvoiceDetails(array $config, array $invoice, array $client, bool $isCredit = false): array
    {
        // https://github.com/SIELOnline/acumulus-for-WHMCS/issues/2: Sending
        //   invoices manually always uses default account.
        // I'm not sure that this is the right solution but according to the
        // issue poster it works for them. Looking at the call tree,
        // $this->get_config() will for all possible execution paths
        // already have been called. But perhaps, it is called too early, when not
        // all data of WHMCS itself has been initialised/is readily available???
        // Calling it once more seems to be a quite innocent action...
        $config = array_merge($config, $this->get_config());

        // Format: yyyy-mm-dd.
        $invoiceDetails['issuedate'] = $invoice['date'];
        // When omitted, or when no match has been made possible, the first available cost centre in the contract will be selected.
        $invoiceDetails['costcenter'] = $config['acumulus_invoice_default_costcenterid'];
        // 1 = Due (default), 2 = Paid
        $invoiceDetails['paymentstatus'] = ($invoice['status'] === 'Paid' ? '2' : '1');
        // Change the format from  yyyy-mm-dd hh:mm:ss to yyyy-mm-dd  and unset var if eq 0000-00-00.
        // Format: yyyy-mm-dd.
        $invoiceDetails['paymentdate'] = explode(' ', $invoice['datepaid'])[0] === '0000-00-00'
            ? null
            : explode(' ', $invoice['datepaid'])[0];
        // When omitted, or when no match has been made possible, the first available template in the contract will be selected.
        $invoiceDetails['template'] = $config['acumulus_invoice_templateid'];

        // If ['acumulus_use_acumulus_invoice_numbering'] is disabled we use the
        // WHMCS invoice number.
        if ($config['acumulus_use_acumulus_invoice_numbering'] !== 'on') {
            // Check if invoice number exists or use the invoice id instead.
            if (empty($invoice['invoicenum'])) {
                $invoice['invoicenum'] = $invoice['invoiceid'];
            }
            $invoiceDetails['number'] = $invoice['invoicenum'];
        }
        // Overall description of the invoice: invoice title.
        $invoiceDetails['description'] = $this->replaceVarsInText($config['acumulus_invoice_description'], $invoice, $client);
        // Multiline field for extended description of the invoice. Content will appear on invoice and associated emails. Use \n for newlines. Tabs are not supported.
        $invoiceDetails['descriptiontext'] = str_replace(
            "\n",
            "\\n",
            $this->replaceVarsInText($config['acumulus_invoice_descriptiontext'], $invoice, $client)
        );
        // Multiline field for additional remarks. Use \n for newlines and \t for tabs. Contents is placed in notes/comments section of the invoice. Content will not appear on the actual invoice or associated emails.
        $invoiceDetails['invoicenotes'] = str_replace(["\n", '{TAB}'],
            ["\\n", "\\t"],
            $this->replaceVarsInText($config['acumulus_invoice_invoicenotes'], $invoice, $client));

        // When omitted, or when no match has been made possible, the first available account number in the contract will be selected
        $invoiceDetails['accountnumber'] = $config['account_numbers'][$invoice['paymentmethod']]['id'];
        $invoiceDetails['vattype'] = $invoice['custom']['vattype'];

        // Credit Invoice adjustments.
        if ($isCredit) {
            // Overall description of the invoice, invoice title.
            $invoiceDetails['description'] = $this->replaceVarsInText($config['acumulus_creditinvoice_description'], $invoice, $client);
            $invoiceDetails['paymentstatus'] = 2;
            $invoiceDetails['paymentdate'] = date('Y-m-d');
        }

        // Invoice Line Variables.
        $invoiceDetails['invoicelines'] = [];
        if ($config['acumulus_summarize_invoice'] === 'on') {
            // Add total taxed items
            if (!empty($invoice['custom']['subtotal_taxedItems_exclTax'])) {
                $invoiceDetails['invoicelines'][] = [
                    // Non-mandatory, If set, this number will precede the product description (product).
                    'itemnumber' => null,
                    // Non-mandatory, Product or service description.
                    'product' => $this->replaceVarsInText($config['acumulus_summarization_text_taxed'], $invoice, $client),
                    // Non-mandatory,  'Product'( default ), 'Service'
                    'nature' => $config['acumulus_invoice_default_nature'],
                    // Non-mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                    'unitprice' => $invoice['custom']['subtotal_taxedItems_exclTax'],
                    // Mandatory,  Applicable vatrate for the product. Defaults to 21.
                    'vatrate' => $invoice['custom']['taxrate'],
                    // Non-mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                    'quantity' => '1',
                    // Non-mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
                    'costprice' => null,
                ];
            }
            // Add total untaxed items
            if (!empty($invoice['custom']['subtotal_untaxedItems'])) {
                $invoiceDetails['invoicelines'][] = [
                    // Non-mandatory, If set, this number will precede the product description (product).
                    'itemnumber' => null,
                    // Non-mandatory, Product or service description.
                    'product' => $this->replaceVarsInText($config['acumulus_summarization_text_untaxed'], $invoice, $client),
                    // non-mandatory,  'Product'( default ), 'Service'
                    'nature' => $config['acumulus_invoice_default_nature'],
                    // Non-mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                    'unitprice' => $invoice['custom']['subtotal_untaxedItems'],
                    // Mandatory,  Applicable vatrate for the product. Defaults to 21.
                    'vatrate' => '-1',
                    // Non-mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                    'quantity' => '1',
                    // Non-mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
                    'costprice' => null,
                ];
            }
        } else {
            foreach ($invoice['items']['item'] as $item) {
                /** @noinspection TypeUnsafeComparisonInspection */
                $invoiceDetails['invoicelines'][] = [
                    // Non-mandatory, if set, this number will precede the product
                    // description on an invoice line.
                    'itemnumber' => null,
                    // Non-mandatory, Product or service description.
                    'product' => preg_replace("/\r\n|\r|\n/", ' ', $item['description']),
                    // Non-mandatory,  'Product'( default ), 'Service'
                    'nature' => $config['acumulus_invoice_default_nature'],
                    // Non-mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                    'unitprice' => $item['custom_price_excl_tax_unrounded'],
                    // Mandatory,  Applicable vat rate for the product. Defaults to 21.
                    'vatrate' => ($item['taxed'] == 1) ? $invoice['custom']['taxrate'] : '-1',
                    // Non-mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                    'quantity' => '1',
                    // Non-mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
                    'costprice' => null,
                ];
            }
        }

        return $invoiceDetails;
    }

    /**
     * Helper function to construct the XML that will be sent to Acumulus.
     *
     * @noinspection PhpFunctionCyclomaticComplexityInspection
     */
    public function generateXml(array $config, array $invoice, array $client, bool $isCredit = false): SimpleXMLElement
    {
        // Create the basic XML.
        $customerDetails = $this->xmlPrepareCustomerDetails($config, $invoice, $client);
        $invoiceDetails = $this->xmlPrepareInvoiceDetails($config, $invoice, $client, $isCredit);

        // Create The XML file.
        $xml = $this->basicXml();

        // Add customer details to the XML.
        $customer = $xml->addChild('customer');
        if ($customer === null) {
            $this->raiseLibxmlError();
        }

        // Send non-mandatory customer information when enabled in module config.
        if ($config['acumulus_customer_import_enabled'] === 'on') {
            if (!empty($customerDetails['type'])) {
                $customer->addChild('type', $customerDetails['type']);
            }
            if (!empty($customerDetails['contactid'])) {
                $customer->addChild('contactid', $customerDetails['contactid']);
            }
            if (!empty($customerDetails['contactyourid'])) {
                $customer->addChild('contactyourid', $customerDetails['contactyourid']);
            }
            if (isset($customerDetails['contactstatus'])) {
                $customer->addChild('contactstatus', $customerDetails['contactstatus']);
            }
            if (!empty($customerDetails['companyname1'])) {
                $customer->addChild('companyname1', $customerDetails['companyname1']);
            }
            if (!empty($customerDetails['companyname2'])) {
                $customer->addChild('companyname2', $customerDetails['companyname2']);
            }
            if (!empty($customerDetails['fullname'])) {
                $customer->addChild('fullname', $customerDetails['fullname']);
            }
            if (!empty($customerDetails['salutation'])) {
                $customer->addChild('salutation', $customerDetails['salutation']);
            }
            if (!empty($customerDetails['address1'])) {
                $customer->addChild('address1', $customerDetails['address1']);
            }
            if (!empty($customerDetails['address2'])) {
                $customer->addChild('address2', $customerDetails['address2']);
            }
            if (!empty($customerDetails['postalcode'])) {
                $customer->addChild('postalcode', $customerDetails['postalcode']);
            }
            if (!empty($customerDetails['city'])) {
                $customer->addChild('city', $customerDetails['city']);
            }
            if (!empty($customerDetails['country'])) {
                $customer->addChild('country', $customerDetails['country']);
            }
            if (!empty($customerDetails['countrycode'])) {
                $customer->addChild('countrycode', $customerDetails['countrycode']);
            }
            if (isset($customerDetails['countryautoname'])) {
                $customer->addChild('countryautoname', $customerDetails['countryautoname']);
            }
            if (!empty($customerDetails['vatnumber'])) {
                $customer->addChild('vatnumber', $customerDetails['vatnumber']);
            }
            if (!empty($customerDetails['telephone'])) {
                $customer->addChild('telephone', $customerDetails['telephone']);
            }
            if (!empty($customerDetails['fax'])) {
                $customer->addChild('fax', $customerDetails['fax']);
            }
            if (!empty($customerDetails['email'])) {
                $customer->addChild('email', $customerDetails['email']);
            }
            if (isset($customerDetails['overwriteifexists'])) {
                $customer->addChild('overwriteifexists', $customerDetails['overwriteifexists']);
            }
            if (!empty($customerDetails['bankaccountnumber'])) {
                $customer->addChild('bankaccountnumber', $customerDetails['bankaccountnumber']);
            }
            if (!empty($customerDetails['mark'])) {
                $customer->addChild('mark', $customerDetails['mark']);
            }
            if (isset($customerDetails['disableduplicates'])) {
                $customer->addChild('disableduplicates', $customerDetails['disableduplicates']);
            }
        } else {
            // Only send mandatory info.
            if (!empty($customerDetails['countrycode'])) {
                $customer->addChild('countrycode', $customerDetails['countrycode']);
            }
            if (!empty($customerDetails['vatnumber'])) {
                $customer->addChild('vatnumber', $customerDetails['vatnumber']);
            }
        }

        // Add Invoice details to the XML.
        $xmlInvoice = $customer->addChild('invoice');
        if ($xmlInvoice === null) {
            $this->raiseLibxmlError();
        }
        if (!empty($invoiceDetails['number'])) {
            $xmlInvoice->addChild('number', $invoiceDetails['number']);
        }
        if (!empty($invoiceDetails['vattype'])) {
            $xmlInvoice->addChild('vattype', $invoiceDetails['vattype']);
        }
        if (!empty($invoiceDetails['issuedate'])) {
            $xmlInvoice->addChild('issuedate', $invoiceDetails['issuedate']);
        }
        if (!empty($invoiceDetails['costcenter'])) {
            $xmlInvoice->addChild('costcenter', $invoiceDetails['costcenter']);
        }
        if (!empty($invoiceDetails['accountnumber'])) {
            $xmlInvoice->addChild('accountnumber', $invoiceDetails['accountnumber']);
        }
        if (!empty($invoiceDetails['paymentdate'])) {
            $xmlInvoice->addChild('paymentdate', $invoiceDetails['paymentdate']);
        }
        if (!empty($invoiceDetails['paymentstatus'])) {
            $xmlInvoice->addChild('paymentstatus', $invoiceDetails['paymentstatus']);
        }
        if (!empty($invoiceDetails['description'])) {
            $xmlInvoice->addChild('description', $invoiceDetails['description']);
        }
        if (!empty($invoiceDetails['descriptiontext'])) {
            $xmlInvoice->addChild('descriptiontext', $invoiceDetails['descriptiontext']);
        }
        if (!empty($invoiceDetails['template'])) {
            $xmlInvoice->addChild('template', $invoiceDetails['template']);
        }
        if (!empty($invoiceDetails['invoicenotes'])) {
            $xmlInvoice->addChild('invoicenotes', $invoiceDetails['invoicenotes']);
        }

        // Add Invoice lines to the XML
        if (!empty($invoiceDetails['invoicelines'])) {
            foreach ($invoiceDetails['invoicelines'] as $invoiceLine) {
                // Set unitprice when omitted.
                if (empty($invoiceLine['unitprice'])) {
                    $invoiceLine['unitprice'] = '0.000';
                }

                $xlmInvoiceLine = $xmlInvoice->addChild('line');
                if ($xlmInvoiceLine === null) {
                    $this->raiseLibxmlError();
                }
                if (!empty($invoiceLine['itemnumber'])) {
                    $xlmInvoiceLine->addChild('itemnumber', $invoiceLine['itemnumber']);
                }
                if (!empty($invoiceLine['product'])) {
                    $xlmInvoiceLine->addChild('product', $invoiceLine['product']);
                }
                if (!empty($invoiceLine['nature'])) {
                    $xlmInvoiceLine->addChild('nature', $invoiceLine['nature']);
                }
                if (!empty($invoiceLine['unitprice'])) {
                    $xlmInvoiceLine->addChild('unitprice', $invoiceLine['unitprice']);
                }
                if (!empty($invoiceLine['vatrate'])) {
                    $xlmInvoiceLine->addChild('vatrate', $invoiceLine['vatrate']);
                }
                if (!empty($invoiceLine['quantity'])) {
                    $xlmInvoiceLine->addChild('quantity', $invoiceLine['quantity']);
                }
                if (!empty($invoiceLine['costprice'])) {
                    $xlmInvoiceLine->addChild('costprice', $invoiceLine['costprice']);
                }
            }
        }

        // Let Acumulus Send invoice to client
        if ($config['acumulus_emailaspdf'] === 'on') {
            $xlmInvoicePdfData = $xmlInvoice->addChild('emailaspdf');    // Imported invoices can be sent as PDF file using email by Acumulus.
            if ($xlmInvoicePdfData === null) {
                $this->raiseLibxmlError();
            }
            if (!empty($customerDetails['email'])) {
                $xlmInvoicePdfData->addChild('emailto', $customerDetails['email']);
            }
            if (!empty($config['acumulus_emailaspdf_emailbcc'])) {
                $xlmInvoicePdfData->addChild('emailbcc', $config['acumulus_emailaspdf_emailbcc']);
            }
            if (!empty($config['acumulus_emailaspdf_emailfrom'])) {
                $xlmInvoicePdfData->addChild('emailfrom', $config['acumulus_emailaspdf_emailfrom']);
            }
            if (!empty($config['acumulus_emailaspdf_subject'])) {
                $xlmInvoicePdfData->addChild('subject', $this->replaceVarsInText($config['acumulus_emailaspdf_subject'], $invoice, $client));
            }
            if (!empty($config['acumulus_emailaspdf_message'])) {
                $xlmInvoicePdfData->addChild(
                    'message',
                    str_replace("\n", "\\n", $this->replaceVarsInText($config['acumulus_emailaspdf_message'], $invoice, $client))
                );
            }

            if ($config['acumulus_emailaspdf_confirmreading'] === 'on') {
                $xlmInvoicePdfData->addChild('confirmreading', '1');  // 1 = Ask for confirmation
            } else {
                $xlmInvoicePdfData->addChild('confirmreading', '0');  // 0 = Do not ask for confirmation
            }
        }

        return $xml;
    }

    /*
     * Functions called by Hooks.
     */

    /**
     * Sends the data of an invoice to Acumulus.
     */
    public function sendInvoice(array $config, int $invoiceId): void
    {
        // Retrieve the invoice and customer.
        $invoice = $this->getInvoice($invoiceId);
        $client = $this->getClient($invoice['userid']);

        //Make the xml file
        $xml = $this->generateXml($config, $invoice, $client);

        //Send xml to Acumulus
        $this->sendInvoiceToAcumulus($config, $invoice, $xml);
    }

    /**
     * Updates the payment status of an invoice entry at Acumulus.
     */
    public function updateInvoice(array $config, int $invoiceId, string $useDate = null): void
    {
        $invoice = $this->getInvoice($invoiceId);

        // Retrieve the token from the mod_acumulus_connect table
        $token = Capsule::table('mod_acumulus_connect')->where('id', $invoice['invoiceid'])->value('token');
        // if the token exists update the invoice in Acumulus, else send entire invoice.
        if ($token !== null) {
            // Update payment gateway if 'use last payment method' is enabled and if it differs from the invoice set payment method.
            if ($config['acumulus_invoice_use_last_paymentmethod'] === 'on'
                && !empty($invoice['transactions']['transaction'])
            ) {
                $lastPaymentGateway = end($invoice['transactions']['transaction'])['gateway'];
                if ($invoice['paymentmethod'] !== $lastPaymentGateway) {
                    logActivity(__FUNCTION__ . "($invoiceId): updating payment method in WHMCS");
                    $this->updateInvoicePaymentMethod($config, $invoiceId, $lastPaymentGateway);
                    // Update the payment method of the invoice in whmcs.
                    // https://developers.whmcs.com/api-reference/updateinvoice/
                    $command = 'UpdateInvoice';
                    $postData = [
                        'invoiceid' => $invoiceId,
                        'paymentmethod' => $lastPaymentGateway,
                    ];
                    $this->localAPI($command, $postData);
                }
            }

            // Update invoice to "paid".
            $xml = $this->basicXml();
            $xml->addChild('token', $token);
            $xml->addChild('paymentstatus', '2');
            if (empty($useDate)) {
                $xml->addChild('paymentdate', substr($invoice['datepaid'], 0, 10));
            } else {
                $xml->addChild('paymentdate', $useDate);
            }

            $url = 'https://api.sielsystems.nl/acumulus/stable/invoices/invoice_paymentstatus_set.php';
            $xml_string = urlencode($xml->asXML());
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $rawData = curl_exec($ch);
            $result = json_decode(json_encode((array) simplexml_load_string($rawData)), true);
            logModuleCall('acumulus_connect', 'invoice_paymentstatus_set', $xml->asXML(), $rawData, $result, $this->getReplaceVars($config));

            if (isset($result['status'])) {
                switch ($result['status']) {
                    case '0':  // Success
                        $resultStatus = 'success';
                        $messages = '';
                        break;
                    case '1':  // Failed.
                        $resultStatus = 'error(s)';
                        $messages = print_r($result['error'], true);
                        break;
                    case '2':  // Success with warnings.
                        $resultStatus = 'warning(s)';
                        $messages = print_r($result['warning'], true);
                        break;
                    default: // Unknown code.
                        $resultStatus = 'error';
                        $messages = "Unspecified Error code {$result['status']}";
                }
            } else {
                // Exception/curl error.
                $resultStatus = 'exception';
                $messages = 'Error reaching acumulus API webservice';
            }
            logActivity(__FUNCTION__ . "($invoiceId): $resultStatus $messages.");
            curl_close($ch);
        } else {
            // Token not found make a module log entry and send entire invoice.
            logActivity(__FUNCTION__ . "($invoiceId) not yet sent, sending.");
            $this->sendInvoice($config, $invoiceId);
        }
    }

    /**
     * Updates the account number of an invoice entry in Acumulus.
     */
    public function updateInvoicePaymentMethod(array $config, int $invoiceId, string $paymentMethod): void
    {
        $entryId = Capsule::table('mod_acumulus_connect')->where('id', $invoiceId)->value('entryid');

        if (!empty($entryId)) {
            $accountNumber = $config['account_numbers'][$paymentMethod]['id'];
            //Update the entry account number
            $xml = $this->basicXml();
            $xml->addChild('entryid', $entryId);
            $xml->addChild('accountnumber', $accountNumber);

            $url = 'https://api.sielsystems.nl/acumulus/stable/entry/entry_update.php';
            $xml_string = urlencode($xml->asXML());
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $rawData = curl_exec($ch);
            $result = json_decode(json_encode((array) simplexml_load_string($rawData)), true);
            logModuleCall('acumulus_connect', 'entry_update', $xml->asXML(), $rawData, $result, $this->getReplaceVars($config));

            if (isset($result['status'])) {
                switch ($result['status']) {
                    case '0':  // Success
                        $resultStatus = 'success';
                        $messages = '';
                        break;
                    case '1':  // Failed.
                        $resultStatus = 'error(s)';
                        $messages = print_r($result['error'], true);
                        break;
                    case '2':  // Success with warnings.
                        $resultStatus = 'warning(s)';
                        $messages = print_r($result['warning'], true);
                        break;
                    default: // Unknown code.
                        $resultStatus = 'error';
                        $messages = "Unspecified Error code {$result['status']}";
                }
            } else {
                // Exception/curl error.
                $resultStatus = 'exception';
                $messages = 'Error reaching acumulus API webservice';
            }
            logActivity(__FUNCTION__ . "($invoiceId): $resultStatus $messages.");
            curl_close($ch);
        }
    }

    /**
     * Refund an invoice by creating a credit note.
     */
    public function invoiceCancelled(array $config, int $invoiceId): void
    {
        // Run whmcs api to retrieve the invoice and customer.
        $invoice = $this->getInvoice($invoiceId, false);
        $client = $this->getClient($invoice['userid']);

        // check if $this->use_acumulus_invoice_numbering is used, if so, send an invoice with negative amounts (credit invoice).
        if ($config['acumulus_use_acumulus_invoice_numbering'] === 'on') {
            // Check if the token exists, otherwise just create an activity record
            // and do nothing else.
            // Retrieve the token from the mod_acumulus_connect table.
            $token = Capsule::table('mod_acumulus_connect')->where('id', $invoice['invoiceid'])->value('token');
            // If the token exists, update the invoice in Acumulus, else send the
            // entire invoice.
            if ($token !== null) {
                // Set invoice to paid if its unpaid (get status from acumulus api).
                $paymentStatus = $this->getPaymentStatus($config, $token);
                /** @noinspection TypeUnsafeComparisonInspection value is an int in the Acumulus API */
                if ($paymentStatus == '0') {
                    $paymentDate = date('Y-m-d');
                    $this->updateInvoice($config, $invoiceId, $paymentDate);
                }

                // Inverse the amounts in the invoice.
                $negativeInvoice = $this->inverseInvoiceAmounts($invoice);
                $negativeInvoice = $this->expandInvoiceWithCustomValues($negativeInvoice, $client);

                // Make the xml file.
                $xml = $this->generateXml($config, $negativeInvoice, $client, true);

                // Send new credit invoice (xml) to Acumulus.
                $this->sendInvoiceToAcumulus($config, $negativeInvoice, $xml);
            } else {
                logActivity(__FUNCTION__ . "($invoiceId): no credit invoice created because no invoice was sent.");
            }
        } else {
            logActivity(
                "acumulus - Credit invoice not created not using acumulus sequential invoice numbering. ($invoiceId for User ID: {$client['userid']})"
            );
        }
    }

    /**
     * Throws an exception with all libxml error messages as message.
     *
     * @throws \RuntimeException
     *   Always.
     */
    public function raiseLibxmlError(): void
    {
        $errors = libxml_get_errors();
        $messages = [];
        foreach ($errors as $error) {
            // Overwrite our own code with the 1st code we get from libxml.
            $messages[] = sprintf(
                'Line %d, column: %d: %s %d - %s',
                $error->line,
                $error->column,
                $error->level === LIBXML_ERR_WARNING ? 'warning' : 'error',
                $error->code,
                trim($error->message)
            );
        }
        throw new RuntimeException(implode("\n", $messages));
    }
}
