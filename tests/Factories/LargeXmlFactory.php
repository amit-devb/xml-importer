<?php

namespace App\Tests\Factories;

use App\Tests\Factories\BaseTestFileFactory;

class LargeXmlFactory implements BaseTestFileFactory
{
    public static function generate()
    {
        // Start the XML output
        $xmlOutput = '<?xml version="1.0" encoding="UTF-8"?>
    <catalog>';
        $numberOfItems = 10000; // Change this number to generate more or fewer items
        // Generate <item> elements
        for ($i = 1; $i <= $numberOfItems; $i++) {
            $xmlOutput .= '
    <item>
        <entity_id>' . $i . '</entity_id>
        <CategoryName><![CDATA[Category ' . $i . ']]></CategoryName>
        <sku>100' . $i . '</sku>
        <name><![CDATA[Product ' . $i . ']]></name>
        <description><![CDATA[Description for Product ' . $i . ']]></description>
        <price>' . (10.99 + $i) . '</price>
        <link>http://example.com/product' . $i . '</link>
        <image>http://example.com/images/product' . $i . '.jpg</image>
        <Brand><![CDATA[Brand ' . chr(65 + ($i % 26)) . ']]></Brand>
        <Rating>' . (3.0 + ($i % 5) * 0.1) . '</Rating>
        <CaffeineType>' . ($i % 2 === 0 ? 'Caffeinated' : 'Decaffeinated') . '</CaffeineType>
        <Count>' . (24 + $i) . '</Count>
        <Flavored>' . ($i % 2 === 0 ? 'Yes' : 'No') . '</Flavored>
        <Seasonal>' . ($i % 2 === 0 ? 'Yes' : 'No') . '</Seasonal>
        <Instock>' . ($i % 2 === 0 ? 'Yes' : 'No') . '</Instock>
        <Facebook>' . ($i * 10) . '</Facebook>
        <IsKCup>' . ($i % 2) . '</IsKCup>
    </item>';
        }

        // End the XML output
        $xmlOutput .= '</catalog>';

        // Save the XML to a file
        $file = __DIR__ . '/../fixtures/huge_catalog_test_case.xml';
        file_put_contents($file, $xmlOutput);

        echo "File generated successfully!";
    }
}
