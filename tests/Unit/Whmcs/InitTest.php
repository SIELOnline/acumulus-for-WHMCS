<?php

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Unit\Whmcs;

use Siel\Acumulus\Tests\Whmcs\TestCase;

/**
 * Tests that WooCommerce and Acumulus have been initialised.
 */
class InitTest extends TestCase
{
    /**
     * A single test to see if the test framework (including the plugins) has been
     * initialised correctly:
     * 1 We have access to the Container.
     * 2 WHMCS has been initialised.
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
