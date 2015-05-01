<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Type;
use GitAutomatedMirror\Type;

class GitRemoteTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider remoteProvider
	 * @param array $data
	 * @param array $expected
	 */
	public function testGetters( Array $data, Array $expected ) {

		$builder = new \ReflectionClass( 'GitAutomatedMirror\Type\GitRemote' );
		/* @type Type\GitRemote $testee */
		$testee = $builder->newInstanceArgs( $data );

		$this->assertEquals(
			$expected[ 'name' ],
			$testee->getName()
		);
		$this->assertEquals(
			$expected[ 'upstreamUri' ],
			$testee->getUpstreamUri()
		);
		$this->assertEquals(
			$expected[ 'downstreamUri' ],
			$testee->getDownstreamUri()
		);

		$this->assertEquals(
			$expected[ 'name' ],
			(string) $testee
		);
	}

	/**
	 * @see testGetters
	 * @return array
	 */
	public function remoteProvider() {

		$data = [];

		#0:
		$data[] = [
			[
				'name'          => 'origin',
				'upstreamUri'   => 'git@github.com:name/repo.git',
				'downstreamUri' => 'git@mirror.github.com:name/repo.git',
			],
			[
				'name'          => 'origin',
				'upstreamUri'   => 'git@github.com:name/repo.git',
				'downstreamUri' => 'git@mirror.github.com:name/repo.git',
			]
		];

		#1:
		$data[] = [
			[
				'name'          => 'clone',
				'upstreamUri'   => 'git@github.com:name/other-repo.git',
				'downstreamUri' => NULL,
			],
			[
				'name'          => 'clone',
				'upstreamUri'   => 'git@github.com:name/other-repo.git',
				'downstreamUri' => 'git@github.com:name/other-repo.git',
			]
		];

		#2:
		$data[] = [
			[
				'name'          => 'local-copy',
				'upstreamUri'   => '../local-copy',
				'downstreamUri' => NULL,
			],
			[
				'name'          => 'local-copy',
				'upstreamUri'   => '../local-copy',
				'downstreamUri' => '../local-copy',
			]
		];

		return $data;
	}
}
 