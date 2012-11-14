<?php

namespace Neutron\TipTop;

class Clock
{
    private $stack = array();

    public function __construct()
    {
        $this->initialize();
    }

    private function initialize()
    {
        declare(ticks = 1);
        pcntl_signal(SIGALRM, array($this, 'beat'), true);
        pcntl_alarm((int) 1);
    }

    public function beat()
    {
        $toReset = $toRemove = array();

        foreach ($this->stack as $moment => $timers) {
            if ($moment > time()) {
                break;
            }

            foreach ($timers as $timer) {
                call_user_func($timer->callback);
                $timer->iterations--;

                if ($timer->iterations > 0) {
                    if (!isset($toReset[$moment + $timer->period])) {
                        $toReset[$moment + $timer->period] = array();
                    }
                    array_push($toReset[$moment + $timer->period], $timer);
                }
                $toRemove[] = $moment;
            }
        }

        foreach ($toRemove as $moment) {
            unset($this->stack[$moment]);
        }

        $sort = false;

        foreach ($toReset as $moment => $timers) {
            if (!isset($this->stack[$moment])) {
                $this->stack[$moment] = array();
                $sort = true;
            }
            $this->stack[$moment] = array_merge($this->stack[$moment], $timers);
        }

        if ($sort === true) {
            ksort($this->stack);
        }

        pcntl_alarm(1);
    }

    public function set($seconds, $callback, $iterations = INF)
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

        array_push($this->stack[$moment], $data);
    }

    public function clear()
    {
        $this->stack = array();
    }
}
