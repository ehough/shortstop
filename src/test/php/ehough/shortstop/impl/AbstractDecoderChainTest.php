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

abstract class ehough_shortstop_impl_AbstractDecoderChainTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_response;

    private $_entity;

    private $_chain;

    function setup()
    {
        $this->_chain    = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_sut      = $this->buildSut($this->_chain);
        $this->_response = new ehough_shortstop_api_HttpResponse();
        $this->_entity   = new ehough_shortstop_api_HttpEntity();
    }

    function testCannotDecode()
    {
        $ctx = $this->_response;

        $this->_chain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) use ($ctx) {

            return $ctx === $arg->get(ehough_shortstop_impl_HttpTransferDecoderChain::CHAIN_KEY_RAW_RESPONSE);

        }))->andReturn(false);

        $this->_sut->decode($this->_response);

        $this->assertTrue(true);
    }

    function testDecode()
    {
        $response = $this->_response;
        $response->setEntity($this->_entity);
        $response->setHeader(ehough_shortstop_api_HttpMessage::HTTP_HEADER_CONTENT_TYPE, 'fooey');

        $this->_chain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) use ($response) {

            $arg->put(ehough_shortstop_impl_HttpTransferDecoderChain::CHAIN_KEY_DECODED_RESPONSE, 'decodeddecoded');

            return $response === $arg->get(ehough_shortstop_impl_HttpTransferDecoderChain::CHAIN_KEY_RAW_RESPONSE);

        }))->andReturn(true);

        $this->_sut->decode($this->_response);

        $this->assertEquals('decodeddecoded', $this->_response->getEntity()->getContent());
        $this->assertTrue($this->_response->getEntity()->getContentLength() === 14);
    }

    function testIsEncoded()
    {
        $this->_entity->setContent('something');
        $this->_response->setHeader($this->getHeaderName(), 'anything');
        $this->_response->setEntity($this->_entity);

        $this->assertTrue($this->_sut->needsToBeDecoded($this->_response));
    }

    function testIsEncodedNoHeader()
    {
        $this->_response->setEntity($this->_entity);
        $this->_entity->setContent('something');
        $this->_response->setHeader($this->getHeaderName(), null);
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    function testIsEncodedEmptyContent()
    {
        $this->_response->setEntity($this->_entity);
        $this->_entity->setContent('');
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    function testIsEncodedNullContent()
    {
        $this->_response->setEntity($this->_entity);
        $this->_entity->setContent(null);
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    protected abstract function getHeaderName();

    protected abstract function buildSut(ehough_chaingang_api_Chain $chain);

    protected function getSut()
    {
        return $this->_sut;
    }
}
