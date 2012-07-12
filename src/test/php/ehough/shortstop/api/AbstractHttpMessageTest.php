<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of shortstop (https://github.com/ehough/shortstop)
 *
 * shortstop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * shortstop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with shortstop.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

abstract class ehough_shortstop_api_AbstractHttpMessageTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    function setUp()
    {
        $this->_sut = $this->buildSut();
    }

    function testSetEntity()
    {
        $entity = new ehough_shortstop_api_HttpEntity();
        $this->_sut->setEntity($entity);
        $this->assertSame($entity, $this->_sut->getEntity());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testGetHeaderBadName()
    {
        $this->_sut->getHeaderValue(6);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testSetHeaderBadValue()
    {
        $this->_sut->setHeader(5, 'two');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testSetHeaderBadName()
    {
        $this->_sut->setHeader(5, 'two');
    }

    function testSetGetHeader()
    {

        $this->_sut->setHeader('something', 'else');
        $this->assertEquals('else', $this->_sut->getHeaderValue('something'));

        $this->assertEquals(array('something' => 'else'), $this->_sut->getAllHeaders());

        $this->_sut->setHeader('foo', 'bar');
        $this->_sut->removeHeaders('something');
        $this->assertEquals(array('foo' => 'bar'), $this->_sut->getAllHeaders(), 'Header "something" did not get removed');
    }

    function testGetHeaderNotExist()
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