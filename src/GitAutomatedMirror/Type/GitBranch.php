<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;
use GitAutomatedMirror\Common;

class GitBranch implements BranchInterface, Common\StringConvertible {

	/**
	 * @type string
	 */
	private $name = '';

	/**
	 * @type bool
	 */
	private $isLocal = FALSE;

	/**
	 * @type array
	 */
	private $remotes = [];

	/**
	 * @param string $name
	 * @param bool $isLocal
	 * @param array $remotes
	 */
	public function __construct( $name, $isLocal = FALSE, Array $remotes = NULL ) {

		$this->name     = $name;
		$this->isLocal = (bool) $isLocal;
		if ( $remotes )
			$this->remotes = $remotes;
	}

	/**
	 * @return bool
	 */
	public function isLocal() {

		return $this->isLocal;
	}

	/**
	 * @return array
	 */
	public function getRemotes() {

		return $this->remotes;
	}

	/**
	 * @return string
	 */
	public function getName() {

		return $this->name;
	}

	/**
	 * @return string
	 */
	public function __toString() {

		return $this->name;
	}

	/**
	 * @param bool $isLocal
	 * @return void
	 */
	public function setIsLocal( $isLocal ) {

		$this->isLocal = (bool) $isLocal;
	}

	/**
	 * @param string $remote
	 * @param string $fullRef (Optional)
	 * @return void
	 */
	public function pushRemote( $remote, $fullRef = '' ) {

		if ( isset( $this->remotes[ $remote ] ) )
			return;

		if ( empty( $fullRef ) )
			$fullRef = $remote;

		$this->remotes[ $remote ] = $fullRef;
	}

	/**
	 * @param string $remote
	 * @return void
	 */
	public function popRemote( $remote ) {

		if ( ! isset( $this->remotes[ $remote ] ) )
			return;

		unset( $this->remotes[ $remote ] );
	}

}