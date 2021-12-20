<?php
//use Database Namespace
use Illuminate\Database\Capsule\Manager as Capsule;

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------
   Helper functions
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function acumulus_connect_getConfig()
{                                              // Helper function to load the configuration data stored in the Database.
    $result = true;
    try {
        $CONFIG['version'] = Capsule::table('tbladdonmodules')->where('module', 'acumulus_connect')->where('setting', 'version')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_code'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_code')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_username'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_username')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_password'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_password')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_warning_email_address'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_warning_email_address')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_error_email_address'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_error_email_address')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_hook_invoice_create_enabled'] = Capsule::table('tbladdonmodules')
                                                                 ->where('setting', 'acumulus_hook_invoice_create_enabled')
                                                                 ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_hook_invoice_paid_enabled'] = Capsule::table('tbladdonmodules')
                                                               ->where('setting', 'acumulus_hook_invoice_paid_enabled')
                                                               ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_hook_invoice_canceled_enabled'] = Capsule::table('tbladdonmodules')
                                                                   ->where('setting', 'acumulus_hook_invoice_canceled_enabled')
                                                                   ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_customer_import_enabled'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_customer_import_enabled')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_customer_type'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_customer_type')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_customer_countryautoname'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_customer_countryautoname')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_customer_overwriteifexists'] = Capsule::table('tbladdonmodules')
                                                                ->where('setting', 'acumulus_customer_overwriteifexists')
                                                                ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_customer_disableduplicates'] = Capsule::table('tbladdonmodules')
                                                                ->where('setting', 'acumulus_customer_disableduplicates')
                                                                ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_correction'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_correction')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_cusromer_mark'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_cusromer_mark')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_whmcs_vatfield'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_whmcs_vatfield')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_whmcs_ibanfield'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_whmcs_ibanfield')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_whmcs_admin'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_whmcs_admin')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_invoice_default_costcenterid'] = explode(" ",
            Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_default_costcenter')->first()->value, 2)[0];
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_default_costcentername'] = explode(" ",
            Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_default_costcenter')->first()->value, 2)[1];
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_invoice_templateid'] = explode(" ", Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_template')->first()->value,
            2)[0];
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_templatename'] = explode(" ",
            Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_template')->first()->value, 2)[1];
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_use_acumulus_invoice_numbering'] = Capsule::table('tbladdonmodules')
                                                                    ->where('setting', 'acumulus_use_acumulus_invoice_numbering')
                                                                    ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_summarize_invoice'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_summarize_invoice')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_summarization_text_taxed'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_summarization_text_taxed')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_summarization_text_untaxed'] = Capsule::table('tbladdonmodules')
                                                                ->where('setting', 'acumulus_summarization_text_untaxed')
                                                                ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_default_nature'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_default_nature')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_description'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_description')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_creditinvoice_description'] = Capsule::table('tbladdonmodules')
                                                               ->where('setting', 'acumulus_creditinvoice_description')
                                                               ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_correction_text'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_correction_text')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_descriptiontext'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_descriptiontext')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_invoicenotes'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_invoicenotes')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_invoice_use_last_paymentmethod'] = Capsule::table('tbladdonmodules')
                                                                    ->where('setting', 'acumulus_invoice_use_last_paymentmethod')
                                                                    ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    try {
        $CONFIG['acumulus_emailaspdf'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_emailaspdf_emailbcc'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_emailbcc')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_emailaspdf_emailfrom'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_emailfrom')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_emailaspdf_subject'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_subject')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_emailaspdf_message'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_message')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }
    try {
        $CONFIG['acumulus_emailaspdf_confirmreading'] = Capsule::table('tbladdonmodules')
                                                               ->where('setting', 'acumulus_emailaspdf_confirmreading')
                                                               ->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    // loop through all account numbers in WHMCS and put them in a array
    foreach (acumulus_connect_getWHMCSAccountNumbers($CONFIG['acumulus_whmcs_admin']) as $accountNumber) {
        try {
            $CONFIG['account_numbers'][$accountNumber['module']]['id'] = explode(" ",
                Capsule::table('tbladdonmodules')->where('setting', 'acumulus_AccountNumber_' . $accountNumber['module'])->first()->value, 2)[0];
            $CONFIG['account_numbers'][$accountNumber['module']]['name'] = explode(" ",
                Capsule::table('tbladdonmodules')->where('setting', 'acumulus_AccountNumber_' . $accountNumber['module'])->first()->value, 2)[1];
        } catch (Exception $e) {
            $result = false;
        }
    }

    //Global WHMCS Settings
    try {
        $CONFIG['TaxType'] = Capsule::table('tblconfiguration')->where('setting', 'TaxType')->first()->value;
    } catch (Exception $e) {
        $result = false;
    }

    if ($result) {
        return $CONFIG;
    } else {
        return null;
    }
}

function acumulus_connect_get_admins()
{                                             // WHMCS API Call to retrieve administrators.
    $admins = array();

    $table = "tbladmins";
    $fields = "username";
    $sort = "username";
    $sortorder = "ASC";

    $result = select_query($table, $fields, null, $sort, $sortorder);
    while ($data = mysql_fetch_array($result)) {
        array_push($admins, $data['username']);
    }
    //cleanup and return the array with admins
    unset($table, $fields, $sort, $sortorder, $result, $data);

    return $admins;
}

function acumulus_connect_getClientCustomfields()
{                                  // WHMCS API Call to retrieve customfields.
    $clientcustomfields = array();
    foreach (Capsule::table('tblcustomfields')->where('type', 'client')->orderBy('fieldname')->get() as $field) {
        array_push($clientcustomfields, $field->fieldname);
    }

    return $clientcustomfields;
}

function acumulus_connect_getclient($clientid, $adminuser)
{                         // WHMCS API Call to retreive client details.

    $command = 'GetClientsDetails';
    $values = array('clientid' => $clientid, 'responsetype' => 'json',);
    # Call API
    $results = localAPI($command, $values, $adminuser);

    if ($results['result'] != "success") {
        echo "An Error Occurred [WHMCS API Call getclient()]: " . print_r($results[message], true);
    }
    //cleanup and return the array with the client data
    unset($command, $values, $clientid, $adminuser);

    if (isset($results["client"])) {
        $results = $results["client"];
    }

    return $results;
}

function acumulus_connect_getinvoice($invoiceid, $adminuser, $expand = true)
{       // WHMCS API Call to retrieve invoice details.
    $command = 'getinvoice';
    $values = array('invoiceid' => $invoiceid);
    # Call API
    $results = localAPI($command, $values, $adminuser);
    if ($results['result'] != "success") {
        echo "An Error Occurred [WHMCS API Call getinvoice()]: " . print_r($results[message], true);
    }
    //add some custom fields to the invoice
    $client = acumulus_connect_getclient($results['userid'], $adminuser);
    if ($expand) {
        return $newinvoice = acumulus_connect_expandInvoiceWithCustoms($results, $client);
    }

    return $results;
}

function acumulus_connect_getWHMCSAccountNumbers($adminuser)
{                       // WHMCS API Call to retrieve the accountnumbers from WHMCS.
    $command = "getpaymentmethods";

    $results = localAPI($command, $values, $adminuser);
    if (isset($results['result'])) {
        if ($results['result'] == "success") {
            return $results['paymentmethods']["paymentmethod"];
        }
    }
    unset($command, $results);

    return false;
}

function acumulus_connect_getPaymentGatewayUsed($CONFIG, $invoiceid)
{               // WHMCS API Call to retrieve the payment gateway used in the first transaction.
    $command = 'GetTransactions';
    $postData = array(
        'invoiceid' => $invoiceid,
    );
    $results = localAPI($command, $postData, $config['acumulus_whmcs_admin']);
    $gateway = localAPI($command, $postData, $config['acumulus_whmcs_admin'])['transactions']['transaction'][0]['gateway'];

    return $gateway;
}

function acumulus_connect_getWHMCSVersion()
{                                        // helper function to retrieve the current WHMCS version.
    $version = Capsule::table('tblconfiguration')->where('setting', 'Version')->first();

    return $version->value;
}

function acumulus_connect_expandInvoiceWithCustoms($invoice, $client)
{              // helper function to Extend the invoice with custom values for tax etc.

    $CONFIG = acumulus_connect_getConfig();

    // Add some custom tax amounts
    $invoice['custom']['subtotal_taxedItems_exclTax'] = (float) 0;
    $invoice['custom']['subtotal_taxedItems_inclTax'] = (float) 0;
    $invoice['custom']['subtotal_untaxedItems'] = (float) 0;
    $invoice['custom']['total_tax_roundedPerItem'] = (float) 0;
    $invoice['custom']['total_tax'] = (float) 0;

    $convertedVatTax = acumulus_connect_invoiceDetails_getVatType($CONFIG, $invoice, $client);
    $invoice['custom']['vattype'] = $convertedVatTax['vattype'];
    $invoice['custom']['taxrate'] = $convertedVatTax['taxrate'];
    if ($invoice['custom']['taxrate'] == '-1' or $invoice['custom']['taxrate'] == '0') {
        $invoice["taxrate"] = '0';
    }

    if ($CONFIG['TaxType'] == 'Exclusive') {
        $counter = 0;
        foreach ($invoice['items']['item'] as $item) {
            if ($item['taxed'] == 1) {
                $invoice['items']['item'][$counter]['custom_tax_unrounded'] = round((floatval($item['amount']) / 100) * floatval($invoice["taxrate"]),
                    4);  // (amount / 100) * Tax Rate
                $invoice['items']['item'][$counter]['custom_tax_rounded'] = round((floatval($item['amount']) / 100) * floatval($invoice["taxrate"]),
                    2);  // (amount / 100) * Tax Rate
                $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = round(floatval($item['amount'] + ((floatval($item['amount']) / 100) * floatval($invoice["taxrate"]))),
                    4);   // amount + ((amount / 100) * Tax Rate)
                $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = round(floatval($item['amount'] + round((floatval($item['amount']) / 100) * floatval($invoice["taxrate"]),
                        2)), 2); // amount + ((amount / 100) * Tax Rate)
                $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round(floatval($item['amount']), 4);
                $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round(floatval($item['amount']), 2);
                $invoice['custom']['subtotal_taxedItems_exclTax'] += floatval($item['amount']);
                $invoice['custom']['subtotal_taxedItems_inclTax'] += round(floatval($item['amount'] + ((floatval($item['amount']) / 100) * floatval($invoice["taxrate"]))),
                    4);   // amount + ((amount / 100) * Tax Rate)
                $invoice['custom']['total_tax_roundedPerItem'] += round((floatval($item['amount']) / 100) * floatval($invoice["taxrate"]),
                    2);   // amount + ((amount / 100) * Tax Rate)
                $invoice['custom']['total_tax'] += round((floatval($item['amount']) / 100) * floatval($invoice["taxrate"]),
                    4);   // amount + ((amount / 100) * Tax Rate)
            } else {
                $invoice['items']['item'][$counter]['custom_tax_unrounded'] = (float) 0;
                $invoice['items']['item'][$counter]['custom_tax_rounded'] = (float) 0;
                $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = (float) 0;
                $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = (float) 0;
                $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round(floatval($item['amount']), 4);
                $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round(floatval($item['amount']), 2);
                $invoice['custom']['subtotal_untaxedItems'] += floatval($item['amount']);
            }
            $counter += 1;
        }
    } else { // Prices are set inclusive
        $counter = 0;
        foreach ($invoice['items']['item'] as $item) {
            if ($item['taxed'] == 1) {
                $invoice['items']['item'][$counter]['custom_tax_unrounded'] = round((floatval($item['amount']) / (100 + floatval($invoice["taxrate"]))) * floatval($invoice["taxrate"]),
                    4);    // amount / (100 + Tax Rate)
                $invoice['items']['item'][$counter]['custom_tax_rounded'] = round((floatval($item['amount']) / (100 + floatval($invoice["taxrate"]))) * floatval($invoice["taxrate"]),
                    2);   // amount / (100 + Tax Rate)
                $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = round(floatval($item['amount']), 4);
                $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = round(floatval($item['amount']), 2);
                $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round((floatval($item['amount']) / (100 + floatval($invoice["taxrate"]))) * 100,
                    4);  // (amount / (100 + Tax Rate)) * 100
                $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round((floatval($item['amount']) / (100 + floatval($invoice["taxrate"]))) * 100,
                    2);  // (amount / (100 + Tax Rate)) * 100
                $invoice['custom']['subtotal_taxedItems_exclTax'] += round((floatval($item['amount']) / (100 + floatval($invoice["taxrate"]))) * 100,
                    4);  // (amount / (100 + Tax Rate)) * 100 ;
                $invoice['custom']['subtotal_taxedItems_inclTax'] += round(floatval($item['amount']), 4);
                $invoice['custom']['total_tax_roundedPerItem'] += round((floatval($item['amount']) / (100 + floatval($invoice["taxrate"]))) * floatval($invoice["taxrate"]),
                    2);   // amount / (100 + Tax Rate);
                $invoice['custom']['total_tax'] += round((floatval($item['amount']) / (100 + floatval($invoice["taxrate"]))) * floatval($invoice["taxrate"]),
                    4);
            } else {
                $invoice['items']['item'][$counter]['custom_tax_unrounded'] = (float) 0;
                $invoice['items']['item'][$counter]['custom_tax_rounded'] = (float) 0;
                $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = round(floatval($item['amount']), 4);
                $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = round(floatval($item['amount']), 2);
                $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round(floatval($item['amount']), 4);
                $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round(floatval($item['amount']), 2);
                $invoice['custom']['subtotal_untaxedItems'] += floatval($item['amount']);
            }
            $counter += 1;
        }
    }
    $invoice['custom']['subamountTaxRounded'] = round($invoice['custom']['subamountTax'], 2);

    // Calculate rounding corrections.
    if ($CONFIG['acumulus_invoice_correction'] == "on") {
        $invoice = acumulus_connect_estimateTotals($CONFIG, $invoice, $client);
    }

    unset($CONFIG, $counter, $convertedVatTax);

    return $invoice;
}

function acumulus_connect_replaceVarsInText($text, $CONFIG, $invoice, $client)
{     // helper function to replace text with dynamic values

    $replVars = array(
        '{USERID}' => isset($client['userid']) ? $client['userid'] : '',
        '{FIRSTNAME}' => isset($client['firstname']) ? $client['firstname'] : '',
        '{LASTNAME}' => isset($client['lastname']) ? $client['lastname'] : '',
        '{FULLNAME}' => isset($client['fullname']) ? $client['fullname'] : '',
        '{COMPANYNAME}' => isset($client['companyname']) ? $client['companyname'] : '',
        '{ADDRESS1}' => isset($client['address1']) ? $client['address1'] : '',
        '{ADDRESS2}' => isset($client['address2']) ? $client['address2'] : '',
        '{CITY}' => isset($client['city']) ? $client['city'] : '',
        '{STATE}' => isset($client['state']) ? $client['state'] : '',
        '{POSTCODE}' => isset($client['postcode']) ? $client['postcode'] : '',
        '{COUNTRYCODE}' => isset($client['countrycode']) ? $client['countrycode'] : '',
        '{COUNTRY}' => isset($client['countryname']) ? $client['countryname'] : '',
        '{PHONENUMBER}' => isset($client['phonenumber']) ? $client['phonenumber'] : '',
        '{CLIENT_CUSTOMFIELD1}' => isset($client['customfields1']) ? $client['customfields1'] : '',
        '{CLIENT_CUSTOMFIELD2}' => isset($client['customfields2']) ? $client['customfields2'] : '',
        '{CLIENT_CUSTOMFIELD3}' => isset($client['customfields3']) ? $client['customfields3'] : '',
        '{CLIENT_CUSTOMFIELD4}' => isset($client['customfields4']) ? $client['customfields4'] : '',
        '{CLIENT_CURRENCY}' => isset($client['currency_code']) ? $client['currency_code'] : '',
        '{INVOICEID}' => isset($invoice['invoiceid']) ? $invoice['invoiceid'] : '',
        '{INVOICENUMBER}' => !empty($invoice['invoicenum']) ? $invoice['invoicenum'] : $invoice['invoiceid'],
        '{INVOICEDATE}' => isset($invoice['date']) ? $invoice['date'] : '',
        '{INVOICEDUE}' => isset($invoice['duedate']) ? $invoice['duedate'] : '',
        '{INVOICENOTES}' => isset($invoice['notes']) ? $invoice['notes'] : '',
        '{INVOICESTATUS}' => isset($invoice['status']) ? $invoice['status'] : '',
    );

    foreach ($replVars as $key => $value) {
        $text = str_ireplace($key, $value, $text);
    }

    str_replace("\n", "\\n", $invoiceDetails['descriptiontext']);
    unset($replVars, $key, $value);

    return $text;
}

function acumulus_connect_getCostcenters()
{                                         // Helper function to retrieve the costcenters from Acumulus.
    $xml = acumulus_connect_basicXML(false); //construct the basic xml without email on errors or warnings.
    $xml->addChild('format', 'xml');
    $xml_string = urlencode($xml->asXML());

    //lets check the credentials against the Acumulus API.
    $url = "https://api.sielsystems.nl/acumulus/stable/picklists/picklist_costcenters.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    //load the xml result
    $xml = simplexml_load_string($response, null, LIBXML_NOCDATA);
    $rawCostcenters = $xml->costcenters;
    // convert XML to json and then convert to Array
    $jsonCostcenters = json_encode($rawCostcenters);
    $costcenters = json_decode($jsonCostcenters, true);
    // if there are more then 1 costcenters in Acumulus, keep returning the same array construction
    if (isset($costcenters['costcenter'][1])) {
        $costcenters = $costcenters['costcenter'];
    }

    unset($xml, $xml_string, $url, $ch, $response, $rawCostcenters, $jsonCostcenters);

    return $costcenters;
}

function acumulus_connect_getAccounts()
{                                            // Helper function to retrieve the accountnumbers from Acumulus.
    $xml = acumulus_connect_basicXML(false); //construct the basic xml without email on errors or warnings.
    $xml->addChild('format', 'xml');
    $xml_string = urlencode($xml->asXML());

    //lets check the credentials against the Acumulus API.
    $url = "https://api.sielsystems.nl/acumulus/stable/picklists/picklist_accounts.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    //load the xml result
    $xml = simplexml_load_string($response, null, LIBXML_NOCDATA);
    $rawAccounts = $xml->accounts;
    // convert XML to json and then convert to Array
    $jsonAccounts = json_encode($rawAccounts);
    $accounts = json_decode($jsonAccounts, true);
    // if there are more then 1 costcenters in Acumulus, keep returning the same array construction
    if (isset($accounts['account'][1])) {
        $accounts = $accounts['account'];
    }
    unset($xml, $xml_string, $url, $ch, $response, $rawAccounts, $jsonAccountss);

    return $accounts;
}

function acumulus_connect_getTemplates()
{                                           // Helper function to retrieve the invoice templates from Acumulus.
    $xml = acumulus_connect_basicXML(false); //construct the basic xml without email on errors or warnings.
    $xml->addChild('format', 'xml');
    $xml_string = urlencode($xml->asXML());

    //lets check the credentials against the Acumulus API.
    $url = "https://api.sielsystems.nl/acumulus/stable/picklists/picklist_invoicetemplates.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    //load the xml result
    $xml = simplexml_load_string($response, null, LIBXML_NOCDATA);
    $rawTemplates = $xml->invoicetemplates;
    // convert XML to json and then convert to Array
    $jsonTemplates = json_encode($rawTemplates);
    $templates = json_decode($jsonTemplates, true);
    // if there are more then 1 templates in Acumulus, keep returning the same array construction
    if (isset($templates['invoicetemplate'][1])) {
        $templates = $templates['invoicetemplate'];
    }

    unset($xml, $xml_string, $url, $ch, $response, $rawTemplates, $jsonTemplates);

    return $templates;
}

function acumulus_connect_isCountryinEU($countrycode, $date)
{                       // Helper function to check if country is a EU member.
    // $date is for future use like the brexit

    $eu_countries = array(
        'BE',
        'BG',
        'CY',
        'DK',
        'DE',
        'EE',
        'FI',
        'FR',
        'EL',
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
    );
    //convert string to date
    $date = strtotime($date);

    // //Add countries based on date
    // if ($date >= strtotime('2050-01-15')) {
    //     array_push($eu_countries, 'XX');
    // }

    //Remove countries based on date
    if ($date >= strtotime('2021-01-01')) {
        $eu_countries = array_diff($eu_countries, ['UK']);
    }
    if ($date >= strtotime('2021-01-01')) {
        $eu_countries = array_diff($eu_countries, ['GB']);
    }

    $isEU = in_array($countrycode, $eu_countries);

    unset($eu_countries);

    return $isEU;
}

function acumulus_connect_invoiceDetails_getVatType($CONFIG, $invoice, $client)
{       // Helper function to calculate the vattype and taxrate by country, nature MOSS etc.
    /* Vattypes:
       1 	National 	Gewone nationale factuur 	DEFAULT
       2 	National reverse charge 	Verlegde BTW binnen Nederland
       3 	International reverse charge 	BTW-verlegd naar ondernemer in de EU. Een intracommunautaire levering.
       4 	Export outside EU (export) 	Een factuur voor goederen buiten de EU
       5 	Margin scheme 	Marge regeling voor 2e-hands producten
       6 	Foreign VAT 	Buitenlandse BTW voor electronische diensten aan particulieren in de EU. Usage of countrycode mandatory
    */

    if (strtoupper($client['countrycode']) == 'NL') {
        // Invoice is National.
        // ------------------------------
        $vattype = '1';
        $taxrate = $invoice['taxrate'];
    } elseif (acumulus_connect_isCountryinEU(strtoupper($client['countrycode']), $invoice['date'])) {
        // Invoice is EU
        // ------------------------------
        if (strtotime($invoice['date']) < strtotime('2015-01-01')) {
            // factuur van voor 1 jan 2015 (pre MOSS)
            if (empty($client['companyname'])) {
                //particulier
                $vattype = '1';
                $taxrate = $invoice['taxrate'];
            } else {
                //bedrijf
                $vattype = '1';
                $taxrate = '0.00';
            }
        } else {
            // factuur na 1 jan 2015 (MOSS Regeling)
            // echo "<pre>";
            if (empty($client['companyname'])) {
                //particulier
                //whmcs zijn digitale diensten
                $vattype = '6';
                $taxrate = $invoice['taxrate'];
            } else {
                //bedrijf
                $vattype = '3';
                $taxrate = '0.00';
            }
        }
    } else {
        // Invoice is Outside EU (WORLD)
        // ------------------------------
        if (strtolower($CONFIG['acumulus_invoice_default_nature']) == 'service') {
            // The Default nature is a service (digitale diensten)
            if (empty($client['companyname'])) {
                //particulier
                //whmcs zijn digitale diensten
                $vattype = '4';
                $taxrate = '0.00';  // btw angifte moet eventueel worden gedaan in het land van de afnemer
            } else {
                //bedrijf
                $vattype = '1';
                $taxrate = '-1'; //btw vrij
            }
        } else {
            // The Default nature is a product
            $vattype = '4';
            $taxrate = '0.00'; //btw vrij
        }
    }

    return array('vattype' => $vattype, 'taxrate' => $taxrate);
}

function acumulus_connect_sendInvoicetoAccumulus($CONFIG, $invoice, $client, $xml)
{    // Helper function to send the constructed XML to Acumulus with curl.

    $url = "https://api.sielsystems.nl/acumulus/stable/invoices/invoice_add.php";
    $xml_string = urlencode($xml->asXML());
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $rawdata = curl_exec($ch);
    $result = json_decode(json_encode((array) simplexml_load_string($rawdata)), 1);
    logModuleCall("acumulus_connect", "Send Invoice to Acumulus", $xml->asXML(), $rawdata, $result,
        array($CONFIG['acumulus_code'], $CONFIG['acumulus_username'], $CONFIG['acumulus_password']));

    if (isset($result["status"])) {
        switch ($result["status"]) {
            case "1":  //failed
                $errors = print_r($result['error'], true);
                logActivity("acumulus_connect - Error sending Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Errors:" . $errors);
                break;
            case "0":  //success without warnings
                logActivity("acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Sent successfully");
                acumulus_connect_setinvoicetoken($invoice, $result["invoice"]["token"], $result["invoice"]["entryid"], $CONFIG);
                break;
            case "2":  //success with warnings
                $warnings = print_r($result['warning'], true);
                logActivity("acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Sent with " . $result["countwarnings"] . " warnings:" . $warnings);
                acumulus_connect_setinvoicetoken($invoice, $result["invoice"]["token"], $result["invoice"]["entryid"], $CONFIG);
                break;
            default:
                logActivity("acumulus_connect - Unspecified Error sending Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"]);
        }
    } else {
        logActivity("acumulus_connect - Error reaching acumulus website to send Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"]);
    }
    curl_close($ch);

    unset($url, $xml_string, $ch, $rawdata, $result, $result, $warnings);
}

function acumulus_connect_setinvoicetoken($invoice, $invoicetoken, $entryid, $config)
{ // Helper function to update the token table for unpaid invoices.

    //dont save the token to the reference table if paid, no need to store references.
    if ($invoice["status"] == "Paid") {
        logModuleCall("acumulus_connect", "Add invoicetoken to database", 'Invoice is already Paid, no need to store invoicetoken.', '', '', '');

        return;
    }

    //check if invoiceid and invoicetoken are already stored and update if so.
    //if (empty(Capsule::table('mod_acumulus_connect')->select('id', 'token')->where('id', $invoice["invoiceid"])->get())) {
    if (!Capsule::table('mod_acumulus_connect')->where('id', $invoiceid)->exists()) {
        // no token exists, so lets add the token
        try {
            if (Capsule::table('mod_acumulus_connect')->insert(
                array(
                    'id' => $invoice["invoiceid"],
                    'token' => $invoicetoken,
                    'entryid' => $entryid,
                    'created_at' => date("Y-m-d H:i:s"),
                )
            )
            ) {
                logModuleCall("acumulus_connect", "Add invoicetoken to database", '',
                    'Added successfull: ' . $invoice["invoiceid"] . ' - ' . $invoicetoken . ' - ' . $entryid, '', '');
            }
        } catch (Exception $e) {
            logModuleCall("acumulus_connect", "Add invoicetoken to database", '', 'Error in SQL Query: ' . $e->getMessage(), '', '');
        }
    } else {
        //a token already exists, so lets update the token
        try {
            $updatedUserCount = Capsule::table('mod_acumulus_connect')
                                       ->where('id', $invoice["invoiceid"])
                                       ->update(
                                           [
                                               'token' => $invoicetoken,
                                               'entryid' => $entryid,
                                               'updated_at' => date("Y-m-d H:i:s"),
                                           ]
                                       );
            if ($updatedUserCount > 0) {
                logModuleCall("acumulus_connect", "Update invoicetoken in database", '',
                    'Changed successfull: ' . $invoice["invoiceid"] . ' - ' . $invoicetoken, '', '');
            }
        } catch (Exception $e) {
            logModuleCall("acumulus_connect", "Update invoicetoken in database", '', 'Error in SQL Query: ' . $e->getMessage(), '', '');
        }
    }
}

function acumulus_connect_estimateTotals($CONFIG, $invoice, $client)
{                  // Helper function to estimate the totals like Acumulus would calculate.

    $totalwhmcs = 0;
    $totalacumulus = 0;

    foreach ($invoice['items']['item'] as $item) {
        $totalwhmcs += (float) $item ["custom_price_incl_tax_unrounded"];
        $totalacumulus += (float) $item ["custom_price_incl_tax_rounded"];
    }
    $totalwhmcs = round($totalwhmcs, 2);
    //$totalwhmcs = 7.23;
    $totalacumulus = round($totalacumulus, 2);

    $difference = $totalwhmcs - $totalacumulus;

    if ($difference != 0) {
        $corrline = array(
            "id" => "n/a",
            "type" => "",
            "relid" => "0",
            "description" => acumulus_connect_replaceVarsInText($CONFIG['acumulus_invoice_correction_text'], $CONFIG, $invoice, $client),
            "amount" => $difference,
            "taxed" => "0",
            "custom_tax_unrounded" => "0",
            "custom_tax_rounded" => "0",
            "custom_price_incl_tax_unrounded" => "$difference",
            "custom_price_incl_tax_rounded" => "$difference",
            "custom_price_excl_tax_unrounded" => "$difference",
            "custom_price_excl_tax_rounded" => "$difference",
        );

        array_push($invoice['items']['item'], $corrline);
    }

    return $invoice;
}

function acumulus_connect_getPaymentStatus($CONFIG, $invoice, $client, $token)
{        // Helper function to get the current payment status from Acumulus.
    $xml = acumulus_connect_basicXML();
    $xml->addChild('token', $token);
    $url = "https://api.sielsystems.nl/acumulus/stable/invoices/invoice_paymentstatus_get.php";
    $xml_string = urlencode($xml->asXML());
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $rawdata = curl_exec($ch);
    $result = json_decode(json_encode((array) simplexml_load_string($rawdata)), 1);
    logModuleCall("acumulus_connect", "invoice_paymentstatus_get()", $xml->asXML(), $rawdata, $result,
        array($CONFIG['acumulus_code'], $CONFIG['acumulus_username'], $CONFIG['acumulus_password']));
    curl_close($ch);

    return ($result['invoice']);    //   [token] => fe283169378b0f829d3a465824d8bff6 ,  [paymentstatus] => 1 ,  [paymentdate] => 2018-01-20
}

function acumulus_connect_inverseInvoiceAmounts($invoice)
{                             // Helper function to inverse the amounts for teh credit invoice.
    $negativeItems = array();
    //Set invoice amounts  negative.
    foreach ($invoice['items']['item'] as $item) {
        $item['amount'] = number_format(0 - $item['amount'], 2);
        array_push($negativeItems, $item);
    }
    $negativeInvoice = $invoice;
    $negativeInvoice['items']['item'] = $negativeItems;
    $negativeInvoice['subtotal'] = number_format(0 - $negativeInvoice['subtotal'], 2);
    $negativeInvoice['tax'] = number_format(0 - $negativeInvoice['tax'], 2);
    $negativeInvoice['total'] = number_format(0 - $negativeInvoice['total'], 2);
    $negativeInvoice['balance'] = number_format(0 - $negativeInvoice['balance'], 2);

    return ($negativeInvoice);
}

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------
   XML Construct Helper functions
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function acumulus_connect_basicXML($includeWarnings = true)
{                                            // Helper function to construct the basix XML.
    $CONFIG = acumulus_connect_getConfig();
    //Create The XML FILE
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><myxml></myxml>');
    //Contract details
    $contract = $xml->addChild('contract');
    $contract->addChild('contractcode', $CONFIG["acumulus_code"]);
    $contract->addChild('username', $CONFIG["acumulus_username"]);
    $contract->addChild('password', $CONFIG["acumulus_password"]);
    if ($includeWarnings) {
        // Do not include childobject if entry is empty
        if (!empty($CONFIG["acumulus_warning_email_address"])) {
            $contract->addChild('emailonerror', $CONFIG["acumulus_warning_email_address"]);
        }
        // Do not include childobject if entry is empty
        if (!empty($CONFIG["acumulus_error_email_address"])) {
            $contract->addChild('emailonwarning', $CONFIG["acumulus_error_email_address"]);
        }
    }
    //the connector feedback code to help siel improve processing and prioritizing.
    $connector = $xml->addChild('connector');
    $connector->addChild('application', 'WHMCS ' . acumulus_connect_getWHMCSVersion());
    $connector->addChild('webkoppel', 'WHMCS for Acumulus (acumulus_connect) versie ' . $CONFIG["version"]);
    $connector->addChild('development', 'SIEL - Remline');
    $connector->addChild('remark', 'development');
    $connector->addChild('sourceuri', 'https://www.siel.nl');
    //cleanup and return the xml object
    unset($CONFIG, $contract, $connector);

    return $xml;
}

function acumulus_connect_XMLPrepairCustomerDetails($CONFIG, $invoice, $client)
{                        // helper function to prepair the customer data for the XML.
    //convert country code to country name
    include_once('assets/ISO3166.php');
    $ISO3166 = new ISO3166;
    try {
        $country = $ISO3166->getByAlpha2($client['countrycode'])['name'];
    } catch (Exception $e) {
        $country = '';
    }

    // Get the id of the custom field corresponding the VAT title and loop through the clients customfieds for the VAT value.
    try {
        $customFieldId = Capsule::table('tblcustomfields')->where('fieldname', $CONFIG["acumulus_whmcs_vatfield"])->first()->id;
        foreach ($client["customfields"] as $key => $val) {
            if ($val['id'] === $customFieldId) {
                $vatNr = $val['value'];
            }
            if (empty($vatNr)) {
                $vatNr = $client['tax_id'];
            }
        }
    } catch (Exception $e) {

        $vatNr = '';
    }

    // Get the id of the custom field corresponding the IBAN title and loop through the clients customfieds for the IBAN value.
    try {
        $customFieldId = Capsule::table('tblcustomfields')->where('fieldname', $CONFIG["acumulus_whmcs_ibanfield"])->first()->id;
        foreach ($client["customfields"] as $key => $val) {
            if ($val['id'] === $customFieldId) {
                $IBAN = $val['value'];
            }
        }
    } catch (Exception $e) {
        $IBAN = '';
    }

    //If there is no companyname then the fullname will be used as companyname and the contactperson(fullname) will be empty.
    $firstname = (isset($client['firstname'])) ? $client['firstname'] : '';
    $lastname = (isset($client['lastname'])) ? $client['lastname'] : '';
    $fullname = $firstname . ' ' . $lastname;
    //$companyname = (isset($client['companyname'])) ? (!$client['companyname'] == '') ? $client['companyname'] : $firstname . ' ' . $lastname : '';

    //Set how the customer is being imported into Acumulus
    //1 = debtor, 2 = creditor, 3 = debtor/creditor (neutral)
    switch ($CONFIG["acumulus_customer_type"]) {
        case 'Debtor':
            $type = '1';
            break;
        case 'Creditor':
            $type = '2';
            break;
        default:
            //Debtor/Creditor (neutral)
            $type = '3';
    }

    //Set the status of the user to the same status as WHMCS.
    //0 = Not active / disabled, 1 = Active
    switch ($client['status']) {
        case 'Inactive':
            $contactstatus = '0';
            break;
        case 'Closed':
            $contactstatus = '0';
            break;
        default:
            //Active
            $contactstatus = '1';
    }

    // Use Automatic prefill of countryname based on supplied countrycode, Yes with Nederland or Leave the same as in WHMCS?.
    switch ($CONFIG['acumulus_customer_countryautoname']) {
        case 'Automatic prefill based on country code':
            $countryautoname = '1';
            break;
        case 'Automatic prefill based on country code including Nederland':
            $countryautoname = '2';
            break;
        default:
            // Use the same country as the customer in WHMCS
            $countryautoname = '0';
    }

    //$countryautoname = $CONFIG['acumulus_customer_countryautoname'];

    $customerDetails['type'] = $type;
    $customerDetails['contactid'] = '';
    $customerDetails['contactyourid'] = (isset($client['userid'])) ? $client['userid'] : '';
    $customerDetails['contactstatus'] = $contactstatus;                           //0 = Not active/disabled,  1 = active
    if (!empty($companyname)) {
        $customerDetails['companyname1'] = $companyname;
        $customerDetails['companyname2'] = '';
    }
    $customerDetails['fullname'] = $fullname;
    $customerDetails['salutation'] = '';
    $customerDetails['address1'] = (isset($client['address1'])) ? $client['address1'] : '';
    $customerDetails['address2'] = (isset($client['address2'])) ? $client['address2'] : '';
    $customerDetails['postalcode'] = (isset($client['postcode'])) ? preg_replace('/\s+/', '',
        $client['postcode']) : '';   //Remove any whitespaces from teh postcode
    $customerDetails['city'] = ((isset($client['city'])) ? $client['city'] : '') . ((!empty($client['state'])) ? ',  ' . $client['state'] : '');
    $customerDetails['country'] = $country;
    $customerDetails['countrycode'] = (isset($client['countrycode'])) ? $client['countrycode'] : '';
    $customerDetails['countryautoname'] = $countryautoname;                             //Automatic prefill of countryname based on supplied countrycode ?
    $customerDetails['vatnumber'] = $vatNr;
    $customerDetails['telephone'] = (isset($client['phonenumber'])) ? $client['phonenumber'] : '';
    $customerDetails['fax'] = '';
    $customerDetails['email'] = (isset($client['email'])) ? $client['email'] : '';
    $customerDetails['overwriteifexists'] = (isset($CONFIG['acumulus_customer_overwriteifexists'])) ? ($CONFIG['acumulus_customer_overwriteifexists'] === 'on') ? '1' : '0' : '0';  // 0 = No update made, 1 = Overwrite all customer contact details
    $customerDetails['bankaccountnumber'] = $IBAN;
    $customerDetails['mark'] = acumulus_connect_replaceVarsInText($CONFIG['acumulus_cusromer_mark'], $CONFIG, $invoice, $client);
    $customerDetails['disableduplicates'] = (isset($CONFIG['acumulus_customer_disableduplicates'])) ? ($CONFIG['acumulus_customer_disableduplicates'] === 'on') ? '1' : '0' : '0';  // 0 = Leave older duplicate contacts as is, 1 = Mark duplicate contacts as disabled

    //Cleanup and return the array.
    unset($firstname, $lastname, $fullname, $companyname, $CONFIG, $client, $type, $contactstatus, $countryautoname);

    return $customerDetails;
}

function acumulus_connect_XMLPrepairInvoiceDetails($CONFIG, $invoice, $client, $isbulkimport = false, $iscredit = false)
{  // helper function to prepair the invoice data for the XML.
    // if $isbulkimport is set to true then invoice numbering from acumulus is being ignored.

    // *************************************************************************
    // * Simple Variables                                                      *
    // *************************************************************************
    $invoiceDetails['issuedate'] = $invoice['date'];                                     // Format: yyyy-mm-dd.
    $invoiceDetails['costcenter'] = $CONFIG['acumulus_invoice_default_costcentername'];   // When omitted, or when no match has been made possible, the first available costcenter in the contract will be selected.
    $invoiceDetails['paymentstatus'] = ($invoice['status'] == 'Paid' ? '2' : '1');           // 1 = Due (default), 2 = Paid
    // Change the format from  yyyy-mm-dd hh:mm:ss   to  yyyy-mm-dd.  and unset var if eq 0000-00-00.
    $invoiceDetails['paymentdate'] = (explode(' ', $invoice['datepaid'])[0] == '0000-00-00' ? null : explode(' ',
        $invoice['datepaid'])[0]);                           // Format: yyyy-mm-dd.
    $invoiceDetails['template'] = $CONFIG['acumulus_invoice_templateid'];               // When omitted, or when no match has been made possible, the first available template in the contract will be selected.

    // If ["acumulus_use_acumulus_invoice_numbering"] is disabled or bulk import is enabled use the WHMCS invoicenumber
    if ($CONFIG['acumulus_use_acumulus_invoice_numbering'] !== 'on' or $isbulkimport) {
        // check if invoice number exsist or uses the invoice id instead
        if ($invoice['invoicenum'] == '') {
            $invoice['invoicenum'] = $invoice['invoiceid'];
        } else {
            $invoice['invoicenum'] = $invoice['invoicenum'];
        }
        $invoiceDetails['number'] = $invoice['invoicenum'];
    }
    $invoiceDetails['description'] = acumulus_connect_replaceVarsInText($CONFIG['acumulus_invoice_description'], $CONFIG, $invoice,
        $client);      // Overall description of the invoice, invoice title.
    $invoiceDetails['descriptiontext'] = str_replace("\n", "\\n",
        acumulus_connect_replaceVarsInText($CONFIG['acumulus_invoice_descriptiontext'], $CONFIG, $invoice,
            $client));  // Multiline field for extended description of the invoice. Content will appear on invoice and associated emails. Use \n for newlines. Tabs are not supported.
    $invoiceDetails['invoicenotes'] = str_replace("{TAB}", "\\t", str_replace("\n", "\\n",
        acumulus_connect_replaceVarsInText($CONFIG['acumulus_invoice_invoicenotes'], $CONFIG, $invoice,
            $client)));     // Multiline field for additional remarks. Use \n for newlines and \t for tabs. Contents is placed in notes/comments section of the invoice. Content will not appear on the actual invoice or associated emails.

    $invoiceDetails['accountnumber'] = $CONFIG['account_numbers'][$invoice['paymentmethod']]['id'];               // When omitted, or when no match has been made possible, the first available accountnumber in the contract will be selected
    $invoiceDetails['vattype'] = $invoice['custom']['vattype'];

    // *************************************************************************
    // * Credit Invoice adustments                                             *
    // *************************************************************************
    if ($iscredit) {
        $invoiceDetails['description'] = acumulus_connect_replaceVarsInText($CONFIG['acumulus_creditinvoice_description'], $CONFIG, $invoice,
            $client);      // Overall description of the invoice, invoice title.
        $invoiceDetails['paymentstatus'] = 2;
        $invoiceDetails['paymentdate'] = date("Y-m-d");
    }

    // *************************************************************************
    // * Invoice Line Variables                                                *
    // *************************************************************************
    $invoiceDetails['invoicelines'] = array();

    if ($CONFIG["acumulus_summarize_invoice"] == 'on') {
        // Add total taxxed items
        if (!empty($invoice['custom']['subtotal_taxedItems_exclTax'])) {
            array_push($invoiceDetails['invoicelines'], array(
                'itemnumber' => null,
                // non mandatory, If set, this number will precede the product description (product).
                'product' => acumulus_connect_replaceVarsInText($CONFIG['acumulus_summarization_text_taxed'], $CONFIG, $invoice, $client),
                // non mandatory, Product or service description.
                'nature' => $CONFIG['acumulus_invoice_default_nature'],
                // non mandatory,  'Product'( default ), 'Service'
                'unitprice' => $invoice['custom']['subtotal_taxedItems_exclTax'],
                // non mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                'vatrate' => $invoice['custom']['taxrate'],
                // mandatory,  Applicable vatrate for the product. Defaults to "21".
                'quantity' => "1",
                // non mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                'costprice' => null,
                // non mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
            ));
        }
        // Add total untaxxed items
        if (!empty($invoice['custom']['subtotal_untaxedItems'])) {
            array_push($invoiceDetails['invoicelines'], array(
                'itemnumber' => null,
                // non mandatory, If set, this number will precede the product description (product).
                'product' => acumulus_connect_replaceVarsInText($CONFIG['acumulus_summarization_text_untaxed'], $CONFIG, $invoice, $client),
                // non mandatory, Product or service description.
                'nature' => $CONFIG['acumulus_invoice_default_nature'],
                // non mandatory,  'Product'( default ), 'Service'
                'unitprice' => $invoice['custom']['subtotal_untaxedItems'],
                // non mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                'vatrate' => "-1",
                // mandatory,  Applicable vatrate for the product. Defaults to "21".
                'quantity' => "1",
                // non mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                'costprice' => null,
                // non mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
            ));
        }
    } else {
        foreach ($invoice['items']['item'] as $item) {
            array_push($invoiceDetails['invoicelines'], array(
                'itemnumber' => null,
                // non mandatory, If set, this number will precede the product description (product).
                // #2
                'product' => preg_replace("/\r\n|\r|\n/", ' ', $item['description']),
                // non mandatory, Product or service description.
                'nature' => $CONFIG['acumulus_invoice_default_nature'],
                // non mandatory,  'Product'( default ), 'Service'
                'unitprice' => $item['custom_price_excl_tax_unrounded'],
                // non mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                'vatrate' => ($item['taxed'] == 1) ? $invoice['custom']['taxrate'] : '-1',
                // mandatory,  Applicable vatrate for the product. Defaults to "21".
                'quantity' => "1",
                // non mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                'costprice' => null,
                // non mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
            ));
        } //end foreach
    }//end else

    return $invoiceDetails;
}

function acumulus_connect_generatexml($CONFIG, $invoice, $client, $isbulkimport, $iscredit = false)
{                       // Helper function to construct the XML that will be send to Acumulus

    // *************************************************************************
    // * Create the basic XML                                                  *
    // *************************************************************************
    $customerDetails = acumulus_connect_XMLPrepairCustomerDetails($CONFIG, $invoice, $client);
    $invoiceDetails = acumulus_connect_XMLPrepairInvoiceDetails($CONFIG, $invoice, $client, $isbulkimport, $iscredit);

    //Create The XML FILE
    $xml = acumulus_connect_basicXML();

    // *************************************************************************
    // * Add customer details to the XML                                       *
    // *************************************************************************
    $customer = $xml->addChild('customer');

    //Send non mandatory customer information when enabled in moduleconfig.
    if ($CONFIG['acumulus_customer_import_enabled'] === "on") {
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
        // only send mandatory info
        if (!empty($customerDetails['countrycode'])) {
            $customer->addChild('countrycode', $customerDetails['countrycode']);
        }
        if (!empty($customerDetails['vatnumber'])) {
            $customer->addChild('vatnumber', $customerDetails['vatnumber']);
        }
    }
    // *************************************************************************
    // * Add Invoice details to the XML                                        *
    // *************************************************************************

    $xlmInvoice = $customer->addChild('invoice');
    if (!empty($invoiceDetails['number'])) {
        $xlmInvoice->addChild('number', $invoiceDetails['number']);
    }
    if (!empty($invoiceDetails['vattype'])) {
        $xlmInvoice->addChild('vattype', $invoiceDetails['vattype']);
    }
    if (!empty($invoiceDetails['issuedate'])) {
        $xlmInvoice->addChild('issuedate', $invoiceDetails['issuedate']);
    }
    if (!empty($invoiceDetails['costcenter'])) {
        $xlmInvoice->addChild('costcenter', $invoiceDetails['costcenter']);
    }
    if (!empty($invoiceDetails['accountnumber'])) {
        $xlmInvoice->addChild('accountnumber', $invoiceDetails['accountnumber']);
    }
    if (!empty($invoiceDetails['paymentdate'])) {
        $xlmInvoice->addChild('paymentdate', $invoiceDetails['paymentdate']);
    }
    if (!empty($invoiceDetails['paymentstatus'])) {
        $xlmInvoice->addChild('paymentstatus', $invoiceDetails['paymentstatus']);
    }
    if (!empty($invoiceDetails['description'])) {
        $xlmInvoice->addChild('description', $invoiceDetails['description']);
    }
    if (!empty($invoiceDetails['descriptiontext'])) {
        $xlmInvoice->addChild('descriptiontext', $invoiceDetails['descriptiontext']);
    }
    if (!empty($invoiceDetails['template'])) {
        $xlmInvoice->addChild('template', $invoiceDetails['template']);
    }
    if (!empty($invoiceDetails['invoicenotes'])) {
        $xlmInvoice->addChild('invoicenotes', $invoiceDetails['invoicenotes']);
    }
    // *************************************************************************
    // * Add Invoice lines to the XML                                          *
    // *************************************************************************

    if (!empty($invoiceDetails['invoicelines'])) {
        foreach ($invoiceDetails['invoicelines'] as $invoiceLine) {
            //correct unitprice when ommitted
            if (empty($invoiceLine['unitprice'])) {
                $invoiceLine['unitprice'] = '0.000';
            }

            $xlmInvoiceLine = $xlmInvoice->addChild('line');
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
    } // end if.

    // *************************************************************************
    // * Let Acumulus Send invoice to client                                   *
    // *************************************************************************

    if ($CONFIG['acumulus_emailaspdf'] == 'on') {
        $xlmInvoicePdfData = $xlmInvoice->addChild('emailaspdf');    // Imported invoices can be send as pdf file using email by Acumulus.
        if (!empty($customerDetails['email'])) {
            $xlmInvoicePdfData->addChild('emailto', $customerDetails['email']);
        }
        if (!empty($CONFIG['acumulus_emailaspdf_emailbcc'])) {
            $xlmInvoicePdfData->addChild('emailbcc', $CONFIG['acumulus_emailaspdf_emailbcc']);
        }
        if (!empty($CONFIG['acumulus_emailaspdf_emailfrom'])) {
            $xlmInvoicePdfData->addChild('emailfrom', $CONFIG['acumulus_emailaspdf_emailfrom']);
        }
        if (!empty($CONFIG['acumulus_emailaspdf_subject'])) {
            $xlmInvoicePdfData->addChild('subject', acumulus_connect_replaceVarsInText($CONFIG['acumulus_emailaspdf_subject'], $CONFIG, $invoice, $client));
        }
        if (!empty($CONFIG['acumulus_emailaspdf_message'])) {
            $xlmInvoicePdfData->addChild('message',
                str_replace("\n", "\\n", acumulus_connect_replaceVarsInText($CONFIG['acumulus_emailaspdf_message'], $CONFIG, $invoice, $client)));
        }

        if ($CONFIG['acumulus_emailaspdf_confirmreading'] == 'on') {
            $xlmInvoicePdfData->addChild('confirmreading', '1');  // 1 = Ask for confirmation
        } else {
            $xlmInvoicePdfData->addChild('confirmreading', '0');  // 0 = Do not ask for confirmation
        }
    }

    //Cleanup and return the XML object.
    unset($CONFIG, $invoice, $client, $customerDetails, $customer, $customerDetails);

    return $xml;
}

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------
   Functions called by Hooks
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function acumulus_connect_sendInvoice($CONFIG, $invoiceid, $isbulkimport = false)
{
    $adminuser = $CONFIG['acumulus_whmcs_admin'];

    //Run whmcsapi to retrieve the invoice and customer.
    $invoice = acumulus_connect_getinvoice($invoiceid, $adminuser);
    $client = acumulus_connect_getclient($invoice['userid'], $adminuser);

    //Make the xml file
    $xml = acumulus_connect_generatexml($CONFIG, $invoice, $client, $isbulkimport);

    //Send xml to Acumulus
    acumulus_connect_sendInvoicetoAccumulus($CONFIG, $invoice, $client, $xml);
}

function acumulus_connect_updateInvoice($CONFIG, $invoiceid, $usedate = null)
{

    $invoice = acumulus_connect_getinvoice($invoiceid, $CONFIG["acumulus_whmcs_admin"]);
    $client = acumulus_connect_getclient($invoice['userid'], $CONFIG["acumulus_whmcs_admin"]);

    // Retrieve the token from the mod_acumulus_connect table
    try {
        $token = Capsule::table('mod_acumulus_connect')->where('id', $invoice["invoiceid"])->value('token');
    } catch (Exception $e) {
        logModuleCall("acumulus_connect", "Hook[InvoicePaid][acumulus_connect_updateInvoice]", '', 'Error in SQL Query: ' . $e->getMessage(), '', '');
    }
    // if the token exists update the invoice in Acumulus, else send entire invoice.
    if (!empty($token)) {

        // Update paymentgateway if use last paymentmethode is enabled and if it differs from the invoice set payment methode.
        if ($CONFIG['acumulus_invoice_use_last_paymentmethod'] == "on") {
            if (!empty($invoice["transactions"]["transaction"])) {
                $lastpaymentgateway = end($invoice["transactions"]["transaction"])["gateway"];
            } else {
                $lastpaymentgateway = null;
            }
            if ($invoice['paymentmethod'] !== $lastpaymentgateway) {
                acumulus_connect_updateInvoicePaymentMethode($CONFIG, $invoiceid, $lastpaymentgateway);
                //update the invoice in whmcs to the paid methode.
                $command = 'UpdateInvoice';
                $postData = array(
                    'invoiceid' => $invoiceid,
                    'paymentmethod' => $lastpaymentgateway,
                );
                $results = localAPI($command, $postData);
                logModuleCall("acumulus_connect", "[acumulus_connect_updateInvoice in whmcs]", 'API CALL:UpdateInvoice: ' . $postData, $result, '', '');
            }
        }

        // Update invoice to paid.
        $xml = acumulus_connect_basicXML();
        $xml->addChild('token', $token);
        $xml->addChild('paymentstatus', '2');
        if (empty($usedate)) {
            $xml->addChild('paymentdate', substr($invoice['datepaid'], 0, 10));
        } else {
            $xml->addChild('paymentdate', $usedate);
        }

        $url = "https://api.sielsystems.nl/acumulus/stable/invoices/invoice_paymentstatus_set.php";
        $xml_string = urlencode($xml->asXML());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $rawdata = curl_exec($ch);
        $result = json_decode(json_encode((array) simplexml_load_string($rawdata)), 1);
        logModuleCall("acumulus_connect", "Update Invoice paid status in Acumulus", $xml->asXML(), $rawdata, $result,
            array($CONFIG['acumulus_code'], $CONFIG['acumulus_username'], $CONFIG['acumulus_password']));

        if (isset($result["status"])) {
            $command = "logactivity";
            $adminuser = $CONFIG['acumulus_whmcs_admin'];
            $values["userid"] = $client["userid"];

            switch ($result["status"]) {
                case "1":  //failed
                    $errors = print_r($result['error'], true);
                    $values["description"] = "acumulus_connect - Error updateing Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Errors:" . $errors;
                    break;
                case "0":  //success without warnings
                    $values["description"] = "acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Updated successfully";
                    //acumulus_connect_delete_invoicetoken($invoiceid,$CONFIG);
                    break;
                case "2":  //success with warnings
                    $warnings = print_r($result['warning'], true);
                    $values["description"] = "acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Updated with " . $result["countwarnings"] . " warnings:" . $warnings;
                    //acumulus_connect_delete_invoicetoken($invoiceid,$CONFIG);
                    break;
                default:
                    $values["description"] = "acumulus_connect - Unspecified Error Updating Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"];
            }
            $results = localAPI($command, $values, $adminuser);
        } else {
            $command = "logactivity";
            $adminuser = $config['acumulus_whmcs_admin'];
            $values["description"] = "acumulus_connect - API Error reaching acumulus website to update Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"];
            $results = localAPI($command, $values, $adminuser);
        }
        curl_close($ch);
    } else {
        // Token not found make a module log entry and sent entire invoice;
        logModuleCall("acumulus_connect", "Hook[InvoicePaid][acumulus_connect_updateInvoice]", '',
            'Token for id : "' . $invoice["invoiceid"] . '" not found so sending entire invoice.', '', '');
        acumulus_connect_sendInvoice($CONFIG, $invoiceid);
    }
    unset($CONFIG, $invoiceid, $invoice, $client, $token, $e, $xml, $result, $command, $adminuser, $values, $warnings, $results, $ch);

    return;
}

function acumulus_connect_updateInvoicePaymentMethode($CONFIG, $invoiceid, $paymentmethod)
{
    $entryid = Capsule::table('mod_acumulus_connect')->where('id', $invoiceid)->value('entryid');

    if (!empty($entryid)) {
        $accountnumber = $CONFIG['account_numbers'][$paymentmethod]['id'];
        //Update the entry account number
        $xml = acumulus_connect_basicXML();
        $xml->addChild('entryid', $entryid);
        $xml->addChild('accountnumber', $accountnumber);

        $url = "https://api.sielsystems.nl/acumulus/stable/entry/entry_update.php";
        $xml_string = urlencode($xml->asXML());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlstring=$xml_string");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $rawdata = curl_exec($ch);
        $result = json_decode(json_encode((array) simplexml_load_string($rawdata)), 1);
        logModuleCall("acumulus_connect", "Update accountnumber in invoice", $xml->asXML(), $rawdata, $result,
            array($CONFIG['acumulus_code'], $CONFIG['acumulus_username'], $CONFIG['acumulus_password']));

        if (isset($result['entry']['entryproc'])) {
            $command = "logactivity";
            $adminuser = $CONFIG['acumulus_whmcs_admin'];

            switch ($result['entry']['entryproc']) {
                case "updated":  //failed
                    $values["description"] = "acumulus_connect - Changing payment methode for Invoice ID: " . $invoiceid . " to " . $CONFIG['account_numbers'][$paymentmethod]['name'] . "(" . $paymentmethod . ")";
                    break;
                case "error":  //success without warnings
                    $values["description"] = "acumulus_connect - Error changing payment methode for Invoice ID: " . $invoiceid . ". Not able to propagate the update.";
                    break;
                default:
                    $values["description"] = "acumulus_connect - Error changing payment methode for Invoice ID: " . $invoiceid . ". Undocumented error, payment methode probably not updated in Acumulus.";
            }
            $results = localAPI($command, $values, $adminuser);
        } else {
            $command = "logactivity";
            $adminuser = $config['acumulus_whmcs_admin'];
            $values["description"] = "acumulus_connect - API Error reaching acumulus website to update paymentmethode ID: " . $invoiceid;
            $results = localAPI($command, $values, $adminuser);
        }
        curl_close($ch);
    }
}

function acumulus_connect_InvoiceCanceled($CONFIG, $invoiceid)
{
    $adminuser = $CONFIG['acumulus_whmcs_admin'];

    logModuleCall("acumulus_connect", "Hook[InvoiceCancelled][DEBUG[CONFIG]]", $CONFIG, '', '', array());

    // check of  acumulus_use_acumulus_invoice_numbering is used send a invoice with negative amounts (credit invoice).
    if ($CONFIG['acumulus_use_acumulus_invoice_numbering'] == 'on') {

        //Run whmcsapi to retrieve the invoice and customer.
        $invoice = acumulus_connect_getinvoice($invoiceid, $adminuser, false);
        $client = acumulus_connect_getclient($invoice['userid'], $adminuser);

        // check if token exist otherwise just create a activity record and do nothing else.
        // Retrieve the token from the mod_acumulus_connect table
        try {
            $token = Capsule::table('mod_acumulus_connect')->where('id', $invoice["invoiceid"])->value('token');
        } catch (Exception $e) {
            logModuleCall("acumulus_connect", "Hook[InvoiceCancelled][getToken]", '', 'Error in SQL Query: ' . $e->getMessage(), '', '');
        }
        // if the token exists update the invoice in Acumulus, else send entire invoice.
        if (!empty($token)) {
            //set invoice to paid if its unpaid (get status from acumulus api)
            $paymentStatus = acumulus_connect_getPaymentStatus($CONFIG, $invoice, $client, $token);   // [paymentstatus] => 1 ,  [paymentdate] => 2018-01-20
            if ($paymentStatus == '0') {
                $paymentDate = date("Y-m-d");
                acumulus_connect_updateInvoice($CONFIG, $invoiceid, $paymentDate);
            } else {
                $paymentDate = $paymentStatus[paymentdate];
            }

            // Inverse the amounts in teh nvoice
            $negativeInvoice = acumulus_connect_inverseInvoiceAmounts($invoice);
            $negativeInvoice = acumulus_connect_expandInvoiceWithCustoms($negativeInvoice, $client);

            //Make the xml file
            $xml = acumulus_connect_generatexml($CONFIG, $negativeInvoice, $client, false, true);

            //Send new credit invoice (xml) to Acumulus
            acumulus_connect_sendInvoicetoAccumulus($CONFIG, $negativeInvoice, $client, $xml);
        } else {
            $command = "logactivity";
            $adminuser = $config['acumulus_whmcs_admin'];
            $values["description"] = "acumulus_connect - Credit invoice not created no token found for " . $invoice["invoiceid"] . " for User ID: " . $client["userid"];
            $results = localAPI($command, $values, $adminuser);
        }
    } else {
        $command = "logactivity";
        $adminuser = $config['acumulus_whmcs_admin'];
        $values["description"] = "acumulus_connect - Credit invoice not created not using acumulus sequential invoice numbering. ( " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " )";
        $results = localAPI($command, $values, $adminuser);
    }
}
