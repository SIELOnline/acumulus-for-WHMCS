<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

//use Database Namespace
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Database\Capsule as Schema;

require_once('acumulus_connect_functions.php');
require_once('assets/gplv3.php');

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------
   Module Mandatory functions
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function acumulus_connect_config()
{
    // Function to return the configuraion array for Acumulus_Connect WHMCS module
    $CONFIG = acumulus_connect_getConfig();

    //Check if any credentials are given or show the basic config.
    if ((!empty($CONFIG["acumulus_code"])) && (!empty($CONFIG["acumulus_username"])) && (!empty($CONFIG["acumulus_password"]))) {
        // Contruct the xml for the Acumulus API check

        $xml = acumulus_connect_basicXML(false); //construct the basic xml without email on errors or warnings.
        $xml->addChild('format', 'xml');
        $xml_string = urlencode($xml->asXML());

        //lets check the credentials against the Acumulus API.
        $url = "https://api.sielsystems.nl/acumulus/stable/general/general_about.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        //lets check if the result with the entered credentials are valid, if so display the full config or show credentails mismatch
        $xml = simplexml_load_string($response);
        if ((string) $xml->general->about) {
            return acumulus_connect_construct_full_configarray();
        } else {
            // the entered credentials are not valid.
            $configarray = acumulus_connect_construct_basic_configarray();
            array_push($configarray["fields"],
                array("FriendlyName" => "Credential check", "Description" => '<font color="red"><b>' . 'The credentials are not correct.' . '</b></font>'));

            return $configarray;
        }
    } else {
        // one or more required credentials are not given.
        $configarray = acumulus_connect_construct_basic_configarray();
        array_push($configarray["fields"], array(
            "FriendlyName" => "Credential check",
            "Description" => '<font color="blue"><b>' . 'Please enter your credentials and click "Save Changes", to continue configurating.' . '</b></font>',
        ));

        return $configarray;
    }
}

function acumulus_connect_activate()
{

    # Create Custom DB Table

    // Create a new table.
    try {
        Schema::schema()->create(
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

        return array('status' => 'error', 'description' => 'Installing the module failed Unable to create my_table: ' . $e->getMessage());
    }

    # Return Result
    return array('status' => 'success', 'description' => 'The module Acumulus Connect is installed Successfully, please fill in the configuration data.');
}

function acumulus_connect_deactivate()
{

    # Remove Custom DB Table
    try {
        Schema::schema()->drop('mod_acumulus_connect');
    } catch (Exception $e) {
        logActivity('Acumulus_Connect: Deactivating the module failed: ' . $e->getMessage());

        return array('status' => 'error', 'description' => 'Deactivating the module failed: ' . $e->getMessage());
    }

    # Return Result
    return array('status' => 'success', 'description' => 'The module Acumulus Connect is deactivated Successfully');
}

function acumulus_connect_upgrade($vars)
{
    $version = $vars['version'];

    # Run SQL Updates for V1.x to V2.0
    if ($version < 2.0) {
        try {
            Schema::schema()->table(
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
    # Run SQL Updates for V2.0 to V2.1
    if ($version < 2.1) {
    }

    # Run SQL Updates for V2.1 to V2.2
    if ($version < 2.2) {
    }

    # Run SQL Updates for V2.2 to V2.3
    if ($version < 2.3) {
    }

    # Run SQL Updates for V2.3 to V2.4
    if ($version < 2.4) {
    }

    # Run SQL Updates for V2.4 to V2.5
    if ($version < 2.5) {
    }

    # Run SQL Updates for V2.5 to V2.6
    if ($version < 2.6) {
    }

    # Run SQL Updates for V2.6 to V2.7
    if ($version < 2.7) {
    }

    # Run SQL Updates for V2.7 to V2.8
    if ($version < 2.8) {
    }

    # Run SQL Updates for V2.8 to V2.9
    if ($version < 2.9) {
    }

    # Run SQL Updates for V2.9 to V3.0
    if ($version < 3.0) {
    }

    # Run SQL Updates for V3.0 to V3.1
    if ($version < 3.1) {
    }

    # Run SQL Updates for V3.1 to V3.2
    if ($version < 3.2) {
    }
}

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------
   Module Additional functions
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function acumulus_connect_sidebar($vars)
{
    $version = $vars['version'];
    $LANG = $vars['_lang'];

    $sidebar = '<span class="header"><img src="images/icons/addonmodules.png" class="absmiddle" width="16" height="16" /> Acumulus Connect</span>
        <ul class="menu">
                <li><a href="#">' . $LANG['Version'] . ': ' . $version . '</a></li>';

    if (isset($_SESSION['acumulus_connect_newversion'])) {
        $sidebar .= '<li><a STYLE="color: #FF0000; font-weight: bold;" href="https://forum.acumulus.nl/index.php/topic,4183.0.html" target="_blank">' . $LANG['update availible'] . '</a></li>';
    }

    $sidebar .= '</ul>';

    return $sidebar;
}

function acumulus_connect_output($vars)
{
    global $_SESSION;
    $LANG = $vars['_lang'];
    if (isset($_POST["action"])) {
        if (empty($_POST['resentinvoice']) and ($_POST['action'] == 'sendinvoice')) {
            echo "<br><h2>" . $LANG['No records found message'] . "</h2>";
            echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $LANG['Return'] . '</a>';

            return;
        }
        switch ($_POST["action"]) {
            case "sendinvoice":
                $invoiceid = mysql_real_escape_string($_POST['resentinvoice']);
                $searchon = mysql_real_escape_string($_POST['search_on']);
                switch ($searchon) {

                    case "invoiceno":
                        try {
                            $invoiceids = Capsule::table('tblinvoices')->select('id')->where('invoicenum', $invoiceid)->get()[0]->id;
                        } catch (Exception $e) {
                            echo "<br><h2>" . $LANG['No records found message'] . "</h2>";
                            echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $LANG['Return'] . '</a>';

                            return;
                        }
                        if (empty($invoiceids)) {
                            echo "<br><h2>" . $LANG['No records found message'] . "</h2>";
                            echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $LANG['Return'] . '</a>';

                            return;
                        }
                        $invoices = array($invoiceids);
                        if (empty($invoiceids)) {
                            $invoices = array($invoiceid);
                        }
                        break;

                    default:
                        $invoices = array($invoiceid);
                }
                if (empty($invoices[0])) {
                    echo "<br><h2>" . $LANG['No records found message'] . "</h2>";
                    echo '<br><a href="addonmodules.php?module=acumulus_connect" class="btn btn-warning">' . $LANG['Return'] . '</a>';

                    return;
                }

                echo acumulus_connect_invoicesummary($invoices, $vars);
                echo '<form method="post" action="addonmodules.php?module=acumulus_connect">
                <input type="hidden" name="action" value="sendinvoicesnow">
                     ' . $LANG['Resent Invoice'] . ' <input type="submit" value="' . $LANG['Submit Invoice'] . '" class="btn-success" name="Submit" onclick="return confirm(\'' . $LANG['Confirm Send single'] . '\')" />
                    <a href="addonmodules.php?module=acumulus_connect" class="btn">Return</a>
                </p>
            </form>';
                break;
            case "sendbatch":
                $invoices = array();
                $filterby = mysql_real_escape_string($_POST['filterby']);
                $filterby2 = mysql_real_escape_string($_POST['filterby2']);
                $datefrom = toMySQLDate(mysql_real_escape_string($_POST['datefrom']));
                $dateto = toMySQLDate(mysql_real_escape_string($_POST['dateto']));

                if ($filterby2 == "All Gateways") {
                    $filterbyStr2 = "*";
                } else {
                    $filterbyStr2 = ' AND tblinvoices.paymentmethod = "' . $filterby2 . '"';
                }

                switch ($_POST['filterby']) {
                    case "Date Paid":
                        if ($filterby2 == "All Gateways") {
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
                        foreach ($results as $result) {
                            array_push($invoices, $result->id);
                        }
                        break;

                    case "Unpaid Invoices":
                        if ($filterby2 == "All Gateways") {
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
                        foreach ($results as $result) {
                            array_push($invoices, $result->id);
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
                        foreach ($results as $result) {
                            array_push($invoices, $result->id);
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
                        foreach ($results as $result) {
                            array_push($invoices, $result->id);
                        }
                }

                echo '<div class="infobox"><strong><span class="title">' . $LANG['Batch import'] . '</span></strong><br />' . $LANG['Batch import time warning'] . '</div>';
                echo acumulus_connect_invoicesummary($invoices, $vars);
                echo '<form method="post" action="addonmodules.php?module=acumulus_connect">
                <input type="hidden" name="action" value="sendinvoicesnow">
                     ' . $LANG['Sent Above Invoices'] . '&nbsp;&nbsp;&nbsp; <input type="submit" value="' . $LANG['Sent Invoices'] . '" class="btn-success" name="Submit" onclick="return confirm(\'' . $LANG['Confirm Send'] . '\')" />
                    <a href="addonmodules.php?module=acumulus_connect" class="btn">' . $LANG['Return'] . '</a>
                </p>
            </form>';
                break;

            case "sendinvoicesnow":
                $invoiceid = $_POST['invoiceid'];
                if (isset($_SESSION['acumulus_connect_sendinvoices'])) {
                    foreach ($_SESSION['acumulus_connect_sendinvoices'] as $invoiceid) {
                        acumulus_connect_sendInvoice($vars, $invoiceid);
                    }
                    echo('<div class="infobox"><strong><span class="title">' . $LANG['Check activity'] . '</span></strong><br />' . $LANG['Check Import Acumulus'] . '</div>');
                    echo('<p>' . str_replace('$$', '<a href="systemactivitylog.php">' . $LANG['Activity Log'] . '</a>', $LANG['Send Result Message']) . '</p>');
                } else {
                    echo('<div class="errorbox"><strong><span class="title">' . $LANG['No records found'] . '</span></strong><br />' . $LANG['No records found message'] . '</div>');
                }

                if (isset($_SESSION['acumulus_connect_sendinvoices'])) {
                    unset($_SESSION['acumulus_connect_sendinvoices']);
                }
                break;
        }
    } else {
        acumulus_connect_show_module_form($vars);
    } //endif
}

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------
   Helper functions
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function acumulus_connect_construct_basic_configarray()
{                // Helper function to create the basic configarray  (used to enter credentials)
    $configarray = array(
        "name" => "Acumulus Connect",
        "description" => "This connect module allows you to send invoices and contacts to Acumulus&reg;.",
        "version" => "3.2",
        "author" => "<a href=\"https://remline.nl\" target=\"_blank\"><img alt=\"Remline ict-diensten\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAAAZCAYAAACxZDnAAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2hpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpBMUE0REEzMUZEMDZFMDExQjM2N0UzQ0NEQjZCOTEyMiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDMEM0Q0I3QTI4NDMxMUU0ODA3RERBRDA0QjNEOEFBQSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMEM0Q0I3OTI4NDMxMUU0ODA3RERBRDA0QjNEOEFBQSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDg4MDExNzQwNzIwNjgxMTgwODNGQ0MyOEEyODFERjAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QTFBNERBMzFGRDA2RTAxMUIzNjdFM0NDREI2QjkxMjIiLz4gPC9yZGY6RGVzY3Jp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\"/></a>",
        "language" => "english",
        "fields" => array(
            "acumulus_code" => array(
                "FriendlyName" => "Contract code",
                "Type" => "text",
                "Size" => "25",
                "Description" => "Enter the Acumulus&reg; contract code here.",
                "Default" => "",
            ),
            "acumulus_username" => array(
                "FriendlyName" => "Username",
                "Type" => "text",
                "Size" => "25",
                "Description" => "Enter the Acumulus&reg; username here.",
                "Default" => "",
            ),
            "acumulus_password" => array(
                "FriendlyName" => "Password",
                "Type" => "password",
                "Size" => "25",
                "Description" => "Enter the Acumulus&reg; password here.",
            ),
        ),
    );

    return $configarray;
}

function acumulus_connect_construct_full_configarray()
{                 // Helper function to create the full configarray (used after credentials are correct)
    $CONFIG = acumulus_connect_getConfig();
    $configarray = acumulus_connect_construct_basic_configarray();

    //get the costcenters from Acumulus and put them in a comma seperate string.
    foreach (acumulus_connect_getCostcenters() as $costcenter) {
        $stringCostcenters .= $costcenter['costcenterid'] . ' ' . str_replace(',', ' ', $costcenter['costcentername']) . ',';
    }
    $stringCostcenters = rtrim($stringCostcenters, ",");   // remove the the comma at the end of the string.

    // get the invoice templates from Acumulus and put then in a comma seperate string.
    $stringTemplates = ',';
    foreach (acumulus_connect_getTemplates() as $template) {
        $stringTemplates .= $template['invoicetemplateid'] . ' ' . str_replace(',', ' ', $template['invoicetemplatename']) . ',';
    }
    $stringTemplates = rtrim($stringTemplates, ",");   // remove the the comma at the end of the string.

    //get the Accountnumbers from Acumulus and put them in a comma seperate string.
    $Acumulus_AccountNumbers = '';
    foreach (acumulus_connect_getAccounts() as $account) {
        $Acumulus_AccountNumbers .= $account['accountid'] . ' ' . str_replace(',', ' ', $account['accountnumber']) . ',';
    }
    $Acumulus_AccountNumbers = rtrim($Acumulus_AccountNumbers, ",");   // remove the the comma at the end of the string.

    array_push($configarray["fields"], array("FriendlyName" => "Credential check", "Description" => 'Credentials are correct.'));

    // *************************************************************************
    // * Features                                                              *
    // *************************************************************************
    $configarray["fields"]["acumulus_features_hook_invoice_message1"] = array(
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Features"),
    );
    $configarray["fields"]["acumulus_emailaspdf"] = array(
        "FriendlyName" => "Let Acumulus&reg send invoice",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled imported invoices will be send as pdf file using Acumulus.<br><i>* When importing invoices with the bulk import, this setting is ignored and no invoices will be send by Acumulus&reg.</i>",
        "Default" => "no",
    );

    $configarray["fields"]["acumulus_customer_import_enabled"] = array(
        "FriendlyName" => "Import customer details",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Import WHMCS customer details in Acumulus&reg when an invoice is send to Acumulus&reg.",
        "Default" => "on",
    );

    // *************************************************************************
    // * Hook Variables                                                        *
    // *************************************************************************

    $configarray["fields"]["acumulus_hook_invoice_create_enabled"] = array(
        "FriendlyName" => "Enable create hook",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Send Invoice to Acumulus&reg; directly when a new invoice has been generated by the cron, order process, API, when converting a quote to an invoice, or when published a draft invoice with email.",
        "Default" => "on",
    );
    $configarray["fields"]["acumulus_hook_invoice_paid_enabled"] = array(
        "FriendlyName" => "Enable paid hook",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Update or Create Invoice in Acumulus&reg; directly when paid for.",
        "Default" => "on",
    );
    $configarray["fields"]["acumulus_hook_invoice_canceled_enabled"] = array(
        "FriendlyName" => "Enable Canceled invoice hook",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Create Credit invoice in Acumulus&reg; when a invoice is being canceled.",
        "Default" => "on",
    );
    // *************************************************************************
    // * Customer Variables                                                    *
    // *************************************************************************

    if ($CONFIG['acumulus_customer_import_enabled'] === "on") {
        $configarray["fields"]["acumulus_customer_message1"] = array(
            "FriendlyName" => "",
            "Description" => acumulus_connect_newConfigSection("Customer Settings"),
        );

        $configarray["fields"]["acumulus_customer_type"] = array(
            "FriendlyName" => "Customer Type",
            "Type" => "dropdown",
            "Options" => "Debtor,Creditor,Debtor/Creditor (neutral)",
            "Description" => "Select under what type the customer needs to be registered in Acumulus&reg.",
            "Default" => "Debtor/Creditor (neutral)",
        );
        $configarray["fields"]["acumulus_customer_countryautoname"] = array(
            "FriendlyName" => "Customer country",
            "Type" => "dropdown",
            "Options" => "Use the same country as the customer in WHMCS,Automatic prefill based on country code,Automatic prefill based on country code including Nederland",
            "Description" => "Select which customer country setting needs to be used in Acumulus&reg.",
            "Default" => "Use the same country as the customer in WHMCS",
        );
        $configarray["fields"]["acumulus_customer_overwriteifexists"] = array(
            "FriendlyName" => "Overwrite customer details",
            "Type" => "yesno",
            "Size" => "25",
            "Description" => "Overwrite customer contact details in Acumulus&reg.",
            "Default" => "on",
        );
        $configarray["fields"]["acumulus_customer_disableduplicates"] = array(
            "FriendlyName" => "Disable customer duplicates",
            "Type" => "yesno",
            "Size" => "25",
            "Description" => "Disable older instances of a contact in Acumulus&reg when multiple contacts match the customer email.",
        );
        $configarray["fields"]["acumulus_whmcs_vatfield"] = array(
            "FriendlyName" => "TAX or VAT field",
            "Type" => "dropdown",
            "Options" => implode(",", acumulus_connect_getClientCustomfields()) . ',[VAT Number]',
            "Description" => "WHMCS Vat field or Custom client field that represents the TAX ID or VAT number. The option [VAT Number] is the new vat field since WHMCS 7.7",
            "Default" => "[VAT Number]",
        );
        $configarray["fields"]["acumulus_whmcs_ibanfield"] = array(
            "FriendlyName" => "IBAN field",
            "Type" => "dropdown",
            "Options" => implode(",", acumulus_connect_getClientCustomfields()),
            "Description" => "Custom client field that represents the clients IBAN number.",
            "Default" => "",
        );
        $configarray["fields"]["acumulus_cusromer_mark"] = array(
            "FriendlyName" => "Client Mark",
            "Type" => "text",
            "Size" => "40",
            "Description" => "Extra label or mark. <i>(See manual for varibles that can be used.)</i>",
            "Default" => "WHMCS Klantnr: {USERID}",
        );
    } //Endif
    // *************************************************************************
    // * Standard Invoice Settings                                             *
    // *************************************************************************
    $configarray["fields"]["acumulus_invoice_message1"] = array(
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Standard Invoice Settings"),
    );

    $configarray["fields"]["acumulus_invoice_default_costcenter"] = array(
        "FriendlyName" => "Default Costcenter",
        "Type" => "dropdown",
        "Options" => $stringCostcenters,
        "Description" => "The default costcenter the new invoices will be booked to.",
        "Default" => "",
    );

    $configarray["fields"]["acumulus_invoice_default_nature"] = array(
        "FriendlyName" => "Default nature",
        "Type" => "dropdown",
        "Options" => 'Product,Service',
        "Description" => "The default Nature on wich an invoice line is booked.",
        "Default" => "Service",
    );

    $configarray["fields"]["acumulus_use_acumulus_invoice_numbering"] = array(
        "FriendlyName" => "Use Acumulus&reg invoice numbering",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled, WHMCS ignors the invoice number and uses Acumulus&reg sequential invoice numbering.<br>",
        "Default" => "on",
    );

    $configarray["fields"]["acumulus_invoice_description"] = array(
        "FriendlyName" => "Invoice title",
        "Type" => "text",
        "Size" => "40",
        "Description" => "Overall description of the invoice, invoice title.<br><i>(See manual for varibles that can be used.)</i>",
        "Default" => "WHMCS Factuur: {INVOICENUMBER}",
    );

    $configarray["fields"]["acumulus_creditinvoice_description"] = array(
        "FriendlyName" => "Credit Invoice title",
        "Type" => "text",
        "Size" => "40",
        "Description" => "Overall description for the Credit invoice, credit invoice title.<br><i>(See manual for varibles that can be used.)</i>",
        "Default" => "Credit Factuur:  WHMCS: {INVOICENUMBER}",
    );

    $configarray["fields"]["acumulus_invoice_descriptiontext"] = array(
        "FriendlyName" => "Invoice extended description",
        "Type" => "textarea",
        "Rows" => "4",
        "Cols" => "47",
        "Description" => "Multiline field for extended description of the invoice. Content will appear on invoice and associated emails.<br><i>(See manual for varibles that can be used.) note: tabs cannot be used here</i>",
        "Default" => "{INVOICENOTES}",
    );

    $configarray["fields"]["acumulus_invoice_invoicenotes"] = array(
        "FriendlyName" => "Invoice additional remarks",
        "Type" => "textarea",
        "Rows" => "4",
        "Cols" => "47",
        "Description" => "Multiline field for additional remarks.  Contents is placed in notes/comments section of the invoice. Content <b>will not appear</b> on the actual invoice or associated emails.<br><i>(See manual for varibles that can be used.) (Use {TAB} for tabs)</i>",
        "Default" => "",
    );

    $configarray["fields"]["acumulus_invoice_template"] = array(
        "FriendlyName" => "Invoice Template",
        "Type" => "dropdown",
        "Options" => $stringTemplates,
        "Description" => "Name of the template tht will be used by Acumulus&reg. When omitted, the first available template in the contract will be selected.",
        "Default" => "",
    );

    // *************************************************************************
    // * Additional Invoice Settings                                           *
    // *************************************************************************
    $configarray["fields"]["acumulus_invoice_message2"] = array(
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Additional Invoice Settings"),
    );

    $configarray["fields"]["acumulus_summarize_invoice"] = array(
        "FriendlyName" => "Summarize invoice lines",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "Combine all invoice lines to one total invoice line. <i>The field \"Invoice line description\" is used as the description on the invoice line.</i>",
        "Default" => "",
    );

    $configarray["fields"]["acumulus_invoice_correction"] = array(
        "FriendlyName" => "Invoice Correction",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled, the module will try to estimate the totals (WHMCS and ACUMULUS) and add a correction line when needed.",
        "Default" => "",
    );
    if ($CONFIG['acumulus_invoice_correction'] == 'on') {
        $configarray["fields"]["acumulus_invoice_correction_text"] = array(
            "FriendlyName" => "Invoice correction line description<br>",
            "Type" => "text",
            "Size" => "40",
            "Description" => "The text that will appear on the invoice if there is a correction needed.<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "WHMCS correctie",
        );
    }
    if ($CONFIG['acumulus_summarize_invoice'] == 'on') {
        $configarray["fields"]["acumulus_summarization_text_taxed"] = array(
            "FriendlyName" => "Invoice line Sumarization description<br>including TAX",
            "Type" => "text",
            "Size" => "40",
            "Description" => "The text that will be used on the summerized invoice line for items with tax.<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "Totaal WHMCS Factuur belast met BTW",
        );

        $configarray["fields"]["acumulus_summarization_text_untaxed"] = array(
            "FriendlyName" => "Invoice line Sumarization description<br>excluding TAX",
            "Type" => "text",
            "Size" => "40",
            "Description" => "The text that will be used on the summerized invoice line for items without tax.<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "Totaal WHMCS Factuur zonder BTW",
        );
    }

    $configarray["fields"]["acumulus_invoice_use_last_paymentmethod"] = array(
        "FriendlyName" => "Use last paymentmethod",
        "Type" => "yesno",
        "Size" => "25",
        "Description" => "When enabled, the module will change the account numbers invoice in Acumulus&reg; to use the last paymentmethod.",
        "Default" => "on",
    );

    // *************************************************************************
    // * Accountnumber translation                                             *
    // *************************************************************************
    $configarray["fields"]["acumulus_accountnumber_message1"] = array(
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Accountnumber translation"),
    );

    foreach (acumulus_connect_getWHMCSAccountNumbers($CONFIG['acumulus_whmcs_admin']) as $accountNumber) {
        // echo $accountNumber['module'];
        $configarray["fields"]["acumulus_AccountNumber_" . $accountNumber['module']] = array(
            "FriendlyName" => "WHMCS Payment Gateway: <b>" . $accountNumber['displayname'] . "</b>",
            "Type" => "dropdown",
            "Options" => $Acumulus_AccountNumbers,
            "Description" => "Select the matching Acumulus&reg AccountNumber.",
            "Default" => "",
        );
    }

    // *************************************************************************
    // * Send email as pdf from Acumulus                                       *
    // *************************************************************************
    if ($CONFIG['acumulus_emailaspdf'] == 'on') {

        $configarray["fields"]["acumulus_emailaspdf_message1"] = array(
            "FriendlyName" => "",
            "Description" => acumulus_connect_newConfigSection("Acumulus E-Mail Settings"),
        );
        $configarray["fields"]["acumulus_emailaspdf_message2"] = array(
            "FriendlyName" => "E-mail To",
            "Description" => "The invoice will be send to the primary customer email adres. WHMCS Aditional Contacts will be ignored.",
        );

        $configarray["fields"]["acumulus_emailaspdf_emailbcc"] = array(
            "FriendlyName" => "E-mail BCC",
            "Type" => "text",
            "Size" => "40",
            "Description" => "Use valid email addresses. Muliple addresses can be used when separated with a comma or semicolon. If emailto is not set, the emailbcc will be ignored and skipped.",
            "Default" => "",
        );
        $configarray["fields"]["acumulus_emailaspdf_emailfrom"] = array(
            "FriendlyName" => "Email From",
            "Type" => "text",
            "Size" => "40",
            "Description" => "Use a single valid emailaddress. If omitted, the email address of the invoice template, with fallback to the account owner will be used. Most pretty results are obtained when using fully configured invoice templates in Acumulus and leaving this option empty (recommended).",
            "Default" => "",
        );
        $configarray["fields"]["acumulus_emailaspdf_subject"] = array(
            "FriendlyName" => "E-mail Subject",
            "Type" => "text",
            "Size" => "40",
            "Description" => "ASCII-only allowed. Be sure to provide xml-escaped htmlentities for UTF-8 characters.<br>If omitted or left empty, the subject will be: Factuur [number] [description]<br><i>(See manual for varibles that can be used.)</i>",
            "Default" => "",
        );
        $configarray["fields"]["acumulus_emailaspdf_message"] = array(
            "FriendlyName" => "E-mail Message",
            "Type" => "textarea",
            "Rows" => "4",
            "Cols" => "47",
            "Description" => "Currently ASCII-only allowed. Mileage may vary when trying to submit multiple lines.<br>If omitted, the email text composed in the template will be used (recommended)<br><i>(See manual for varibles that can be used.)</i>.",
            "Default" => "",
        );
        $configarray["fields"]["acumulus_emailaspdf_confirmreading"] = array(
            "FriendlyName" => "E-mail Confirm Reading",
            "Type" => "yesno",
            "Size" => "25",
            "Description" => "Ask the recipient to confirm the delivery of the email message.",
            "Default" => "no",
        );
    }//end if

    // *************************************************************************
    // * Warnings & Error Variables                                            *
    // *************************************************************************
    $configarray["fields"]["acumulus_warning_message2"] = array(
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("Warnings & Errors"),
    );

    $configarray["fields"]["acumulus_warning_email_address"] = array(
        "FriendlyName" => "Email address for warnings",
        "Type" => "text",
        "Size" => "25",
        "Description" => "Enter a email address where api warning messages should be send to.<br> When ommitted no warnings will be send.",
        "Default" => "",
    );

    $configarray["fields"]["acumulus_error_email_address"] = array(
        "FriendlyName" => "Email address for errors",
        "Type" => "text",
        "Size" => "25",
        "Description" => "Enter a email address where api error messages should be send to.<br>When ommitted no errors will be send.",
        "Default" => "",
    );

    // *************************************************************************
    // * API Variables                                                         *
    // *************************************************************************
    $configarray["fields"]["acumulus_api_message1"] = array(
        "FriendlyName" => "",
        "Description" => acumulus_connect_newConfigSection("WHMCS API Settings"),
    );
    $configarray["fields"]["acumulus_whmcs_admin"] = array(
        "FriendlyName" => "Hook admin",
        "Type" => "dropdown",
        "Options" => implode(",", acumulus_connect_get_admins()),
        "Description" => "WHMCS Admin user that the acumulus_connect module and runs as.",
        "Default" => "admin",
    );

    return $configarray;
}

function acumulus_connect_newConfigSection($section)
{
    $linebreak = '';
    for ($i = 1; $i <= 100; $i++) {
        $linebreak .= "&diams;";
    }
    $linebreak .= "<br>&diams;&nbsp;&nbsp;" . $section . "<br>";
    for ($i = 1; $i <= 100; $i++) {
        $linebreak .= "&diams;";
    }

    return $linebreak;
}

function acumulus_connect_show_module_form($vars)
{                      // Helper function to show the Module form.
    $todaysdate = getTodaysDate();
    $LANG = $vars['_lang'];

    $gateways = array();
    foreach (acumulus_connect_getWHMCSAccountNumbers($_SESSION['adminid']) as $gateway) {
        array_push($gateways, $gateway['module']);
    }

    echo "<form method=\"post\" action=\"addonmodules.php?module=acumulus_connect\">
      <input type=\"hidden\" name=\"action\" value=\"sendinvoice\">
      <p>
        <b>" . $LANG['Single invoice "title'] . "</b>
      </p>
        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
        <tbody>
           <tr><td colspan=\"2\"><p>" . $LANG['Single invoice detail text'] . "</p></td></tr>
           <tr>
            <td width=\"25%\" class=\"fieldlabel\">" . $LANG['Invoice ID'] . ":</td>
            <td class=\"fieldarea\">
             <input type=\"text\" name=\"resentinvoice\" size=\"30\" value=\"\"> <input type=\"submit\" value=\"" . $LANG['Sent Invoice'] . "\">
            </td>
           </tr>
           <tr>
            <td width=\"25%\" class=\"fieldlabel\">" . $LANG['Search on'] . ":</td>
            <td class=\"fieldarea\">
             <select name=\"search_on\">
                <option value=\"invoiceno\" selected>" . $LANG['Invoice No'] . "</option>
                <option value=\"invoiceid\">" . $LANG['Invoice ID'] . "</option>
             </select>
            </td>
           </tr>
        </tbody>
        </table>

        <br><br>
        <p>
           <b>" . $LANG['Send multiple invoices header'] . "</b>
        </p>
    </form>


    <form method=\"post\" action=\"addonmodules.php?module=acumulus_connect\">
        <input type=\"hidden\" name=\"action\" value=\"sendbatch\">
        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
        <tbody>
           <tr><td colspan=\"2\"><div class=\"infobox\"><strong><span class=\"title\">" . $LANG['Batch import'] . "</span></strong><br />" . $LANG['Batch import time warning'] . "</div> </td></tr>
           <tr><td colspan=\"2\"><p>" . $LANG['Batch import detail text'] . "</p></td></tr>
           <tr>
                <td class=\"fieldlabel\">" . $LANG['Filter By'] . "</td>
                <td class=\"fieldarea\"><select name=\"filterby\"><option>Invoice Date</option><option>Date Paid</option><option>Unpaid Invoices</option><option>Paid Invoices by invoicedate</option></select></td>
           </tr>
           <tr>
                <td class=\"fieldlabel\">" . $LANG['Payment Method'] . "</td>
                <td class=\"fieldarea\"><select name=\"filterby2\">
                        <option>All Gateways</option><option>" . implode("</option><option>", $gateways) . "</option></td>
           </tr>



           <tr>
                <td class=\"fieldlabel\">" . $LANG['Date Range'] . "</td>
                <td class=\"fieldarea\"><input type=\"text\" name=\"datefrom\" value=\"" . $todaysdate . "\" class=\"datepick\" /> &nbsp;&nbsp;&nbsp;&nbsp;" . $LANG['to'] . "&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"dateto\" value=\"" . $todaysdate . "\" class=\"datepick\" /></td>
           </tr>
           <tr>
             <td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . $LANG['Submit invoices'] . "\"></td>
           </tr>
        </tbody>
        </table>
    </form>
    <br><br>



      <input type=\"hidden\" name=\"action\" value=\"rechecklicense\">
      <p>
        <b>" . $LANG['License Information'] . "</b>
      </p>
        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
        <tbody>
           <tr>
                <td width=\"100px;\" align=\"center\">
                    <img src=\"data:image/png;base64,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\"/>
                </td>
                <td>" . $LANG['License Information text'] . " <a href=\"https://remline.nl\" target=\"_blank\">Remline ict-diensten</a> (<a href=\"mailto: whmcs@acumulus.nl\"> whmcs@acumulus.nl</a>)</td></tr>
        </tbody>
        </table>
    ";
}

function acumulus_connect_invoicesummary($invoices, $vars)
{
    global $_SESSION;
    $LANG = $vars['_lang'];

    $totalinvoices = 0;
    $summaryline = '';
    $sendinvoices = array();

    foreach ($invoices as $invoiceid) {
        $command = "getinvoice";
        $adminuser = $vars["acumulus_whmcs_admin"];
        $values["invoiceid"] = $invoiceid;
        $data = localAPI($command, $values, $adminuser);

        $command = "getclientsdetails";
        $adminuser = $vars["acumulus_whmcs_admin"];
        $clientid = $data["userid"];
        $client = acumulus_connect_getclient($clientid, $adminuser);

        // check if invoice number exsist or uses the invoice id instead
        if ($data["invoicenum"] == "") {
            $invoicenumber = $data['invoiceid'];
        } else {
            $invoicenumber = $data["invoicenum"];
        }

        $data["clientname"] = ($client["companyname"] != "" ? $client["companyname"] . " - " . $client["firstname"] . " " . $client["lastname"] : $client["firstname"] . " " . $client["lastname"]);
        unset($client, $adminuser, $command);
        $summaryline .= "<tr>";
        $summaryline .= "<td> <a href=\"invoices.php?action=edit&id=" . $data["invoiceid"] . "\">" . $data["invoiceid"] . "</a></td>";
        $summaryline .= "<td>" . $invoicenumber . "</td>";
        $summaryline .= "<td><a href=\"clientssummary.php?userid=" . $data["userid"] . "\">" . $data["clientname"] . "</a></td>";
        $summaryline .= "<td>" . fromMySQLDate($data["date"]) . "</td>";
        $summaryline .= "<td>" . fromMySQLDate($data["datepaid"]) . "</td>";
        $summaryline .= "<td>" . $data["total"] . "</td>";
        $summaryline .= "<td>" . $data["paymentmethod"] . "</td>";
        if (strtolower($data["status"]) == "paid") {
            $summaryline .= "<td><span class=\"textgreen\">" . $LANG['Paid'] . "</span></td>";
        } elseif (strtolower($data["status"]) == "unpaid") {
            $summaryline .= "<td><span class=\"textred\">" . $LANG["Unpaid"] . "</span></td>";
        } elseif (strtolower($data["status"]) == "cancelled") {
            $summaryline .= "<td><span>" . $LANG["Cancelled"] . "</span></td>";
        } else {
            $summaryline .= "<td><span>" . $data["status"] . "</span></td>";
        }
        $summaryline .= "<tr>";
        ++$totalinvoices;
        array_push($sendinvoices, $data["invoiceid"]);
        unset($data);
    }//endfor
    unset($invoice);
    $summary = "<p>" . $LANG['Sent invoice summery'] . ".</p>";
    $summary .= $totalinvoices . " " . $LANG['Records Found'];
    $summary .= '<div class="tablebg">
                <table id="sortabletbl1" class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
                <tr><th>' . $LANG['Invoice ID'] . '</th><th>' . $LANG['Invoice No'] . '</th><th>' . $LANG['Client Name'] . '</th><th>' . $LANG['Invoice Date'] . '</th><th>' . $LANG['Date Paid'] . '</th><th>Total</th><th>' . $LANG['Payment Method'] . '</th><th>' . $LANG['Status'] . '</th></tr>';
    $summary .= $summaryline;
    $summary .= "</table></div>";

    $_SESSION['acumulus_connect_sendinvoices'] = $sendinvoices;

    return $summary;
}

?>
