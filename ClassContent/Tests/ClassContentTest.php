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

namespace BackBee\ClassContent\Tests;

use BackBee\ClassContent\AClassContent;
use BackBee\ClassContent\Element\image;
use BackBee\ClassContent\Revision;
use BackBee\ClassContent\Tests\Mock\MockContent;
use BackBee\Exception\BBException;

/**
 * @category    BackBee
 * @package     BackBee\NestedNode\Tests
 * @copyright   Lp digital system
 * @author      n.dufreche <nicolas.dufreche@lp-digital.fr>
 */
class ClassContentTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->content = new MockContent();
        $this->content->load();
    }

    /**
     * test getProperty
     *
     * @coverage \BackBee\ClassContent\AClassContent::getProperty
     */
    public function testGetProperty()
    {
        $this->assertInternalType('array', $this->content->getProperty());
        $this->assertEquals('Mock Content', $this->content->getProperty('name'));
        $this->assertNull($this->content->getProperty('notset'));
    }

    /**
     * test setProperty
     *
     * @coverage \BackBee\ClassContent\AClassContent::setProperty
     */
    public function testSetProperty()
    {
        $this->content->setProperty('foo', 'bar');
        $this->assertEquals('bar', $this->content->getProperty('foo'));
    }

    /**
     * test createClone
     *
     * @coverage \BackBee\ClassContent\AClassContent::createClone
     */
    public function testCreateClone()
    {
        $this->content->setProperty('foo', 'bar');
        $this->content->title->value = 'baz';
        $clone = $this->content->createClone();

        $this->assertInstanceOf('BackBee\ClassContent\Tests\Mock\MockContent', $clone);
        $this->assertNull($clone->getProperty('foo'));
        $this->assertEquals('baz', $clone->title->value);
        $this->assertNotEquals($this->content->getUid(), $clone->getUid());
    }

    /**
     * test setProperty
     *
     * @coverage \BackBee\ClassContent\AContent::_isAccepted
     */
    public function testAcceptedType()
    {
        $this->assertTrue($this->content->isAccepted($this->content->title, 'title'));
        $this->assertTrue($this->content->isAccepted('foo', 'bar'));

        $this->assertFalse($this->content->isAccepted(new \stdClass(), 'title'));
        $this->assertFalse($this->content->isAccepted('false'));
    }

    /**
     * test defineProperty
     *
     * @coverage \BackBee\ClassContent\AClassContent::_defineProperty
     */
    public function testDefineProperty()
    {
        $name = $this->content->getProperty('name');

        $this->content->defineProperty('name', $name.' foobar');
        $this->assertEquals($name, $this->content->getProperty('name'));

        $this->content->defineProperty('newproperty', 'foobar');
        $this->assertEquals('foobar', $this->content->getProperty('newproperty'));
    }

    /**
     * test defineParam
     *
     * @coverage \BackBee\ClassContent\AClassContent::_defineParam
     */
    public function testDefineParam()
    {
        $this->assertFalse(
            $this->content->getDefaultParameters()['excludefromautobloc']['array']['default'],
            'Before load excludefromautobloc have to be false'
        );
        $param = array(
            'default' => array(
                'rendertype' => 'checkbox',
                'label' => 'Exclude from autoblocs',
                'default' => true,
            ),
        );
        $this->content->defineParam('excludefromautobloc', 'array', $param);
        $this->assertTrue(
            $this->content->getDefaultParameters()['excludefromautobloc']['array']['default']
        );
        $this->assertNotEquals(
            $this->content->getParam('excludefromautobloc:array:default'),
            $this->content->getDefaultParameters()['excludefromautobloc']['array']['default']
        );
        $this->content->defineParam('foo', 'scalar', 'toBeNull');
        $this->assertNull($this->content->getParam('foo:scalar'));
        $this->assertNull($this->content->getDefaultParameters()['foo']['scalar']);
    }

    /**
     * test defineData
     *
     * @coverage \BackBee\ClassContent\AClassContent::_defineData
     */
    public function testDefineData()
    {
        $this->content->defineData(
            'title',
            '\BackBee\ClassContent\Element\date',
            array(
                'default' => array('value' => 'Foo Bar Baz'),
            )
        );
        $this->content->defineData(
            'title',
            '\BackBee\ClassContent\Element\image',
            array(
                'default' => array('value' => 'Foo Bar Baz'),
            ),
            false
        );
        $this->content->defineData(
            'date',
            '\BackBee\ClassContent\Element\date',
            array(
                'default' => array('value' => 'A date'),
            ),
            true
        );

        $this->assertNotEquals('Foo Bar Baz', $this->content->title->value);
        $this->assertInstanceOf('BackBee\ClassContent\Element\date', $this->content->date);

        $this->assertTrue($this->content->isAccepted($this->content->date, 'title'));
        $this->assertTrue($this->content->isAccepted($this->content->title, 'title'));
        $this->assertFalse($this->content->isAccepted(new image(), 'title'));

        $this->assertEquals('A date', $this->content->date->value);
    }

    /**
     * test prepareCommitDraft
     *
     * @coverage \BackBee\ClassContent\AClassContent::prepareCommitDraft
     */
    public function prepareCommitDraft()
    {
        try {
            $this->content->prepareCommitDraft();
            $this->fail('RevisionMissingException not raise');
        } catch (BBException $expected) {
            $this->assertInstanceOf('\BackBee\ClassContent\Exception\RevisionMissingException', $expected);
        }

        $revision = new Revision();
        $revision->setContent($this->content);
        $this->content->setDraft($revision);
        $revision->setState(Revision::STATE_CONFLICTED);

        $this->assertInstanceOf('BackBee\ClassContent\Element\text', $this->content->getDraft()->getData('title'));

        try {
            $this->content->prepareCommitDraft();
            $this->fail('RevisionConflictedException not raise');
        } catch (BBException $expected) {
            $this->assertInstanceOf('\BackBee\ClassContent\Exception\RevisionConflictedException', $expected);
        }

        $revision->setState('bad case');
        try {
            $this->content->prepareCommitDraft();
            $this->fail('RevisionUptodateException not raise');
        } catch (BBException $expected) {
            $this->assertInstanceOf('\BackBee\ClassContent\Exception\RevisionUptodateException', $expected);
        }
    }
}