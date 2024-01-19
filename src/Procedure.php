<?php

class Procedure
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

    /** @var Procedure[] */
    public $subProcedures = [];

    public function __construct($name)
    {

        $this->name = $name;
        $this->startTime = microtime(true);
    }

    /**
     * @param string $error
     */
    public function addError($error)
    {
        $this->errorOccurred = true;
        $this->errors[] = $error;
    }

    /**
     * @param string $warning
     */
    public function addWarning($warning)
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
        $this->addError($error);
    }

    /**
     * @param string $warning
     */
    public function withWarning($warning)
    {
        $this->addWarning($warning);
    }

    /**
     * @param string $log
     */
    public function log($log)
    {
        $this->logs[] = $log;
    }

    /**
     * @param Procedure $subProcedure
     */
    public function addSubProcedure($subProcedure)
    {
        $this->subProcedures[] = $subProcedure;
    }

    public function isFinished()
    {
        return $this->endTime > 0;
    }

    private function clone()
    {
        $clone = new Procedure($this->name);
        $clone->result = $this->result;
        $clone->startTime = $this->startTime;
        $clone->endTime = $this->endTime;
        $clone->errorOccurred = $this->errorOccurred;
        $clone->warningOccurred = $this->warningOccurred;
        $clone->errors = $this->errors;
        $clone->warnings = $this->warnings;
        $clone->logs = $this->logs;
        $clone->subProcedures = $this->subProcedures;
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

        foreach ($clone["subProcedures"] as $subProcedureKey => $subProcedure) {
            $clone["subProcedures"][$subProcedureKey] = $subProcedure->asReduced();
        }

        foreach ($clone as $key => $value) {

            if (!$value) {
                unset($clone[$key]);
            }

        }

        return $clone;
    }

    private function asArray()
    {
        return get_object_vars($this);
    }
}
