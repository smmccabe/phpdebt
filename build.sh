#!/bin/bash

cp vendor/drupal/coder/coder_sniffer/Drupal/ vendor/squizlabs/php_codesniffer/src/Standards/ -r
cp vendor/drupal/coder/coder_sniffer/DrupalPractice/ vendor/squizlabs/php_codesniffer/src/Standards/ -r
./vendor/bin/phar-composer build . 