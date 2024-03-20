<?php

// Read a CSV file and store it in an associative array
function readCsv($filename) {

    // Read the file content into an array
    $fileContent = file($filename);

    // Check if the file is empty
    if (!$fileContent || count($fileContent) === 0) {
        exit("The CSV file $filename is empty.\n");
    }

    // Map rows and loop through them 
    $rows   = array_map('str_getcsv', $fileContent);

    // Remove the first one row that contains headers
    $header = array_shift($rows);

    $data = []; // an associative array to store the CSV data including headers

    // Combine the headers with each following row
    foreach($rows as $row) {
        $data[] = array_combine($header, $row);
    }
    return $data;
}


// Filter services by country code
function filterByCountry($services, $countryCode) {
    // filter condition
    $filterCallback = function($service) use ($countryCode) {
        // convert to lowercase for case-insensitive comparison
        return strtolower($service['Country']) == strtolower($countryCode);
    };

    return array_filter($services, $filterCallback);
}


// calculate total number of services in each country
function calculateSummary($services) {
    $summary = [];

    foreach ($services as $service) {     
        // convert the country code to uppercase
        $uppercaseCountryCode = strtoupper($service['Country']);

        if (isset($summary[$uppercaseCountryCode])) {
            $summary[$uppercaseCountryCode]++;
        } else {
            $summary[$uppercaseCountryCode] = 1;
        }
    }
    return $summary;
}

// Display the filtered result
function displayFilteredResult($array) {
    // Fixed width for each column
    $columnWidth = 30;

    // Extract headers (keys)
    $headers = array_keys(reset($array));

    // Print headers
    foreach ($headers as $header) {
        printf("%-{$columnWidth}s", $header);
    }
    echo "\n";

    // Print separator line
    echo str_repeat('-', $columnWidth * count($headers)) . "\n";

    // Print each row of data
    foreach ($array as $row) {
        foreach ($row as $cell) {
            printf("%-{$columnWidth}s", $cell);
        }
        echo "\n";
    }   
}

// Show the total number of services in each country
function displaySummary($array){
    echo "Country\tNumber of Services\n";

    foreach ($array as $country => $count) {
        echo "$country\t$count\n";
    }
}


// Main function
function main() {
    $csvFile = 'services.csv'; // change the path if needed

    // Check if the CSV file exists
    if (!file_exists($csvFile)) {
        exit("The CSV file $csvFile is not found\n");
    }

    $argc = $_SERVER['argc'];
    $argv = $_SERVER['argv'];

    if ($argc < 2) {
        exit("Please provide a country code\n");
    }
    
    $countryCode = $argv[1];
    $services = readCsv($csvFile);

    $filteredServices = filterByCountry($services, $countryCode);
    $servicesSummary = calculateSummary($services);

    if (empty($filteredServices)) {
        echo "No services found for country code $countryCode\n\n";
    }
    else{
        displayFilteredResult($filteredServices);
        echo "\n";
    }

    displaySummary($servicesSummary);
}


main();