<?php
/**
 * This file contains hook implementations for the Acumulus connect addon.
 *
 * This file will be detected and loaded by WHMCS on every page load,
 * {@see https://developers.whmcs.com/hooks/module-hooks/}
 *
 * @noinspection PhpUnused  Hooks are called from the WHMCS system by
 *    constructing their name.
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// @todo: replaced by the line below, is that correct?
//   use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Database\Capsule;

include_once('acumulus_connect_functions.php');

/**
 * This hook is run when:
 * - A new invoice has been generated following sending the Invoice Created
 *   email (hook 'InvoiceCreated') .
 * - A new invoice has been generated by the cron, order process, API, when
 *   converting a quote to an invoice, or when published a draft invoice with
 *   email. This is run before the invoice is sent to the client (hook
 *   'InvoiceCreationPreEmail'.
 *
 * @param array $vars
 */
function acumulus_connect_triggerInvoiceCreationPreEmailHook(array $vars): void
{
    $config = acumulus_connect_getConfig();
    if ($config['acumulus_hook_invoice_create_enabled'] == 'on') {
        $invoiceid = $vars['invoiceid'];
        // Check if invoice id and invoice token are already stored and, if so,
        // skip sending the invoice.
        if (!Capsule::table('mod_acumulus_connect')->where('id', $invoiceid)->exists()) {
            // No token exists, so let's send the invoice;
            acumulus_connect_sendInvoice($config, $invoiceid);
        } else {
            logActivity("acumulus_connect - Skipped sending Invoice ID: " . $invoiceid . " by hook 'triggerInvoiceCreationPreEmailHook' because it was already send.");
        }
    }
}

/**
 * This hook is run when:
 * - An invoice is paid prior to any email or automation tasks associated with
 *   the payment action having been run.
 *
 * @param array $vars
 */
function acumulus_connect_triggerInvoicePaidHook(array $vars): void
{
    $config = acumulus_connect_getConfig();
    if ($config['acumulus_hook_invoice_paid_enabled'] == 'on') {
        $invoiceid = $vars['invoiceid'];
        acumulus_connect_updateInvoice($config, $invoiceid);
    }
}

/**
 * This hook is run when:
 * - Changing the gateway on an invoice.
 *
 * @param array $vars
 */
function acumulus_connect_triggerInvoiceChangeGatewayHook(array $vars): void
{
    $config = acumulus_connect_getConfig();
    $invoiceid = $vars['invoiceid'];
    $paymentmethod = $vars['paymentmethod'];
    acumulus_connect_updateInvoicePaymentMethode($config, $invoiceid, $paymentmethod);
}

/**
 * This hook is run when:
 * - An invoice is canceled. This function reacts by creating a credit invoice.
 *
 * @param array $vars
 */
function acumulus_connect_triggerInvoiceCanceledHook(array $vars): void
{
    $config = acumulus_connect_getConfig();
    if ($config['acumulus_hook_invoice_canceled_enabled'] == 'on') {
        $invoiceid = $vars['invoiceid'];
        acumulus_connect_InvoiceCanceled($config, $invoiceid);
    }
}

add_hook('InvoiceCreated', 500, "acumulus_connect_triggerInvoiceCreationPreEmailHook");
add_hook("InvoiceCreationPreEmail", 500, "acumulus_connect_triggerInvoiceCreationPreEmailHook");
add_hook("InvoicePaid", 1, "acumulus_connect_triggerInvoicePaidHook");
add_hook('InvoiceChangeGateway', 1, "acumulus_connect_triggerInvoiceChangeGatewayHook");
add_hook('InvoiceCancelled', 1, "acumulus_connect_triggerInvoiceCanceledHook");
