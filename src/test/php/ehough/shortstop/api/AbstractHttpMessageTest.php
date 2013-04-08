<?php
/**
 * Copyright 2013 Eric D. Hough (http://ehough.com)
 *
 * This file is part of shortstop (https://github.com/ehough/shortstop)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

abstract class ehough_shortstop_api_AbstractHttpMessageTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    public function setUp()
    {
        $this->_sut = $this->buildSut();
    }

    public function testSetEntity()
    {
        $entity = new ehough_shortstop_api_HttpEntity();
        $this->_sut->setEntity($entity);
        $this->assertSame($entity, $this->_sut->getEntity());
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    public function testGetHeaderBadName()
    {
        $this->_sut->getHeaderValue(6);
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    public function testSetHeaderBadValue()
    {
        $this->_sut->setHeader(5, 'two');
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    public function testSetHeaderBadName()
    {
        $this->_sut->setHeader(5, 'two');
    }

    public function testSetGetHeader()
    {

        $this->_sut->setHeader('something', 'else');
        $this->assertEquals('else', $this->_sut->getHeaderValue('something'));

        $this->assertEquals(array('something' => 'else'), $this->_sut->getAllHeaders());

        $this->_sut->setHeader('foo', 'bar');
        $this->_sut->removeHeaders('something');
        $this->assertEquals(array('foo' => 'bar'), $this->_sut->getAllHeaders(), 'Header "something" did not get removed');
    }

    public function testGetHeaderNotExist()
    {

        $this->assertFalse($this->_sut->containsHeader('something'));
        $this->assertNull($this->_sut->getHeaderValue('something'));
    }

    protected abstract function buildSut();

    protected function getSut()
    {
        return $this->_sut;
    }
}