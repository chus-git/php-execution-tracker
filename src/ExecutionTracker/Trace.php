<?php

namespace ExecutionTracker;

include_once "Result.php";

class Trace
{

    /** @var string */
    public $name = "";

    /** @var string */
    public $result = "";

    /** @var float */
    public $startTime = 0;

    /** @var float */
    public $endTime = 0;

    /** @var string[] */
    public $errors = [];

    /** @var string[] */
    public $warnings = [];

    /** @var string[] */
    public $logs = [];

    /** @var Trace */
    public $parentTrace = null;

    /** @var Trace[] */
    public $subTraces = [];

    public function __construct($name, $parentTrace = null)
    {

        $this->name = $name;
        $this->parentTrace = $parentTrace;
        $this->startTime = microtime(true);

    }

    /**
     * @param string $error
     */
    public function error($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @param string $warning
     */
    public function warning($warning)
    {
        $this->warnings[] = $warning;
    }

    /**
     * @param string $result
     * @param bool $hide
     */
    public function end($result, $hide = false)
    {
        $this->result = $result;
        $this->endTime = microtime(true);

        if (!$hide && $this->parentTrace) {
            $this->parentTrace->subTraces[] = $this;
        }

        return $this;
    }

    /**
     * @param string $error
     */
    public function withError($error)
    {
        $this->error($error);
    }

    /**
     * @param string $warning
     */
    public function withWarning($warning)
    {
        $this->warning($warning);
    }

    /**
     * @param string $log
     */
    public function log($log)
    {
        $this->logs[] = $log;
    }

    /**
     * @param Trace $subTrace
     */
    public function addSubTrace($subTrace)
    {
        $this->subTraces[] = $subTrace;
    }

    public function isFinished()
    {
        return $this->endTime > 0;
    }

    /**
     * Obtain the result of the trace.
     *
     * @param string[] $options Options to customize the behavior.
     *                       Available options are:
     *                       - 'reduced': If set to true, reduces the result by removing irrelevant data.
     *                       - 'withHumanTimes': If set to true, displays times in human-readable format.
     *                       - 'withDuration': If set to true, includes the duration between startTime and endTime.
     */
    public function result($options = [])
    {
        return new Result($this, $options);
    }

    /**
     * Hide the trace. Note: Cannot hide the main trace.
     * 
     * @return bool True if the trace was hidden, false otherwise.
     */

    public function hide()
    {

        if (!$this->parentTrace) {
            return false;
        }

        $this->parentTrace->subTraces = array_filter($this->parentTrace->subTraces, function ($subTrace) {
            return $subTrace !== $this;
        });

        return true;
    }

    /**
     * Obtain the total number of subtraces and subtraces of subtraces.
     */

    public function totalSubTraces()
    {
        return count($this->subTraces) + array_reduce($this->subTraces, function ($carry, $subTrace) {
            return $carry + $subTrace->totalSubTraces();
        }, 0);
    }
}
