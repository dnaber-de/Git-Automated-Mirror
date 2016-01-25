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

	/**
	 * @type Asset\DirectoryNavigator
	 */
	private $directory_navigator;

	/**
	 * @type array
	 */
	private $repositories = [];

	/**
	 * @type Asset\GitStdOutParser
	 */
	private $gitParser;

	/**
	 * runs before each test
	 */
	public function setUp() {

		if ( ! $this->gitParser )
			$this->gitParser = new Asset\GitStdOutParser;
		if ( ! $this->directory_navigator )
			$this->directory_navigator = new Asset\DirectoryNavigator;

		$tmpDir = $this->directory_navigator->getTmpDir();
		$this->organizer = new Asset\RepositoryTestOrganizer( $tmpDir );
		$this->organizer->cleanUp();
		$this->repositories = $this->organizer->getRepositories();
	}

	/**
	 * testing a call like
	 * $ gitAutomatedMirror -d /path/to/repo --remote-source origin --remote-mirror mirror
	 *
	 * @see GitAutomatedMirror::run()
	 */
	public function testRun() {

		$this->organizer->setUpRepositories();
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
		$this->compareRepositoriesForTestRun();
		$this->organizer->updateSourceRepo();

		$GLOBALS[ 'debug' ] = TRUE;
		$testee->run( $argv );
		$this->compareRepositoriesForTestRun();

	}

	private function compareRepositoriesForTestRun() {

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

	/**
	 * testing a call like
	 * $ gitAutomatedMirror -d /path/to/repo --remote-source origin --remote-mirror mirror --merge-branch mergeBranch
	 *
	 * @see GitAutomatedMirror::run()
	 */
	public function testRunWithMergeBranch() {

		$this->organizer->setUpRepositoriesForTagTest();
		$repositories = $this->organizer->getRepositories();

		$argv = [
			__FILE__,
			'-d',
			$repositories[ 'process' ][ 'path' ],
			'--remote-source',
			$repositories[ 'source' ][ 'name' ],
			'--remote-mirror',
			$repositories[ 'mirror' ][ 'name' ],
			'--merge-branch',
			'mergeBranch'
		];

		$diContainer = new Dice\Dice;
		$testee = new App\GitAutomatedMirror(
			$diContainer,
			new Config\DiceConfigurator( $diContainer )
		);
		$GLOBALS[ 'debug' ] = FALSE;
		$testee->init();
		$testee->run( $argv );
		$this->compareRepositoriesForTestRun();
		$this->organizer->updateSourceRepo();

		$GLOBALS[ 'debug' ] = TRUE;
		$testee->run( $argv );
		$this->compareRepositoriesForTestRun();
		$this->checkMergedTags();
	}

	/**
	 * the tags in the mirror repo
	 * should all have a merge commit with the tmpBranch as last commit
	 */
	private function checkMergedTags() {

		chdir( $this->repositories[ 'mirror' ][ 'path' ] );
		$rawTags = `git tag`;
		$tags = $this->gitParser->parseTags( $rawTags );
		foreach ( $tags as $tag ) {
			$tag = escapeshellarg( $tag );
			$rawLog = `git log $tag -n 1 --oneline`;
			$log = $this->gitParser->parseLogPrettyOneLine( $rawLog );

			$this->assertEquals(
				"Merge branch 'mergeBranch' into gamTempBranch",
				current( $log ),
				"Tag $tag failed to merge mergeBranch"
			);
		}
	}

	public function tearDown() {

		$this->organizer->cleanUp();
	}
}
 