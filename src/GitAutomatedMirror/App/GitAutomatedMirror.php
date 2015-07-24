<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;
use GitAutomatedMirror\Argument;
use GitAutomatedMirror\Printer;
use League\Event;
use GetOptionKit;
use PHPGit;
use Dice;

class GitAutomatedMirror {

	/**
	 * @type string
	 */
	private $version;

	/**
	 * @type Dice\Dice
	 */
	private $diContainer;

	/**
	 * @type Config\DiceConfigurator
	 */
	private $diceConfigurator;

	/**
	 * actually passes parameters
	 *
	 * @type GetOptionKit\OptionResult
	 */
	private $args;

	/**
	 * @type PHPGit\Git
	 */
	private $git;

	/**
	 * @type Event\Emitter
	 */
	private $eventEmitter;

	/**
	 * @type Argument\ArgumentsController
	 */
	private $argController;

	/**
	 * @type GetOptionKit\OptionCollection
	 */
	private $argumentsSpecification;

	/**
	 * @param Dice\Dice               $diContainer
	 * @param Config\DiceConfigurator $diceConfigurator
	 */
	public function __construct(
		Dice\Dice $diContainer,
		Config\DiceConfigurator $diceConfigurator
	) {

		$this->diContainer = $diContainer;
		$this->diceConfigurator = $diceConfigurator;
		$this->version = '0.1-master';
	}

	/**
	 * initialize the objects
	 */
	public function init() {

		$this->diceConfigurator->initialConfiguration();
		$this->git = $this->diContainer->create( 'PHPGit\Git' );
		$this->eventEmitter = $this->diContainer->create( 'League\Event\Emitter' );
		$this->argController = $this->diContainer->create( 'GitAutomatedMirror\Argument\ArgumentsController' );
		$this->argumentsSpecification = $this->diContainer->create( 'GetOptionKit\OptionCollection' );
		// Register arguments
		$this->argController->registerArguments();

		$this->diceConfigurator->printerConfiguration();
	}

	/**
	 * Damn this mess has to be refactored
	 *
	 * @param array $argv
	 */
	public function run( array $argv ) {

		$optionResults = $this->argController->parseInput( $argv );
		/**
		 * share the passed arguments with the object tree
		 */
		$this->diceConfigurator->applySubstitution(
			'GetOptionKit\OptionResult',
			$optionResults,
			[
				'GitAutomatedMirror\Argument\ArgumentsValidator',
				__NAMESPACE__ . '\GitMirrorArguments'
			]
		);

		// validating the arguments
		/** @type  Argument\ArgumentsValidator $argValidator */
		$argValidator = $this->diContainer->create( 'GitAutomatedMirror\Argument\ArgumentsValidator' );

		/**
		 * give access to the script arguments
		 *
		 * @type GitMirrorArguments $appArguments
		 */
		$appArguments = $this->diContainer->create( __NAMESPACE__ . '\GitMirrorArguments' );

		// closing the application if the help argument is passed or there are no arguments at all
		if ( $optionResults->has( 'help' ) || ! $argValidator->isValidRequest() ) {
			$stdPrinter = new Printer\StdOutPrinter;
			$stdPrinter->printLine( "Git automated mirror Version {$this->version}" );
			$optionPrinter = new GetOptionKit\OptionPrinter\ConsoleOptionPrinter;
			echo $optionPrinter->render( $this->argumentsSpecification );

			return;
		}

		// tell the git client about the directory
		$this->git->setRepository( $appArguments->getRepository() );
		// checkout the master branch (the repo might be in a 'not on any branch' state
		// which leads to errors in the Git client somehow
		$this->git->checkout( 'master' );

		// invalid merge-branch argument
		if ( $argValidator->mergeBranchProvided() && ! $argValidator->mergeBranchExists() ) {
			$optionPrinter = new Printer\StdOutPrinter;
			$optionPrinter->printLine( "Error: Merge branch does not exist!" );
			return;
		}

		/**
		 * @type Config\EventListenerAssigner $eventListenerAssigner
		 */
		$eventListenerAssigner = $this->diContainer->create( 'GitAutomatedMirror\Config\EventListenerAssigner' );
		$eventListenerAssigner->registerGitSynchronizeListener();
		if ( $argValidator->mergeBranchProvided() )
			$eventListenerAssigner->registerMergeBranchListener( $appArguments->getMergeBranch() );


		// now that are all required arguments exists, check the given remotes
		if ( ! $argValidator->remotesExists() ) {
			$missingRemotes = $argValidator->getInvalidRemotes();
			$missingRemotesStr = implode( ", $", $missingRemotes );
			echo "Error: the given remotes does not exist in the repository: \$$missingRemotesStr\n";
			return;
		}

		// the remote we want to pull from
		$sourceRemote = $appArguments->getRemoteSource();
		// the remote we want to push to
		$mirrorRemote = $appArguments->getRemoteMirror();

		// now fetch the updates
		$this->git->fetch( $sourceRemote );

		/**
		 * The BranchReader gets us the branches from the repository
		 *
		 * @type Git\BranchReader $branchReader
		 */
		$branchReader = $this->diContainer->create( 'GitAutomatedMirror\Git\BranchReader' );
		$ignoredBranches = new Git\IgnoredBranches( $branchReader );
		$branchesSynchronizer = new Git\BranchsSynchronizer( $this->git, $branchReader, $this->eventEmitter );
		foreach ( $ignoredBranches->getIgnoredBranches() as $branch )
			$branchesSynchronizer->pushIgnoredBranch( $branch );

		// ignore some branches like HEAD
		$branchesSynchronizer->pushIgnoredBranch( new Type\GitBranch( 'HEAD', FALSE ) );
		// don't sync the local merge branch
		if ( $argValidator->mergeBranchProvided() )
			$branchesSynchronizer->pushIgnoredBranch( $appArguments->getMergeBranch() );

		$branchesSynchronizer->synchronizeBranches( $sourceRemote, $mirrorRemote );

		/** @type Git\TagMerger $tagMerger */
		$tagMerger = $this->diContainer->create( 'GitAutomatedMirror\Git\TagMerger' );
		$tagMerger->fetchTags( $appArguments->getRepository(), $appArguments->getRemoteSource() );
		if ( $argValidator->mergeBranchProvided() ) {
			/** @type Git\TagMerger $tagMerger */
			$tagMerger->mergeBranchIntoTags( $appArguments->getMergeBranch(), $appArguments->getRemoteMirror() );
		}
		$tagMerger->pushTags( $appArguments->getRemoteMirror() );
	}


	public function shutdown() {

		echo "\n";
	}
}