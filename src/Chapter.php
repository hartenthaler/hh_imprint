<?php
/*
 * webtrees - object Chapter
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

/**
 * class Chapter
 *

 */


/**
 * class Chapter
 * object to store a chapter with meta data and text
 * this is used by the legal notice custom module
 */
class Chapter
{
    // ------------ definition of data structures

    /**
     * @var object $chapter
     *  ->key               string      key
     *  ->id                int         unique chapter id                    // tbd kann vielleicht entfallen
     *  ->heading           string      translated chapter heading
     *  ->level             int         level of heading (1=top level)
     *  ->link              int         link to id of next higher level
     *  ->enabled           bool        chapter should be shown
     *  ->content           string      content of this chapter
     */
    private object $chapter;

    // ------------ definition of methods

    /**
     * construct object
     *
     * @param string $key
     * @param int $id
     * @param string $heading
     * @param int $level
     * @param int $link
     * @param bool $enabled
     * @param string $content
     */
    public function __construct
        (
            string  $key,
            int     $id,
            string  $heading,
            int     $level,
            int     $link,
            bool    $enabled,
            string  $content
        )
    {
        $this->chapter = (object)[];
        $this->chapter->key             = $key;
        $this->chapter->id              = $id;
        $this->chapter->heading         = $heading;
        $this->chapter->level           = $level;
        $this->chapter->link            = $link;
        $this->chapter->enabled         = $enabled;
        $this->chapter->content         = $content;
    }

    /**
     * get object chapter
     *
     * @return object
     */
    public function getChapter(): object
    {
        return $this->chapter;
    }

    /**
     * get key of chapter
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->chapter->key;
    }

    /**
     * get level of chapter (1=top level)
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->chapter->level;
    }

    /**
     * get chapter heading
     *
     * @return string
     */
    public function getHeading(): string
    {
        return $this->chapter->heading;
    }

    /**
     * get chapter enabling status
     *
     * @return string
     */
    public function getEnabled(): bool
    {
        return $this->chapter->enabled;
    }
}
