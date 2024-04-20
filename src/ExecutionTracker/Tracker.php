<?php

namespace ExecutionTracker;

require_once "Trace.php";

class Tracker
{

    /** @var Trace */
    private static $mainTrace;

    /** @var Trace */
    private static $currentTrace;

    /** @var bool */
    private static $enabled = true;

    /**
     * @param string $name
     * 
     * @return Trace The new trace
     */
    public static function track($name)
    {

        if (!self::$enabled) {
            return new Trace($name);
        }

        // Iterate current trace and his parents to find the last trace that is not finished
        $parent = self::$currentTrace;
        while ($parent && $parent->isFinished()) {
            $parent = $parent->parentTrace;
        }

        $trace = new Trace($name, $parent);

        if (!self::$mainTrace) {
            self::$mainTrace = $trace;
            self::$currentTrace = $trace;
        }

        self::$currentTrace = $trace;

        return $trace;
    }

    /**
     * @return Trace The main trace
     */
    public static function getMainTrace()
    {
        return self::$mainTrace;
    }

    /**
     * Clear all traces
     */
    public static function clear()
    {
        self::$mainTrace = null;
        self::$currentTrace = null;
    }

    /**
     * @return Trace The current trace
     */
    public static function getCurrentTrace()
    {
        return self::$currentTrace;
    }

    /**
     * Disable the tracker
     */
    public static function enable()
    {
        self::$enabled = true;
    }

    /**
     * Enable the tracker
     */
    public static function disable()
    {
        self::$enabled = false;
    }

}
