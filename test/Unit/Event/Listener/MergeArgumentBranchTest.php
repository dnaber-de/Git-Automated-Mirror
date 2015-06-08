<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Unit\Event\Listener;
use GitAutomatedMirror\Test\Asset;
use GitAutomatedMirror\Event\Listener;

class MergeArgumentBranchTest extends \PHPUnit_Framework_TestCase {

	public function testHandle() {

		$mockBuilder     = new Asset\MockBuilder( $this );
		$eventMock       = $mockBuilder->getEventMock();
		$branchMock      = $mockBuilder->getGitBranchMock();
		$mergeBranchMock = $mockBuilder->getGitBranchMock();
		$gitClientMock   = $mockBuilder->getPhpGitMock( [ 'checkout', 'merge' ] );
		$gitClientMock->expects( $this->exactly( 1 ) )
			->method( 'checkout' )
			->with( $branchMock );
		$gitClientMock->expects( $this->exactly( 1 ) )
			->method( 'merge' )
			->with(
				$mergeBranchMock,
				NULL,
				[
					'no-ff'    => TRUE,
					'strategy' => 'ours'
				]
			);
		$arguments = [
			'gitClient' => $gitClientMock,
			'branch'    => $branchMock
		];

		$testee = new Listener\MergeArgumentBranch( $mergeBranchMock );
		$void = $testee->handle( $eventMock, $arguments );
		$this->assertNull( $void );
	}
}
 