<?php

namespace App\Tests\Factories;

use App\Tests\Factories\EmptyXmlFactory;
use App\Tests\Factories\LargeXmlFactory;
use App\Tests\Factories\MalformedXmlFactory;

class CreateLocalXmlReaderTestFileFactory
{
    public static function generateAllTestFiles()
    {
        LargeXmlFactory::generate();
        MalformedXmlFactory::generate();
        EmptyXmlFactory::generate();
    }
}
