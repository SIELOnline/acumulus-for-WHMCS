<?php

declare(strict_types=1);

namespace Whmcs\Mail;

use Siel\Acumulus\Tests\Whmcs\TestCase;
use Siel\Acumulus\Whmcs\Mail\Mailer;

/**
 * MailerTest test the WHMCS {@see Mailer} class.
 */
class MailerTest extends TestCase
{
    public function testSend(): void
    {
        $this->_testMailer(hasTextPart: false);
    }
}
