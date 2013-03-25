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

class ehough_shortstop_impl_HttpClientChainTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_request;
    private $_response;
    private $_url;
    private $_context;
    private $_mockChain;
    private $_mockHttpTransferDecoder;
    private $_mockHttpContentDecoder;

    function setup()
    {
        $this->_chain                   = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_mockHttpContentDecoder  = Mockery::mock('ehough_shortstop_spi_HttpContentDecoder');
        $this->_mockHttpTransferDecoder = Mockery::mock('ehough_shortstop_spi_HttpTransferDecoder');

        $this->_sut = new ehough_shortstop_impl_HttpClientChain(

            $this->_chain,
            $this->_mockHttpContentDecoder,
            $this->_mockHttpTransferDecoder
        );

        $this->_response = new ehough_shortstop_api_HttpResponse();
        $this->_context  = new ehough_chaingang_impl_StandardContext();
        $this->_url      = new ehough_curly_Url('http://ehough.com');
        $this->_request  = new ehough_shortstop_api_HttpRequest('GET', $this->_url);
    }

    function testGet()
    {
        $this->_setupForNormalExecution();
        $this->_verifyNormalExecution();
    }

    function testExecuteAndHandle()
    {
        $this->_setupForNormalExecution();
        $this->_verifyHandledExecution();
    }

    function testGetWithBadEntity()
    {
        $this->_setupRequestWithBadEntity();
        $this->_setupDecoder();
        $this->_verifyNormalExecution();
    }

    function testGetWithEntity()
    {
        $this->_setupRequestWithEntity();
        $this->_setupDecoder();
        $this->_verifyNormalExecution();
    }

    /**
     * @expectedException ehough_shortstop_api_exception_RuntimeException
     */
    function testGetNoCommandsCouldHandle()
    {
        $response = $this->_response;

        $this->_setupForNormalExecution();
        $this->_chain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) use ($response) {

            $arg->put(ehough_shortstop_impl_HttpClientChain::CHAIN_KEY_RESPONSE, $response);

            return $arg instanceof ehough_chaingang_api_Context;

        }))->andReturn(false);

        $this->_sut->execute($this->_request);
    }

    private function _verifyHandledExecution()
    {
        $response = $this->_response;

        $this->_chain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) use ($response) {

            $arg->put(ehough_shortstop_impl_HttpClientChain::CHAIN_KEY_RESPONSE, $response);

            return $arg instanceof ehough_chaingang_api_Context;

        }))->andReturn(true);

        $handler = \Mockery::mock('ehough_shortstop_api_HttpResponseHandler');
        $handler->shouldReceive('handle')->once()->with($this->_response)->andReturn('final result');
        $this->_verifyDecoders();

        $result = $this->_sut->executeAndHandleResponse($this->_request, $handler);

        $this->assertEquals('final result', $result);
    }

    private function _verifyNormalExecution()
    {
        $response = $this->_response;

        $this->_chain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) use ($response) {

            $arg->put(ehough_shortstop_impl_HttpClientChain::CHAIN_KEY_RESPONSE, $response);

            return $arg instanceof ehough_chaingang_api_Context;

        }))->andReturn(true);

        $this->_verifyDecoders();

        $result = $this->_sut->execute($this->_request);

        $this->assertTrue($this->_response === $result);
    }

    private function _verifyDecoders()
    {
        $this->_mockHttpTransferDecoder->shouldReceive('needsToBeDecoded')->once()->with($this->_response)->andReturn(true);
        $this->_mockHttpContentDecoder->shouldReceive('needsToBeDecoded')->once()->with($this->_response)->andReturn(true);
        $this->_mockHttpTransferDecoder->shouldReceive('decode')->once()->with($this->_response);
        $this->_mockHttpContentDecoder->shouldReceive('decode')->once()->with($this->_response);
    }

    private function _setupForNormalExecution()
    {
        $this->_setupRequestNoEntity();

        $this->_setupDecoder();
    }

    private function _setupDecoder()
    {
        $this->_mockHttpContentDecoder->shouldReceive('getAcceptEncodingHeaderValue')->once()->andReturn('encoding header problem');
    }

    private function _setupRequestBase()
    {
        $map = array(

            ehough_shortstop_api_HttpRequest::HTTP_HEADER_USER_AGENT => 'TubePress; http://tubepress.org',
            ehough_shortstop_api_HttpMessage::HTTP_HEADER_HTTP_VERSION => 'HTTP/1.0',
            ehough_shortstop_api_HttpRequest::HTTP_HEADER_ACCEPT_ENCODING => 'encoding header problem'
        );

        foreach ($map as $headerName => $headerValue) {

            $this->_request->setHeader($headerName, $headerValue);
        }
    }

    private function _setupRequestNoEntity()
    {
        $this->_setupRequestBase();
    }

    private function _setupRequestWithBadEntity()
    {
        $this->_setupRequestBase();
        $entity = new ehough_shortstop_api_HttpEntity();

        $this->_request->setEntity($entity);
    }

    private function _setupRequestWithEntity()
    {
        $this->_setupRequestBase();
        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent('content');
        $entity->setContentLength(103);
        $entity->setContentType('text/html');

        $this->_request->setEntity($entity);

        $this->_request->setHeader(ehough_shortstop_api_HttpRequest::HTTP_HEADER_CONTENT_ENCODING, 'content encoding');
        $this->_request->setHeader(ehough_shortstop_api_HttpRequest::HTTP_HEADER_CONTENT_TYPE, 'text/html');
        $this->_request->setHeader(ehough_shortstop_api_HttpRequest::HTTP_HEADER_CONTENT_LENGTH, '103');
    }
}
