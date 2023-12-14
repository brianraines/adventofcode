<?php

// https://adventofcode.com/2023/day/13
/**
 * I AM NOT PROUD OF THIS SOLUTION.
 * - Brian
 */


$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// break lines into patterns
$patterns = getPatterns($lines);
//echo 'Patterns: ' . print_r($patterns, true) . "\n";

$iterations = 0;
$total = 0;
foreach ($patterns as $pattern) {
    $total += processPattern($pattern);
    $iterations++;
    if ($iterations === 3) {
        //break;
    }
}

echo 'Total: ' . $total . "\n";
// 45384 is too high
// 21308 is too low
// 27292 is too low
// 35193 is too high
// 41292 is too high

// 32283

function processPattern(array $pattern = []) : int
{
    echo "==================================================================\n";
    // echo "Processing pattern: " . print_r($pattern, true) . "\n";
    // assume the worst
    $value = 0;

    $originalPattern = [
        'pattern' => $pattern,
        'before' => false,
        'after' => false,
        'multiplier' => 100,
        'value' => 0
    ];
    $originalPatternResult = getPatternValue($originalPattern);
    echo 'Original Pattern: ' . print_r($originalPatternResult, true) . "\n";

    if ($originalPatternResult['value'] === 0) {
        $originalPattern = [
            'pattern' => rotatePattern($pattern),
            'before' => false,
            'after' => false,
            'multiplier' => 1,
            'value' => 0
        ];
        $originalPatternResult = getPatternValue($originalPattern);
        echo 'Rotated Original Pattern: ' . print_r($originalPatternResult, true) . "\n";
    }

    // get all alternate patterns
    $alternatePatterns = getAlternatePatterns($pattern);
    echo 'Alternate Patterns: by rows: ' . count($alternatePatterns['rows']) . " and by columns: " . count($alternatePatterns['columns']) . "\n";

    // check the original pattern
    $value = getPatternsValue($alternatePatterns['rows'], $originalPatternResult);
    if ($value === 0) {
        return getPatternsValue($alternatePatterns['columns'], $originalPatternResult);
    }

    // return the value
    return $value;
}

function getPatternsValue(array $patterns = [], array $originalPattern = [])
{
    $value = 0;
    foreach ($patterns as $pattern) {
        // get the value of the pattern
        $patternResult = getPatternValue($pattern, $originalPattern);

        if ($patternResult['value'] > 0
            && ($originalPattern['before'] !== $patternResult['before']
            || $originalPattern['after'] !== $patternResult['after']
            || $originalPattern['multiplier'] !== $patternResult['multiplier'])
        ) {
            $value = $patternResult['value'];
            break;
        }
    }
    return $value;
}

function getPatternValue(array $pattern = [], array $originalPattern = [])
{

    foreach ($pattern['pattern'] as $rowIndex => $row) {

        if (!empty($originalPattern) && $originalPattern['multiplier'] === $pattern['multiplier'] && $originalPattern['before'] === $rowIndex) {
            continue;
        }

        $debug = false;

        if ($debug) {
            echo "Row {$rowIndex}: " . $row . "\n";
        }

        // grab the row above
        $rowBelow = (isset($pattern['pattern'][$rowIndex + 1])) ? $pattern['pattern'][$rowIndex + 1] : false;

        if ($debug) {
            echo "Row Below: " . $rowBelow . "\n";
        }

        // check the row below
        if ($rowBelow !== false && $row === $rowBelow) {
            $before = $rowIndex;
            $after = $rowIndex + 1;

            // assume the best
            $mirrored = true;

            // validate in both directions
            for ($i = 0; $i < count($pattern['pattern']); $i++) {
                // check the row above
                $lineA = (isset($pattern['pattern'][$before - $i])) ? $pattern['pattern'][$before - $i] : false;
                $lineB = (isset($pattern['pattern'][$after + $i])) ? $pattern['pattern'][$after + $i] : false;

                if ($debug) {
                    echo "Line A: " . $lineA . "\n";
                    echo "Line B: " . $lineB . "\n";
                }

                if ($lineA !== false && $lineB !== false && $lineA !== $lineB) {
                    $mirrored = false;
                    if ($debug) {
                        echo "Not mirrored\n";
                    }
                    break;
                }
            }

            if ($mirrored) {
                if ($debug) {
                    echo "Mirrored\n";
                }
                $pattern['before'] = $before;
                $pattern['after'] = $after;
                $pattern['value'] = $pattern['multiplier'] * ($before + 1);
                break;
            }
        }

        // check the column to
    }

    return $pattern;
}

function getAlternatePatterns(array $pattern = [])
{
    $alternatePatterns = [];

    foreach ($pattern as $rowIndex => $row) {

        // for each character try changing it to the opposite
        $rowCharacters = str_split($row);

        for ($j = 0; $j < count($rowCharacters); $j++) {
            // replace one character at a time
            $newCharacters = $rowCharacters;
            $newCharacters[$j] = ($newCharacters[$j] === '#') ? '.' : '#';
            $newRow = implode('', $newCharacters);

            // replace the new row in a new pattern
            $newPattern = $pattern;
            $newPattern[$rowIndex] = $newRow;
            $alternatePatterns['rows'][] = [
                'pattern' => $newPattern,
                'before' => false,
                'after' => false,
                'multiplier' => 100,
                'value' => 0
            ];

            $alternatePatterns['columns'][] = [
                'pattern' => rotatePattern($newPattern),
                'before' => false,
                'after' => false,
                'multiplier' => 1,
                'value' => 0
            ];
        }
    }

    return $alternatePatterns;
}


function rotatePattern($pattern)
{
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

    return $rotatedPattern;
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
