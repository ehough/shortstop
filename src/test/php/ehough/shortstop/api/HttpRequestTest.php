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

class ehough_shortstop_api_HttpRequestTest extends ehough_shortstop_api_AbstractHttpMessageTest
{

    function buildSut()
    {
        return new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, 'http://tubepress.org/foo.html');
    }

    function testToString()
    {
        $expected = 'GET to http://tubepress.org/foo.html';
        $this->assertEquals($expected, $this->getSut()->toString());
        $this->assertEquals($expected, $this->getSut()->__toString());
    }

    function testToHTML()
    {
        $expected = 'GET to <a href="http://tubepress.org/foo.html">URL</a>';
        $this->assertEquals($expected, $this->getSut()->toHTML());
    }

    function testSetUrlUrl()
    {
        $url = new ehough_curly_Url('http://tubepress.org/foo.html');
        $this->getSut()->setUrl($url);
        $url = $this->getSut()->getUrl();

        $this->assertTrue($url instanceof ehough_curly_Url);
        $this->assertEquals('http://tubepress.org/foo.html', $url->toString());
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetUrlBadArg()
    {
        $this->getSut()->setUrl(4);
    }

    function testSetUrlString()
    {
        $this->getSut()->setUrl('http://tubepress.org/foo.html');
        $url = $this->getSut()->getUrl();

        $this->assertTrue($url instanceof ehough_curly_Url);
        $this->assertEquals('http://tubepress.org/foo.html', $url->toString());
    }

    function testSetGetMethod()
    {
        $this->getSut()->setMethod('pOsT');
        $this->assertEquals('POST', $this->getSut()->getMethod());
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetBadMethod()
    {
        $this->getSut()->setMethod('something dumb');
    }
}