<?php

namespace App\Services\XmlReader;

use App\Contracts\XmlReaderInterface;
use Psr\Log\LoggerInterface;

class FtpXmlReader implements XmlReaderInterface
{
    public function readXml(string $xmlFilePath, LoggerInterface $logger): ?string
    {
        $ftpServer = $_ENV['FTP_SERVER'] ?? getenv('FTP_SERVER');
        $ftpUsername = $_ENV['FTP_USERNAME'] ?? getenv('FTP_USERNAME');
        $ftpPassword = $_ENV['FTP_PASSWORD'] ?? getenv('FTP_PASSWORD');

        // Append the filename to the FTP server URL to get the full FTP path
        $ftpPath = $xmlFilePath;

        // Create a temporary file to store the downloaded XML content
        $tempFile = tempnam(sys_get_temp_dir(), 'xml_');

        // Connect to the FTP server
        $ftpConnection = ftp_connect(parse_url($ftpServer, PHP_URL_HOST));
        if (!$ftpConnection) {
            $logger->error('Failed to connect to the FTP server.');
            return null;
        }

        // Log in to the FTP server
        $ftpLogin = ftp_login($ftpConnection, $ftpUsername, $ftpPassword);
        if (!$ftpLogin) {
            $logger->error('Failed to login to the FTP server.');
            ftp_close($ftpConnection);
            return null;
        }

        // Enable passive mode
        ftp_pasv($ftpConnection, true);

        echo 'FTP Path: ' . $ftpPath . PHP_EOL;
        if (ftp_get($ftpConnection, $tempFile, $ftpPath, FTP_BINARY)) {
            echo 'Downloaded the remote XML file: ' . $xmlFilePath . PHP_EOL;
        } else {
            echo 'Failed to download the remote XML file: ' . $xmlFilePath . PHP_EOL;
            ftp_close($ftpConnection);
            return null;
        }

        // Close the FTP connection
        ftp_close($ftpConnection);

        // Read the downloaded XML content from the temporary file
        $xmlString = file_get_contents($tempFile);

        // Delete the temporary file
        unlink($tempFile);

        return $xmlString;
    }
}
