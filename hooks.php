<?php
/**
 * This file contains hook implementations for the Acumulus addon.
 *
 * This file will be detected and loaded by WHMCS on every page load,
 * {@see https://developers.whmcs.com/hooks/module-hooks/}
 *
 * @noinspection StaticClosureCanBeUsedInspection
 */

declare(strict_types=1);

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use Siel\Whmcs\Acumulus\Hooks;

/**
 * Initialises the autoloader and returns an {@see \Siel\Whmcs\Acumulus\Hooks} instance.
 */
function getAcumulusHooks(): ?Hooks
{
    static $hooks = null;

    if ($hooks === null) {
        require_once __DIR__ . '/vendor/autoload.php';
        $hooks = new Hooks();
    }
    return $hooks;
}

/**
 * See: https://developers.whmcs.com/hooks-reference/invoices-and-quotes/#invoicecreated
 *
 * Executed when an invoice has left “Draft” status and is available to its respective
 * client. Execution of this hook occurs after sending the Invoice Created email.
 */
add_hook('InvoiceCreated', 500, function (array $vars): void {
    getAcumulusHooks()->invoiceCreated($vars, 'InvoiceCreated');
});

/**
 * Hook 'InvoiceCreationPreEmail'.
 * @todo is this necessary?
 */
add_hook('InvoiceCreationPreEmail', 500, function (array $vars): void {
    getAcumulusHooks()->invoiceCreated($vars, 'InvoiceCreationPreEmail');
});

/**
 * See https://developers.whmcs.com/hooks-reference/invoices-and-quotes/#invoicepaid
 *
 * Executes when an invoice is Paid following the email receipt having been sent and any
 * automation tasks associated with the payment action having been run.
 */
add_hook('InvoicePaid', 1, function (array $vars): void {
    getAcumulusHooks()->invoicePaid($vars);
});

/**
 * See https://developers.whmcs.com/hooks-reference/invoices-and-quotes/#invoicechangegateway
 *
 * Executes when changing the gateway on an invoice.
 *
 * Note: the gateway defines the payment method which in turn may change the account or
 * template of an invoice in Acumulus.
 */
add_hook('InvoiceChangeGateway', 1, function (array $vars): void {
    getAcumulusHooks()->invoiceChangeGateway($vars);
});

/**
 * See https://developers.whmcs.com/hooks-reference/invoices-and-quotes/#invoicecancelled
 *
 * Executes when an invoice is being cancelled.
 *
 * Note: We should react by creating and sending a credit note.
 */
add_hook('InvoiceCancelled', 1, function (array $vars): void {
    getAcumulusHooks()->invoiceCancelled($vars);
});

/**
 * See https://developers.whmcs.com/hooks-reference/output/#adminareaheadoutput
 *
 * Runs on every admin area page load. All template variables defined at the time the hook
 * is invoked are made available to this hook point. This can vary by page. The list on
 * the mentioned page is not exhaustive.
 *
 * Response: Accepts HTML to be output within the head tag of the admin area output.
 */
add_hook('AdminAreaHeadOutput', 500, function (array $vars): string {
    return getAcumulusHooks()->adminAreaHeadOutput($vars);
});
