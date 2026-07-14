<?php
/**
 * @noinspection AutoloadingIssuesInspection
 * @noinspection EmptyClassInspection
 * @noinspection PhpMissingDocCommentInspection
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection PhpMultipleClassesDeclarationsInOneFile
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection PhpInconsistentReturnPointsInspection
 * @noinspection PhpReturnDocTypeMismatchInspection
 * @noinspection SpellCheckingInspection
 *
 * Thi stub contains definitions of functions, classes and methods available for addon
 * developers. As WHMCS code is obscured with ioncube, these definitions are not readily
 * available in the IDE. This stub will never be compiled and is only meant to be used at
 * development time, aiding the IDE with:
 * - code completion
 * - type checking
 * - code analysis
 *
 * Most of the below definitions come from the WHMCS developer documentation:
 * - https://developers.whmcs.com/: developer documentation containing a.o. all hooks an
 *   addon can subscribe to and all internal api methods an addon can call.
 * - https://classdocs.whmcs.com/8.13/index.html: internal classes. However, note that in
 *   hooks you will likely get arrays with the (public?) property names as keys.
 * - ChatGPT did find some functions/methods I hadn't found myself. I hope they are not
 *   AI hallucinations (yet to test at the moment of writing this).
 */

declare(strict_types=1);

namespace {

    /*
     * Functions available in the global namespace.
     */

    /**
     * Logs activity.
     *
     * A helper method is available for adding entries to the activity log. This function
     * is available to all hooks, modules and template files throughout the WHMCS system.
     *
     * From: {@link https://developers.whmcs.com/advanced/logging/}
     *
     * @param string $message
     *   The message to log.
     * @param int $userId
     *   An optional user id to which the log entry relates.
     */
    function logActivity(string $message, int $userId = 0): void
    {
    }

    /**
     * Logs a module call.
     *
     * We recommend making use of the module log to record all external API calls and
     * requests. This makes debugging the external API calls from your modules easier
     * and consistent with other modules.
     *
     * We recommend passing data strings such as usernames and passwords into the
     * $replaceVars parameter to allow them to be automatically scrubbed and ommitted from
     * module log entries.
     *
     * From: {@link https://developers.whmcs.com/advanced/logging/}
     * See {@link https://developers.whmcs.com/provisioning-modules/module-logging/}
     *
     * @param string $module The name of the module
     * @param string $action The name of the action being performed
     * @param array|string $requestString The input parameters for the API call
     * @param array|string $responseData The response data from the API call
     * @param array|string $processedData The resulting data after any post processing (eg. json decode, xml decode, etc...)
     * @param array $replaceVars An array of strings for replacement
     */
    function logModuleCall(
        string $module,
        string $action,
        array|string $requestString,
        array|string $responseData,
        array|string $processedData,
        array $replaceVars
    ): void {
    }

    /**
     * The Internal API should be used when making API calls from within the WHMCS system.
     * Common uses for this include from modules, hooks, or other custom code local
     * to the WHMCS installation.
     * The import of init.php is not required when you’re already in a WHMCS runtime
     * (like in a hook) where init.php has already been imported.
     * From: {@link https://developers.whmcs.com/api/internal-api/}
     * See {@link https://developers.whmcs.com/api/api-index/} for an overview of
     * available API calls with details per call on separate pages.
     *
     * @param string $command
     *   The API call to execute.
     * @param array $values
     *   The arguments to pass to the API call, depends on the API call to execute.
     *
     * @return array
     *  Array with keys:
     *  - result: string: success or error.
     *  - message: string: optional, error message in case of error.
     *  - Other keys depend on the API funtion executed.
     */
    function localAPI(string $command, array $values, string $adminUserName = ''): array
    {
    }

    /**
     * Register hook function call.
     *
     * @param string $hookPoint The hook point to call
     * @param int $priority The priority for the given hook function
     * @param callable $function Function name to call or anonymous function.
     *
     * @return mixed
     *   Depends on hook function point.
     */
    function add_hook(string $hookPoint, int $priority, callable $function)
    {
    }

    /**
     * Runs a cyustom hook.
     *
     * @param string $hookPoint
     *   The name of the hook.
     * @param array $vars
     *   The variables to pass to the functions that subscribed to this hook.
     *   The values are keyed by their variable name.
     */
    function run_hook(string $hookPoint, array $vars): void
    {
    }

    /**
     * Converts a date entered in the system setting format to a MySQL Date/Timestamp
     *
     * @return string Format: 2016-12-30 23:59:59
     */
    function toMySQLDate(string $userInputDate): string
    {
    }

    /**
     * Formats a MySQL Date/Timestamp value to system settings
     *
     * @param string $datetimestamp The MySQL Date/Timestamp value
     * @param bool $includeTime Pass true to include the time in the result
     * @param bool $applyClientDateFormat Set true to apply Localisation > Client Date Format
     */
    function fromMySQLDate(string $datetimestamp, bool $includeTime = false, bool $applyClientDateFormat = false): string
    {
    }

    /**
     * Returns today's date
     * By default, returns the format defined in General Settings > Localisation > Date Format
     *
     * @param bool $applyClientDateFormat Set true to apply Localisation > Client Date Format
     */
    function getTodaysDate(bool $applyClientDateFormat = false): string
    {
    }
}

namespace WHMCS {

    class Application
    {
        public static function getVersion(): string
        {
        }
    }
}

namespace WHMCS\Billing {

    class Tax
    {
        /**
         * @return \stdClass[]
         */
        public static function all(): array
        {
        }
    }
}

namespace WHMCS\Config {

    class Setting
    {
        public static function getValue(string $key): string
        {
        }
    }
}

namespace WHMCS\Database {

    class Capsule extends \Illuminate\Database\Capsule\Manager
    {
    }
}
