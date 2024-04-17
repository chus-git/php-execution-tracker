<?php

require_once __DIR__ . "/../src/ExecutionTracker/Tracker.php";

use ExecutionTracker\Tracker;

function power($base, $exponent)
{

    $trace = Tracker::track(
        "Exponentiation of $base to the power of $exponent
    ");

    $result = 1;

    for ($i = 0; $i < $exponent; $i++) {
        $result = multiply($result, $base);
    }

    $trace->end("The result is $result");

    return $result;
}

function multiply($factor1, $factor2)
{

    $trace = Tracker::track(
        "Multiplication of $factor1 by $factor2
    ");

    $result = 0;

    for ($i = 0; $i < $factor2; $i++) {
        $result = add($result, $factor1);
    }

    $trace->end("The result is $result");

    return $result;
}

function add($addend1, $addend2)
{

    $trace = Tracker::track(
        "Addition of $addend1 and $addend2"
    );

    $result = $addend1 + $addend2;

    // Sleep 0.2s to show how reduced() option works works
    usleep(200000);

    $trace->end("The result is $result");

    return $result;
}

power(2, 3);

$mainTrack = Tracker::getMainTrace();

echo $mainTrack->result([
    'reduced' => true,
    'withHumanTimes' => true,
    'withDuration' => true
])->asJson();