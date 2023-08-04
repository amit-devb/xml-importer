<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use App\Contracts\StorageAdapterInterface;
use App\Contracts\XmlReaderInterface;

class ImportXmlCommand extends Command
{
    use LockableTrait;

    private $logger;
    private $storageAdapter;
    private $xmlReader;

    public function __construct(
        StorageAdapterInterface $storageAdapter,
        LoggerInterface $logger,
        XmlReaderInterface $xmlReader
    ) {
        parent::__construct();

        $this->storageAdapter = $storageAdapter;
        $this->logger = $logger;
        $this->xmlReader = $xmlReader;
    }

    /**
     * Desc - Method used for command's configurations
     */
    protected function configure(): void
    {
        $this
            ->setName('import:xml')
            ->setDescription('Import data from XML file and store in CSV')
            ->addArgument('source-type', InputArgument::REQUIRED, 'Source Type-LOCAL/API/FTP')
            ->addArgument('xml-file', InputArgument::REQUIRED, 'Path to XML file (local or remote)')
            ->addArgument('storage-type', InputArgument::REQUIRED, 'Storage type (CSV or SQLite)');
    }

    /**
     * Desc - Initialize the command
     */
    protected function initialize(InputInterface $input, OutputInterface $output): int
    {
        /**
         * This is to prevent running the same console command.
         * Multiple times in Symfony using the built-in locking mechanism.
         */
        if (!$this->lock()) {
            $output->writeln('<error>The command is already running in another process.</error>');

            return 1;
        }

        parent::initialize($input, $output);

        return 0;
    }

    /**
     * Desc - Method to execute the command 
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceType = $input->getArgument('source-type');
        $xmlFilePath = $input->getArgument('xml-file');
        $storageType = $input->getArgument('storage-type');

        // Read the XML content using the selected reader
        $xmlString = $this->xmlReader->readXml($xmlFilePath, $this->logger);
        if ($xmlString === null) {
            $output->writeln('<error>Error: XML content not found.</error>');
            $this->logger->error("Error: XML content not found");
            return 1;
        }
        $this->logger->info("Source Type is: $sourceType");
        $xml = simplexml_load_string($xmlString);
        $this->logger->info('Parsed its contents');

        // Validate if the XML is parsed successfully
        if (!$xml) {
            $output->writeln('<error>Error: Failed to parse XML file.</error>');
            $this->logger->error("Error: Failed to parse XML file.");
            return 1; // Error status code
        }

        $outputStyle = new OutputFormatterStyle('green');
        $output->getFormatter()->setStyle('success', $outputStyle);
        $output->writeln('<success>XML data processing started</success>');

        // Initialize the data array with a null header row
        $data = [];
        $headerRow = null;

        // Get the total number of items in the XML for the progress bar
        $totalItems = count($xml->item);

        $progressBar = new ProgressBar($output, $totalItems);
        $progressBar->setFormat(" %current%/%max% [%bar%] %percent:3s%%  %memory:6s%\n");
        $progressBar->start();

        // Process each item in the XML
        foreach ($xml->item as $item) {
            $row = [];

            // If headerRow is not set, extract it from the first item's keys
            if ($headerRow === null) {
                foreach ($item->children() as $child) {
                    $headerRow[] = $child->getName();
                }
                $data[] = $headerRow;
            }

            // Loop through each child element of $item and add its value to the $row array
            foreach ($item->children() as $child) {
                // Convert values to appropriate types (e.g., string, float, int)
                if (is_numeric($child)) {
                    $row[] = $child + 0; // Convert to int or float
                } else {
                    $row[] = (string) $child; // Convert to string
                }
            }

            $data[] = $row;

            // Advance the progress bar unless disabled
            $progressBar->advance();
        }
        // Finish the progress bar if it was not disabled
        $progressBar->finish();
        $output->writeln("\n<success>XML data processing finished</success>");

        $this->logger->info('XML data processing finished');

        $this->logger->info("Data import started from $sourceType to $storageType, for the XML file: $xmlFilePath");
        $this->storageAdapter->storeData($data);
        $this->logger->info("Data imported from $sourceType and stored to $storageType");

        $output->writeln("\n<success>Data imported from $sourceType and stored to $storageType</success>");

        return 0; // Success status code
    }
}
