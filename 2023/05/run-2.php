<?php

// https://adventofcode.com/2023/day/5
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// build the maps
$maps = buildMaps($lines);
//echo "Maps: " . print_r($maps, true) . "\n";

// get argument passed in
$seedSet = $argv[1] ?? false;

/*
set - seed range - lowest location
0 - 202517468  131640971 - 1880769172
1 - 1553776977 241828580 - 384658003
2 - 1435322022 100369067 - 1531539628
3 - 2019100043 153706556 - 318728750
4 - 460203450  84630899  - 469381783
5 - 3766866638 114261107 - 1165763062
6 - 1809826083 153144153 - 95181345
7 - 2797169753 177517156 - 1314084048
8 - 2494032210 235157184 - 190399026
9 - 856311572  542740109 - 37384986
*/

// populate all the seed data
$lowestLocation = getLowestLocation($maps, $seedSet);
echo "Lowest Location: " . print_r($lowestLocation, true) . "\n";

/**
 * Build the maps
 *
 * @param [type] $lines The lines of the input file
 *
 * @return array The maps
 */
function buildMaps($lines) : array
{
    // start up some maps
    $maps = [
        'seeds' => [],
        'seed-to-soil' => [],
        'soil-to-fertilizer' => [],
        'fertilizer-to-water' => [],
        'water-to-light' => [],
        'light-to-temperature' => [],
        'temperature-to-humidity' => [],
        'humidity-to-location' => [],
    ];

    $currentMap = '';
    // lets start looking with lines
    foreach ($lines as $lineIndex => $line) {
        // check if the line has a :
        if (strpos($line, ':') !== false) {
            // k, first check for seeds
            if (strpos($line, 'seeds:') !== false) {
                $currentMap = 'seeds';
                $lineData = str_replace('seeds:', '', $line);
                $maps['seeds'] = explode(' ', trim($lineData));
            } else {
                // k, we have a new map
                $currentMap = str_replace(' map:', '', $line);
            }
        } else if (trim($line) != '') {
            // k, we have a line of data
            $lineData = explode(' ', trim($line));
            $range = trim($lineData[2]) * 1;

            $sourceMin = trim($lineData[1]) * 1;
            $sourceMax = $sourceMin + $range - 1;
            $destinationMin = trim($lineData[0]) * 1;

            $maps[$currentMap][] = [
                'sourceMin' => $sourceMin,
                'sourceMax' => $sourceMax,
                'destinationMin' => $destinationMin
            ];
        }
    }
    return $maps;
}

function getLowestLocation(array $maps = [], $seedSet = false): int
{
    $lowestLocation = false;

    // start with seeds
    $seedRanges = array_chunk($maps['seeds'], 2);


    echo "Seed Ranges: " . count($seedRanges) . "\n";

    if ($seedSet !== false) {
        $seedRanges = array_slice($seedRanges, $seedSet, 1);
    }

    echo "Seed Ranges: " . print_r($seedRanges, true) . "\n";

    // foreach seed chunk
    foreach ($seedRanges as $seedRange) {

        $startingSeed = $seedRange[0];
        $endingSeed = $startingSeed + $seedRange[1] - 1;
        echo "----------------------------------------\n";
        echo "Processing seeds $startingSeed to $endingSeed\n";

        $progress = 0;
        $progressTotal = $endingSeed - $startingSeed;
        $progressBroadcasts = [];

        // for the range of seeds
        for ($seed = $startingSeed; $seed <= $endingSeed; $seed++) {

            $progress++;
            //echo "Progress: $progress of $progressTotal\n";
            $progressPercentage = round(($progress / $progressTotal) * 100, 2);
            //echo "Progress: $progressPercentage%\n";
            if (fmod($progressPercentage, 5) == 0.0 && in_array($progressPercentage, $progressBroadcasts) == false) {
                echo "Progress: $progressPercentage%\n";
                $progressBroadcasts[] = $progressPercentage;
                echo "Lowest Location: $lowestLocation\n";
            }

            // hydrate the seed
            $location = getLocation($seed, $maps);
            //echo "Seed $seed Location: $location\n";

            // check if the location is lower than the current lowest
            if ($lowestLocation === false || $location < $lowestLocation) {
                // if so, set the new lowest
                $lowestLocation = $location;
            }
        }
    }

    return $lowestLocation;
}

function getLocation(int $seed, array $maps = []): int
{
    // get the destinations
    $soil = getDestination($seed, $maps['seed-to-soil']);
    $fertilizer = getDestination($soil, $maps['soil-to-fertilizer']);
    $water = getDestination($fertilizer, $maps['fertilizer-to-water']);
    $light = getDestination($water, $maps['water-to-light']);
    $temperature = getDestination($light, $maps['light-to-temperature']);
    $humidity = getDestination($temperature, $maps['temperature-to-humidity']);
    return getDestination($humidity, $maps['humidity-to-location']);
}

function getDestination(int $seed, array $map = []) : int
{
    // set the default destination
    $destination = false;

    // loop through the map
    foreach ($map as $mapData) {
        // check if the seed is in the source range
        if ($seed >= $mapData['sourceMin'] && $seed <= $mapData['sourceMax']) {
            // if so, calculate the destination using the delta
            $destination = $mapData['destinationMin'] + ($seed - $mapData['sourceMin']);
        }
    }

    return ($destination !== false) ? $destination : $seed;
}
