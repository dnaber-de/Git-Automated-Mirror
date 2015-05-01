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
	private $is_local = FALSE;

	/**
	 * @type array
	 */
	private $remotes = [];

	/**
	 * @param string $name
	 * @param bool $is_local
	 * @param array $remotes
	 */
	public function __construct( $name, $is_local, Array $remotes = NULL ) {

		$this->name     = $name;
		$this->is_local = (bool) $is_local;
		if ( $remotes )
			$this->remotes = $remotes;
	}

	/**
	 * @return bool
	 */
	public function isLocal() {

		return $this->is_local;
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
} 