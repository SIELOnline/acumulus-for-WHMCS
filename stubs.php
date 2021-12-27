<?php
/**
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection PhpMultipleClassesDeclarationsInOneFile
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection PhpInconsistentReturnPointsInspection
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
 * Log module call.
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
 * Log activity.
 *
 * @param string $message The message to log
 * @param int $userId An optional user id to which the log entry relates
 */
function logActivity(string $message, int $userId = 0) {}

/**
 * The Internal API should be used when making API calls from within the WHMCS system.
 *
 * Common uses for this include from modules, hooks, or other custom code local
 * to the WHMCS installation.
 *
 * The import of init.php is not required when you’re already in a WHMCS runtime
 * (like in a hook) where init.php has already been imported.
 *
 * @param string $command
 * @param array $values
 * @param string $adminUserName
 *
 * @return array
 *  Array with keys:
 *  - result: string: success or another string indicating non-success.
 *  - message: string: error message
 */
function localAPI(string $command, array $values, string $adminUserName = ''): array {}

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
