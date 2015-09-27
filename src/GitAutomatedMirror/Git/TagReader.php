<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;
use PHPGit;

class TagReader {

	/**
	 * @type PHPGit\Git
	 */
	private $gitClient;

	/**
	 * @param PHPGit\Git $gitClient
	 */
	public function __construct( PHPGit\Git $gitClient ) {

		$this->gitClient = $gitClient;
	}

	/**
	 * @return array
	 */
	public function getTags() {

		$tags = $this->gitClient->tag();
		$tagObjects = [];
		foreach ( $tags as $tag )
			$tagObjects[] = new Type\GitTag( $tag );

		return $tagObjects;
	}

	/**
	 * @param Type\GitRepository $repository
	 * @param Type\GitRemote $remote
	 * @return array
	 */
	public function getRemoteTags( Type\GitRepository $repository, Type\GitRemote $remote) {

		chdir( $repository->getDirectory() );
		$repoName = escapeshellarg( $remote );
		$result   = `git ls-remote --tags {$repoName}`;

		return $this->parseRawRemoteTags( $result );
	}

	/**
	 * @param string $rawTags
	 * @return array
	 */
	public function parseRawRemoteTags( $rawTags ) {

		$rawTags = explode( "\n", $rawTags );

		$tags = array_map(
			function( $rawTag ) {
				$parts = preg_split( '~\s+~', $rawTag );
				$tag = array_pop( $parts );
				$tag = str_replace( 'refs/tags/', '', $tag );

				return $tag;
			},
			$rawTags
		);

		return $tags;
	}
} 