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

class ehough_shortstop_impl_HttpResponseHandlerChainTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_response;

    private $_entity;

    private $_chain;

    function setup()
    {
        $this->_chain = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_sut = new ehough_shortstop_impl_HttpResponseHandlerChain($this->_chain);
        $this->_response = new ehough_shortstop_api_HttpResponse();
        $this->_entity = new ehough_shortstop_api_HttpEntity();
    }

    function testNon200NobodyCouldHandle()
    {
        $this->_testNon200(false, 'An unknown HTTP error occurred. Please examine shortstop\'s debug output for further details');
    }

    function testNon200()
    {
        $this->_testNon200(true, 'this is an error message');
    }

    function test200()
    {
        $this->_entity->setContent('money money money');
        $this->_response->setStatusCode(200);
        $this->_response->setEntity($this->_entity);

        $result = $this->_sut->handle($this->_response);

        $this->assertEquals('money money money', $result);
    }

    private function _testNon200($status, $message)
    {
        $this->_response->setStatusCode(401);

        $response = $this->_response;

        $this->_chain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) use ($response, $message) {

            $arg->put(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_ERROR_MESSAGE, $message);

            return $arg instanceof ehough_chaingang_api_Context && $arg->get(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_RESPONSE) === $response;

        }))->andReturn($status);

        try {

            $this->_sut->handle($this->_response);

        } catch (Exception $e) {

            $this->assertEquals($message, $e->getMessage());
            return;
        }

        $this->fail('Did not throw exception');
    }

}