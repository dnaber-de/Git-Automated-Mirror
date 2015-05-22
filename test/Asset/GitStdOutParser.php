<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;

class GitStdOutParser {

	/**
	 * @param string $stdOut
	 * @return array
	 */
	public function parseBranches( $stdOut ) {

		$branches = explode( "\n", trim( $stdOut ) );
		foreach ( $branches as &$branch ) {
			$branch = trim( $branch );
			if ( 0 === strpos( $branch, '* ' ) ) {
				$branch = substr( $branch, 2 );
			}
		}

		return $branches;
	}

	/**
	 * @param string $stdOut
	 * @return array
	 */
	public function parseTags( $stdOut ) {

		$tags = explode( "\n", trim( $stdOut ) );
		$tags = array_map( 'trim', $tags );

		return $tags;
	}
} 