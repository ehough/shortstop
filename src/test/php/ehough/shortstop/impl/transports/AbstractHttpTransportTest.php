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

abstract class ehough_shortstop_impl_transports_AbstractHttpTransportTest extends PHPUnit_Framework_TestCase
{
    private $_sut;
    private $_args;
    private $_server;
    private $_mockHttpMessageParser;

    function setUp()
    {
        parent::setUp();

        $this->_mockHttpMessageParser = Mockery::mock('ehough_shortstop_spi_HttpMessageParser');
        $this->_sut                   = $this->_getSutInstance($this->_mockHttpMessageParser);
        $this->_server                = 'http://tubepress.org/http_tests';

        $this->_mockHttpMessageParser->shouldReceive('getHeadersStringFromRawHttpMessage')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_DefaultHttpMessageParser();

            return $x->getHeadersStringFromRawHttpMessage($data);

        });

        $this->_mockHttpMessageParser->shouldReceive('getBodyStringFromRawHttpMessage')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_DefaultHttpMessageParser();

            return $x->getBodyStringFromRawHttpMessage($data);

        });

        $this->_mockHttpMessageParser->shouldReceive('getArrayOfHeadersFromRawHeaderString')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_DefaultHttpMessageParser();

            return $x->getArrayOfHeadersFromRawHeaderString($data);

        });

        $this->_mockHttpMessageParser->shouldReceive('getHeaderArrayAsString')->andReturnUsing(function ($data) {

            $x = new ehough_shortstop_impl_DefaultHttpMessageParser();

            return $x->getHeaderArrayAsString($data);
        });
    }

    function testGet200Plain()
    {
        $this->_getTest('code-200-plain.php', 34, 'text/html', 200, $this->_contents200Plain());
    }

    function testGet404()
    {
        try {

            $this->_getTest('code-404.php', 0, 'text/html', 404, null);

        } catch (Exception $e) {

            if (! $this->_sut instanceof ehough_shortstop_impl_transports_FopenTransport) {

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
        $context->put(ehough_shortstop_impl_HttpClientChain::CHAIN_KEY_REQUEST, $request);

        $result = $this->_sut->execute($context);

        $this->assertTrue($result, "Command did not return true that it had handled request ($result)");

        $response = $context->get(ehough_shortstop_impl_HttpClientChain::CHAIN_KEY_RESPONSE);

        $this->assertTrue($response instanceof ehough_shortstop_api_HttpResponse, 'Reponse is not of type HttpResponse');

        $actualContentType = $response->getHeaderValue(ehough_shortstop_api_HttpMessage::HTTP_HEADER_CONTENT_TYPE);
        $this->assertTrue($actualContentType === $type || $actualContentType === "$type; charset=utf-8", "Expected Content-Type $type but got $actualContentType");

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