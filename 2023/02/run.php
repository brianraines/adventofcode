<?php

// https://adventofcode.com/2023/day/2
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

/*
Game 1: 3 blue, 4 red; 1 red, 2 green, 6 blue; 2 green
Game 2: 1 blue, 2 green; 3 green, 4 blue, 1 red; 1 green, 1 blue
Game 3: 8 green, 6 blue, 20 red; 5 blue, 4 red, 13 green; 5 green, 1 red
Game 4: 1 green, 3 red, 6 blue; 3 green, 6 red; 3 green, 15 blue, 14 red
Game 5: 6 red, 1 blue, 3 green; 2 blue, 1 red, 2 green
*/

$score = 0;
$power = 0;

foreach ($lines as $game) {
    echo "------------------------------------------------------------\n";
    echo "Processing: $game\n";

    // split by : then by ; then by ,
    $gameParts = explode(":", $game);
    $gameName = $gameParts[0];
    $gameNameParts = explode(" ", $gameName);
    $gameNumber = $gameNameParts[1];
    echo "Game Number: $gameNumber\n";

    // split the game data into two subsets
    $subsets = explode(";", $gameParts[1]);
    echo "Subsets: " . print_r($subsets, true) . "\n";

    $gameMaxDice = [];
    $gamePower = false;

    // start a counter for the number of dice
    $possibleSubsets = 0;
    foreach ($subsets as $subset) {
        echo "------------------------------\n";

        // split the subset into dice by color
        $subsetData = explode(",", $subset);

        $subsetDice = [];

        // get the number of dice
        foreach ($subsetData as $data) {
            $gameDataPart1 = trim($data);
            $diceParts = explode(" ", $gameDataPart1);
            $diceNumber = $diceParts[0] * 1;
            $diceColor = strtolower($diceParts[1]);

            if (isset($subsetDice[$diceColor])) {
                $subsetDice[$diceColor] += $diceNumber;
            } else {
                $subsetDice[$diceColor] = $diceNumber;
            }

            if (isset($gameMaxDice[$diceColor])) {
                $gameMaxDice[$diceColor] = max($gameMaxDice[$diceColor], $diceNumber);
            } else {
                $gameMaxDice[$diceColor] = $diceNumber;
            }

        }

        // determine if the subset is possible
        if (isSubsetPossible($subsetDice)) {
            $possibleSubsets++;
        }

        // determine if game is possible
        if ($possibleSubsets == count($subsets)) {
            // add to total score
            $score += $gameNumber;
        }
    }

    // get the power of the GAME
    echo "Game Max Dice: " . print_r($gameMaxDice, true) . "\n";
    foreach ($gameMaxDice as $maxColor => $maxNumber) {
        $gamePower = ($gamePower === false) ? $maxNumber : $gamePower * $maxNumber;
    }
    echo "Game Power: $gamePower\n";
    $power += $gamePower;

    echo "Total Score: $score\n";
    echo "Total Power: $power\n";
}

/**
 * Only 12 red cubes, 13 green cubes, and 14 blue cubes
 *
 * @param array $gameDice Array of dice
 *
 * @return array
 */
function isSubsetPossible($dice = []) : bool
{

    echo "Subset Dice: " . print_r($dice, true) . "\n";

    $availableDice = [
        "red"   => 12,
        "blue"  => 14,
        "green" => 13
    ];

    $isPossible = true;

    foreach ($dice as $color => $number) {
        if ($number > $availableDice[$color]) {
            $isPossible = false;
            break;
        }
    }

    echo "Is Subset Possible: " . ($isPossible ? "Yes" : "No") . "\n";

    return $isPossible;
}