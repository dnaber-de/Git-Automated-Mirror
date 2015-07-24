<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Git;
use GitAutomatedMirror\Type;
use PHPGit;

class BranchReader {

	/**
	 * @type PHPGit\Git
	 */
	private $git;

	/**
	 * @type array
	 */
	private $branches = [];

	/**
	 * @param PHPGit\Git $git
	 */
	public function __construct( PHPGit\Git $git ) {

		$this->git = $git;
		$this->updateBranches();
	}

	/**
	 * @return array (Array of Type\GitBranch objects)
	 */
	public function getBranches() {

		return $this->branches;
	}

	/**
	 * reads the current git branches and adds
	 * them to the branch list
	 *
	 * @return array
	 */
	public function buildBranches() {

		$rawBranches = $this->git->branch( [ 'all' => TRUE ] );
		$branches = [];
		foreach ( $rawBranches as $rawBranch )
			$branches = $this->addRawBranch( $rawBranch, $branches );

		return $branches;
	}

	/**
	 * update the internal cache of builded branches
	 *
	 */
	public function updateBranches() {

		$this->branches = $this->buildBranches();
	}

	/**
	 * Add another branch to the list of branches or »merge« two branch objects
	 * the objects. That means a branch 'remotes/origin/master' will become a
	 * remote option of an existing 'master' branch.
	 *
	 * @see PHPGit\Command\BranchCommand::__invoke()
	 * @param array $rawBranch
	 * @param array $branches
	 * @return array
	 */
	public function addRawBranch( Array $rawBranch, Array $branches ) {

		$branchInfo = $this->parseBranchName( $rawBranch[ 'name' ] );
		$branchName = $branchInfo[ 'name' ];
		if ( ! isset( $branches[ $branchName ] ) )
			$branches[ $branchName ] = new Type\GitBranch( $branchName );

		if ( empty( $branchInfo[ 'remote' ] ) )
			$branches[ $branchName ]->setIsLocal( TRUE );
		else
			$branches[ $branchName ]->pushRemote( $branchInfo[ 'remote' ], $rawBranch[ 'name' ] );

		return $branches;
	}

	/**
	 * Convention: Branches have to look like
	 * remotes/$remote/$branchname or just $branchname
	 *
	 * @param $name
	 * @return array
	 */
	public function parseBranchName( $name ) {

		$parts = [
			'name'   => $name,
			'remote' => ''
		];

		if ( FALSE === strpos( $name, '/' ) )
			return $parts;

		$chunks = explode( '/', $name );
		if ( isset( $chunks[ 1 ] ) )
			$parts[ 'remote' ] = $chunks[ 1 ];
		if ( isset( $chunks[ 2 ] ) )
			$parts[ 'name' ] = $chunks[ 2 ];

		return $parts;
	}

	/**
	 * @param array $rawBranch
	 * @return Type\GitBranch
	 */
	public function parseRawBranch( Array $rawBranch ) {

		$branchInfo = $this->parseBranchName( $rawBranch[ 'name' ] );
		$gitBranch = new Type\GitBranch( $branchInfo[ 'name' ] );

		if ( empty( $branchInfo[ 'remote' ] ) )
			$gitBranch->setIsLocal( TRUE );
		else
			$gitBranch->pushRemote( $branchInfo[ 'remote' ], $rawBranch[ 'name' ] );

		return $gitBranch;
	}

	/**
	 * @return Type\GitBranch|NULL
	 */
	public function getCurrentBranch() {

		$rawBranches = $this->git->branch( [ 'all' => TRUE ] );
		$branches = [];
		$currentBranch = NULL;
		foreach ( $rawBranches as $rawBranch ) {
			$branchInfo = $this->parseBranchName( $rawBranch[ 'name' ] );
			$branchName = $branchInfo[ 'name' ];
			$branches = $this->addRawBranch( $rawBranch, $branches );
			if ( $rawBranch[ 'current' ] )
				$currentBranch = $branches[ $branchName ];
		}

		return $currentBranch;
	}
}