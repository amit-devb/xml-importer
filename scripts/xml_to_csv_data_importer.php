<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/constants.php';

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Console\Application;
use App\Command\ImportXmlCommand;
use App\Adapter\CsvStorageAdapter;


$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$logFilePath = __DIR__ . '/../logs/xml_import.log';
$logger = new Logger('xml_importer');
$logger->pushHandler(new RotatingFileHandler($logFilePath, 7, Logger::INFO));

$application = new Application();

// Get the source type and XML file path from the command-line arguments
$sourceType = $argv[1];
$xmlFilePath = $argv[2];
$csvFile = $argv[3];

// Check if the source type is valid
if (!array_key_exists($sourceType, XML_FILE_TYPE)) {
    echo 'Invalid source type. Supported source types: ' . implode(', ', array_keys(XML_FILE_TYPE)) . PHP_EOL;
    exit(1);
}

// Create a unique filename for the CSV file with the current date appended
$date = date('Y-m-d');
$csvFilePath = __DIR__ . "/../$csvFile" . '_' . "$date.csv";

// Get the corresponding XML reader class based on the source type
$xmlReaderClass = XML_FILE_TYPE[$sourceType];
$xmlReader = new $xmlReaderClass();

$csvAdapter = new CsvStorageAdapter($csvFilePath);
$importCommand = new ImportXmlCommand($csvAdapter, $logger, $xmlReader, $sourceType); // Pass the XML content to the command

$application->add($importCommand);
$application->setDefaultCommand($importCommand->getName(), true);
$application->run();
