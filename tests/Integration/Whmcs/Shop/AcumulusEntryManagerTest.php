<?php

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Integration\Whmcs\Shop;

use DateTimeImmutable;
use Exception;
use Siel\Acumulus\Invoice\Source;
use Siel\Acumulus\Whmcs\Helpers\LocalApiTrait;
use Siel\Acumulus\Whmcs\Shop\AcumulusEntry;
use Siel\Acumulus\Tests\Whmcs\TestCase;
use Siel\Acumulus\Whmcs\Shop\AcumulusEntryManager;
use WHMCS\Database\Capsule;

/**
 * AcumulusEntryManagerTest tests the WHMCS
 * {@see \Siel\Acumulus\Whmcs\Shop\AcumulusEntryManager} class.
 */
class AcumulusEntryManagerTest extends TestCase
{
    use LocalApiTrait;

    private const testSourceType = Source::Invoice;
    private const testSourceId = 1;
    private const testConceptId = 2; // Acumulus concept ids are auto incrementing and will never equal this anymore.
    private const testEntryId = 7; // Acumulus entry ids are auto incrementing and will never equal this anymore.
    private const testToken = 'TESTTOKEN0123456789TESTTOKENTest';

    private static bool $didCreateTable = false;

    private static function getAcumulusEntryManager(): AcumulusEntryManager
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return static::getContainer()->getAcumulusEntryManager();
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $tableName = 'mod_acumulus_entries';
        if (!Capsule::schema()->hasTable($tableName)) {
            static::assertTrue(static::getAcumulusEntryManager()->install());
            static::$didCreateTable = true;
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$didCreateTable) {
            static::assertTrue(static::getAcumulusEntryManager()->uninstall());
        }
        parent::tearDownAfterClass();
    }

    /**
     * Ensures that a client exists with the given id.
     */
    private function ensureClient(string $email): int
    {
        $result = $this->localApi()->exec('getClients', ['search' => 'jan.modaal@example.com',]);
        if (empty($result['clients']['client'])) {
            $client = [
                'firstname' => 'Jan',
                'lastname' => 'Modaal',
                'email' => $email,
                'address1' => 'Kalverstraat 123',
                'city' => 'Amsterdam',
                'postcode' => '1234 AB',
                'country' => 'NL',
                'phonenumber' => '0123456789',
                'password2' => 'password',
                'clientip' => '1.2.3.4',
            ];
            $result = $this->localApi()->exec('AddClient', $client);
            $clientId = $result['clientid'];
        } else {
            $clientId = $result['clients']['client'][0]['id'];
        }
        return $clientId;
    }

    private function ensureInvoice(int $id): void
    {
        $clientId = $this->ensureClient('jan.modaal@example.com');
        try {
            $this->localApi()->getInvoice($id);
        } catch (Exception) {
            $invoice = [
                'userid' => $clientId,
                'status' => 'Unpaid',
                'sendinvoice' => '1',
                'paymentmethod' => 'mailin',
                'taxrate' => '10.00',
                'date' => '2026-01-01',
                'duedate' => '2026-01-08',
                'itemdescription1' => 'Sample Invoice Item',
                'itemamount1' => '15.95',
                'itemtaxed1' => '0',
                'itemdescription2' => 'Sample Second Invoice Item',
                'itemamount2' => '1.00',
                'itemtaxed2' => '1',
                'autoapplycredit' => '0',
            ];
            $this->localApi()->exec('CreateInvoice', $invoice);
        }
    }

    /**
     * Tests creating an acumulus entry and the getByInvoiceSource() method.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testCreate(): Source
    {
        $acumulusEntryManager = static::getAcumulusEntryManager();
        $this->ensureInvoice(static::testSourceId);
        $source = static::getContainer()->createSource(static::testSourceType, static::testSourceId);
        $now = new DateTimeImmutable();
        self::assertTrue($acumulusEntryManager->save($source, static::testConceptId, null));

        $entry = $acumulusEntryManager->getByInvoiceSource($source);
        self::assertInstanceOf(AcumulusEntry::class, $entry);
        self::assertSame(static::testSourceType, $entry->getSourceType());
        self::assertSame(static::testSourceId, $entry->getSourceId());
        self::assertSame(static::testConceptId, $entry->getConceptId());
        self::assertNull($entry->getEntryId());
        self::assertNull($entry->getToken());
        // Checks that the timezone is correct, 25 s is a large interval but is for when we are debugging.
        self::assertEqualsWithDelta(0, static::getDiffInSeconds($entry->getCreated(), $now), 25);
        $diff = static::getDiffInSeconds($entry->getCreated(), $entry->getUpdated());
        self::assertSame(0, $diff);

        return $source;
    }

    /*
    public function testDelete(): void
    {
    }

    public function testGetByInvoiceSource(): void
    {
    }

    public function testDeleteByEntryId(): void
    {
    }

    public function testGetByEntryId(): void
    {
    }
    */
}
