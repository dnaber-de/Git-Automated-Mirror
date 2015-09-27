<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Git;
use
	GitAutomatedMirror\Type,
	PHPGit,
	League\Event;

/**
 * Class TagMerger
 *
 * Update each tag: merge the mergeBranch into each tag
 *
 * @package GitAutomatedMirror\Git
 */
class TagMerger {

	/**
	 * @type TagReader
	 */
	private $tagReader;

	/**
	 * @type PHPGit\Git
	 */
	private $gitClient;

	/**
	 * @type Event\Emitter
	 */
	private $eventEmitter;

	/**
	 * @type BranchReader
	 */
	private $branchReader;

	/**
	 * @type string
	 */
	private $tempBranch = '';

	/**
	 * @param TagReader     $tagReader
	 * @param PHPGit\Git    $gitClient
	 * @param Event\Emitter $eventEmitter
	 * @param BranchReader  $branchReader
	 */
	public function __construct(
		TagReader $tagReader,
		PHPGit\Git $gitClient,
		Event\Emitter $eventEmitter,
		BranchReader $branchReader
	) {

		$this->tagReader    = $tagReader;
		$this->gitClient    = $gitClient;
		$this->eventEmitter = $eventEmitter;
		$this->branchReader = $branchReader;

		$this->tempBranch = 'gamTempBranch';
	}

	/**
	 * @param Type\GitRepository $repository
	 * @param Type\GitRemote     $fromRemote
	 */
	public function fetchTags( Type\GitRepository $repository, Type\GitRemote $fromRemote ) {

		// unfortunately PHPGit does not support fetching tags
		chdir( $repository );
		$remoteName = escapeshellarg( $fromRemote->getName() );
		shell_exec( "git fetch --tags $remoteName" );
	}

	/**
	 * @param Type\GitBranch $mergeBranch
	 * @param Type\GitRemote $toRemote
	 * @param array $remoteTags List of Type\GitTag objects of the mirror repository
	 */
	public function mergeBranchIntoTags(
		Type\GitBranch $mergeBranch,
		Type\GitRemote $toRemote,
		Array $remoteTags
	) {

		// @Todo: Take care of the current branch of the repo and set it back after we're done
		foreach ( $this->tagReader->getTags() as $tag ) {
			/* @var Type\GitTag $tag */
			if ( in_array( $tag->getName(), $remoteTags ) ) {
				/**
				 * Assuming that tags never change in the original (source) repository
				 * we can skip tags that already exists in the mirror directory.
				 * @link https://github.com/dnaber-de/Git-Automated-Mirror/issues/5
				 */
				$this->eventEmitter->emit(
					'git.tagMerge.skipExistingTag',
					[
						'gitClient'   => $this->gitClient,
						'tag'         => $tag,
						'mergeBranch' => $mergeBranch,
						'remote'      => $toRemote,
						'tmpBranch'   => $this->tempBranch
					]
				);
				continue;
			}

			$this->mergeBranch( $mergeBranch, $tag, $toRemote );
		}
	}

	/**
	 * »merge« branch into tag
	 *
	 * sequence:
	 *  - checkout the tag
	 *  - create a temporary branch
	 *  - merge the mergeBranch into the temporary branch
	 *  - tag the new commit with the existing tag name
	 *  - push the »updated« tag
	 *  - delete the temporary branch
	 *
	 * @param Type\GitBranch $mergeBranch
	 * @param Type\GitTag    $tag
	 * @param Type\GitRemote $toRemote
	 */
	public function mergeBranch( Type\GitBranch $mergeBranch, Type\GitTag $tag, Type\GitRemote $toRemote ) {

		// delete the temp branch if an earlier process was interrupted
		if ( $this->branchReader->branchExists( $this->tempBranch ) )
			$this->gitClient->branch->delete( $this->tempBranch );
		// create a temporary branch
		// start at the tag commit
		$this->gitClient->checkout->create( $this->tempBranch, $tag );
		$this->gitClient->checkout( $this->tempBranch );
		// now merge the merge-branch …
		$result = `git merge {$mergeBranch}`;
		$this->gitClient->merge( $mergeBranch, NULL, [ 'no-ff' => TRUE ] );
		// update the tag …
		$this->gitClient->tag->create( $tag, NULL, [ 'force' => TRUE ] );
		$this->eventEmitter->emit(
			'git.tagMerge.beforePushTag',
			[
				'gitClient'   => $this->gitClient,
				'tag'         => $tag,
				'mergeBranch' => $mergeBranch,
				'remote'      => $toRemote,
				'tmpBranch'   => $this->tempBranch
			]
		);
		$this->gitClient->push( $toRemote, $tag, [ 'force' => TRUE ] );

		// checkout another ref before deleting the temp branch
		$this->gitClient->checkout( $tag );
		$this->gitClient->branch->delete( $this->tempBranch );
	}

	/**
	 * @param Type\GitRemote $toRemote
	 */
	public function pushTags( Type\GitRemote $toRemote ) {

		$this->gitClient->push( $toRemote, NULL, [ 'tags' => TRUE, 'force' => TRUE ] );
	}
} 