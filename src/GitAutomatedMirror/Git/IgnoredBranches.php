<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;

class IgnoredBranches {

	/**
	 * @type BranchReader
	 */
	private $branchReader;

	public function __construct( BranchReader $branchReader ) {

		$this->branchReader = $branchReader;
	}

	/**
	 * @return array
	 */
	public function getIgnoredBranches() {

		$ignoredBranches = [];
		foreach ( $this->branchReader->getBranches() as $branch ) {
			if ( ! $this->isBranchIgnored( $branch ) )
				continue;
			$ignoredBranches[] = $branch;
		}

		return $ignoredBranches;
	}

	/**
	 * @param Type\GitBranch $branch
	 * @return bool
	 */
	public function isBranchIgnored( Type\GitBranch $branch ) {

		$ignoredBranchNames = [
			'HEAD',
			'1.0-branch'
		];

		return in_array( $branch->getName(), $ignoredBranchNames );
	}
} 