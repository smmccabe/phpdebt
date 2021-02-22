<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

use PHPDebt\Console\Command\CodeSnifferConfigCommand;
use Symfony\Component\Console\Application;

$command = new CodeSnifferConfigCommand();
$app = new Application();
$app->add($command);
$app->setDefaultCommand($command->getName(), TRUE);
$app->run();
