#!/bin/sh

# Load environment variables from .env file
php -r "require 'vendor/autoload.php'; (new \Symfony\Component\Dotenv\Dotenv())->load('.env');"

# Set the XML_FILE_PATH environment variable
export XML_FILE_PATH="$XML_FILE_PATH"

# Run the command specified in the Dockerfile
# Set ownership to root
chown -R root:root /var/www/html/productsup_xml_importer/var/log/

# Set permissions to 755
chmod -R 755 /var/www/html/productsup_xml_importer/var/log/

exec "$@"
