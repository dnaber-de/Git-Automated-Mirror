<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Build;
use GitAutomatedMirror\Test\Asset;
use GitAutomatedMirror\Build;
use GitAutomatedMirror\Config;

class PharBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @type Asset\DirectoryNavigator
	 */
	private $dirNavigator;

	public function setUp() {

		$this->dirNavigator = new Asset\DirectoryNavigator;
	}

	public function testGetDirectoryIterator() {

		$phar = new \Phar( 'test.phar' );
		$dirListMock = $this->getMockBuilder( 'GitAutomatedMirror\Config\SourceDirectoriesList' )
			->disableOriginalConstructor()
			->getMock();
		$testee = new Build\PharBuilder( $phar, $dirListMock );

		$testDir = $this->dirNavigator->getBaseDir() . "/vendor/tombzombie/dice";
		$iterator = $testee->getDirectoryIterator( $testDir, '~.+\.php$~' );

		$this->markTestIncomplete( 'Under construction' );
	}
}
 