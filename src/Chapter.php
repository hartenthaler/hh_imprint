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

declare(strict_types=1);

namespace Hartenthaler\Webtrees\Module\LegalNotice;

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
     *  ->id                int         unique chapter id
     *  ->heading           string      translated chapter heading
     *  ->level             int         level of heading (1=top level)
     *  ->link              int         link to id of next higher level
     *  ->enabled           bool        true if chapter should be shown
     *  ->content           string      content of this chapter (used if contentIWe is false)
     */
    private object $chapter;

    // ------------ definition of methods

    /**
     * construct object
     *
     * @param string        $key
     * @param int           $id
     * @param string        $heading
     * @param int           $level
     * @param int           $link
     * @param bool          $enabled
     * @param bool          $contentIWe
     * @param array<string> $content    list of paragraphs
     */
    public function __construct
        (
            string  $key,
            int     $id,
            string  $heading,
            int     $level,
            int     $link,
            bool    $enabled,
            bool    $contentIWe,
            array   $content
        )
    {
        $this->chapter = (object)[];
        $this->chapter->key             = $key;
        $this->chapter->id              = $id;
        $this->chapter->heading         = $heading;
        $this->chapter->level           = $level;
        $this->chapter->link            = $link;
        $this->chapter->enabled         = $enabled;
        $this->chapter->contentIWe      = $contentIWe;
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
     * get id of chapter
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->chapter->id;
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
     * get link of this chapter to next higher hierarchy id
     *
     * @return int
     */
    public function getLink(): int
    {
        return $this->chapter->link;
    }

    /**
     * get chapter content (list of paragraphs)
     *
     * @return array
     */
    public function getContent(): array
    {
        return $this->chapter->content;
    }

    /**
     * get chapter enabling status
     *
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->chapter->enabled;
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
}
