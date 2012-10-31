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
class ehough_shortstop_impl_contentencoding_NativeDeflateRfc1950DecompressorTest extends ehough_shortstop_impl_contentencoding_AbstractDecompressorTest
{
    protected function buildSut()
    {
        return new ehough_shortstop_impl_contentencoding_NativeDeflateRfc1950Decompressor();
    }

    protected function getHeaderValue()
    {
        return 'deflate';
    }

    protected function getCompressed($data, $level)
    {
        return gzcompress($data, $level);
    }
}