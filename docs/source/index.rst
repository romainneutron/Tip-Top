Tip-Top ! Documentation
=======================

Introduction
------------

Tip-Top is a PHP micro-library to set timers and timeouts in your PHP
code. It is based on `SIGALRM` signal, so it requires both posix and pcntl
extensions.

Disclaimer
----------

Due to PHP, timers and timeout may be triggered lately in case you use blocking
calls like `sleep(5)`.

A common workaround for `sleep` is to iterate 1 seconds sleeps :
`for ($i=0; $i!=5; $i++;) {sleep(1);}`.

Limitations
-----------

- `Neutron\TipTop\Clock` requires you declare ticks in the script you're running.
- `Neutron\TipTop\Clock` may not work as expected with blocking calls (try it before)

Installation
------------

We rely on `composer <http://getcomposer.org/>`_ to use this library. If you do
no still use composer for your project, you can start with this ``composer.json``
at the root of your project:

.. code-block:: json

    {
        "require": {
            "neutron/tip-top": "0.1.x-dev"
        }
    }

Install composer :

.. code-block:: bash

    # Install composer
    curl -s http://getcomposer.org/installer | php
    # Upgrade your install
    php composer.phar install

You now just have to autoload the library to use it :

.. code-block:: php

    <?php
    require 'vendor/autoload.php';

This is a very short intro to composer.
If you ever experience an issue or want to know more about composer,
you will find help on their  website
`http://getcomposer.org/ <http://getcomposer.org/>`_.

Basic Usage
-----------

You **MUST** declare ticks in the script where you're using
`Neutron\TipTop\Clock`, it is mandatory for the clock to work.

.. code-block:: php

    <?php
    use Neutron\TipTop\Clock;

    // mandatory for the clock to work
    declare(ticks=1);

    $clock = new Clock();

    // trigger a callback every second
    $clock->addPeriodicTimer(1, function () { echo "BOOM ! I'm triggered every second !\n"; });

    // trigger a callback every ten seconds, five times
    $clock->addPeriodicTimer(10, function () { echo "Doubidou\n"; }, 5);

    // trigger a callback one time, in 3 seconds
    $signature = $clock->addTimer(3, function () { echo "BOOM !\n"; }, 1);

    // remove a timer identified by a signature
    $clock->clear($signature);

    // do your job
    $n = 10;
    while ($n > 0) {
        sleep(1);
        $n--;
    }

Handling Exceptions
-------------------

Report a bug
------------

If you experience an issue, please report it in our
`issue tracker <https://github.com/neutron/Tip-Top/issues>`_. Before
reporting an issue, please be sure that it is not already reported by browsing
open issues.

Ask for a feature
-----------------

We would be glad you ask for a feature ! Feel free to add a feature request in
the `issues manager <https://github.com/neutron/Tip-Top/issues>`_ on GitHub !

Contribute
----------

You find a bug and resolved it ? You added a feature and want to share ? You
found a typo in this doc and fixed it ? Feel free to send a
`Pull Request <http://help.github.com/send-pull-requests/>`_ on GitHub, we will
be glad to merge your code.


Run tests
---------

Tip Top ! relies on `PHPUnit <http://www.phpunit.de/manual/current/en/>`_ for
unit tests. To run tests on your system, ensure you have PHPUnit installed,
and, at the root of the project, execute it :

.. code-block:: bash

    phpunit

About
-----

Tip Top ! has been written by Romain Neutron based on a Bulat Shakirzyanov
`gist <https://gist.github.com/3085581>`_.

License
-------

RabbitMQ Management API client is licensed under the
`MIT License <http://opensource.org/licenses/MIT>`_
