<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Config;
use GitAutomatedMirror\App;
use Dice;

/**
 * Improvement refactoring hints:
 *
 * Currently the class does two things: It provides an interface to
 * apply substitutions to the Dice instance (which is not application specific)
 *
 * and
 *
 * it builds up concrete instances of other classes which is application specific
 *
 * both should be split to separate classes.
 */
class DiceConfigurator {

	/**
	 * @type Dice\Dice;
	 */
	private $diContainer;

	/**
	 * @param Dice\Dice $diContainer
	 */
	public function __construct( Dice\Dice $diContainer ) {

		$this->diContainer = $diContainer;
	}

	/**
	 * configure the DI container
	 */
	public function initialConfiguration() {

		/**
		 * setup Dice to share the OptionCollection instance
		 * in the object tree
		 *
		 * @link https://r.je/dice.html
		 */
		$optionCollectionRule = new Dice\Rule;
		$optionCollectionRule->shared = TRUE;
		$this->diContainer->addRule( 'GetOptionKit\OptionCollection', $optionCollectionRule );

		// share the git object
		$gitRule = new Dice\Rule;
		$gitRule->shared = TRUE;
		$this->diContainer->addRule( 'PHPGit\Git', $gitRule );

		// share the event emitter
		$eventEmitterRule = new Dice\Rule;
		$eventEmitterRule->shared = TRUE;
		$this->diContainer->addRule( 'League\Event\Emitter', $eventEmitterRule );
	}

	/**
	 * share a already created instance with the DI container
	 * to all dependents
	 *
	 * @param       $className
	 * @param       $instance
	 * @param array $affectedClasses
	 */
	public function applySubstitution( $className, $instance, Array $affectedClasses = [] ) {

		$rule = new Dice\Rule;
		$rule->shared = TRUE;
		$rule->substitutions[ $className ] = $instance;
		if ( empty( $affectedClasses ) ) {
			$this->diContainer->addRule( $className, $rule );
			return;
		}

		foreach ( $affectedClasses as $class )
			$this->diContainer->addRule( $class, $rule );
	}
} 