<?php

namespace App\Tests\Command;

use App\Command\ImportXmlCommand;
use App\Contracts\StorageAdapterInterface;
use App\Contracts\XmlReaderInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Tests\Util\FileHelper;
use App\Tests\Factories\CreateImportXmlCommandTestFileFactory;

class ImportXmlCommandTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * Set up the test environment before each test method.
     */
    protected function setUp(): void
    {
        parent::setUp();

        CreateImportXmlCommandTestFileFactory::generate();

        // Create an instance of the command
        $storageAdapter = $this->createMock(StorageAdapterInterface::class);
        $xmlReader = $this->createMock(XmlReaderInterface::class);
        $logger = new NullLogger();

        $this->application = new Application();
        $this->application->add(new ImportXmlCommand($storageAdapter, $logger, $xmlReader));

        $this->commandTester = new CommandTester($this->application->find('import:xml'));
    }

    /**
     * Test the command execution with a valid XML file.
     */
    public function testExecuteWithValidFile()
    {
        // Provide the command with valid arguments
        $xmlFilePath = __DIR__ . '/../fixtures/valid_test_case.xml';
        $arguments = [
            'command' => $this->application->find('import:xml')->getName(),
            'source-type' => 'local',
            'xml-file' => $xmlFilePath,
            'storage-type' => 'csv',
        ];

        // Create a mock for XmlReaderInterface
        $xmlReaderMock = $this->createMock(XmlReaderInterface::class);
        $xmlReaderMock->method('readXml')
            ->with($xmlFilePath, $this->isInstanceOf(NullLogger::class))
            ->willReturn('<xml><item><id>1</id><name>Product 1</name></item></xml>');

        // Create a mock for StorageAdapterInterface
        $storageAdapterMock = $this->createMock(StorageAdapterInterface::class);
        $storageAdapterMock->method('storeData')
            ->with([['id', 'name'], [1, 'Product 1']]);

        // Replace the dependencies in the command with the mock objects
        $command = $this->application->find('import:xml');
        $command->__construct($storageAdapterMock, new NullLogger(), $xmlReaderMock);

        // Execute the command
        $this->commandTester->execute($arguments);

        // Assertions
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('XML data processing started', $output);
        $this->assertStringContainsString('XML data processing finished', $output);
        $this->assertStringContainsString('Data imported from local and stored to csv', $output);
    }


    /**
     * Clean up the test environment after all test methods in the class have been executed.
     */
    public static function tearDownAfterClass(): void
    {
        // Delete the generated files after all tests in the class have been executed successfully
        FileHelper::deleteGeneratedFiles();
    }
}
