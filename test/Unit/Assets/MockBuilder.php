<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Assets;
use GitAutomatedMirror\Type;
use GetOptionKit;

class MockBuilder {

	/**
	 * @type \PHPUnit_Framework_TestCase
	 */
	private $testCase;

	public function __construct( \PHPUnit_Framework_TestCase $testCase ) {

		$this->testCase = $testCase;
	}

	/**
	 * @param array $attributes
	 * @type Type\ApplicationArgument
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getTypeApplicationArgumentMock( Array $attributes = NULL ) {

		$class = 'GitAutomatedMirror\Type\ApplicationArgument';
		$mock = $this->testCase->getMockBuilder( $class )
			->disableOriginalConstructor()
			->getMock();

		if ( ! $attributes )
			return $mock;

		$methods = [
			'type'        => 'getType',
			'name'        => 'getName',
			'shortName'   => 'getShortName',
			'isRequired'  => 'isRequired',
			'description' => 'getDescription'
		];

		foreach ( $methods as $name => $method ) {
			if ( ! isset( $attributes[ $name ] ) )
				continue;
			$mock->expects( $this->testCase->any() )
				->method( $method )
				->willReturn( $attributes[ $name ] );
		}

		return $mock;
	}

	/**
	 * @type GetOptionKit\OptionCollection
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getOptionCollectionMock() {

		$class = 'GetOptionKit\OptionCollection';

		return $this->getMockWithoutConstructor( $class );
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getCommonApplicationArgumentBuilderMock() {

		$class = 'GitAutomatedMirror\Common\ApplicationArgumentBuilder';

		return $this->getMockWithoutConstructor( $class );
	}

	/**
	 * @type GetOptionKit\Option
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getOptionKitOptionMock() {

		$class = 'GetOptionKit\Option';

		return $this->getMockWithoutConstructor( $class );
	}

	/**
	 * @param string $class
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getMockWithoutConstructor( $class ) {

		$mock = $this->testCase->getMockBuilder( $class )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}
} 