<?php

declare(strict_types=1);

const WHMCS = true;

require_once __DIR__ . '/environment.php';

$whmcsRoot = getenv('WHMCS_ROOT');
if (empty($whmcsRoot)) {
    $whmcsRoot = dirname(__FILE__, 3);
    // If our plugin is symlinked, which it is in my test env, we may need to redefine
    // $whmcsRoot.
    // Try to find it by looking at the --bootstrap option as passed to phpunit.
    global $argv;
    if (is_array($argv)) {
        $i = array_search('--bootstrap', $argv, true);
        // if we found --bootstrap, the value is in the next entry.
        if ($i < count($argv) - 1) {
            $bootstrapFile = $argv[$i + 1];
            $whmcsRoot = substr($bootstrapFile, 0, strpos($bootstrapFile, 'modules') - 1);
        }
    }
}

if (!is_file($whmcsRoot . '/configuration.php')) {
    fwrite(STDERR, "WHMCS niet gevonden op $whmcsRoot. Zet WHMCS_ROOT.\n");
    exit(1);
}

define('ROOTDIR', $whmcsRoot); // veel includes verwachten dit
$acumulusRoot = $whmcsRoot . '/modules/addons/acumulus';

chdir($whmcsRoot);
//require_once $whmcsRoot . '/vendor/autoload.php';
//require_once $whmcsRoot . '/configuration.php';
/** @noinspection PhpIncludeInspection false positive */
require_once $whmcsRoot . '/init.php';

/* @todo: When trying to add a client we got the message "Error: Call to a member function
 *   get_req_var() on string". Looking at the stack trace it seemed that an "App" object
 *   was expected. Apparently our bootstrap is not full (and probably will not instantiate
 *   a "WebApp" instance). So revert to "direct" db access instead of API calls to create
 *   things (or at least a client + user combination)?
 */

// Start up the Acumulus specific testing environment.
require __DIR__ . '/bootstrap-acumulus.php';
