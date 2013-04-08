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
     * Fired when an HTTP transport is selected. Allows event listeners to veto the selection.
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg ok      boolean
     * @arg request ehough_shortstop_api_HttpRequest
     */
    const TRANSPORT_SELECTED = 'ehough.shortstop.exec.transport.selected';

    /**
     * Fired after an HTTP transport is initialized to handle a request.
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request ehough_shortstop_api_HttpRequest
     */
    const TRANSPORT_INITIALIZED = 'ehough.shortstop.exec.transport.initialized';

    /**
     * Fired after an HTTP transport successfully handles a request.
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request  ehough_shortstop_api_HttpRequest
     * @arg response ehough_shortstop_api_HttpResponse
     */
    const TRANSPORT_SUCCESS = 'ehough.shortstop.exec.transport.success';

    /**
     * Fired after an HTTP transport hits an error.
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request  ehough_shortstop_api_HttpRequest
     * @arg response ehough_shortstop_api_HttpResponse
     */
    const TRANSPORT_FAILURE = 'ehough.shortstop.exec.transport.failure';

    /**
     * Fired after an HTTP transport is deconstructed.
     *
     * @subject ehough_shortstop_impl_exec_command_AbstractHttpExecutionCommand
     *
     * @arg request ehough_shortstop_api_HttpRequest
     */
    const TRANSPORT_TORNDOWN = 'ehough.shortstop.exec.transport.torndown';
}