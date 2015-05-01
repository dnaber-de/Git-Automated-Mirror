<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Common;
use GitAutomatedMirror\Common;
use GitAutomatedMirror\Type;

class ApplicationArgumentBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testBuildArgument() {

		$args = [
			'name'        => 'help',
			'type'        => 'string',
			'isRequired'  => FALSE,
			'shortName'   => 'h',
			'description' => 'Print help message.'
		];

		$testee = new Common\ApplicationArgumentBuilder;
		/* @type Type\ApplicationArgument $argument */
		$argument = call_user_func_array( [ $testee, 'buildArgument' ], $args );

		$this->assertInstanceOf(
			'GitAutomatedMirror\Type\ApplicationArgument',
			$argument
		);
	}
}
 