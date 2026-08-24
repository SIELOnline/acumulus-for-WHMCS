<?php
/**
 * @noinspection HtmlDeprecatedAttribute
 * @noinspection HtmlDeprecatedTag
 */

declare(strict_types=1);

/**
 * A note on security:
 * https://laravel.com/docs/8.x/queries#introduction says:
 *     The Laravel query builder uses PDO parameter binding to protect your
 *     application against SQL injection attacks. There is no need to clean
 *     or sanitise strings passed to the query builder as query bindings.
 *
 *     PDO does not support binding column names. Therefore, you should
 *     never allow user input to dictate the column names referenced by your
 *     queries, including "order by" columns.
 *
 * So by using the query builder and not constructing our own queries we are
 * safe against SQL injection attacks (and differences in the SQL dialect of the
 * actual database used).
 *
 * A note on error logging:
 * https://docs.whmcs.com/Error_Management#Controlling_How_Errors_Are_Managed
 */
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use Siel\Whmcs\Acumulus\Acumulus;

/**
 * Initialises the autoloader and returns an {@see \Siel\Whmcs\Acumulus\Acumulus} instance.
 */
function acumulus_get(): ?Acumulus
{
    require_once __DIR__ . '/vendor/autoload.php';
    return Acumulus::getInstance();
}

/*
 *  Module Mandatory functions.
 */

/**
 * Function to return the configuration fields for the Acumulus module.
 *
 * @noinspection PhpUnused function {addon_name}_config() is required by WHMCS.
 */
function acumulus_config(): array
{
    return acumulus_get()->config();
}

/**
 * Performs custom actions on activating this module.
 * - Create DB table
 *
 * @return string[]
 *   Result of the activation: 2 strings keyed by 'status''and 'description'.
 *
 * @noinspection PhpUnused  Called by WHMCS when this module gets activated.
 */
function acumulus_activate(): array
{
    return acumulus_get()->activate();
}

/**
 * Performs custom actions on deactivating this module.
 *
 * - Remove Custom DB Table
 *
 * @todo
 *   Should we always drop the table or can we ask for confirmation?
 *
 * @return string[]
 *   Result of the deactivation: 2 strings keyed by 'status''and 'description'.
 *
 * @noinspection PhpUnused  Called by WHMCS when this module gets deactivated.
 */
function acumulus_deactivate(): array
{
    return acumulus_get()->deactivate();
}


/**
 * Performs custom actions on upgrading this module.
 *
 * @noinspection PhpUnused  Called by WHMCS after this module has been upgraded.
 */
function acumulus_upgrade($vars): void
{
    acumulus_get()->upgrade($vars);
}

/*
 * Module Additional functions. These functions are called by WHMCS based on
 * naming patterns.
 */

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
 * @noinspection PhpUnused  Called by WHMCS to render the admin page.
 */
function acumulus_output(array $vars): void
{
    acumulus_get()->output($vars);
}
