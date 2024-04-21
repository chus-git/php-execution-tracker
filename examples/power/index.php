<?php

require_once '../../vendor/autoload.php';

use ExecutionTracker\Tracker;

function sum($a, $b)
{
    $trace = Tracker::track("Sum $a + $b");
    $result = $a + $b;
    $trace->end("Obtained $result");
    return $a + $b;
}

function multiply($a, $b)
{
    $trace = Tracker::track("Multiply $a * $b");
    $result = 0;
    for ($i = 0; $i < $b; $i++) {
        $result = sum($result, $a);
    }
    $trace->end("Obtained $result");
    return $a * $b;
}

function power($a, $b)
{
    $trace = Tracker::track("Power $a ^ $b");
    $result = 1;
    for ($i = 0; $i < $b; $i++) {
        $result = multiply($result, $a);
    }
    $trace->end("Obtained $result");
    return $result;
}

$trace = Tracker::track("Calculate 3 ^ 3");
$result = power(3, 3);
$trace->end("Obtained $result");

echo $trace->result([
    'reduced' => true,
])->asJson();
