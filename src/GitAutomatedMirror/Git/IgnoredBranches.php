<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;

/**
 * Class IgnoredBranches
 *
 * Todo:
 * This class is ineffective as it has a fixed (static) list of
 * ignored branch. Solution: exclude the list of ignored branches
 * to an implementation of a simple BranchList interface which itself
 * is a matter of the Config domain
 *
 * @package GitAutomatedMirror\Git
 */
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
			'gamTempBranch' // used by Git\TagMerger Todo: resolve this static dependency
		];

		return in_array( $branch->getName(), $ignoredBranchNames );
	}
} 