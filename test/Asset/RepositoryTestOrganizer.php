<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;

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

		// setup the source (origin) repo
		$this->initRepository( $this->repositories[ 'source' ] );
		$this->createDefaultContent( $this->repositories[ 'source' ] );

		// setup the mirror repository as bare repository
		$this->initRepository( $this->repositories[ 'mirror' ], TRUE );

		// create the process repository
		$this->cloneRepository(
			$this->repositories[ 'process' ],
			$this->repositories[ 'source' ]
		);
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

		`git commit -am"update readme"`;
	}

	/**
	 * @param Array $repository
	 * @param bool $bare
	 */
	public function initRepository( Array $repository, $bare = FALSE ) {

		if ( ! is_dir( $repository[ 'path' ] ) )
			mkdir( $repository[ 'path' ] );

		chdir( $repository[ 'path' ] );
		if ( ! $bare )
			`git init`;
		else
			`git init --bare`;

		if ( empty( $repository[ 'remotes' ] ) )
			return;

		foreach ( $repository[ 'remotes' ] as $name => $uri ) {
			$name = escapeshellarg( $name );
			$uri  = escapeshellarg( $uri );

			`git remote add $name $uri`;
		}
	}

	/**
	 * add some stuff to the source repo to test
	 * against after an additional app run
	 */
	public function updateSourceRepo() {

		$repo = $this->repositories[ 'source' ];
		chdir( $repo[ 'path' ] );

		`git checkout master`;

		$newFile = $repo[ 'path' ] . '/newFile.txt';
		file_put_contents( $newFile, 'Foo Bar' );

		`git add .`;
		`git commit -m"add new file"`;
		`git tag v1.2`;
		`git checkout -b 1.2-branch`;
	}

	/**
	 * @param array $cloneRepo
	 * @param array $sourceRepo
	 */
	public function cloneRepository( Array $cloneRepo, Array $sourceRepo ) {

		if ( ! is_dir( $cloneRepo[ 'path' ] ) )
			mkdir( $cloneRepo[ 'path' ] );

		chdir( $cloneRepo[ 'path' ] );
		$sourcePath = escapeshellarg( $sourceRepo[ 'path' ] );

		`git clone $sourcePath .`;

		// add other remotes if necessary
		foreach ( $cloneRepo[ 'remotes' ] as $name => $path ) {
			if ( 'origin' === $name )
				continue;

			$name = escapeshellarg( $name );
			$path = escapeshellarg( $path );

			`git remote add $name $path`;
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

		$directoryEraser = new RecursiveDirectoryEraser;
		foreach ( $this->repositories as $info ) {
			if ( ! is_dir( $info[ 'path' ] ) )
				continue;
			$directoryEraser->rmDir( $info[ 'path' ] );
		}
	}
} 