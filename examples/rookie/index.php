<?php

require_once "../../src/ExecutionTracker.php";

function multiply($a, $b) {

    $procedure = ExecutionTracker::beginProcedure(
        "Multiply $a by $b"
    );

    $result = $a * $b;

    $procedure->end($result);

}

multiply(2, 2);

$mainProcedure = ExecutionTracker::getMainProcedure();

echo $mainProcedure->asJson();