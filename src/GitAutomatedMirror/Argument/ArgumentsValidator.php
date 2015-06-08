<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Argument;
use GitAutomatedMirror\Config;
use GitAutomatedMirror\Type;
use GetOptionKit;
use PHPGit;

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
	private $optionResults;

	/**
	 * The arguments definition of the application
	 *
	 * @type Config\ArgumentsSetup
	 */
	private $argumentsSetup;

	/**
	 * @type PHPGit\Git
	 */
	public $git;

	/**
	 * @param GetOptionKit\OptionResult $optionResults
	 * @param Config\ArgumentsSetup     $argumentsSetup
	 * @param PHPGit\Git                $git
	 */
	public function __construct(
		GetOptionKit\OptionResult $optionResults,
		Config\ArgumentsSetup $argumentsSetup,
		PHPGit\Git $git
	) {

		$this->optionResults  = $optionResults;
		$this->argumentsSetup = $argumentsSetup;
		$this->git            = $git;
	}

	/**
	 * Return an array of Type\ApplicationArgument-s
	 * if any is missing
	 *
	 * @return array
	 */
	public function getMissingArguments() {

		$missingArguments = [];
		$argSpecification = $this->argumentsSetup->getRegisteredArguments();
		foreach ( $argSpecification as $arg ) {
			/* @type Type\ApplicationArgument $arg */
			if ( ! $arg->isRequired() )
				continue;

			if ( $this->optionResults->has( $arg->getName() ) )
				continue;

			$missingArguments[] = $arg;
		}

		return $missingArguments;
	}

	/**
	 * @return bool
	 */
	public function remotesExists() {

		return [] === $this->getInvalidRemotes();
	}

	/**
	 * @return array
	 */
	public function getInvalidRemotes() {

		$origin  = $this->optionResults[ 'remote-source' ]->value;
		$mirror  = $this->optionResults[ 'remote-mirror' ]->value;
		$remotes = $this->git->remote();
		$missing = [];

		if ( ! isset( $remotes[ $origin ] ) )
			$missing[] = 'remote-source';

		if ( ! isset( $remotes[ $mirror ] ) )
			$missing[] = 'remote-mirror';

		return $missing;
	}

	/**
	 * @return bool
	 */
	public function isValidRequest() {

		return [] === $this->getMissingArguments();
	}
} 