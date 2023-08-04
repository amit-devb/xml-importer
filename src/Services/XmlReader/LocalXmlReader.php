<?php

namespace App\Services\XmlReader;

use App\Contracts\XmlReaderInterface;
use Psr\Log\LoggerInterface;

class LocalXmlReader implements XmlReaderInterface
{
    public function readXml(string $xmlFilePath, LoggerInterface $logger): ?string
    {
        if (!file_exists($xmlFilePath) || !is_readable($xmlFilePath)) {
            $logger->error('XML file "' . $xmlFilePath . '" does not exist or is not readable.');
            return null;
        }

        // Check if the file is empty
        if (filesize($xmlFilePath) === 0) {
            $logger->info('The XML file is empty: ' . $xmlFilePath);
            return null;
        }

        // Create a new XMLReader instance
        $xmlReader = new \XMLReader();

        // Open the XML file for reading
        if (!$xmlReader->open($xmlFilePath)) {
            $logger->error('Failed to open the local XML file: ' . $xmlFilePath);
            return null;
        }

        // Initialize an empty string to store the XML content
        $xmlString = '';

        // Read the XML in chunks and concatenate the results
        while ($xmlReader->read()) {
            if ($xmlReader->nodeType === \XMLReader::ELEMENT && $xmlReader->depth === 0) {
                // Get the XML node as a string and append it to the XML content
                $xmlString .= $xmlReader->readOuterXML();
            }
        }

        // Close the XMLReader instance
        $xmlReader->close();

        // Parse the XML and check for malformed syntax
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        // Check if the XML could be parsed successfully
        if ($xml === false || !empty($errors)) {
            $logger->error('Failed to parse the XML file: ' . $xmlFilePath);
            return null;
        }

        return $xmlString;
    }
}
