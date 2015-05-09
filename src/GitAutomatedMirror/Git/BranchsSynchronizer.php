<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;
use League\Event;
use PHPGit;

class BranchsSynchronizer {

	/**
	 * @type PHPGit\Git
	 */
	private $git;

	/**
	 * @type BranchReader
	 */
	private $branchReader;

	/**
	 * @type Event\Emitter
	 */
	private $eventEmitter;

	/**
	 * @type array
	 */
	private $ignoredBranches = [];

	/**
	 * @param PHPGit\Git    $git
	 * @param BranchReader  $branchReader
	 * @param Event\Emitter $eventEmitter
	 */
	public function __construct(
		PHPGit\Git $git,
		BranchReader $branchReader,
		Event\Emitter $eventEmitter
	) {

		$this->git = $git;
		$this->branchReader = $branchReader;
		$this->eventEmitter = $eventEmitter;
	}

	/**
	 * @param Type\GitBranch $branch
	 */
	public function pushIgnoredBranch( Type\GitBranch $branch ) {

		if ( ! in_array( $branch, $this->ignoredBranches ) )
			$this->ignoredBranches[] = $branch;
	}

	/**
	 * @param Type\GitBranch $branch
	 */
	public function popIgnoredBranch( Type\GitBranch $branch ) {

		$key = array_search( $branch, $this->ignoredBranches );
		if ( FALSE === $key )
			return;

		unset( $this->ignoredBranches[ $key ] );
	}

	/**
	 * the working horse
	 * loops over all branches and deliver them to the mirror
	 *
	 * @param Type\GitRemote $from
	 * @param Type\GitRemote $to
	 */
	public function synchronizeBranches( Type\GitRemote $from, Type\GitRemote $to ) {

		foreach ( $this->branchReader->getBranches() as $branch ) {
			if ( in_array( $branch, $this->ignoredBranches ) )
				continue;
			$this->synchronizeSingleBranch( $branch, $from, $to );
		}
		$this->eventEmitter->emit(
			'git.synchronize.done',
			[
				'gitClient'    => $this->git,
				'branchReader' => $this->branchReader,
				'fromRemote'   => $from,
				'toRemote'     => $to
			]
		);
	}

	/**
	 * Pull/track a given branch and push it to the given Remote
	 *
	 * @param Type\GitBranch $branch
	 * @param Type\GitRemote $from
	 * @param Type\GitRemote $to
	 */
	public function synchronizeSingleBranch( Type\GitBranch $branch, Type\GitRemote $from, Type\GitRemote $to ) {

		if ( ! $branch->isLocal() ) {
			$this->trackBranchLocally( $branch, $from );
		}

		$this->pullBranch( $branch, $from );
		$this->eventEmitter->emit(
			'git.synchronize.beforePushBranch',
			[
				'gitClient'    => $this->git,
				'branchReader' => $this->branchReader,
				'branch'       => $branch,
				'fromRemote'   => $from,
				'toRemote'     => $to
			]
		);
		$this->pushBranch( $branch, $to );
	}

	/**
	 * @param Type\GitBranch $branch
	 * @param Type\GitRemote $from
	 * @return bool
	 */
	public function trackBranchLocally( Type\GitBranch $branch, Type\GitRemote $from ) {

		if ( $branch->isLocal() )
			return FALSE;

		$remotes = $branch->getRemotes();
		$remoteName = $from->getName();
		# branch cannot tracked from the given remote
		if ( ! isset( $remotes[ $remoteName ] ) )
			return FALSE;

		$fullRef = $remotes[ $remoteName ];
		if ( $this->git->checkout->create( $branch->getName(), $fullRef ) )
			$branch->setIsLocal( TRUE );
	}

	/**
	 * @param Type\GitBranch $branch
	 * @param Type\GitRemote $from
	 * @return bool
	 */
	public function pullBranch( Type\GitBranch $branch, Type\GitRemote $from ) {

		$branchRemotes = $branch->getRemotes();
		if ( ! isset( $branchRemotes[ $from->getName() ] ) )
			return FALSE;

		// e.g. 'remotes/origin/master' → 'origin/master'
		$remoteRef = str_replace( 'remotes/', '', $branchRemotes[ $from->getName() ] );
		$commitMessage = "Pull {$branch->getName()}";

		/**
		 * a simple $this->git->pull() would lead
		 * to a possibly necessary merge we can not deal with,
		 * so we make a manual pull with fetch and merge
		 *
		 * @link http://longair.net/blog/2009/04/16/git-fetch-and-merge/
		 */
		$this->git->checkout( $branch->getName() );
		$this->git->fetch( $from->getName() );

		/**
		 * a `$ git fetch` updates the branch origin/master (= $remoteRef)
		 * after that, we merge the $remoteRef into the current branch
		 */
		$this->git->merge( $remoteRef, $commitMessage, [ 'strategy' => 'theirs', 'no-ff' => TRUE ] );
		return TRUE;
	}

	/**
	 * @param Type\GitBranch $branch
	 * @param Type\GitRemote $to
	 * @return bool
	 */
	public function pushBranch( Type\GitBranch $branch, Type\GitRemote $to ) {

		$this->git->checkout( $branch->getName() );
		$this->git->push( $to->getName(), $branch->getName(), [ 'force' => TRUE ] );

		return TRUE;
	}
} 