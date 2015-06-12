<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Config;

class SourceDirectoriesList {

	/**
	 * @type string
	 */
	private $baseDir;

	/**
	 * @param string $baseDir
	 */
	public function __construct( $baseDir ) {

		$this->baseDir = rtrim( $baseDir, '\\/' );
	}

	/**
	 * @return array
	 */
	public function getDependencyDirectories() {

		$dependencies = [
			'corneltek/getoptionkit/src',
			'dnaber/requisite/src',
			'kzykhys/git/src',
			'league/event/src',
			'symfony/options-resolver',
			'symfony/process',
			'tombzombie/dice',
		];

		foreach ( $dependencies as &$path )
			$path = $this->baseDir . "/vendor/$path";

		return $dependencies;
	}

	public function getSourceDirectory() {

		return $this->baseDir . '/src';
	}

	public function getEntryFile() {

		return $this->baseDir . '/bin/git-automated-mirror.php';
	}
} 