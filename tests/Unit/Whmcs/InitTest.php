<?php

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Unit\Whmcs;

use Siel\Acumulus\Tests\Whmcs\TestCase;

/**
 * Tests that WooCommerce and Acumulus have been initialized.
 */
class InitTest extends TestCase
{
    /**
     * A single test to see if the test framework (including the plugins) has been
     * initialized correctly:
     * 1 We have access to the Container.
     * 2 WooCommerce has been initialized.
     */
    public function testInit(): void
    {
        // 1.
        $container = self::getContainer();
        $environmentInfo = $container->getEnvironment()->toArray();
        // 2.
        static::assertMatchesRegularExpression('|\d+\.\d+\.\d+|', $environmentInfo['shopVersion']);
    }
}
