<?php

namespace App\Runners;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Console\Application;
use App\Command\ImportXmlCommand;

/**
 * * Desc - Import XML runner helps in running appropriate XML utility command 
 */
class ImportXmlRunner
{
    private $argv;

    /**
     * * Desc - ImportXmlRunner initializer method
     */
    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    /**
     * Initialize the logger
     *
     */
    private function setupLogger(): Logger
    {
        $logFilePath = __DIR__ . '/../../logs/xml_import.log';
        $logger = new Logger('xml_importer');
        $logger->pushHandler(new RotatingFileHandler($logFilePath, 7, Logger::INFO));
        return $logger;
    }

    /**
     * Load environment variables from .env file
     */
    private function loadEnvVariables(): void
    {
        $dotenv = new \Symfony\Component\Dotenv\Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');
    }

    /**
     * * Desc - This is the main method to run the XML Runner
     * @argv - Arguments received from the command
     */
    public function run(): void
    {
        $this->loadEnvVariables();
        $logger = $this->setupLogger();

        $application = new Application();

        // Get the source type and XML file path from the command-line arguments
        $sourceType = $this->argv[1];
        $storageType = $this->argv[3];

        // Get the corresponding XML reader class based on the source type
        $xmlReaderClass = XML_FILE_TYPE[$sourceType];
        $xmlReader = new $xmlReaderClass();

        // Get the corresponding XML storage class based on the storage type
        $storageInitValues = XML_STORAGE_INIT_VALUSE[$storageType];
        $xmlStorageClass = XML_STORAGE_TYPE[$storageType];
        $xmlStorage = new $xmlStorageClass($storageInitValues);

        $importCommand = new ImportXmlCommand($xmlStorage, $logger, $xmlReader);

        $application->add($importCommand);
        $application->setDefaultCommand($importCommand->getName(), true);
        $application->run();
    }
}
