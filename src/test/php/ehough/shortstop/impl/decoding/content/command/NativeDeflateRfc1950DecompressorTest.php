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
class ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1950DecompressorTest extends ehough_shortstop_impl_decoding_content_command_AbstractContentDecompressingCommandTest
{
    protected function buildSut()
    {
        return new ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1950DecompressingCommand();
    }

    protected function getHeaderValue()
    {
        return 'deflate';
    }

    protected function getCompressed($data, $level)
    {
        return gzcompress($data, $level);
    }


    protected function isAvailable()
    {
        return function_exists('gzcompress') && function_exists('gzuncompress');
    }
}