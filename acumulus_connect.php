<?php
/**
 * @noinspection HtmlDeprecatedAttribute
 * @noinspection HtmlDeprecatedTag
 * @noinspection HtmlRequiredAltAttribute
 * @noinspection DuplicatedCode  remove at a later stage and extract those
 * duplicates into helper functions.
 * @noinspection PhpUnused  The functions in this file are called by WHMCS based
 *   on naming patterns.
 * @todo: remove these inspections and the warnings that will show again.
 *
 * A note on security:
 * https://laravel.com/docs/8.x/queries#introduction says:
 *     The Laravel query builder uses PDO parameter binding to protect your
 *     application against SQL injection attacks. There is no need to clean
 *     or sanitize strings passed to the query builder as query bindings.
 *
 *     PDO does not support binding column names. Therefore, you should
 *     never allow user input to dictate the column names referenced by your
 *     queries, including "order by" columns.
 *
 * So by using the query builder and not constructing our own queries we are
 * safe against sql injection attacks (and differences in the slq dialect of the
 * actual database used).
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

require_once('acumulus_connect_functions.php');
/** @noinspection PhpIncludeInspection  false positive */
require_once('assets/gplv3.php');

/*
 *  Module Mandatory functions.
 */

/**
 * Function to return the configuration array for the Acumulus module.
 *
 * @return array
 */
function acumulus_connect_config(): array
{
    $config = acumulus_connect_getConfig();

    //Check if any credentials are given or show the basic config.
    if ((!empty($config["acumulus_code"])) && (!empty($config["acumulus_username"])) && (!empty($config["acumulus_password"]))) {
        // Construct the xml for the Acumulus API check

        $xml = acumulus_connect_basicXML(false); //construct the basic xml without email on errors or warnings.
        $xml->addChild('format', 'xml');
        $xml_string = urlencode($xml->asXML());

        // Lets check the credentials against the Acumulus API.
        $url = "https://api.sielsystems.nl/acumulus/stable/general/general_about.php";
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
            $config_array = acumulus_connect_construct_full_configarray();
        } else {
            // the entered credentials are not valid.
            $config_array = acumulus_connect_construct_basic_configarray();
            $config_array["fields"][] = [
                "FriendlyName" => "Credential check",
                "Description" => '<font color="red"><b>' . 'The credentials are not correct.' . '</b></font>'
            ];
        }
    } else {
        // one or more required credentials are not given.
        $config_array = acumulus_connect_construct_basic_configarray();
        $config_array["fields"][] = [
            "FriendlyName" => "Credential check",
            "Description" => '<font color="blue"><b>' . 'Please enter your credentials and click "Save Changes", to continue configurating.' . '</b></font>',
        ];
    }
    return $config_array;
}

/**
 * Performs custom actions on activating this module.
 *
 * - Create DB table
 *
 * @return string[]
 *   Results of the activation.
 */
function acumulus_connect_activate(): array
{
    try {
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
    } catch (Exception $e) {
        logActivity('Acumulus_Connect: Installing the module failed Unable to create my_table: ' . $e->getMessage());
        return ['status' => 'error', 'description' => 'Installing the module failed Unable to create my_table: ' . $e->getMessage()];
    }

    // Return the result.
    return ['status' => 'success', 'description' => 'The Acumulus module is installed Successfully, please fill in the configuration data.'];
}

/**
 * Performs custom actions on deactivating this module.
 *
 * - Remove Custom DB Table
 *
 * @return string[]
 *   Results of the activation.
 */
function acumulus_connect_deactivate(): array
{
    try {
        Capsule::schema()->drop('mod_acumulus_connect');
    } catch (Exception $e) {
        logActivity('Acumulus_Connect: Deactivating the module failed: ' . $e->getMessage());
        return ['status' => 'error', 'description' => 'Deactivating the module failed: ' . $e->getMessage()];
    }

    // Return the result
    return ['status' => 'success', 'description' => 'The Acumulus module is deactivated Successfully'];
}


/**
 * Performs custom actions on upgrading this module.
 */
function acumulus_connect_upgrade($vars)
{
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
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
}

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------
   Module Additional functions
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Return html to add to the sidebar.
 *
 * @param array $vars
 *
 * @return string
 */
function acumulus_connect_sidebar(array $vars): string
{
    $version = $vars['version'];
    $lang = $vars['_lang'];

    $sidebar = '<span class="header"><img src="images/icons/addonmodules.png" class="absmiddle" width="16" height="16" alt="" />Acumulus</span>
        <ul class="menu">
                <li><a href="#">' . $lang['Version'] . ': ' . $version . '</a></li>';
    if (isset($_SESSION['acumulus_connect_newversion'])) {
        $sidebar .= '<li><a STYLE="color: #FF0000; font-weight: bold;" href="https://forum.acumulus.nl/index.php/topic,4183.0.html" target="_blank">' . $lang['update available'] . '</a></li>';
    }
    $sidebar .= '</ul>';

    return $sidebar;
}

/**
 * Output our screen?
 *
 * @param array $vars
 */
function acumulus_connect_output(array $vars)
{
    global $_SESSION;
    $lang = $vars['_lang'];
    if (isset($_POST["action"])) {
        if (empty($_POST['resentinvoice']) and ($_POST['action'] == 'sendinvoice')) {
            echo "<br><h2>" . $lang['No records found message'] . "</h2>";
            echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $lang['Return'] . '</a>';

            return;
        }
        switch ($_POST["action"]) {
            case "sendinvoice":
                $invoiceid = $_POST['resentinvoice'];
                $searchon = $_POST['search_on'];
                switch ($searchon) {
                    case "invoiceno":
                        try {
                            $invoiceids = Capsule::table('tblinvoices')->select('id')->where('invoicenum', $invoiceid)->get()[0]->id;
                        } catch (Exception $e) {
                            echo "<br><h2>" . $lang['No records found message'] . "</h2>";
                            echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $lang['Return'] . '</a>';
                            return;
                        }
                        if (empty($invoiceids)) {
                            echo "<br><h2>" . $lang['No records found message'] . "</h2>";
                            echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $lang['Return'] . '</a>';
                            return;
                        }
                        $invoices = [$invoiceids];
                        break;
                    default:
                        $invoices = [$invoiceid];
                }
                if (empty($invoices[0])) {
                    echo "<br><h2>" . $lang['No records found message'] . "</h2>";
                    echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $lang['Return'] . '</a>';
                    return;
                }

                echo acumulus_connect_invoicesummary($invoices, $vars);
                echo '<form method="post" action="addonmodules.php?module=acumulus_connect">
                <input type="hidden" name="action" value="sendinvoicesnow">
                     ' . $lang['Resent Invoice'] . ' <input type="submit" value="' . $lang['Submit Invoice'] . '" class="btn-success" name="Submit" onclick="return confirm(\'' . $lang['Confirm Send single'] . '\')" />
                    <a href="addonmodules.php?module=acumulus_connect" class="btn">Return</a>
                </p>
                </form>';
                break;
            case "sendbatch":
                $invoices = [];
                $filterby = $_POST['filterby'];
                $filterby2 = $_POST['filterby2'];
                $datefrom = toMySQLDate($_POST['datefrom']);
                $dateto = toMySQLDate($_POST['dateto']);

                // @todo: optimize by building the query step by step.
                switch ($filterby) {
                    case "Date Paid":
                        if ($filterby2 === "All Gateways") {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('datepaid', ">=", $datefrom . ' 00:00:00')
                                              ->where('datepaid', "<=", $dateto . ' 23:59:59')
                                              ->where('status', 'Paid')
                                              ->get();
                        } else {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('datepaid', ">=", $datefrom . ' 00:00:00')
                                              ->where('datepaid', "<=", $dateto . ' 23:59:59')
                                              ->where('status', 'Paid')
                                              ->where('paymentmethod', $filterby2)
                                              ->get();
                        }
                        break;
                    case "Unpaid Invoices":
                        if ($filterby2 === "All Gateways") {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('date', ">=", $datefrom)
                                              ->where('date', "<=", $dateto)
                                              ->where('status', 'Unpaid')
                                              ->get();
                        } else {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('date', ">=", $datefrom)
                                              ->where('date', "<=", $dateto)
                                              ->where('status', 'Unpaid')
                                              ->where('paymentmethod', $filterby2)
                                              ->get();
                        }
                        break;
                    case "Paid Invoices by invoicedate":
                        if ($filterby2 == "All Gateways") {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('date', ">=", $datefrom)
                                              ->where('date', "<=", $dateto)
                                              ->where('status', 'Paid')
                                              ->get();
                        } else {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('date', ">=", $datefrom)
                                              ->where('date', "<=", $dateto)
                                              ->where('status', 'Paid')
                                              ->where('paymentmethod', $filterby2)
                                              ->get();
                        }
                        break;
                    default:
                        if ($filterby2 == "All Gateways") {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('date', ">=", $datefrom)
                                              ->where('date', "<=", $dateto)
                                              ->get();
                        } else {
                            $results = Capsule::table('tblinvoices')->select('id')
                                              ->where('date', ">=", $datefrom)
                                              ->where('date', "<=", $dateto)
                                              ->where('paymentmethod', $filterby2)
                                              ->get();
                        }
                }
                foreach ($results as $result) {
                    $invoices[] = $result->id;
                }

                echo '<div class="infobox"><strong><span class="title">' . $lang['Batch import'] . '</span></strong><br />' . $lang['Batch import time warning'] . '</div>';
                echo acumulus_connect_invoicesummary($invoices, $vars);
                echo '<form method="post" action="addonmodules.php?module=acumulus_connect">
                <input type="hidden" name="action" value="sendinvoicesnow">
                     ' . $lang['Sent Above Invoices'] . '&nbsp;&nbsp;&nbsp; <input type="submit" value="' . $lang['Sent Invoices'] . '" class="btn-success" name="Submit" onclick="return confirm(\'' . $lang['Confirm Send'] . '\')" />
                    <a href="addonmodules.php?module=acumulus_connect" class="btn">' . $lang['Return'] . '</a>
                </p>
                </form>';
                break;
            case "sendinvoicesnow":
                // @todo: what did this statement do here?
                // $invoiceid = $_POST['invoiceid'];
                if (isset($_SESSION['acumulus_connect_sendinvoices'])) {
                    foreach ($_SESSION['acumulus_connect_sendinvoices'] as $invoiceid) {
                        acumulus_connect_sendInvoice($vars, $invoiceid);
                    }
                    echo('<div class="infobox"><strong><span class="title">' . $lang['Check activity'] . '</span></strong><br />' . $lang['Check Import Acumulus'] . '</div>');
                    echo('<p>' . str_replace('$$', '<a href="systemactivitylog.php">' . $lang['Activity Log'] . '</a>', $lang['Send Result Message']) . '</p>');
                    unset($_SESSION['acumulus_connect_sendinvoices']);
                } else {
                    echo('<div class="errorbox"><strong><span class="title">' . $lang['No records found'] . '</span></strong><br />' . $lang['No records found message'] . '</div>');
                }
                break;
        }
    } else {
        acumulus_connect_show_module_form($vars);
    }
}

/**
 * Helper function to create the basic config array used to enter credentials.
 *
 * @return array
 */
function acumulus_connect_construct_basic_configarray(): array
{
    return [
        // This is where the module name is defined!:
        "name" => "Acumulus",
        "description" => "The Acumulus module connects to the Acumulus online financial administration application.",
        // This is where the module version is defined!:
        "version" => "3.3",
        "author" => 'SIEL & Buro RaDer',
        "language" => "english",
        "fields" => [
            "acumulus_code" => [
                "FriendlyName" => "Contract code",
                "Type" => "text",
                "Size" => "25",
                "Description" => "Enter the Acumulus contract code here.",
                "Default" => "",
            ],
            "acumulus_username" => [
                "FriendlyName" => "Username",
                "Type" => "text",
                "Size" => "25",
                "Description" => "Enter the Acumulus username here.",
                "Default" => "",
            ],
            "acumulus_password" => [
                "FriendlyName" => "Password",
                "Type" => "password",
                "Size" => "25",
                "Description" => "Enter the Acumulus password here.",
            ],
        ],
    ];
}

/**
 * Helper function to create the full config array used after credentials are
 * correct.
 *
 * @return array
 */
function acumulus_connect_construct_full_configarray(): array
{
    $config = acumulus_connect_getConfig();
    $config_array = acumulus_connect_construct_basic_configarray();

    // Get the cost centers from Acumulus and put them in a comma separated
    // string.
    $stringCostCenters = '';
    foreach (acumulus_connect_getCostcenters() as $costcenter) {
        $stringCostCenters .= $costcenter['costcenterid'] . ' ' . str_replace(',', ' ', $costcenter['costcentername']) . ',';
    }
    $stringCostCenters = rtrim($stringCostCenters, ",");

    // Get the invoice templates from Acumulus and put then in a comma separated
    // string.
    $stringTemplates = ',';
    foreach (acumulus_connect_getTemplates() as $template) {
        $stringTemplates .= $template['invoicetemplateid'] . ' ' . str_replace(',', ' ', $template['invoicetemplatename']) . ',';
    }
    $stringTemplates = rtrim($stringTemplates, ",");

    // Get the Account numbers from Acumulus and put them in a comma separated
    // string.
    $Acumulus_AccountNumbers = '';
    foreach (acumulus_connect_getAccounts() as $account) {
        $Acumulus_AccountNumbers .= $account['accountid'] . ' ' . str_replace(',', ' ', $account['accountnumber']) . ',';
    }
    $Acumulus_AccountNumbers = rtrim($Acumulus_AccountNumbers, ",");

    $config_array["fields"][] = ["FriendlyName" => "Credential check", "Description" => 'Credentials are correct.'];

    // Features.
    $config_array["fields"]["acumulus_features_hook_invoice_message1"] = [
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Features"),
    ];
    $config_array["fields"]["acumulus_emailaspdf"] = [
        "FriendlyName" => "Let Acumulus send invoice",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled imported invoices will be send as pdf file using Acumulus.<br><i>* When importing invoices with the bulk import, this setting is ignored and no invoices will be sent by Acumulus.</i>",
        "Default" => "no",
    ];

    $config_array["fields"]["acumulus_customer_import_enabled"] = [
        "FriendlyName" => "Import customer details",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Import WHMCS customer details in Acumulus when an invoice is send to Acumulus.",
        "Default" => "on",
    ];

    // Hook Variables.
    $config_array["fields"]["acumulus_hook_invoice_create_enabled"] = [
        "FriendlyName" => "Enable create hook",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Send Invoice to Acumulus directly when a new invoice has been generated by the cron, order process, API, when converting a quote to an invoice, or when published a draft invoice with email.",
        "Default" => "on",
    ];
    $config_array["fields"]["acumulus_hook_invoice_paid_enabled"] = [
        "FriendlyName" => "Enable paid hook",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Update or Create Invoice in Acumulus directly when paid for.",
        "Default" => "on",
    ];
    $config_array["fields"]["acumulus_hook_invoice_canceled_enabled"] = [
        "FriendlyName" => "Enable Canceled invoice hook",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Create Credit invoice in Acumulus when a invoice is being canceled.",
        "Default" => "on",
    ];

    // Customer Variables.
    if ($config['acumulus_customer_import_enabled'] === "on") {
        $config_array["fields"]["acumulus_customer_message1"] = [
            "FriendlyName" => "",
            "Description" => acumulus_connect_newConfigSection("Customer Settings"),
        ];

        $config_array["fields"]["acumulus_customer_type"] = [
            "FriendlyName" => "Customer Type",
            "Type" => "dropdown",
            "Options" => "Debtor,Creditor,Debtor/Creditor (neutral)",
            "Description" => "Select under what type the customer needs to be registered in Acumulus.",
            "Default" => "Debtor/Creditor (neutral)",
        ];
        $config_array["fields"]["acumulus_customer_countryautoname"] = [
            "FriendlyName" => "Customer country",
            "Type" => "dropdown",
            "Options" => "Use the same country as the customer in WHMCS,Automatic prefill based on country code,Automatic prefill based on country code including Nederland",
            "Description" => "Select which customer country setting needs to be used in Acumulus.",
            "Default" => "Use the same country as the customer in WHMCS",
        ];
        $config_array["fields"]["acumulus_customer_overwriteifexists"] = [
            "FriendlyName" => "Overwrite customer details",
            "Type" => "yesno",
            "Size" => "25",
            "Description" => "Overwrite customer contact details in Acumulus.",
            "Default" => "on",
        ];
        $config_array["fields"]["acumulus_customer_disableduplicates"] = [
            "FriendlyName" => "Disable customer duplicates",
            "Type" => "yesno",
            "Size" => "25",
            "Description" => "Disable older instances of a contact in Acumulus when multiple contacts match the customer email.",
        ];
        $config_array["fields"]["acumulus_whmcs_vatfield"] = [
            "FriendlyName" => "TAX or VAT field",
            "Type" => "dropdown",
            "Options" => implode(",", acumulus_connect_getClientCustomfields()) . ',[VAT Number]',
            "Description" => "WHMCS Vat field or Custom client field that represents the TAX ID or VAT number. The option [VAT Number] is the new vat field since WHMCS 7.7",
            "Default" => "[VAT Number]",
        ];
        $config_array["fields"]["acumulus_whmcs_ibanfield"] = [
            "FriendlyName" => "IBAN field",
            "Type" => "dropdown",
            "Options" => implode(",", acumulus_connect_getClientCustomfields()),
            "Description" => "Custom client field that represents the clients IBAN number.",
            "Default" => "",
        ];
        // @todo: correct typo but add an update function to retain value.
        $config_array["fields"]["acumulus_cusromer_mark"] = [
            "FriendlyName" => "Client Mark",
            "Type" => "text",
            "Size" => "40",
            "Description" => "Extra label or mark. <i>(See manual for variables that can be used.)</i>",
            "Default" => "WHMCS Klantnr: {USERID}",
        ];
    }

    // Standard Invoice Settings.
    $config_array["fields"]["acumulus_invoice_message1"] = [
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Standard Invoice Settings"),
    ];

    $config_array["fields"]["acumulus_invoice_default_costcenter"] = [
        "FriendlyName" => "Default Costcenter",
        "Type" => "dropdown",
        "Options" => $stringCostCenters,
        "Description" => "The default costcenter the new invoices will be booked to.",
        "Default" => "",
    ];

    $config_array["fields"]["acumulus_invoice_default_nature"] = [
        "FriendlyName" => "Default nature",
        "Type" => "dropdown",
        "Options" => 'Product,Service',
        "Description" => "The default Nature on wich an invoice line is booked.",
        "Default" => "Service",
    ];

    $config_array["fields"]["acumulus_use_acumulus_invoice_numbering"] = [
        "FriendlyName" => "Use Acumulus invoice numbering",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled, WHMCS ignors the invoice number and uses Acumulus sequential invoice numbering.<br>",
        "Default" => "on",
    ];

    $config_array["fields"]["acumulus_invoice_description"] = [
        "FriendlyName" => "Invoice title",
        "Type" => "text",
        "Size" => "40",
        "Description" => "Overall description of the invoice, invoice title.<br><i>(See manual for varibles that can be used.)</i>",
        "Default" => "WHMCS Factuur: {INVOICENUMBER}",
    ];

    $config_array["fields"]["acumulus_creditinvoice_description"] = [
        "FriendlyName" => "Credit Invoice title",
        "Type" => "text",
        "Size" => "40",
        "Description" => "Overall description for the Credit invoice, credit invoice title.<br><i>(See manual for varibles that can be used.)</i>",
        "Default" => "Credit Factuur:  WHMCS: {INVOICENUMBER}",
    ];

    $config_array["fields"]["acumulus_invoice_descriptiontext"] = [
        "FriendlyName" => "Invoice extended description",
        "Type" => "textarea",
        "Rows" => "4",
        "Cols" => "47",
        "Description" => "Multiline field for extended description of the invoice. Content will appear on invoice and associated emails.<br><i>(See manual for varibles that can be used.) note: tabs cannot be used here</i>",
        "Default" => "{INVOICENOTES}",
    ];

    $config_array["fields"]["acumulus_invoice_invoicenotes"] = [
        "FriendlyName" => "Invoice additional remarks",
        "Type" => "textarea",
        "Rows" => "4",
        "Cols" => "47",
        "Description" => "Multiline field for additional remarks.  Contents is placed in notes/comments section of the invoice. Content <b>will not appear</b> on the actual invoice or associated emails.<br><i>(See manual for varibles that can be used.) (Use {TAB} for tabs)</i>",
        "Default" => "",
    ];

    $config_array["fields"]["acumulus_invoice_template"] = [
        "FriendlyName" => "Invoice Template",
        "Type" => "dropdown",
        "Options" => $stringTemplates,
        "Description" => "Name of the template tht will be used by Acumulus. When omitted, the first available template in the contract will be selected.",
        "Default" => "",
    ];

    // Additional Invoice Settings.
    $config_array["fields"]["acumulus_invoice_message2"] = [
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Additional Invoice Settings"),
    ];

    $config_array["fields"]["acumulus_summarize_invoice"] = [
        "FriendlyName" => "Summarize invoice lines",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Combine all invoice lines to one total invoice line. <i>The field \"Invoice line description\" is used as the description on the invoice line.</i>",
        "Default" => "",
    ];

    $config_array["fields"]["acumulus_invoice_correction"] = [
        "FriendlyName" => "Invoice Correction",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled, the module will try to estimate the totals (WHMCS and ACUMULUS) and add a correction line when needed.",
        "Default" => "",
    ];
    if ($config['acumulus_invoice_correction'] == 'on') {
        $config_array["fields"]["acumulus_invoice_correction_text"] = [
            "FriendlyName" => "Invoice correction line description<br>",
            "Type" => "text",
            "Size" => "40",
            "Description" => "The text that will appear on the invoice if there is a correction needed.<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "WHMCS correctie",
        ];
    }
    if ($config['acumulus_summarize_invoice'] == 'on') {
        $config_array["fields"]["acumulus_summarization_text_taxed"] = [
            "FriendlyName" => "Invoice line Sumarization description<br>including TAX",
            "Type" => "text",
            "Size" => "40",
            "Description" => "The text that will be used on the summerized invoice line for items with tax.<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "Totaal WHMCS Factuur belast met BTW",
        ];

        $config_array["fields"]["acumulus_summarization_text_untaxed"] = [
            "FriendlyName" => "Invoice line Sumarization description<br>excluding TAX",
            "Type" => "text",
            "Size" => "40",
            "Description" => "The text that will be used on the summerized invoice line for items without tax.<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "Totaal WHMCS Factuur zonder BTW",
        ];
    }

    $config_array["fields"]["acumulus_invoice_use_last_paymentmethod"] = [
        "FriendlyName" => "Use last paymentmethod",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled, the module will change the account numbers invoice in Acumulus to use the last paymentmethod.",
        "Default" => "on",
    ];

    // Account number translation
    $config_array["fields"]["acumulus_accountnumber_message1"] = [
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Accountnumber translation"),
    ];

    foreach (acumulus_connect_getWHMCSAccountNumbers($config['acumulus_whmcs_admin']) as $accountNumber) {
        $config_array["fields"]["acumulus_AccountNumber_" . $accountNumber['module']] = [
            "FriendlyName" => "WHMCS Payment Gateway: <b>" . $accountNumber['displayname'] . "</b>",
            "Type" => "dropdown",
            "Options" => $Acumulus_AccountNumbers,
            "Description" => "Select the matching Acumulus AccountNumber.",
            "Default" => "",
        ];
    }

    // Send email as pdf from Acumulus.
    if ($config['acumulus_emailaspdf'] == 'on') {

        $config_array["fields"]["acumulus_emailaspdf_message1"] = [
            "FriendlyName" => "",
            "Description" => acumulus_connect_newConfigSection("Acumulus E-Mail Settings"),
        ];
        $config_array["fields"]["acumulus_emailaspdf_message2"] = [
            "FriendlyName" => "E-mail To",
            "Description" => "The invoice will be send to the primary customer email adres. WHMCS Aditional Contacts will be ignored.",
        ];

        $config_array["fields"]["acumulus_emailaspdf_emailbcc"] = [
            "FriendlyName" => "E-mail BCC",
            "Type" => "text",
            "Size" => "40",
            "Description" => "Use valid email addresses. Muliple addresses can be used when separated with a comma or semicolon. If emailto is not set, the emailbcc will be ignored and skipped.",
            "Default" => "",
        ];
        $config_array["fields"]["acumulus_emailaspdf_emailfrom"] = [
            "FriendlyName" => "Email From",
            "Type" => "text",
            "Size" => "40",
            "Description" => "Use a single valid emailaddress. If omitted, the email address of the invoice template, with fallback to the account owner will be used. Most pretty results are obtained when using fully configured invoice templates in Acumulus and leaving this option empty (recommended).",
            "Default" => "",
        ];
        $config_array["fields"]["acumulus_emailaspdf_subject"] = [
            "FriendlyName" => "E-mail Subject",
            "Type" => "text",
            "Size" => "40",
            "Description" => "ASCII-only allowed. Be sure to provide xml-escaped htmlentities for UTF-8 characters.<br>If omitted or left empty, the subject will be: Factuur [number] [description]<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "",
        ];
        $config_array["fields"]["acumulus_emailaspdf_message"] = [
            "FriendlyName" => "E-mail Message",
            "Type" => "textarea",
            "Rows" => "4",
            "Cols" => "47",
            "Description" => "Currently ASCII-only allowed. Mileage may vary when trying to submit multiple lines.<br>If omitted, the email text composed in the template will be used (recommended)<br><i>(See manual for varibles that can be used.)</i>.",
            "Default" => "",
        ];
        $config_array["fields"]["acumulus_emailaspdf_confirmreading"] = [
            "FriendlyName" => "E-mail Confirm Reading",
            "Type" => "yesno",
            "Size" => "25",
            "Description" => "Ask the recipient to confirm the delivery of the email message.",
            "Default" => "no",
        ];
    }

    // Warnings & Error Variables.
    $config_array["fields"]["acumulus_warning_message2"] = [
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Warnings & Errors"),
    ];

    $config_array["fields"]["acumulus_warning_email_address"] = [
        "FriendlyName" => "Email address for warnings",
        "Type" => "text",
        "Size" => "25",
        "Description" => "Enter a email address where api warning messages should be send to.<br> When ommitted no warnings will be send.",
        "Default" => "",
    ];

    $config_array["fields"]["acumulus_error_email_address"] = [
        "FriendlyName" => "Email address for errors",
        "Type" => "text",
        "Size" => "25",
        "Description" => "Enter a email address where api error messages should be send to.<br>When ommitted no errors will be send.",
        "Default" => "",
    ];

    // API Variables.
    $config_array["fields"]["acumulus_api_message1"] = [
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("WHMCS API Settings"),
    ];
    $config_array["fields"]["acumulus_whmcs_admin"] = [
        "FriendlyName" => "Hook admin",
        "Type" => "dropdown",
        "Options" => implode(",", acumulus_connect_get_admins()),
        "Description" => "WHMCS Admin user that the acumulus_connect module and runs as.",
        "Default" => "admin",
    ];

    return $config_array;
}

/**
 * Returns a highly visible section header.
 *
 * @param string $section
 *
 * @return string
 */
function acumulus_connect_newConfigSection(string $section): string
{
    $linebreak = str_repeat('&diams;', 100);
    $linebreak .= "<br>&diams;&nbsp;&nbsp;" . $section . "<br>";
    $linebreak .= str_repeat('&diams;', 100);
    return $linebreak;
}

/**
 * Helper function to show the Module form.
 *
 * @param array $vars
 */
function acumulus_connect_show_module_form(array $vars)
{
    $todaysdate = getTodaysDate();
    $lang = $vars['_lang'];

    $gateways = [];
    foreach (acumulus_connect_getWHMCSAccountNumbers($_SESSION['adminid']) as $gateway) {
        $gateways[] = $gateway['module'];
    }

    echo "<form method=\"post\" action=\"addonmodules.php?module=acumulus_connect\">
      <input type=\"hidden\" name=\"action\" value=\"sendinvoice\">
      <p>
        <b>" . $lang['Single invoice "title'] . "</b>
      </p>
        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
        <tbody>
           <tr><td colspan=\"2\"><p>" . $lang['Single invoice detail text'] . "</p></td></tr>
           <tr>
            <td width=\"25%\" class=\"fieldlabel\">" . $lang['Invoice ID'] . ":</td>
            <td class=\"fieldarea\">
             <input type=\"text\" name=\"resentinvoice\" size=\"30\" value=\"\"> <input type=\"submit\" value=\"" . $lang['Sent Invoice'] . "\">
            </td>
           </tr>
           <tr>
            <td width=\"25%\" class=\"fieldlabel\">" . $lang['Search on'] . ":</td>
            <td class=\"fieldarea\">
             <select name=\"search_on\">
                <option value=\"invoiceno\" selected>" . $lang['Invoice No'] . "</option>
                <option value=\"invoiceid\">" . $lang['Invoice ID'] . "</option>
             </select>
            </td>
           </tr>
        </tbody>
        </table>

        <br><br>
        <p>
           <b>" . $lang['Send multiple invoices header'] . "</b>
        </p>
    </form>


    <form method=\"post\" action=\"addonmodules.php?module=acumulus_connect\">
        <input type=\"hidden\" name=\"action\" value=\"sendbatch\">
        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
        <tbody>
           <tr><td colspan=\"2\"><div class=\"infobox\"><strong><span class=\"title\">" . $lang['Batch import'] . "</span></strong><br />" . $lang['Batch import time warning'] . "</div> </td></tr>
           <tr><td colspan=\"2\"><p>" . $lang['Batch import detail text'] . "</p></td></tr>
           <tr>
                <td class=\"fieldlabel\">" . $lang['Filter By'] . "</td>
                <td class=\"fieldarea\"><select name=\"filterby\"><option>Invoice Date</option><option>Date Paid</option><option>Unpaid Invoices</option><option>Paid Invoices by invoicedate</option></select></td>
           </tr>
           <tr>
                <td class=\"fieldlabel\">" . $lang['Payment Method'] . "</td>
                <td class=\"fieldarea\"><select name=\"filterby2\">
                        <option>All Gateways</option><option>" . implode("</option><option>", $gateways) . "</option></td>
           </tr>



           <tr>
                <td class=\"fieldlabel\">" . $lang['Date Range'] . "</td>
                <td class=\"fieldarea\"><input type=\"text\" name=\"datefrom\" value=\"" . $todaysdate . "\" class=\"datepick\" /> &nbsp;&nbsp;&nbsp;&nbsp;" . $lang['to'] . "&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"dateto\" value=\"" . $todaysdate . "\" class=\"datepick\" /></td>
           </tr>
           <tr>
             <td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . $lang['Submit invoices'] . "\"></td>
           </tr>
        </tbody>
        </table>
    </form>
    <br><br>



      <input type=\"hidden\" name=\"action\" value=\"rechecklicense\">
      <p>
        <b>" . $lang['License Information'] . "</b>
      </p>
        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
        <tbody>
            <tr>
                <td>" . $lang['License Information text'] . ' <a href="mailto: whmcs@acumulus.nl"> whmcs@acumulus.nl</a>.</td>
            </tr>
        </tbody>
        </table>
    ';
}

/**
 * Return an html string with a summary of the invoices.
 *
 * @param array $invoices
 * @param array $vars
 *
 * @return string
 */
function acumulus_connect_invoicesummary(array $invoices, array $vars): string
{
    global $_SESSION;
    $lang = $vars['_lang'];

    $totalinvoices = 0;
    $summaryline = '';
    $sendinvoices = [];

    foreach ($invoices as $invoiceid) {

        // https://developers.whmcs.com/api-reference/getinvoice/ (GetInvoice)
        $command = "getinvoice";
        $values["invoiceid"] = $invoiceid;
        $adminuser = $vars["acumulus_whmcs_admin"];
        $data = localAPI($command, $values, $adminuser);
        $client = acumulus_connect_getclient($data["userid"], $adminuser);

        // Check if invoice number exists or use the invoice id instead.
        if ($data["invoicenum"] === "") {
            $invoicenumber = $data['invoiceid'];
        } else {
            $invoicenumber = $data["invoicenum"];
        }

        $data["clientname"] = ($client["companyname"] != "" ? $client["companyname"] . " - " . $client["firstname"] . " " . $client["lastname"] : $client["firstname"] . " " . $client["lastname"]);
        $summaryline .= "<tr>";
        $summaryline .= "<td> <a href=\"invoices.php?action=edit&id=" . $data["invoiceid"] . "\">" . $data["invoiceid"] . "</a></td>";
        $summaryline .= "<td>" . $invoicenumber . "</td>";
        $summaryline .= "<td><a href=\"clientssummary.php?userid=" . $data["userid"] . "\">" . $data["clientname"] . "</a></td>";
        $summaryline .= "<td>" . fromMySQLDate($data["date"]) . "</td>";
        $summaryline .= "<td>" . fromMySQLDate($data["datepaid"]) . "</td>";
        $summaryline .= "<td>" . $data["total"] . "</td>";
        $summaryline .= "<td>" . $data["paymentmethod"] . "</td>";
        if (strtolower($data["status"]) == "paid") {
            $summaryline .= "<td><span class=\"textgreen\">" . $lang['Paid'] . "</span></td>";
        } elseif (strtolower($data["status"]) == "unpaid") {
            $summaryline .= "<td><span class=\"textred\">" . $lang["Unpaid"] . "</span></td>";
        } elseif (strtolower($data["status"]) == "cancelled") {
            $summaryline .= "<td><span>" . $lang["Cancelled"] . "</span></td>";
        } else {
            $summaryline .= "<td><span>" . $data["status"] . "</span></td>";
        }
        $summaryline .= "<tr>";
        ++$totalinvoices;
        $sendinvoices[] = $data["invoiceid"];
        unset($data);
    }//endfor
    unset($invoice);
    $summary = "<p>" . $lang['Sent invoice summery'] . ".</p>";
    $summary .= $totalinvoices . " " . $lang['Records Found'];
    $summary .= '<div class="tablebg">
                <table id="sortabletbl1" class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
                <tr><th>' . $lang['Invoice ID'] . '</th><th>' . $lang['Invoice No'] . '</th><th>' . $lang['Client Name'] . '</th><th>' . $lang['Invoice Date'] . '</th><th>' . $lang['Date Paid'] . '</th><th>Total</th><th>' . $lang['Payment Method'] . '</th><th>' . $lang['Status'] . '</th></tr>';
    $summary .= $summaryline;
    $summary .= "</table></div>";

    $_SESSION['acumulus_connect_sendinvoices'] = $sendinvoices;

    return $summary;
}
