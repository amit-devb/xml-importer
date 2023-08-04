
# Use the official PHP image with FPM as the base image
FROM php:8.0-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y wget unzip

# Install Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory inside the container
WORKDIR /var/www/html/productsup_xml_importer/

# Copy the entrypoint.sh file into the container
COPY entrypoint.sh /entrypoint.sh

# Give executable permissions to the entrypoint.sh file
RUN chmod +x /entrypoint.sh

# Copy the test data files to the image
COPY tests/fixtures/ /var/www/html/productsup_xml_importer/tests/fixtures/


# Expose port 8000 for the Symfony development server
EXPOSE 8000
