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
/**
 * Decodes HTTP responses.
 */
interface ehough_shortstop_spi_HttpResponseDecoder
{
    /**
     * Determines if this response needs to be decoded.
     *
     * @param ehough_shortstop_api_HttpResponse $response The HTTP response.
     *
     * @return boolean True if this response should be decoded. False otherwise.
     */
    function needsToBeDecoded(ehough_shortstop_api_HttpResponse $response);

    /**
     * Decodes an HTTP response.
     *
     * @param ehough_shortstop_api_HttpResponse $response The HTTP response.
     *
     * @return void
     */
    function decode(ehough_shortstop_api_HttpResponse $response);
}