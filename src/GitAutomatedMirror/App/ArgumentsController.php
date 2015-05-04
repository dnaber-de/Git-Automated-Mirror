<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use GetOptionKit;

/**
 * Class ArgumentsController
 *
 * Registers and parses the application arguments
 *
 * @package GitAutomatedMirror\App
 */
class ArgumentsController {

	/**
	 * @type GetOptionKit\OptionCollection
	 */
	private $optionCollection;

	/**
	 * @type Config\ArgumentsSetup
	 */
	private $arguments;

	/**
	 * @type GetOptionKit\OptionParser
	 */
	private $parser;

	/**
	 * @param Config\ArgumentsSetup              $arguments
	 * @param GetOptionKit\OptionCollection $optionCollection
	 * @param GetOptionKit\OptionParser     $parser
	 */
	public function __construct(
		Config\ArgumentsSetup $arguments,
		GetOptionKit\OptionCollection $optionCollection,
		GetOptionKit\OptionParser $parser
	) {
		$this->arguments        = $arguments;
		$this->optionCollection = $optionCollection;
		$this->parser           = $parser;
	}

	public function registerArguments() {

		$this->arguments->registerDefaultArguments();
	}

	/**
	 * @param array $argv
	 * @return GetOptionKit\OptionResult
	 */
	public function parseInput( Array $argv ) {

		try {
			return $this->parser->parse( $argv );
		} catch ( GetOptionKit\Exception\InvalidOptionException $e ) {
			exit( $e->getMessage() );
		}
	}
} 