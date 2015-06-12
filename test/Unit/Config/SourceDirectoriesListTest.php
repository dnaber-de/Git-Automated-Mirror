<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Config;
use GitAutomatedMirror\Config;
use GitAutomatedMirror\Test\Asset;

class SourceDirectoriesListTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @type Asset\DirectoryNavigator
	 */
	private $dirNavigator;

	public function setUp() {

		$this->dirNavigator = new Asset\DirectoryNavigator;
	}

	public function testDependencyDirectoriesExists() {

		$testee = new Config\SourceDirectoriesList( $this->dirNavigator->getBaseDir() );
		$directories = $testee->getDependencyDirectories();
		foreach ( $directories as $dir ) {
			$this->assertFileExists( $dir );
		}
	}
}
 