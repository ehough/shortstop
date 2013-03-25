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
class ehough_shortstop_impl_contentencoding_SimulatedGzipDecompressorTest extends ehough_shortstop_impl_contentencoding_AbstractDecompressorTest
{
    protected function buildSut()
    {
        return new ehough_shortstop_impl_contentencoding_SimulatedGzipDecompressor();
    }

    protected function getHeaderValue()
    {
        return 'gzip';
    }

    protected function getCompressed($data, $level)
    {
        return gzencode($data, $level);
    }

    function testDecompressFile()
    {
        global $data;

        $compressed = file_get_contents(dirname(__FILE__) . '/../../../../../resources/data.txt.gz');

        $entity   = new ehough_shortstop_api_HttpEntity();
        $entity->setContent($compressed);
        $this->getResponse()->setEntity($entity);
        $this->getResponse()->setHeader(ehough_shortstop_api_HttpResponse::HTTP_HEADER_CONTENT_ENCODING, $this->getHeaderValue());

        $result = $this->getSut()->execute($this->getContext());

        $this->assertTrue($result);

        $decoded = $this->getContext()->get(ehough_shortstop_impl_HttpContentDecoderChain::CHAIN_KEY_DECODED_RESPONSE);
        $this->assertNotNull($decoded);

        $this->assertEquals($data, $decoded);
    }
}