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

class ehough_shortstop_impl_exec_command_FsockOpenCommandTest extends ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommandTest
{

    protected function getSutInstance(ehough_shortstop_spi_HttpMessageParser $mp, ehough_tickertape_EventDispatcherInterface $ed)
    {
        return new ehough_shortstop_impl_exec_command_FsockOpenCommand($mp, $ed);
    }

    protected function isAvailable()
    {
        return true;
    }
}