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

    $procedure = ExecutionTracker::beginProcedure("Multiplication of $factor1 by $factor2");

    $result = 0;

    for ($i = 0; $i < $factor2; $i++) {
        $result = add($result, $factor1);
    }

    $procedure->end("The result is $result");

    return $result;

}

function add($addend1, $addend2)
{

    $procedure = ExecutionTracker::beginProcedure("Addition of $addend1 and $addend2");

    $result = $addend1 + $addend2;

    usleep(200000);

    $procedure->end("The result is $result");

    return $result;

}

power(2, 4);

$mainProcedure = ExecutionTracker::getMainProcedure();
echo $mainProcedure->asJsonReduced();