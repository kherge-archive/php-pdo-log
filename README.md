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

// retrieves all logged queries
$entries = $pdo->getLog();
```

Installation
------------

Use Composer:

```
$ composer require "herrera-io/pdo-log=~1.0"
```

Usage
-----

The logging `Pdo` class is a subclass of the real `PDO` class, so the only
thing that has been changed is adding the ability to log certain actions,
and that `query()` and `prepare()` will return the logging version of the
`PDOStatement` class. This version of the class is not a subclass of the
original, but all property gets/sets and method calls are mirrored. You
can still retrieve the real instance using `PdoStatement->getPdoStatement()`.

[Build Status]: https://travis-ci.org/herrera-io/php-pdo-log.png?branch=master
