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

class ehough_shortstop_impl_transferencoding_ChunkTransferDecoderTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_response;

    private $_entity;

    function setup()
    {
        $this->_sut      = new ehough_shortstop_impl_transferencoding_ChunkedTransferDecoder();
        $this->_response = new ehough_shortstop_api_HttpResponse();
        $this->_entity   = new ehough_shortstop_api_HttpEntity();
    }

    /**
     * @expectedException ehough_shortstop_api_exception_InvalidArgumentException
     */
    function testDecodeBadData()
    {
        $tests = $this->_decodeTestArray();

        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(ehough_shortstop_impl_HttpTransferDecoderChain::CHAIN_KEY_RAW_RESPONSE, $this->_response);
        $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING, 'CHUNKED');

        $this->_entity->setContent('this is not encoded data\r\npoo');
        $this->_response->setEntity($this->_entity);

        $result = $this->_sut->execute($context);
    }

    function testDecodeNotChunked()
    {
        $tests = $this->_decodeTestArray();
        foreach ($tests as $decoded => $encoded) {

            $context = new ehough_chaingang_impl_StandardContext();
            $context->put(ehough_shortstop_impl_HttpTransferDecoderChain::CHAIN_KEY_RAW_RESPONSE, $this->_response);
            $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING, 'something else');

            $this->assertFalse($this->_sut->execute($context));
        }
    }

    function testDecode()
    {
        $tests = $this->_decodeTestArray();
        foreach ($tests as $decoded => $encoded) {

            $context = new ehough_chaingang_impl_StandardContext();
            $context->put(ehough_shortstop_impl_HttpTransferDecoderChain::CHAIN_KEY_RAW_RESPONSE, $this->_response);
            $this->_response->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING, 'chuNKEd');
            $this->_response->setEntity($this->_entity);
            $this->_entity->setContent($encoded);


            $result = $this->_sut->execute($context);

            $this->assertTrue($result);
            $contextDecoded = $context->get(ehough_shortstop_impl_HttpTransferDecoderChain::CHAIN_KEY_DECODED_RESPONSE);
            $this->assertEquals($decoded, $contextDecoded, var_export($contextDecoded, true) . " does not match expected " . var_export($decoded, true));
        }
    }

    //http://svn.php.net/viewvc/pecl/http/trunk/tests
    private function _decodeTestArray() {

        return array(
            <<<EOT
abra
cadabra
EOT
            => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0\r\nnothing\n",
            <<<EOT
abra
cadabra
EOT
            => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\n0\nhidden\n",
            <<<EOT
abra
cadabra
all we got

EOT
            => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
            <<<EOT
this string is chunked encoded

EOT
            => "05\r\nthis \r\n07\r\nstring \r\n12\r\nis chunked encoded\r\n01\r\n\n\r\n00",
            <<<EOT
this string is chunked encoder

EOT
            => "005   \r\nthis \r\n     07\r\nstring \r\n12     \r\nis chunked encoder\r\n   000001     \r\n\n\r\n00"
        );
    }
}
