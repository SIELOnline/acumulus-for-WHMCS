<?php
/**
 * @noinspection PhpStaticAsDynamicMethodCallInspection
 */

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Integration\Whmcs\Shop;

use Siel\Acumulus\Invoice\Source;
use Siel\Acumulus\Tests\Whmcs\TestCase;

/**
 * InvoiceCreateTest tests the process of creating an {@see Invoice}.
 */
class InvoiceCreateTest extends TestCase
{
    protected static bool $emailAsPdf;

    /**
     * @before
     */
    public function beforeGetConfig(): void
    {
        self::$emailAsPdf = self::getContainer()->getConfig()->get('emailAsPdf');
        self::getContainer()->getConfig()->set('emailAsPdf', true);
    }

    /**
     * @after
     */
    public function afterResetConfig(): void
    {
        self::getContainer()->getConfig()->set('emailAsPdf', self::$emailAsPdf ?? true);
    }

    public static function InvoiceDataProvider(): array
    {
        return [
            'NL consument prijs inc btw' => [Source::Invoice, 2,],
            'NL consument prijs ex btw' => [Source::Invoice, 3,],
            'FR consument met FR btw' => [Source::Invoice, 4,],
        ];
    }

    /**
     * Tests the Creation process, i.e. collecting and completing an
     * {@see \Siel\Acumulus\Data\Invoice}.
     *
     * @dataProvider InvoiceDataProvider
     * @throws \JsonException
     */
    public function testCreate(string $type, int $id, array $excludeFields = []): void
    {
        $this->_testCreate($type, $id, $excludeFields);
    }
}
