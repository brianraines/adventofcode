<?php

// https://adventofcode.com/2023/day/9
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

$sum = processLines($lines);
echo "Total Sum: " . $sum . "\n";
// 1244326759 is too low
// 1581679977
// 2312053647 is too high

function processLines($lines) {
    $sum = 0;
    foreach ($lines as $line) {
        echo "----------------------------------------------------------------------------\n";
        $prevInSequence = getPrevInSequence($line);
        echo  $prevInSequence . " <= " . $line . "\n";
        $sum += $prevInSequence;
        echo "Current Sum: " . $sum . "\n";
        //exit();
    }
    return $sum;
}

function getPrevInSequence($line) {
    // split numbers by space
    $sequences = [];
    $sequences[] = explode(' ', $line);

    // build a matrices of sequences
    $sequences = getAllSequences($sequences);
    //echo "Sequences: " . print_r($sequences, true) . "\n";

    // find the prev number in the sequence
    return getPrevInTopSequence($sequences);
}

function getAllSequences(array $sequences = []) : array
{
    // get the first sequence
    $startingSequence = $sequences[0];

    echo implode(' ', $startingSequence) . "\n";

    // start a new sequence
    $sequence = [];

    $allZeros = true;
    // loop through numbers starting with the second number
    for ($i = 1; $i < count($startingSequence); $i++) {
        // find the difference between the current number and the previous number
        $difference = $startingSequence[$i] - $startingSequence[$i - 1];
        if ($difference != 0) {
            $allZeros = false;
        }
        $sequence[] = $difference;
    }


    if ($allZeros === true) {
        $sequences[] = $sequence;
        echo implode(' ', $sequence) . "\n";
        return $sequences;
    }

    return array_merge($sequences, getAllSequences([$sequence]));

}

function getPrevInTopSequence(array $sequences = []) : int
{
    // first, flip the order of the sequences
    $sequences = array_reverse($sequences);

    $prevInSequence = false;

    for ($index = 0; $index < count($sequences); $index++) {
        // get the current sequence
        $sequence = $sequences[$index];
        echo implode(' ', $sequence) . (($index === 0) ? " 0" : "") . "\n";

        // get the last number in this sequence
        $firstNumber = $sequence[0];

        // if this is not the last sequence
        if (isset($sequences[$index + 1])) {
            // get the last number in the prev sequence
            $prevLastNumber = $sequences[$index + 1][0];

            // add the last number in this sequence to the last number in the prev sequence
            $prevInSequence = $prevLastNumber - $firstNumber;
            // add this as the first element in the next sequence
            array_unshift($sequences[$index + 1], $prevInSequence);
        } else {
            // if this is the last sequence, then the prev number in the sequence is the last number in this sequence
            $prevInSequence = $firstNumber;
        }

    }
    //echo "Prev in sequence: " . $prevInSequence . "\n";

    return $prevInSequence;
}