<?php

namespace ExecutionTracker;

require_once "Track.php";

class Tracker
{

    /** @var Track */
    private static $mainTrack;

    /** @var Track[] */
    private static $tracks = [];

    /**
     * @param string $name
     * 
     * @return Track The new track
     */
    public static function track($name)
    {

        $track = new Track($name);

        if (!self::$mainTrack) {
            self::$mainTrack = $track;
        }

        $parentTrack = end(self::$tracks);

        while ($parentTrack && $parentTrack->isFinished()) {
            $parentTrack = self::$tracks[array_search($parentTrack, self::$tracks) - 1];
        }

        if ($parentTrack) {
            $parentTrack->subTracks[] = $track;
        }

        self::$tracks[] = $track;

        return $track;
    }

    /**
     * @return Track The main track
     */
    public static function getMainTrack()
    {
        return self::$mainTrack;
    }

    /**
     * Clear all tracks
     */
    public static function clear()
    {
        self::$tracks = [];
    }

}
