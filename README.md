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
$clock->addPeriodicTimer(1, function () { echo "BOOM ! I'm triggered every second !\n"; });

// trigger a callback in 5 second
$signature = $clock->addTimer(function () { echo "BOOM ! I was planned 5 seconds ago !\n"; });

// remove a timer identified by a signature
$clock->clear($signature);

```

##License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).

