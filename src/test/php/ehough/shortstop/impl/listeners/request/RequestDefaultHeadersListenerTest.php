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

class ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener
     */
    private $_sut;

    private $_mockHttpContentDecoder;

    public function setUp()
    {
        $this->_mockHttpContentDecoder = ehough_mockery_Mockery::mock('ehough_shortstop_spi_HttpContentDecoder');

        $this->_sut = new ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener($this->_mockHttpContentDecoder);
    }

    public function testOnRequestEntity()
    {
        $request = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, 'http://ehough.com');
        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent('foobar');
        $entity->setContentType('something');
        $request->setEntity($entity);
        $event   = new ehough_tickertape_GenericEvent($request);

        $this->_mockHttpContentDecoder->shouldReceive('getAcceptEncodingHeaderValue')->once()->andReturn('foobarr');

        $this->_sut->onPreRequest($event);

        $this->assertEquals('foobarr', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_ACCEPT_ENCODING));
        $this->assertEquals('shortstop; https://github.com/ehough/shortstop', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_USER_AGENT));
        $this->assertEquals('HTTP/1.0', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_HTTP_VERSION));
    }

    public function testOnRequestEntityNoContentOrType()
    {
        $request = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, 'http://ehough.com');
        $request->setEntity(new ehough_shortstop_api_HttpEntity());
        $event   = new ehough_tickertape_GenericEvent($request);

        $this->_mockHttpContentDecoder->shouldReceive('getAcceptEncodingHeaderValue')->once()->andReturn('foobarr');

        $this->_sut->onPreRequest($event);

        $this->assertEquals('foobarr', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_ACCEPT_ENCODING));
        $this->assertEquals('shortstop; https://github.com/ehough/shortstop', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_USER_AGENT));
        $this->assertEquals('HTTP/1.0', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_HTTP_VERSION));
    }

    public function testOnRequestNoEntity()
    {
        $request = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, 'http://ehough.com');
        $event   = new ehough_tickertape_GenericEvent($request);

        $this->_mockHttpContentDecoder->shouldReceive('getAcceptEncodingHeaderValue')->once()->andReturn('foobarr');

        $this->_sut->onPreRequest($event);

        $this->assertEquals('foobarr', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_ACCEPT_ENCODING));
        $this->assertEquals('shortstop; https://github.com/ehough/shortstop', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_USER_AGENT));
        $this->assertEquals('HTTP/1.0', $request->getHeaderValue(ehough_shortstop_api_HttpRequest::HTTP_HEADER_HTTP_VERSION));
    }
}