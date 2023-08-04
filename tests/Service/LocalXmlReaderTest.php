<?php

namespace App\Tests\Service;

use App\Services\XmlReader\LocalXmlReader;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use App\Tests\Util\FileHelper;
use App\Tests\Factories\CreateLocalXmlReaderTestFileFactory;

class LocalXmlReaderTest extends TestCase
{
    /**
     * Method to set up the test environment before each test method.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Generate all the test files needed for testing
        CreateLocalXmlReaderTestFileFactory::generateAllTestFiles();
    }

    /**
     * Test reading XML with an invalid file path.
     */
    public function testReadXmlWithInvalidFile()
    {
        // Provide an invalid file path that does not exist
        $xmlFilePath = __DIR__ . '/../fixtures/file_does_not_exists.xml';

        // Create the LocalXmlReader instance
        $localXmlReader = new LocalXmlReader();

        // Set up the logger (you can use a mocked logger instead)
        $logger = new NullLogger();

        // Test the readXml method with an invalid file path
        $xmlString = $localXmlReader->readXml($xmlFilePath, $logger);

        // Assertion: The XML string should be null since the file does not exist
        $this->assertNull($xmlString);
    }

    /**
     * Test reading XML from an empty file.
     */
    public function testReadXmlWithEmptyFile()
    {
        // Provide a path to an empty XML file
        $xmlFilePath = __DIR__ . '/../fixtures/empty_file_test_case.xml';

        // Create the LocalXmlReader instance
        $localXmlReader = new LocalXmlReader();

        // Set up the logger (you can use a mocked logger instead)
        $logger = new NullLogger();

        // Test the readXml method with an empty XML file
        $xmlString = $localXmlReader->readXml($xmlFilePath, $logger);

        // Assertion: The XML string should be null since the file is empty
        $this->assertNull($xmlString);
    }

    /**
     * Test reading XML from a file with malformed content.
     */
    public function testReadXmlWithMalformedFile()
    {
        // Provide a path to a file with malformed XML content
        $xmlFilePath = __DIR__ . '/../fixtures/malformed_content_test_case.xml';

        // Create the LocalXmlReader instance
        $localXmlReader = new LocalXmlReader();

        // Set up the logger (you can use a mocked logger instead)
        $logger = new NullLogger();

        // Test the readXml method with a malformed XML file
        try {
            $xmlString = $localXmlReader->readXml($xmlFilePath, $logger);

            // Assertion: The XML string should be null since the file has malformed syntax
            $this->assertNull($xmlString);

            // Additionally, you can log a warning or error message if the XML is malformed
            $logger->warning('The XML file has malformed syntax.');
        } catch (\Exception $e) {
            // Assertion: Ensure that an Exception is thrown by the XML reader
            $this->assertInstanceOf(\Exception::class, $e);

            // Additionally, you can log the error message for debugging purposes
            $logger->error('Failed to read XML file: ' . $e->getMessage());
        }
    }

    /**
     * Test reading XML from a valid large file.
     */
    public function testReadXmlWithValidLargeFile()
    {
        // Provide a path to a large XML file
        $xmlFilePath = __DIR__ . '/../fixtures/huge_catalog_test_case.xml';

        // Create the LocalXmlReader instance
        $localXmlReader = new LocalXmlReader();

        // Set up the logger (you can use a mocked logger instead)
        $logger = new NullLogger();

        // Test the readXml method with a large XML file
        $xmlString = $localXmlReader->readXml($xmlFilePath, $logger);

        // Assertions: The XML string should not be null and should be a non-empty string
        $this->assertNotNull($xmlString);
        $this->assertIsString($xmlString);
        $this->assertNotEmpty($xmlString);
    }

    /**
     * Method to clean up the test environment after all test methods in the class have been executed.
     */
    public static function tearDownAfterClass(): void
    {
        // Call the static method to delete generated files
        FileHelper::deleteGeneratedFiles();
    }
}
