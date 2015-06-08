<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Event\Listener;
use GitAutomatedMirror\Type;
use PHPGit;
use League\Event;
use League\Event\EventInterface;

/**
 * Class MergeArgumentBranch
 *
 * merges $branchToMerge into that one provided
 * by the event git.synchronize.beforePushBranch
 *
 * @package GitAutomatedMirror\Event\Listener
 */
class MergeArgumentBranch implements Event\ListenerInterface {

	/**
	 * @type Type\GitBranch
	 */
	private $branchToMerge;

	/**
	 * @type Event\Emitter
	 */
	private $eventEmitter;

	/**
	 * @param Type\GitBranch $branchToMerge
	 * @param Event\Emitter  $eventEmitter
	 */
	public function __construct(
		Type\GitBranch $branchToMerge,
		Event\Emitter $eventEmitter
	) {

		$this->branchToMerge = $branchToMerge;
		$this->eventEmitter = $eventEmitter;
	}

	/**
	 * Handle an event.
	 *
	 * @see Git\BranchsSynchronizer::synchronizeSingleBranch()
	 * @param EventInterface $event
	 * @return void
	 */
	public function handle( EventInterface $event ) {

		$arguments = func_num_args() > 1
			? func_get_args()[ 1 ]
			: [];
		/**
		 * @type Type\GitBranch $branch
		 * @type PHPGit\Git $git
		 */
		$branch = $arguments[ 'branch' ];
		$git    = $arguments[ 'gitClient' ];

		$git->checkout( $branch );
		$git->merge(
			$this->branchToMerge,
			NULL,
			[
				'no-ff' => TRUE,
				'strategy' => 'ours' //  the source repo "wins"
			]
		);
		$this->eventEmitter->emit(
			'git.event.mergedMergeBranch',
			[
				'branch' => $branch,
				'mergeBranch' => $this->branchToMerge,
			]
		);
	}

	/**
	 * Check weather the listener is the given parameter.
	 *
	 * @param mixed $listener
	 * @return bool
	 */
	public function isListener( $listener ) {

		return $this === $listener;
	}
} 