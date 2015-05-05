<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Type;
use GitAutomatedMirror\Type;

class GitBranchTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider branchProvider
	 * @param array $data
	 * @param array $expected
	 */
	public function testGetters( Array $data, Array $expected ) {

		$builder = new \ReflectionClass( 'GitAutomatedMirror\Type\GitBranch' );
		/* @type Type\GitBranch $testee */
		$testee = $builder->newInstanceArgs( $data );

		$this->assertEquals(
			$expected[ 'name' ],
			$testee->getName()
		);
		$this->assertEquals(
			$expected[ 'isLocal' ],
			$testee->isLocal()
		);
		$this->assertEquals(
			$expected[ 'remotes' ],
			$testee->getRemotes()
		);

		$this->assertEquals(
			$expected[ 'name' ],
			(string) $testee
		);
	}

	public function testSetters() {

		$testee = new Type\GitBranch( 'master', TRUE, [ 'origin' ] );

		$this->assertEquals(
			'master',
			$testee->getName()
		);

		$this->assertTrue( $testee->isLocal() );
		$testee->setIsLocal( TRUE );
		$this->assertTrue( $testee->isLocal() );
		$testee->setIsLocal( FALSE );
		$this->assertFalse( $testee->isLocal() );

		$this->assertEquals(
			[ 'origin' ],
			$testee->getRemotes()
		);

		$testee->popRemote( 'origin' );
		$this->assertEquals(
			[],
			$testee->getRemotes()
		);

		$testee->pushRemote( 'mirror' );
		$testee->pushRemote( 'origin' );
		$this->assertEquals(
			[ 'mirror', 'origin' ],
			array_values( $testee->getRemotes() )
		);
	}

	/**
	 * @see testGetters
	 * @return array
	 */
	public function branchProvider() {

		$data = [];

		#0:
		$data[] = [
			[
				'name'    => 'master',
				'isLocal' => TRUE,
				'remotes' => [
					'origin/master',
					'clone/master'
				]
			],
			[
				'name'    => 'master',
				'isLocal' => TRUE,
				'remotes' => [
					'origin/master',
					'clone/master'
				]
			]
		];

		#1:
		$data[] = [
			[
				'name'    => 'master',
				'isLocal' => FALSE,
				'remotes' => NULL
			],
			[
				'name'    => 'master',
				'isLocal' => FALSE,
				'remotes' => []
			]
		];

		return $data;
	}
}
 