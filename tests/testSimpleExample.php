<?php

require_once __DIR__ . "/../src/ExecutionTracker/Tracker.php";

use ExecutionTracker\Tracker;

// Start tracking the execution and get the trace object
$trace = Tracker::track("Count the cats in the array");

$animals = ['🐵', '🐼', '🐤', '🐱', '🐶', '🐷', '🐱', '🐴', '🐸'. '🐹', '🐭', '🐱', '🐰'];

$cats = 0;

foreach ($animals as $key => $animal) {

    if($animal === '🐱') {
        $trace->log("Cat found! We have $cats cats so far");
        $cats++;
    }

}

// End the trace and set the result
$trace->end("$cats cats found in the array");

// Print the result as JSON
echo $trace->result()->asJson();