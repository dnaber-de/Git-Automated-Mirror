<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\App;
use GitAutomatedMirror\Type;
use GetOptionKit;

class GitMirrorArguments {

	/**
	 * @type GetOptionKit\OptionResult;
	 */
	private $optionResults;

	/**
	 * @param GetOptionKit\OptionResult $optionResults
	 */
	public function __construct( GetOptionKit\OptionResult $optionResults ) {

		$this->optionResults = $optionResults;
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
		return new Type\GitRemote( $remote, '' );
	}

	/**
	 * @return Type\GitRemote
	 */
	public function getRemoteMirror() {

		$remote = $this->optionResults[ 'remote-mirror' ]->value;
		return new Type\GitRemote( $remote, '' );
	}
} 