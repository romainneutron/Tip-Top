#TIP-TOP

A micro library to set timeouts and periodic timers.

## Example

```php
use Neutron\TipTop\Clock;

// mandatory for the clock to work
declare(ticks=1);

$clock = new Clock();

// trigger a callback every second
$clock->set(1, function () { echo "BOOM ! I'm triggered every second !\n"; });

// trigger a callback every ten seconds, five times
$clock->set(10, function () { echo "Doubidou\n"; }, 5);

$n = 10;
while ($n > 0) {
    sleep(1);
    $n--;
}
```

## Limitations

- This timer requires you declare ticks in the script the clock is used.
- Timer won't work as expected with blocking calls (try it before)

##License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).

