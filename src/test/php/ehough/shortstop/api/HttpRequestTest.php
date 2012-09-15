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

require_once dirname(__FILE__) . '/AbstractHttpMessageTest.php';

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