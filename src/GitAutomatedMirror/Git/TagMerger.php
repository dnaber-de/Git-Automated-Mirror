<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;
use PHPGit;
use League\Event;

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
	 * @param TagReader     $tagReader
	 * @param PHPGit\Git    $gitClient
	 * @param Event\Emitter $eventEmitter
	 */
	public function __construct(
		TagReader $tagReader,
		PHPGit\Git $gitClient,
		Event\Emitter $eventEmitter
	) {

		$this->tagReader    = $tagReader;
		$this->gitClient    = $gitClient;
		$this->eventEmitter = $eventEmitter;
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
	 */
	public function mergeBranchIntoTags(
		Type\GitBranch $mergeBranch,
		Type\GitRemote $toRemote
	) {

		foreach ( $this->tagReader->getTags() as $tag ) {
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

		$tmpBranch = 'gamTempBranch';
		// create a temporary branch
		// start at the tag commit
		$this->gitClient->checkout->create( $tmpBranch, $tag );
		$this->gitClient->checkout( $tmpBranch );
		// now merge the merge-branch …
		$this->gitClient->merge( $mergeBranch );
		// update the tag …
		$this->gitClient->tag->create( $tag, NULL, [ 'force' => TRUE ] );
		$this->eventEmitter->emit(
			'git.tagMerge.beforePushTag',
			[
				'gitClient'   => $this->gitClient,
				'tag'         => $tag,
				'mergeBranch' => $mergeBranch,
				'remote'      => $toRemote,
				'tmpBranch'   => $tmpBranch
			]
		);
		$this->gitClient->push( $toRemote, $tag, [ 'force' => TRUE ] );

		// checkout another ref before deleting the temp branch
		$this->gitClient->checkout( $tag );
		$this->gitClient->branch->delete( $tmpBranch );
	}

	/**
	 * @param Type\GitRemote $toRemote
	 */
	public function pushTags( Type\GitRemote $toRemote ) {

		$this->gitClient->push( $toRemote, NULL, [ 'tags' => TRUE ] );
	}
} 