<?php

// https://adventofcode.com/2023/day/8
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// slice the array to get the first line
$firstLine = array_slice($lines, 0, 1);

// the first line is list of steps
$steps = str_split(trim($firstLine[0]));

// convert characters to array
echo "Steps (" . count($steps) . "): " . implode(", ", $steps) . "\n";

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
//echo "Paths: " . print_r($paths, true) . "\n";

// get all the paths that end in A
$pathsEndingInA = [];
foreach ($paths as $location => $path) {
    // if the $location ends in A
    if (substr($location, -1) === 'A') {
        $pathsEndingInA[] = $location;
    }
}

/*
$pathsEndingInA = [
    'DNA', // 20569
    'HNA', // 18727
    'AAA', // 14429
    //'LMA', // 13201
    //'VGA', // 18113
    //'LLA' // 22411
];

all divisible by 307, the number of directions

// LCM = 10921547990923
*/

echo "Paths ending in A: " . print_r($pathsEndingInA, true) . "\n";

// for each path that ends in A, get the number of steps to get to end with Z
$pathSteps = [];
foreach ($pathsEndingInA as $location) {
    $pathSteps[$location] = getStepsToZ($location, $paths, $steps);
}
echo "Path steps to end in Z: " . print_r($pathSteps, true) . "\n";

// get the LCM of the path steps
$stepsToZ = array_values($pathSteps);
$totalStepsRequired = getLowestCommonMultiplier($stepsToZ);
echo "Total steps required: $totalStepsRequired \n";

/**
 * Undocumented function
 *
 * @param string $location Location
 * @param array  $paths    Paths
 * @param array  $steps    Steps
 *
 * @return integer
 */
function getStepsToZ(string $location, array $paths, array $steps) : int
{
    $stepsTaken = 0;
    $currentStep = 0;
    while (substr($location, -1) !== 'Z') {

        if (isset($steps[$currentStep]) === false) {
            $currentStep = 0;
        }

        // get the current step
        $step = $steps[$currentStep];

        // get the current location
        $currentLocation = $paths[$location];

        // get the next location
        $nextLocation = $currentLocation[$step];

        //echo "Step $location-" . $step . ": $location => $nextLocation \n";

        // increment the steps taken
        $stepsTaken++;
        $currentStep++;

        // set the location to the next location
        $location = $nextLocation;
    }

    return $stepsTaken;
}

/**
 * Get Greatest Common Divisor
 *
 * @param integer $a First number
 * @param integer $b Second number
 *
 * @return integer Common Divisor
 */
function getGreatestCommonDivisor($a, $b)
{
    if ($b == 0) {
        return $a;
    }

    return getGreatestCommonDivisor($b, $a % $b);
}

/**
 * Get Least Common Multiple of n numbers
 *
 * @param array $numbers Array of numbers
 *
 * @return integer Least Common Multiple
 */
function getLowestCommonMultiplier(array $numbers = []) : int
{
    // Initialize result
    $lcm = $numbers[0];
    $n = count($numbers);

    // ans contains LCM of
    // arr[0], ..arr[i]
    // after i'th iteration,
    for ($i = 1; $i < $n; $i++) {
        $lcm = ((($numbers[$i] * $lcm)) / (getGreatestCommonDivisor($numbers[$i], $lcm)));
    }

    return $lcm;
}
