# PHP Execution Tracker

A PHP library to track the execution of a process. It allows you to track the time difference between processes, debug, logs, warnings and errors.

> **Important**: Currently, I use this same library in production, but it is still subject to potential significant changes due to the early version it is in.

- [PHP Execution Tracker](#php-execution-tracker)
  - [Usage](#usage)
    - [Result formats](#result-formats)
      - [Array](#array)
      - [JSON](#json)
      - [HTML](#html)
    - [Result options](#result-options)
      - [Reduced](#reduced)
      - [With durations](#with-durations)
      - [With human readable times](#with-human-readable-times)
    - [Hiding traces](#hiding-traces)
  - [License](#license)

## Usage

Install the library using composer:

```bash
composer require chus/php-execution-tracker
```

Import the library:

```php
require_once __DIR__ . '/vendor/autoload.php';

use ExecutionTracker/Tracker;
```

Then, you can track the execution of a process like this:

```php
// Start tracking the execution and get the trace object
$trace = Tracker::track("Count the cats in the array");

$animals = ['🐵', '🐱', '🐶', '🐷', '🐱', '🐴', '🐱', '🐸', '🐰'];

$cats = 0;

foreach ($animals as $key => $animal) {

    if($animal === '🐱') {
        $trace->log("Cat found! We have $cats cats so far");
        $cats++;
    }

}

// End the trace and set the result
$trace->end("$cats cats found in the array");

// Print the result as JSON
echo $trace->result()->asJson();
```

Output:

```json
{
    "name": "Count the cats in the array",
    "result": "3 cats found in the array",
    "startTime": 1713383993.159653,
    "endTime": 1713383993.159678,
    "errors": [],
    "warnings": [],
    "logs": [
        "Cat found! We have 0 cats so far",
        "Cat found! We have 1 cats so far",
        "Cat found! We have 2 cats so far"
    ],
    "subTraces": []
}
```

### Result formats

You can get the result in different formats:

#### Array

```php
// Result as an array
$array = $trace->result()->asArray();
```

#### JSON

```php
// Result as a JSON string
$json = $trace->result()->asJson();
```

#### HTML

```php
// Result as HTML
$html = $trace->result()->asHtml();
```

### Result options

You can also get the result with different options:

- [PHP Execution Tracker](#php-execution-tracker)
  - [Usage](#usage)
    - [Result formats](#result-formats)
      - [Array](#array)
      - [JSON](#json)
      - [HTML](#html)
    - [Result options](#result-options)
      - [Reduced](#reduced)
      - [With durations](#with-durations)
      - [With human readable times](#with-human-readable-times)
    - [Hiding traces](#hiding-traces)
  - [License](#license)

#### Reduced

Get the result with a reduced version of the trace. This will
remove the elements with false values (empty arrays, empty strings, etc.).
Start and end times will be removed if the duration is minor than 1 second.

```php
$array = $trace->result([
    'reduced' => true	
])->asJson();
```

Output:

```json
{
    "name": "Count the cats in the array",
    "result": "3 cats found in the array",
    "logs": [
        "Cat found! We have 0 cats so far",
        "Cat found! We have 1 cats so far",
        "Cat found! We have 2 cats so far"
    ]
}
```

#### With durations

Get the result indicating all traces duration. This will add the
duration of each trace to the result.

```php
$array = $trace->result([
    'withDurations' => true
])->asJson();
```

Output:

```json
{
    "name": "Count the cats in the array",
    "result": "3 cats found in the array",
    "startTime": 1713383993.159653,
    "endTime": 1713383993.159678,
    "duration": 0.000025,
    "errors": [],
    "warnings": [],
    "logs": [
        "Cat found! We have 0 cats so far",
        "Cat found! We have 1 cats so far",
        "Cat found! We have 2 cats so far"
    ],
    "subTraces": []
}
```

#### With human readable times

Get the result with human readable times. This will convert the
timestamps to human readable times.

```php
$array = $trace->result([
    'withDuration' => true,
    'withHumanTimes' => true	
])->asJson();
```

Output:

```json
{
    "name": "Count the cats in the array",
    "result": "3 cats found in the array",
    "startTime": "2024-12-06 12:26:33",
    "endTime": "2024-12-06 12:26:34",
    "duration": "0h 0min 1s",
    "errors": [],
    "warnings": [],
    "logs": [
        "Cat found! We have 0 cats so far",
        "Cat found! We have 1 cats so far",
        "Cat found! We have 2 cats so far"
    ],
    "subTraces": []
}
```

### Hiding traces

You can disable and enable the *Tracker* wethever you want. This will disable the tracking of the process and the creation of sub-traces.

Having this code:

```php
function sum($a, $b) {
    $trace = Tracker::track("Sum $a + $b");
    $result = $a + $b;
    $trace->end("Result: $result");
    return $a + $b;
}

function multiply($a, $b) {
    $trace = Tracker::track("Multiply $a * $b");
    $result = 0;
    for ($i = 0; $i < $b; $i++) {
        $result = sum($result, $a);
    }
    $trace->end("Result: $result");
    return $a * $b;
}

function power($a, $b) {
    $trace = Tracker::track("Power $a ^ $b");
    $result = 1;
    for ($i = 0; $i < $b; $i++) {
        $result = multiply($result, $a);
    }
    $trace->end("Result: $result");
    return $result;
}
```

Without disabling the tracker:

```php
$trace = Tracker::track("Calculate 3 ^ 3");
$result = power(3, 3);
$trace->end("Result: $result");

echo $trace->result([
    'reduced' => true
])->asJson();
```

Output:

```json
{
    "name": "Calculate 3 ^ 3",
    "result": "Result: 27",
    "subTraces": [
        {
            "name": "Power 3 ^ 3",
            "result": "Result: 27",
            "subTraces": [
                {
                    "name": "Multiply 1 * 3",
                    "result": "Result: 3",
                    "subTraces": [
                        {
                            "name": "Sum 0 + 1",
                            "result": "Result: 1"
                        },
                        {
                            "name": "Sum 1 + 1",
                            "result": "Result: 2"
                        },
                        {
                            "name": "Sum 2 + 1",
                            "result": "Result: 3"
                        }
                    ]
                },
                {
                    "name": "Multiply 3 * 3",
                    "result": "Result: 9",
                    "subTraces": [
                        {
                            "name": "Sum 0 + 3",
                            "result": "Result: 3"
                        },
                        {
                            "name": "Sum 3 + 3",
                            "result": "Result: 6"
                        },
                        {
                            "name": "Sum 6 + 3",
                            "result": "Result: 9"
                        }
                    ]
                },
                {
                    "name": "Multiply 9 * 3",
                    "result": "Result: 27",
                    "subTraces": [
                        {
                            "name": "Sum 0 + 9",
                            "result": "Result: 9"
                        },
                        {
                            "name": "Sum 9 + 9",
                            "result": "Result: 18"
                        },
                        {
                            "name": "Sum 18 + 9",
                            "result": "Result: 27"
                        }
                    ]
                }
            ]
        }
    ]
}
```

Disabling the tracker:

```php
$trace = Tracker::track("Calculate 3 ^ 3");

Tracker::disable();
$result = power(3, 3);
Tracker::enable();

$trace->end("Result: $result");

echo $trace->result([
    'reduced' => true
])->asJson();
```

Output:

```json
{
    "name": "Calculate 3 ^ 3",
    "result": "Result: 27"
}
```

### Clearing the tracker

You can clear the tracker to remove all the traces and reset it.

```php
$trace = Tracker::track("Wait 1 second");
sleep(1);
$trace->end("Waited 1 second");
echo $trace->result(['reduced' => true, 'withHumanTimes' => true, 'withDuration' => true])->asJson();

Tracker::clear();

$trace = Tracker::track("Wait 2 seconds");
sleep(2);
$trace->end("Waited 2 seconds");
echo $trace->result(['reduced' => true, 'withHumanTimes' => true, 'withDuration' => true])->asJson();
```

Output:

```json
{
    "name": "Wait 1 second",
    "result": "Waited 1 second",
    "startTime": "2024-04-17 21:30:55",
    "endTime": "2024-04-17 21:30:56",
    "duration": "1s"
}

{
    "name": "Wait 2 seconds",
    "result": "Waited 2 seconds",
    "startTime": "2024-04-17 21:30:56",
    "endTime": "2024-04-17 21:30:58",
    "duration": "2s"
}
```

You can also hide a unique trace:

```php
$trace1 = Tracker::track("Wait 1 second");
sleep(1);

$trace2 = Tracker::track("Wait 1 second");
sleep(1);

$trace2->end("Waited 1 second")->hide(); // Or $trace2->end(); then $trace2->hide();

$trace1->end("Waited 1 second");

echo $trace1->result(['reduced' => true, 'withHumanTimes' => true, 'withDuration' => true])->asJson();
```

> Is better to use Tracker::disable() and Tracker::enable() to disable and enable the tracker, respectively, instead of hiding traces, due to the traces will be still stored in memory.

## License

This library is licensed under the MIT license. See the [LICENSE](MIT-LICENSE) file for more information.