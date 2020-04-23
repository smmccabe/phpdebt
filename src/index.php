<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

use PHPMD\PHPMD;
use PHPMD\RuleSetFactory;

use SebastianBergmann\FinderFacade\FinderFacade;
use SebastianBergmann\PHPLOC\Analyser;

use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Config;

$code_faults = 0;
$standards_faults = 0;
$path = $argv[1];

function phpMD($path, $type) {
    $ruleSetFactory = new RuleSetFactory();
    $phpmd = new PHPMD();

    $report = $phpmd->runReport(
        $path,
        $type,
        $ruleSetFactory
    );

    $faults = count($report->getRuleViolations());

    print "phpmd " . $type . ": " . $faults . "\n";

    return count($report->getRuleViolations());
}

$code_faults += phpMD($path, 'cleancode');
$code_faults += phpMD($path, 'codesize');
$code_faults += phpMD($path, 'design');
$code_faults += phpMD($path, 'naming');
$code_faults += phpMD($path, 'unusedcode');


$runner = new Runner();

$runner->config = new Config();
$runner->config->files = [$path];
$runner->config->standards = ['Drupal'];
$runner->config->extensions = [
    'php' => 'PHP',
    'module' => 'PHP',
    'inc' => 'PHP',
    'install' => 'PHP',
    'test' => 'PHP',
    'profile' => 'PHP',
    'theme' => 'PHP',
    'css' => 'CSS',
    'info' => 'PHP',
    'js' => 'JS'
];
$runner->init();

$faults = $runner->run();
print "phpcs Drupal: " . $faults . "\n";
$standards_faults += $faults;

$runner->config->standards = ['DrupalPractice'];
$runner->init();
$faults = $runner->run();
print "phpcs DrupalPractice: " . $faults . "\n";
$standards_faults += $faults;

// Lines of Code
$files = (new FinderFacade([$path], [], ['*.php', '*.module', '*.install', '*.inc', '*.js', '*.scss'], []))->findFiles();
$count = (new Analyser)->countFiles($files, true);
$total_lines = $count['ncloc'];

$faults = $code_faults + $standards_faults;
$percent = ($faults / $total_lines) * 100;

// Summary
print "Total Faults: " . $faults . "\n";
print "Total Lines: " . $total_lines . "\n";
print "Quality Score: " . number_format($percent, 2) . " faults per 100 lines\n";

$ratio = 200;
$base = ($total_lines / $ratio) * sqrt($percent);

if ($percent > 25) {
    $hours = ($total_lines / $ratio) * sqrt($percent - ($percent - 25));
    print "Estimate to get to 25: " . number_format($base - $hours, 2) . " hours\n";
}

if ($percent > 10) {
    $hours = ($total_lines / $ratio) * sqrt($percent - ($percent - 10));
    print "Estimate to get to 10: " . number_format($base - $hours, 2) . " hours\n";
}

if ($percent > 5) {
    $hours = ($total_lines / $ratio) * sqrt($percent - ($percent - 5));
    print "Estimate to get to 5: " . number_format($base - $hours, 2) . " hours\n";
}

if ($percent > 3) {
    $hours = ($total_lines / $ratio) * sqrt($percent - ($percent - 3));
    print "Estimate to get to 3: " . number_format($base - $hours, 2) . " hours\n";
}