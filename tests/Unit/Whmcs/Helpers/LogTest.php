<?php
/**
 * @noinspection PhpUndefinedMethodInspection
 */

declare(strict_types=1);

namespace Siel\Acumulus\Tests\Unit\Whmcs\Helpers;

use Siel\Acumulus\Helpers\Severity;
use PHPUnit\Framework\TestCase;
use WHMCS\Database\Capsule;

use function sprintf;

use const Siel\Acumulus\Version;

/**
 * EventTest tests the {@see \Siel\Acumulus\Whmcs\Helpers\Log} class.
 */
class LogTest extends TestCase
{
    private static string $msg = 'My Acumulus test log message';

    public static function tearDownAfterClass(): void
    {
        Capsule::table('tblactivitylog')
            ->where('description', 'like', '%' . self::$msg . '%')
            ->delete();
        parent::tearDownAfterClass();
    }

    public function testLog(): void
    {
        $log = acumulus_get()->getAcumulusContainer()->getLog();
        $level = Severity::Info;
        $log->setLogLevel($level);

        $log->info(static::$msg);
        $info = (fn(string $severity) => $this->getSeverityString($severity))->call($log, $level);
        $completeMsg = sprintf('Acumulus %s: %s - %s', Version, $info, static::$msg);
        $record = Capsule::table('tblactivitylog')
            ->orderByDesc('date')
            ->first();
        static::assertSame($completeMsg, $record->description);
    }
}
