# PHP Execution Tracker

A PHP library to track the execution of a process. It allows you to track the time difference between processes, debug, logs, warnings and errors.

> Important: Currently, I use this same library in production, but it is still subject to potential significant changes due to the early version it is in.

## Usage

Install the library using composer:

```bash
composer require chus/php-execution-tracker
```

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use ExecutionTracker/Tracker;

$trace = Tracker::track("Print Hello World");

echo "Hello World";

$trace->end();

echo $trace->result()->asJson();
```