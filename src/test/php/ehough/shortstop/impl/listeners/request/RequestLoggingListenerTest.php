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

class ehough_shortstop_impl_listeners_request_RequestLoggingListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener
     */
    private $_sut;

    public function setUp()
    {
        $this->_sut = new ehough_shortstop_impl_listeners_request_RequestLoggingListener();
    }

    public function testOnRequest()
    {
        $request = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, 'http://ehough.com');

        $request->setHeader('foo', 'bar');

        $event   = new ehough_tickertape_GenericEvent($request);

        $this->_sut->onPreRequest($event);
    }
}