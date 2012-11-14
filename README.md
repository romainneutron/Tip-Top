#TIP-TOP

[![Build Status](https://secure.travis-ci.org/romainneutron/Tip-Top.png?branch=master)](https://travis-ci.org/romainneutron/Tip-Top)

A micro library to set timeouts and periodic timers.

## Example

There are two main methods on the `Neutron\TipTop\Clock` object :
`addPeriodicTimer` and `addTimer`.

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

// trigger a callback every second
$clock->addPeriodicTimer(1, function () { echo "BOOM ! I'm triggered every second !\n"; });

// trigger a callback every ten seconds, five times
$clock->addPeriodicTimer(10, function () { echo "Doubidou\n"; }, 5);

// trigger a callback one time, in 3 seconds
$clock->addTimer(3, function () { echo "BOOM !\n"; }, 5);

// do your job
$n = 10;
while ($n > 0) {
    sleep(1);
    $n--;
}
```

## Limitations

- This clock requires you declare ticks in the script you're running.
- Clock won't work as expected with long blocking calls (try it before)

##License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).

