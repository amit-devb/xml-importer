<?php

namespace App\Contracts;

interface StorageAdapterInterface
{
    public function storeData(array $data): void;
}
