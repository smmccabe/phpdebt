<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

use PHPDebt\Console\Command\PHPDebtCommand;
use Symfony\Component\Console\Application;

$command = new PHPDebtCommand();
$app = new Application();
$app->add($command);
$app->setDefaultCommand($command->getName(), TRUE);
$app->run();
