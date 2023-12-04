<?php

// Day 2: https://adventofcode.com/2023/day/4
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
$points = 0;

// lets start looking with lines
foreach ($lines as $lineIndex => $line) {
    // split the line by : then by |
    $lineParts = explode(':', $line);
    $card = $lineParts[0];
    $game = explode('|', $lineParts[1]);

    echo "------------------------------------------------------------\n";
    echo "Card: $card\n";

    // split the game by space
    $cleanWinningNumbers = str_replace('  ', ' ', trim($game[0]));
    echo "Clean Winning Numbers: $cleanWinningNumbers\n";
    $winningNumbers = explode(' ', $cleanWinningNumbers);
    echo "Winning Numbers: " . implode(', ', $winningNumbers) . "\n";

    $numbers = explode(' ', str_replace('  ', ' ', trim($game[1])));
    echo "Numbers: " . implode(', ', $numbers) . "\n";

    // check if the card is a winner
    $isWinner = isWinner($winningNumbers, $numbers);
    echo "Is Winner: " . (($isWinner !== false) ? $isWinner : 'No') . "\n";

    if ($isWinner !== false) {
        // add 1 point to the sum
        $points += $isWinner;
    }
    echo "Points: $points\n";
}

function isWinner($winningNumbers, $numbers)
{
    $matches = 0;
    // check if the numbers are in the winning numbers
    foreach ($numbers as $number) {
        if (in_array($number, $winningNumbers)) {
            $matches++;
        }
    }

    echo "Matches: $matches\n";

    // if we have 0 matches, return false
    if ($matches === 0) {
        return false;
    }

    // if we have 1 match, return 1 point, otherwise return 1 + doubling for each remaining match
    $points = 1;
    if ($matches > 1) {
        $matches--;
        for ($i = 0; $i < $matches; $i++) {
            $points *= 2;
        }
    }

    return $points;
}
