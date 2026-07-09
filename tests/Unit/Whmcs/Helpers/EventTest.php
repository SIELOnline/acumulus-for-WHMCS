<?php
/**
 * @noinspection PhpUndefinedMethodInspection
 */

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Unit\Whmcs\Helpers;

use Siel\Acumulus\Whmcs\Helpers\Event;
use PHPUnit\Framework\TestCase;

/**
 * EventTest tests the {@see \Siel\Acumulus\Whmcs\Helpers\Event} class.
 */
class EventTest extends TestCase
{
    public function testGetEventName(): void
    {
        $event = new Event();
        $eventName = (fn($eventName) => $this->getEventName($eventName))->call($event, 'triggerInvoiceCreateBefore');
        static::assertSame('AcumulusInvoiceCreateBefore', $eventName);
    }

    public function testEventListener(): void
    {
        $p1 = 'p1';
        $p2 = 'p2';
        add_hook('AcumulusAcumulusTestEvent', 1, static function (array $args) use ($p1, $p2) {
            EventTest::assertSame($p1, $args['arg1']);
            EventTest::assertSame($p2, $args['arg2']);
        });

        $event = new Event();
        (fn(string $eventName, array $args) => $this->triggerEventByMethodName($eventName, $args))->call(
            $event,
            'triggerAcumulusTestEvent',
            ['arg1' => $p1, 'arg2' => $p2]
        );
    }
}
