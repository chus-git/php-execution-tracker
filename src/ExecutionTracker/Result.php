<?php

namespace ExecutionTracker;

class Result
{

    private const OPTION_REDUCED = "reduced";
    private const OPTION_WITH_HUMAN_TIMES = "withHumanTimes";
    private const OPTION_WITH_DURATION = "withDuration";

    /** @var Trace */
    private $trace;

    private $options = [
        self::OPTION_REDUCED => false,
        self::OPTION_WITH_HUMAN_TIMES => false,
        self::OPTION_WITH_DURATION => false
    ];

    /**
     * Constructor of the Result class.
     *
     * @param mixed $trace The trace object.
     * @param array $options Options to customize the behavior.
     *                       Available options are:
     *                       - 'reduced': If set to true, reduces the result by removing irrelevant data.
     *                       - 'withHumanTimes': If set to true, displays times in human-readable format.
     *                       - 'withDuration': If set to true, includes the duration between startTime and endTime.
     */
    public function __construct($trace, $options = [])
    {
        $this->trace = $trace;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Result output functions
     */

    public function asArray()
    {
        $options = $this->options;
        $trace = $this->trace;

        $resultArray = [
            "name" => $trace->name,
            "result" => $trace->result,
            "startTime" => $trace->startTime,
            "endTime" => $trace->endTime,
            "errors" => $trace->errors,
            "warnings" => $trace->warnings,
            "logs" => $trace->logs,
            "duration" => null,
            "subTraces" => array_reduce($trace->subTraces, function ($carry, $subTrace) use ($options) {
                if (!$subTrace->hidden) {
                    $carry[] = $subTrace->result($options)->asArray();
                }
                return $carry;
            }, [])
        ];

        if ($options[self::OPTION_WITH_DURATION]) {
            $resultArray["duration"] = $this->calculateDuration($resultArray["startTime"], $resultArray["endTime"]);
        } else {
            unset($resultArray["duration"]);
        }

        if ($options[self::OPTION_REDUCED]) {
            $resultArray = $this->reduceResult($resultArray);
        }

        if ($options[self::OPTION_WITH_HUMAN_TIMES]) {
            $resultArray = $this->humanizeTimes($resultArray);
        }

        return $resultArray;
    }

    public function asJson(int $depth = 512)
    {
        return json_encode($this->asArray(), 0, $depth);
    }

    public function asHtml()
    {
        $options = $this->options;
        $trace = $this->trace;

        $resultHtml = "<div class='trace'>";
        $resultHtml .= "<h3>{$trace->name}</h3>";
        $resultHtml .= "<p><strong>Result:</strong> {$trace->result}</p>";
        $resultHtml .= "<p><strong>Start time:</strong> " . date(
            "Y-m-d H:i:s",
            $trace->startTime
        ) . "</p>";
        $resultHtml .= "<p><strong>End time:</strong> " . date("Y-m-d H:i:s", $trace->endTime) . "</p>";

        if ($options[self::OPTION_WITH_DURATION]) {
            $resultHtml .= "<p><strong>Duration:</strong> " . $this->calculateDuration($trace->startTime, $trace->endTime) . "s</p>";
        }

        if ($trace->errors) {
            $resultHtml .= "<p><strong>Errors:</strong></p>";
            $resultHtml .= "<ul>";
            foreach ($trace->errors as $error) {
                $resultHtml .= "<li>$error</li>";
            }
            $resultHtml .= "</ul>";
        }

        if ($trace->warnings) {
            $resultHtml .= "<p><strong>Warnings:</strong></p>";
            $resultHtml .= "<ul>";
            foreach ($trace->warnings as $warning) {
                $resultHtml .= "<li>$warning</li>";
            }
            $resultHtml .= "</ul>";
        }

        if ($trace->logs) {
            $resultHtml .= "<p><strong>Logs:</strong></p>";
            $resultHtml .= "<ul>";
            foreach ($trace->logs as $log) {
                $resultHtml .= "<li>$log</li>";
            }
            $resultHtml .= "</ul>";
        }

        if ($trace->subTraces) {
            $resultHtml .= "<p><strong>Sub traces:</strong></p>";
            $resultHtml .= "<ul>";
            foreach ($trace->subTraces as $subTrace) {
                $resultHtml .= $subTrace->result($options)->asHtml();
            }
            $resultHtml .= "</ul>";
        }

        $resultHtml .= "</div>";

        return $resultHtml;
    }

    /**
     * Private functions
     */

    private function reduceResult($array)
    {

        $timeDiff = $array["endTime"] - $array["startTime"];

        if ($timeDiff < 1) {
            unset($array["startTime"]);
            unset($array["endTime"]);
        }

        if (isset($array["duration"]) && $array["duration"] < 1) {
            unset($array["duration"]);
        }

        return array_filter($array, function ($value) {
            return $value != false;
        });
    }

    private function calculateDuration($startTime, $endTime)
    {
        return $endTime - $startTime;
    }

    private function humanizeTimes($array)
    {

        if (isset($array["startTime"]) && isset($array["endTime"])) {
            $array["startTime"] = date("Y-m-d H:i:s", $array["startTime"]);
            $array["endTime"] = date("Y-m-d H:i:s", $array["endTime"]);
        }

        if (isset($array["duration"])) {
            $duration = $array["duration"];
            $hours = floor($duration / 3600);
            $minutes = floor(($duration / 60) % 60);
            $seconds = $duration % 60;
            $array["duration"] = ($hours > 0 ? $hours . "h " : "") . ($minutes > 0 ? $minutes . "min " : "") . ($seconds > 0 ? $seconds . "s" : "");
        }

        return $array;
    }
}
