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

class ehough_shortstop_api_HttpResponseTest extends ehough_shortstop_api_AbstractHttpMessageTest
{
    function buildSut()
    {
        return new ehough_shortstop_api_HttpResponse();
    }


    function testSetResponseCode()
    {
        $this->getSut()->setStatusCode(134.2);
        $this->assertEquals(134, $this->getSut()->getStatusCode());

        $this->getSut()->setStatusCode(432);
        $this->assertEquals(432, $this->getSut()->getStatusCode());
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetStatusCodeTooHigh()
    {
        $this->getSut()->setStatusCode(600);
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetStatusCodeTooLow()
    {
        $this->getSut()->setStatusCode(99);
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testSetResponseCodeNonNumeric()
    {
        $this->getSut()->setStatusCode('something');
    }
}