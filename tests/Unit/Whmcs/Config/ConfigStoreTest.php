<?php
/**
 * @noinspection PhpUnhandledExceptionInspection
 * @noinspection PhpDynamicFieldDeclarationInspection
 */

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Unit\Whmcs\Config;

use Siel\Acumulus\Tests\Whmcs\TestCase;
use Siel\Acumulus\Whmcs\Config\ConfigStore;
use WHMCS\Database\Capsule;

/**
 * ConfigStoreTest tests the WHMCS {@see ConfigStore} class.
 */
class ConfigStoreTest extends TestCase
{
    private const ConfigKey = 'acumulus-test-config-key';

    private static ConfigStore $configStore;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$configStore = new ConfigStore();
        (fn() => $this->configKey = ConfigStoreTest::ConfigKey)->call(self::$configStore);
    }

    public static function tearDownAfterClass(): void
    {
        Capsule::table('tbladdonmodules')
            ->where(['module' => self::ConfigKey, 'setting' => self::ConfigKey])
            ->delete();
        parent::tearDownAfterClass();
    }

    public function testLoadEmpty(): void
    {
        static::assertEmpty(self::$configStore->load());
    }

    public function testSaveAndLoad(): void
    {
        $values = [
            'debug' => 2,
            'logLevel' => 4,
            'contractcode' => '123456789',
            'username' => 'my_user_name',
            'password' => 'my_password',
            'emailonerror' => 'test@example.com',
        ];

        static::assertTrue(self::$configStore->save($values));
        $loadedValues = self::$configStore->load();
        static::assertSame($values, $loadedValues);
    }
}
