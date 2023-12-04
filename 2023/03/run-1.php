<?php

// Day 2: https://adventofcode.com/2023/day/3
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// start a sum
$partNumberSum = 0;

// lets start looking with lines
foreach ($lines as $lineIndex => $line) {

    // split the line into an array of characters
    $currentCharacters = str_split($line);

    // set the below characters
    $aboveCharacters = (isset($lines[$lineIndex - 1])) ? str_split($lines[$lineIndex - 1]) : [];

    // set the below characters
    $belowCharacters = (isset($lines[$lineIndex + 1])) ? str_split($lines[$lineIndex + 1]) : [];

    echo "------------------------------------------------------------\n";
    echo "Above: " . $lineIndex - 1 . "\n" . ((isset($lines[$lineIndex - 1])) ? $lines[$lineIndex - 1] : "None") . "\n";
    echo "Current: $lineIndex\n" . $line . "\n";
    echo "Below: " . $lineIndex + 1 . "\n" . ((isset($lines[$lineIndex + 1])) ? $lines[$lineIndex + 1] : "None") . "\n";

    // start a current number
    $partNumber = false;
    $adjacentIndexes = [];

    // loop through all characters in the current line
    foreach ($currentCharacters as $characterIndex => $currentCharacter) {
        // are we looking at a number?
        if (is_numeric($currentCharacter)) {
            // set the current number
            $partNumber = $partNumber . $currentCharacter;
            //echo "Current Part Number: $partNumber\n";

            // add adjacent indexes
            $adjacentIndexes[] = $characterIndex - 1;
            $adjacentIndexes[] = $characterIndex;
            $adjacentIndexes[] = $characterIndex + 1;
        } else {
            // do we have a current part number
            if ($partNumber !== false) {
                echo "------------------------------\n";

                echo "Current Part Number: $partNumber\n";

                // check if the part number is valid
                if (isValidPartNumber($aboveCharacters, $currentCharacters, $belowCharacters, $adjacentIndexes)) {
                    // add to the part number sum
                    $partNumberSum += ($partNumber * 1);
                    echo "Valid: Yes\n";
                } else {
                    echo "Valid: No\n";
                }

                // reset the part number
                $partNumber = false;
                $adjacentIndexes = [];
            }
        }
    }

    // do we still have a current part number
    if ($partNumber !== false) {
        echo "------------------------------\n";

        echo "Current Part Number: $partNumber\n";

        // check if the part number is valid
        if (isValidPartNumber($aboveCharacters, $currentCharacters, $belowCharacters, $adjacentIndexes)) {
            // add to the part number sum
            $partNumberSum += ($partNumber * 1);
            echo "Valid: Yes\n";
        } else {
            echo "Valid: No\n";
        }
    }
}

echo "Part Number Sum: $partNumberSum\n";

/**
 * Use the above characters, current characters, and below characters to
 * determine if the part number is valid.
 *
 * @param [type] $aboveCharacters   Array of characters above the current line
 * @param [type] $currentCharacters Array of characters in the current line
 * @param [type] $belowCharacters   Array of characters below the current line
 * @param [type] $adjacentIndexes   Array of adjacent indexes
 *
 * @return boolean
 */
function isValidPartNumber($aboveCharacters, $currentCharacters, $belowCharacters, $adjacentIndexes) : bool
{
    $isValid = false;

    // get a list of unique adjacent indexes
    $uniqueAdjacentIndexes = array_unique($adjacentIndexes);
    echo "Unique Adjacent Indexes: " . implode(", ", $uniqueAdjacentIndexes) . "\n";

    // start an array of adjacent characters
    $adjacentCharacters = [];
    $top = [];
    $current = [];
    $bottom = [];

    // loop through all adjacent indexes
    foreach ($uniqueAdjacentIndexes as $adjacentIndex) {
        // check above adjacent character
        if (isset($aboveCharacters[$adjacentIndex])) {
            $adjacentCharacters[] = $aboveCharacters[$adjacentIndex];
            $top[] = $aboveCharacters[$adjacentIndex];
        }
    }
    echo implode("", $top) . "\n";

    // loop through all adjacent indexes
    foreach ($uniqueAdjacentIndexes as $adjacentIndex) {
        // check above adjacent character
        if (isset($currentCharacters[$adjacentIndex])) {
            $adjacentCharacters[] = $currentCharacters[$adjacentIndex];
            $current[] = $currentCharacters[$adjacentIndex];
        }
    }
    echo implode("", $current) . "\n";

    // loop through all adjacent indexes
    foreach ($uniqueAdjacentIndexes as $adjacentIndex) {
       // check below adjacent character
        if (isset($belowCharacters[$adjacentIndex])) {
            $adjacentCharacters[] = $belowCharacters[$adjacentIndex];
            $bottom[] = $belowCharacters[$adjacentIndex];
        }
    }
    echo implode("", $bottom) . "\n";

    echo "Unique Adjacent Charcters: " . implode("", $adjacentCharacters) . "\n";

    // loop through all adjacent characters
    foreach ($adjacentCharacters as $adjacentCharacter) {
        // is the adjacent character a symbol?
        if ($isValid === false && isSymbol($adjacentCharacter)) {
            // the part number is valid
            $isValid = true;
        }
    }

    return $isValid;
}

function isSymbol($character)
{
    // is not numeric and not a period
    return (!is_numeric($character) && $character !== ".");
}
