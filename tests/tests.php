<?php

require_once __DIR__ . "/../src/ExecutionTracker/Tracker.php";

use ExecutionTracker\Tracker;

$procedure = Tracker::beginProcedure("Main procedure");

$procedure->end("a");

echo $procedure->asJson();

