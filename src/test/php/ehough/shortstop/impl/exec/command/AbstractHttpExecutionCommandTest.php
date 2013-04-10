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
    private static $_baseServerPath = 'http://tubepress.org/http_tests';

    /**
     * @var ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpMessageParser;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    private $_closureVarRequest;

    public function setUp()
    {
        $this->_mockHttpMessageParser = ehough_mockery_Mockery::mock('ehough_shortstop_spi_HttpMessageParser');
        $this->_mockEventDispatcher   = ehough_mockery_Mockery::mock('ehough_tickertape_EventDispatcherInterface');

        $this->_sut                   = $this->getSutInstance($this->_mockHttpMessageParser, $this->_mockEventDispatcher);

        $this->_mockHttpMessageParser->shouldReceive('getHeadersStringFromRawHttpMessage')->andReturnUsing(array($this, '_callbackSetup1'));
        $this->_mockHttpMessageParser->shouldReceive('getBodyStringFromRawHttpMessage')->andReturnUsing(array($this, '_callbackSetup2'));
        $this->_mockHttpMessageParser->shouldReceive('getArrayOfHeadersFromRawHeaderString')->andReturnUsing(array($this, '_callbackSetup3'));
        $this->_mockHttpMessageParser->shouldReceive('getHeaderArrayAsString')->andReturnUsing(array($this, '_callbackSetup4'));
    }

    public function testGet200Plain()
    {
        $this->_run200Test();
        $this->_run200Test();
    }

    public function testGet404()
    {
        $this->_run404Test();
        $this->_run404Test();
    }

    private function _run200Test()
    {
        /**
         * @var $response ehough_shortstop_api_HttpResponse
         */
        $response = $this->fetchRealResponse('code-200-plain.php');

        $this->assertEquals(200, $response->getStatusCode(), get_class($this->_sut) . ' reported the wrong status code');
        $this->assertStringStartsWith('text/html', $response->getEntity()->getContentType(), get_class($this->_sut) . ' reported the wrong content type');
        $this->assertEquals($this->_contents200Plain(), $response->getEntity()->getContent(), get_class($this->_sut) . ' reported the wrong content');
        $this->assertEquals(34, $response->getEntity()->getContentLength(), get_class($this->_sut) . ' reported the wrong content length');
    }

    private function _run404Test()
    {
        try {

            if ($this->_sut instanceof ehough_shortstop_impl_exec_command_FopenCommand) {

                $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_FAILURE,
                    ehough_mockery_Mockery::on(array($this, '_callbackFopenFailure')));
            }

            $response = $this->fetchRealResponse('code-404.php');

            $this->assertEquals(404, $response->getStatusCode());
            $this->assertStringStartsWith('text/html', $response->getEntity()->getContentType(), get_class($this->_sut) . ' reported the wrong content type');
            $this->assertEquals('', $response->getEntity()->getContent(), get_class($this->_sut) . ' reported the wrong content');
            $this->assertEquals(0, $response->getEntity()->getContentLength(), get_class($this->_sut) . ' reported the wrong content length');

        } catch (Exception $e) {

            if (! $this->_sut instanceof ehough_shortstop_impl_exec_command_FopenCommand) {

                throw $e;
            }

            if (!($e instanceof ehough_shortstop_api_exception_IException)) {

                throw $e;
            }
        }
    }

    protected function fetchRealResponse($path)
    {
        if (! $this->isAvailable()) {

            $this->markTestSkipped(get_class($this->_sut) . 'is not navailable');

            return false;
        }

        $this->prepareForRequest();

        $context = new ehough_chaingang_impl_StandardContext();
        $request = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, self::$_baseServerPath . "/$path");
        $request->setHeader(ehough_shortstop_api_HttpRequest::HTTP_HEADER_USER_AGENT, 'ehough/shortstop');
        $context->put('request', $request);

        $this->_closureVarRequest = $request;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_SELECTED, ehough_mockery_Mockery::on(array($this, '_callbackTestGet1')));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_INITIALIZED, ehough_mockery_Mockery::on(array($this, '_callbackTestGet2')));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_TORNDOWN, ehough_mockery_Mockery::on(array($this, '_callbackTestGet3')));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(ehough_shortstop_api_Events::TRANSPORT_SUCCESS, ehough_mockery_Mockery::on(array($this, '_callbackTestGet4')));

        $result = $this->_sut->execute($context);

        $this->assertTrue($result, get_class($this->_sut) . " did not return true that it had handled request ($result)");

        $response = $context->get('response');

        $this->assertTrue($response instanceof ehough_shortstop_api_HttpResponse, 'Response is not of type HttpResponse');

        if ($response->getHeaderValue('transfer-encoding') === 'chunked') {

            $decoder = new ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand();
            $chain = new ehough_chaingang_impl_StandardChain();
            $context = new ehough_chaingang_impl_StandardContext();
            $context->put('response', $response);
            $chain->addCommand($decoder);

            $result = $chain->execute($context);

            if (!$result) {

                throw new Exception('Unable to decode chunked-transfer encoded response');
            }

            $decoded = $context->get('response');
            $response->getEntity()->setContent($decoded);
            $response->getEntity()->setContentLength(strlen($decoded));
        }

        return $response;
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

    public function _callbackFopenFailure(ehough_tickertape_GenericEvent $event)
    {
        $ok = $event->getSubject() instanceof ehough_shortstop_impl_exec_command_FopenCommand
            && $event->getArgument('request') instanceof ehough_shortstop_api_HttpRequest
            && $event->getArgument('exception') instanceof ehough_shortstop_api_exception_RuntimeException;

        $event->setArgument('rethrow', true);

        return $ok;
    }

    protected abstract function getSutInstance(ehough_shortstop_spi_HttpMessageParser $mp, ehough_tickertape_EventDispatcherInterface $ed);

    protected abstract function isAvailable();

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