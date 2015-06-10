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

	/**
	 * Parses output from commands like `git log --pretty=oneline
	 *
	 * @param $stdOut
	 * @return array
	 */
	public function parseLogPrettyOneLine( $stdOut ) {

		$rawCommits = explode( "\n", $stdOut );
		$commits = [];
		foreach ( $rawCommits as $line ) {
			if ( '' === trim( $line ) )
				continue;
			$parts = explode( ' ', $line );
			$hash = array_shift( $parts );
			$commits[ $hash ] = implode( ' ', $parts );
		}

		return $commits;
	}
} 