#!/usr/bin/env bash

echo "Fixing code sniffs"
php -d memory_limit=512M vendor/bin/phpcbf -p -s --standard=build/phpcs.xml

echo "Running php-cs-fixer"
vendor/bin/php-cs-fixer fix src
vendor/bin/php-cs-fixer fix tests


echo "Running code sniffer"
vendor/bin/phpcs -p -s --standard=build/phpcs.xml

echo "Running Mess Detector"
vendor/bin/phpmd src text build/phpmd.xml
