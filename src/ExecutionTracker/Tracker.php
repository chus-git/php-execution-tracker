<?php

namespace ExecutionTracker;

require_once "Procedure.php";

class Tracker
{

    /** @var Procedure */
    private static $mainProcedure;

    /** @var Procedure[] */
    private static $procedures = [];

    /**
     * @param string $name
     * 
     * @return Procedure The new procedure
     */
    public static function beginProcedure($name)
    {

        $procedure = new Procedure($name);

        if (!self::$mainProcedure) {
            self::$mainProcedure = $procedure;
        }

        $parentProcedure = end(self::$procedures);

        while ($parentProcedure && $parentProcedure->isFinished()) {
            $parentProcedure = self::$procedures[array_search($parentProcedure, self::$procedures) - 1];
        }

        if ($parentProcedure) {
            $parentProcedure->subProcedures[] = $procedure;
        }

        self::$procedures[] = $procedure;

        return $procedure;
    }

    /**
     * @return Procedure The main procedure
     */
    public static function getMainProcedure()
    {
        return self::$mainProcedure;
    }

    /**
     * Clear all procedures
     */
    public static function clear()
    {
        self::$procedures = [];
    }

}
