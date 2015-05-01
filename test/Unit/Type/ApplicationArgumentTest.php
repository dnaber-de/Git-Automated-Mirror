<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Type;
use GitAutomatedMirror\Type;

class ApplicationArgumentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider argumentProvider
	 * @param array $data
	 * @param array $expected
	 */
	public function testGetters( Array $data, Array $expected ) {

		$builder = new \ReflectionClass( 'GitAutomatedMirror\Type\ApplicationArgument' );
		/* @type Type\ApplicationArgument $testee */
		$testee = $builder->newInstanceArgs( $data );

		$this->assertEquals(
			$expected[ 'name' ],
			$testee->getName()
		);
		$this->assertEquals(
			$expected[ 'type' ],
			$testee->getType()
		);
		$this->assertEquals(
			$expected[ 'isRequired' ],
			$testee->isRequired()
		);
		$this->assertEquals(
			$expected[ 'shortName' ],
			$testee->getShortName()
		);
		$this->assertEquals(
			$expected[ 'description' ],
			$testee->getDescription()
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
	public function argumentProvider() {

		$data = [];

		#0:
		$data[] = [
			[
				'name'        => 'help',
				'type'        => 'string',
				'isRequired'  => FALSE,
				'shortName'   => 'h',
				'description' => 'Print help message.',
			],
			[
				'name'        => 'help',
				'type'        => 'string',
				'isRequired'  => FALSE,
				'shortName'   => 'h',
				'description' => 'Print help message.',
			],
		];

		#1:
		$data[] = [
			[
				'name'        => 'directory',
				'type'        => 'string',
				'isRequired'  => 1,
				'shortName'   => NULL,
				'description' => NULL,
			],
			[
				'name'        => 'directory',
				'type'        => 'string',
				'isRequired'  => TRUE,
				'shortName'   => '',
				'description' => '',
			],
		];

		return $data;
	}
}
 