<?php
/*
 * webtrees - imprint
 *
 * Copyright (C) 2023 Hermann Hartenthaler. All rights reserved.
 *
 * webtrees: online genealogy / web based family history software
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
 * Test: alle Kombinationen von: Adreßzusatz = leer und Straße = leer und Ort = leer
 * README.md: Screenshot des erzeugten Impressums
 * README.md: Aktualisierung des Screenshots des Verwaltungsmenüs
 *
 * tbd later on
 * ==============================================================
 * alle offenen issues aus GitHub
 * Fehlermeldung: Der Parameter "tree" fehlt, wenn die verantwortliche Person leer ist
 * Vorbelegung der verantwortlichen Person aus den Angaben für den ersten Website-Administrator (Vor-, Nachname, E-Mail)
 * Formatierung: Name und Adresse oben auf einer Linie
 * Test der Initialisierung bei Erstverwendung mit erstem Admin (Name und E-Mail)
 * Validierung der Base_URL aktivieren
 * Warum is require_once nötig?
 * E-Mail-Funktion: check if there is one @ inside emailAddress and no blanks; if address is not correct: use it as simple eMail
 * Dokumentation in Deutsch für webtrees-Handbuch fertigstellen und README anpassen (Rückportierung)
 * Code review und Refactoring
 */

/**
 * footer with a link to an "Imprint" page
 */

declare(strict_types=1);

namespace Hartenthaler\Webtrees\Module\Imprint;

use Fisharebest\Localization\Translation;
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

use function str_replace;
use function strtolower;
use function trim;
use function md5;

require_once('src/ContactsList.php');                           // tbd why is this necessary (see autoload.php)?

class ImprintFooterModule extends PrivacyPolicy
                          implements ModuleCustomInterface, ModuleFooterInterface, ModuleConfigInterface {
    use ModuleCustomTrait;
    use ModuleFooterTrait;
    use ModuleConfigTrait;

    /**
     * list of const for module administration
     */
    public const CUSTOM_TITLE       = 'Imprint';
    public const CUSTOM_MODULE      = 'hh_imprint';
    public const CUSTOM_AUTHOR      = 'Hermann Hartenthaler';
    public const CUSTOM_GITHUB_USER = 'hartenthaler';
    public const CUSTOM_WEBSITE     = 'https://github.com/' . self::CUSTOM_GITHUB_USER . '/' . self::CUSTOM_MODULE . '/';
    public const CUSTOM_VERSION     = '2.1.15.0';
    public const CUSTOM_LAST        = 'https://raw.githubusercontent.com/' . self::CUSTOM_GITHUB_USER . '/' .
                                            self::CUSTOM_MODULE . '/main/latest-version.txt';

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
        return /* I18N: Description of this module */ I18N::translate('Imprint as a footer element for this site.');
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
     * Where to get support for this module?  Perhaps a GitHub repository?
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
     *
     * @param string $language
     *
     * @return string[]         // array<string,string>
     */
    public function customTranslations(string $language): array
    {
        $lang_dir   = $this->resourcesFolder() . 'lang/';
        $file       = $lang_dir . $language . '.mo';
        if (file_exists($file)) {
            return (new Translation($file))->asArray();
        } else {
            return [];
        }
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
        $response = $this->getInitializedOptions($request);
        return $this->viewResponse($this->name() . '::' . 'settings', $response);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    private function getInitializedOptions(ServerRequestInterface $request): array
    {
        $response = [];

        $response['title'] = $this->title();
        $response['description'] = $this->description();

        $preferences = $this->listOfPreferences();
        foreach ($preferences as $preference) {
            $response[$preference] = $this->getPreference($preference);
        }
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

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Page',
            'tree'   => $tree?->name(),
        ]);

        return view($this->name() . '::footer', ['url' => $url]);
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

        return $this->viewResponse($this->name() . '::page', [
            'title'                     => $this->title(),
            //'tree'                      => Validator::attributes($request)->tree(),
            'tree'                      => Validator::attributes($request)->treeOptional(),
            'imprintHead1'              => I18N::translate('Responsible person'),
            'imprintHead2'              => I18N::translate('This website is operated by:'),
            'responsibleName'           => $this->responsibleName(),
            'showGravatar'              => $this->showGravatar(),
            'image'                     => $this->getGravatar($this->getPreference('email', ''),'40'),
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
        ]);
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
                    I18N::translate('message via imprint of site %s', $this->getHostName($request));
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
     * get name of this website host
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getHostName(ServerRequestInterface $request): string
    {
        $baseUrl = $request->getAttribute('base_url', '');      //tbd
        //$baseUrl = Validator::attributes($request)->isLocalUrl();          // tbd type is not string
        return str_replace(array('http://','https://'), '', $baseUrl);
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Optional, size in pixels, defaults to 80px [ 1..2048 ]
     * @param string $d Optional, default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $r Optional, maximum rating (inclusive) [ g | pg | r | x ]
     * @param bool   $img Optional, true to return a complete IMG tag, false for just the URL
     * @param array  $atts Optional, additional key/value attributes to include in the IMG tag
     *
     * @return string containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    private function getGravatar( string $email, string $s = '80', string $d = 'mp', string $r = 'g',
                                 bool $img = true,  array $attributes = array() ) : string
    {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $attributes as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
};
