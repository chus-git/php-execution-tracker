<?php

namespace ExecutionTracker;

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

    /** @var bool */
    public $errorOccurred = false;

    /** @var bool */
    public $warningOccurred = false;

    /** @var string[] */
    public $errors = [];

    /** @var string[] */
    public $warnings = [];

    /** @var string[] */
    public $logs = [];

    /** @var Trace[] */
    public $subTraces = [];

    public function __construct($name)
    {

        $this->name = $name;
        $this->startTime = microtime(true);
    }

    /**
     * @param string $error
     */
    public function error($error)
    {
        $this->errorOccurred = true;
        $this->errors[] = $error;
    }

    /**
     * @param string $warning
     */
    public function warning($warning)
    {
        $this->warningOccurred = true;
        $this->warnings[] = $warning;
    }

    /**
     * @param string $result
     */
    public function end($result)
    {
        $this->result = $result;
        $this->endTime = microtime(true);
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

    private function clone()
    {
        $clone = new Trace($this->name);
        $clone->result = $this->result;
        $clone->startTime = $this->startTime;
        $clone->endTime = $this->endTime;
        $clone->errorOccurred = $this->errorOccurred;
        $clone->warningOccurred = $this->warningOccurred;
        $clone->errors = $this->errors;
        $clone->warnings = $this->warnings;
        $clone->logs = $this->logs;
        $clone->subTraces = $this->subTraces;
        return $clone;
    }

    public function asJson()
    {
        return json_encode($this);
    }

    public function asJsonReduced()
    {
        return json_encode($this->asReduced());
    }

    public function asReduced()
    {

        $clone = $this->clone()->asArray();

        // Unset startTime and endTime if the difference is less than 1 second
        
        $timeDiff = $clone["endTime"] - $clone["startTime"];
        
        if ($timeDiff < 1) {
            unset($clone["startTime"]);
            unset($clone["endTime"]);
        }

        if($clone["subTraces"])
            foreach ($clone["subTraces"] as $subTraceKey => $subTrace) {
                $clone["subTraces"][$subTraceKey] = $subTrace->asReduced();
            }

        foreach ($clone as $key => $value) {

            if (!$value) {
                unset($clone[$key]);
            }

        }

        return $clone;
    }

    public function asArray()
    {
        return get_object_vars($this);
    }

}
