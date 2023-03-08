<?php

/**
 * @file
 * PHP Technical Debt Calculator.
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

use PHPMD\PHPMD;
use PHPMD\Report;
use PHPMD\RuleSetFactory;

use SebastianBergmann\PHPLOC\Analyser;

use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Config;

use Symfony\Component\Finder\Finder;

$code_faults = 0;
$standards_faults = 0;
$path = $argv[1];

// Lines of code.
$finder = Finder::create();
$finder->files()->in($path)->filter(static function (SplFileInfo $file) {
    return $file->isDir() || \preg_match('/\.(php|module|install|inc|js|scss)$/', $file->getPathname());
});
$files = [];
foreach ($finder as $file) {
  $files[] = $file->getRealPath();
}
$count = (new Analyser)->countFiles($files, TRUE);
$total_lines = $count['ncloc'];

if ($total_lines === 0) {
  print "There is no code to analyse.\n";
  exit;
}

/**
 * Mess Detector function.
 */
function php_md($path, $type) {
  $ruleSetFactory = new RuleSetFactory();
  $phpmd = new PHPMD();
  $report = new Report();
  $phpmd->processFiles(
        $path,
        $type,
        $render = [],
        $ruleSetFactory,
        $report,
    );

  $faults = count($report->getRuleViolations());

  print "phpmd " . $type . ": " . $faults . "\n";

  return count($report->getRuleViolations());
}

$code_faults += php_md($path, 'cleancode');
$code_faults += php_md($path, 'codesize');
$code_faults += php_md($path, 'design');
$code_faults += php_md($path, 'naming');
$code_faults += php_md($path, 'unusedcode');


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
  'js' => 'JS',
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

$faults = $code_faults + $standards_faults;
$percent = ($faults / $total_lines) * 100;

// Summary.
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
