<?php

// https://adventofcode.com/2023/day/4
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

/*
Card 1: 41 48 83 86 17 | 83 86  6 31 17  9 48 53
Card 2: 13 32 20 16 61 | 61 30 68 82 17 32 24 19
Card 3:  1 21 53 59 44 | 69 82 63 72 16 21 14  1
Card 4: 41 92 73 84 69 | 59 84 76 51 58  5 54 83
Card 5: 87 83 26 28 32 | 88 30 70 12 93 22 82 36
Card 6: 31 18 13 56 72 | 74 77 10 23 35 67 36 11
 */

// start a points sum
$scratchcards = [];

// lets start looking with lines
foreach ($lines as $lineIndex => $line) {
    // split the line by : then by |
    $lineParts = explode(':', $line);
    $card = $lineParts[0];

    // remove any non-numeric characters to get the card number
    $cardNumber = trim(str_replace('Card ', '', $card));

    // add this card to the scratchcards array
    if (isset($scratchcards[$cardNumber])) {
        $scratchcards[$cardNumber]++;
    } else {
        $scratchcards[$cardNumber] = 1;
    }

    // split the game by |
    $game = explode('|', $lineParts[1]);

    echo "------------------------------------------------------------\n";
    echo "Card: $cardNumber\n";

    // split the game by space
    $winningNumbers = explode(' ', str_replace('  ', ' ', trim($game[0])));
    echo "Winning Numbers: " . implode(', ', $winningNumbers) . "\n";

    $numbers = explode(' ', str_replace('  ', ' ', trim($game[1])));
    echo "Numbers: " . implode(', ', $numbers) . "\n";

    // check if the card is a winner
    $matches = getMatches($winningNumbers, $numbers);
    echo "Matches: $matches\n";

    //echo "Before Scratchcards: " . print_r($scratchcards, true) . "\n";

    // if there are matches, add the copies to the scratchcards array
    if ($matches > 0) {
        // find the number of instances of the card to know how many copies to add
        $cardInstances = (isset($scratchcards[$cardNumber])) ? $scratchcards[$cardNumber] : 1;
        echo "Current Card Instances: $cardInstances\n";

        // start an iterator at the current card number
        $nextCardNumber = $cardNumber;

        for ($i = 0; $i < $matches; $i++) {
            // for each match, add a copy of the card to the scratchcards array
            $nextCardNumber++;

            // if the next card number is in the lines array, add it to the scratchcards array
            if (isset($lines[$nextCardNumber - 1])) {
                if (isset($scratchcards[$nextCardNumber])) {
                    $scratchcards[$nextCardNumber] += $cardInstances;
                } else {
                    $scratchcards[$nextCardNumber] = $cardInstances;
                }
            }
        }
    }

    //echo "After Scratchcards: " . print_r($scratchcards, true) . "\n";
    echo "Scratchcard Count: " . getScratchcardCount($scratchcards) . "\n";
}


function getMatches($winningNumbers, $numbers)
{
    $matches = 0;
    // check if the numbers are in the winning numbers
    foreach ($numbers as $number) {
        if (in_array($number, $winningNumbers)) {
            $matches++;
        }
    }

    return $matches;
}

function getScratchcardCount($scratchcards)
{
    $count = 0;
    foreach ($scratchcards as $scratchcardCount) {
        $count += $scratchcardCount;
    }

    return $count;
}
