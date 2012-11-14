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

        $clock->set(1, function() use (&$boolean) {
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

        $clock->set(1, function() use ($start, &$stack) {
                $stack[] = round(microtime(true) - $start) . 'bip';
            });
        $clock->set(2, function() use ($start, &$stack) {
                $stack[] = round(microtime(true) - $start) . 'bop';
            });
        $clock->set(1, function() use ($start, &$stack) {
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
     * @covers Neutron\TipTop\Clock::clear
     */
    public function testClear()
    {
        declare(ticks = 1);

        $clock = new Clock();

        $boolean = false;

        $clock->set(1, function() use (&$boolean) {
                $boolean = true;
            });
        $clock->clear();

        usleep(1200000);

        $this->assertFalse($boolean);
    }
}
