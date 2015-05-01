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
	 * @return Type\ApplicationArgument
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
	 * @return GetOptionKit\OptionCollection
	 */
	public function getOptionCollectionMock() {

		$class = 'GetOptionKit\OptionCollection';
		$mock = $this->testCase->getMockBuilder( $class )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	public function getCommonApplicationArgumentBuilderMock() {

		$class = 'GitAutomatedMirror\Common\ApplicationArgumentBuilder';
		$mock = $this->testCase->getMockBuilder( $class )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}
} 