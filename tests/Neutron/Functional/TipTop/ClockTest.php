<?php

namespace Neutron\Functional\TipTop;

use Neutron\TipTop\Clock;
use Neutron\TipTop\Pulse;

class ClockTest extends \PHPUnit_Framework_TestCase
{
    public function testSetSingle()
    {
        declare (ticks=1);
        $clock = new Clock(new Pulse());
        $boolean = false;

        $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });

        usleep(1350000);
        $this->assertTrue($boolean);
    }

    public function testSetMultiple()
    {
        $clock = new Clock(new Pulse());
        $stack = array();
        $start = microtime(true);

        $clock->addPeriodicTimer(1, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bip';
        });
        $clock->addPeriodicTimer(2, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bop';
        });
        $clock->addTimer(1, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bup';
        });

        $n = 50;
        while ($n > 0) {
            $n--;
            usleep(100000);
        }

        $this->assertEquals(array('1bip', '1bup', '2bip', '2bop', '3bip', '4bop', '4bip', '5bip'), $stack);
    }

    public function testClearSignature()
    {
        $clock = new Clock(new Pulse());
        $boolean = false;

        $timer = $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });

        usleep(800000);
        $timer->cancel();
        usleep(400000);
        $this->assertFalse($boolean);
    }

    public function testClearSignatureAfterIterations()
    {
        $clock = new Clock(new Pulse());
        $stack = array();

        $timer = $clock->addPeriodicTimer(1, function() use (&$stack) {
            $stack[] = 'burp';
        });

        usleep(1200000);
        $this->assertEquals(array('burp'), $stack);
        $timer->cancel();
        usleep(1200000);
        $this->assertEquals(array('burp'), $stack);
    }

    public function testClearMultipleSignature()
    {
        $clock = new Clock(new Pulse());

        $stack = array();
        $start = microtime(true);

        $timer = $clock->addPeriodicTimer(1, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bip';
        });

        $timer->cancel();

        $clock->addPeriodicTimer(2, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bop';
        });
        $clock->addTimer(1, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bup';
        });

        $n = 5;
        while ($n > 0) {
            $n--;
            sleep(1);
        }

        $this->assertEquals(array('1bup', '2bop', '4bop'), $stack);
    }

    public function testClear()
    {
        $clock = new Clock(new Pulse());
        $boolean = false;

        $timer = $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });

        $timer->cancel();
        usleep(1200000);
        $this->assertFalse($boolean);
    }

    public function testAddLimitedPeriod()
    {
        $clock = new Clock(new Pulse());
        $stack = array();

        $clock->addPeriodicTimer(1, function() use (&$stack) {
            $stack[] = 'bip';
        }, 2);

        $n = 4;
        while($n>0) {
            sleep(1);
            $n--;
        }

        $this->assertEquals(array('bip', 'bip'), $stack);
    }

    public function testClearMultiple()
    {
        $clock = new Clock(new Pulse());
        $boolean = false;

        $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });
        $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });

        $clock->cancelTimers();
        usleep(1200000);
        $this->assertFalse($boolean);
    }

    public function testTimerFunctionReceivesTimer()
    {
        $clock = new Clock(new Pulse());
        $clock->addPeriodicTimer(1, function($timer) {
            $timer->cancel();
        });
        $start = microtime(true);
        $clock->block();
        $duration = microtime(true) - $start;

        $this->assertGreaterThan(1, $duration);
        $this->assertLessThan(1.002, $duration);
    }

    public function testBlockIsBlocking()
    {
        $clock = new Clock(new Pulse());
        $clock->addTimer(1, function () {});
        $start = microtime(true);
        $clock->block();
        $duration = microtime(true) - $start;

        $this->assertGreaterThan(1, $duration);
        $this->assertLessThan(1.002, $duration);
    }

    public function testBlockWithoutTimerIsNotBlocking()
    {
        $clock = new Clock(new Pulse());
        $start = microtime(true);
        $clock->block();
        $duration = microtime(true) - $start;

        $this->assertGreaterThan(0, $duration);
        $this->assertLessThan(0.002, $duration);
    }
}
