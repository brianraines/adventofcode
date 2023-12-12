<?php

// https://adventofcode.com/2023/day/12
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

$allPossibleArrangements = 0;
// for each line get the possible arrangements
foreach ($lines as $line) {
    $possibleArrangements = getArrangementCount($line);
    //echo "Possible arrangements: $possibleArrangements\n";

    $allPossibleArrangements += $possibleArrangements;
}

echo "All possible arrangements: $allPossibleArrangements\n";
// answer: 7236

function getArrangementCount($line)
{
    /*
        Line 1 as an example
        .???##?????????#?.?? 1,12

        Two possible arrangements:
        .1..############.... 1,12
        ..1.############.... 1,12
    */

    //echo "Line: $line\n";

    // split the line by " "
    list($records, $groups) = explode(" ", $line);
    //echo "Groups: $groups\n";
    //echo "Records: $records\n";

    $masks = generateMasks([$records]);
    //echo "Masks: " . print_r($masks, true) . "\n";

    // foreach mask get the arrangement and see if it matches the groups
    $arrangements = [];
    foreach ($masks as $mask) {
        $isPossible = isPossibleArrangement($mask, $groups);
        if ($isPossible) {
            $arrangements[] = $mask;
        }
    }

    //echo "Arrangements: " . print_r($arrangements, true) . "\n";
    return count($arrangements);

}

function isPossibleArrangement($mask, $groups)
{
    // get the number of adjoining # characters separated by .
    $arrangement = [];

    $chars = str_split($mask);
    $currentGroup = 0;
    foreach ($chars as $char) {

        if ($char == '#') {
            $currentGroup++;
        } else if ($char == '.') {
            if ($currentGroup > 0) {
                $arrangement[] = $currentGroup;
                $currentGroup = 0;
            }
        }
    }

    if ($currentGroup > 0) {
        $arrangement[] = $currentGroup;
        $currentGroup = 0;
    }


    // if the arrangement matches the groups then return true
    $potentialGroups = implode(',', $arrangement);
    if ($potentialGroups == $groups) {
        //echo "Arrangement of $mask: " . print_r($arrangement, true) . "\n";
        //echo "Matched $potentialGroups with $groups\n";
        return true;
    }
    return false;
}

function generateMasks($records)
{
    $masks = [];
    $hasQuestionMark = false;
    foreach ($records as $record) {
        if (strpos($record, '?') !== false) {
            $hasQuestionMark = true;
            //echo "Record: $record\n";
            // replace first ? only with .
            $masks[] = preg_replace('/\?/', '.', $record, 1);
            // replace first ? only with #
            $masks[] = preg_replace('/\?/', '#', $record, 1);
        }
    }
    if ($hasQuestionMark && count($masks) > 0) {
        return generateMasks($masks);
    }

    return $records;
}