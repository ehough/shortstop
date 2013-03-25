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

class ehough_shortstop_impl_decoding_content_HttpContentDecodingChainTest extends ehough_shortstop_impl_decoding_AbstractDecodingChainTest
{
    protected function buildSut(ehough_chaingang_api_Chain $chain)
    {
        return new ehough_shortstop_impl_decoding_content_HttpContentDecodingChain($chain);
    }

    protected function getHeaderName()
    {
        return ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING;
    }

    protected function getHeaderValue()
    {
        return 'chuNkEd';
    }

    function testGetAcceptEncodingHeader()
    {
        $this->assertEquals('gzip;q=1.0, deflate;q=0.5', $this->getSut()->getAcceptEncodingHeaderValue());
    }

}