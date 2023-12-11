<?php

// https://adventofcode.com/2023/day/11
$filename = 'input.txt';
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// map the universe and look for all rows and columns with no galaxies
$universe = [];
$galaxies = [];
$emptyRows = [];
$emptyColumns = [];

mapTheUniverse($lines);

echo "Galaxies: " . count($galaxies) . "\n";
echo "Empty Rows: " . print_r($emptyRows, true) . "\n";
echo "Empty Columns: " . print_r($emptyColumns, true) . "\n";

// find the shortest distance between all galaxies
$routes = [];
routeAllTheGalaxies();
echo "Routes: " . count($routes) . "\n";
$totalDistance = 0;
foreach ($routes as $route) {
    $totalDistance += $route['distance'];
}

echo "Total Distance: " . $totalDistance . "\n";
//9545480


function routeAllTheGalaxies()
{
    global $universe, $galaxies, $routes;

    foreach ($galaxies as $galaxyA => $coordinatesA) {

        $rowA = $coordinatesA[0];
        $columnA = $coordinatesA[1];

        foreach ($galaxies as $galaxyB => $coordinatesB) {
            if ($galaxyA === $galaxyB) {
                continue;
            }

            $routeKey = min($galaxyA, $galaxyB) . "-" . max($galaxyA, $galaxyB);
            if (isset($routes[$routeKey]) !== true) {
                $routes[$routeKey] = [
                    $galaxyA => $coordinatesA,
                    $galaxyB => $coordinatesB
                ];
            }

            $rowB = $coordinatesB[0];
            $columnB = $coordinatesB[1];

            $distance = getShortestDistance($coordinatesA, $coordinatesB);

            if (isset($routes[$routeKey]['distance']) === false || $distance < $routes[$routeKey]['distance']) {
                $routes[$routeKey]['distance'] = $distance;
            }
        }
    }
}

function getShortestDistance($coordinatesA, $coordinatesB)
{
    global $emptyRows, $emptyColumns;
    $expansion = 1;

    $rowA = $coordinatesA[0];
    $columnA = $coordinatesA[1];

    $rowB = $coordinatesB[0];
    $columnB = $coordinatesB[1];

    $rowDistance = max($rowA, $rowB) - min($rowA, $rowB);

    // if any of the empty rows are between the two galaxies, add 1000000 to the distance for each empty row
    foreach ($emptyRows as $emptyRow) {
        if ($rowA != $rowB && $emptyRow >= min($rowA, $rowB) && $emptyRow <= max($rowA, $rowB)) {
            $rowDistance += $expansion;
        }
    }

    $columnDistance = max($columnA, $columnB) - min($columnA, $columnB);

     // if any of the empty columns are between the two galaxies, add 1000000 to the distance for each empty column
    foreach ($emptyColumns as $emptyColumn) {
        if ($columnA != $columnB && $emptyColumn >= min($columnA, $columnB) && $emptyColumn <= max($columnA, $columnB)) {
            $columnDistance += $expansion;
        }
    }

    return $rowDistance + $columnDistance;
}

function mapTheUniverse($lines)
{
    global $universe, $galaxies, $emptyRows, $emptyColumns;

    $galaxyCount = 1;

    $emptyColumns = [];
    $columnCount = 0;

    foreach ($lines as $lineIndex => $line) {
        $row = $lineIndex;
        $columns = str_split($line);
        $columnCount = count($columns);

        $emptyRow = true;

        for ($column = 0; $column < count($columns); $column++) {
            $coordinate = $columns[$column];

            if (!isset($emptyColumns[$column])) {
                $emptyColumns[$column] = true;
            }


            if ($coordinate === "#") {
                $galaxies[$galaxyCount] = [$row, $column];
                $galaxyCount++;
                $emptyRow = false;

                $emptyColumns[$column] = false;
            }

            $universe[$row][$column] = $coordinate;
        }

        if ($emptyRow) {
            $emptyRows[$row] = true;
        }
    }

    foreach ($emptyColumns as $column => $isEmpty) {
        if ($isEmpty === false) {
            unset($emptyColumns[$column]);
        }
    }

    $emptyRows = array_keys($emptyRows);
    $emptyColumns = array_keys($emptyColumns);

}
