<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Type;
use GitAutomatedMirror\Type;

class GitRepositoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider repositoryProvider
	 * @param array $data
	 * @param array $expected
	 */
	public function testGetters( Array $data, Array $expected ) {

		$builder = new \ReflectionClass( 'GitAutomatedMirror\Type\GitRepository' );
		/* @type Type\GitRepository $testee */
		$testee = $builder->newInstanceArgs( $data );

		$this->assertEquals(
			$expected[ 'directory' ],
			$testee->getDirectory()
		);

		$this->assertEquals(
			$expected[ 'directory' ],
			(string) $testee
		);
	}

	/**
	 * @return array
	 */
	public function repositoryProvider() {

		$data = [];

		#0:
		$data[] = [
			[
				'directory' => '/var/www/my-repo'
			],
			[
				'directory' => '/var/www/my-repo'
			]
		];

		#1:
		$data[] = [
			[
				'directory' => NULL
			],
			[
				'directory' => ''
			]
		];

		return $data;
	}
}
 