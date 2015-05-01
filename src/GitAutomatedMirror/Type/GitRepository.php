<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;
use GitAutomatedMirror\Common;

class GitRepository implements VcsRepositoryInterface, Common\StringConvertible {

	/**
	 * @type string
	 */
	private $directory = '';

	/**
	 * @param $directory
	 */
	public function __construct( $directory ) {

		$this->directory = (string) $directory;
	}

	/**
	 * returns the absolute path to the
	 * repository
	 *
	 * @return string
	 */
	public function getDirectory() {

		return $this->directory;
	}

	/**
	 * @return string
	 */
	public function __toString() {

		return $this->directory;
	}

} 