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

class ehough_shortstop_impl_listeners_response_ResponseDecodingListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ehough_shortstop_impl_listeners_response_ResponseLoggingListener
     */
    private $_sut;

    private $_mockHttpResponseDecoder;

    public function setUp()
    {
        $this->_mockHttpResponseDecoder = Mockery::mock('ehough_shortstop_spi_HttpResponseDecoder');

        $this->_sut = new ehough_shortstop_impl_listeners_response_ResponseDecodingListener($this->_mockHttpResponseDecoder, 'fake');
    }

    public function testOnResponseNeedToDecode()
    {
        $response = new ehough_shortstop_api_HttpResponse();
        $request  = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, 'http://ehough.com');
        $event    = new ehough_tickertape_GenericEvent($response, array('request' => $request));

        $this->_mockHttpResponseDecoder->shouldReceive('needsToBeDecoded')->once()->with($response)->andReturn(true);
        $this->_mockHttpResponseDecoder->shouldReceive('decode')->once()->with($response);

        $this->_sut->onResponse($event);
    }

    public function testOnResponseNoNeedToDecode()
    {
        $response = new ehough_shortstop_api_HttpResponse();
        $request  = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, 'http://ehough.com');
        $event    = new ehough_tickertape_GenericEvent($response, array('request' => $request));

        $this->_mockHttpResponseDecoder->shouldReceive('needsToBeDecoded')->once()->with($response)->andReturn(false);

        $this->_sut->onResponse($event);
    }
}