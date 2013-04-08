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

abstract class ehough_shortstop_impl_decoding_AbstractDecodingChainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ehough_shortstop_impl_decoding_AbstractDecodingChain
     */
    private $_sut;

    /**
     * @var ehough_shortstop_api_HttpResponse
     */
    private $_response;

    /**
     * @var ehough_shortstop_api_HttpEntity
     */
    private $_entity;

    /**
     * @var ehough_chaingang_api_Chain
     */
    private $_chain;

    private $_closureVarCtx;
    private $_closureVarResponse;

    public function setup()
    {
        $this->_chain    = ehough_mockery_Mockery::mock('ehough_chaingang_api_Chain');
        $this->_sut      = $this->buildSut($this->_chain);
        $this->_response = new ehough_shortstop_api_HttpResponse();
        $this->_entity   = new ehough_shortstop_api_HttpEntity();
    }

    public function testCannotDecode()
    {
        $ctx = $this->_response;

        $this->_closureVarCtx = $ctx;

        $this->_chain->shouldReceive('execute')->once()->with(ehough_mockery_Mockery::on(array($this, '_callbackTestCannotDecode')))->andReturn(false);

        $this->_sut->decode($this->_response);

        $this->assertTrue(true);
    }

    public function _callbackTestCannotDecode($arg)
    {
        return $this->_closureVarCtx === $arg->get('response');
    }

    public function testDecode()
    {
        $response = $this->_response;
        $response->setEntity($this->_entity);
        $response->setHeader(ehough_shortstop_api_HttpMessage::HTTP_HEADER_CONTENT_TYPE, 'fooey');

        $this->_closureVarResponse = $response;

        $this->_chain->shouldReceive('execute')->once()->with(ehough_mockery_Mockery::on(array($this, '_callbackTestDecode')))->andReturn(true);

        $this->_sut->decode($this->_response);

        $this->assertEquals('decodeddecoded', $this->_response->getEntity()->getContent());
        $this->assertTrue($this->_response->getEntity()->getContentLength() === 14);
    }

    public function _callbackTestDecode($arg)
    {
        $ok = $this->_closureVarResponse === $arg->get('response');

        $arg->put('response', 'decodeddecoded');

        return $ok;
    }

    public function testIsEncoded()
    {
        $this->_entity->setContent('something');
        $this->_response->setHeader($this->getHeaderName(), 'anything');
        $this->_response->setEntity($this->_entity);

        $this->assertTrue($this->_sut->needsToBeDecoded($this->_response));
    }

    public function testIsEncodedNoHeader()
    {
        $this->_response->setEntity($this->_entity);
        $this->_entity->setContent('something');
        $this->_response->setHeader($this->getHeaderName(), null);
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    public function testIsEncodedEmptyContent()
    {
        $this->_response->setEntity($this->_entity);
        $this->_entity->setContent('');
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    public function testIsEncodedNullContent()
    {
        $this->_response->setEntity($this->_entity);
        $this->_entity->setContent(null);
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    protected abstract function getHeaderName();

    protected abstract function buildSut(ehough_chaingang_api_Chain $chain);

    /**
     * @return ehough_shortstop_impl_decoding_AbstractDecodingChain
     */
    protected function getSut()
    {
        return $this->_sut;
    }
}
