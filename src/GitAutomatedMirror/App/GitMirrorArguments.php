<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Type;
use GetOptionKit;
use PHPGit;

/**
 * Class GitMirrorArguments
 *
 * provides the given arguments as
 * instances of defined type interfaces
 * (e.g. Type\GitBranch)
 *
 * @package GitAutomatedMirror\App
 */
class GitMirrorArguments {

	/**
	 * @type GetOptionKit\OptionResult;
	 */
	private $optionResults;

	/**
	 * @type PHPGit\Git
	 */
	private $git;

	/**
	 * @param GetOptionKit\OptionResult $optionResults
	 * @param PHPGit\Git                $git
	 */
	public function __construct( GetOptionKit\OptionResult $optionResults, PHPGit\Git $git ) {

		$this->optionResults = $optionResults;
		$this->git = $git;
	}

	/**
	 * @return Type\GitRepository
	 */
	public function getRepository() {

		$directory = $this->optionResults[ 'dir' ]->value;
		$directory = realpath( $directory );
		return new Type\GitRepository( $directory );
	}

	/**
	 * @return Type\GitRemote
	 */
	public function getRemoteSource() {

		$remote = $this->optionResults[ 'remote-source' ]->value;
		$remoteInfo = $this->git->remote();

		return new Type\GitRemote(
			$remote,
			$remoteInfo[ $remote ][ 'push' ],
			$remoteInfo[ $remote ][ 'fetch' ]
		);
	}

	/**
	 * @return Type\GitRemote
	 */
	public function getRemoteMirror() {

		$remote = $this->optionResults[ 'remote-mirror' ]->value;
		$remoteInfo = $this->git->remote();

		return new Type\GitRemote(
			$remote,
			$remoteInfo[ $remote ][ 'push' ],
			$remoteInfo[ $remote ][ 'fetch' ]
		);
	}

	/**
	 * @return Type\GitBranch | NULL
	 */
	public function getMergeBranch() {

		if ( ! isset( $this->optionResults[ 'merge-branch' ] ) )
			return NULL;

		$branchName = $this->optionResults[ 'merge-branch' ];
		if ( empty( $branchName ) )
			return NULL;

		$mergeBranch = new Type\GitBranch( $branchName );

		return $mergeBranch;
	}
} 