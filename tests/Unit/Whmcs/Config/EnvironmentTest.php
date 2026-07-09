<?php

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Unit\Whmcs\Config;

use Siel\Acumulus\Tests\Whmcs\TestCase;
use Siel\Acumulus\Whmcs\Config\Environment;

/**
 * EnvironmentTest tests the
 * {@see \Siel\Acumulus\Whmcs\Config\Environment WHMC Environment} class.
 */
class EnvironmentTest extends TestCase
{
    /**
     * Tests the {@see Environment::toArray() method}.
     *
     * This method returns a keyed array with information about the environment of this
     * addon/library:
     *    - 'baseUri'
     *    - 'apiVersion'
     *    - 'libraryVersion'
     *    - 'moduleVersion'
     *    - 'shopName'
     *    - 'shopVersion'
     *    - 'cmsName'
     *    - 'cmsVersion'
     *    - 'hostName'
     *    - 'phpVersion'
     *    - 'os'
     *    - 'curlVersion'
     *    - 'dbName'
     *    - 'dbVersion'
     *    - 'supportEmail'
     */
    public function testGetEnvironment(): void
    {
        $container = self::getContainer();
        $environmentInfo = $container->getEnvironment()->toArray();

        // Tests all keys are available.
        $keys = [
            'baseUri',
            'apiVersion',
            'libraryVersion',
            'moduleVersion',
            'shopName',
            'shopVersion',
            'cmsName',
            'cmsVersion',
            'hostName',
            'phpVersion',
            'os',
            'curlVersion',
            'dbName',
            'dbVersion',
            'supportEmail',
        ];
        foreach ($keys as $key) {
            static::assertArrayHasKey($key, $environmentInfo);
        }

        // Some specific tests:
        static::assertNotSame(Environment::Unknown, $environmentInfo['dbName']);
        static::assertNotSame(Environment::Unknown, $environmentInfo['dbVersion']);
        static::assertSame('WHMCS', $environmentInfo['shopName']);
        static::assertMatchesRegularExpression('|\d+\.\d+\.\d+|', $environmentInfo['shopVersion']);
    }
}
