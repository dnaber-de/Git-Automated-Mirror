<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Event\Listener;
use GitAutomatedMirror\Printer;
use GitAutomatedMirror\Type;
use League\Event;


/**
 * Class GitSynchronizeVerboseReporter
 *
 * Listen to git.synchronize events and reports them
 * verbose to a printer
 *
 * @package GitAutomatedMirror\Event\Listener
 */
class GitSynchronizeVerboseReporter implements Event\ListenerInterface {

	/**
	 * @type Printer\PrinterInterface
	 */
	private $printer;

	/**
	 * @param Printer\PrinterInterface $printer
	 */
	public function __construct( Printer\PrinterInterface $printer ) {

		$this->printer = $printer;
	}

	/**
	 * Handle an event.
	 *
	 * @param Event\EventInterface $event
	 *
	 * @return void
	 */
	public function handle( Event\EventInterface $event ) {

		$eventParameter = func_num_args() > 1
			? func_get_args()[ 1 ]
			: [];
		if ( ! is_array( $eventParameter ) )
			$eventParameter = [ $eventParameter ];

		switch ( $event->getName() ) {
			case 'git.synchronize.beforePushBranch' :
				$message = $this->getBeforePushBranchMessage( $event, $eventParameter );
				break;

			case 'git.synchronize.done' :
				$message = $this->getSynchronizeDoneMessage( $event, $eventParameter );
				break;

			case 'git.event.mergedMergeBranch' :
				$message = $this->getMergeBranchMergedMessage( $event, $eventParameter );
				break;

			case 'git.tagMerge.beforePushTag' :
				$message = $this->getBeforePushTagMessage( $event, $eventParameter );
				break;

			case 'git.tagMerge.skipExistingTag' :
				$message = $this->getSkippedTagMessage( $event, $eventParameter );
				break;

			default :
				$message = $this->getUnknownEventMessage( $event );
				break;
		}

		$this->printer->printLine( $message );
	}

	/**
	 * @param Event\EventInterface $event
	 * @return string
	 */
	public function getUnknownEventMessage( Event\EventInterface $event ) {

		$message = "Event {$event->getName()} triggered.";

		return $message;
	}

	/**
	 * @param Event\EventInterface $event
	 * @param array                $eventParameter
	 * @return string
	 */
	public function getBeforePushBranchMessage( Event\EventInterface $event, Array $eventParameter ) {

		/**
		 * @type Type\GitRemote $fromRemote
		 * @type Type\GitRemote $toRemote
		 * @type Type\GitBranch $branch
		 */
		$fromRemote = $eventParameter[ 'fromRemote' ];
		$toRemote   = $eventParameter[ 'toRemote' ];
		$branch     = $eventParameter[ 'branch' ];
		$message    = sprintf(
			"Push branch '%s' from '%s' to '%s'",
			$branch->getName(),
			$fromRemote->getName(),
			$toRemote->getName()
		);

		return $message;
	}

	/**
	 * @param Event\EventInterface $event
	 * @param array                $eventParameter
	 * @return string
	 */
	public function getSynchronizeDoneMessage( Event\EventInterface $event, Array $eventParameter ) {

		/**
		 * @type Type\GitRemote $fromRemote
		 * @type Type\GitRemote $toRemote
		 */
		$fromRemote = $eventParameter[ 'fromRemote' ];
		$toRemote   = $eventParameter[ 'toRemote' ];
		$message    = sprintf(
			"Synchronized remote '%s' to '%s'",
			$fromRemote->getName(),
			$toRemote->getName()
		);

		return $message;
	}

	public function getMergeBranchMergedMessage( Event\EventInterface $event, Array $eventParameter ) {

		/**
		 * @type Type\GitBranch $banch
		 * @type Type\GitBranch $mergeBranch
		 */
		$branch = $eventParameter[ 'branch' ];
		$mergeBranch = $eventParameter[ 'mergeBranch' ];
		$message = sprintf(
			"Merged '%s' into '%s'",
			$mergeBranch,
			$branch
		);

		return $message;
	}

	/**
	 * @param Event\EventInterface $event
	 * @param array                $eventParameter
	 * @return string
	 */
	public function getBeforePushTagMessage( Event\EventInterface $event, Array $eventParameter ) {

		/**
		 * @type Type\GitTag $tag
		 * @type Type\GitRemote $remote
		 */
		$tag = $eventParameter[ 'tag' ];
		$remote = $eventParameter[ 'remote' ];
		$message = sprintf(
			'Push rebased tag %s to %s',
			$tag,
			$remote
		);

		return $message;
	}

	/**
	 * @param Event\EventInterface $event
	 * @param array                $eventParameter
	 * @return string
	 */
	public function getSkippedTagMessage( Event\EventInterface $event, Array $eventParameter ) {

		/**
		 * @type Type\GitTag $tag
		 * @type Type\GitRemote $remote
		 */
		$tag = $eventParameter[ 'tag' ];
		$remote = $eventParameter[ 'remote' ];
		$message = sprintf(
			'Skipped tag %s as it already exists in %s',
			$tag,
			$remote
		);

		return $message;
	}

	/**
	 * Check weather the listener is the given parameter.
	 *
	 * @param mixed $listener
	 *
	 * @return bool
	 */
	public function isListener( $listener ) {

		return $this === $listener;
	}
}