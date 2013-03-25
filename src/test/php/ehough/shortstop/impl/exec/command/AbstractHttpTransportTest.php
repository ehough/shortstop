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

abstract class ehough_shortstop_impl_exec_command_AbstractHttpTransportTest extends PHPUnit_Framework_TestCase
{
    private $_sut;
    private $_args;
    private $_server;
    private $_mockHttpMessageParser;

    function setUp()
    {
        $this->_mockHttpMessageParser = Mockery::mock('ehough_shortstop_spi_HttpMessageParser');
        $this->_sut                   = $this->_getSutInstance($this->_mockHttpMessageParser);
        $this->_server                = 'http://tubepress.org/http_tests';

        $this->_mockHttpMessageParser->shouldReceive('getHeadersStringFromRawHttpMessage')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

            return $x->getHeadersStringFromRawHttpMessage($data);

        });

        $this->_mockHttpMessageParser->shouldReceive('getBodyStringFromRawHttpMessage')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

            return $x->getBodyStringFromRawHttpMessage($data);

        });

        $this->_mockHttpMessageParser->shouldReceive('getArrayOfHeadersFromRawHeaderString')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

            return $x->getArrayOfHeadersFromRawHeaderString($data);

        });

        $this->_mockHttpMessageParser->shouldReceive('getHeaderArrayAsString')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

            return $x->getHeaderArrayAsString($data);
        });
    }

    function testGet200Plain()
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

    function testGet404()
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

            if (! $this->_sut instanceof ehough_shortstop_impl_exec_command_FopenTransport) {

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

    protected abstract function _getSutInstance(ehough_shortstop_spi_HttpMessageParser $mp);

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