<?php

class ehough_shortstop_impl_listeners_response_ResponseDecodingListener
{
    /**
     * @var ehough_epilog_psr_LoggerInterface
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('HTTP Response Logging Listener');
    }

    public function onResponse(ehough_tickertape_GenericEvent $event)
    {
        if (!$this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            return;
        }

        $response = $event->getSubject();
        $request  = $event->getArgument('request');

        $this->_logger->debug(sprintf('The raw result for %s is in the HTML source for this page <span style="display:none">%s</span>',
            $request, htmlspecialchars(var_export($response, true))));
    }
}