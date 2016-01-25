<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;

use
	GitAutomatedMirror\Test;

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

	/**
	 * @return string
	 */
	public function getBaseDir() {

		return $this->baseDir;
	}

	/**
	 * @return string
	 */
	public function getTmpDir() {

		if ( defined( 'GitAutomatedMirror\Test\TMP_DIR' ) && Test\TMP_DIR )
			$tmp_dir = Test\TMP_DIR;
		else
			$tmp_dir = sys_get_temp_dir() . '/gam';

		if ( ! is_dir( $tmp_dir ) )
			mkdir( $tmp_dir );

		return $tmp_dir;
	}
} 