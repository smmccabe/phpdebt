<?php

namespace PHPDebt;

use PHPMD\PHPMD;
use PHPMD\RuleSetFactory;

/**
 * The main facade of the PHPDebt's PHPMD application.
 *
 * @package PHPDebt
 */
class PHPDebtMD {

	/**
	 * This methods does the job of returning the rule violations as per PHPMD.
	 *
	 * @param string $path
	 *   The provided path that needs to be scanned.
	 * @param string $type
	 *   The received ruleset.
	 *
	 * @return int|void
	 */
	public function process(string $path, string $type) {
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

}
