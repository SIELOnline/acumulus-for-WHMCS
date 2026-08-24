<?php

declare(strict_types=1);

namespace Siel\Whmcs\Acumulus;

use Siel\Acumulus\Config\Config;
use Siel\Acumulus\Helpers\Container;
use Siel\Acumulus\Helpers\Log;
use Siel\Acumulus\Helpers\Severity;
use Siel\Acumulus\Whmcs\Helpers\LocalApiTrait;
use Throwable;
use WHMCS\App;
use WHMCS\Application;
use WHMCS\Config\Setting;

use function in_array;

/**
 * AcumulusHelper defines helper methods to get access to the Acumulus library.
 */
class AcumulusHelper
{
    use LocalApiTrait;

    private static Container $acumulusContainer;

    /**
     * Returns an Acumulus container.
     *
     * Perhaps, more importantly - especially for 3rd parties that want to use
     * features from libAcumulus - the 1st time it is called, it ensures that
     * libAcumulus autoloading is defined and that a Container with the correct
     * settings is created.
     *
     * So, for 3rd parties, this is the correct way to access the libAcumulus:
     * <code>Acumulus::create()->getAcumulusContainer()</code>
     */
    public function getAcumulusContainer(): Container
    {
        $this->init();
        return self::$acumulusContainer;
    }

    /**
     * Loads our library and creates the Container.
     *
     * This method gets called by {@see \Acumulus::getAcumulusContainer()} and most of the
     * time that is the right moment. However, in some actions, class constants from our
     * library are used before the container is retrieved, and in those cases, that action
     * method should call init() itself.
     */
    private function init(): void
    {
        if (!isset(self::$acumulusContainer)) {
            $shopNameSpace = 'Whmcs';
            $language = $_SESSION['Language'] ?? $_SESSION['adminlang'] ?? Setting::getValue('Language');
            if (!in_array($language, ['en', 'nl'])) {
                // Unsupported language: fall back to English.
                $language = 'en';
            }
            self::$acumulusContainer = new Container($shopNameSpace, $language);
            // Start with a high log level, will be corrected when the config is
            // loaded.
            self::$acumulusContainer->getLog()->setLogLevel(Severity::Log);
        }
    }

    /**
     * Helper method to translate strings.
     *
     * @param string $key
     *  The key to get a translation for.
     *
     * @return string
     *   The translation for the given key or the key itself if no translation
     *   could be found.
     */
    public function t(string $key): string
    {
        return $this->getAcumulusContainer()->getTranslator()->get($key);
    }

    public function getAcumulusConfig(): Config
    {
        return $this->getAcumulusContainer()->getConfig();
    }

    public function getLog(): Log
    {
        return $this->getAcumulusContainer()->getLog();
    }

    /**
     * Description.
     *
     * @param string $message
     *   The message to log, optionally followed by arguments. If there are arguments,
     *   the $message is passed through {@see vsprintf()}.
     * @param mixed ...$values
     *   Any values to replace %-placeholders in $message.
     */
    public function logActivity(string $message, ...$values): void
    {
        $this->getLog()->info($message, ...$values);
    }

    public function logException(Throwable $e, bool $includeTrace = true): void
    {
        $this->getLog()->exception($e, $includeTrace);
    }

    /**
     * Returns whether we are on our own addon page (rendered with acumulus_output()).
     */
    public function isOwnAddOnAdminPage(string $addOnName = 'acumulus'): bool
    {
        /** @var Application $whmcs */
        global $whmcs;
        return $whmcs->getCurrentFilename() === 'addonmodules' && $whmcs->get_req_var('module') === $addOnName;
    }

    /**
     * Returns the page parameter passed to our own addon page.
     */
    public function getAddOnPageType(): string
    {
        /** @var Application $whmcs */
        global $whmcs;
        return $whmcs->get_req_var('page');
    }
}
