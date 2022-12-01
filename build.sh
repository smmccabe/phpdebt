#!/bin/bash

cp -R vendor/drupal/coder/coder_sniffer/Drupal vendor/squizlabs/php_codesniffer/src/Standards
cp -R vendor/drupal/coder/coder_sniffer/DrupalPractice vendor/squizlabs/php_codesniffer/src/Standards
rm phpdebt.phar
./vendor/bin/phar-composer build .