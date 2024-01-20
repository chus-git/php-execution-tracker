require_once "/vendor/autoload.php";

use ExecutionTracker\Tracker;

$track = Tracker::track("Wait 1 second");
sleep(1);
$track->end("Waited 1 second");

$track->asJson();