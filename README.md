# PHP Execution Tracker

A PHP library to track the execution of a process. It allows you to track the time difference between processes, debug, logs, warnings and errors.

> Important: Currently, I use this same library in production, but it is still subject to potential significant changes due to the early version it is in.

## Usage

```php
<?php

require_once "../../src/ExecutionTracker.php";

function power($base, $exponent)
{

    $procedure = ExecutionTracker::beginProcedure("Exponentiation of $base to the power of $exponent");

    $result = 1;

    for ($i = 0; $i < $exponent; $i++) {
        $result = multiply($result, $base);
    }

    $procedure->end("The result is $result");

    return $result;

}

function multiply($factor1, $factor2)
{

    $procedure = ExecutionTracker::beginProcedure(
        "Multiplication of $factor1 by $factor2"
    );

    $result = 0;

    for ($i = 0; $i < $factor2; $i++) {
        $result = add($result, $factor1);
    }

    $procedure->end("The result is $result");

    return $result;

}

function add($addend1, $addend2)
{

    $procedure = ExecutionTracker::beginProcedure(
        "Addition of $addend1 and $addend2
    ");

    $result = $addend1 + $addend2;

    usleep(200000);

    $procedure->end("The result is $result");

    return $result;

}

power(2, 4);

$mainProcedure = ExecutionTracker::getMainProcedure();

echo $mainProcedure->asJsonReduced();
```

Output:

```json
{
    "name": "Exponentiation of 2 to the power of 4",
    "result": "The result is 16",
    "startTime": 1705694473.75968,
    "endTime": 1705694475.421149,
    "subProcedures": [
        {
            "name": "Multiplication of 1 by 2",
            "result": "The result is 2",
            "subProcedures": [
                {
                    "name": "Addition of 0 and 1",
                    "result": "The result is 1"
                },
                {
                    "name": "Addition of 1 and 1",
                    "result": "The result is 2"
                }
            ]
        },
        {
            "name": "Multiplication of 2 by 2",
            "result": "The result is 4",
            "subProcedures": [
                {
                    "name": "Addition of 0 and 2",
                    "result": "The result is 2"
                },
                {
                    "name": "Addition of 2 and 2",
                    "result": "The result is 4"
                }
            ]
        },
        {
            "name": "Multiplication of 4 by 2",
            "result": "The result is 8",
            "subProcedures": [
                {
                    "name": "Addition of 0 and 4",
                    "result": "The result is 4"
                },
                {
                    "name": "Addition of 4 and 4",
                    "result": "The result is 8"
                }
            ]
        },
        {
            "name": "Multiplication of 8 by 2",
            "result": "The result is 16",
            "subProcedures": [
                {
                    "name": "Addition of 0 and 8",
                    "result": "The result is 8"
                },
                {
                    "name": "Addition of 8 and 8",
                    "result": "The result is 16"
                }
            ]
        }
    ]
}
```