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
} 