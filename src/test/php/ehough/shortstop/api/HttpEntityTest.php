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

class ehough_shortstop_api_HttpEntityTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    public function setUp()
    {
        $this->_sut = new ehough_shortstop_api_HttpEntity();
    }

    public function testSetGetContentType()
    {
        $this->_sut->setContentType('hello you');
        $this->assertEquals('hello you', $this->_sut->getContentType());
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    public function testSetNonStringContentType()
    {
        $this->_sut->setContentType(4);
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    public function testSetNegativeContentLength()
    {
        $this->_sut->setContentLength(-1);
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    public function testSetBadContentLength()
    {
        $this->_sut->setContentLength('something');
    }

    public function testSetGetContentLength()
    {
        $this->_sut->setContentLength(55);
        $this->assertEquals(55, $this->_sut->getContentLength());

        $this->_sut->setContentLength(45.6);
        $this->assertEquals(45, $this->_sut->getContentLength());
    }

    public function testSetContent()
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