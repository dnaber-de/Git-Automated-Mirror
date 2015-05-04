<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Config;
use GitAutomatedMirror\App\GitAutomatedMirror;
use GitAutomatedMirror\Test\Unit\Assets;
use GitAutomatedMirror\Config;

class ArgumentsTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @see GitAutomatedMirror\Config\Arguments::registerDefaultArguments()
	 * @see GitAutomatedMirror\Config\Arguments::createArguments()
	 */
	public function testRegisterDefaultArguments() {

		$mockBuilder = new Assets\MockBuilder( $this );
		$attributeBuilder = $mockBuilder->getCommonApplicationArgumentBuilderMock();
		$attributeBuilder->expects( $this->exactly( 4 ) )
			->method( 'buildArgument' )
			->willReturnCallback(
				function() {
					$reflector = new \ReflectionClass( 'GitAutomatedMirror\Type\ApplicationArgument' );
					return $reflector->newInstanceArgs( func_get_args() );
				}
			);
		$optionCollection = $mockBuilder->getOptionCollectionMock();

		/**
		 * catch the expected methods calls
		 */
		$option_mock = $mockBuilder->getOptionKitOptionMock();
		$option_mock->expects( $this->atLeast( 1 ) )
			->method( 'isa' )
			->willReturn( $option_mock );
		$option_mock->expects( $this->atLeast( 1 ) )
			->method( 'desc' )
			->willReturn( $option_mock );
		$optionCollection->expects( $this->exactly( 4 ) )
			->method( 'add' )
			->willReturn( $option_mock );

		$testee = new Config\Arguments( $optionCollection, $attributeBuilder );
		$testee->registerDefaultArguments();
	}

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
 