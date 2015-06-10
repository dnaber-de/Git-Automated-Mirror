<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test\Asset;

class Debug {

	/**
	 * @link http://php.net/manual/en/function.debug-backtrace.php#112238
	 * @return string
	 */
	public function generateCallTrace() {

		$e = new \Exception();
		$trace = explode( "\n", $e->getTraceAsString() );

		// reverse array to make steps line up chronologically
		$trace = array_reverse( $trace );
		array_shift( $trace ); // remove {main}
		array_pop( $trace ); // remove call to this method
		$length = count( $trace );
		$result = [];

		for ( $i = 0; $i < $length; $i++ )
		{
			$result[] = ($i + 1)  . ')'
				. substr( $trace[ $i ], strpos( $trace[ $i ], ' ') );
		}
		$result = array_reverse( $result );

		return "\t" . implode( "\n\t" , $result );
	}
} 