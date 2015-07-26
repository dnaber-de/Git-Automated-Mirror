<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Git;
use GitAutomatedMirror\Git;
use GitAutomatedMirror\Test\Asset;

class BranchReaderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider branchProvider
	 * @param string $name
	 * @param array $expected
	 */
	public function testParseBranchName( $name, Array $expected ) {

		$mockBuilder = new Asset\MockBuilder( $this );
		$gitMock = $mockBuilder->getPhpGitMock( [ 'branch' ] );
		$gitMock->expects( $this->any() )
			->method( 'branch' )
			->willReturn( [] );

		$testee = new Git\BranchReader( $gitMock );
		$this->assertEquals(
			$expected,
			$testee->parseBranchName( $name )
		);
	}

	/**
	 * @dataProvider phpGitBranchProvider
	 * @param array $branches
	 * @param array $expectedBranchSignatures
	 */
	public function testBuildBranches( Array $branches, Array $expectedBranchSignatures ) {

		$mockBuilder = new Asset\MockBuilder( $this );
		$gitMock = $mockBuilder->getPhpGitMock( [ 'branch' ] );
		$gitMock->expects( $this->any() )
			->method( 'branch' )
			->with( [ 'all' => TRUE ] )
			->willReturn( $branches );

		$testee = new Git\BranchReader( $gitMock );
		$branches = $testee->buildBranches();

		// test the names of the branch
		$expectedNames = array_keys( $expectedBranchSignatures );
		$actualNames   = array_keys( $branches );

		/**
		 * @link http://stackoverflow.com/a/28189403/2169046
		 */
		$this->assertEquals(
			$expectedNames,
			$actualNames,
			"Comparing Arrays without caring of the order.",
			$delta = 0.0,
			$maxDepth = 10,
			$canonicalize = true
		);

		foreach ( $expectedBranchSignatures as $branchName => $signatue ) {
			$this->assertEquals(
				$signatue[ 'isLocal' ],
				$branches[ $branchName ]->isLocal(),
				'Assert isLocal()'
			);
			$this->assertEquals(
				$signatue[ 'name' ],
				$branches[ $branchName ]->getName(),
				'Assert getName()'
			);
			$this->assertEquals(
				$signatue[ 'remotes' ],
				$branches[ $branchName ]->getRemotes(),
				'Assert getRemotes()'
			);
		}
	}

	/**
	 * @dataProvider parseRawBranchProvider
	 * @param array $branch
	 * @param array $expected
	 */
	public function testParseRawBranch( Array $branch, Array $expected ) {

		$mockBuilder = new Asset\MockBuilder( $this );
		$gitMock = $mockBuilder->getPhpGitMock( [ 'branch' ] );
		$gitMock->expects( $this->any() )
			->method( 'branch' )
			->willReturn( [] );
		$testee = new Git\BranchReader( $gitMock );

		$branch = $testee->parseRawBranch( $branch );

		$this->assertEquals(
			$expected[ 'name' ],
			$branch->getName(),
			'Comparing branch name.'
		);
		$this->assertEquals(
			$expected[ 'isLocal' ],
			$branch->isLocal(),
			'Comparing branch is-local.'
		);
		$this->assertEquals(
			$expected[ 'remotes' ],
			$branch->getRemotes(),
			'Comparing branch remotes.'
		);
	}

	/**
	 * @dataProvider getCurrentBranchProvider
	 * @param array $branches
	 * @param array $expected
	 */
	public function testGetCurrentBranch( Array $branches, Array $expected ) {

		$mockBuilder = new Asset\MockBuilder( $this );
		$gitMock = $mockBuilder->getPhpGitMock( [ 'branch' ] );
		$gitMock->expects( $this->any() )
			->method( 'branch' )
			->willReturn( $branches );

		$testee = new Git\BranchReader( $gitMock );
		$currentBranch = $testee->getCurrentBranch();

		if ( ! empty( $expected ) ) {
			$this->assertInstanceOf(
				'GitAutomatedMirror\Type\GitBranch',
				$currentBranch
			);
			$this->assertEquals(
				$expected[ 'name' ],
				$currentBranch->getName()
			);
			$this->assertEquals(
				$expected[ 'remotes' ],
				$currentBranch->getRemotes()
			);
		} else {
			$this->assertNull( $currentBranch );
		}
	}

	/**
	 * @dataProvider branchExistsProvider
	 * @param array $branches
	 * @param array $expected
	 */
	public function testBranchExists( Array $branches, Array $expected ) {

		$mockBuilder = new Asset\MockBuilder( $this );
		$gitMock = $mockBuilder->getPhpGitMock( [ 'branch' ] );
		$gitMock->expects( $this->any() )
			->method( 'branch' )
			->with( [ 'all' => TRUE ] )
			->willReturn( $branches );

		$testee = new Git\BranchReader( $gitMock );

		if ( $expected[ 'exists' ] ) {
			$this->assertTrue(
				$testee->branchExists( $expected[ 'test_branch' ] )
			);
		} else {
			$this->assertFalse(
				$testee->branchExists( $expected[ 'test_branch' ] )
			);
		}
	}

	/**
	 * @return array
	 */
	public function branchProvider () {

		$data = [];

		#0:
		$data[] = [
			'remotes/origin/master',
			[
				'name' => 'master',
				'remote' => 'origin'
			]
		];

		#1:
		$data[] = [
			'remotes/mirror/1.1-dev',
			[
				'name' => '1.1-dev',
				'remote' => 'mirror'
			]
		];
		#1:
		$data[] = [
			'master',
			[
				'name' => 'master',
				'remote' => ''
			]
		];

		return $data;
	}

	/**
	 * @see testBuildBranches
	 * @return array
	 */
	public function phpGitBranchProvider() {

		$data = [];

		#0:
		$data[] = [
			#1.parameter $branches
			[
				'master' => [
					'current' => FALSE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				]
			],
			#2. parameter $expectedBranchSignature
			[
				'master' => [
					'isLocal' => TRUE,
					'name'    => 'master',
					'remotes' => [
						'origin' => 'remotes/origin/master'
					]
				]
			]
		];

		#1:
		$data[] = [
			#1.parameter $branches
			[
				'master' => [
					'current' => FALSE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/mirror/1.1-branch' => [
					'current' => FALSE,
					'name'    => 'remotes/mirror/1.1-branch',
					'hash'    => '3357ad4',
					'title'   => 'foo commit'
				],
				'local-dev' => [
					'current' => TRUE,
					'name'    => 'local-dev',
					'hash'    => 'f349dc4',
					'title'   => 'bar commit'
				]
			],
			#2. parameter $expectedBranchSignature
			[
				'master' => [
					'isLocal' => TRUE,
					'name'    => 'master',
					'remotes' => [
						'origin' => 'remotes/origin/master'
					]
				],
				'1.1-branch' => [
					'isLocal' => FALSE,
					'name'    => '1.1-branch',
					'remotes' => [
						'mirror'  => 'remotes/mirror/1.1-branch'
					]
				],
				'local-dev' => [
					'isLocal' => TRUE,
					'name'    => 'local-dev',
					'remotes' => []
				]
			]
		];

		#2:
		$data[] = [
			#1.parameter $branches
			[
				'master' => [
					'current' => FALSE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/mirror/master' => [
					'current' => FALSE,
					'name'    => 'remotes/mirror/master',
					'hash'    => '3357ad4',
					'title'   => 'foo commit'
				]
			],
			#2. parameter $expectedBranchSignature
			[
				'master' => [
					'isLocal' => TRUE,
					'name'    => 'master',
					'remotes' => [
						'origin' => 'remotes/origin/master',
						'mirror' => 'remotes/mirror/master'
					]
				]
			]
		];

		return $data;
	}

	/**
	 * @see testParseRawBranch
	 * @return array
	 */
	public function parseRawBranchProvider() {

		$data = [];

		#0:
		$data[] = [
			#1. Parameter $branch
			[
				'current' => FALSE,
				'name'    => 'master',
				'hash'    => '2485d2f',
				'title'   => 'some commit message'
			],
			#2. Parameter $expected
			[
				'name' => 'master',
				'isLocal' => TRUE,
				'remotes' => []
			]
		];

		#1:
		$data[] = [
			#1. Parameter $branch
			[
				'current' => FALSE,
				'name'    => 'remotes/origin/master',
				'hash'    => '2485d2f',
				'title'   => 'some commit message'
			],
			#2. Parameter $expected
			[
				'name' => 'master',
				'isLocal' => FALSE,
				'remotes' => [
					'origin' => 'remotes/origin/master'
				]
			]
		];

		return $data;
	}

	/**
	 * @see testGetCurrentBranch
	 * @return array
	 */
	public function getCurrentBranchProvider() {

		$data = [];

		#0:
		$data[] = [
			#1.parameter $branches
			[
				'master' => [
					'current' => TRUE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'patch' => [
					'current' => FALSE,
					'name'    => 'patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				],
				'remotes/mirror/patch' => [
					'current' => FALSE,
					'name'    => 'remotes/mirror/patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				]
			],
			#2. parameter $expectedBranchSignature
			[
				'name' => 'master',
				'remotes' => [
					'origin' => 'remotes/origin/master'
				]
			]
		];

		#1:
		$data[] = [
			#1.parameter $branches
			[
				'master' => [
					'current' => FALSE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'patch' => [
					'current' => FALSE,
					'name'    => 'patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				],
				'remotes/mirror/patch' => [
					'current' => FALSE,
					'name'    => 'remotes/mirror/patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				]
			],
			#2. parameter $expectedBranchSignature
			[
				// currently not on any branch
			]
		];

		return $data;
	}

	/**
	 * @see testBranchExists
	 * @return array
	 */
	public function branchExistsProvider() {

		$data = [];

		#0:
		$data[] = [
			[
				'master' => [
					'current' => TRUE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'patch' => [
					'current' => FALSE,
					'name'    => 'patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				],
				'remotes/mirror/patch' => [
					'current' => FALSE,
					'name'    => 'remotes/mirror/patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				]
			],
			[
				'test_branch' => 'master',
				'exists' => TRUE
			]
		];

		#1:
		$data[] = [
			[
				'master' => [
					'current' => TRUE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'patch' => [
					'current' => FALSE,
					'name'    => 'patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				],
				'remotes/mirror/patch' => [
					'current' => FALSE,
					'name'    => 'remotes/mirror/patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				]
			],
			[
				'test_branch' => 'remotes/mirror/patch',
				'exists' => TRUE
			]
		];

		#2:
		$data[] = [
			[
				'master' => [
					'current' => TRUE,
					'name'    => 'master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'remotes/origin/master' => [
					'current' => FALSE,
					'name'    => 'remotes/origin/master',
					'hash'    => '2485d2f',
					'title'   => 'some commit message'
				],
				'patch' => [
					'current' => FALSE,
					'name'    => 'patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				],
				'remotes/mirror/patch' => [
					'current' => FALSE,
					'name'    => 'remotes/mirror/patch',
					'hash'    => 'f84a8c3',
					'title'   => 'some other message'
				]
			],
			[
				'test_branch' => 'missing_branch',
				'exists' => FALSE
			]
		];

		return $data;
	}
}
 