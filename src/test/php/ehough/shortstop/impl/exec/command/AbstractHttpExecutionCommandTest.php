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

abstract class ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommandTest extends PHPUnit_Framework_TestCase
{
    private $_sut;
    private $_args;
    private $_server;
    private $_mockHttpMessageParser;
    private $_mockEventDispatcher;
    private $_closureVarRequest;

    public function setUp()
    {
        $this->_mockHttpMessageParser = ehough_mockery_Mockery::mock('ehough_shortstop_spi_HttpMessageParser');
        $this->_mockEventDispatcher   = ehough_mockery_Mockery::mock('ehough_tickertape_EventDispatcherInterface');
        $this->_sut                   = $this->_getSutInstance($this->_mockHttpMessageParser, $this->_mockEventDispatcher);
        $this->_server                = 'http://tubepress.org/http_tests';

        $this->_mockHttpMessageParser->shouldReceive('getHeadersStringFromRawHttpMessage')->andReturnUsing(array($this, '_callbackSetup1'));
        $this->_mockHttpMessageParser->shouldReceive('getBodyStringFromRawHttpMessage')->andReturnUsing(array($this, '_callbackSetup2'));
        $this->_mockHttpMessageParser->shouldReceive('getArrayOfHeadersFromRawHeaderString')->andReturnUsing(array($this, '_callbackSetup3'));
        $this->_mockHttpMessageParser->shouldReceive('getHeaderArrayAsString')->andReturnUsing(array($this, '_callbackSetup4'));
    }

    public function _callbackSetup1($data)
    {
        $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

        return $x->getHeadersStringFromRawHttpMessage($data);
    }

    public function _callbackSetup2($data)
    {
        $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

        return $x->getBodyStringFromRawHttpMessage($data);
    }

    public function _callbackSetup3($data)
    {
        $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

        return $x->getArrayOfHeadersFromRawHeaderString($data);
    }

    public function _callbackSetup4($data)
    {
        $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

        return $x->getHeaderArrayAsString($data);
    }

    public function testGet200Plain()
    {
        $this->_testGet200Plain();
        $this->_testGet200Plain();
    }

    private function _testGet200Plain()
    {
        if (! $this->_isAvailable()) {

            $this->assertTrue(true);
            return;
        }

        $this->_getTest('code-200-plain.php', 34, 'text/html', 200, $this->_contents200Plain());
    }

    public function testGet404()
    {

        $this->_testGet404();
        $this->_testGet404();
    }

    private function _testGet404()
    {
        if (! $this->_isAvailable()) {

            $this->assertTrue(true);
            return;
        }

        try {

            $this->_getTest('code-404.php', 0, 'text/html', 404, null);

        } catch (Exception $e) {

            if (! $this->_sut instanceof ehough_shortstop_impl_exec_command_FopenCommand) {

                throw $e;
            }

            $this->assertTrue(true);
        }
    }

    protected function _getTest($path, $length, $type, $status, $expected, $message = null, $encoding = null)
    {
        $this->prepareForRequest();

        $context = new ehough_chaingang_impl_StandardContext();
        $request = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, $this->_server . "/$path");
        $request->setHeader(ehough_shortstop_api_HttpRequest::HTTP_HEADER_USER_AGENT, 'TubePress');
        $context->put('request', $request);

        $this->_closureVarRequest = $request;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_SELECTED, ehough_mockery_Mockery::on(array($this, '_callbackTestGet1')));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_INITIALIZED, ehough_mockery_Mockery::on(array($this, '_callbackTestGet2')));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_TORNDOWN, ehough_mockery_Mockery::on(array($this, '_callbackTestGet3')));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_SUCCESS, ehough_mockery_Mockery::on(array($this, '_callbackTestGet4')));

        $result = $this->_sut->execute($context);

        $this->assertTrue($result, "Command did not return true that it had handled request ($result)");

        $response = $context->get('response');

        $this->assertTrue($response instanceof ehough_shortstop_api_HttpResponse, 'Reponse is not of type HttpResponse');

        $actualContentType = $response->getHeaderValue(ehough_shortstop_api_HttpMessage::HTTP_HEADER_CONTENT_TYPE);
        $this->assertTrue($actualContentType === $type || $actualContentType === "$type; charset=utf-8" || $actualContentType === "$type; charset=UTF-8", "Expected Content-Type $type but got $actualContentType");

        $encoded = $response->getHeaderValue(ehough_shortstop_api_HttpMessage::HTTP_HEADER_CONTENT_ENCODING);
        $this->assertEquals($encoding, $encoded, "Expected encoding $encoding but got $encoded");

        $this->assertEquals($status, $response->getStatusCode(), "Expected status code $status but got " . $response->getStatusCode());

        $entity = $response->getEntity();
        $this->assertTrue($entity instanceof ehough_shortstop_api_HttpEntity);

        if ($response->getHeaderValue(ehough_shortstop_api_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING) === 'chunked') {

            $data = @http_chunked_decode($entity->getContent());

            if ($data === false) {

                $data = $entity->getContent();
            }

        } else {

            $data = $entity->getContent();
        }


        $this->assertEquals($expected, $data);
    }

    public function _callbackTestGet1($event)
    {
        return $event instanceof ehough_tickertape_GenericEvent && $event->getSubject() === $this->_sut && $event->getArgument('request') === $this->_closureVarRequest;
    }

    public function _callbackTestGet2($event)
    {
        return $event instanceof ehough_tickertape_GenericEvent && $event->getSubject() === $this->_sut && $event->getArgument('request') === $this->_closureVarRequest;
    }

    public function _callbackTestGet3($event)
    {
        return $event instanceof ehough_tickertape_GenericEvent && $event->getSubject() === $this->_sut && $event->getArgument('request') === $this->_closureVarRequest;
    }

    public function _callbackTestGet4($event)
    {
        return $event instanceof ehough_tickertape_GenericEvent && $event->getSubject() === $this->_sut && $event->getArgument('request') === $this->_closureVarRequest
            && $event->getArgument('response') instanceof ehough_shortstop_api_HttpResponse;
    }

    protected abstract function _getSutInstance(ehough_shortstop_spi_HttpMessageParser $mp, ehough_tickertape_EventDispatcherInterface $ed);

    protected abstract function _isAvailable();

    protected function prepareForRequest()
    {
        //override point
    }

    private function _contents200Plain()
    {
        return <<<EOT
random stuff!
here's another line

EOT;
    }
}