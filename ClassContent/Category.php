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

namespace BackBee\ClassContent;

/**
 * Category represents a classcontent category and contains every blocks which has current category name
 *
 * @category    BackBee
 * @package     BackBee\ClassContent
 * @copyright   Lp digital system
 * @author      e.chau <eric.chau@lp-digital.fr>
 */
class Category implements \JsonSerializable
{
    /**
     * Category's name
     * @var string
     */
    private $name;

    /**
     * Contains many \stdClass which hold informations (label, description, type, visible) about blocks
     * @var array
     */
    private $blocks = [];

    /**
     * Category's contructor
     *
     * @param string $name category's name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Category's name getter
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * This method allows us to build new block according to provided classcontent ($content) and
     * add it into Category blocks attribute
     *
     * @param AClassContent $content the class content to add to this category
     * @param boolean       $visible if this block is visible or not
     *
     * @return self
     */
    public function addBlock(AClassContent $content, $visible = true, array $options = array())
    {
        $block = new \stdClass();
        $block->visible = $visible;
        $block->label = $content->getProperty('name');
        $block->description = $content->getProperty('description');
        $block->type = str_replace(
            array(AContent::CLASSCONTENT_BASE_NAMESPACE, NAMESPACE_SEPARATOR),
            array('', '/'),
            get_class($content)
        );

        if (array_key_exists('thumbnail_url_pattern', $options)) {
            $block->thumbnail = sprintf($options['thumbnail_url_pattern'], $block->type);
        }

        $this->blocks[] = $block;

        return $this;
    }

    /**
     * Category's blocks getter
     *
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array(
            'name'     => $this->name,
            'contents' => $this->blocks,
        );
    }
}