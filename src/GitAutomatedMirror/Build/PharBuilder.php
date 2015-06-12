<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Build;
use GitAutomatedMirror\Config;

class PharBuilder {

	/**
	 * @type \Phar
	 */
	private $phar;

	/**
	 * @type Config\SourceDirectoriesList
	 */
	private $sourceList;

	/**
	 * @param \Phar $phar
	 * @param Config\SourceDirectoriesList $sourceList
	 */
	public function __construct( \Phar $phar, Config\SourceDirectoriesList $sourceList ) {

		$this->phar = $phar;
		$this->sourceList = $sourceList;
	}

	public function buildDependencies() {

		foreach ( $this->sourceList->getDependencyDirectories() as $dir )
			$this->buildFromDirectory( $dir );
	}

	public function buildEntryScript() {

		$fp = fopen( $this->sourceList->getEntryFile(), 'rb' );
		$this->phar[ 'index.php' ] = $fp;
	}

	public function buildSource() {

		$dir = $this->sourceList->getSourceDirectory();
		$this->buildFromDirectory( $dir );
	}

	/**
	 * @param string $dir
	 * @param string $filter (a valid regex or NULL)
	 */
	public function buildFromDirectory( $dir, $filter = NULL ) {

		$iterator = $this->getDirectoryIterator( $dir, $filter );
		$this->phar->buildFromIterator( $iterator, dirname( $dir ) );
	}

	/**
	 * @param string $dir
	 * @param string $filter (a valid regex or NULL)
	 * @return \RecursiveIteratorIterator|\RegexIterator
	 */
	public function getDirectoryIterator( $dir, $filter = NULL ) {

		$directoryIterator = new \RecursiveDirectoryIterator( $dir );
		if ( $filter ) {
			$directoryIterator = new \RegexIterator( $directoryIterator, $filter, \RecursiveRegexIterator::GET_MATCH );
		}

		return $directoryIterator;
	}
}