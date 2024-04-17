<?php

require_once __DIR__ . "/../src/ExecutionTracker/Tracker.php";

use ExecutionTracker\Tracker;

$track = Tracker::track("Wait 1 second");
sleep(1);
$track->end("Waited 1 second");

echo $track->result([
    'reduced' => true,
    'withHumanTimes' => true,
    'withDuration' => true
])->asJson();