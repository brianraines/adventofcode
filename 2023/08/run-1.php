<?php

// https://adventofcode.com/2023/day/6
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// slice the array to get the first line
$firstLine = array_slice($lines, 0, 1);

// the first line is list of steps
$steps = str_split(trim($firstLine[0]));

// convert characters to array
echo "Steps: " . implode(", ", $steps) . "\n";

// the rest of the lines are the list of decisions
$paths = [];

// loop through the rest of the lines
foreach ($lines as $lineIndex => $line) {

    // skip the first two lines
    if ($lineIndex < 2) {
        continue;
    }

    // split the line into two parts
    list($location, $directions) = explode(" = ", $line);

    // split the directions into an array of left and right
    $directions = str_replace("(", "", $directions);
    $directions = str_replace(")", "", $directions);
    list($left, $right) = explode(", ", $directions);
    $paths[$location] = [
        'L' => $left,
        'R' => $right
    ];
}
echo "Paths: " . print_r($paths, true) . "\n";

// create a while loop that starts with AAA and ends with ZZZ
$location = 'AAA';
$endLocation = 'ZZZ';
$stepsTaken = 0;
$currentStep = 0;
while ($location != $endLocation) {

    if (isset($steps[$currentStep]) === false) {
        $currentStep = 0;
    }

    // get the current step
    $step = $steps[$currentStep];

    // get the current location
    $currentLocation = $paths[$location];

    // get the next location
    $nextLocation = $currentLocation[$step];

    echo "Step $location-" . $step . ": $location => $nextLocation \n";

    // increment the steps taken
    $stepsTaken++;
    $currentStep++;

    // set the location to the next location
    $location = $nextLocation;
}

echo "Steps taken: " . $stepsTaken . "\n";
