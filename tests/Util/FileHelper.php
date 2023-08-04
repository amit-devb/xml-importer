<?php

namespace App\Tests\Util;

class FileHelper
{
    /**
     * Helper function to delete generated files from the given directory.
     * If no directory is provided, it will use the 'fixtures' directory by default.
     *
     * @param string|null $directory The directory path where files should be deleted from.
     * @return void
     */
    public static function deleteGeneratedFiles($directory = null)
    {
        if ($directory === null) {
            $directory = __DIR__ . '/../fixtures/';
        }

        // Check if the directory exists
        if (!is_dir($directory)) {
            return;
        }

        // Open the directory and loop through its contents
        $dirHandle = opendir($directory);
        while (($file = readdir($dirHandle)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $directory . $file;

                // Check if the path is a file
                if (is_file($filePath)) {
                    // Delete the file
                    unlink($filePath);
                }
            }
        }

        // Close the directory handle
        closedir($dirHandle);
    }
}
