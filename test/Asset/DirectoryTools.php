<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;
use DirectoryIterator;
use RecursiveDirectoryIterator;

class DirectoryTools {

	/**
	 * @type string
	 */
	private $path;

	/**
	 * @param string $path
	 */
	public function __construct( $path ) {

		$this->path = realpath( $path );
	}

	/**
	 * @param string $path
	 */
	public function cd( $path ) {

		$this->path = realpath( $path );
	}

	/**
	 * list file names
	 *
	 * @return array|bool
	 */
	public function ls() {

		if ( ! $this->path )
			return FALSE;

		$list = [];
		$iterator = new DirectoryIterator( $this->path );
		foreach ( $iterator as $file )
			$list[] = $file->getFilename();

		return $list;
	}

	/**
	 * @return array|bool
	 */
	public function getFiles() {

		if ( ! $this->path )
			return FALSE;

		$list = [];
		$iterator = new DirectoryIterator( $this->path );
		foreach ( $iterator as $file ) {
			if ( ! $file->isDot() && $file->isFile() )
				$list[ $file->getFilename() ] = $file->getRealPath();
		}

		return $list;
	}
}