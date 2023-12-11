<?php

// https://adventofcode.com/2023/day/10
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// load every character of every line into a matrix array
$matrix = [];

// start a coordinate system at 0,0 for S.
$startingRow = false;
$startingColumn = false;

foreach ($lines as $lineIndex => $line) {

    // find the starting point
    if (strpos($line, 'S') !== false) {
        $startingRow = $lineIndex;
    }

    $row = str_split($line);

    if ($startingRow !== false && $startingColumn === false) {
        $startingColumn = array_search('S', $row);
    }

    $matrix[] = $row;
}

// where are we?
echo "Starting at $startingRow row, $startingColumn col\n";
echo "Proof: '{$matrix[$startingRow][$startingColumn]}'\n";

// start at the starting point
$row = $startingRow;
$col = $startingColumn;

// keep track of the direction we're going
// up = | or F or 7
// down = | or J or L
// right = - or 7 or J
// left = - or F or L

// check above
$direction = false;
if (in_array(lookUp($row, $col), ['|', 'F', '7'])) {
    $direction = 'up';
}

// check right
if ($direction === false && in_array(lookRight($row, $col), ['-', '7', 'J'])) {
    $direction = 'right';
}

// check below
if ($direction === false && in_array(lookDown($row, $col), ['|', 'J', 'L'])) {
    $direction = 'down';
}

// check left
if ($direction === false && in_array(lookLeft($row, $col), ['-', 'F', 'L'])) {
    $direction = 'left';
}

// which way are we going?
echo "Starting direction: $direction\n";

$totalSteps = getStepsToS($row, $col, $direction);
echo "Total steps: $totalSteps\n";
// 13640

$farthestStep = $totalSteps / 2;
echo "Farthest step: $farthestStep\n";
// 6820

function getStepsToS($row, $col, $direction)
{
    global $matrix;
    $steps = 0;
    $youAreHere = false;

    while ($youAreHere !== 'S') {

        switch ($direction) {
        case 'up':
            $row--;
            break;
        case 'down':
            $row++;
            break;
        case 'left':
            $col--;
            break;
        case 'right':
            $col++;
            break;
        }

        $youAreHere = $matrix[$row][$col];
        echo "You are now at $youAreHere\n";

        $steps++;

        if ($youAreHere !== 'S') {
            $direction = getNewDirection($youAreHere, $direction);

            echo "You are heading $direction next.\n";
        }

        echo "You have taken $steps steps.\n";

    }

    return $steps;
}

function getNewDirection($youAreHere, $previousDirection)
{
    switch($previousDirection) {
    case 'up':
        return getDirectionFromBelow($youAreHere);
    case 'down':
        return getDirectionFromAbove($youAreHere);
    case 'left':
        return getDirectionFromRight($youAreHere);
    case 'right':
        return getDirectionFromLeft($youAreHere);
    }
}

function getDirectionFromBelow($youAreHere)
{
    return match ($youAreHere) {
        '|' => 'up',
        '7' => 'left',
        'F' => 'right',
        default => false
    };
}

function getDirectionFromAbove($youAreHere)
{
    return match ($youAreHere) {
        '|' => 'down',
        'J' => 'left',
        'L' => 'right',
        default => false
    };
}

function getDirectionFromRight($youAreHere)
{
    return match ($youAreHere) {
        '-' => 'left',
        'F' => 'down',
        'L' => 'up',
        default => false
    };
}

function getDirectionFromLeft($youAreHere)
{
    return match ($youAreHere) {
        '-' => 'right',
        '7' => 'down',
        'J' => 'up',
        default => false
    };
}


function lookUp($row, $col)
{
    global $matrix;
    if (isset($matrix[$row - 1]) !== true) {
        return false;
    }

    return $matrix[$row - 1][$col];
}

function lookDown($row, $col)
{
    global $matrix;
    if (isset($matrix[$row + 1]) !== true) {
        return false;
    }

    return $matrix[$row - 1][$col];
}

function lookLeft($row, $col)
{
    global $matrix;
    if (isset($matrix[$row][$col - 1]) !== true) {
        return false;
    }

    return $matrix[$row][$col + 1];
}

function lookRight($row, $col)
{
    global $matrix;
    if (isset($matrix[$row][$col + 1]) !== true) {
        return false;
    }

    return $matrix[$row][$col + 1];
}
