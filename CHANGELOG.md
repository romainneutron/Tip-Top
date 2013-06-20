CHANGELOG
=========

* 0.2.0 (2013-06-20)

  * BC Break : `Clock::addTimer` and `Clock::addPeriodicTimer` now returns
    `Timer` object (thanks to react implementation).

  * `Clock` is now an event emitter that emits 'tick' events.
  * New Timer API.
  * Support for multiple clocks in the same PHP process.
  * New methods `Clock::pause` and `Clock::resume`.
  * Implemented proper destruction method `Clock::destroy`.

* 0.1.2 (2013-05-13)

  * Pass signature to timers callbacks.

* 0.1.1 (2013-05-13)

  * Add Clock::block feature.

* 0.1.0 (2013-05-12)

  * First tagged release.
