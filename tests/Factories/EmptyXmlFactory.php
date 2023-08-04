<?php

namespace App\Tests\Factories;

use App\Tests\Factories\BaseTestFileFactory;

class EmptyXmlFactory implements BaseTestFileFactory
{
    public static function generate()
    {
        $file = __DIR__ . '/../fixtures/empty_file_test_case.xml';
        file_put_contents($file, '');

        echo "Empty file generated successfully!";
    }
}
