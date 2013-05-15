<?php

/**
 * Code from ReactPHP - http://reactphp.org/
 * Licensed under MIT license
 *
 * Copyright (c) 2012 Igor Wiedler
 */

namespace Neutron\TipTop\Timer;

use SplObjectStorage;
use SplPriorityQueue;
use InvalidArgumentException;
use Neutron\TipTop\Clock;

class Timers
{
    const MIN_RESOLUTION = 1;

    private $time;
    private $timers;
    private $scheduler;

    public function __construct(Clock $clock)
    {
        $this->timers = new SplObjectStorage();
        $this->scheduler = new SplPriorityQueue();

        $clock->on('tick', array($this, 'tick'));
    }

    public function updateTime()
    {
        return $this->time = round(microtime(true), 1);
    }

    public function getTime()
    {
        return $this->time ?: $this->updateTime();
    }

    public function add(TimerInterface $timer)
    {
        $interval = $timer->getInterval();

        if ($interval < self::MIN_RESOLUTION) {
            throw new InvalidArgumentException('Timer events do not support sub-second timeouts.');
        }

        $scheduledAt = $interval + $this->getTime();

        $this->timers->attach($timer, $scheduledAt);
        $this->scheduler->insert($timer, -$scheduledAt);
    }

    public function contains(TimerInterface $timer)
    {
        return $this->timers->contains($timer);
    }

    public function cancel(TimerInterface $timer)
    {
        $this->timers->detach($timer);
    }

    public function clear()
    {
        $timers = array();
        foreach ($this->timers as $timer) {
            $timers[] = $timer;
        }
        foreach($timers as $timer) {
            $timer->cancel();
        }
    }

    public function getFirst()
    {
        if ($this->scheduler->isEmpty()) {
            return null;
        }

        $scheduledAt = $this->timers[$this->scheduler->top()];

        return $scheduledAt;
    }

    public function isEmpty()
    {
        return count($this->timers) === 0;
    }

    public function tick()
    {
        $time = $this->updateTime();
        $timers = $this->timers;
        $scheduler = $this->scheduler;

        while (!$scheduler->isEmpty()) {
            $timer = $scheduler->top();

            if (!isset($timers[$timer])) {
                $scheduler->extract();
                $timers->detach($timer);

                continue;
            }

            if ($timers[$timer] > $time) {
                break;
            }

            $scheduler->extract();
            call_user_func($timer->getCallback(), $timer);

            if ($timer->isPeriodic() && isset($timers[$timer])) {
                $timer->removePeriod();
                if (0 < $timer->getPeriods()) {
                    $timers[$timer] = $scheduledAt = $timer->getInterval() + $time;
                    $scheduler->insert($timer, -$scheduledAt);
                } else {
                    $timers->detach($timer);
                }
            } else {
                $timers->detach($timer);
            }
        }
    }
}