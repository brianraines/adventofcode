<?php

// https://adventofcode.com/2023/day/6
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

/*
Time:        35     69     68     87
Distance:   213   1168   1086   1248
Ways to win: 20     10     17     50
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

    // for loop the time
    for ($milliseconds = 0; $milliseconds < $time; $milliseconds++) {
        $speed = $milliseconds;
        $raceDistance = ($time - $milliseconds) * $speed;

        //echo "Hold time: $milliseconds, Speed: $speed, Distance: $raceDistance\n";
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

    $lineOne = str_replace('Time:        ', '', $lines[0]);
    $times = explode('     ', $lineOne);

    $lineTwo = str_replace('Distance:   ', '', $lines[1]);
    $distances = explode('   ', $lineTwo);


    for ($i = 0; $i < count($times); $i++) {
        $races[] = [
            'time' => $times[$i],
            'distance' => $distances[$i]
        ];
    }
    return $races;
}
