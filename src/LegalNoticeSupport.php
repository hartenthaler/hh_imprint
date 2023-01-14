<?php
/*
 * webtrees - extended family part
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

declare(strict_types=1);

namespace Hartenthaler\Webtrees\Module\LegalNotice;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

// string functions
use function str_replace;
use function str_starts_with;
use function strtolower;
use function trim;
use function md5;

/**
 * class LegalNoticeSupport
 *
 * static support methods for legal notice module
 */
class LegalNoticeSupport
{
    /**
     * get URL of this website host
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public static function getURL(ServerRequestInterface $request): string
    {
        $baseUrl = $request->getAttribute('base_url', '');      //tbd
        //$baseUrl = Validator::attributes($request)->isLocalUrl();          // tbd type is not string
        return $baseUrl;
    }

    /**
     * get name of this website host
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public static function getHostName(ServerRequestInterface $request): string
    {
        return str_replace(array('http://','https://'), '', self::getURL($request));
    }

    /**
     * get status of https/http
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public static function getHttps(ServerRequestInterface $request): bool
    {
        return str_starts_with(self::getURL($request), 'https://');
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Optional, size in pixels, defaults to 80px [ 1..2048 ]
     * @param string $d Optional, default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $r Optional, maximum rating (inclusive) [ g | pg | r | x ]
     * @param bool   $img Optional, true to return a complete IMG tag, false for just the URL
     * @param array  $attributes Optional, additional key/value attributes to include in the IMG tag
     *
     * @return string containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public static function getGravatar( string $email, string $s = '80', string $d = 'mp', string $r = 'g',
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

    /**
     * tbd Parameter deutlich vereinfachen und $this eliminieren
     *
     * should the build-in cookies warning be used?
     * Don't use it if there is an external cookies warning system used.
     * Use it if there is a webtrees analytics modules activated or
     * if there is an additional tracking service configured in this module.
     *
     * @param Tree          $tree
     * @param UserInterface $user
     * @param array         $trackingServices
     * @param array         $cookiesServices
     *
     * @return bool
     */
    public static function useBuildInCookiesWarning(Tree $tree, UserInterface $user, array $trackingServices, array $cookiesServices): bool
    {
        //return (count($cookiesServices) == 0) && ($this->analyticsModules($tree, $user)->isNotEmpty() || count($trackingServices) > 0);
        return false;           // tbd
    }

    /**
     * get parameters for the used chapters
     *
     * level: int (1=top level)
     * id: int (unique id)                                      // tbd kann vielleicht entfallen
     * link: int (link to id of next higher level)
     * heading: string (translated chapter heading)
     *
     * @return array<string,array<string,int|string>>
     */
    public static function getChapterParameters(): array    // new elements can be added and renamed, but not changed or deleted
        // keys (= names of elements) have to be shorter than 25 characters
        // this sequence is the default order of chapters
    {
        return [
            'DataProtection'        => ['level' => 1, 'id' =>  1, 'link' => 0, 'heading' => I18N::translateContext('heading','Data protection')],
            'Purpose'               => ['level' => 2, 'id' =>  2, 'link' => 1, 'heading' => I18N::translateContext('heading','Purpose')],
            'Privacy'               => ['level' => 2, 'id' =>  3, 'link' => 1, 'heading' => I18N::translateContext('heading','Privacy')],
            'PersonalData'          => ['level' => 2, 'id' =>  4, 'link' => 1, 'heading' => I18N::translateContext('heading','Processing of personal data')],
            'GDPR'                  => ['level' => 2, 'id' =>  5, 'link' => 1, 'heading' => I18N::translateContext('heading','GDPR in connection with online genealogies')],
            'ProvidingInformation'  => ['level' => 2, 'id' =>  6, 'link' => 1, 'heading' => I18N::translateContext('heading','Right of providing information')],
            'CorrectionDeletion'    => ['level' => 2, 'id' =>  7, 'link' => 1, 'heading' => I18N::translateContext('heading','Right to correction or deletion of personal data')],
            'Appeal'                => ['level' => 2, 'id' =>  8, 'link' => 1, 'heading' => I18N::translateContext('heading','Right of appeal')],
            'LegalRegulations'      => ['level' => 1, 'id' =>  9, 'link' => 0, 'heading' => I18N::translateContext('heading','Legal regulations')],
            'LiabilityContent'      => ['level' => 2, 'id' => 10, 'link' => 9, 'heading' => I18N::translateContext('heading','Liability for the content of these websites')],
            'LiabilityLinks'        => ['level' => 2, 'id' => 11, 'link' => 9, 'heading' => I18N::translateContext('heading','Liability for links')],
            'Copyright'             => ['level' => 2, 'id' => 12, 'link' => 9, 'heading' => I18N::translateContext('heading','Copyright and distribution of genealogical data')],
            'UseDataLegalNotice'    => ['level' => 2, 'id' => 13, 'link' => 9, 'heading' => I18N::translateContext('heading','Use of the address data in the Legal Notice')],
            'MitigateDamages'       => ['level' => 2, 'id' => 14, 'link' => 9, 'heading' => I18N::translateContext('heading','Duty to mitigate damages')],
            'OpenSourceLicense'     => ['level' => 2, 'id' => 15, 'link' => 9, 'heading' => I18N::translateContext('heading','Open-source and License')],
            'SeverabilityClause'    => ['level' => 2, 'id' => 16, 'link' => 9, 'heading' => I18N::translateContext('heading','Severability Clause')],
        ];
    }

    /**
     * get content for the chapters
     *
     * contentIWe: bool (true if I and We style is used)
     * content:    array of string (list of paragraphs)
     * contentI:   array of string (list of paragraphs)
     * contentWe:  array of string (list of paragraphs)
     *
     * @param string $hostingDomain     domain name of this website
     * @param string $hostingCountry    translated name of country the server belongs to
     * @return array<string,array<string,int|string>>
     */
    public static function getChapterContent(string $hostingDomain, string $hostingCountry): array
    {
        return [
            'DataProtection'        => ['contentIWe' => false, 'content'   => []],
            'Purpose'               => ['contentIWe' => false, 'content'   => []],
            'Privacy'               => ['contentIWe' => false, 'content'   => []],
            'PersonalData'          => ['contentIWe' => false, 'content'   => []],
            'GDPR'                  => ['contentIWe' => false, 'content'   => []],
            'ProvidingInformation'  => ['contentIWe' => false, 'content'   => []],
            'CorrectionDeletion'    => ['contentIWe' => false, 'content'   => []],
            'Appeal'                => ['contentIWe' => false, 'content'   => []],
            'LegalRegulations'      => ['contentIWe' => false, 'content'   => []],
            'LiabilityContent'      => ['contentIWe' => true,  'contentI'  => [I18N::translate('The contents of my pages were created with great care. However, I cannot guarantee that the content is correct, complete or up-to-date. I am responsible for my own content on these pages according to general laws; However, I am not obliged to monitor transmitted or stored third-party information or to investigate circumstances that indicate illegal activity. An obligation to remove or block the use of information according to general laws remains unaffected. However, liability in this regard is only possible from the point in time at which knowledge of a specific infringement of the law is known. As soon as I become aware of any legal violations, I will remove this content immediately.'),
                                                                               I18N::translate('If you, as a user of this site, are entitled to create content yourself or upload files, you are obliged to comply with all legal requirements and to protect personal data by using the mechanisms of webtrees. If in doubt, please feel free to contact me.')],
                                                               'contentWe' => [I18N::translate('The contents of our pages were created with great care. However, we cannot guarantee that the content is correct, complete or up-to-date. We are responsible for our own content on these pages according to general laws; however, we are not obliged to monitor transmitted or stored third-party information or to investigate circumstances that indicate illegal activity. An obligation to remove or block the use of information according to general laws remains unaffected. However, liability in this regard is only possible from the point in time at which knowledge of a specific infringement of the law is known. As soon as we become aware of any legal violations, we will remove this content immediately.'),
                                                                               I18N::translate('If you, as a user of this site, are entitled to create content yourself or upload files, you are obliged to comply with all legal requirements and to protect personal data by using the mechanisms of webtrees. If in doubt, please feel free to contact us.')]],
            'LiabilityLinks'        => ['contentIWe' => true,  'contentI'  => [I18N::translate('This web application contains links to external websites over which I have no control. Therefore I cannot assume any liability for this external content. The respective provider or operator of the pages is always responsible for the content of the linked pages. The linked pages were checked for possible legal violations at the time of linking. Illegal content was not recognizable at the time of linking. However, a permanent control of the content of the linked pages is not reasonable without concrete evidence of an infringement. As soon as I become aware of legal violations, I will remove such links immediately.')],
                                                               'contentWe' => [I18N::translate('This web application contains links to external websites over which we have no control. Therefore we cannot assume any liability for this external content. The respective provider or operator of the pages is always responsible for the content of the linked pages. The linked pages were checked for possible legal violations at the time of linking. Illegal content was not recognizable at the time of linking. However, a permanent control of the content of the linked pages is not reasonable without concrete evidence of an infringement. As soon as we become aware of legal violations, we will remove such links immediately.')]],
            'Copyright'             => ['contentIWe' => true,  'contentI'  => [I18N::translate('I always endeavor to observe the copyrights of others or to use self-created and license-free works. Third-party contributions to this website are marked as such by naming a source if they cannot be used freely. Should you nevertheless become aware of a copyright infringement, please inform me accordingly. As soon as I become aware of legal violations, I will remove such content immediately.'),
                                                                              ($hostingCountry == '' ?
                                                                               I18N::translate('The content and works created by me and the registered users on this website are subject to copyright. If you contribute content as a registered user of this website, you transfer all rights to it to me. The distribution and any kind of exploitation of the content of this website outside the limits of copyright require my written consent. Commercial use of the information provided here is generally prohibited.')
                                                                              :
                                                                               I18N::translate('The content and works created by me and the registered users on this website are subject to copyright in %s. If you contribute content as a registered user of this website, you transfer all rights to it to me. The distribution and any kind of exploitation of the content of this website outside the limits of copyright require my written consent. Commercial use of the information provided here is generally prohibited.',$hostingCountry)
                                                                              ),
                                                                               I18N::translate('Copying or downloading genealogical data for private use is permitted. Anyone who wants to use parts of our data in their own family trees is obliged to name %s as the source. In particular, the protected images may only be accessible there for family members. The information on living people must not be copied to other websites where the privacy of these people cannot be guaranteed. A lot of time and effort was put into the webtrees database. I don\'t want others to simply copy our work together. I expect you to follow these guidelines as well. This database remains my property and will not be sold, donated or rented in any way.',$hostingDomain)],
                                                               'contentWe' => [I18N::translate('We always endeavor to observe the copyrights of others or to use self-created and license-free works. Third-party contributions to this website are marked as such by naming a source if they cannot be used freely. Should you nevertheless become aware of a copyright infringement, please inform us accordingly. As soon as we become aware of legal violations, we will remove such content immediately.'),
                                                                              ($hostingCountry == '' ?
                                                                               I18N::translate('The content and works created by us and by the registered users on this website are subject to copyright. If you contribute content as a registered user of this website, you transfer all rights to it to us. The distribution and any kind of exploitation of the content of this website outside the limits of copyright require our written consent. Commercial use of the information provided here is generally prohibited.')
                                                                              :
                                                                               I18N::translate('The content and works created by us and by the registered users on this website are subject to copyright in %s. If you contribute content as a registered user of this website, you transfer all rights to it to us. The distribution and any kind of exploitation of the content of this website outside the limits of copyright require our written consent. Commercial use of the information provided here is generally prohibited.',$hostingCountry)
                                                                              ),
                                                                               I18N::translate('Copying or downloading genealogical data for private use is permitted. Anyone who wants to use parts of our data in their own family trees is obliged to name %s as the source. In particular, the protected images may only be accessible there for family members. The information on living people must not be copied to other websites where the privacy of these people cannot be guaranteed. A lot of time and effort was put into the webtrees database. We don\'t want others to simply copy our work together. We expect you to follow these guidelines as well. This database remains our property and will not be sold, donated or rented in any way.',$hostingDomain)]],
            'UseDataLegalNotice'    => ['contentIWe' => true,  'contentI'  => [I18N::translate('The use of the contact data published by us as part of the imprint obligation by third parties for the purpose of sending unsolicited advertising and information material is hereby expressly prohibited. I expressly reserve the right to take legal action in the event of unsolicited advertising being sent, such as spam e-mails.')],
                                                               'contentWe' => [I18N::translate('The use of the contact data published by us as part of the imprint obligation by third parties for the purpose of sending unsolicited advertising and information material is hereby expressly prohibited. We expressly reserve the right to take legal action in the event of unsolicited advertising being sent, such as spam e-mails.')]],
            'MitigateDamages'       => ['contentIWe' => true,  'contentI'  => [I18N::translate('Should you notice any irregularities on our website, please contact me beforehand to avoid unnecessary legal disputes and costs. I am sure that we will come to an amicable and informal solution. The cost note of a legal warning will therefore be rejected as unfounded in the sense of the obligation to mitigate the damage if I have not been contacted via my e-mail address beforehand and have been informed of this possible grievance.')],
                                                               'contentWe' => [I18N::translate('Should you notice any irregularities on our website, we ask you to contact us beforehand to avoid unnecessary legal disputes and costs. We are sure that we will come to an amicable and informal solution. The cost note of a legal warning will therefore be rejected as unfounded in the sense of the obligation to mitigate the damage if we have not been contacted beforehand via one of our e-mail addresses and have been informed of this possible grievance.')]],
            'OpenSourceLicense'     => ['contentIWe' => false, 'content'   => [I18N::translate('This website uses the genealogy program webtrees - an open source software. The source code is publicly available on GitHub and can be changed by anyone. Usage is free. This program was developed by people from many countries and is provided under the terms of the GNU General Public License Version 3 or a later version. The developers and the entire webtrees community are volunteering their time and skills to the project. Apart from the few donations from users, the developers receive no compensation for the time they invest in the project. There are also no external sources of income to support the project.')]],
            'SeverabilityClause'    => ['contentIWe' => false, 'content'   => [I18N::translate('If parts or individual formulations of this text do not, no longer or not completely correspond to the applicable legal situation, the remaining parts of the text remain unaffected in their content and validity.')]],
        ];
    }

    /**
     * list of parts of extended family
     *
     * @return array<int,string>
     */
    public static function listChapterKeys(): array
    {
        return array_keys(self::getChapterParameters());
    }

    /**
     * set content to a chapter using the I and the We style
     *
     * @param object    $chapter        Chapter object
     * @param bool      $iWe            true if both, I stayle and We style, exists
     * @param string    $textI          text using I style or text independent of style
     * @param string    $textWe         text using We style
     * @return void
     */
    public static function addChapterContent(object $chapter, bool $iWe, string $textI, string $textWe = ''): void
    {
        $chapter->iWe = $iWe;
        if ($iWe) {
            $chapter->contentI = $textI;
            $chapter->contentWe = $textWe;
        } else {
            $chapter->content = $textI;
        }
    }
}
