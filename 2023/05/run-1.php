<?php

// https://adventofcode.com/2023/day/5
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// build the maps
$maps = buildMaps($lines);
//echo "Maps: " . print_r($maps, true) . "\n";

// populate all the seed data
$seeds = hydrateSeeds($maps);
echo "Seeds: " . print_r($seeds, true) . "\n";

// find the seed with the lowest location
$lowestLocation = findLowestLocation($seeds);
echo "Lowest Location: " . $lowestLocation . "\n";

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
                $seeds = explode(' ', trim($lineData));
                foreach ($seeds as $seed) {
                    $maps['seeds'][] = [
                        'seed' => $seed,
                        'soil' => '',
                        'fertilizer' => '',
                        'water' => '',
                        'light' => '',
                        'temperature' => '',
                        'humidity' => '',
                        'location' => ''
                    ];
                }
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
            $destinationMax = $destinationMin + $range - 1;

            $maps[$currentMap][] = [
                'sourceMin' => $sourceMin,
                'sourceMax' => $sourceMax,
                'destinationMin' => $destinationMin,
                'destinationMax' => $destinationMax
            ];
        }
    }
    return $maps;
}

function hydrateSeeds(array $maps = []): array
{
    $seeds = [];

    // loop through the seeds
    foreach ($maps['seeds'] as $seed) {
        $seeds[] = hydrateSeed($seed, $maps);
    }

    return $seeds;
}

function hydrateSeed(array $seed = [], array $maps = []): array
{

    // get the destination
    $seed['soil'] = getDestination($seed['seed'], $maps['seed-to-soil']);
    $seed['fertilizer'] = getDestination($seed['soil'], $maps['soil-to-fertilizer']);
    $seed['water'] = getDestination($seed['fertilizer'], $maps['fertilizer-to-water']);
    $seed['light'] = getDestination($seed['water'], $maps['water-to-light']);
    $seed['temperature'] = getDestination($seed['light'], $maps['light-to-temperature']);
    $seed['humidity'] = getDestination($seed['temperature'], $maps['temperature-to-humidity']);
    $seed['location'] = getDestination($seed['humidity'], $maps['humidity-to-location']);

    //echo "Seed: " . print_r($seed, true) . "\n";

    return $seed;
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

function findLowestLocation(array $seeds = []) : int
{
    $lowest = false;

    // loop through the seeds
    foreach ($seeds as $seed) {
        // check if the location is lower than the current lowest
        if ($lowest == false || $seed['location'] < $lowest) {
            // if so, set the new lowest
            $lowest = $seed['location'];
        }
    }

    return $lowest;
}
