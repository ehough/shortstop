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

class ehough_shortstop_impl_exec_DefaultHttpMessageParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ehough_shortstop_impl_exec_DefaultHttpMessageParser
     */
    private $_sut;

    public function setup()
    {
        $this->_sut = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();
    }

    public function testGetHeaderArrayNullMessage()
    {
        $this->assertEquals(array(), $this->_sut->getArrayOfHeadersFromRawHeaderString(null));
    }

    public function testGetHeaderArrayBadMessage()
    {
        $this->assertEquals(array(), $this->_sut->getArrayOfHeadersFromRawHeaderString('this is a string with nothing in it'));
    }

    public function testGetHeaderArraySomeBadMessages()
    {
        $expected = array(
            'Header' => 'Value',
            'Another' => 'header'
        );
        $this->assertEquals($expected, $this->_sut->getArrayOfHeadersFromRawHeaderString("Header: Value\r\ntthis is a string with nothing in it\r\nAnother: \theader\r\nHeader21:\r\n"));
    }

    public function testGetHeaderArrayMultipleHeaders()
    {
        $expected = array(
            'Header' => array('Value', 'something else'),
            'Another' => 'header'
        );
        $this->assertEquals($expected, $this->_sut->getArrayOfHeadersFromRawHeaderString("Header: Value\r\nAnother: \theader\r\nHeader: something else\r\n"));
    }

    public function testGetHeaderAsString()
    {
        $message = new ehough_shortstop_api_HttpResponse();
        $message->setHeader('one', 'two');
        $message->setHeader('three', 'four');

        $result = $this->_sut->getHeaderArrayAsString($message);
        $this->assertEquals("one: two\r\nthree: four\r\n", $result);
    }

    public function testGetHeadersStringFromRawHttpMessage()
    {
        $result = $this->_sut->getHeadersStringFromRawHttpMessage("headers\r\n\r\nHeaders");
        $this->assertEquals('headers', $result);
    }

    public function testGetHeadersStringFromRawHttpMessageBadMessage()
    {
        $result = $this->_sut->getHeadersStringFromRawHttpMessage("something");
        $this->assertEquals('something', $result);
    }

    public function testGetHeadersStringFromRawHttpMessageNullMessage()
    {
        $result = $this->_sut->getHeadersStringFromRawHttpMessage(null);
        $this->assertNull($result);
    }

    public function testGetBodyStringFromRawHttpMessage()
    {
        $result = $this->_sut->getBodyStringFromRawHttpMessage("headers\r\n\r\nbody");
        $this->assertEquals('body', $result);
    }

    public function testGetBodyStringFromRawHttpMessageBadMessage()
    {
        $result = $this->_sut->getBodyStringFromRawHttpMessage("something");
        $this->assertNull($result);
    }

    public function testGetBodyStringFromRawHttpMessageNullMessage()
    {
        $result = $this->_sut->getBodyStringFromRawHttpMessage(null);
        $this->assertNull($result);
    }

}