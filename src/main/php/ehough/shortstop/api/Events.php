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

/**
 * Registry of all HTTP events.
 */
class ehough_shortstop_api_Events
{
    /**
     * Fired immediately before a request is executed.
     *
     * @subject ehough_shortstop_api_HttpRequest
     */
    const REQUEST = 'ehough.shortstop.request';

    /**
     * Fired when a response is returned.
     *
     * @subject ehough_shortstop_api_HttpResponse
     *
     * @arg request ehough_shortstop_api_HttpRequest
     */
    const RESPONSE = 'ehough.shortstop.response';

    /**
     * Fired when
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg ok      boolean
     * @arg request ehough_shortstop_api_HttpRequest
     */
    const TRANSPORT_SELECTED = 'ehough.shortstop.exec.transport.selected';

    /**
     * Fired when
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request ehough_shortstop_api_HttpRequest
     */
    const TRANSPORT_INITIALIZED = 'ehough.shortstop.exec.transport.initialized';

    /**
     * Fired when
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request  ehough_shortstop_api_HttpRequest
     * @arg response ehough_shortstop_api_HttpResponse
     */
    const TRANSPORT_SUCCESS = 'ehough.shortstop.exec.transport.success';

    /**
     * Fired when
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request  ehough_shortstop_api_HttpRequest
     * @arg response ehough_shortstop_api_HttpResponse
     */
    const TRANSPORT_FAILURE = 'ehough.shortstop.exec.transport.failure';

    /**
     * Fired when
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request ehough_shortstop_api_HttpRequest
     */
    const TRANSPORT_TORNDOWN = 'ehough.shortstop.exec.transport.torndown';
}