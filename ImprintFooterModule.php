<?php
/*
 * webtrees - Legal Notice
 * custom footer with a link to a page of information containing "Legal Notice / Privacy Policy"
 * (in German: "Impressum / Datenschutzerklärung")
 *
 * based on custom footer module of Josef Prause for Czech locale environment, see
 * https://github.com/jpretired/jp-privacy-policy
 *
 * Partly inspired by mp, see:
 * https://www.webtrees.net/index.php/en/forum/help-for-2-0/35233-how-to-edit-the-privacy-policy-and-the-footer#82090
 *
 * Later adopted the MikeT's way of contact the administrator, see:
 * https://www.webtrees.net/index.php/en/forum/help-for-2-0/35233-how-to-edit-the-privacy-policy-and-the-footer#84085
 *
 * Diskussionen mit Peter Schulz und Burkhard Spiegel (inkl Antworten der Datenschutzbeauftragten, Link zur ct)
 *
 * Diskussionen mit anderen
 *
 * webtrees: online genealogy / web based family history software
 * Copyright (C) 2023 Hermann Hartenthaler. All rights reserved.
 * Copyright (C) 2023 webtrees development team.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * tbd for next release
 * ==============================================================
 * Modul in "Legal Notice" umbenennen; dazu Repository auf GitHub umbenennen
 * alle offenen issues aus GitHub
 * Zeilenabstände in page.phtml und settings.phtml über CSS statt Leerzeilen realisieren
 * alle Nutzereingaben mit trim() behandeln
 * Status http/https automatisch ermitteln
 * einheitliche Verwendung von "Copyright" statt "copy right"
 * cookie Warnung einbauen, testen und dokumentieren (falls keine externe Cookie-Managementanwendung verwendet wird)
 * Vorbelegung der verantwortlichen Person aus den Angaben für den ersten Website-Administrator (Vor-, Nachname, E-Mail)
 * READme: Referenzen aus dieser Datei (ganz oben) prüfen und dann übernehmen
 *
 * tbd later on
 * ==============================================================
 * Verwaltungsmenü für Chapters: rekursive hierarchische Gestaltung des Menüs
 * alle restlichen Konstanten aus diesem Modul als Option in das Verwaltungsmenü in den zugehörigen Abschnitt verschieben
 * Auftragsdatenverarbeitung agreement first/last date / time in zwei Elemente zerlegen (Datum dd.mm.yyyy und Zeit hh:mm)
 * alle Texte aus dem alten Modul "Datenschutzerklärung" überarbeiten
 *      entsprechend der handschriftlichen Korrekturen
 *      entsprechend der vorhandenen Vorlagen
 *      in englischer Sprache als übersetzbare Textelemente
 * Validierung "copy right start year" auf "4 digits" und Wert "1970..aktuelles Jahr"
 * Validierung der Base_URL aktivieren
 * Warum ist require_once nötig?
 * E-Mail-Funktion: check if there is one @ inside emailAddress and no blanks; if address is not correct: use it as simple eMail
 * Dokumentation in Deutsch für webtrees-Handbuch fertigstellen und README anpassen (Rückportierung)
 * Code review und Refactoring
 */

/**
 * footer with a link to a "Legal Notice" page
 */

declare(strict_types=1);

namespace Hartenthaler\Webtrees\Module\Imprint;

use Fisharebest\Localization\Translation;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleFooterTrait;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleConfigTrait;
use Fisharebest\Webtrees\Module\PrivacyPolicy;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function trim;
use function file_exists;
use function assert;
use function view;

require_once('src/ContactsList.php');                           // tbd why is this necessary (see autoload.php)?

class ImprintFooterModule extends PrivacyPolicy
                          implements ModuleCustomInterface, ModuleFooterInterface, ModuleConfigInterface {
    use ModuleCustomTrait;
    use ModuleFooterTrait;
    use ModuleConfigTrait;

    /**
     * list of const for module administration
     */
    public const CUSTOM_TITLE       = 'Legal Notice and Privacy Policy';
    public const CUSTOM_MODULE      = 'hh_imprint';             // tbd change "imprint"
    public const CUSTOM_AUTHOR      = 'Hermann Hartenthaler';
    public const GITHUB_USER        = 'hartenthaler';
    public const CUSTOM_WEBSITE     = 'https://github.com/' . self::GITHUB_USER . '/' . self::CUSTOM_MODULE . '/';
    public const CUSTOM_VERSION     = '2.1.15.1';
    public const CUSTOM_LAST        = 'https://raw.githubusercontent.com/' . self::GITHUB_USER . '/' .
                                            self::CUSTOM_MODULE . '/main/latest-version.txt';

    // tbd move the following 3 const to control panel where they can be changed by an administrator

    // used third party services
    private const THIRD_PARTY_SERVICES = [
        'Google charts' => 'https://developers.google.com/',
    ];

    // used tracking and analysis services (beside the services offered by webtrees)
    private const TRACKING_SERVICES = [
        //'ClustrMaps™' => 'https://clustrmaps.com/',
    ];

    // used external cookies services (keyword and URL (including "/" at the end))
    private const COOKIES_SERVICES = [
        //'Usercentrics' => 'https://usercentrics.com/',
    ];

    /** @var ModuleService */
    private ModuleService $moduleService;

    /** @var UserService */
    private UserService $userService;

    /**
     * constructor
     */
    public function __construct() {
        parent::__construct(
            $this->moduleService = new ModuleService(),
            $this->userService = new UserService()
        );
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return /* I18N: Name of this module */ I18N::translate(self::CUSTOM_TITLE);
    }

    /**
     * A sentence describing what this module does. Used in the list of all installed modules.
     *
     * @return string
     */
    public function description(): string
    {
        return /* I18N: Description of this module */ I18N::translate('Legal notice as a footer element for this site.');
    }

    /**
     * The person or organisation who created this module.
     *
     * {@inheritDoc}
     * @see ModuleCustomInterface::customModuleAuthorName()
     */
    public function customModuleAuthorName(): string
    {
        return self::CUSTOM_AUTHOR;
    }

    /**
     * The version of this module.
     *
     * {@inheritDoc}
     * @see ModuleCustomInterface::customModuleVersion()
     *
     * This module uses a system where the version number is equal to the latest stable version of webtrees.
     * Interim versions get an extra sub number.
     *
     * The dev version is always one step above the latest stable version of this module.
     * The subsequent stable version depends on the version number of the latest stable version of webtrees
     *
     */
    public function customModuleVersion(): string
    {
        return self::CUSTOM_VERSION;
    }

    /**
     * A URL that will provide the latest version of this module.
     *
     * @return string
     */
    public function customModuleLatestVersionUrl(): string
    {
        return self::CUSTOM_LAST;
    }

    /**
     * Where to get support for this module?
     *
     * @return string
     */
    public function customModuleSupportUrl(): string
    {
        return self::CUSTOM_WEBSITE;
    }

    /**
     * Where does this module store its resources?
     *
     * @return string
     */
    public function resourcesFolder(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
    }

    /**
     * Additional/updated translations.
     * This module uses the po/mo system. It needs a .mo file.
     *
     * @param string $language
     *
     * @return string[]         // array<string,string>
     */
    public function customTranslations(string $language): array
    {
        $file = $this->resourcesFolder() . 'lang' . DIRECTORY_SEPARATOR . $language . '.mo';
        return file_exists($file) ? (new Translation($file))->asArray() : [];
    }

    /**
     * Bootstrap the module
     *
     * Here is also a good place to register any views (templates) used by the module.
     * This command allows the module to use: view($this->name() . '::', 'fish')
     * to access the file ./resources/views/fish.phtml
     */
    public function boot(): void
    {
        // Register a namespace for our views.
        View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
    }

    /**
     * generate list of preferences (control panel options)
     *
     * @return array<int,string>
     */
    private function listOfPreferences(): array
    {
        return [
            'showCopyRight',
            'copyRightStartYear',
            'copyRightName',
            'responsibleFirst',
            'responsibleSurname',
            'responsibleSex',
            'showGravatar',
            'organization',
            'additionalAddress',
            'street',
            'city',
            'phone',
            'fax',
            'email',
            'simpleEmail',
            'vatNumberLabel',
            'vatNumber',
            'showTreeContacts',
            'showAdministrators',
            'hostingCountry',
            'hostingCompanyName',
            'hostingCompanyUrl',
            'hostingPrivacyNotice',
            'hostingStartDate',
            'hostingEndDate',
        ];
    }

    /**
     * Open control panel page with options
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';
        return $this->viewResponse($this->name() . '::' . 'settings', $this->getInitializedOptions($request));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    private function getInitializedOptions(ServerRequestInterface $request): array
    {
        $response = [];

        $response['title'] = $this->moduleTitle();
        $response['description'] = $this->description();

        $preferences = $this->listOfPreferences();
        foreach ($preferences as $preference) {
            $response[$preference] = $this->getPreference($preference);
        }

        $response['chapters'] = $this->getChapters();
/*
        if ($response['responsibleFirst'] == '' && $response['responsibleSurname'] == '') {
            $contactsListObject = new ContactsList($this->userService, $request);
            // there is always at least 1 administrator; use this first one to initialize the responsible person
            $defaultAdministrator = $contactsListObject->getAdministratorsList()[0];
            $response['responsibleFirst'] = $defaultAdministrator->realName;     // tbd split in first name and surname
            $response['responsibleSurname'] = $defaultAdministrator->realName;   // tbd split in first name and surname

            if ($response['email'] == '') {
                $response['email'] = $defaultAdministrator->email;
            }
        }
*/
        if ($response['responsibleSex'] == '') {
            $response['responsibleSex'] = 'U';
        }
        return $response;
    }

    /**
     * Save the user preferences in the database
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        //$params = (array) $request->getParsedBody();
        //if ($params['save'] === '1') {
        if (Validator::parsedBody($request)->string('save') === '1') {
            $this->postAdminActionSave($request);
            FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.',
                $this->title()), 'success');
        }
        return redirect($this->getConfigLink());
    }

    /**
     * Save the user preferences for all parameters
     *
     * @param ServerRequestInterface $request
     */
    private function postAdminActionSave(ServerRequestInterface $request)
    {
        $preferences = $this->listOfPreferences();
        foreach ($preferences as $preference) {
            $this->setPreference($preference, Validator::parsedBody($request)->string($preference));
        }
        $this->postAdminActionChapter($request);
    }

    /**
     * save the user preferences for all parameters related to the chapters of this module in the database
     *
     * @param ServerRequestInterface $request
     */
    private function postAdminActionChapter(ServerRequestInterface $request)
    {
        $params = (array) $request->getParsedBody();                    // tbd use Validator
        $order = implode(",", $params['order']);
        $this->setPreference('order', $order);
        foreach (LegalNoticeSupport::listChapterKeys() as $chapterKey) {
            $this->setPreference('status-' . $chapterKey, '0');
        }
        foreach ($params as $key => $value) {
            if (str_starts_with($key, 'status-')) {
                $this->setPreference($key, $value);
            }
        }
    }

    /**
     * A footer, to be added at the bottom of every page.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getFooter(ServerRequestInterface $request): string
    {
        $tree = Validator::attributes($request)->treeOptional();
        if ($tree === null) {
            return '';
        }

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Page',
            'tree'   => $tree?->name(),
        ]);

        $user = $request->getAttribute('user');
        assert($user instanceof UserInterface);

        $title = I18N::translate('Legal Notice');
        if ($this->isChapterEnabled('DataProtection')) {
            $title .= ' ' . I18N::translate('and Privacy Policy');
        }

        return view($this->name() . '::footer', [
            'title'              => $title,
            'url'                => $url,
            'showCopyRight'      => $this->showCopyRight(),
            'copyRightStartYear' => $this->copyRightStartYear(),
            'copyRightName'      => $this->copyRightName(),
            'cookiesWarning'     => LegalNoticeSupport::useBuildInCookiesWarning(
                $tree, $user, self::TRACKING_SERVICES, self::COOKIES_SERVICES),
            'cookiesMessage'     => I18N::translate('This website uses cookies.'),
        ]);
    }

    /**
     * Generate the page that will be shown when a user clicks the link in the footer.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getPageAction(ServerRequestInterface $request): ResponseInterface
    {
        $contactsListObject = new ContactsList($this->userService, $request);
        if ($this->showTreeContacts()) {
            $contactsTreeContacts = $contactsListObject->getTreeContactsList();
        } else {
            $contactsTreeContacts = [];
        }
        if ($this->showAdministrators()) {
            foreach ($contactsListObject->getAdministratorsList() as $admin) {
                $contactsAdministrators[] = $admin->contact;
            }
        } else {
            $contactsAdministrators = [];
        }

        $tree = Validator::attributes($request)->treeOptional();
        $user = $request->getAttribute('user');             // tbd: replace by Validator::attributes($request)->user()
        assert($user instanceof UserInterface);
        $singular = count($contactsAdministrators) == 1;
        $https = true;                                                            // tbd: automatisch ermitteln

        return $this->viewResponse($this->name() . '::page', [
            'title'                     => $this->title(),
            'tree'                      => $tree,
            'legalNoticeTitle'          => I18N::translate('Legal Notice'),
            'legalNoticeHead1'          => I18N::translate('Responsible person'),
            'legalNoticeHead2'          => I18N::translate('This website is operated by:'),
            'responsibleName'           => $this->responsibleName(),
            'showGravatar'              => $this->showGravatar(),
            'image'                     => LegalNoticeSupport::getGravatar($this->getPreference('email', ''),'40'),
            'organization'              => $this->organization(),
            'representedBy'             => I18N::translate('Represented by:'),
            'additionalAddress'         => $this->additionalAddress(),
            'street'                    => $this->street(),
            'city'                      => $this->city(),
            'phoneLabel'                => I18N::translate('Phone'),
            'phone'                     => $this->phone(),
            'faxLabel'                  => I18N::translate('Fax'),
            'fax'                       => $this->fax(),
            'emailLabel'                => I18N::translate('eMail'),
            'email'                     => $this->email($request),
            'vatNumberLabel'            => $this->vatNumberLabel(),
            'vatNumber'                 => $this->vatNumber(),
            'showTreeContacts'          => $this->showTreeContacts(),
            'headTreeContacts'          => I18N::plural('Additional contact','Additional contacts', count($contactsTreeContacts)),
            'countTreeContacts'         => count($contactsTreeContacts),
            'contactsTreeContacts'      => $contactsTreeContacts,
            'showAdministrators'        => $this->showAdministrators(),
            'headAdministrators'        => I18N::plural('Website administrator','Website administrators', count($contactsAdministrators)),
            'commentAdministrators'     => I18N::plural('The webtrees administrator is responsible to manage users and to set the preferences for this website.',
                                            'The webtrees administrators are responsible to manage users and to set the preferences for this website.', count($contactsAdministrators)),
            'countAdministrators'       => count($contactsAdministrators),
            'contactsAdministrators'    => $contactsAdministrators,
            'chapters'                  => $this->getChapters(),
            'showDataProtection'        => $this->isChapterEnabled('DataProtection'),                                            // tbd
            'showLegalRegulations'      => $this->isChapterEnabled('LegalRegulations'),                                          // tbd
            'singular'                  => $singular,
            'https'                     => $https,
            'analytics'                 => $this->analyticsModules($tree, $user),
            'trackingServices'          => self::TRACKING_SERVICES,
            'thirdPartyServices'        => self::THIRD_PARTY_SERVICES,
            'cookiesServices'           => self::COOKIES_SERVICES,
            //'usercentricsLanguages' => self::USERCENTRICS_LANGUAGES,
            'hostingDomain'             => LegalNoticeSupport::getHostName($request),
            'hostingCountry'            => I18N::translate($this->hostingCountry()),
            'hostingCompanyName'        => $this->hostingCompanyName(),
            'hostingCompanyUrl'         => $this->hostingCompanyUrl(),
            'hostingPrivacyNotice'      => $this->hostingPrivacyNotice(),
            'hostingStartDate'          => $this->hostingStartDate(),
            'hostingEndDate'            => $this->hostingEndDate(),
        ]);
    }

    /**
     * title of this module used in the headings
     *
     * @return string
     */
    private function moduleTitle(): string
    {
        return I18N::translate('Legal Notice and Privacy Policy');;
    }

    /**
     * should a copy right notice be shown?
     *
     * @return bool
     */
    private function showCopyRight(): bool
    {
        return ($this->getPreference('showCopyRight', '0') !== '0');
    }

    /**
     * show copy right start year               // tbd check "4 digits" and value "1970..aktuelles Jahr"
     *
     * @return string
     */
    private function copyRightStartYear(): string
    {
        return $this->getPreference('copyRightStartYear', '');
    }

    /**
     * name of holder of copy right
     *
     * @return string
     */
    private function copyRightName(): string
    {
        return $this->getPreference('copyRightName', '');
    }

    /**
     * first name(s) of responsible person
     *
     * @return string
     */
    private function responsibleFirst(): string
    {
        return $this->getPreference('responsibleFirst', '');
    }

    /**
     * surname(s) of responsible person
     *
     * @return string
     */
    private function responsibleSurname(): string
    {
        return $this->getPreference('responsibleSurname', '');
    }

    /**
     * name of responsible person, assuming that the sequence is first name and then last name
     *
     * @return string
     */
    private function responsibleName(): string
    {
        return trim($this->responsibleFirst() . ' ' . $this->responsibleSurname());
    }

    /**
     * sex of responsible person
     *
     * @return string
     */
    private function responsibleSex(): string
    {
        return $this->getPreference('responsibleSex', 'M');
    }

    /**
     * should a gravatar image be shown?
     *
     * @return bool
     */
    private function showGravatar(): bool
    {
        return ($this->getPreference('showGravatar', '0') !== '0');
    }

    /**
     * organization
     *
     * @return string
     */
    private function organization(): string
    {
        return $this->getPreference('organization', '');
    }

    /**
     * additional address line
     *
     * @return string
     */
    private function additionalAddress(): string
    {
        return $this->getPreference('additionalAddress', '');
    }

    /**
     * street name and house number of responsible person
     *
     * @return string
     */
    private function street(): string
    {
        return $this->getPreference('street', '');
    }

    /**
     * city and zip code of responsible person
     *
     * @return string
     */
    private function city(): string
    {
        return $this->getPreference('city', '');
    }

    /**
     * phone number of responsible person
     *
     * @return string
     */
    private function phone(): string
    {
        return $this->getPreference('phone', '');
    }

    /**
     * fax number of responsible person
     *
     * @return string
     */
    private function fax(): string
    {
        return $this->getPreference('fax', '');
    }

    /**
     * E-Mail address of responsible person in two versions
     * - only eMail address
     * - eMail address and additionally parameter for subject and first line of body
     *
     * @param ServerRequestInterface $request
     * @param bool $simpleEmail Optional, use true to force a simple Email address without subject and body
     *
     * @return string
     */
    private function email(ServerRequestInterface $request, bool $simpleEmail = false): string
    {
        $emailAddress = $this->getPreference('email', '');

        if ($emailAddress !== '') {
            if ($simpleEmail || $this->simpleEmail()) {
                $emailLink = '<a href="mailto:' .
                    e($emailAddress) .
                    '" style="background-color:transparent;color:rgb(85,85,85);text-decoration:none;">' .
                    e($emailAddress) .
                    '</a>';
            } else {
                $subject = /* I18N: subject of e-mail */
                    I18N::translate('message via legal notice of site %s', LegalNoticeSupport::getHostName($request));
                if ($this->responsibleSex() == 'M') {
                    $body = /* I18N: first line of body of e-mail using surname */
                        I18N::translate('Dear Mr. %s', $this->responsibleSurname()) . ',';
                } elseif ($this->responsibleSex() == 'F') {
                    $body = /* I18N: first line of body of e-mail using surname */
                        I18N::translate('Dear Mrs. %s', $this->responsibleSurname()) . ',';
                } else {
                    $body = /* I18N: first line of body of e-mail using full name */
                        I18N::translate('Dear %s', $this->responsibleName()) . ',';
                }

                $emailLink = '<a href="mailto:' .
                    e($emailAddress) .
                    '?subject=' .
                    e($subject) .
                    '&amp;body=' .
                    e($body) .
                    '" style="background-color:transparent;color:rgb(85,85,85);text-decoration:none;">' .
                    e($emailAddress) .
                    '</a>';
            }
            return $emailLink;
        } else {
            return '';
        }
    }

    /**
     * should a simple or a more complex E-Mail address of the responsible person be used?
     *
     * @return bool
     */
    private function simpleEmail(): bool
    {
        return ($this->getPreference('simpleEmail', '0') !== '0');
    }

    /**
     * Label for VAT number or other registration number
     *
     * @return string
     */
    private function vatNumberLabel(): string
    {
        return $this->getPreference('vatNumberLabel', '');
    }

    /**
     * VAT number or other registration number
     *
     * @return string
     */
    private function vatNumber(): string
    {
        return $this->getPreference('vatNumber', '');
    }

    /**
     * should the tree contacts be shown
     *
     * @return bool
     */
    private function showTreeContacts(): bool
    {
        return ($this->getPreference('showTreeContacts', '0') !== '0');
    }

    /**
     * should the tree contacts be shown
     *
     * @return bool
     */
    private function showAdministrators(): bool
    {
        return ($this->getPreference('showAdministrators', '0') !== '0');
    }

    /**
     * country of the webtrees server location (in English language)
     * e.g. Germany
     *
     * @return string
     */
    private function hostingCountry(): string
    {
        return $this->getPreference('hostingCountry', '');
    }

    /**
     * hosting service company name
     * e.g. Strato
     *
     * @return string
     */
    private function hostingCompanyName(): string
    {
        return $this->getPreference('hostingCompanyName', '');
    }

    /**
     * hosting service link (URL)
     * e.g. https://strato.de
     *
     * @return string
     */
    private function hostingCompanyUrl(): string
    {
        return $this->getPreference('hostingCompanyUrl', '');
    }

    /**
     * hosting service link to privacy notice (URL)
     * e.g. https://www.strato.de/datenschutz
     *
     * @return string
     */
    private function hostingPrivacyNotice(): string
    {
        return $this->getPreference('hostingPrivacyNotice', '');
    }

    /**
     * hosting Auftragsverarbeitung agreement first date / time             // tbd should be 2 elements date + time
     * e.g. 26.11.2018 um 00:12 Uhr
     *
     * @return string
     */
    private function hostingStartDate(): string
    {
        return $this->getPreference('hostingStartDate', '');
    }

    /**
     * hosting Auftragsverarbeitung agreement last date / time             // tbd should be 2 elements date + time
     * e.g. 25.11.2022 um 00:33 Uhr
     *
     * @return string
     */
    private function hostingEndDate(): string
    {
        return $this->getPreference('hostingEndDate', '');
    }

    /**
     * ordered chapters
     * set default values in case the settings are not stored in the database yet
     *
     * @return array<object> of ordered objects
     */
    private function getChapters(): array
    {
        $listChapters = LegalNoticeSupport::listChapterKeys();
        $orderDefault = implode(',', $listChapters);
        $order = explode(',', $this->getPreference('order', $orderDefault));

        if (count($listChapters) > count($order)) {
            $this->addChapters($listChapters, $order);
        }

        $chaptersList = [];
        foreach ($order as $chapterKey) {
            $parameter = LegalNoticeSupport::getChapterParameters()[$chapterKey];
            $chaptersList[] = new Chapter(
                                $chapterKey,
                                $parameter['id'],
                                $parameter['heading'],
                                $parameter['level'],
                                $parameter['link'],
                                $this->getPreference('status-' . $chapterKey, 'on') == 'on',
                                ''
            );
        }
        return $chaptersList;
    }

    /**
     * add chapters, which are newly defined
     * tbd: it is not possible to delete chapters, only add new ones
     *
     * @param array $listChapters list of chapters defined by this module
     * @param array $order list of ordered chapters out of parameters
     */
    private function addChapters(array $listChapters, array &$order)
    {

        foreach ($listChapters as $chapter) {
            if (!in_array($chapter, $order)) {
                $order[] = $chapter;                 // add new chapters at the end of the list
            }
        }
    }

    /**
     * is a specific chapter enabled?
     *
     * @param string $chapterKey
     * @return bool
     */
    private function isChapterEnabled(string $chapterKey): bool
    {
        $enabled = false;
        $chapters = $this->getChapters();
        foreach ($chapters as $chapter) {
            if ($chapter->getKey() == $chapterKey) {
                $enabled = $chapter->getEnabled();
            }
        }
        return $enabled;
    }
};
