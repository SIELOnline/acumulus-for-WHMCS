<?php
/**
 * @noinspection SpellCheckingInspection Lots of spell check hints
 */

declare(strict_types=1);

namespace Siel\Whmcs\Acumulus;

use Siel\Acumulus\Config\Config;
use Siel\Acumulus\Helpers\Form;
use Siel\Acumulus\Helpers\Message;
use Siel\Acumulus\Helpers\Severity;
use Siel\Acumulus\Shop\BatchFormTranslations;
use Siel\Acumulus\Shop\ConfigFormTranslations;
use Siel\Acumulus\Whmcs\Helpers\LocalApiTrait;
use Throwable;

use function count;
use function is_array;
use function sprintf;

/**
 * Acumulus contains WHMCS Addon specific code.
 *
 * @noinspection PhpClassHasTooManyDeclaredMembersInspection
 */
class Acumulus
{
    use LocalApiTrait;

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

    protected function getHelper(): AcumulusHelper
    {
        return $this->acumulusHelper;
    }

    protected function t(string $key): string
    {
        return $this->acumulusHelper->t($key);
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
            $this->getHelper()->getAcumulusContainer()->getAcumulusEntryManager()->install();
            return [
                'status' => 'success',
                'description' => $this->t('install_success'),
            ];
        } catch (Throwable $e) {
            $this->getHelper()->logException($e);
            return [
                'status' => 'error',
                'description' => sprintf($this->t('install_failure'), $e->getMessage()),
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
            $this->getHelper()->getAcumulusContainer()->getAcumulusEntryManager()->uninstall();
            return [
                'status' => 'success',
                'description' => $this->t('uninstall_success'),
            ];
        } catch (Throwable $e) {
            $this->getHelper()->logException($e);
            return [
                'status' => 'error',
                'description' => sprintf($this->t('uninstall_failure'), $e->getMessage()),
            ];
        }
    }

    /**
     * Performs custom actions on upgrading this module.
     */
    public function upgrade(array $vars): void
    {
        // Old version of module.
        $this->getHelper()->getAcumulusContainer()->getConfig()->save([Config::VersionKey => self::Version]);
        $this->getHelper()->logActivity(
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
     * Function to return the configuration form fields for the Acumulus module.
     */
    public function config(): array
    {
        $moduleLink = $this->getHelper()->getAcumulusContainer()->getShopCapabilities()->getLink('settings');
        $moduleName = static::Name;
        /** @noinspection HtmlUnknownTarget */
        $fields = [
            'welcome' => [
                'Description' => sprintf('Please visit our configuration pages at <a href="%s">Modules - %s</a>', $moduleLink, $moduleName),
            ],
        ];
        return [
            'name' => static::Name,
            'version' => static::Version,
            'author' => static::Author,
            'description' => $this->t('desc_module'),
            'fields' => $fields,
        ];
    }

    /**
     * Renders the main screen: the Acumulus send invoice(s) form.
     *
     * @param array $vars
     *   An array with all information that may be needed. It contains:
     *   - '_lang': the contents of the language file of the user's language.
     *   - 'module': name of this module ('acumulus').
     *   - 'modulelink': internal link: part after http(s)://example.com/admin/ to the
     *     page that should be output ('addonmodules.php?module=acumulus').
     *   - 'acumulus': the complete config of this module.
     *   - 'version': version of this module
     *   - 'access': has the curent user access to the module? ('1' = yes)
     *
     * @throws \Throwable
     *
     * @noinspection PhpFunctionCyclomaticComplexityInspection
     * @noinspection PhpUnusedParameterInspection
     */
    public function output(array $vars): void
    {
        $type = $this->getHelper()->getAddOnPageType();
        if (empty($type)) {
            $accountStatus = $this->getHelper()->getAcumulusContainer()->getCheckAccount()->getAccountStatus(false);
            $type = match ($accountStatus) {
                true => 'batch',
                default => 'settings',
            };
        }
        echo $this->processForm($type);
    }

    /**
     * Processes and renders the form of the given type.
     *
     * @param string $type
     *   The form type: 'settings', 'mappings', 'batch' or one of the other forms.
     *
     * @return string
     *   The form HTML to output.
     *
     * @throws \Throwable
     */
    protected function processForm(string $type): string
    {
        $form = $this->getHelper()->getAcumulusContainer()->getForm($type);
        try {
            $form->process();
            $this->preRenderForm($form);
            // Render the form first before wrapping it in its final format so that any
            // messages added during rendering can be shown on top.
            $formOutput = $this->getHelper()->getAcumulusContainer()->getFormRenderer()->render($form);
        } catch (Throwable $e) {
            // We handle our "own" exceptions but only when we can process them
            // as we want, i.e. show it as an error at the beginning of the
            // form. That's why we start catching only after we have a form and
            // stop catching just before postRenderForm().
            try {
                $crashReporter = $this->getHelper()->getAcumulusContainer()->getCrashReporter();
                $message = $crashReporter->logAndMail($e);
                $form->createAndAddMessage($message, Severity::Exception);
            } catch (Throwable) {
                // We don't know if we have informed the user per mail or
                // screen, so assume we didn't and rethrow the original exception.
                throw $e;
            }
        }
        return $this->postRenderForm($form, $formOutput ?? 'ERROR');
    }

    /**
     * Performs form type-specific actions before rendering a form.
     *
     * Think of things like:
     * - Adding CSS and JS.
     * - Setting properties of the {@see \Siel\Acumulus\Helpers\FormRenderer}.
     *
     * @param \Siel\Acumulus\Helpers\Form $form
     *   The form that is going to be rendered.
     */
    private function preRenderForm(Form $form): void
    {
    }

    /**
     * Performs form type-specific actions after a form has been rendered.
     *
     * @param \Siel\Acumulus\Helpers\Form $form
     *   The form that has been rendered.
     * @param string $formOutput
     *   The HTML of the rendered form.
     *
     * @return string
     *   The rendered form with any wrapping around it.
     */
    private function postRenderForm(Form $form, string $formOutput): string
    {
        $output = '';
        $type = $form->getType();
        $id = "acumulus-$type";
        $wait = $this->t('wait');
        $url = $this->getHelper()->getAcumulusContainer()->getShopCapabilities()->getLink($type);

        $output .= $this->renderAcumulusPageHeader($type);
        $output .= $this->showNotices($form);
        switch ($type) {
            case 'register':
            case 'settings':
            case 'mappings':
            case 'activate':
            case 'batch':
            case 'invoice':
                $wrap = $form->isFullPage();
                if ($wrap) {
                    $output .= '<div class="wrap"><form id="' . $id . '" method="post" action="' . $url . '">';
                } else {
                    $output .= "<div id='$id' class='acumulus-area' data-acumulus-wait='$wait'>";
                }
                $output .= $formOutput;
                if ($wrap) {
                    $output .= sprintf('<input class="btn btn-primary" type="submit" value="%s">', $this->t("button_submit_$type"));
                    $output .= '</form></div>';
                } else {
                    $output .= '</div>';
                }
                break;
            case 'rate':
            case 'message':
                $noticeType = $type === 'rate' ? Severity::Success : Severity::Info;
                $attributesList = [
                    'id' => $id,
                    'class' => $this->severityToNoticeClass($noticeType) . ' acumulus acumulus-area',
                    'data-acumulus-wait' => $wait,
                ];
                if ($this->getHelper()->isOwnAddOnAdminPage()) {
                    $attributesList['class'] .= ' inline';
                }
                $attributes = '';
                foreach ($attributesList as $attribute => $value) {
                    $attributes .= " $attribute=\"$value\"";
                }
                $output .= sprintf("<div%s>\n%s\n</div>", $attributes, $formOutput);
                break;
        }

        return $output;
    }

    protected function renderAcumulusPageHeader(mixed $activeType): string
    {
        $accountStatus = $this->getHelper()->getAcumulusContainer()->getCheckAccount()->getAccountStatus(true);
        $shopCapabilities = $this->getHelper()->getAcumulusContainer()->getShopCapabilities();
        $translator = $this->getHelper()->getAcumulusContainer()->getTranslator();
        $buttons = [];
        if ($accountStatus !== true) {
            $translator->add(new ConfigFormTranslations());
            $buttons['register'] = [
                sprintf($this->t('button_link'), $this->t('register_form_link_text'), $shopCapabilities->getLink('register')),
                $this->t('config_form_register'),
            ];
        }
        if ($accountStatus === true) {
            $translator->add(new BatchFormTranslations());
            $buttons['batch'] = [
                sprintf($this->t('button_link'), $this->t('batch_form_link_text'), $shopCapabilities->getLink('batch')),
                $this->t('batch_form_header'),
            ];
        }
        $buttons['settings'] = [
            sprintf($this->t('button_link'), $this->t('settings_form_link_text'), $shopCapabilities->getLink('settings')),
            $this->t('settings_form_description'),
        ];
        $buttons['mappings'] = [
            sprintf($this->t('button_link'), $this->t('mappings_form_link_text'), $shopCapabilities->getLink('mappings')),
            $this->t('mappings_form_description'),
        ];
        $myData = $this->getHelper()->getAcumulusContainer()->getAboutBlockForm()->getMyData($accountStatus);
        if (is_array($myData) && count($myData) > 0) {
            $supportDescription = $this->t('activate_renew');
        } else {
            $supportDescription = $this->t('activate_new');
        }
        $buttons['activate'] = [
            sprintf($this->t('button_link'), $this->t('activate_form_link_text'), $shopCapabilities->getLink('activate')),
            $supportDescription,
        ];
        $output = '<div class="acumulus-page-header">';
        $output .= $this->renderAcumulusPageHeaderButtons($buttons, $activeType);
        $output .= '</div>';
        return $output;
    }

    protected function renderAcumulusPageHeaderButtons(array $buttons, string $activeType): string
    {
        $output = '';
        foreach ($buttons as $type => $texts) {
            $classes = ['acumulus-page-header-button', "acumulus-page-$type"];
            if ($type === $activeType) {
                $classes[] = 'acumulus-form-active';
            }
            $classes = implode(' ', $classes);
            $output .= sprintf('<div class="%s">', $classes);
            $output .= $this->renderAcumulusPageHeaderButton($texts);
            $output .= '</div>';
        }
        return $output;
    }

    protected function renderAcumulusPageHeaderButton(array $texts): string
    {
        return vsprintf('%s<span class="page-description">%s</span>', $texts);
    }

    /**
     * Renders an admin message.
     *
     * Example from WHMCS self:
     * <div class="infobox">
     *     <strong><span class="title">Succesvol doorgevoerd</span></strong><br>
     *     De wijzigingen die u heeft doorgevoerd zijn succesvol opgeslagen in het systeem.
     * </div>
     * @noinspection GrazieInspection
     */
    protected function showNotices(Form $form): string
    {
        return implode("\n", array_map($this->renderNotice(...), $form->getMessages()));
    }

    protected function renderNotice(Message $message): string
    {
        $noticeClass = $this->severityToNoticeClass($message->getSeverity());
        $messageBefore = '';
        $messageAfter = '';
        if ($message->getField() !== '') {
            $messageBefore = sprintf('<label for="%s">', $message->getField());
            $messageAfter = '</label>';
        }
        $text = $message->getText();
        return sprintf('<div class="%s">%s%s%s</div>', $noticeClass, $messageBefore, $text, $messageAfter);
    }

    protected function severityToNoticeClass(int $severity): string
    {
        return match ($severity) {
            Severity::Success => 'successbox',
            Severity::Log,
            Severity::Info,
            Severity::Notice => 'infobox',
            // Warning, Error, Exception
            default => 'errorbox',
        };
    }
}
