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

class ehough_shortstop_api_HttpEntityTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    function setUp()
    {
        $this->_sut = new ehough_shortstop_api_HttpEntity();
    }

    function testSetGetContentType()
    {
        $this->_sut->setContentType('hello you');
        $this->assertEquals('hello you', $this->_sut->getContentType());
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetNonStringContentType()
    {
        $this->_sut->setContentType(4);
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetNegativeContentLength()
    {
        $this->_sut->setContentLength(-1);
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetBadContentLength()
    {
        $this->_sut->setContentLength('something');
    }

    function testSetGetContentLength()
    {
        $this->_sut->setContentLength(55);
        $this->assertEquals(55, $this->_sut->getContentLength());

        $this->_sut->setContentLength(45.6);
        $this->assertEquals(45, $this->_sut->getContentLength());
    }

    function testSetContent()
    {
        $tests = array(

            array(1),
            array('one' => 'two'),
            'string',
            false,
            7E-10,
            null,
            new stdClass()
        );

        foreach ($tests as $test)
        {
            $this->_testSetContent($test);
        }
    }

    private function _testSetContent($value)
    {
        $this->_sut->setContent($value);
        $this->assertEquals($value, $this->_sut->getContent());
    }
}