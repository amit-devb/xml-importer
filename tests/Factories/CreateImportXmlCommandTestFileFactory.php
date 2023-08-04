<?php

namespace App\Tests\Factories;

use App\Tests\Factories\BaseTestFileFactory;

class CreateImportXmlCommandTestFileFactory implements BaseTestFileFactory
{
    public static function generate()
    {
        // Start the XML output
        $xmlOutput = '<xml><item><id>1</id><name>Product 1</name></item></xml>';

        // Save the XML to a file
        $file = __DIR__ . '/../fixtures/valid_xml_file.xml';
        file_put_contents($file, $xmlOutput);

        echo "File generated successfully!";
    }
}
