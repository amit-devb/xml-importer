<?php

const XML_FILE_TYPE = [
    "local" => "App\Services\XmlReader\LocalXmlReader",
    "ftp" => "App\Services\XmlReader\FtpXmlReader"
];

/**
 * We can add any other values for specific storage type like SQLite, ElasticSearch
 */
const XML_STORAGE_TYPE = [
    "csv" => "App\Adapter\CsvStorageAdapter"
];

const XML_STORAGE_INIT_VALUSE = [
    "csv" => "data/xml_csv_output"
];
