#TIP-TOP

[![Build Status](https://secure.travis-ci.org/romainneutron/Tip-Top.png?branch=master)](https://travis-ci.org/romainneutron/Tip-Top)

A micro library to set timeouts and periodic timers.

## Documentation

See [documentation](https://tip-top.readthedocs.org) for limitations, usage and evrything that has to be in
documentation, like an [API browser](https://tip-top.readthedocs.org/en/latest/_static/API/) or other great things.

[![Tip-Top !](https://raw.github.com/romainneutron/Tip-Top/master/docs/source/_themes/Alchemy/static/img/project.png)](https://tip-top.readthedocs.org)

## Example

There are two main methods on the `Neutron\TipTop\Clock` object :
`addPeriodicTimer` and `addTimer`.

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

// trigger a callback every second
$clock->addPeriodicTimer(1, function (signature) { echo "BOOM ! I'm triggered every second !\n"; });

// trigger a callback in 5 second
$signature = $clock->addTimer(function (signature) { echo "BOOM ! I was planned 5 seconds ago !\n"; });

// remove all timers
$clock->clear($);
```

### Remove a timer

You can remove a timer using the `clear` method :

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

$stop = false;
$clock->addPeriodicTimer(1, function ($signature) use ($clock, &$stop) {
    if ($stop) {
        $clock->clear($signature);
    }
});

// remove all timers
$clock->clear($);
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

