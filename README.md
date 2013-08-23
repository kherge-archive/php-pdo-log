PDO Log
=======

[![Build Status]](https://travis-ci.org/herrera-io/php-pdo-log)

This library provides an alternative class to `PDO`. Its purpose is to log
queries and their execution times. You can also set an observer that will be
called whenever a new log entry is added.

```php
$pdo = new Herrera\Pdo\Pdo('sqlite::memory');

$pdo->onLog(
    function (array $entry) {
        print_r($entry);
    }
);

$pdo->exec('CREATE TABLE test ()');

/*
Array
(
    [query] => CREATE TABLE test ()
    [time] => 0.00026607513427734
    [values] => Array
        (
        )
)
*/
```

Installation
------------

Use Composer:

```
$ composer require "herrera-io/pdo-log=~1.0"
```

Usage
-----

?

[Build Status]: https://travis-ci.org/herrera-io/php-pdo-log.png?branch=master
