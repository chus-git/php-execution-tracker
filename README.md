# PHP Execution Tracker

A PHP library to track the execution of a process. It allows you to track the time difference between processes, debug, logs, warnings and errors.

> Important: Currently, I use this same library in production, but it is still subject to potential significant changes due to the early version it is in.

## Usage

```php
<?php

function multiply($a, $b) {

    $procedure = ExecutionTracker::beginProcedure("Multiply $a by $b");

    $result = $a * $b;

    $procedure->end($result);

    return $a * $b;
}

multiply(2, 3);

$mainProcedure = ExecutionTracker::getMainProcedure();

echo $mainProcedure->asJson();
```