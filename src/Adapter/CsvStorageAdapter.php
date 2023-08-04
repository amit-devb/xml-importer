<?php

namespace App\Adapter;

use App\Contracts\StorageAdapterInterface;

class CsvStorageAdapter implements StorageAdapterInterface
{
    private $csvFilePath;

    public function __construct(string $csvFilePath)
    {
        $date = date('Y-m-d_H-i-s');
        $this->csvFilePath = __DIR__ . "/../../" . $csvFilePath . '_' . "$date.csv";
    }

    public function storeData(array $data): void
    {
        $csvFile = fopen($this->csvFilePath, 'w');
        foreach ($data as $row) {
            fputcsv($csvFile, $row);
        }
        fclose($csvFile);
    }
}
