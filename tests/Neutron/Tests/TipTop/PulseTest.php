<?php

namespace Neutron\Tests\TipTop;

use Neutron\TipTop\Pulse;

class PulseTest extends \PHPUnit_Framework_TestCase
{
    public function testTickShouldEmitATick()
    {
        $pulse = new Pulse();
        $boolean = false;
        $pulse->on('tick', function () use (&$boolean) {
            $boolean = true;
        });
        $pulse->tick();

        $this->assertTrue($boolean);
    }

    public function testStop()
    {
        $pulse = new Pulse();
        $this->assertFalse($pulse->isStopped());
        $pulse->stop();
        $this->assertTrue($pulse->isStopped());
        $pulse->start();
        $this->assertFalse($pulse->isStopped());
    }

    public function testBeatShouldEmitIfNotStopped()
    {
        $pulse = new Pulse();
        $boolean = false;
        $pulse->on('tick', function () use (&$boolean) {
            $boolean = true;
        });
        $pulse->beat();

        $this->assertTrue($boolean);
    }

    public function testBeatShouldNotEmitIfStopped()
    {
        $pulse = new Pulse();
        $pulse->stop();
        $boolean = false;
        $pulse->on('tick', function () use (&$boolean) {
            $boolean = true;
        });
        $pulse->beat();

        $this->assertFalse($boolean);
    }

    public function testGetInstanceShouldReturnTheSamePulse()
    {
        $this->assertSame(Pulse::getInstance(), Pulse::getInstance());
    }
}
