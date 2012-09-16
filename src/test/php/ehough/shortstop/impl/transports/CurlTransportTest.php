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

class ehough_shortstop_impl_transports_CurlTransportTest extends ehough_shortstop_impl_transports_AbstractHttpTransportTest {

    protected function _getSutInstance(ehough_shortstop_spi_HttpMessageParser $mp)
    {
        return new ehough_shortstop_impl_transports_CurlTransport($mp);
    }

    protected function _isAvailable()
    {
        return function_exists('curl_init');
    }
}