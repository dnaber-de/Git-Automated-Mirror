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
		$GLOBALS[ 'debug' ] = FALSE;
		$testee->init();
		$testee->run( $argv );
		$this->compareRepositories();
		$this->organizer->updateSourceRepo();

		$GLOBALS[ 'debug' ] = TRUE;
		$testee->run( $argv );
		$this->compareRepositories();

	}

	private function compareRepositories() {

		$gitParser = new Asset\GitStdOutParser;
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
		 * compare branches
		 */
		chdir( $repositories[ 'source' ][ 'path' ] );
		$rawSourceBranches = `git branch -a`;
		$sourceBranches = $gitParser->parseBranches( $rawSourceBranches );

		chdir( $repositories[ 'mirror' ][ 'path' ] );
		$rawMirrorBranches = `git branch -a`;
		$mirrorBranches = $gitParser->parseBranches( $rawMirrorBranches );

		$this->assertEquals(
			$sourceBranches,
			$mirrorBranches,
			"Branches are not matching",
			0.0,
			10,
			TRUE //canonicalize: ignore order of elements in Arrays
		);

		/**
		 * compare tags
		 */
		chdir( $repositories[ 'source' ][ 'path' ] );
		$rawSourceTags = `git tag`;
		$sourceTags = $gitParser->parseTags( $rawSourceTags );

		chdir( $repositories[ 'mirror' ][ 'path' ] );
		$rawMirrorTags = `git tag`;
		$mirrorTags = $gitParser->parseTags( $rawMirrorTags );

		$this->assertEquals(
			$sourceTags,
			$mirrorTags,
			"Tags are not matching",
			0.0,
			10,
			TRUE //canonicalize: ignore order of elements in Arrays
		);
	}

	public function tearDown() {

		$this->organizer->cleanUp();
	}
}
 