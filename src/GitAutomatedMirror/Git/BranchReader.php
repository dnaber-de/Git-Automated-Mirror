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
	 */
	public function buildBranches() {

		$rawBranches = $this->git->branch( [ 'all' => TRUE ] );
		foreach ( $rawBranches as $rawBranch )
			$this->addRawBranch( $rawBranch );
	}

	/**
	 * @see PHPGit\Command\BranchCommand::__invoke()
	 * @param array $rawBranch
	 */
	public function addRawBranch( Array $rawBranch ) {

		$branchInfo = $this->parseBranchName( $rawBranch[ 'name' ] );
		$branchName = $branchInfo[ 'name' ];
		if ( ! isset( $this->branches[ $branchName ] ) )
			$this->branches[ $branchName ] = new Type\GitBranch( $branchName );

		if ( empty( $branchInfo[ 'remote' ] ) )
			$this->branches[ $branchName ]->setIsLocal( TRUE );
		else
			$this->branches[ $branchName ]->pushRemote( $branchInfo[ 'remote' ], $rawBranch[ 'name' ] );
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
}