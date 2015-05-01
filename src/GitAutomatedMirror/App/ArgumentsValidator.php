<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use GetOptionKit;

/**
 * Class ArgumentsValidator
 *
 * Validates the given Arguments
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
	 * @type Config\Arguments
	 */
	private $arguments;

	/**
	 * @param GetOptionKit\OptionResult $optionResult
	 * @param Config\Arguments $arguments
	 */
	public function __construct( GetOptionKit\OptionResult $optionResult, Config\Arguments $arguments ) {

		$this->optionResult = $optionResult;
	}

	public function getMissingArguments() {


	}
} 