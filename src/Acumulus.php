<?php
/**
 * @noinspection SpellCheckingInspection Lots of spell check hints
 */

declare(strict_types=1);

namespace Siel\Whmcs\Acumulus;

use Siel\Acumulus\Config\Config;
use Throwable;

use function sprintf;

/**
 * Acumulus contains WHMCS Addon specific code.
 *
 * @noinspection PhpClassHasTooManyDeclaredMembersInspection
 */
class Acumulus
{

    public const Name = 'Acumulus';
    public const Version = '4.0';
    public const Author = 'Buro RaDer (commissioned by SIEL)';

    private static ?Acumulus $instance = null;

    /**
     * Returns the singleton instance.
     */
    public static function getInstance(): static
    {
        return static::$instance ??= new static();
    }

    protected AcumulusHelper $acumulusHelper;

    private function __construct()
    {
        $this->acumulusHelper = new AcumulusHelper();
    }

    public function getName(): string
    {
        return self::Name;
    }

    public function getVersion(): string
    {
        return self::Version;
    }

    /**
     * Performs custom actions on activating this module.
     * - Create DB table
     *
     * @return string[]
     *   Result of the activation: 2 strings keyed by 'status''and 'description'.
     */
    public function activate(): array
    {
        try {
            $this->acumulusHelper->getAcumulusContainer()->getAcumulusEntryManager()->install();
            return [
                'status' => 'success',
                'description' => $this->acumulusHelper->t('install_success'),
            ];
        } catch (Throwable $e) {
            $this->acumulusHelper->logException($e);
            return [
                'status' => 'error',
                'description' => sprintf($this->acumulusHelper->t('install_failure'), $e->getMessage()),
            ];
        }
    }

    /**
     * Performs custom actions on deactivating this module.
     *
     * - Remove Custom DB Table
     *
     * @return string[]
     *   Result of the deactivation: 2 strings keyed by 'status''and 'description'.
     *
     * @todo
     *   Should we always drop the table or can we ask for confirmation?
     *
     */
    public function deactivate(): array
    {
        try {
            $this->acumulusHelper->getAcumulusContainer()->getAcumulusEntryManager()->uninstall();
            return [
                'status' => 'success',
                'description' => $this->acumulusHelper->t('uninstall_success'),
            ];
        } catch (Throwable $e) {
            $this->acumulusHelper->logException($e);
            return [
                'status' => 'error',
                'description' => sprintf($this->acumulusHelper->t('uninstall_failure'), $e->getMessage()),
            ];
        }
    }

    /**
     * Performs custom actions on upgrading this module.
     */
    public function upgrade(array $vars): void
    {
        // Old version of module.
        $this->acumulusHelper->getAcumulusContainer()->getConfig()->save([Config::VersionKey => self::Version]);
        $this->acumulusHelper->logActivity(
            sprintf(
                '%1$s: The Acumulus module has been upgraded successfully from version %2$s to %3$s',
                __FUNCTION__,
                $vars['version'],
                self::Version
            )
        );
    }

    /*
     * Module Additional functions. These functions are called by WHMCS based on
     * naming patterns.
     */
    /**
     * Return HTML to add to the sidebar.
     */
    public function sidebar(array $vars): string
    {
        $moduleLink = $vars['moduleLink'];
        return '';
    }

    /**
     * Function to return the configuration form fields for the Acumulus module.
     */
    public function config(): array
    {
        $config = [
            'name' => static::Name,
            'version' => static::Version,
            'author' => static::Author,
            'description' => $this->acumulusHelper->t('desc_module'),
            'fields' => [],
        ];
        return $config;
    }

    /**
     * Renders the main screen: the Acumulus send invoice(s) form.
     *
     * @param array $vars
     *   An array with all information that may be needed. It contains:
     *   - '_lang': the contents of the language file of the user's language.
     *   - 'module': name of this module (acumulus).
     *   - 'modulelink': internal link: part after http(s)://example.com/admin/ to the
     *     page that should be output (addonmodules.php?module=acumulus).
     *   - 'version': version of this module
     *   - 'acumulus_...': the complete config of this module.
     *
     * @noinspection PhpFunctionCyclomaticComplexityInspection
     */
    public function output(array $vars): void
    {
        $moduleLink = $vars['moduleLink'];
        $action = $vars['action'] ?? 'batch';
        echo $this->processForm($action);
    }

    protected function processForm(string $action): string
    {
        $output = '';
        // @todo.
        return $output;
    }
}
