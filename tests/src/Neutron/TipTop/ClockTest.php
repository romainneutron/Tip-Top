<?php

namespace Neutron\TipTop;

class ClockTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Neutron\TipTop\Clock::set
     */
    public function testSet()
    {
        declare(ticks = 1);

        $clock = new Clock();

        $boolean = false;

        $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });

        usleep(1200000);

        $this->assertTrue($boolean);
    }

    /**
     * @covers Neutron\TipTop\Clock::set
     */
    public function testSetMultiple()
    {
        declare(ticks = 1);

        $clock = new Clock();

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
        }, 1);

        $n = 5;
        while ($n > 0) {
            $n--;
            sleep(1);
        }

        $this->assertEquals(array('1bip', '1bup', '2bop', '2bip', '3bip', '4bop', '4bip', '5bip'), $stack);
    }

    /**
     * @covers Neutron\TipTop\Clock::set
     */
    public function testClearSignature()
    {
        declare(ticks = 1);

        $clock = new Clock();

        $boolean = false;

        $signature = $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });

        usleep(800000);
        $clock->clear($signature);
        usleep(400000);

        $this->assertFalse($boolean);
    }

    /**
     * @covers Neutron\TipTop\Clock::set
     */
    public function testClearSignatureAfterIterations()
    {
        declare(ticks = 1);

        $clock = new Clock();

        $stack = array();

        $signature = $clock->addPeriodicTimer(1, function() use (&$stack) {
            $stack[] = 'burp';
        });

        usleep(1200000);

        $this->assertEquals(array('burp'), $stack);

        $clock->clear($signature);
        usleep(1200000);
        $this->assertEquals(array('burp'), $stack);
    }

    /**
     * @covers Neutron\TipTop\Clock::set
     */
    public function testClearMultipleSignature()
    {
        declare(ticks = 1);

        $clock = new Clock();

        $stack = array();

        $start = microtime(true);

        $signature = $clock->addPeriodicTimer(1, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bip';
        });

        $clock->clear($signature);

        $clock->addPeriodicTimer(2, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bop';
        });
        $clock->addTimer(1, function() use ($start, &$stack) {
            $stack[] = round(microtime(true) - $start) . 'bup';
        }, 1);

        $n = 5;
        while ($n > 0) {
            $n--;
            sleep(1);
        }

        $this->assertEquals(array('1bup', '2bop', '4bop'), $stack);
    }

    /**
     * @covers Neutron\TipTop\Clock::clear
     */
    public function testClear()
    {
        declare(ticks = 1);

        $clock = new Clock();

        $boolean = false;

        $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });
        $clock->clear();

        usleep(1200000);

        $this->assertFalse($boolean);
    }

    /**
     * @covers Neutron\TipTop\Clock::clear
     */
    public function testAddLimitedPeriod()
    {
        declare(ticks = 1);

        $clock = new Clock();

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

    /**
     * @covers Neutron\TipTop\Clock::clear
     */
    public function testClearMultiple()
    {
        declare(ticks = 1);

        $clock = new Clock();

        $boolean = false;

        $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });
        $clock->addPeriodicTimer(1, function() use (&$boolean) {
            $boolean = true;
        });
        $clock->clear();

        usleep(1200000);

        $this->assertFalse($boolean);
    }

    public function testBlockIsBlocking()
    {
        declare(ticks = 1);

        $clock = new Clock();
        $clock->addTimer(1, function () {});
        $start = microtime(true);
        $clock->block();
        $duration = microtime(true) - $start;

        $this->assertGreaterThan(1, $duration);
        $this->assertLessThan(1.001, $duration);
    }

    public function testBlockWithoutTimerIsNotBlocking()
    {
        declare(ticks = 1);

        $clock = new Clock();
        $start = microtime(true);
        $clock->block();
        $duration = microtime(true) - $start;

        $this->assertGreaterThan(0, $duration);
        $this->assertLessThan(0.001, $duration);
    }
}
