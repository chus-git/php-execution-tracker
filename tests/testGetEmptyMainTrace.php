<?php

require_once __DIR__ . "/../src/ExecutionTracker/Tracker.php";

use ExecutionTracker\Tracker;

$trace = Tracker::getMainTrace();
echo $trace->asJson();
echo "<br>";
echo $trace->asJsonReduced();