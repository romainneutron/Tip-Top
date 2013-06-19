#TIP-TOP

[![Build Status](https://secure.travis-ci.org/romainneutron/Tip-Top.png?branch=master)](https://travis-ci.org/romainneutron/Tip-Top)

A micro library to set timeouts and periodic timers.

```php
$clock = new Clock();
// trigger a callback every second
$timer = $clock->addPeriodicTimer(1, function ($timer) {
    echo "BOOM ! I'm triggered every second !\n";
});
```

It uses [Evenement](https://github.com/igorw/evenement). Most of the timers
code has been taken from [ReactPHP](https://github.com/reactphp/react) timers
implementation.

## Documentation

See [documentation](https://tip-top.readthedocs.org) for limitations, usage and
everything that has to be in documentation, like an
[API browser](https://tip-top.readthedocs.org/en/latest/_static/API/) or other
great things.

[![Tip-Top !](https://raw.github.com/romainneutron/Tip-Top/master/docs/source/_themes/Alchemy/static/img/project.png)](https://tip-top.readthedocs.org)

## Disclaimer

### Is this blocking/non-blocking IO ?

TipTop is non blocking, but it has been designed to support some blocking
calls. What you have to remember is that you may block for a 3 seconds, but
timers would not be triggered. Once the blocking call ends, all timers that
should have been triggered will be fired.

Think about long running processes that have to be checked regularly.

### Has the clock atomic precision ?

Unfortunately, this library is based on a hack on the
[pcntl_alarm](php.net/manual/en/function.pcntl-alarm.php) function. Therefore,
the resolution of the clock is the second, and it has shift that is
approximatively 0,001 second per second.

## Examples

There are two main methods on the `Neutron\TipTop\Clock` object :
`addPeriodicTimer` and `addTimer`.

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

// trigger a callback every second
$timer = $clock->addPeriodicTimer(1, function ($timer) {
    echo "BOOM ! I'm triggered every second !\n";
});

// trigger a callback in 5 second
$timer = $clock->addTimer(function ($timer) {
    echo "BOOM ! I was planned 5 seconds ago !\n";
});

// remove all timers
$clock->clear();
```

### Remove a timer

You can remove a timer using its `cancel` method :

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

$timer = $clock->addPeriodicTimer(1, function ($timer) {
    if ($stop) {
        $timer->cancel();
    }
});
```

### Clear all timers

You may want to clear all timers :


```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

$timer = $clock->addPeriodicTimer(1, function ($timer) { echo "Hello"; });
$timer = $clock->addPeriodicTimer(1, function ($timer) { echo "Hello World"; });

// remove all timers
$clock->clear();
```

### Block

You can sometimes want to block until all timers have finished, use the `block`
method to do that.

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

// echoes three times
$clock->addPeriodicTimer(1, function (signature) {
    echo "The block method blocks \n";
}, 3);

$clock->block();
echo "This line will be blocked until last timer executes
```

##License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).
