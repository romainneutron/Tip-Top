#TIP-TOP

[![Build Status](https://secure.travis-ci.org/romainneutron/Tip-Top.png?branch=master)](https://travis-ci.org/romainneutron/Tip-Top)

A micro library to set timeouts and periodic timers.

```php
$clock = new Clock();
// triggers a callback every second
$timer = $clock->addPeriodicTimer(1, function ($timer) {
    echo "BOOM ! I'm triggered every second !\n";
});
```

It uses [Evenement](https://github.com/igorw/evenement). Most of the timers
code has been taken from [ReactPHP](https://github.com/reactphp/react) timers
implementation.

[![Tip-Top !](https://raw.github.com/romainneutron/Tip-Top/master/docs/source/_themes/Alchemy/static/img/project.png)](https://tip-top.readthedocs.org)

## Installation

The recommended way to use tip-top is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "neutron/tip-top": "0.2.*"
    }
}
```

## Disclaimer

### Limitations

- Tip-Top requires you declare ticks in the script you're running.
```
declare(ticks=1);
```
- Tip-Top may not work as expected with blocking calls (try it before)

### Is this blocking or non-blocking IO ?

TipTop is non blocking, but it has been designed to support some blocking
calls. Timers and timeout may be triggered lately in case you use blocking
calls like ``sleep(5)``.

A common workaround for `sleep` is to iterate 1 seconds sleeps :
``for ($i=0; $i!=5; $i++;) {sleep(1);}``.

### Has the clock atomic precision ?

Unfortunately, this library is based on a hack on the
[pcntl_alarm](php.net/manual/en/function.pcntl-alarm.php) function. Therefore,
the resolution of the clock is the second, and it has shift that is
approximatively 0,001 second per second.

### What's the use case for such clock ?

Think about long running processes that have to be checked regularly.


## Documentation

[API browser](https://tip-top.readthedocs.org/en/latest/_static/API/).

### Examples

There are two main methods on the `Neutron\TipTop\Clock` object :
`addPeriodicTimer` and `addTimer`.

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

// triggers a callback every second
$timer = $clock->addPeriodicTimer(1, function ($timer) {
    echo "BOOM ! I'm triggered every second !\n";
});

// triggers a callback in 5 second
$timer = $clock->addTimer(5, function ($timer) {
    echo "BOOM ! I was planned 5 seconds ago !\n";
});

// removes all timers
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

// removes all timers
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

### Tests

Tip Top is functional and unit testable with PHPUnit.
To run tests on your system, please be sure to install dev-dependencies with
`composer install --dev`, then run either `bin/phpunit -c phpunit.xml.dist` for
unit tests, either `bin/phpunit -c phpunit-functional.xml.dist` for functional
tests.

##License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).
