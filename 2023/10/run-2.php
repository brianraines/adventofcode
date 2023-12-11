<?php

// https://adventofcode.com/2023/day/10
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// load every character of every line into a matrix array
$matrix = [];

// start a coordinate system at 0,0 for S.
$startingRow = false;
$startingColumn = false;

$totalRows = count($lines);
$totalColumns = strlen($lines[0]);

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
//echo "Starting direction: $direction\n";

// keep track of path coordinates
$pathCoordinates = [];
$output = [];

$totalSteps = getStepsToS($row, $col, $direction);
//echo "Total steps: $totalSteps\n";
// 13640

$farthestStep = $totalSteps / 2;
//echo "Farthest step: $farthestStep\n";
// part 1: 6820

//echo "Path coordinates: " . print_r($pathCoordinates, true) . "\n";

// redraw the map marking all non-path coordinates as X
redrawMap($totalRows, $totalColumns);

floodInnerMap($totalRows, $totalColumns);

$insideCount = 0;
foreach ($output as $row) {
    foreach ($row as $col) {
        if ($col === "I") {
            $insideCount++;
        }
    }
    echo implode('', $row) . "\n";
}

echo "Inside count: $insideCount\n";
// part two: inside 337

//write to output.txt
$fp = fopen('output.txt', 'w');
foreach ($output as $row) {
    fwrite($fp, implode('', $row) . "\n");
}

function floodInnerMap($totalRows, $totalColumns)
{
    global $output;
    global $pathCoordinates;

    for ($row = 0; $row < $totalRows; $row++) {
        $pipeCount = 0;
        $upperHalfPipeCount = 0;
        $lowerHalfPipeCount = 0;

        for ($col = 0; $col < $totalColumns; $col++) {
            //echo "Pipe count: $pipeCount\n";

            if (isset($pathCoordinates[$row])
                && isset($pathCoordinates[$row][$col])
            ) {
                if ($output[$row][$col] === '│' || $output[$row][$col] === 'S') {
                    $pipeCount++;
                }

                if (in_array($output[$row][$col], ['┘', '└'])) {
                    $upperHalfPipeCount++;
                }

                if (in_array($output[$row][$col], ['┐', '┌'])) {
                    $lowerHalfPipeCount++;
                }
            } else {
                if ($pipeCount === 0 || $pipeCount % 2 === 0) {
                    $output[$row][$col] = "O";
                } else {
                    $output[$row][$col] = "I";
                }
            }

            if ($upperHalfPipeCount > 0 && $lowerHalfPipeCount > 0) {
                $pipeCount++;
                $upperHalfPipeCount--;
                $lowerHalfPipeCount--;
            }
        }

    }
}

function redrawMap($totalRows, $totalColumns)
{
    global $matrix;
    global $pathCoordinates;
    global $output;

    for ($row = 0; $row < $totalRows; $row++) {

        for ($col = 0; $col < $totalColumns; $col++) {
            if (isset($pathCoordinates[$row][$col])) {
                $coordinate = getPathCharacter($pathCoordinates[$row][$col]);
            } else {
                $coordinate = "X";
            }
            $output[$row][$col] = $coordinate;
        }
    }

    return $output;
}

function getPathCharacter($character)
{
    return match ($character) {
        'J' => '┘',
        'L' => '└',
        '7' => '┐',
        'F' => '┌',
        '|' => '│',
        '-' => '─',
        default => 'S'
    };

}

function getStepsToS($row, $col, $direction)
{
    global $matrix;
    global $pathCoordinates;

    $steps = 0;
    $youAreHere = false;

    while ($youAreHere !== 'S') {
        $pathCoordinates[$row][$col] = $youAreHere;

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
        //echo "You are now at $youAreHere\n";

        $steps++;

        if ($youAreHere !== 'S') {
            $direction = getNewDirection($youAreHere, $direction);

            //echo "You are heading $direction next.\n";
        }

        // echo "You have taken $steps steps.\n";

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
