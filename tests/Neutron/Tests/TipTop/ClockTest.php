<?php

namespace Neutron\Tests\TipTop;

use Neutron\TipTop\Clock;

class ClockTest extends \PHPUnit_Framework_TestCase
{
    public function getPulseMock()
    {
        return $this->getMockBuilder('Neutron\TipTop\Pulse')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getTimersMock()
    {
        return $this->getMockBuilder('Neutron\TipTop\Timer\Timers')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDestroy()
    {
        $pulse = $this->getPulseMock();
        $pulse->expects($this->once())
            ->method('on')
            ->with('tick', $this->anything());

        $pulse->expects($this->once())
            ->method('removeListener')
            ->with('tick', $this->anything());

        $clock = new Clock($pulse);
        $clock->destroy();
    }

    public function testPauseResume()
    {
        $clock = new Clock($this->getPulseMock());
        $this->assertFalse($clock->isPaused());
        $clock->pause();
        $this->assertTrue($clock->isPaused());
        $clock->resume();
        $this->assertFalse($clock->isPaused());
    }

    public function testBlocksUntilLastTimer()
    {
        $clock = new Clock($this->getPulseMock());
        $start = microtime(true);

        $n = 0;
        $timers = $this->getTimersMock();
        $timers->expects($this->exactly(5))
            ->method('isEmpty')
            ->will($this->returnCallback(function () use (&$n) {
                $n++;
                return $n === 5;
            }));
        $clock->setTimers($timers);

        $clock->block();
        $this->assertLessThan(0.01, microtime(true) - $start);
    }

    public function testBlockDoesNotBlockIfEmpty()
    {
        $clock = new Clock($this->getPulseMock());
        $timers = $this->getTimersMock();
        $timers->expects($this->once())
            ->method('isEmpty')
            ->will($this->returnValue(true));
        $clock->setTimers($timers);
        $start = microtime(true);
        $clock->block();
        $this->assertLessThan(0.001, microtime(true) - $start);
    }

    public function testAddTimer()
    {
        $clock = new Clock($this->getPulseMock());
        $caught = null;
        $timers = $this->getTimersMock();
        $timers->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf('Neutron\TipTop\Timer\TimerInterface'))
            ->will($this->returnCallback(function ($timer) use (&$caught) {
                $caught = $timer;
                return $timer;
            }));
        $clock->setTimers($timers);

        $callback = function () {};
        $timer = $clock->addTimer(1, $callback);
        $this->assertSame($caught, $timer);
        $this->assertFalse($timer->isPeriodic());
        $this->assertSame($callback, $timer->getCallback());
    }

    public function testAddPeriodicTimer()
    {
        $clock = new Clock($this->getPulseMock());
        $caught = null;
        $timers = $this->getTimersMock();
        $timers->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf('Neutron\TipTop\Timer\TimerInterface'))
            ->will($this->returnCallback(function ($timer) use (&$caught) {
                $caught = $timer;
                return $timer;
            }));
        $clock->setTimers($timers);

        $callback = function () {};
        $timer = $clock->addPeriodicTimer(1, $callback);
        $this->assertSame($caught, $timer);
        $this->assertTrue($timer->isPeriodic());
        $this->assertSame($callback, $timer->getCallback());
        $this->assertEquals(INF, $timer->getPeriods());
    }

    public function testAddPeriodicTimerWithPeriods()
    {
        $clock = new Clock($this->getPulseMock());
        $caught = null;
        $timers = $this->getTimersMock();
        $timers->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf('Neutron\TipTop\Timer\TimerInterface'))
            ->will($this->returnCallback(function ($timer) use (&$caught) {
                $caught = $timer;
                return $timer;
            }));
        $clock->setTimers($timers);

        $callback = function () {};
        $timer = $clock->addPeriodicTimer(1, $callback, 4);
        $this->assertSame($caught, $timer);
        $this->assertTrue($timer->isPeriodic());
        $this->assertSame($callback, $timer->getCallback());
        $this->assertEquals(4, $timer->getPeriods());
    }

    public function testCancelTimer()
    {
        $clock = new Clock($this->getPulseMock());

        $timer = $this->getMock('Neutron\TipTop\Timer\TimerInterface');

        $timers = $this->getTimersMock();
        $timers->expects($this->once())
            ->method('cancel')
            ->with($timer);

        $clock->setTimers($timers);
        $clock->cancelTimer($timer);
    }

    public function testCancelTimers()
    {
        $clock = new Clock($this->getPulseMock());
        $timers = $this->getTimersMock();
        $timers->expects($this->once())
            ->method('clear');
        $clock->setTimers($timers);
        $clock->cancelTimers();
    }

    public function testIsTimerActive()
    {
        $clock = new Clock($this->getPulseMock());

        $timer = $this->getMock('Neutron\TipTop\Timer\TimerInterface');

        $timers = $this->getTimersMock();
        $timers->expects($this->once())
            ->method('contains')
            ->with($timer);

        $clock->setTimers($timers);
        $clock->isTimerActive($timer);
    }

    public function testTickShouldEmitIfNotPaused()
    {
        $clock = new Clock($this->getPulseMock());

        $boolean = false;
        $clock->on('tick', function () use (&$boolean) {
            $boolean = true;
        });

        $clock->tick();
        $this->assertTrue($boolean);
    }

    public function testTickShouldNotEmitIfPaused()
    {
        $clock = new Clock($this->getPulseMock());

        $boolean = false;
        $clock->on('tick', function () use (&$boolean) {
            $boolean = true;
        });

        $clock->pause();
        $clock->tick();
        $this->assertFalse($boolean);
    }

    public function testTickShouldNotEmitIfResumed()
    {
        $clock = new Clock($this->getPulseMock());

        $boolean = false;
        $clock->on('tick', function () use (&$boolean) {
            $boolean = true;
        });

        $clock->pause();
        $clock->resume();
        $clock->tick();
        $this->assertTrue($boolean);
    }
}
