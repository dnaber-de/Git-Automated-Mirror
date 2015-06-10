<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;
use GitAutomatedMirror\Type;
use GetOptionKit;
use PHPGit;

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
	 * @type PHPGit\Git
	 * @param Array $methods
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getPhpGitMock( Array $methods = NULL ) {

		$class = 'PHPGit\Git';

		return $this->getMockWithoutConstructor( $class, $methods );
	}

	/**
	 * @param string $eventName
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getEventMock( $eventName = NULL ) {

		$mock = $this->getMockWithoutConstructor( 'League\Event\Event' );
		if ( $eventName )
			$mock->expects( $this->testCase->any() )
				->method( 'getName' )
				->willReturn( $eventName );

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getGitBranchMock() {

		$mock = $this->getMockWithoutConstructor( 'GitAutomatedMirror\Type\GitBranch' );

		return $mock;
	}

	/**
	 * @param array $methods
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getTagReaderMock( Array $methods = NULL ) {

		$mock = $this->getMockWithoutConstructor(
			'GitAutomatedMirror\Git\TagReader',
			$methods
		);

		return $mock;
	}

	/**
	 * @param array $methods
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getEventEmitterMock( Array $methods = NULL ) {

		$mock = $this->getMockWithoutConstructor(
			'League\Event\Emitter',
			$methods
		);

		return $mock;
	}

	/**
	 * @param string $class
	 * @param Array $methods
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getMockWithoutConstructor( $class, Array $methods = NULL ) {

		$mockBuilder = $this->testCase->getMockBuilder( $class )
			->disableOriginalConstructor();

		if ( $methods )
			$mockBuilder->setMethods( $methods );

		return $mockBuilder->getMock();
	}
} 