<?php
ini_set('memory_limit', '5G');

// https://adventofcode.com/2023/day/12
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// set a memoize cache
$cache = [];

// start with 0 possible arrangements
$allPossibleArrangements = 0;

// for each line get the possible arrangements
foreach ($lines as $lineIndex => $line) {
    echo "========================================\n";
    echo "Processing Line: " . $lineIndex + 1 . "\n";

    $possibleArrangements = getArrangementCount($line);
    echo "Possible arrangements: $possibleArrangements\n";

    $allPossibleArrangements += $possibleArrangements;
}

echo "All possible arrangements: $allPossibleArrangements\n";
// answer: 11607695322318


function getArrangementCount($line)
{
    // split the line by " "
    list($records, $groups) = explode(" ", $line);

    // expand the $groups by 5x separated by ,
    $expandedGroups = [];
    $expandedGroups[] = $groups;
    $expandedGroups[] = $groups;
    $expandedGroups[] = $groups;
    $expandedGroups[] = $groups;
    $expandedGroups[] = $groups;
    $groups = implode(',', $expandedGroups);
    echo "Expanded Groups: $groups\n";

    // expand the $records by 5x separated by ?
    $expandedRecords = [];
    $expandedRecords[] = $records;
    $expandedRecords[] = $records;
    $expandedRecords[] = $records;
    $expandedRecords[] = $records;
    $expandedRecords[] = $records;
    $records = implode('?', $expandedRecords);
    echo "Expanded Records: $records\n";

    // get the match count
    return getMatchCount($records, explode(',', $groups));
}

function getMatchCount($records, $groups, $result = 0)
{
    // use the global cache
    global $cache;

    // create a cache key
    $cacheKey = $records . implode($groups);

    // if the cache key exists, return the value
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }

    // if there are no more groups, return true if there are no # in the records
    if (count($groups) == 0) {
        // set the cache and return
        $cache[$cacheKey] = !(strpos($records, '#') !== false) ? 1 : 0;
        return $cache[$cacheKey];
    }

    // get the first group
    $currentGroup = array_shift($groups);

    // set a upper range limit
    $upperLimit = strlen($records) - array_sum($groups) - count($groups) - $currentGroup + 1;

    // loop through the upper range limit
    for ($i = 0; $i <= $upperLimit; $i++) {

        // if there is a # in the records, break
        if (strpos(substr($records, 0, $i), "#") !== false) {
            break;
        }

        // set the next position
        $next = $i + $currentGroup;
        if ($next <= strlen($records)
            && strpos(substr($records, $i, $next - $i), ".") === false
            && substr($records, $next, 1) != "#"
        ) {
            $result += getMatchCount(substr($records, $next + 1), $groups);
        }
    }

    // set the cache and return
    $cache[$cacheKey] = $result;
    return $result;
}