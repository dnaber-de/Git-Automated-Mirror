<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\App;
use GitAutomatedMirror\Test\Unit\Assets;
use GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use Dice;

class GitAutomatedMirrorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @type Assets\RepositoryTestOrganizer
	 */
	private $organizer;

	public function setUp() {

		$tmpDir = dirname( dirname( __DIR__ ) ) . '/tmp';
		$this->organizer = new Assets\RepositoryTestOrganizer( $tmpDir );
		$this->organizer->cleanUp();
		$this->organizer->setUpRepositories();
	}

	/**
	 * @see GitAutomatedMirror::run()
	 */
	public function testRun() {

		$repositories = $this->organizer->getRepositories();
		/**
		 * Assuming a valid script call like
		 * $ php git-automated-mirror.php -d /path/to/repo --remote-source origin --remote-mirror mirror
		 *
		 * the $argv array looks like the following
		 */
		$argv = [
			__FILE__,
			'-d',
			$repositories[ 'process' ][ 'path' ],
			'--remote-source',
			$repositories[ 'source' ][ 'name' ],
			'--remote-mirror',
			$repositories[ 'mirror' ][ 'name' ]
		];

		$diContainer = new Dice\Dice;
		$testee = new App\GitAutomatedMirror(
			$diContainer,
			new Config\DiceConfigurator( $diContainer )
		);

		$testee->run( $argv );

		$this->markTestIncomplete( 'Under construction…' );
	}

	public function tearDown() {

		$this->organizer->cleanUp();
	}
}
 