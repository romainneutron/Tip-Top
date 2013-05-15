<?php

/**
 * This file is part of TipTop.
 *
 * (c) Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Neutron\TipTop;

use Evenement\EventEmitter;
use Neutron\TipTop\Timer\Timer;
use Neutron\TipTop\Timer\Timers;
use Neutron\TipTop\Timer\TimerInterface;

class Clock extends EventEmitter
{
    private $pulse;
    private $paused = false;
    private $timers;
    private $pulseCallback;

    public function __construct(Pulse $pulse = null)
    {
        $this->timers = new Timers($this);
        $this->pulse = $pulse ?: Pulse::getInstance();
        $this->pulseCallback = array($this, 'tick');
        $this->pulse->on('tick', $this->pulseCallback);
    }

    public function getTimers()
    {
        return $this->timers;
    }

    public function setTimers(Timers $timers)
    {
        $this->timers = $timers;

        return $this;
    }

    public function destroy()
    {
        $this->timers->clear();
        $this->pulse->removeListener('tick', $this->pulseCallback);
        $this->timers = $this->pulse = null;
    }

    public function pause()
    {
        $this->paused = true;
    }

    public function resume()
    {
        $this->paused = false;
    }

    public function isPaused()
    {
        return $this->paused;
    }

    public function block()
    {
        while(!$this->timers->isEmpty()) {
            usleep(1000);
        }
    }

    /**
     * Adds a timer (triggered one time, when the interval is expired).
     *
     * @param  integer  $interval The interval of the timer in seconds
     * @param  callable $callback Any callable
     *
     * @return string The signature of the timer
     */
    public function addTimer($interval, $callback)
    {
        $timer = new Timer($this, $interval, $callback, false);
        $this->timers->add($timer);

        return $timer;
    }

    /**
     * Adds a periodic timer (triggered every interval).
     *
     * @param  integer  $interval   The interval of the timer in seconds
     * @param  callable $callback   Any callable
     * @param  integer  $iterations The number of time the callback must be triggered, infinite by default
     *
     * @return string The signature of the timer
     */
    public function addPeriodicTimer($interval, $callback, $iterations = INF)
    {
        $timer = new Timer($this, $interval, $callback, true, $iterations);
        $this->timers->add($timer);

        return $timer;
    }

    public function clear(TimerInterface $timer)
    {
        $this->cancelTimer($timer);
    }

    public function cancelTimer(TimerInterface $timer)
    {
        $this->timers->cancel($timer);
    }

    public function cancelTimers()
    {
        $this->timers->clear();
    }

    public function isTimerActive(TimerInterface $timer)
    {
        return $this->timers->contains($timer);
    }

    /**
     * Internal method for the heartbeat, should not be used.
     */
    public function tick()
    {
        if (!$this->paused) {
            $this->emit('tick', array($this));
        }
    }
}
