<?php

/**
 * Code from ReactPHP - http://reactphp.org/
 * Licensed under MIT license
 *
 * Copyright (c) 2012 Igor Wiedler
 */

namespace Neutron\TipTop\Timer;

use Neutron\TipTop\Clock;
use InvalidArgumentException;

class Timer implements TimerInterface
{
    protected $clock;
    protected $interval;
    protected $callback;
    protected $periodic;
    protected $periods;
    protected $data;

    public function __construct(Clock $clock, $interval, $callback, $periodic = false, $periods = INF, $data = null)
    {
        if (false === is_callable($callback)) {
            throw new InvalidArgumentException('The callback argument must be a valid callable object');
        }

        $this->clock = $clock;
        $this->interval = (int) $interval;
        $this->callback = $callback;
        $this->periodic = (bool) $periodic;

        if ($this->periodic) {
            $this->periods = $periods;
        }

        $this->data = $data;
    }

    public function getClock()
    {
        return $this->clock;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function removePeriod()
    {
        return $this->periods--;
    }

    public function getPeriods()
    {
        return $this->periods;
    }

    public function isPeriodic()
    {
        return $this->periodic;
    }

    public function isActive()
    {
        return $this->clock->isTimerActive($this);
    }

    public function cancel()
    {
        $this->clock->cancelTimer($this);
    }
}
