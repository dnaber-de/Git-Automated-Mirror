<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Config;
use GitAutomatedMirror\Test\Unit\Assets;
use GitAutomatedMirror\Config;

class ArgumentsTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getCombinedNameProvider
	 * @param array $data
	 * @param string $expected
	 */
	public function testGetCombinedName( Array $data, $expected ) {

		$mockBuilder = new Assets\MockBuilder( $this );
		$attributeBuilder = $mockBuilder->getCommonApplicationArgumentBuilderMock();
		$optionCollection = $mockBuilder->getOptionCollectionMock();

		$attribute = $mockBuilder->getTypeApplicationArgumentMock( $data );
		$testee = new Config\Arguments( $optionCollection, $attributeBuilder );

		$this->assertEquals(
			$expected,
			$testee->getCombinedName( $attribute )
		);
	}

	/**
	 * @see testGetCombinedName
	 * @return array
	 */
	public function getCombinedNameProvider() {

		$data = [];

		#0:
		$data[] = [
			[
				'name'       => 'help',
				'shortName'  => 'h',
				'isRequired' => FALSE
			],
			'h|help'
		];
		#0:
		$data[] = [
			[
				'name'       => 'directory',
				'shortName'  => '',
				'isRequired' => TRUE
			],
			'directory:'
		];

		return $data;
	}
}
 