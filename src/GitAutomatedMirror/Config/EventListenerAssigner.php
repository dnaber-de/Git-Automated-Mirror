<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Config;
use GitAutomatedMirror\Type;
use GitAutomatedMirror\Event\Listener;
use GitAutomatedMirror\Event\ListenerProvider;
use GetOptionKit;
use League\Event;
use Dice;

/**
 * Class EventListenerAssigner
 *
 * Handles event listener registration
 *
 * @todo re-think that name!
 *
 * @package GitAutomatedMirror\Config
 */
class EventListenerAssigner {

	/**
	 * @type GetOptionKit\OptionCollection
	 */
	private $optionCollection;

	/**
	 * @type Event\Emitter
	 */
	private $eventEmitter;

	/**
	 * @type Dice\Dice
	 */
	private $diContainer;

	/**
	 * @param GetOptionKit\OptionCollection $optionCollection
	 * @param Event\Emitter                 $eventEmitter
	 * @param Dice\Dice                     $diContainer
	 */
	public function __construct(
		GetOptionKit\OptionCollection $optionCollection,
		Event\Emitter                 $eventEmitter,
		Dice\Dice                     $diContainer
	) {

		$this->optionCollection = $optionCollection;
		$this->eventEmitter = $eventEmitter;
		$this->diContainer = $diContainer;
	}

	/**
	 * registers verbose printer to
	 * git.synchronize.* events
	 */
	public function registerGitSynchronizeListener() {

		/**
		 * @type Event\Emitter $emitter
		 */
		$emitter = $this->diContainer->create( 'League\Event\Emitter' );
		$listener = $this->diContainer
			->create( 'GitAutomatedMirror\Event\Listener\GitSynchronizeVerboseReporter' );
		$listenerProvider = new ListenerProvider\ListenerMapProvider(
			[
				'git.synchronize.done' => $listener,
				'git.synchronize.beforePushBranch' => $listener,
				'git.event.mergedMergeBranch' => $listener,
				'git.tagMerge.beforePushTag' => $listener
			]
		);
		$emitter->useListenerProvider( $listenerProvider );
	}

	/**
	 * @param Type\GitBranch $mergeBranch
	 */
	public function registerMergeBranchListener( Type\GitBranch $mergeBranch ) {

		/**
		 * @type Event\Emitter $emitter
		 */
		// @todo: resolve this courier anti-pattern
		$listener = new Listener\MergeArgumentBranch( $mergeBranch, $this->eventEmitter );
		$listenerProvider = new ListenerProvider\ListenerMapProvider(
			[
				'git.synchronize.beforePushBranch' => $listener
			]
		);
		$emitter = $this->diContainer->create( 'League\Event\Emitter' );
		$emitter->useListenerProvider( $listenerProvider );
	}
} 