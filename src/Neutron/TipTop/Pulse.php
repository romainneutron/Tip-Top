<?php

namespace Neutron\TipTop;

use Evenement\EventEmitter;

class Pulse extends EventEmitter
{
    private static $instance;
    private $stopped = false;

    public function __construct()
    {
        pcntl_signal(SIGALRM, array($this, 'beat'), true);
        $this->start();
    }

    public function start()
    {
        $this->stopped = false;
        $this->beat();
    }

    public function tick()
    {
        $this->emit('tick', array($this));
    }

    public function stop()
    {
        $this->stopped = true;
    }

    public function isStopped()
    {
        return $this->stopped;
    }

    public function beat()
    {
        if (false === $this->stopped) {
            pcntl_alarm((int) 1);
            $this->tick();
        }
    }

    public static function getInstance()
    {
        if (false === static::$instance instanceof static) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
