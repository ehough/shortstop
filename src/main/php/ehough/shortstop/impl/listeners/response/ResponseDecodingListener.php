<?php

class ehough_shortstop_impl_listeners_response_ResponseDecodingListener
{
    /**
     * @var ehough_epilog_psr_LoggerInterface
     */
    private $_logger;

    /**
     * @var ehough_shortstop_spi_HttpResponseDecoder
     */
    private $_responseDecoder;

    /**
     * @var string
     */
    private $_name;

    public function __construct(ehough_shortstop_spi_HttpResponseDecoder $httpResponseDecoder, $name)
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('HTTP Response Decoder Listener');

        $this->_responseDecoder = $httpResponseDecoder;
        $this->_name            = $name;
    }

    public function onResponse(ehough_tickertape_GenericEvent $event)
    {
        $isDebugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $response = $event->getSubject();

        if ($this->_responseDecoder->needsToBeDecoded($response)) {

            if ($isDebugEnabled) {

                $this->_logger->debug(sprintf('Response is %s-Encoded. Attempting decode.', $this->_name));
            }

            $this->_responseDecoder->decode($response);

            if ($isDebugEnabled) {

                $this->_logger->debug(sprintf('Successfully decoded %s-Encoded response.', $this->_name));
            }

        } else {

            if ($isDebugEnabled) {

                $this->_logger->debug(sprintf('Response is not %s-Encoded.', $this->_name));
            }
        }
    }
}