<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Config;
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
		 * @type Event\ListenerProviderInterface $listenerProvider
		 *
		 */
		$emitter = $this->diContainer->create( 'League\Event\Emitter' );
		$listenerProvider = $this->diContainer->create(
			'GitAutomatedMirror\Event\ListenerProvider\ListenerMapProvider',
			[
				[
					'git.synchronize.done' => $this->diContainer
							->create( 'GitAutomatedMirror\Event\Listener\GitSynchronizeVerboseReporter' ),
					'git.synchronize.beforePushBranch' => $this->diContainer
							->create( 'GitAutomatedMirror\Event\Listener\GitSynchronizeVerboseReporter' )
				]
			]
		);
		$emitter->useListenerProvider( $listenerProvider );
	}
} 