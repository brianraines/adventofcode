<?php

// https://adventofcode.com/2023/day/12
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// break lines into patterns
$patterns = getPatterns($lines);
//echo 'Patterns: ' . print_r($patterns, true) . "\n";

$iterations = 0;
$total = 0;
foreach ($patterns as $pattern) {
    $total += getPatternValue($pattern);
    $iterations++;
    if ($iterations === 5) {
        //break;
    }

}

echo 'Total: ' . $total . "\n";
// 34911

function getPatternValue(array $pattern = []) : int
{
    echo "==================================================================\n";
    echo "Processing pattern: " . print_r($pattern, true) . "\n";
    // assume the worst
    $value = 0;

    // find to array values that match that are next to each other in array keys
    $mirroredIndexes = findMirroredIndexes($pattern);
    echo "Mirrored Indexes: " . print_r($mirroredIndexes, true) . "\n";

    // if found then the multiplier is 100
    $multiplier = 100;

    if ($mirroredIndexes['before'] === false && $mirroredIndexes['after'] === false) {
        // if none found flip pattern 90degrees and look for a horizontal reflection again
        $pattern = rotatePattern($pattern);
        $mirroredIndexes = findMirroredIndexes($pattern);
        $multiplier = 1;
        echo "Mirrored Indexes: " . print_r($mirroredIndexes, true) . "\n";
    }

    if ($mirroredIndexes['before'] !== false && $mirroredIndexes['after'] !== false) {
        $value = $multiplier * ($mirroredIndexes['before'] + 1);
    }

    // return the value
    return $value;
}

function rotatePattern($pattern) {
    $rotatedPattern = [];
    // get the max length of a row
    $maxRowLength = max(array_map('strlen', $pattern));
    // iterate through each character
    for ($i = 0; $i < $maxRowLength; $i++) {
        $newRow = '';
        // iterate through each row
        for ($j = count($pattern) - 1; $j >= 0; $j--) {
            $newRow .= $pattern[$j][$i] ?? '.';
        }
        $rotatedPattern[] = $newRow;
    }

    echo "Rotated Pattern: " . print_r($rotatedPattern, true) . "\n";
    return $rotatedPattern;
}

function findMirroredIndexes(array $pattern = [])
{
    $mirroredIndexes = [
        'before' => false,
        'after' => false
    ];

    foreach ($pattern as $rowIndex => $row) {
        // grab the row above
        $rowBelow = (isset($pattern[$rowIndex + 1])) ? $pattern[$rowIndex + 1] : false;

        // check the row below
        if ($rowBelow !== false && $row === $rowBelow) {
            $before = $rowIndex;
            $after = $rowIndex + 1;

            // assume the best
            $mirrored = true;

            // validate in both directions

            for ($i = 0; $i < count($pattern); $i++) {
                // check the row above
                $lineA = (isset($pattern[$before - $i])) ? $pattern[$before - $i] : false;
                $lineB = (isset($pattern[$after + $i])) ? $pattern[$after + $i] : false;

                echo "Line A: " . print_r($lineA, true) . "\n";
                echo "Line B: " . print_r($lineB, true) . "\n";

                if ($lineA !== false && $lineB !== false && $lineA !== $lineB) {
                    $mirrored = false;
                    break;
                }
            }

            if ($mirrored) {
                $mirroredIndexes['before'] = $before;
                $mirroredIndexes['after'] = $after;
                break;
            }
        }

        // check the column to
    }

    return $mirroredIndexes;
}

function getPatterns(array $lines = []) : array
{
    // start up a collection of patterns
    $patterns = [];

    // loop through the lines and build patterns up until we hit an empty line
    $pattern = [];
    foreach ($lines as $line) {
        if (empty(trim($line))) {
            $patterns[] = $pattern;
            $pattern = [];
            continue;
        }

        // break the line into an array of characters
        $pattern[] = $line;
    }
    // grab the last pattern
    $patterns[] = $pattern;

    return $patterns;
}
