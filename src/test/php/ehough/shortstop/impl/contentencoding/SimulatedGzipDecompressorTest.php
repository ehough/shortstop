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