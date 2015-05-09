<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;
use GitAutomatedMirror\Argument;
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
		 * in the object tree
		 *
		 * @link https://r.je/dice.html
		 */
		$optionCollectionRule = new Dice\Rule;
		$optionCollectionRule->shared = TRUE;
		$this->di_container->addRule( 'GetOptionKit\OptionCollection', $optionCollectionRule );

		// share the git object
		$gitRule = new Dice\Rule;
		$gitRule->shared = TRUE;
		$this->di_container->addRule( 'PHPGit\Git', $gitRule );

		/* @type PHPGit\Git $git */
		$git = $this->di_container->create( 'PHPGit\Git' );

		/* @type Argument\ArgumentsController $argController */
		$argController = $this->di_container->create( 'GitAutomatedMirror\Argument\ArgumentsController' );

		/* @type GetOptionKit\OptionCollection $argument_specs */
		$argumentsSpec = $this->di_container->create( 'GetOptionKit\OptionCollection' );

		// Register arguments and parse $argv
		$argController->registerArguments();
		$optionResults = $argController->parseInput( $argv );

		/**
		 * share the parsing results with the object tree
		 */
		$optionResultRule = new Dice\Rule;
		$optionCollectionRule->shared = TRUE;
		$optionResultRule->substitutions[ 'GetOptionKit\OptionResult' ] = $optionResults;
		$this->di_container->addRule( 'GitAutomatedMirror\Argument\ArgumentsValidator', $optionResultRule );
		$this->di_container->addRule(  __NAMESPACE__ . '\GitMirrorArguments', $optionResultRule );

		// validating the arguments
		/** @type  Argument\ArgumentsValidator $argValidator */
		$argValidator = $this->di_container->create( 'GitAutomatedMirror\Argument\ArgumentsValidator' );

		// closing the application if the help argument is passed or there are no arguments at all
		if ( $optionResults->has( 'help' ) || ! $argValidator->isValidRequest() ) {
			$printer = new GetOptionKit\OptionPrinter\ConsoleOptionPrinter;
			echo $printer->render( $argumentsSpec );
			return;
		}

		/** @type GitMirrorArguments $appArguments */
		$appArguments = $this->di_container->create( __NAMESPACE__ . '\GitMirrorArguments' );

		// now that are all required arguments exists, check the given remotes
		$git->setRepository( $appArguments->getRepository() );

		// now that are all required arguments exists, check the given remotes
		if ( ! $argValidator->remotesExists() ) {
			$missingRemotes = $argValidator->getInvalidRemotes();
			$missingRemotesStr = implode( ", $", $missingRemotes );
			echo "Error: the given remotes does not exist in the repository: \$$missingRemotesStr\n";
			return;
		}

		/**
		 * The BranchReader gets us the branches from the repository
		 *
		 * @type Git\BranchReader $branchReader
		 */
		$branchReader = $this->di_container->create( 'GitAutomatedMirror\Git\BranchReader' );
		$branchReader->buildBranches();
		$branches = $branchReader->getBranches();


		// the remote we want to pull from
		$sourceRemote = $appArguments->getRemoteSource();
		// the remote we want to push to
		$mirrorRemote = $appArguments->getRemoteMirror();

		$ignoredBranches = new Git\IgnoredBranches( $branchReader );

		$branchesSynchronizer = new Git\BranchsSynchronizer( $git, $branchReader );
		foreach ( $ignoredBranches->getIgnoredBranches() as $branch )
			$branchesSynchronizer->pushIgnoredBranch( $branch );

		# ignore some branches like HEAD
		$branchesSynchronizer->pushIgnoredBranch( new Type\GitBranch( 'HEAD', FALSE ) );
		$branchesSynchronizer->synchronizeBranches( $sourceRemote, $mirrorRemote );

	}

	public function shutdown() {

		echo "\n";
	}
}