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

require_once(__DIR__ . '/../../../../../resources/data.inc.txt');

abstract class ehough_shortstop_impl_contentencoding_AbstractDecompressorTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_context;

    private $_response;

    function setUp()
    {
        $this->_sut = $this->buildSut();

        $this->_response = new ehough_shortstop_api_HttpResponse();

        $this->_context = new ehough_chaingang_impl_StandardContext();

        $this->_context->put(ehough_shortstop_impl_HttpContentDecoderChain::CHAIN_KEY_RAW_RESPONSE, $this->_response);
        ob_start();
    }

    function tearDown()
    {
        ob_end_clean();
    }

    function testCannotDecompressObject()
    {
        $entity   = new ehough_shortstop_api_HttpEntity();
        $this->_response->setEntity($entity);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());
        $entity->setContent($this->_sut);

        $result = $this->_sut->execute($this->_context);

        $this->assertFalse($result);
    }


    function testCannotDecompressReservedBitsSet()
    {
        global $data;

        $toSend = $this->getCompressed($data, 9);
        $entity = new ehough_shortstop_api_HttpEntity();

        $toSend[3] = "\x11";

        $this->_response->setEntity($entity);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());

        $entity->setContent($toSend);
        $this->_sut->execute($this->_context);

        $this->assertTrue(true);
    }

    function testCannotDecompressString()
    {
        $entity   = new ehough_shortstop_api_HttpEntity();

        $this->_response->setEntity($entity);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());
        $entity->setContent('something that cannot be decompressed');
        $result = $this->_sut->execute($this->_context);

        $this->assertFalse($result);
    }

    function testNoContentEncodingHeader()
    {
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, null);

        $this->assertFalse($this->_sut->execute($this->_context));
    }

    function testCompress()
    {

        for ($x = 1; $x < 10; $x++) {

            $this->_testCompress($x);
        }

    }

    function _testCompress($level)
    {
        global $data;

        $entity   = new ehough_shortstop_api_HttpEntity();

        $this->_response->setEntity($entity);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());
        $entity->setContent($this->getCompressed($data, $level));

        $result = $this->_sut->execute($this->_context);

        $this->assertTrue($result);

        $decoded = $this->_context->get(ehough_shortstop_impl_HttpContentDecoderChain::CHAIN_KEY_DECODED_RESPONSE);
        $this->assertNotNull($decoded);

        $this->assertEquals($data, $decoded);
    }

    protected abstract function buildSut();

    protected abstract function getHeaderValue();

    protected abstract function getCompressed($data, $level);

    protected function getContext()
    {
        return $this->_context;
    }

    protected function getResponse()
    {
        return $this->_response;
    }

    protected function getSut()
    {
        return $this->_sut;
    }
}