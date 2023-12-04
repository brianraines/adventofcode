<?php

// Day 2: https://adventofcode.com/2023/day/3
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// start a sum
$sum = 0;

// lets start looking with lines
foreach ($lines as $lineIndex => $line) {

    // split the line into an array of characters
    $currentCharacters = str_split($line);

    // set the below characters
    $aboveCharacters = (isset($lines[$lineIndex - 1])) ? str_split($lines[$lineIndex - 1]) : [];

    // set the below characters
    $belowCharacters = (isset($lines[$lineIndex + 1])) ? str_split($lines[$lineIndex + 1]) : [];

    echo "------------------------------------------------------------\n";
    echo "Current: $lineIndex\n";
    echo ((isset($lines[$lineIndex - 1])) ? $lines[$lineIndex - 1] : "None") . "\n";
    echo $line . "\n";
    echo ((isset($lines[$lineIndex + 1])) ? $lines[$lineIndex + 1] : "None") . "\n";

    // loop through all characters in the current line
    foreach ($currentCharacters as $characterIndex => $currentCharacter) {
        // are we looking at a gear?
        if ($currentCharacter == "*") {
            echo "------------------------------\n";
            $adjacentNumbers = [];

            // check if top left is a number
            $topLeft = (isset($aboveCharacters[$characterIndex - 1])) ? $aboveCharacters[$characterIndex - 1] : "";
            if (is_numeric($topLeft)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($aboveCharacters, $characterIndex - 1);
            }

            // check if top is a number
            $top = (isset($aboveCharacters[$characterIndex])) ? $aboveCharacters[$characterIndex] : "";
            if (is_numeric($top)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($aboveCharacters, $characterIndex);
            }

            // check if top right is a number
            $topRight = (isset($aboveCharacters[$characterIndex + 1])) ? $aboveCharacters[$characterIndex + 1] : "";
            if (is_numeric($topRight)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($aboveCharacters, $characterIndex + 1);
            }

            // check if left is a number
            $left = (isset($currentCharacters[$characterIndex - 1])) ? $currentCharacters[$characterIndex - 1] : "";
            if (is_numeric($left)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($currentCharacters, $characterIndex - 1);
            }

            // check if right is a number
            $right = (isset($currentCharacters[$characterIndex + 1])) ? $currentCharacters[$characterIndex + 1] : "";
            if (is_numeric($right)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($currentCharacters, $characterIndex + 1);
            }

            // check if bottom left is a number
            $bottomLeft = (isset($belowCharacters[$characterIndex - 1])) ? $belowCharacters[$characterIndex - 1] : "";
            if (is_numeric($bottomLeft)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($belowCharacters, $characterIndex - 1);
            }

            // check if bottom is a number
            $bottom = (isset($belowCharacters[$characterIndex])) ? $belowCharacters[$characterIndex] : "";
            if (is_numeric($bottom)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($belowCharacters, $characterIndex);
            }

            // check if bottom right is a number
            $bottomRight = (isset($belowCharacters[$characterIndex + 1])) ? $belowCharacters[$characterIndex + 1] : "";
            if (is_numeric($bottomRight)) {
                // get the full number
                $adjacentNumbers[] = getFullNumber($belowCharacters, $characterIndex + 1);
            }

            echo $topLeft . $top . $topRight . "\n";
            echo $left . $currentCharacter . $right . "\n";
            echo $bottomLeft . $bottom . $bottomRight . "\n";

            // make the adjacent numbers unique
            $adjacentNumbers = array_unique($adjacentNumbers);

            // if there are exactly 2 adjacent numbers
            if (count($adjacentNumbers) == 2) {
                echo "Is Valid: Yes\n";
                echo "Adjacent Numbers: " . implode(", ", $adjacentNumbers) . "\n";

                // get first and last number
                $lastNumber = array_pop($adjacentNumbers);
                $firstNumber = array_pop($adjacentNumbers);

                // multiply the numbers together
                $gearSum = $firstNumber * $lastNumber;
                echo "Gear Sum: $gearSum\n";

                // add to the total sum
                $sum += $gearSum;
            } else {
                echo "Is Valid: No\n";
            }
        }
    }
}

echo "Gear Sum: $sum\n";

function getFullNumber($characters, $index) : int
{
    // get the full number
    $numbers = [];

    // set the current number
    $numbers[$index] = $characters[$index];

    // get the left numbers
    $currentIndex = $index;
    while (isset($characters[$currentIndex - 1]) && is_numeric($characters[$currentIndex - 1])) {
        $numbers[$currentIndex - 1] = $characters[$currentIndex - 1];
        $currentIndex--;
    }

    // get the right numbers
    $currentIndex = $index;
    while (isset($characters[$currentIndex + 1]) && is_numeric($characters[$currentIndex + 1])) {
        $numbers[$currentIndex + 1] = $characters[$currentIndex + 1];
        $currentIndex++;
    }

    // put the numbers in order by index
    ksort($numbers);

    // return the full number
    $fullNumber = implode("", $numbers);
    return $fullNumber * 1;
}