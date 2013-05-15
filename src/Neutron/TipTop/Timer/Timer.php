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
    protected $iterations;
    protected $data;

    public function __construct(Clock $clock, $interval, $callback, $periodic = false, $iterations = INF, $data = null)
    {
        if (false === is_callable($callback)) {
            throw new InvalidArgumentException('The callback argument must be a valid callable object');
        }

        $this->clock = $clock;
        $this->interval = (int) $interval;
        $this->callback = $callback;
        $this->periodic = (bool) $periodic;

        if ($this->periodic) {
            $this->iterations = $iterations;
        }

        $this->data = $data;
    }

    /**
     * @return Clock
     */
    public function getClock()
    {
        return $this->clock;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function decrementIterations()
    {
        return $this->iterations--;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * {@inheritdoc}
     */
    public function isPeriodic()
    {
        return $this->periodic;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->clock->isTimerActive($this);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel()
    {
        $this->clock->cancelTimer($this);
    }
}
