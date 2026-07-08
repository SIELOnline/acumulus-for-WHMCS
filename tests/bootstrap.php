<?php

declare(strict_types=1);

define('WHMCS', true);

require_once __DIR__ . '/environment.php';

$whmcsRoot = getenv('WHMCS_ROOT');
if (empty($whmcsRoot)) {
    $whmcsRoot = dirname(__FILE__, 3);
    // If our plugin is symlinked, we may need to redefine $whmcsRoot.
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
    fwrite(STDERR, "WHMCS niet gevonden op {$whmcsRoot}. Zet WHMCS_ROOT.\n");
    exit(1);
}

define('ROOTDIR', $whmcsRoot); // veel includes verwachten dit
$acumulusRoot = $whmcsRoot . '/modules/addons/acumulus';

chdir($whmcsRoot);
//require_once $whmcsRoot . '/vendor/autoload.php';
//require_once $whmcsRoot . '/configuration.php';
require_once $whmcsRoot . '/init.php';

// Start up the Acumulus specific testing environment.z
require __DIR__ . '/bootstrap-acumulus.php';
