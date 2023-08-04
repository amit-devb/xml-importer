<?php

namespace App\Contracts;

use Psr\Log\LoggerInterface;


interface XmlReaderInterface
{
    public function readXml(string $xmlFilePath, LoggerInterface $logger): ?string;
}
