# shortstop [![Build Status](https://secure.travis-ci.org/ehough/shortstop.png)](http://travis-ci.org/ehough/shortstop)

Fast and flexible HTTP client for PHP 5.2+.

### Motivation

Today we can choose from a number of excellent HTTP client libraries for PHP. However, they all assume a particular configuration of the underlying PHP installation. e.g. [Guzzle](http://guzzlephp.org/) requires [cURL](http://php.net/manual/en/book.curl.php). This
presents a problem if you want to ship code that can be run on an arbitrary PHP server.

`shortstop` mitigates this problem by dynamically selecting the best underlying HTTP mechanism for the server's environment. As a result, `shortstop` works on virtually any PHP 5.2+ installation.

### Features

* Compatible with PHP 5.2+
* Supports [cURL](http://php.net/manual/en/book.curl.php), PHP's [HTTP extension](http://php.net/manual/en/book.http.php), [fsockopen()](http://php.net/manual/en/function.fsockopen.php), [fopen()](http://php.net/manual/en/function.fopen.php), and [streams](http://www.php.net/manual/en/book.stream.php)
* HTTP 1.0 only (for now!)
* Strong support for HTTP content compression. Exact mechanism (deflate, gzip, etc) determined by server environment
* Built-in chunked-transfer decoding
* Highly configurable and extensible via an [event dispatcher compatible with Symfony's event dispatcher](https://github.com/ehough/tickertape)
* Heavily tested with excellent code coverage

### Usage

As you can see below, the trade-off for all the flexibility of `stash` is that assembling the components can be quite verbose. Using
an inversion-of-control container can help, as the actual client can be reused as a singleton. If, however, you'd like to use `stash` on its own then you can follow the code below.

```php
//build an event dispatcher
$eventDispatcher = new ehough_tickertape_EventDispatcher();  //implements ehough_tickertape_EventDispatcherInterface

//build an HTTP message parser
$httpMessageParser = new ehough_shortstop_impl_exec_DefaultHttpMessageParser();

//build a chain of HTTP transport mechanisms
$chain = new ehough_chaingang_impl_StandardChain();
$chain->addCommand(new ehough_shortstop_impl_exec_command_CurlCommand($httpMessageParser, $eventDispatcher));
$chain->addCommand(new ehough_shortstop_impl_exec_command_ExtCommand($httpMessageParser, $eventDispatcher));
$chain->addCommand(new ehough_shortstop_impl_exec_command_FopenCommand($httpMessageParser, $eventDispatcher));
$chain->addCommand(new ehough_shortstop_impl_exec_command_FsockOpenCommand($httpMessageParser, $eventDispatcher));
$chain->addCommand(new ehough_shortstop_impl_exec_command_StreamsCommand($httpMessageParser, $eventDispatcher));

//build the HTTP client
$client = new ehough_shortstop_impl_DefaultHttpClient($eventDispatcher, $chain);

//build a chain of HTTP content decoders
$contentDecodingChain = new ehough_chaingang_impl_StandardChain();
$contentDecodingChain->addCommand(new ehough_shortstop_impl_decoding_content_command_NativeGzipDecompressingCommand());
$contentDecodingChain->addCommand(new ehough_shortstop_impl_decoding_content_command_SimulatedGzipDecompressingCommand());
$contentDecodingChain->addCommand(new ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1950DecompressingCommand());
$contentDecodingChain->addCommand(new ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1951DecompressingCommand());

//build an HTTP content decoder
$httpContentDecoder = new ehough_shortstop_impl_decoding_content_HttpContentDecodingChain($contentDecodingChain);

//add some HTTP request listeners
$eventDispatcher->addListener(ehough_shortstop_api_Events::REQUEST,
    array(new ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener($httpContentDecoder), 'onPreRequest'));
$eventDispatcher->addListener(ehough_shortstop_api_Events::REQUEST,
    array(new ehough_shortstop_impl_listeners_request_RequestLoggingListener(), 'onPreRequest'));

//build a chain of HTTP transport decoders
$transferDecodingChain = new ehough_chaingang_impl_StandardChain();
$transferDecodingChain->addCommand(new ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand());

//build an HTTP transport decoder
$httpTransferDecoder = new ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain($transferDecodingChain);

//add some HTTP response listeners
$eventDispatcher->addListener(ehough_shortstop_api_Events::RESPONSE,
    array(new ehough_shortstop_impl_listeners_response_ResponseDecodingListener($httpTransferDecoder, 'Transfer'), 'onResponse'));
$eventDispatcher->addListener(ehough_shortstop_api_Events::RESPONSE,
    array(new ehough_shortstop_impl_listeners_response_ResponseDecodingListener($httpContentDecoder, 'Content'), 'onResponse'));
$eventDispatcher->addListener(ehough_shortstop_api_Events::RESPONSE,
    array(new ehough_shortstop_impl_listeners_response_ResponseLoggingListener(), 'onResponse'));

//build a new HTTP request
$request = new ehough_shortstop_api_HttpRequest('GET', 'https://github.com/');

//execute the request
$response = $client->execute($request);

$status = $response->getStatusCode();    //e.g. 200

$entity = $response->getEntity();        // instance of ehough_shortstop_api_HttpEntity
$type   = $entity->getContentType();     // e.g. text/html
$body   = $entity->getContent();         // the actual body of the response
```