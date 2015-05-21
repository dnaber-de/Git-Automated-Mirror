<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\App;
use GitAutomatedMirror\Test\Asset;
use GitAutomatedMirror\App;
use GitAutomatedMirror\Config;
use Dice;

class GitAutomatedMirrorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @type Asset\RepositoryTestOrganizer
	 */
	private $organizer;

	public function setUp() {

		$tmpDir = dirname( dirname( __DIR__ ) ) . '/tmp';
		$this->organizer = new Asset\RepositoryTestOrganizer( $tmpDir );
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
		$this->compareRepositories();
		$this->organizer->updateSourceRepo();

		$diContainer = new Dice\Dice;
		$testee = new App\GitAutomatedMirror(
			$diContainer,
			new Config\DiceConfigurator( $diContainer )
		);
		$testee->run( $argv );
		$this->compareRepositories();

	}

	private function compareRepositories() {

		$repositories = $this->organizer->getRepositories();
		// test the existence of all heads
		// note: mirror is a bare repository
		$mirrorHeadsDir = $repositories[ 'mirror' ][ 'path' ] . '/refs/heads';
		$sourceHeadsDir = $repositories[ 'source' ][ 'path' ] . '/.git/refs/heads';
		$mirrorTagsDir  = $repositories[ 'mirror' ][ 'path' ] . '/refs/tags';
		$sourceTagsDir  = $repositories[ 'source' ][ 'path' ] . '/.git/refs/tags';

		$this->assertTrue(
			is_dir( $mirrorHeadsDir ),
			'Directory mirror/refs/heads does not exist.'
		);

		$dirTools    = new Asset\DirectoryTools( $mirrorHeadsDir );
		$mirrorHeadsFiles = $dirTools->getFiles();

		$dirTools->cd( $sourceHeadsDir );
		$sourceHeadsFiles = $dirTools->getFiles();

		$dirTools->cd( $mirrorTagsDir );
		$mirrorTagsFiles = $dirTools->getFiles();

		$dirTools->cd( $sourceTagsDir );
		$sourceTagsFiles = $dirTools->getFiles();

		/**
		 * each HEAD in the source must appear in the mirror
		 * and each file should point to the same object
		 */
		foreach ( $sourceHeadsFiles as $fileName => $filePath ) {
			$this->assertArrayHasKey(
				$fileName,
				$mirrorHeadsFiles,
				"HEAD $fileName does not exist in mirror repo."
			);

			$this->assertFileEquals(
				$filePath,
				$mirrorHeadsFiles[ $fileName ]
			);
		}

		/**
		 * each Tag in the source must appear in the mirror
		 * and each file should point to the same object
		 */
		foreach ( $sourceTagsFiles as $fileName => $filePath ) {
			$this->assertArrayHasKey(
				$fileName,
				$mirrorTagsFiles,
				"Tag $fileName does not exist in mirror repo."
			);

			$this->assertFileEquals(
				$filePath,
				$mirrorTagsFiles[ $fileName ]
			);
		}
	}

	public function tearDown() {

		$this->organizer->cleanUp();
	}
}
 