<?php

/**
 * Code from ReactPHP - http://reactphp.org/
 * Licensed under MIT license
 *
 * Copyright (c) 2012 Igor Wiedler
 */

namespace Neutron\TipTop\Timer;

interface TimerInterface
{
    /**
     * @api
     *
     * Returns the interval of the timer.
     */
    public function getInterval();

    /**
     * @api
     *
     * Returns the callback of the timer.
     */
    public function getCallback();

    /**
     * @api
     *
     * Attaches data to the timer.
     */
    public function setData($data);

    /**
     * @api
     *
     * Returns data attached to the timer.
     */
    public function getData();

    /**
     * Removes an iteration.
     */
    public function decrementIterations();

    /**
     * Returns remaining iterations.
     */
    public function getIterations();

    /**
     * @api
     *
     * Tells if the timer is periodic.
     */
    public function isPeriodic();

    /**
     * @api
     *
     * Tells if the timer is active.
     */
    public function isActive();

    /**
     * @api
     *
     * Cancels the timer.
     */
    public function cancel();
}
