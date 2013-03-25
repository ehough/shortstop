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