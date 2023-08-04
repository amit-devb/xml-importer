<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constants.php';

use App\Runners\ImportXmlRunner;

$sourceType = $argv[1];
$xmlFilePath = $argv[2];
$storageType = $argv[3];

$commandVariables = array(
    'sourceType' => 'Source type',
    'xmlFilePath' => 'XML file path',
    'storageType' => 'Storage type'
);

// Check if all the arguments are present
foreach ($commandVariables as $variable => $displayName) {
    if (empty($$variable)) {
        echo "Error: $displayName is empty.\n";
        exit(1);
    }
}

// Check if the source type is valid
if (!array_key_exists($sourceType, XML_FILE_TYPE)) {
    echo 'Invalid source type. Supported source types: ' . implode(', ', array_keys(XML_FILE_TYPE)) . PHP_EOL;
    exit(1);
}

// Check if the storage type is valid
if (!array_key_exists($storageType, XML_STORAGE_TYPE)) {
    echo 'Invalid storage type. Supported storage types: ' . implode(', ', array_keys(XML_STORAGE_TYPE)) . PHP_EOL;
    exit(1);
}

// Instantiate Import Xml Runner
$runner = new ImportXmlRunner($argv);

// Run the Import XML Runner
$runner->run();
