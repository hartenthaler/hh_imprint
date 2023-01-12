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

namespace Hartenthaler\Webtrees\Module\Imprint;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ServerRequestInterface;

// string functions
use function str_replace;
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
     * get name of this website host
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public static function getHostName(ServerRequestInterface $request): string
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
     * Create a contact link for a user.
     *
     * @param User $user
     *
     * @return string
     */
    public static function contactLink(User $user): string
    {
        $request = app(ServerRequestInterface::class);
        //return $this->userService->contactLink($user, $request);          /tbd remove $this
        return '';
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
            'Liability'             => ['level' => 2, 'id' => 10, 'link' => 9, 'heading' => I18N::translateContext('heading','Liability')],
            'Copyright'             => ['level' => 2, 'id' => 11, 'link' => 9, 'heading' => I18N::translateContext('heading','Copyright')],
            'MitigateDamages'       => ['level' => 2, 'id' => 12, 'link' => 9, 'heading' => I18N::translateContext('heading','Duty to mitigate damages')],
            'OpenSource'            => ['level' => 2, 'id' => 13, 'link' => 9, 'heading' => I18N::translateContext('heading','Open-source and License')],
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
}
