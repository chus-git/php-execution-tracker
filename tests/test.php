<?php

use PHPUnit\Framework\TestCase;
use ExecutionTracker\Tracker;

function sum($a, $b)
{
    $trace = Tracker::track("Sum $a + $b");
    $result = $a + $b;
    $trace->end("Obtained $result");
    return $a + $b;
}

function multiply($a, $b)
{
    $trace = Tracker::track("Multiply $a * $b");
    $result = 0;
    for ($i = 0; $i < $b; $i++) {
        $result = sum($result, $a);
    }
    $trace->end("Obtained $result");
    return $a * $b;
}

function power($a, $b)
{
    $trace = Tracker::track("Power $a ^ $b");
    $result = 1;
    for ($i = 0; $i < $b; $i++) {
        $result = multiply($result, $a);
    }
    $trace->end("Obtained $result");
    return $result;
}

class Test extends TestCase
{
    public function testCalculateTotalSubTraces()
    {

        Tracker::clear();

        $totalSubTraces = 7;

        $trace = Tracker::track("Starting the test...");

        for ($i = 0; $i < $totalSubTraces; $i++) {
            $subTrace = Tracker::track("Sub trace $i");
            $subTrace->end("Sub trace $i completed");
        }

        $trace->end("Test completed");

        $this->assertEquals($totalSubTraces, $trace->totalSubTraces());
    }

    public function testClearTracker()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->end("Test completed");

        Tracker::clear();

        $this->assertNull(Tracker::getMainTrace());
    }

    public function testDuration()
    {

        Tracker::clear();

        $sleepTime = 100000;

        $trace = Tracker::track("Starting the test...");

        usleep($sleepTime);

        $trace->end("Test completed");

        $duration = ($trace->endTime - $trace->startTime);

        $this->assertGreaterThan($sleepTime / 1000000, $duration);
        $this->assertLessThan($sleepTime * 1.2 / 1000000, $duration);
    }

    public function testDisableTracker()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        Tracker::disable();

        $hiddenTrace = Tracker::track("Hidden trace");
        $hiddenTrace->end("Hidden trace completed");

        Tracker::enable();

        $trace->end("Test completed");

        $this->assertEquals(0, $trace->totalSubTraces());
    }

    public function testResultReducedAsArray()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->log("This is a log message");
        $trace->warning("This is a warning message");
        $trace->error("This is an error message");

        $trace->end("Test completed");

        $result = $trace->result([
            "reduced" => true
        ])->asArray();

        $expectedResult = [
            "name" => "Starting the test...",
            "result" => "Test completed",
            "logs" => ["This is a log message"],
            "warnings" => ["This is a warning message"],
            "errors" => ["This is an error message"]
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testResultReducedAsJson()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->log("This is a log message");
        $trace->warning("This is a warning message");
        $trace->error("This is an error message");

        $trace->end("Test completed");

        $result = $trace->result([
            "reduced" => true
        ])->asJson();

        $expectedResult = json_encode([
            "name" => "Starting the test...",
            "result" => "Test completed",
            "logs" => ["This is a log message"],
            "warnings" => ["This is a warning message"],
            "errors" => ["This is an error message"]
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testResultWithHumanTimes()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->end("Test completed");

        $result = $trace->result([
            "withHumanTimes" => true
        ])->asArray();

        $this->assertIsNotFloat($result["startTime"]);
        $this->assertIsNotFloat($result["endTime"]);
    }

    public function testResultWithTimestamps()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->end("Test completed");

        $result = $trace->result()->asArray();

        $this->assertIsFloat($result["startTime"]);
        $this->assertIsFloat($result["endTime"]);
    }

    public function testResultWithDuration()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->end("Test completed");

        $result = $trace->result([
            "withDuration" => true
        ])->asArray();

        $this->assertArrayHasKey("duration", $result);
    }

    public function testResultWithoutDuration()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->end("Test completed");

        $result = $trace->result([
            "withDuration" => false
        ])->asArray();

        $this->assertArrayNotHasKey("duration", $result);
    }

    public function testResultWithDurationWithHumanTimes()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $trace->end("Test completed");

        $result = $trace->result([
            "withDuration" => true,
            "withHumanTimes" => true
        ])->asArray();

        $this->assertIsNotFloat($result["duration"]);
    }

    public function testRenderLargeResultAsJson()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $numberOfSubTraces = 10000;

        for ($i = 0; $i < $numberOfSubTraces; $i++) {
            $subTrace = Tracker::track("Sub trace $i");
            $subTrace->end("Sub trace $i completed");
        }

        $trace->end("Test completed");

        $expectedJson = json_encode([
            "name" => "Starting the test...",
            "result" => "Test completed",
            "subTraces" => array_map(function ($i) {
                return [
                    "name" => "Sub trace $i",
                    "result" => "Sub trace $i completed"
                ];
            }, range(0, $numberOfSubTraces - 1))
        ]);

        $result = $trace->result(['reduced' => true])->asJson();

        $this->assertJson($result);
        $this->assertEquals($expectedJson, $result);
    }

    public function testHideTraces()
    {

        Tracker::clear();

        $trace = Tracker::track("Starting the test...");

        $subTrace = Tracker::track("Hidden trace");
        $subTrace->end("Hidden trace completed", true);

        $subTrace = Tracker::track("Hidden trace");
        $subTrace->end("Hidden trace completed");
        $hiddingResult = $subTrace->hide();
        $this->assertTrue($hiddingResult);

        Tracker::disable();
        $subTrace = Tracker::track("Hidden trace");
        $subTrace->end("Hidden trace completed");
        Tracker::enable();

        $trace->end("Test completed");

        $this->assertEquals(0, $trace->totalSubTraces());
    }

    public function testRenderJson()
    {

        Tracker::clear();

        $trace = Tracker::track("Calculate 3 ^ 3");
        $result = power(3, 3);
        $trace->end("Obtained $result");

        $expectedResult = '{"name":"Calculate 3 ^ 3","result":"Obtained 27","subTraces":[{"name":"Power 3 ^ 3","result":"Obtained 27","subTraces":[{"name":"Multiply 1 * 3","result":"Obtained 3","subTraces":[{"name":"Sum 0 + 1","result":"Obtained 1"},{"name":"Sum 1 + 1","result":"Obtained 2"},{"name":"Sum 2 + 1","result":"Obtained 3"}]},{"name":"Multiply 3 * 3","result":"Obtained 9","subTraces":[{"name":"Sum 0 + 3","result":"Obtained 3"},{"name":"Sum 3 + 3","result":"Obtained 6"},{"name":"Sum 6 + 3","result":"Obtained 9"}]},{"name":"Multiply 9 * 3","result":"Obtained 27","subTraces":[{"name":"Sum 0 + 9","result":"Obtained 9"},{"name":"Sum 9 + 9","result":"Obtained 18"},{"name":"Sum 18 + 9","result":"Obtained 27"}]}]}]}';
        $result = $trace->result([
            'reduced' => true
        ])->asJson();

        $this->assertJson($result);
        $this->assertEquals($expectedResult, $result);
    }
}
