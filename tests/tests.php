<?php

require_once __DIR__ . "/../src/ExecutionTracker/Tracker.php";

use ExecutionTracker\Tracker;

function power($base, $exponent)
{

    $track = Tracker::track(
        "Exponentiation of $base to the power of $exponent
    ");

    $result = 1;

    for ($i = 0; $i < $exponent; $i++) {
        $result = multiply($result, $base);
    }

    $track->end("The result is $result");

    return $result;
}

function multiply($factor1, $factor2)
{

    $track = Tracker::track(
        "Multiplication of $factor1 by $factor2
    ");

    $result = 0;

    for ($i = 0; $i < $factor2; $i++) {
        $result = add($result, $factor1);
    }

    $track->end("The result is $result");

    return $result;
}

function add($addend1, $addend2)
{

    $track = Tracker::track(
        "Addition of $addend1 and $addend2"
    );

    $result = $addend1 + $addend2;

    // Sleep 0.2s to show how asJsonReduced() works
    usleep(200000);

    $track->end("The result is $result");

    return $result;
}

power(2, 3);

$mainTrack = Tracker::getMainTrack();

echo $mainTrack->asJsonReduced();