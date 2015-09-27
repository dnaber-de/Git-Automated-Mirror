<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Git;

use
	GitAutomatedMirror\Git,
	GitAutomatedMirror\Test\Asset;

class TagReaderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Asset\MockBuilder
	 */
	private $mockBuilder;

	public function setUp() {

		$this->mockBuilder = new Asset\MockBuilder( $this );
	}

	/**
	 * @group debug
	 * @dataProvider rawRemoteTagsData
	 * @param $rawTags
	 * @param array $expected
	 */
	public function testParseRawRemoteTags( $rawTags, Array $expected ) {

		$gitClientMock = $this->mockBuilder->getPhpGitMock();

		$testee = new Git\TagReader( $gitClientMock );

		$tags = $testee->parseRawRemoteTags( $rawTags );

		$this->assertSame(
			$expected,
			$tags
		);
	}

	/**
	 * @see testParseRawRemoteTags
	 * @return array
	 */
	public function rawRemoteTagsData() {

		$data = [];

		$data[ 'remote_braches' ] = [
			#1. Parameter $rawTags
			"fcf9872a2808347f5376273d6880a957a1ab3189	refs/tags/1.0.0
6a2b0951e9397609ad546d7d9eac4c03510f4718	refs/tags/1.0.0^{}
1d73121a6e5c2838a0c81246b9e2ae116a4f2fb4	refs/tags/1.2.0
b0fc72830bad2bd61227ce68403e2c507c470823	refs/tags/1.3.0",
			#2. Parameter $expected
			[
				'1.0.0',
				'1.0.0^{}',
				'1.2.0',
				'1.3.0',
			]
		];

		return $data;
	}
}
 