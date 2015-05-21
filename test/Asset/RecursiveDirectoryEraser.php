<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class RecursiveDirectoryEraser {

	/**
	 * Remove a directory even if it is not empty
	 * just like $ rm -r
	 *
	 * @link http://stackoverflow.com/a/3352564/2169046
	 * @param string $directory
	 */
	public function rmDir( $directory ) {

		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$directory,
				RecursiveDirectoryIterator::SKIP_DOTS
			),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ( $files as $fileInfo ) {
			/* @type RecursiveDirectoryIterator $fileInfo */
			$function = $fileInfo->isDir()
				? 'rmdir'
				: 'unlink';
			$function( $fileInfo->getPathname() );
		}

		rmdir( $directory );
	}
} 