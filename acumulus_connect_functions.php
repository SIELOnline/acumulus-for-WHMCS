<?php
/**
 * @noinspection DuplicatedCode  @todo: remove at a later stage and extract
 *   those duplicates into helper functions.
 */

use WHMCS\Database\Capsule;

/**
 * Helper function to load the configuration data stored in the Database.
 *
 * @return array|null
 */
function acumulus_connect_getConfig(): ?array
{
    try {
        $config = [];
        $config['version'] = Capsule::table('tbladdonmodules')->where('module', 'acumulus_connect')->where('setting', 'version')->first()->value;
        $config['acumulus_code'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_code')->first()->value;
        $config['acumulus_username'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_username')->first()->value;
        $config['acumulus_password'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_password')->first()->value;
        $config['acumulus_warning_email_address'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_warning_email_address')->first()->value;
        $config['acumulus_error_email_address'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_error_email_address')->first()->value;
        $config['acumulus_hook_invoice_create_enabled'] = Capsule::table('tbladdonmodules')
                                                                 ->where('setting', 'acumulus_hook_invoice_create_enabled')
                                                                 ->first()->value;
        $config['acumulus_hook_invoice_paid_enabled'] = Capsule::table('tbladdonmodules')
                                                               ->where('setting', 'acumulus_hook_invoice_paid_enabled')
                                                               ->first()->value;
        $config['acumulus_hook_invoice_canceled_enabled'] = Capsule::table('tbladdonmodules')
                                                                   ->where('setting', 'acumulus_hook_invoice_canceled_enabled')
                                                                   ->first()->value;
        $config['acumulus_customer_import_enabled'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_customer_import_enabled')->first()->value;
        $config['acumulus_customer_type'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_customer_type')->first()->value;
        $config['acumulus_customer_countryautoname'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_customer_countryautoname')->first()->value;
        $config['acumulus_customer_overwriteifexists'] = Capsule::table('tbladdonmodules')
                                                                ->where('setting', 'acumulus_customer_overwriteifexists')
                                                                ->first()->value;
        $config['acumulus_customer_disableduplicates'] = Capsule::table('tbladdonmodules')
                                                                ->where('setting', 'acumulus_customer_disableduplicates')
                                                                ->first()->value;
        $config['acumulus_invoice_correction'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_correction')->first()->value;
        $config['acumulus_cusromer_mark'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_cusromer_mark')->first()->value;
        $config['acumulus_whmcs_vatfield'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_whmcs_vatfield')->first()->value;
        $config['acumulus_whmcs_ibanfield'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_whmcs_ibanfield')->first()->value;
        $config['acumulus_whmcs_admin'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_whmcs_admin')->first()->value;
        $config['acumulus_invoice_default_costcenterid'] = explode(" ",
            Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_default_costcenter')->first()->value, 2)[0];
        $config['acumulus_invoice_default_costcentername'] = explode(" ",
            Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_default_costcenter')->first()->value, 2)[1];
        $config['acumulus_invoice_templateid'] = explode(" ", Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_template')->first()->value,
            2)[0];
        $config['acumulus_invoice_templatename'] = explode(" ",
            Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_template')->first()->value, 2)[1];
        $config['acumulus_use_acumulus_invoice_numbering'] = Capsule::table('tbladdonmodules')
                                                                    ->where('setting', 'acumulus_use_acumulus_invoice_numbering')
                                                                    ->first()->value;
        $config['acumulus_summarize_invoice'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_summarize_invoice')->first()->value;
        $config['acumulus_summarization_text_taxed'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_summarization_text_taxed')->first()->value;
        $config['acumulus_summarization_text_untaxed'] = Capsule::table('tbladdonmodules')
                                                                ->where('setting', 'acumulus_summarization_text_untaxed')
                                                                ->first()->value;
        $config['acumulus_invoice_default_nature'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_default_nature')->first()->value;
        $config['acumulus_invoice_description'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_description')->first()->value;
        $config['acumulus_creditinvoice_description'] = Capsule::table('tbladdonmodules')
                                                               ->where('setting', 'acumulus_creditinvoice_description')
                                                               ->first()->value;
        $config['acumulus_invoice_correction_text'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_correction_text')->first()->value;
        $config['acumulus_invoice_descriptiontext'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_descriptiontext')->first()->value;
        $config['acumulus_invoice_invoicenotes'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_invoice_invoicenotes')->first()->value;
        $config['acumulus_invoice_use_last_paymentmethod'] = Capsule::table('tbladdonmodules')
                                                                    ->where('setting', 'acumulus_invoice_use_last_paymentmethod')
                                                                    ->first()->value;
        $config['acumulus_emailaspdf'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf')->first()->value;
        $config['acumulus_emailaspdf_emailbcc'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_emailbcc')->first()->value;
        $config['acumulus_emailaspdf_emailfrom'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_emailfrom')->first()->value;
        $config['acumulus_emailaspdf_subject'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_subject')->first()->value;
        $config['acumulus_emailaspdf_message'] = Capsule::table('tbladdonmodules')->where('setting', 'acumulus_emailaspdf_message')->first()->value;
        $config['acumulus_emailaspdf_confirmreading'] = Capsule::table('tbladdonmodules')
                                                               ->where('setting', 'acumulus_emailaspdf_confirmreading')
                                                               ->first()->value;
        // Loop through all account numbers in WHMCS and put them in an array.
        foreach (acumulus_connect_getWHMCSAccountNumbers($config['acumulus_whmcs_admin']) as $accountNumber) {
            $config['account_numbers'][$accountNumber['module']]['id'] = explode(" ",
                Capsule::table('tbladdonmodules')->where('setting', 'acumulus_AccountNumber_' . $accountNumber['module'])->first()->value, 2)[0];
            $config['account_numbers'][$accountNumber['module']]['name'] = explode(" ",
                Capsule::table('tbladdonmodules')->where('setting', 'acumulus_AccountNumber_' . $accountNumber['module'])->first()->value, 2)[1];
        }

        // Global WHMCS Settings.
        $config['TaxType'] = Capsule::table('tblconfiguration')->where('setting', 'TaxType')->first()->value;
    } catch (Exception $e) {
        // @todo: log exception.
        $config = null;
    }

    return $config;
}

/**
 * WHMCS API Call to retrieve administrators.
 *
 * @return array
 */
function acumulus_connect_get_admins(): array
{
    $results = [];
    foreach(Capsule::table("tbladmins")->orderBy("username")->get("username") as $record) {
        $results[] = $record->username;
    }
    return $results;
}

/**
 * WHMCS API Call to retrieve custom fields.
 *
 * @return array
 */
function acumulus_connect_getClientCustomfields(): array
{
    $results = [];
    foreach (Capsule::table('tblcustomfields')->where('type', 'client')->orderBy('fieldname')->get('fieldname') as $record) {
        $results[] = $record->fieldname;
    }
    return $results;
}

/**
 * WHMCS API Call to retrieve client details.
 *
 * @param int $clientid
 * @param string $adminuser
 *
 * @return array
 */
function acumulus_connect_getclient(int $clientid, string $adminuser): array
{
    $command = 'GetClientsDetails';
    $values = ['clientid' => $clientid, 'responsetype' => 'json',];
    $results = localAPI($command, $values, $adminuser);

    if ($results['result'] != "success") {
        echo "An Error Occurred [WHMCS API Call $command()]: " . print_r($results['message'], true);
    }

    if (isset($results["client"])) {
        $results = $results["client"];
    }

    return $results;
}

/**
 * WHMCS API Call to retrieve invoice details.
 *
 * @param int $invoiceid
 * @param string $adminuser
 * @param bool $expand
 *
 * @return array
 */
function acumulus_connect_getinvoice(int $invoiceid, string $adminuser, bool $expand = true): array
{
    $command = 'getinvoice';
    $values = ['invoiceid' => $invoiceid];
    $results = localAPI($command, $values, $adminuser);
    if ($results['result'] != "success") {
        echo "An Error Occurred [WHMCS API Call $command()]: " . print_r($results['message'], true);
    }
    // Add some custom fields to the invoice.
    $client = acumulus_connect_getclient($results['userid'], $adminuser);
    if ($expand) {
        $results = acumulus_connect_expandInvoiceWithCustoms($results, $client);
    }

    return $results;
}

/**
 * WHMCS API Call to retrieve the account numbers from WHMCS.
 *
 * @param string $adminuser
 *
 * @return array[]
 */
function acumulus_connect_getWHMCSAccountNumbers(string $adminuser): array
{
    $command = "GetPaymentMethods";
    $values = [];
    $results = localAPI($command, $values, $adminuser);
    if (isset($results['result'])) {
        if ($results['result'] == "success") {
            return $results['paymentmethods']["paymentmethod"];
        }
    }

    return [];
}

/**
 * WHMCS API Call to retrieve the payment gateway used in the first transaction.
 *
 * @param array $config
 * @param int $invoiceid
 *
 * @return string
 *
 * @todo: still used?
 * @noinspection PhpUnused
 */
function acumulus_connect_getPaymentGatewayUsed(array $config, int $invoiceid): string
{
    $command = 'GetTransactions';
    $values = [
        'invoiceid' => $invoiceid,
    ];
    return localAPI($command, $values, $config['acumulus_whmcs_admin'])['transactions']['transaction'][0]['gateway'];
}

/**
 * Helper function to retrieve the current WHMCS version.
 *
 * @return string
 */
function acumulus_connect_getWHMCSVersion(): string
{
    $version = Capsule::table('tblconfiguration')->where('setting', 'Version')->first();
    return $version->value;
}

/**
 * Helper function to Extend the invoice with custom values for tax etc.
 *
 * @param array $invoice
 * @param array $client
 *
 * @return array
 */
function acumulus_connect_expandInvoiceWithCustoms(array $invoice, array $client): array
{
    $config = acumulus_connect_getConfig();

    // Add some custom tax amounts
    $invoice['custom']['subtotal_taxedItems_exclTax'] = 0.0;
    $invoice['custom']['subtotal_taxedItems_inclTax'] = 0.0;
    $invoice['custom']['subtotal_untaxedItems'] = 0.0;
    $invoice['custom']['total_tax_roundedPerItem'] = 0.0;
    $invoice['custom']['total_tax'] = 0.0;

    $convertedVatTax = acumulus_connect_invoiceDetails_getVatType($config, $invoice, $client);
    $invoice['custom']['vattype'] = $convertedVatTax['vattype'];
    $invoice['custom']['taxrate'] = $convertedVatTax['taxrate'];
    if ($invoice['custom']['taxrate'] == '-1' || $invoice['custom']['taxrate'] == '0') {
        $invoice["taxrate"] = '0';
    }

    // @todo: optimize (reverse if and foreach => merge inner else branches,
    //   because the upper one contains errors (setting price incl to 0?);
    //   use "... as &item" instead of accessing $item by $counter.
    $counter = 0;
    if ($config['TaxType'] === 'Exclusive') {
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
                $invoice['items']['item'][$counter]['custom_tax_unrounded'] = 0.0;
                $invoice['items']['item'][$counter]['custom_tax_rounded'] = 0.0;
                $invoice['items']['item'][$counter]['custom_price_incl_tax_unrounded'] = 0.0;
                $invoice['items']['item'][$counter]['custom_price_incl_tax_rounded'] = 0.0;
                $invoice['items']['item'][$counter]['custom_price_excl_tax_unrounded'] = round(floatval($item['amount']), 4);
                $invoice['items']['item'][$counter]['custom_price_excl_tax_rounded'] = round(floatval($item['amount']), 2);
                $invoice['custom']['subtotal_untaxedItems'] += floatval($item['amount']);
            }
            $counter += 1;
        }
    } else {
        // Prices are set inclusive.
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
                $invoice['items']['item'][$counter]['custom_tax_unrounded'] = 0.0;
                $invoice['items']['item'][$counter]['custom_tax_rounded'] = 0.0;
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
    if ($config['acumulus_invoice_correction'] == "on") {
        $invoice = acumulus_connect_estimateTotals($config, $invoice, $client);
    }

    return $invoice;
}

/**
 * Helper function to replace text with dynamic values.
 *
 * @param string $text
 * @param array $invoice
 * @param array $client
 *
 * @return array|string|string[]
 */
function acumulus_connect_replaceVarsInText(string $text, array $invoice, array $client): string
{
    $replVars = [
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

    foreach ($replVars as $key => $value) {
        $text = str_ireplace($key, $value, $text);
    }

    return $text;
}

/**
 * Helper function to retrieve the cost centers from Acumulus.
 *
 * @return array
 */
function acumulus_connect_getCostcenters(): array
{
    // Construct the basic xml without email on errors or warnings.
    $xml = acumulus_connect_basicXML(false);
    $xml->addChild('format', 'xml');
    $xml_string = urlencode($xml->asXML());

    // Let's check the credentials against the Acumulus API.
    $url = "https://api.sielsystems.nl/acumulus/stable/picklists/picklist_costcenters.php";
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
    $rawCostcenters = $xml->costcenters;
    // Convert XML to json and then convert to Array.
    $jsonCostcenters = json_encode($rawCostcenters);
    $costcenters = json_decode($jsonCostcenters, true);
    // If there are more than 1 cost center in Acumulus, keep returning the
    // same array construction.
    if (isset($costcenters['costcenter'][1])) {
        $costcenters = $costcenters['costcenter'];
    }

    return $costcenters;
}

/**
 * Helper function to retrieve the bank account numbers from Acumulus.
 *
 * @return array
 */
function acumulus_connect_getAccounts(): array
{
    $xml = acumulus_connect_basicXML(false); //construct the basic xml without email on errors or warnings.
    $xml->addChild('format', 'xml');
    $xml_string = urlencode($xml->asXML());

    // Let's check the credentials against the Acumulus API.
    $url = "https://api.sielsystems.nl/acumulus/stable/picklists/picklist_accounts.php";
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
 *
 * @return array
 */
function acumulus_connect_getTemplates(): array
{
    // Construct the basic xml without email on errors or warnings.
    $xml = acumulus_connect_basicXML(false);
    $xml->addChild('format', 'xml');
    $xml_string = urlencode($xml->asXML());

    // Let's check the credentials against the Acumulus API.
    $url = "https://api.sielsystems.nl/acumulus/stable/picklists/picklist_invoicetemplates.php";
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
 * @param string $countrycode
 * @param string $date
 *
 * @return bool
 *
 * @todo: replace with Acumulus API call
 */
function acumulus_connect_isCountryinEU(string $countrycode, string $date): bool
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
    ];
    // Convert string to date.
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

    $isEU = in_array($countrycode, $eu_countries);

    unset($eu_countries);

    return $isEU;
}

/**
 * Helper function to calculate the vattype and taxrate by country, nature MOSS etc.
 *
 * @param array $config
 * @param array $invoice
 * @param array $client
 *
 * @return array
 */
function acumulus_connect_invoiceDetails_getVatType(array $config, array $invoice, array $client): array
{
    /* Vattypes:
       1 	National 	Gewone nationale factuur 	DEFAULT
       2 	National reverse charge 	Verlegde BTW binnen Nederland
       3 	International reverse charge 	BTW-verlegd naar ondernemer in de EU. Een intracommunautaire levering.
       4 	Export outside EU (export) 	Een factuur voor goederen buiten de EU
       5 	Margin scheme 	Marge regeling voor 2e-hands production
       6 	Foreign VAT 	Buitenlandse BTW voor electronische diensten aan particulieren in de EU. Usage of countrycode mandatory
    */

    if (strtoupper($client['countrycode']) == 'NL') {
        // Invoice is National.
        $vattype = '1';
        $taxrate = $invoice['taxrate'];
    } elseif (acumulus_connect_isCountryinEU(strtoupper($client['countrycode']), $invoice['date'])) {
        // Invoice is EU.
        if (strtotime($invoice['date']) < strtotime('2015-01-01')) {
            // factuur van voor 1 jan 2015 (pre MOSS).
            $vattype = '1';
            if (empty($client['companyname'])) {
                // particulier.
                $taxrate = $invoice['taxrate'];
            } else {
                // bedrijf.
                $taxrate = '0.00';
            }
        } else {
            // factuur na 1 jan 2015 (MOSS Regeling).
            // echo "<pre>";
            if (empty($client['companyname'])) {
                // particulier.
                // whmcs zijn digitale diensten.
                $vattype = '6';
                $taxrate = $invoice['taxrate'];
            } else {
                // bedrijf.
                $vattype = '3';
                $taxrate = '0.00';
            }
        }
    } else {
        // Invoice is Outside EU (WORLD).
        if (strtolower($config['acumulus_invoice_default_nature']) == 'service') {
            // The Default nature is a service (digitale diensten).
            if (empty($client['companyname'])) {
                // particulier
                // whmcs zijn digitale diensten.
                $vattype = '4';
                $taxrate = '0.00';  // btw angifte moet eventueel worden gedaan in het land van de afnemer.
            } else {
                // bedrijf.
                $vattype = '1';
                $taxrate = '-1'; // btw vrij.
            }
        } else {
            // The Default nature is a product.
            $vattype = '4';
            $taxrate = '0.00'; // btw vrij
        }
    }

    return ['vattype' => $vattype, 'taxrate' => $taxrate];
}

/**
 * Helper function to send the constructed XML to Acumulus with curl.
 *
 * @param array $config
 * @param array $invoice
 * @param array $client
 * @param \SimpleXMLElement $xml
 *
 */
function acumulus_connect_sendInvoicetoAccumulus(array $config, array $invoice, array $client, SimpleXMLElement $xml)
{
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
        [$config['acumulus_code'], $config['acumulus_username'], $config['acumulus_password']]);

    if (isset($result["status"])) {
        switch ($result["status"]) {
            case "1":  //failed
                $errors = print_r($result['error'], true);
                logActivity("acumulus_connect - Error sending Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Errors:" . $errors);
                break;
            case "0":  //success without warnings
                logActivity("acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Sent successfully");
                acumulus_connect_setinvoicetoken($invoice, $result["invoice"]["token"], $result["invoice"]["entryid"]);
                break;
            case "2":  //success with warnings
                $warnings = print_r($result['warning'], true);
                logActivity("acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Sent with " . $result["countwarnings"] . " warnings:" . $warnings);
                acumulus_connect_setinvoicetoken($invoice, $result["invoice"]["token"], $result["invoice"]["entryid"]);
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

/**
 * Helper function to update the token table for unpaid invoices.
 *
 * @param array $invoice
 * @param string $invoicetoken
 * @param int $entryid
 *
 */
function acumulus_connect_setinvoicetoken(array $invoice, string $invoicetoken, int $entryid)
{
    // Don't save the token to the reference table if paid, no need to store
    // references.
    if ($invoice["status"] == "Paid") {
        logModuleCall("acumulus_connect", "Add invoicetoken to database", 'Invoice is already Paid, no need to store invoicetoken.', '', '', []);
        return;
    }

    // check if invoiceid and invoicetoken are already stored and, if so, update.
    if (!Capsule::table('mod_acumulus_connect')->where('id', $invoice["invoiceid"])->exists()) {
        // no token exists, so let's add the token.
        try {
            if (Capsule::table('mod_acumulus_connect')->insert(
                [
                    'id' => $invoice["invoiceid"],
                    'token' => $invoicetoken,
                    'entryid' => $entryid,
                    'created_at' => date("Y-m-d H:i:s"),
                ]
            )
            ) {
                logModuleCall("acumulus_connect", "Add invoicetoken to database", '',
                    'Added successful: ' . $invoice["invoiceid"] . ' - ' . $invoicetoken . ' - ' . $entryid, '', []);
            }
        } catch (Exception $e) {
            logModuleCall("acumulus_connect", "Add invoicetoken to database", '', 'Error in SQL Query: ' . $e->getMessage(), '', []);
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
                    'Changed successfull: ' . $invoice["invoiceid"] . ' - ' . $invoicetoken, '', []);
            }
        } catch (Exception $e) {
            logModuleCall("acumulus_connect", "Update invoicetoken in database", '', 'Error in SQL Query: ' . $e->getMessage(), '', []);
        }
    }
}

/**
 * Helper function to estimate the totals like Acumulus would calculate.
 *
 * @param array $config
 * @param array $invoice
 * @param array $client
 *
 * @return array
 */
function acumulus_connect_estimateTotals(array $config, array $invoice, array $client): array
{
    $totalwhmcs = 0;
    $totalacumulus = 0;

    foreach ($invoice['items']['item'] as $item) {
        $totalwhmcs += (float) $item ["custom_price_incl_tax_unrounded"];
        $totalacumulus += (float) $item ["custom_price_incl_tax_rounded"];
    }
    $totalwhmcs = round($totalwhmcs, 2);
    $totalacumulus = round($totalacumulus, 2);

    $difference = $totalwhmcs - $totalacumulus;

    if ($difference != 0) {
        $corrline = [
            "id" => "n/a",
            "type" => "",
            "relid" => "0",
            "description" => acumulus_connect_replaceVarsInText($config['acumulus_invoice_correction_text'], $invoice, $client),
            "amount" => $difference,
            "taxed" => "0",
            "custom_tax_unrounded" => "0",
            "custom_tax_rounded" => "0",
            "custom_price_incl_tax_unrounded" => "$difference",
            "custom_price_incl_tax_rounded" => "$difference",
            "custom_price_excl_tax_unrounded" => "$difference",
            "custom_price_excl_tax_rounded" => "$difference",
        ];

        $invoice['items']['item'][] = $corrline;
    }

    return $invoice;
}

/**
 * Helper function to get the current payment status from Acumulus.
 *
 * @param array $config
 * @param string $token
 *
 * @return array
 *
 */
function acumulus_connect_getPaymentStatus(array $config, string $token): array
{
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
        [$config['acumulus_code'], $config['acumulus_username'], $config['acumulus_password']]);
    curl_close($ch);

    return $result['invoice'];
}

/**
 * Helper function to inverse the amounts for teh credit invoice.
 *
 * @param array $invoice
 *
 * @return array
 */
function acumulus_connect_inverseInvoiceAmounts(array $invoice): array
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
 *
 * @param bool $includeWarnings
 *
 * @return \SimpleXMLElement
 */
function acumulus_connect_basicXML(bool $includeWarnings = true): SimpleXMLElement
{
    $config = acumulus_connect_getConfig();
    // Create The XML FILE.
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><myxml></myxml>');
    // Contract details
    $contract = $xml->addChild('contract');
    $contract->addChild('contractcode', $config["acumulus_code"]);
    $contract->addChild('username', $config["acumulus_username"]);
    $contract->addChild('password', $config["acumulus_password"]);
    if ($includeWarnings) {
        // Do not include child object if entry is empty.
        if (!empty($config["acumulus_warning_email_address"])) {
            $contract->addChild('emailonerror', $config["acumulus_warning_email_address"]);
        }
        // Do not include child object if entry is empty.
        if (!empty($config["acumulus_error_email_address"])) {
            $contract->addChild('emailonwarning', $config["acumulus_error_email_address"]);
        }
    }
    // The connector feedback code.
    $connector = $xml->addChild('connector');
    $connector->addChild('application', 'WHMCS ' . acumulus_connect_getWHMCSVersion());
    $connector->addChild('webkoppel', 'WHMCS for Acumulus (acumulus_connect) versie ' . $config["version"]);
    $connector->addChild('development', 'SIEL - Remline');
    $connector->addChild('remark', 'development');
    $connector->addChild('sourceuri', 'https://www.siel.nl');

    return $xml;
}

/**
 * Helper function to prepare the customer data for the XML.
 *
 * @param array $config
 * @param array $invoice
 * @param array $client
 *
 * @return array
 */
function acumulus_connect_XMLPrepairCustomerDetails(array $config, array $invoice, array $client): array
{
    // Convert country code to country name.
    /** @noinspection PhpIncludeInspection  false positive */
    include_once('assets/ISO3166.php');
    $ISO3166 = new ISO3166;
    try {
        $country = $ISO3166->getByAlpha2($client['countrycode'])['name'];
    } catch (Exception $e) {
        $country = '';
    }

    // Get the id of the custom field corresponding the VAT title and loop
    // through the clients custom fields for the VAT value.
    $vatNr = '';
    try {
        $customFieldId = Capsule::table('tblcustomfields')->where('fieldname', $config["acumulus_whmcs_vatfield"])->first()->id;
        foreach ($client["customfields"] as $val) {
            if ($val['id'] === $customFieldId) {
                $vatNr = $val['value'];
            }
            if (empty($vatNr)) {
                $vatNr = $client['tax_id'];
            }
        }
        // @todo: do not catch and dismiss exceptions.
    } catch (Exception $e) {}

    // Get the id of the custom field corresponding the IBAN title and loop
    // through the clients custom fields for the IBAN value.
    $IBAN = '';
    try {
        $customFieldId = Capsule::table('tblcustomfields')->where('fieldname', $config["acumulus_whmcs_ibanfield"])->first()->id;
        foreach ($client["customfields"] as $val) {
            if ($val['id'] === $customFieldId) {
                $IBAN = $val['value'];
            }
        }
        // @todo: do not catch and dismiss exceptions.
    } catch (Exception $e) {}

    $firstname = (isset($client['firstname'])) ? $client['firstname'] : '';
    $lastname = (isset($client['lastname'])) ? $client['lastname'] : '';
    $fullname = $firstname . ' ' . $lastname;

    // Set how the customer is being imported into Acumulus
    // 1 = debtor, 2 = creditor, 3 = debtor/creditor (neutral)
    switch ($config["acumulus_customer_type"]) {
        case 'Debtor':
            $type = '1';
            break;
        case 'Creditor':
            $type = '2';
            break;
        default:
            // Debtor/Creditor (neutral)
            $type = '3';
    }

    // Set the status of the user to the same status as WHMCS.
    // 0 = Not active / disabled, 1 = Active
    switch ($client['status']) {
        case 'Closed':
        case 'Inactive':
            $contactstatus = '0';
            break;
        default:
            // Active
            $contactstatus = '1';
    }

    // Use Automatic prefill of countryname based on supplied countrycode,
    // Yes with Nederland or Leave the same as in WHMCS?.
    switch ($config['acumulus_customer_countryautoname']) {
        case 'Automatic prefill based on country code':
            $countryautoname = '1';
            break;
        case 'Automatic prefill based on country code including Nederland':
            $countryautoname = '2';
            break;
        default:
            // Use the same country as the customer in WHMCS.
            $countryautoname = '0';
    }

    $customerDetails['type'] = $type;
    $customerDetails['contactid'] = '';
    $customerDetails['contactyourid'] = (isset($client['userid'])) ? $client['userid'] : '';
    $customerDetails['contactstatus'] = $contactstatus;
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
    $customerDetails['overwriteifexists'] = (isset($config['acumulus_customer_overwriteifexists'])) ? ($config['acumulus_customer_overwriteifexists'] === 'on') ? '1' : '0' : '0';  // 0 = No update made, 1 = Overwrite all customer contact details
    $customerDetails['bankaccountnumber'] = $IBAN;
    $customerDetails['mark'] = acumulus_connect_replaceVarsInText($config['acumulus_cusromer_mark'], $invoice, $client);
    $customerDetails['disableduplicates'] = (isset($config['acumulus_customer_disableduplicates'])) ? ($config['acumulus_customer_disableduplicates'] === 'on') ? '1' : '0' : '0';  // 0 = Leave older duplicate contacts as is, 1 = Mark duplicate contacts as disabled

    // Cleanup and return the array.
    unset($firstname, $lastname, $fullname, $companyname, $config, $client, $type, $contactstatus, $countryautoname);

    return $customerDetails;
}

/**
 * Helper function to prepair the invoice data for the XML.
 *
 * if $isbulkimport is set to true then invoice numbering from acumulus is being ignored.
 *
 * @param array $config
 * @param array $invoice
 * @param array $client
 * @param bool $iscredit
 *
 * @return array
 */
function acumulus_connect_XMLPrepairInvoiceDetails(array $config, array $invoice, array $client, bool $iscredit = false): array
{
    // Format: yyyy-mm-dd.
    $invoiceDetails['issuedate'] = $invoice['date'];
    // When omitted, or when no match has been made possible, the first available costcenter in the contract will be selected.
    $invoiceDetails['costcenter'] = $config['acumulus_invoice_default_costcentername'];
    // 1 = Due (default), 2 = Paid
    $invoiceDetails['paymentstatus'] = ($invoice['status'] === 'Paid' ? '2' : '1');
    // Change the format from  yyyy-mm-dd hh:mm:ss   to  yyyy-mm-dd.  and unset var if eq 0000-00-00.
    // Format: yyyy-mm-dd.
    $invoiceDetails['paymentdate'] = (explode(' ', $invoice['datepaid'])[0] == '0000-00-00' ? null : explode(' ',
        $invoice['datepaid'])[0]);
    // When omitted, or when no match has been made possible, the first available template in the contract will be selected.
    $invoiceDetails['template'] = $config['acumulus_invoice_templateid'];

    // If ["acumulus_use_acumulus_invoice_numbering"] is disabled or bulk import is enabled use the WHMCS invoice number
    if ($config['acumulus_use_acumulus_invoice_numbering'] !== 'on') {
        // check if invoice number exists or uses the invoice id instead
        if ($invoice['invoicenum'] == '') {
            $invoice['invoicenum'] = $invoice['invoiceid'];
        }
        $invoiceDetails['number'] = $invoice['invoicenum'];
    }
    // Overall description of the invoice, invoice title.
    $invoiceDetails['description'] = acumulus_connect_replaceVarsInText($config['acumulus_invoice_description'], $invoice, $client);
    // Multiline field for extended description of the invoice. Content will appear on invoice and associated emails. Use \n for newlines. Tabs are not supported.
    $invoiceDetails['descriptiontext'] = str_replace("\n", "\\n",
        acumulus_connect_replaceVarsInText($config['acumulus_invoice_descriptiontext'], $invoice, $client));
    // Multiline field for additional remarks. Use \n for newlines and \t for tabs. Contents is placed in notes/comments section of the invoice. Content will not appear on the actual invoice or associated emails.
    $invoiceDetails['invoicenotes'] = str_replace("{TAB}", "\\t", str_replace("\n", "\\n",
        acumulus_connect_replaceVarsInText($config['acumulus_invoice_invoicenotes'], $invoice, $client)));

    // When omitted, or when no match has been made possible, the first available accountnumber in the contract will be selected
    $invoiceDetails['accountnumber'] = $config['account_numbers'][$invoice['paymentmethod']]['id'];
    $invoiceDetails['vattype'] = $invoice['custom']['vattype'];

    // Credit Invoice adjustments.
    if ($iscredit) {
        // Overall description of the invoice, invoice title.
        $invoiceDetails['description'] = acumulus_connect_replaceVarsInText($config['acumulus_creditinvoice_description'], $invoice, $client);
        $invoiceDetails['paymentstatus'] = 2;
        $invoiceDetails['paymentdate'] = date("Y-m-d");
    }

    // Invoice Line Variables.
    $invoiceDetails['invoicelines'] = [];
    if ($config["acumulus_summarize_invoice"] == 'on') {
        // Add total taxed items
        if (!empty($invoice['custom']['subtotal_taxedItems_exclTax'])) {
            $invoiceDetails['invoicelines'][] = [
                // non-mandatory, If set, this number will precede the product description (product).
                'itemnumber' => null,
                // non-mandatory, Product or service description.
                'product' => acumulus_connect_replaceVarsInText($config['acumulus_summarization_text_taxed'], $invoice, $client),
                // non-mandatory,  'Product'( default ), 'Service'
                'nature' => $config['acumulus_invoice_default_nature'],
                // non-mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                'unitprice' => $invoice['custom']['subtotal_taxedItems_exclTax'],
                // mandatory,  Applicable vatrate for the product. Defaults to "21".
                'vatrate' => $invoice['custom']['taxrate'],
                // non-mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                'quantity' => "1",
                // non-mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
                'costprice' => null,
            ];
        }
        // Add total untaxed items
        if (!empty($invoice['custom']['subtotal_untaxedItems'])) {
            $invoiceDetails['invoicelines'][] = [
                // non-mandatory, If set, this number will precede the product description (product).
                'itemnumber' => null,
                // non-mandatory, Product or service description.
                'product' => acumulus_connect_replaceVarsInText($config['acumulus_summarization_text_untaxed'], $invoice, $client),
                // non-mandatory,  'Product'( default ), 'Service'
                'nature' => $config['acumulus_invoice_default_nature'],
                // non-mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                'unitprice' => $invoice['custom']['subtotal_untaxedItems'],
                // mandatory,  Applicable vatrate for the product. Defaults to "21".
                'vatrate' => "-1",
                // non-mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                'quantity' => "1",
                // non-mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
                'costprice' => null,
            ];
        }
    } else {
        foreach ($invoice['items']['item'] as $item) {
            $invoiceDetails['invoicelines'][] = [
                // non-mandatory, If set, this number will precede the product
                // description on an invoice line.
                'itemnumber' => null,
                // non-mandatory, Product or service description.
                'product' => preg_replace("/\r\n|\r|\n/", ' ', $item['description']),
                // non-mandatory,  'Product'( default ), 'Service'
                'nature' => $config['acumulus_invoice_default_nature'],
                // non-mandatory,  Unit price without VAT. Decimal separator is a point. No thousand separators. 4 decimals precision. E.g. 12.95 or 1200.50 or 12.6495. Will be rounded if provided with more than 4 decimals.
                'unitprice' => $item['custom_price_excl_tax_unrounded'],
                // mandatory,  Applicable vatrate for the product. Defaults to "21".
                'vatrate' => ($item['taxed'] == 1) ? $invoice['custom']['taxrate'] : '-1',
                // non-mandatory, Number of products/services. Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 1 or 1.5 or 12.64. Default is 1.
                'quantity' => "1",
                // non-mandatory, Use in case of margin vat (marge-regeling). Decimal separator is a point. No thousand separators. 2 decimals precision. E.g. 12.95 or 1200.50
                'costprice' => null,
            ];
        }
    }

    return $invoiceDetails;
}

/**
 * Helper function to construct the XML that will be sent to Acumulus.
 *
 * @param array $config
 * @param array $invoice
 * @param array $client
 * @param bool $iscredit
 *
 * @return \SimpleXMLElement
 */
function acumulus_connect_generatexml(array $config, array $invoice, array $client, bool $iscredit = false): SimpleXMLElement
{
    // Create the basic XML.
    $customerDetails = acumulus_connect_XMLPrepairCustomerDetails($config, $invoice, $client);
    $invoiceDetails = acumulus_connect_XMLPrepairInvoiceDetails($config, $invoice, $client, $iscredit);

    // Create The XML file.
    $xml = acumulus_connect_basicXML();

    // Add customer details to the XML.
    $customer = $xml->addChild('customer');

    // Send non-mandatory customer information when enabled in module config.
    if ($config['acumulus_customer_import_enabled'] === "on") {
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

    // Add Invoice lines to the XML
    if (!empty($invoiceDetails['invoicelines'])) {
        foreach ($invoiceDetails['invoicelines'] as $invoiceLine) {
            // Set unitprice when omitted.
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
    }

    // Let Acumulus Send invoice to client
    if ($config['acumulus_emailaspdf'] == 'on') {
        $xlmInvoicePdfData = $xlmInvoice->addChild('emailaspdf');    // Imported invoices can be send as pdf file using email by Acumulus.
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
            $xlmInvoicePdfData->addChild('subject', acumulus_connect_replaceVarsInText($config['acumulus_emailaspdf_subject'], $invoice, $client));
        }
        if (!empty($config['acumulus_emailaspdf_message'])) {
            $xlmInvoicePdfData->addChild('message',
                str_replace("\n", "\\n", acumulus_connect_replaceVarsInText($config['acumulus_emailaspdf_message'], $invoice, $client)));
        }

        if ($config['acumulus_emailaspdf_confirmreading'] == 'on') {
            $xlmInvoicePdfData->addChild('confirmreading', '1');  // 1 = Ask for confirmation
        } else {
            $xlmInvoicePdfData->addChild('confirmreading', '0');  // 0 = Do not ask for confirmation
        }
    }

    // Cleanup and return the XML object.
    unset($config, $invoice, $client, $customerDetails, $customer, $customerDetails);

    return $xml;
}

/*
 * Functions called by Hooks
 */

/**
 * Sends the data of an invoice to Acumulus.
 *
 * @param array $config
 * @param int $invoiceid
 */
function acumulus_connect_sendInvoice(array $config, int $invoiceid): void
{
    $adminuser = $config['acumulus_whmcs_admin'];

    //Run whmcsapi to retrieve the invoice and customer.
    $invoice = acumulus_connect_getinvoice($invoiceid, $adminuser);
    $client = acumulus_connect_getclient($invoice['userid'], $adminuser);

    //Make the xml file
    $xml = acumulus_connect_generatexml($config, $invoice, $client);

    //Send xml to Acumulus
    acumulus_connect_sendInvoicetoAccumulus($config, $invoice, $client, $xml);
}

/**
 * Updates the payment status of an invoice at Acumulus.
 *
 * @param array $config
 * @param int $invoiceid
 * @param ?string $usedate
 */
function acumulus_connect_updateInvoice(array $config, int $invoiceid, string $usedate = null): void
{
    $invoice = acumulus_connect_getinvoice($invoiceid, $config["acumulus_whmcs_admin"]);
    $client = acumulus_connect_getclient($invoice['userid'], $config["acumulus_whmcs_admin"]);

    // Retrieve the token from the mod_acumulus_connect table
    try {
        $token = Capsule::table('mod_acumulus_connect')->where('id', $invoice["invoiceid"])->value('token');
    } catch (Exception $e) {
        logModuleCall("acumulus_connect", "Hook[InvoicePaid][acumulus_connect_updateInvoice]", '', 'Error in SQL Query: ' . $e->getMessage(), '', []);
    }
    // if the token exists update the invoice in Acumulus, else send entire invoice.
    if (!empty($token)) {

        // Update paymentgateway if use last paymentmethode is enabled and if it differs from the invoice set payment methode.
        if ($config['acumulus_invoice_use_last_paymentmethod'] == "on") {
            if (!empty($invoice["transactions"]["transaction"])) {
                $lastpaymentgateway = end($invoice["transactions"]["transaction"])["gateway"];
            } else {
                $lastpaymentgateway = null;
            }
            if ($invoice['paymentmethod'] !== $lastpaymentgateway) {
                acumulus_connect_updateInvoicePaymentMethode($config, $invoiceid, $lastpaymentgateway);
                //update the invoice in whmcs to the paid methode.
                $command = 'UpdateInvoice';
                $postData = [
                    'invoiceid' => $invoiceid,
                    'paymentmethod' => $lastpaymentgateway,
                ];
                $results = localAPI($command, $postData);
                logModuleCall("acumulus_connect", "[acumulus_connect_updateInvoice in whmcs]", 'API CALL:UpdateInvoice: ' . implode(',', $postData), $results, '', []);
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
            [$config['acumulus_code'], $config['acumulus_username'], $config['acumulus_password']]);

        $command = "logactivity";
        $adminuser = $config['acumulus_whmcs_admin'];
        if (isset($result["status"])) {
            $values["userid"] = $client["userid"];
            switch ($result["status"]) {
                case "1":  //failed
                    $errors = print_r($result['error'], true);
                    $values["description"] = "acumulus_connect - Error updateing Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Errors:" . $errors;
                    break;
                case "0":  //success without warnings
                    $values["description"] = "acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Updated successfully";
                    //acumulus_connect_delete_invoicetoken($invoiceid,$config);
                    break;
                case "2":  //success with warnings
                    $warnings = print_r($result['warning'], true);
                    $values["description"] = "acumulus_connect - Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"] . " Updated with " . $result["countwarnings"] . " warnings:" . $warnings;
                    //acumulus_connect_delete_invoicetoken($invoiceid,$config);
                    break;
                default:
                    $values["description"] = "acumulus_connect - Unspecified Error Updating Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"];
            }
        } else {
            $values["description"] = "acumulus_connect - API Error reaching acumulus website to update Invoice ID: " . $invoice["invoiceid"] . " for User ID: " . $client["userid"];
        }
        localAPI($command, $values, $adminuser);
        curl_close($ch);
    } else {
        // Token not found make a module log entry and sent entire invoice;
        logModuleCall("acumulus_connect", "Hook[InvoicePaid][acumulus_connect_updateInvoice]", '',
            'Token for id : "' . $invoice["invoiceid"] . '" not found so sending entire invoice.', '', []);
        acumulus_connect_sendInvoice($config, $invoiceid);
    }
}

/**
 * Updates the payment method of an invoice.
 *
 * @param array $config
 * @param int $invoiceid
 * @param string $paymentmethod
 */
function acumulus_connect_updateInvoicePaymentMethode(array $config, int $invoiceid, string $paymentmethod): void
{
    $entryid = Capsule::table('mod_acumulus_connect')->where('id', $invoiceid)->value('entryid');

    if (!empty($entryid)) {
        $accountnumber = $config['account_numbers'][$paymentmethod]['id'];
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
            [$config['acumulus_code'], $config['acumulus_username'], $config['acumulus_password']]);

        $command = "logactivity";
        $adminuser = $config['acumulus_whmcs_admin'];
        if (isset($result['entry']['entryproc'])) {
            switch ($result['entry']['entryproc']) {
                case "updated":  //failed
                    $values["description"] = "acumulus_connect - Changing payment methode for Invoice ID: " . $invoiceid . " to " . $config['account_numbers'][$paymentmethod]['name'] . "(" . $paymentmethod . ")";
                    break;
                case "error":  //success without warnings
                    $values["description"] = "acumulus_connect - Error changing payment methode for Invoice ID: " . $invoiceid . ". Not able to propagate the update.";
                    break;
                default:
                    $values["description"] = "acumulus_connect - Error changing payment methode for Invoice ID: " . $invoiceid . ". Undocumented error, payment methode probably not updated in Acumulus.";
            }
        } else {
            $values["description"] = "acumulus_connect - API Error reaching acumulus website to update paymentmethode ID: " . $invoiceid;
        }
        localAPI($command, $values, $adminuser);
        curl_close($ch);
    }
}

/**
 * Refund an invoice by creating a credit note.
 *
 * @param array $config
 * @param int $invoiceid
 */
function acumulus_connect_InvoiceCanceled(array $config, int $invoiceid): void
{
    $adminuser = $config['acumulus_whmcs_admin'];

    logModuleCall("acumulus_connect", "Hook[InvoiceCancelled][DEBUG[CONFIG]]", $config, '', '', []);

    // Run whmcsapi to retrieve the invoice and customer.
    $invoice = acumulus_connect_getinvoice($invoiceid, $adminuser, false);
    $client = acumulus_connect_getclient($invoice['userid'], $adminuser);

    // check if acumulus_use_acumulus_invoice_numbering is used, if so, send an invoice with negative amounts (credit invoice).
    if ($config['acumulus_use_acumulus_invoice_numbering'] == 'on') {
        // Check if the token exists, otherwise just create an activity record
        // and do nothing else.
        // Retrieve the token from the mod_acumulus_connect table.
        try {
            $token = Capsule::table('mod_acumulus_connect')->where('id', $invoice["invoiceid"])->value('token');
        } catch (Exception $e) {
            logModuleCall("acumulus_connect", "Hook[InvoiceCancelled][getToken]", '', 'Error in SQL Query: ' . $e->getMessage(), '', []);
        }
        // If the token exists update the invoice in Acumulus, else send the
        // entire invoice.
        if (!empty($token)) {
            // Set invoice to paid if its unpaid (get status from acumulus api).
            $paymentStatus = acumulus_connect_getPaymentStatus($config, $token);   // [paymentstatus] => 1 ,  [paymentdate] => 2018-01-20
            if ($paymentStatus == '0') {
                $paymentDate = date("Y-m-d");
                acumulus_connect_updateInvoice($config, $invoiceid, $paymentDate);
            }

            // Inverse the amounts in the invoice.
            $negativeInvoice = acumulus_connect_inverseInvoiceAmounts($invoice);
            $negativeInvoice = acumulus_connect_expandInvoiceWithCustoms($negativeInvoice, $client);

            // Make the xml file.
            $xml = acumulus_connect_generatexml($config, $negativeInvoice, $client, true);

            //Send new credit invoice (xml) to Acumulus.
            acumulus_connect_sendInvoicetoAccumulus($config, $negativeInvoice, $client, $xml);
        } else {
            $command = "logActivity";
            $values["description"] = "acumulus_connect - Credit invoice not created no token found for " . $invoice["invoiceid"] . " for User ID: " . $client["userid"];
            localAPI($command, $values, $adminuser);
        }
    } else {
        $command = "logActivity";
        $values["description"] = "acumulus_connect - Credit invoice not created not using acumulus sequential invoice numbering. ( " . $invoiceid . " for User ID: " . $client["userid"] . " )";
        localAPI($command, $values, $adminuser);
    }
}
