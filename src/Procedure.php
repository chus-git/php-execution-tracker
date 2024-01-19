<?php

class Procedure
{

    public $name = "No name specified";
    public $result = "No result specified";
    public $startTime = 0;
    public $endTime = 0;
    public $errorOccurred = false;
    public $warningOccurred = false;
    public $errors = [];
    public $warnings = [];
    public $logs = [];
    public $subProcedures = [];

    public function __construct($name, $parentProcedure = null)
    {
        $this->name = $name;
        $this->startTime = microtime(true);

        if ($parentProcedure) {
            $parentProcedure->addSubProcedure($this);
        }

    }

    // Functions

    public function addError($error) {
        $this->errorOccurred = true;
        $this->errors[] = $error;
    }

    public function addWarning($warning) {
        $this->warningOccurred = true;
        $this->warnings[] = $warning;
    }

    public function end($result)
    {
        $this->result = $result;
        $this->endTime = microtime(true);
        return $this;
    }

    public function withError($error)
    {
        $this->addError($error);
    }

    public function withWarning($warning)
    {
        $this->addWarning($warning);

    }

    public function log($log)
    {
        $this->logs[] = $log;
    }

    public function addSubProcedure($subProcedure)
    {
        $this->subProcedures[] = $subProcedure;
    }

    public function isFinished()
    {
        return $this->endTime > 0;
    }

}
