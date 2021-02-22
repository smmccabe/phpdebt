<?php

namespace PHPDebt\Console\Command;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Runner;
use PHPDebt\PHPDebtMD;
use SebastianBergmann\FinderFacade\FinderFacade;
use SebastianBergmann\PHPLOC\Analyser;
use Symfony\Component\Console\{
	Command\Command,
	Input\InputArgument,
	Input\InputInterface,
	Input\InputOption,
	Output\OutputInterface
};

/**
 * The class for all `phpdebt` command.
 *
 * @package PHPDebt\Console\Command
 */
class PHPDebtCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('phpdebt')
			->addArgument('path', InputArgument::REQUIRED, 'The path of the file or directory that needs to be scanned.')
			->addOption('standard', 's', InputOption::VALUE_REQUIRED, 'The path of the phpcs.xml.dist')
			->setDescription('This tool provides code quality score based on a number of standards from existing code analysis tools.');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$code_faults = 0;
		$standards_faults = 0;
		$path = $input->getArgument('path');
		$option = $input->getOption('standard');

		$debt_md = new PHPDebtMD();
		$code_faults += $debt_md->process($path, 'cleancode');
		$code_faults += $debt_md->process($path, 'codesize');
		$code_faults += $debt_md->process($path, 'design');
		$code_faults += $debt_md->process($path, 'naming');
		$code_faults += $debt_md->process($path, 'unusedcode');

		$runner = new Runner();
		// If PHPCS config is provided, then execute this.
		if ($option) {
			define('PHP_CODESNIFFER_CBF', FALSE);
			$runner->config = new Config(["--standard=$option"]);
			$runner->config->files = [$path];

			$runner->init();
			$faults = $runner->run();
			print "phpcs Drupal: " . $faults . "\n";
			$standards_faults += $faults;

			$faults = $runner->run();
			print "phpcs DrupalPractice: " . $faults . "\n";
			$standards_faults += $faults;
		}
		else {
			$runner->config = new Config();
			$runner->config->files = [$path];
			$runner->config->standards = ['Drupal'];
			$runner->config->extensions = [
				'php'     => 'PHP',
				'module'  => 'PHP',
				'inc'     => 'PHP',
				'install' => 'PHP',
				'test'    => 'PHP',
				'profile' => 'PHP',
				'theme'   => 'PHP',
				'css'     => 'CSS',
				'info'    => 'PHP',
				'js'      => 'JS',
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
		}

		// Lines of Code should be working on and anything unique about getting his local back up to date?
		$files = (new FinderFacade([$path], [], ['*.php', '*.module', '*.install', '*.inc', '*.js', '*.scss'], []))->findFiles();
		$count = (new Analyser)->countFiles($files, TRUE);
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
		return 0;
	}

}
