# shortstop [![Build Status](https://secure.travis-ci.org/ehough/shortstop.png)](http://travis-ci.org/ehough/shortstop)

Fast and flexible HTTP client for PHP 5.2 and above

### Features

* Compatible with PHP 5.2+
* Supports [cURL](http://php.net/manual/en/book.curl.php), PHP's [HTTP extension](http://php.net/manual/en/book.http.php), [fsockopen()](http://php.net/manual/en/function.fsockopen.php), [fopen()](http://php.net/manual/en/function.fopen.php), and [streams](http://www.php.net/manual/en/book.stream.php)
* HTTP 1.0 only (for now!)
* Strong support for HTTP content compression. Exact mechanism (deflate, gzip, etc) determined by server environment
* Built-in chunked-transfer decoding
* Highly configurable and extensible via an [event dispatcher compatible with Symfony's event dispatcher](https://github.com/ehough/tickertape)
* Heavily tested with excellent code coverage