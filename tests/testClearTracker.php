<?php

require_once __DIR__ . "/../src/ExecutionTracker/Tracker.php";

use ExecutionTracker\Tracker;

$trace = Tracker::track("Wait 1 second");
sleep(1);
$trace->end("Waited 1 second");
echo $trace->result(['reduced' => true, 'withHumanTimes' => true, 'withDuration' => true])->asJson();

Tracker::clear();

$trace = Tracker::track("Wait 2 seconds");
sleep(2);
$trace->end("Waited 2 seconds");
echo $trace->result(['reduced' => true, 'withHumanTimes' => true, 'withDuration' => true])->asJson();