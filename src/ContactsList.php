<?php
/*
 * webtrees - imprint as footer element
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

namespace Hartenthaler\Webtrees\Module\Imprint;

use Fisharebest\Webtrees\Module\ContactsFooterModule;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * class ContactsList
 *
 * support methods for module hh_imprint
 *
 * generate lists of additional contacts to be displayed in the footer "Imprint" page
 *      - list of contact persons for a tree (genealogical and technical)
 *      - list of administrators of a site including their contact links
 */
class ContactsList
{
    // ------------ definition of data structures

    /** @var array<int|string> $treeContactsList */
    private array $treeContactsList;

    /** @var array<int|object> $administratorsList */
    private array $administratorsList;

    // ------------ definition of methods

    /**
     * construct ContactsList object
     *
     * @param UserService $userService
     * @param ServerRequestInterface $request
     */
    public function __construct(UserService $userService, ServerRequestInterface $request)
    {
        $this->findTreeContacts($userService, $request);
        $this->findAdministrators($userService, $request);
    }

    /**
     * get list of contact persons for a tree (genealogical and technical)
     *
     * @return array<int|string>
     */
    public function getTreeContactsList(): array
    {
        return $this->treeContactsList;
    }

    /**
     * get list of administrators of a site including their contact links
     *
     * @return array<int|object>
     */
    public function getAdministratorsList(): array
    {
        return $this->administratorsList;
    }

    /**
     * create a list of genealogical and technical contacts of this tree
     * (those strings are already translated)
     *
     * @param UserService $userService
     * @param ServerRequestInterface $request
     */
    private function findTreeContacts(UserService $userService, ServerRequestInterface $request): void
    {
        $this->treeContactsList = [];

        $tree = Validator::attributes($request)->treeOptional();
        if ($tree === null) {
            return;
        }

        $footerClass = new ContactsFooterModule($userService);

        $contactUser   = $userService->find((int) $tree->getPreference('CONTACT_USER_ID'));
        $webmasterUser = $userService->find((int) $tree->getPreference('WEBMASTER_USER_ID'));

        if ($contactUser instanceof User && $contactUser === $webmasterUser) {
            $this->treeContactsList[] = $footerClass->contactLinkEverything($contactUser, $request);
        } elseif ($contactUser instanceof User && $webmasterUser instanceof User) {
            $this->treeContactsList[] = $footerClass->contactLinkGenealogy($contactUser, $request);
            $this->treeContactsList[] = $footerClass->contactLinkTechnical($webmasterUser, $request);
        } elseif ($contactUser instanceof User) {
            $this->treeContactsList[] = $footerClass->contactLinkGenealogy($contactUser, $request);
        } elseif ($webmasterUser instanceof User) {
            $this->treeContactsList[] = $footerClass->contactLinkTechnical($webmasterUser, $request);
        }
    }

    /**
     * Create a list of administrators of this site.
     *
     * @param UserService $userService
     * @param ServerRequestInterface $request
     */
    private function findAdministrators(UserService $userService, ServerRequestInterface $request): void
    {
        $this->administratorsList = [];
        $administrators = $userService->administrators();     // Collection<int,User>
        foreach ($administrators as $admin) {
            $administrator = (object)[];
            $administrator->userId = $admin->id();
            $administrator->realName = $admin->realName();
            $administrator->email = $admin->email();
            $administrator->contact = $userService->contactLink($admin, $request);
            $this->administratorsList[] = $administrator;
        }
    }
}
