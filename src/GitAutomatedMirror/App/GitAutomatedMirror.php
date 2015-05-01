<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use GetOptionKit;
use PHPGit;
use Dice;

class GitAutomatedMirror {

	/**
	 * @type Dice\Dice
	 */
	private $di_container;

	/**
	 * actually passes parameters
	 *
	 * @type GetOptionKit\OptionResult
	 */
	private $args;

	/**
	 * @param Dice\Dice $di_container
	 */
	public function __construct( Dice\Dice $di_container ) {

		$this->di_container = $di_container;
	}

	/**
	 * Damn this mess has to be refactored
	 *
	 * @param array $argv
	 */
	public function run( array $argv ) {

		/**
		 * setup Dice to share the OptionCollection instance
		 * between objects
		 *
		 * @link https://r.je/dice.html
		 */
		$optionCollectionRule = new Dice\Rule;
		$optionCollectionRule->shared = TRUE;
		$this->di_container->addRule( 'GetOptionKit\OptionCollection', $optionCollectionRule );

		/* @type ArgumentsController $argController */
		$argController = $this->di_container->create( __NAMESPACE__ . '\ArgumentsController' );
		/* @type GetOptionKit\OptionCollection $argument_specs */
		$argumentsSpec = $this->di_container->create( 'GetOptionKit\OptionCollection' );

		$argController->registerArguments();
		$optionResults = $argController->parseInput( $argv );
		/* Pass the GetOptionKit\OptionResult to other instances */
		$optionResultRule = new Dice\Rule;
		$optionCollectionRule->shared = TRUE;
		$optionCollectionRule->substitutions[ 'GetOptionKit\OptionResult' ] = $optionResults;
		$this->di_container->addRule( 'GetOptionKit\OptionResult', $optionResultRule );

		/* closing the application if the help argument is passed or there are no arguments at all */
		if ( $optionResults->has( 'help' ) || 2 > count( $optionResults->arguments ) ) {
			$printer = new GetOptionKit\OptionPrinter\ConsoleOptionPrinter;
			echo $printer->render( $argumentsSpec );
			return;
		}

		$git = new PHPGit\Git();
		$git->setRepository( '/var/www/projects/php/requisite' );
		$branches = $git->branch( [ 'all' => TRUE ] );

		var_dump( $branches );
		echo "end\n";
	}


}