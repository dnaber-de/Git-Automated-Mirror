<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;

class DirectoryNavigator {

	/**
	 * @type string
	 */
	private $baseDir;

	public function __construct() {

		$this->baseDir = dirname(
			dirname( # /test
				__DIR__ # /Assets
			)
		);
	}

	public function getBaseDir() {

		return $this->baseDir;
	}
} 