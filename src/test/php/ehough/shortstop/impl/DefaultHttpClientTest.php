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

class ehough_shortstop_impl_DefaultHttpClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ehough_shortstop_impl_DefaultHttpClient
     */
    private $_sut;

    private $_request;
    private $_response;
    private $_url;
    private $_context;
    private $_mockChain;
    private $_mockEventDispatcher;

    private $_closureVarRequest;
    private $_closureVarResponse;

    public function setup()
    {
        $this->_mockChain           = ehough_mockery_Mockery::mock('ehough_chaingang_api_Chain');
        $this->_mockEventDispatcher = ehough_mockery_Mockery::mock('ehough_tickertape_EventDispatcherInterface');

        $this->_sut = new ehough_shortstop_impl_DefaultHttpClient(

            $this->_mockEventDispatcher,
            $this->_mockChain
        );

        $this->_response = new ehough_shortstop_api_HttpResponse();
        $this->_context  = new ehough_chaingang_impl_StandardContext();
        $this->_url      = new ehough_curly_Url('http://ehough.com');
        $this->_request  = new ehough_shortstop_api_HttpRequest('GET', $this->_url);
    }

    public function tearDown()
    {
        ehough_mockery_Mockery::close();
    }

    public function testGet()
    {
        $request = $this->_request;
        $response = $this->_response;

        $this->_closureVarRequest = $request;
        $this->_closureVarResponse = $response;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::REQUEST, ehough_mockery_Mockery::on(array($this, '_callbackTestGet1')));

        $this->_mockChain->shouldReceive('execute')->once()->with(ehough_mockery_Mockery::on(array($this, '_callbackTestGet2')))->andReturn(true);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::RESPONSE, ehough_mockery_Mockery::on(array($this, '_callbackTestGet3')));

        $response = $this->_sut->execute($this->_request);

        $this->assertSame($this->_response, $response);
    }

    public function _callbackTestGet1($event)
    {
        $req = $event->getSubject();

        return $req === $this->_closureVarRequest;
    }

    public function _callbackTestGet2($context)
    {
        $context->put('response', $this->_closureVarResponse);

        return $context instanceof ehough_chaingang_api_Context && $context->get('request') === $this->_closureVarRequest;
    }

    public function _callbackTestGet3($event)
    {
        $resp = $event->getSubject();
        $req  = $event->getArgument('request');

        return $req === $this->_closureVarRequest && $resp === $this->_closureVarResponse;
    }

    /**
     * @expectedException ehough_shortstop_api_exception_RuntimeException
     */
    public function testGetNoCommandsCouldHandle()
    {
        $this->_closureVarRequest = $this->_request;
        $this->_closureVarResponse = $this->_response;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::REQUEST, ehough_mockery_Mockery::on(array($this, '_callbackTestGetNoCommandsCouldHandle1')));

        $this->_mockChain->shouldReceive('execute')->once()->with(ehough_mockery_Mockery::on(array($this, '_callbackTestGetNoCommandsCouldHandle2')))->andReturn(false);

        $this->_sut->execute($this->_request);
    }

    public function _callbackTestGetNoCommandsCouldHandle1($event)
    {
        $req = $event->getSubject();

        return $req === $this->_closureVarRequest;
    }

    public function _callbackTestGetNoCommandsCouldHandle2($context)
    {
        $context->put('response', $this->_closureVarResponse);

        return $context instanceof ehough_chaingang_api_Context && $context->get('request') === $this->_closureVarRequest;
    }
}
