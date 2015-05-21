<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Assets;

/**
 * Sets up a Repository (tmp/process) with two local remotes
 * (source: tmp/source and mirror: tmp/mirror) and create
 * some branches and tags the testee should copy from
 * source to mirror
 *
 */
class RepositoryTestOrganizer {

	/**
	 * @type string
	 */
	private $baseDir;

	/**
	 * @type array
	 */
	private $repositories = [];

	/**
	 * @param $baseDir
	 */
	public function __construct( $baseDir ) {

		$this->baseDir = realpath( $baseDir );
		if ( FALSE === $this->baseDir )
			exit( "Directory $baseDir does not exist.\n" );

		$this->repositories = [
			'source'  => [
				'path'    => $this->baseDir . '/source',
				'remotes' => [],
				'name'    => 'origin'
			],
			'process' => [
				'path'    => $this->baseDir . '/process',
				'remotes' => [
					'origin' => '../source',
					'mirror' => '../mirror'
				],
				'name'    => 'process'
			],
			'mirror'  => [
				'path' => $this->baseDir . '/mirror',
				'remotes' => [],
				'name'    => 'mirror'
			]
		];
	}

	/**
	 * initially build the repositories
	 */
	public function setUpRepositories() {

		foreach ( $this->repositories as $name => $info )
			$this->initRepository( $name, $info[ 'path' ], $info[ 'remotes' ] );

		$this->createDefaultContent( $this->repositories[ 'source' ] );
	}

	/**
	 * create some default files to track with git
	 *
	 * @param array $repository
	 */
	public function createDefaultContent( Array $repository ) {

		chdir( $repository[ 'path' ] );

		$readmeFile = $repository[ 'path' ] . '/readme.md';
		file_put_contents( $readmeFile, "Hello World.\n" );

		`git add .`;
		`git commit -m"add readme"`;
		`git checkout -b 1.1-branch`;

		file_put_contents( $readmeFile, "Update!\n", FILE_APPEND );

		`git commit -m"update readme"`;
	}

	/**
	 * @param string $name
	 * @param string $path
	 * @param array $remotes
	 */
	public function initRepository( $name, $path, Array $remotes ) {

		if ( ! is_dir( $path ) )
			mkdir( $path );

		chdir( $path );
		`git init`;

		if ( empty( $remotes ) )
			return;

		foreach ( $remotes as $name => $uri ) {
			$name = escapeshellarg( $name );
			$uri  = escapeshellarg( $uri );

			`git remote add $name $uri`;
		}
	}

	/**
	 * @return array
	 */
	public function getRepositories() {

		return $this->repositories;
	}

	/**
	 * remove the test repositories completely
	 */
	public function cleanUp() {

		$directroyEraser = new RecursiveDirectoryEraser;
		foreach ( $this->repositories as $info ) {
			if ( ! is_dir( $info[ 'path' ] ) )
				continue;
			$directroyEraser->rmDir( $info[ 'path' ] );
		}
	}
} 