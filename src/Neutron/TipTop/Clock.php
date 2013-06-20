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
    /** @var Pulse */
    private $pulse;
    /** @var Boolean */
    private $paused = false;
    /** @var Timers */
    private $timers;
    /** @var Callable */
    private $pulseCallback;

    public function __construct(Pulse $pulse = null)
    {
        $this->timers = new Timers($this);
        $this->pulse = $pulse ?: Pulse::getInstance();
        $this->pulseCallback = array($this, 'tick');
        $this->pulse->on('tick', $this->pulseCallback);
    }

    /**
     * @return Timers
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * @param Timers $timers
     *
     * @return Clock
     */
    public function setTimers(Timers $timers)
    {
        $this->timers = $timers;

        return $this;
    }

    /**
     * @api
     *
     * Prepares the clock to be unset, releases contained objects,
     *
     * @return Clock
     */
    public function destroy()
    {
        $this->removeAllListeners();
        $this->timers->clear();
        $this->pulse->removeListener('tick', $this->pulseCallback);
        $this->timers = $this->pulse = $this->pulseCallback = null;
    }

    /**
     * @api
     *
     * Pauses the clock.
     *
     * While the clock is paused, no timers will be triggered.
     *
     * @return Clock
     */
    public function pause()
    {
        $this->paused = true;

        return $this;
    }

    /**
     * @api
     *
     * Resumes the clock.
     *
     * Timers will restart.
     *
     * @return \Neutron\TipTop\Clock
     */
    public function resume()
    {
        $this->paused = false;

        return $this;
    }

    /**
     * @api
     *
     * Returns true is the clock is paused
     *
     * @return Boolean
     */
    public function isPaused()
    {
        return $this->paused;
    }

    /**
     * @api
     *
     * Blocks until all timers have been triggered.
     *
     * Be carefull, by running this method with infinite periodic timers, it
     * will block forever unless timers cancel themselves.
     */
    public function block()
    {
        while(!$this->timers->isEmpty()) {
            usleep(1000);
        }
    }

    /**
     * @api
     *
     * Adds a timer (triggered one time, when the interval is expired).
     *
     * @param  integer  $interval The interval of the timer in seconds
     * @param  callable $callback Any callable
     *
     * @return TimerInterface The timer
     */
    public function addTimer($interval, $callback)
    {
        $timer = new Timer($this, $interval, $callback, false);
        $this->timers->add($timer);

        return $timer;
    }

    /**
     * @api
     *
     * Adds a periodic timer (triggered every interval).
     *
     * @param  integer  $interval   The interval of the timer in seconds
     * @param  callable $callback   Any callable
     * @param  integer  $iterations The number of time the callback must be triggered, infinite by default
     *
     * @return TimerInterface The timer
     */
    public function addPeriodicTimer($interval, $callback, $iterations = INF)
    {
        $timer = new Timer($this, $interval, $callback, true, $iterations);
        $this->timers->add($timer);

        return $timer;
    }

    /**
     * Alias for cancelTimer
     *
     * @param TimerInterface $timer
     */
    public function clear(TimerInterface $timer)
    {
        $this->cancelTimer($timer);

        return $this;
    }

    /**
     * Stops the given timer.
     *
     * @param TimerInterface $timer
     *
     * @return Clock
     */
    public function cancelTimer(TimerInterface $timer)
    {
        $this->timers->cancel($timer);

        return $this;
    }

    /**
     * @api
     *
     * Removes all timers.
     *
     * @return Clock
     */
    public function cancelTimers()
    {
        $this->timers->clear();

        return $this;
    }

    /**
     * Returns true if the timer is active.
     *
     * @param TimerInterface $timer
     *
     * @return Boolean
     */
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
