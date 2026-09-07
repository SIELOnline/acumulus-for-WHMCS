<?php

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Whmcs;

use Siel\Acumulus\Tests\AcumulusTestUtils as BaseAcumulusTestUtils;

use function dirname;

/**
 * AcumulusTestUtils contains WHMCS specific test functionalities
 */
trait AcumulusTestUtils
{
    use BaseAcumulusTestUtils;

    protected static string $shopNamespace = 'Whmcs';

    protected static function getTestsPath(): string
    {
        return dirname(__FILE__, 2);
    }
}
