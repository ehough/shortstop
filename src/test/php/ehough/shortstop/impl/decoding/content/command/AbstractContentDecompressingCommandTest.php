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

require_once(__DIR__ . '/../../../../../../../resources/data.inc.txt');

abstract class ehough_shortstop_impl_decoding_content_command_AbstractContentDecompressingCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ehough_shortstop_impl_decoding_content_command_AbstractContentDecompressingCommand
     */
    private $_sut;

    /**
     * @var ehough_chaingang_api_Context
     */
    private $_context;

    /**
     * @var ehough_shortstop_api_HttpResponse
     */
    private $_response;

    public function setUp()
    {
        $this->_sut = $this->buildSut();

        $this->_response = new ehough_shortstop_api_HttpResponse();

        $this->_context = new ehough_chaingang_impl_StandardContext();

        $this->_context->put('response', $this->_response);

        ob_start();
    }

    public function tearDown()
    {
        ob_end_clean();
    }

    public function testCannotDecompressObject()
    {
        $entity   = new ehough_shortstop_api_HttpEntity();
        $this->_response->setEntity($entity);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());
        $entity->setContent($this->_sut);

        $result = $this->_sut->execute($this->_context);

        $this->assertFalse($result);
    }


    public function testCannotDecompressReservedBitsSet()
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

    public function testCannotDecompressString()
    {
        $entity   = new ehough_shortstop_api_HttpEntity();

        $this->_response->setEntity($entity);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());
        $entity->setContent('something that cannot be decompressed');
        $result = $this->_sut->execute($this->_context);

        $this->assertFalse($result);
    }

    public function testNoContentEncodingHeader()
    {
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, null);

        $this->assertFalse($this->_sut->execute($this->_context));
    }

    public function testCompress()
    {
        for ($x = 1; $x < 10; $x++) {

            $this->setUp();

            try {
                $this->_testCompress($x);
            } catch (Exception $e) {

                $this->fail('Compression failed at level ' . $x . ': ' . $e->getMessage());
            }
        }
    }

    public function _testCompress($level)
    {
        global $data;

        $entity = new ehough_shortstop_api_HttpEntity();

        $this->_response->setEntity($entity);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());
        $entity->setContent($this->getCompressed($data, $level));

        $result = $this->_sut->execute($this->_context);

        $this->assertTrue($result);

        $decoded = $this->_context->get('response');
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