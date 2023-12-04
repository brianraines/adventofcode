<?php

// Day 1: https://adventofcode.com/2023/day/1
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);
$replacePairs = [
    "one"   => "1",
    "two"   => "2",
    "three" => "3",
    "four"  => "4",
    "five"  => "5",
    "six"   => "6",
    "seven" => "7",
    "eight" => "8",
    "nine"  => "9"
];

// build a list of word combinations like eightwo
$replaceCombinedPairs = getWordCombinations($replacePairs);
echo "Combined pairs: " . print_r($replaceCombinedPairs, true) . "\n";

// start total at zero
$total = 0;

// loop through all lines
foreach ($lines as $line) {
    echo "------------------------------------------------------------\n";
    echo "Processing: $line\n";

    // convert to lowercase
    $lowerCaseLine = strtolower($line);

    // look for combined number words like eightwo
    $combinedNumbers = strtr($lowerCaseLine, $replaceCombinedPairs);
    //echo "Converted combined string numbers: $combinedNumbers\n";

    // look for single number words like eight
    $stringNumbers = strtr($combinedNumbers, $replacePairs);
    //echo "Converted string numbers: $stringNumbers\n";

    // remove all non-numeric characters
    $onlyNumbers = preg_replace("/[^0-9]/", "", $stringNumbers);
    echo "Only numbers: $onlyNumbers\n";

    // get first and last number
    $firstNumber = substr($onlyNumbers, 0, 1);
    $lastNumber = substr($onlyNumbers, -1);

    // concatenate to form the line number
    $lineNumber = ($firstNumber . $lastNumber) * 1;
    echo "Line Number: $lineNumber\n";

    // add to total
    $total += $lineNumber;
    echo "Total: $total\n";
}

/**
 * Get word combinations one layer deep.
 *
 * @param [type] $replaceNumbers Array of numbers to replace words
 *
 * @return void
 */
function getWordCombinations(array $replaceNumbers = []) : array
{
    // array to store combinations
    $combinations = [];

    // loop through all words
    foreach ($replaceNumbers as $firstWord => $firstNumber) {
        // loop through all words again
        foreach ($replaceNumbers as $secondWord => $secondNumber) {
            // if last letter of first word is the same as first letter of second word
            if (substr($firstWord, -1) == substr($secondWord, 0, 1)) {
                // add combination to array
                $combinations[$firstWord . substr($secondWord, 1)] = $firstNumber . $secondNumber;
            }
        }
    }

    // return combinations
    return $combinations;
}
