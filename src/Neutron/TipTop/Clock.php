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

class Clock
{
    private $stack = array();
    private $timers = array();

    public function __construct()
    {
        $this->initialize();
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
        return $this->set($interval, $callback, $iterations);
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
        return $this->set($interval, $callback, 1);
    }

    /**
     * Clears a timer by its signature. If no signature provided, all timers are cleared.
     *
     * @param  string $signature
     *
     * @return Clock
     */
    public function clear($signature = null)
    {
        if ($signature && isset($this->timers[$signature])) {
            unset($this->timers[$signature]);
        } else {
            $this->stack = $this->timers = array();
        }

        return $this;
    }

    /**
     * Internal method for the heartbeat, should not be used.
     */
    public function _beat()
    {
        $toReset = $toRemove = array();

        foreach ($this->stack as $moment => $signatures) {
            if ($moment > time()) {
                break;
            }

            foreach ($signatures as $signature) {
                if (!isset($this->timers[$signature])) {
                    // timer has been cleared
                    continue;
                }

                $timer = $this->timers[$signature];

                call_user_func($timer->callback);
                $timer->iterations--;

                if ($timer->iterations > 0) {
                    if (!isset($toReset[$moment + $timer->period])) {
                        $toReset[$moment + $timer->period] = array();
                    }
                    array_push($toReset[$moment + $timer->period], $signature);
                } else {
                    unset($this->timers[$signature]);
                }
            }

            $toRemove[] = $moment;
        }

        foreach ($toRemove as $moment) {
            unset($this->stack[$moment]);
        }

        $sort = false;

        foreach ($toReset as $moment => $signatures) {
            if (!isset($this->stack[$moment])) {
                $this->stack[$moment] = array();
                $sort = true;
            }
            $this->stack[$moment] = array_merge($this->stack[$moment], $signatures);
        }

        if ($sort === true) {
            ksort($this->stack);
        }

        pcntl_alarm(1);
    }

    private function initialize()
    {
        declare(ticks = 1);
        pcntl_signal(SIGALRM, array($this, '_beat'), true);
        pcntl_alarm((int) 1);
    }

    private function set($seconds, $callback, $iterations = INF)
    {
        $moment = time() + $seconds;

        if (!isset($this->stack[$moment])) {
            $this->stack[$moment] = array();
            ksort($this->stack);
        }

        $data = (object) array(
                'period'     => $seconds,
                'callback'   => $callback,
                'iterations' => $iterations,
        );

        $signature = $this->generateSignature();
        $this->timers[$signature] = $data;

        array_push($this->stack[$moment], $signature);

        return $signature;
    }

    private function generateSignature()
    {
        return uniqid('', true);
    }
}
