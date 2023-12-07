<?php

// https://adventofcode.com/2023/day/6
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

/*
Time:        35696887
Distance:   213116810861248
Ways to win:
*/

// build an array of each race
$races = getRaces($lines);
echo "Races: " . print_r($races, true) . "\n";

$score = 1;
foreach ($races as $race) {
    echo "----------------------------------------\n";
    echo "Race: " . print_r($race, true) . "\n";
    // calculate the list of hold times that would win
    $numberOfWaysToWin = getNumberOfWaysToWin($race);
    echo "Number of ways to win: " . print_r($numberOfWaysToWin, true) . "\n";
    $score = $score * $numberOfWaysToWin;
}

echo "Score: $score\n";

function getNumberOfWaysToWin($race)
{
    $numberOfWaysToWin = 0;
    $time = $race['time'];
    $distance = $race['distance'];

    $progressBroadcasts = [];
    // for loop the time
    for ($milliseconds = 0; $milliseconds < $time; $milliseconds++) {
        //echo "Progress: $progress of $progressTotal\n";
        $progress = round(($milliseconds / $time) * 100, 2);
        //echo "Progress: $progressPercentage%\n";
        if (fmod($progress, 5) == 0.0 && in_array($progress, $progressBroadcasts) == false) {
            echo "Progress: $progress%\n";
            $progressBroadcasts[] = $progress;
            echo "Number of ways to win: $numberOfWaysToWin\n";
        }

        $speed = $milliseconds;
        $raceDistance = ($time - $milliseconds) * $speed;

        if ($raceDistance >= $distance) {
            $numberOfWaysToWin++;
        }
    }

    return $numberOfWaysToWin;
}

/**
 * Read the array of lines to build
 *
 * @param array $lines The array of lines from the input file
 *
 * @return array
 */
function getRaces(array $lines = []) : array
{
    $races = [];

    $time = str_replace('Time:', '', $lines[0]);
    $time = str_replace(' ', '', $time);

    $distance = str_replace('Distance:', '', $lines[1]);
    $distance = str_replace(' ', '', $distance);

    $races[] = [
        'time' => $time,
        'distance' => $distance
    ];
    return $races;
}
