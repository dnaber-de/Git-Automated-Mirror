<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use GetOptionKit;

/**
 * Class ArgumentsValidator
 *
 * Validates the given ArgumentsSetup
 *
 * @package GitAutomatedMirror\App
 */
class ArgumentsValidator {

	/**
	 * The arguments actually passed to the application
	 *
	 * @type GetOptionKit\OptionResult
	 */
	private $optionResult;

	/**
	 * The arguments definition of the application
	 *
	 * @type Config\ArgumentsSetup
	 */
	private $arguments;

	/**
	 * @param GetOptionKit\OptionResult $optionResult
	 * @param Config\ArgumentsSetup $arguments
	 */
	public function __construct( GetOptionKit\OptionResult $optionResult, Config\ArgumentsSetup $arguments ) {

		$this->optionResult = $optionResult;
	}

	public function getMissingArguments() {


	}
} 