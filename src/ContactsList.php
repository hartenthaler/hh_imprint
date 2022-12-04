<?php
/*
 * webtrees - imprint
 *
 * Copyright (C) 2022 Hermann Hartenthaler. All rights reserved.
 *
 * webtrees: online genealogy / web based family history software
 * Copyright (C) 2022 webtrees development team.
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
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

/**
 * class ContactsList
 *
 * support methods for module hh_imprint
 *
 * generate list of contacts to be displayed in the footer "Imprint" page:
 *      - list of contact persons for a tree (genealogical and technical)
 *      - list of contact links to administrators of a site
 */
class ContactsList
{
    // ------------ definition of data structures

    /** @var array $treeContactsList */
    private array $treeContactsList;

    /** @var array $administratorsList of objects */
    private array $administratorsList;

    /** @var UserService $userService */
    private UserService $userService;

    /** @var ServerRequestInterface $request */
    private ServerRequestInterface $request;

    // ------------ definition of methods

    /**
     * construct ContactsList object
     *
     * @param UserService $userService
     * @param ServerRequestInterface $request
     */
    public function __construct(UserService $userService, ServerRequestInterface $request)
    {
        $this->userService = $userService;
        $this->request = $request;

        $this->treeContactsList = [];
        $this->administratorsList = [];
        $this->findTreeContacts();
        $this->findAdministrators();
    }

    /**
     * @return array
     */
    public function getTreeContactsList(): array
    {
        return $this->treeContactsList;
    }

    /**
     * @return array of object
     */
    public function getAdministratorsList(): array
    {
        return $this->administratorsList;
    }

    /**
     * Create a list of genealogical and technical contacts of this tree.
     * (those strings are already translated)
     */
    private function findTreeContacts()
    {
        $tree = Validator::attributes($this->request)->treeOptional();
        if ($tree === null) {
            return;
        }

        $footerClass = new ContactsFooterModule($this->userService);

        $contactUser   = $this->userService->find((int) $tree->getPreference('CONTACT_USER_ID'));
        $webmasterUser = $this->userService->find((int) $tree->getPreference('WEBMASTER_USER_ID'));

        if ($contactUser instanceof User && $contactUser === $webmasterUser) {
            $this->treeContactsList[] = $footerClass->contactLinkEverything($contactUser, $this->request);
        }

        if ($contactUser instanceof User && $webmasterUser instanceof User) {
            $this->treeContactsList[] = $footerClass->contactLinkGenealogy($contactUser, $this->request);
            $this->treeContactsList[] = $footerClass->contactLinkTechnical($webmasterUser, $this->request);
        }

        if ($contactUser instanceof User) {
            $this->treeContactsList[] = $footerClass->contactLinkGenealogy($contactUser, $this->request);
        }

        if ($webmasterUser instanceof User) {
            $this->treeContactsList[] = $footerClass->contactLinkTechnical($webmasterUser, $this->request);
        }
    }

    /**
     * Create a list of administrators of this site.
     */
    private function findAdministrators()
    {
        $administrators = $this->userService->administrators();     // Collection<int,User>
        foreach ($administrators as $admin) {
            $administrator = (object)[];
            $administrator->userId = $admin->id();
            $administrator->realName = $admin->realName();
            $administrator->email = $admin->email();
            $administrator->contact = $this->userService->contactLink($admin, $this->request);
            $this->administratorsList[] = $administrator;
        }
    }

    /**
     * Create a contact link for a user.
     *
     * @param User $user
     *
     * @return string

    private function contactLink(User $user): string
    {
        $request = app(ServerRequestInterface::class);
        return $this->userService->contactLink($user, $request);
    }
     */
}
