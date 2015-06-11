<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Git;
use GitAutomatedMirror\Test\Asset;
use GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;
use PHPGit;

class TagMergerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @type Asset\RepositoryTestOrganizer
	 */
	private $organizer;

	/**
	 * @type Array
	 */
	private $repositories;

	/**
	 * @type Asset\MockBuilder
	 */
	private $mockBuilder;

	/**
	 * @type Asset\GitStdOutParser
	 */
	private $gitStdOutParser;

	/**
	 * runs before each test
	 */
	public function setUp() {

		$this->mockBuilder = new Asset\MockBuilder( $this );
		$this->gitStdOutParser = new Asset\GitStdOutParser;

		$tmpDir = dirname( dirname( __DIR__ ) ) . '/tmp';
		$this->organizer = new Asset\RepositoryTestOrganizer( $tmpDir );
		$this->organizer->cleanUp();
		$this->repositories = $this->organizer->getRepositories();
	}

	/**
	 * runs after each test
	 */
	public function tearDown() {

		$this->organizer->cleanUp();
	}

	/**
	 * @see TagMerger::fetchTags()
	 */
	public function testFetchTags() {

		// preparing the setup
		$this->organizer->setUpRepositories();
		$this->organizer->createDefaultTags(
			$this->repositories[ 'source' ]
		);

		$phpGitMock       = $this->mockBuilder->getPhpGitMock();
		$tagReaderMock    = $this->mockBuilder->getTagReaderMock();
		$eventEmitterMock = $this->mockBuilder->getEventEmitterMock();
		$testee = new Git\TagMerger( $tagReaderMock, $phpGitMock, $eventEmitterMock );

		// fetch the tags in the working repo from the source remote
		$repository = new Type\GitRepository( $this->repositories[ 'process' ][ 'path' ] );
		$fromRemote = new Type\GitRemote(
			$this->repositories[ 'source' ][ 'name' ],
			$this->repositories[ 'source' ][ 'path' ]
		);

		$testee->fetchTags( $repository, $fromRemote );

		// now the following tags should be available
		chdir( $this->repositories[ 'process' ][ 'path' ] );
		$rawTags = `git tag`;
		$tags = $this->gitStdOutParser->parseTags( $rawTags );

		$this->assertEquals(
			[
				'v1.0.0',
				'v1.0.1'
			],
			$tags
		);
	}

	/**
	 * @see TagMerger::mergeBranch()
	 */
	public function testMergeBranch() {

		$this->organizer->setUpRepositoriesForTagTest();
		// preparing the setup
		$this->organizer->createDefaultTags(
			$this->repositories[ 'source' ]
		);

		// fetch the tags from the source repo
		$sourceRemote = escapeshellarg( $this->repositories[ 'source' ][ 'name' ] );
		chdir( $this->repositories[ 'process' ][ 'path' ] );
		`git fetch --tags $sourceRemote`;

		// setup the testee and dependencies
		$phpGit = new PHPGit\Git;
		$tagReaderMock = $this->mockBuilder->getTagReaderMock();
		$eventEmitterMock = $this->mockBuilder->getEventEmitterMock( [ 'emit' ] );
		$testee = new Git\TagMerger( $tagReaderMock, $phpGit, $eventEmitterMock );

		$toRemote = new Type\GitRemote(
			$this->repositories[ 'mirror' ][ 'name' ],
			$this->repositories[ 'mirror' ][ 'path' ]
		);
		$mergeBranch = new Type\GitBranch(
			'mergeBranch',
			TRUE
		);
		$tag = new Type\GitTag( 'v1.0.1' );
		$eventEmitterMock->expects( $this->exactly( 1 ) )
			->method( 'emit' )
			->with(
				'git.tagMerge.beforePushTag',
				[
					'gitClient'   => $phpGit,
					'tag'         => $tag,
					'mergeBranch' => $mergeBranch,
					'remote'      => $toRemote,
					'tmpBranch'   => 'gamTempBranch'
				]
			);
		$testee->mergeBranch( $mergeBranch, $tag, $toRemote );

		// now check the state of the mirror repository
		// last commit message should be "Merge branch 'mergeBranch' into gamTempBranch"
		chdir( $this->repositories[ 'mirror' ][ 'path' ] );
		$rawLog = `git log v1.0.1 -n 1 --oneline`;
		$log = $this->gitStdOutParser->parseLogPrettyOneLine( $rawLog );
		$this->assertEquals(
			"Merge branch 'mergeBranch' into gamTempBranch",
			current( $log )
		);

		// check that the temporary branch was deleted
		chdir( $this->repositories[ 'process' ][ 'path' ] );
		$rawBranches = `git branch -a`;
		$branches = $this->gitStdOutParser->parseBranches( $rawBranches );

		$this->assertFalse(
			in_array( 'gamTempBranch', $branches ),
			'Temp branch "gamTempBranch" still exists in process repository.'
		);
	}
}
 