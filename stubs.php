<?php
/**
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection PhpMultipleClassesDeclarationsInOneFile
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection PhpInconsistentReturnPointsInspection
 * @noinspection PhpReturnDocTypeMismatchInspection
 * @noinspection SpellCheckingInspection
 *
 * This stub will never be compiled and is thus only used at development time.
 *
 * This file contains class and function stubs to aid the IDE with:
 * - code completion
 * - type checking
 * - code analysis
 */

/**
 * Global namespace: available functions
 */
namespace
{
/**
 * Log activity.
 *
 * A helper method is available for adding entries to the activity log. This
 * function is available to all hooks, modules and template files throughout the
 * WHMCS system.
 *
 * From: {@see https://developers.whmcs.com/advanced/logging/}
 *
 * @param string $message The message to log
 * @param int $userId An optional user id to which the log entry relates
 */
function logActivity(string $message, int $userId = 0) {}

/**
 * Log module call.
 *
 * We recommend making use of the module log to record all external API calls
 * and requests. This makes debugging the external API calls your modules easier
 * and consistent with other modules.
 *
 * We recommend passing data strings such as usernames and passwords into the
 * $replaceVars parameter to allow them to be automatically scrubbed and
 * ommitted from module log entries.
 *
 * From: {@see https://developers.whmcs.com/advanced/logging/}
 * See {@see https://developers.whmcs.com/provisioning-modules/module-logging/}
 *
 * @param string $module The name of the module
 * @param string $action The name of the action being performed
 * @param string|array $requestString The input parameters for the API call
 * @param string|array $responseData The response data from the API call
 * @param string|array $processedData The resulting data after any post processing (eg. json decode, xml decode, etc...)
 * @param array $replaceVars An array of strings for replacement
 */
function logModuleCall(string $module, string $action, $requestString, $responseData, $processedData, array $replaceVars) {}

/**
 * The Internal API should be used when making API calls from within the WHMCS system.
 *
 * Common uses for this include from modules, hooks, or other custom code local
 * to the WHMCS installation.
 *
 * The import of init.php is not required when you’re already in a WHMCS runtime
 * (like in a hook) where init.php has already been imported.
 *
 * From: {@see https://developers.whmcs.com/api/internal-api/}
 *
 * See {@see https://developers.whmcs.com/api/api-index/} for an overview of
 * available API calls with details per call on separate pages.
 *
 * @param string $command
 * @param array $values
 * @param string $adminUserName
 *
 * @return array
 *  Array with keys:
 *  - result: string: success or error.
 *  - message: string: optional, error message in case of error.
 *  - Other keys depend on the API funtion called, see
 *    {@see https://developers.whmcs.com/api/api-index/}.
 */
function localAPI(string $command, array $values, string $adminUserName = ''): array {}

/**
 * Register hook function call.
 *
 * @param string $hookPoint The hook point to call
 * @param integer $priority The priority for the given hook function
 * @param callable $function Function name to call or anonymous function.
 *
 * @return mixed
 *   Depends on hook function point.
 */
function add_hook(string $hookPoint, int $priority, callable $function) {}

/**
 * Converts a date entered in the system setting format to a MySQL Date/Timestamp
 *
 * @param string $userInputDate
 *
 * @return string Format: 2016-12-30 23:59:59
 */
function toMySQLDate(string $userInputDate): string {}
/**
 * Formats a MySQL Date/Timestamp value to system settings
 *
 * @param string $datetimestamp The MySQL Date/Timestamp value
 * @param bool $includeTime Pass true to include the time in the result
 * @param bool $applyClientDateFormat Set true to apply Localisation > Client Date Format
 *
 * @return string
 */
function fromMySQLDate(string $datetimestamp, bool $includeTime = false, bool $applyClientDateFormat = false): string {}
/**
 * Returns today's date
 *
 * By default, returns the format defined in General Settings > Localisation > Date Format
 *
 * @param bool $applyClientDateFormat Set true to apply Localisation > Client Date Format
 *
 * @return string
 */
function getTodaysDate(bool $applyClientDateFormat = false): string {}
}

namespace WHMCS\Database {
    class Capsule extends \Illuminate\Database\Capsule\Manager {}
}
