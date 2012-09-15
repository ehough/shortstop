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

class ehough_shortstop_impl_HttpContentDecoderChainTest extends ehough_shortstop_impl_AbstractDecoderChainTest
{
    protected function buildSut(ehough_chaingang_api_Chain $chain)
    {
        return new ehough_shortstop_impl_HttpContentDecoderChain($chain);
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