<?php

/**
 * Code from ReactPHP - http://reactphp.org/
 * Licensed under MIT license
 *
 * Copyright (c) 2012 Igor Wiedler
 */

namespace Neutron\TipTop\Timer;

interface TimerInterface
{
    public function getInterval();
    public function getCallback();
    public function setData($data);
    public function getData();
    public function removePeriod();
    public function getPeriods();
    public function isPeriodic();
    public function isActive();
    public function cancel();
}
