<?php

namespace ExecutionTracker;

require_once "Trace.php";

class Tracker
{

    /** @var Trace */
    private static $mainTrace;

    /** @var Trace[] */
    private static $traces = [];

    /** @var bool */
    private static $enabled = true;

    /**
     * @param string $name
     * 
     * @return Trace The new trace
     */
    public static function track($name)
    {

        $trace = new Trace($name);

        if (!self::$mainTrace) {
            self::$mainTrace = $trace;
        }

        $parentTrace = end(self::$traces);

        while ($parentTrace && $parentTrace->isFinished()) {
            $parentTrace = self::$traces[array_search($parentTrace, self::$traces) - 1];
        }

        if(!self::$enabled) {
            return $trace;
        }

        if ($parentTrace) {
            $parentTrace->subTraces[] = $trace;
        }

        self::$traces[] = $trace;

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
        self::$traces = [];
    }

    /**
     * @return Trace The current trace
     */
    public static function getCurrentTrace()
    {
        return end(self::$traces);
    }

    /**
     * Disable the tracker
     */
    public static function enable() {
        self::$enabled = true;
    }

    /**
     * Enable the tracker
     */
    public static function disable() {
        self::$enabled = false;
    }

}
