<?php

/*
 * Copyright (c) 2011-2015 Lp digital system
 *
 * This file is part of BackBee.
 *
 * BackBee5 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BackBee is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BackBee. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 */

namespace BackBee\ClassContent\Indexes;

/**
 * Entity class for Page-Content join table
 *
 * @category    BackBee
 * @package     BackBee\ClassContent
 * @subpackage  Indexes
 * @copyright   Lp digital system
 * @author      c.rouillon <charles.rouillon@lp-digital.fr>
 * @Entity(repositoryClass="BackBee\ClassContent\Repository\IndexationRepository")
 * @Table(name="idx_page_content",indexes={@index(name="IDX_PAGE", columns={"page_uid"}), @index(name="IDX_CONTENT_PAGE", columns={"content_uid"})})
 */
class IdxPageContent
{
    /**
     * @var string
     * @Id @Column(type="string", length=32)
     */
    private $page_uid;

    /**
     * @var string
     * @Id @Column(type="string", length=32)
     */
    private $content_uid;
}