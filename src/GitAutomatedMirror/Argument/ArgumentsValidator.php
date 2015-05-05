<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Argument;
use GitAutomatedMirror\Config;
use GitAutomatedMirror\Type;
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
	public $optionResult;

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
		$this->arguments    = $arguments;
	}

	/**
	 * Return an array of Type\ApplicationArgument-s
	 * if any is missing
	 *
	 * @return array
	 */
	public function getMissingArguments() {

		$missingArguments = [];
		$argSpecification = $this->arguments->getDefinedArguments();
		foreach ( $argSpecification as $arg ) {
			/* @type Type\ApplicationArgument $arg */
			if ( ! $arg->isRequired() )
				continue;

			if ( $this->optionResult->has( $arg->getName() ) )
				continue;

			$missingArguments[] = $arg;
		}

		return $missingArguments;
	}

	/**
	 * @return bool
	 */
	public function isValidRequest() {

		return [] === $this->getMissingArguments();
	}
} 