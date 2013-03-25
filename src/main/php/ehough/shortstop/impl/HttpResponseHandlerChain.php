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

/**
 * Handles HTTP responses.
 */
class ehough_shortstop_impl_HttpResponseHandlerChain implements ehough_shortstop_api_HttpResponseHandler
{
    /**
     * Error message chain key.
     */
    const CHAIN_KEY_ERROR_MESSAGE = 'message';

    /**
     * Repsonse chain key.
     */
    const CHAIN_KEY_RESPONSE = 'response';

    /** @var \ehough_chaingang_api_Chain */
    private $_chain;

    /** @var \ehough_epilog_api_ILogger */
    private $_logger;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_chain  = $chain;
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('HTTP Response Handler Chain');
    }

    /**
     * Handles an HTTP response.
     *
     * @param ehough_shortstop_api_HttpResponse $response The HTTP response.
     *
     * @return string The raw entity body of the response. May be empty or null.
     */
    public final function handle(ehough_shortstop_api_HttpResponse $response)
    {
        $statusCode = $response->getStatusCode();

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Response returned status %d', $statusCode));
        }

        switch ($statusCode) {

            case 200:

                return $this->_handleSuccess($response);

            default:

                $this->_handleError($response);

                return null;
        }
    }

    private function _handleError(ehough_shortstop_api_HttpResponse $response)
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(self::CHAIN_KEY_RESPONSE, $response);

        $result = $this->_chain->execute($context);

        if ($result !== true) {

            throw new ehough_shortstop_api_exception_RuntimeException('An unknown HTTP error occurred. Please examine shortstop\'s debug output for further details');
        }

        throw new ehough_shortstop_api_exception_RuntimeException($context->get(self::CHAIN_KEY_ERROR_MESSAGE));
    }

    private function _handleSuccess(ehough_shortstop_api_HttpResponse $response)
    {
        $entity = $response->getEntity();

        if ($entity !== null) {

            return $entity->getContent();
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Null entity in response');
        }

        return '';
    }
}