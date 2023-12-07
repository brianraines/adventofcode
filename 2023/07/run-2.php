<?php

// https://adventofcode.com/2023/day/6
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

/*
32T3K 765
T55J5 684
KK677 28
KTJJT 220
QQQJA 483
*/

// build an array of each race
$hands = getClassifiedHands($lines);
//echo "Hands: " . print_r($hands, true) . "\n";

// rank each type of hand
$rankedHands = rankHands($hands);
//echo "Hands: " . print_r($rankedHands['two-pair'], true) . "\n";

// let's see what we got:
echo "Five of a kind: " . count($rankedHands['five-of-a-kind']) . "\n";
echo "Four of a kind: " . count($rankedHands['four-of-a-kind']) . "\n";
echo "Full house: " . count($rankedHands['full-house']) . "\n";
echo "Three of a kind: " . count($rankedHands['three-of-a-kind']) . "\n";
echo "Two pair: " . count($rankedHands['two-pair']) . "\n";
echo "One pair: " . count($rankedHands['one-pair']) . "\n";
echo "High card: " . count($rankedHands['high-card']) . "\n";

// okay now cross rank all hands
$allRankedHands = [];

// first add the five of a kind hands
foreach ($rankedHands['five-of-a-kind'] as $hand) {
    $allRankedHands[] = $hand;
}

// get the next index to use
$nextIndex = count($allRankedHands);

// next add the four of a kind hands
foreach ($rankedHands['four-of-a-kind'] as $hand) {
    $allRankedHands[$nextIndex] = $hand;
    $nextIndex++;
}

// next add the full house hands
foreach ($rankedHands['full-house'] as $hand) {
    $allRankedHands[$nextIndex] = $hand;
    $nextIndex++;
}

// next add the three of a kind hands
foreach ($rankedHands['three-of-a-kind'] as $hand) {
    $allRankedHands[$nextIndex] = $hand;
    $nextIndex++;
}

// next add the two pair hands
foreach ($rankedHands['two-pair'] as $hand) {
    $allRankedHands[$nextIndex] = $hand;
    $nextIndex++;
}

// next add the one pair hands
foreach ($rankedHands['one-pair'] as $hand) {
    $allRankedHands[$nextIndex] = $hand;
    $nextIndex++;
}

// next add the high card hands
foreach ($rankedHands['high-card'] as $hand) {
    $allRankedHands[$nextIndex] = $hand;
    $nextIndex++;
}


// reverse the array order
$allRankedHands = array_reverse($allRankedHands);

echo "All ranked hands: " . print_r($allRankedHands, true) . "\n";
echo "All ranked hands: " . count($allRankedHands) . "\n";

// now let's calculate the winnings
$winnings = 0;

// set a multiplier
$multiplier = 1;
foreach ($allRankedHands as $hand) {
    $winnings += ($hand['bid'] * $multiplier);
    $multiplier++;
}


echo "Winnings: " . $winnings . "\n";

function getClassifiedHands(array $lines = []) : array
{
    $types =[
        'five-of-a-kind' => [],
        'four-of-a-kind' => [],
        'full-house' => [],
        'three-of-a-kind' => [],
        'two-pair' => [],
        'one-pair' => [],
        'high-card' => [],
    ];


    foreach ($lines as $line) {
        list($hand, $bid) = explode(' ', $line);
        $type = getHandType($hand);
        $types[$type][] = [
            'hand' => $hand,
            'bid' => $bid,
        ];
    }

    return $types;
}

function getHandType(string $hand) : string
{
    $cards = str_split($hand);

    if (isFiveOfAKind($cards)) {
        return 'five-of-a-kind';
    } elseif (isFourOfAKind($cards)) {
        return 'four-of-a-kind';
    } elseif (isFullHouse($cards)) {
        return 'full-house';
    } elseif (isThreeOfAKind($cards)) {
        return 'three-of-a-kind';
    } elseif (isTwoPair($cards)) {
        return 'two-pair';
    } elseif (isOnePair($cards)) {
        return 'one-pair';
    } else {
        return 'high-card';
    }
}

function isFiveOfAKind(array $cards = []) : bool
{
    // where all five cards have the same label: AAAAA
    if ($cards[0] !== 'J') {
        $first = $cards[0];
    } else if ($cards[1] !== 'J') {
        $first = $cards[1];
    } else if ($cards[2] !== 'J') {
        $first = $cards[2];
    } else if ($cards[3] !== 'J') {
        $first = $cards[3];
    } else {
        $first = $cards[4];
    }

    foreach ($cards as $card) {
        if ($card !== $first && $card !== 'J') {
            return false;
        }
    }
    return true;
}

function isFourOfAKind(array $cards = []) : bool
{
    // where four cards have the same label and one card has a different label: AA8AA
    $isFourOfAKind = false;
    foreach ($cards as $card) {
        if (getMatchCount($card, $cards) === 4) {
            $isFourOfAKind = true;
        }
    }

    return $isFourOfAKind;
}

function isFullHouse(array $cards = []) : bool
{
    // if there is a three of a kind, then check for a one pair
    if (isThreeOfAKind($cards)) {
        // remove the three of a kind cards from the array and check for a one pair
        $threeOfAKindCard = false;
        foreach ($cards as $card) {
            if ($threeOfAKindCard === false && $card != 'J' && getMatchCount($card, $cards) === 3) {
                $threeOfAKindCard = $card;
            }
        }

        // remove the three of a kind cards from the cards array
        $firstCardIndex = array_search($threeOfAKindCard, $cards);
        unset($cards[$firstCardIndex]);

        $neededJokers = 0;
        $secondCardIndex = array_search($threeOfAKindCard, $cards);
        if ($secondCardIndex === false) {
            $neededJokers++;
        } else {
            unset($cards[$secondCardIndex]);
        }

        $thirdCardIndex = array_search($threeOfAKindCard, $cards);
        if ($thirdCardIndex === false) {
            $neededJokers++;
        } else {
            unset($cards[$thirdCardIndex]);
        }

        //echo "Needed Jokers: " . $neededJokers . "\n";

        if ($neededJokers > 0) {
            // then we need to pull some jokers
            for ($i = 0; $i < $neededJokers; $i++) {
                // find the first joker
                $jokerIndex = array_search('J', $cards);
                // remove the joker from the cards array
                unset($cards[$jokerIndex]);
            }
        }

        //echo "Three of a kind card: " . $threeOfAKindCard . "\n";
        //echo "Remaining Cards: " . print_r($cards, true) . "\n";

        return isOnePair($cards);
    }

    return false;
}

function isThreeOfAKind(array $cards = []) : bool
{
    // where three cards have the same label, and the remaining two cards are each different from any other card in the hand: TTT98
    $isThreeOfAKind = false;
    foreach ($cards as $card) {
        if (getMatchCount($card, $cards) === 3) {
            $isThreeOfAKind = true;
        }
    }

    return $isThreeOfAKind;
}

function isTwoPair(array $cards = []) : bool
{
    echo "Cards: " . implode('', $cards) . "\n";
    // 3J94T
    // where two cards share one label, two other cards share a second label, and the remaining card has a third label: 23432

    $firstPair = false;
    $secondPair = false;
    foreach ($cards as $card) {
        if ($firstPair === false && $card != 'J' && getMatchCount($card, $cards) === 2) {
            $firstPair = $card;
        }
    }

    if ($firstPair !== false) {
        echo "First pair card: " . $firstPair . "\n";

        // remove the first pair cards from the cards array
        $firstIndex = array_search($firstPair, $cards);
        unset($cards[$firstIndex]);

        if ($firstPair === 'J') {
            // if the first pair card is a joker, and the next highest card
            $highestCard = false;
            foreach ($cards as $card) {
                if (($highestCard === false && $card !== 'J') || convertFaceCardToNumber($highestCard) < convertFaceCardToNumber($card)) {
                    $highestCard = $card;
                }
            }

            echo "Next Highest card: " . $highestCard . "\n";

            if ($highestCard !== false) {
                $secondIndex = array_search($highestCard, $cards);
                unset($cards[$secondIndex]);
            }
        } else {
            // otherwise, remove the first pair cards from the cards array
            $secondIndex = array_search($firstPair, $cards);
            if ($secondIndex === false) {
                $secondIndex = array_search('J', $cards);
            }
            unset($cards[$secondIndex]);
        }

        echo "Remaining Cards: " . print_r($cards, true) . "\n";

        // look for a second pair
        return isOnePair($cards);
    }

    return false;
}

function isOnePair(array $cards = []) : bool
{
    // where two cards share one label, and the other three cards have a different label from the pair and each other: A23A4
    $isOnePair = false;
    foreach ($cards as $card) {
        if (getMatchCount($card, $cards) === 2) {
            $isOnePair = true;
        }
    }

    return $isOnePair;
}

function getMatchCount($match, array $cards = []) : int
{
    $count = 0;
    foreach ($cards as $card) {
        if ($card === $match || $card === 'J') {
            $count++;
        }
    }

    return $count;
}

function rankHands(array $hands = []) : array
{
    $rankedHands = [];
    foreach ($hands as $type => $handsOfType) {
        $rankedHands[$type] = rankHandsOfType($handsOfType);
    }

    return $rankedHands;
}

function rankHandsOfType(array $hands = []) : array
{
    /*
    Start by comparing the first card in each hand. If these cards are different,
    the hand with the stronger first card is considered stronger. If the first
    card in each hand have the same label, however, then move on to considering
    the second card in each hand. If they differ, the hand with the higher second
    card wins; otherwise, continue with the third card in each hand, then the fourth,
    then the fifth.
    */
    usort(
        $hands, function ($a, $b) {
            $aCards = str_split($a['hand']);
            $bCards = str_split($b['hand']);

            for ($i = 0; $i < 5; $i++) {
                $aCard = convertFaceCardToNumber($aCards[$i]);
                $bCard = convertFaceCardToNumber($bCards[$i]);

                if ($aCard !== $bCard) {
                    return ($aCard > $bCard) ? -1 : 1;
                }
            }

            return 0;
        }
    );

    return $hands;
}

function convertFaceCardToNumber(string $card) : int
{
    switch ($card) {
    case 'T':
        return 10;
    case 'J':
        return 1;
    case 'Q':
        return 11;
    case 'K':
        return 12;
    case 'A':
        return 13;
    default:
        return (int) $card;
    }
}
