<?php

require_once "Procedure.php";

class ExecutionTracker {

    /** @var Procedure */
    private static $mainProcedure;

    public static function beginProcedure($name)
    {
        $procedure = new Procedure($name, self::getActiveProcedure());
        
        if (!self::$mainProcedure) {
            self::$mainProcedure = $procedure;
        }
        
        return $procedure;
    }

    public static function getActiveProcedure() {

        return array_reduce(self::$mainProcedure->subProcedures, fn($carry, $item) => end($item));

    }

    public static function getMainProcedure() {
        return self::$mainProcedure;
    }

}