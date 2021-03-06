<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Printer;

/**
 * Class StdOutPrinter
 *
 * Writes to the php://stdout stream
 * which is not catchable by output buffers
 *
 * @package GitAutomatedMirror\Printer
 */
class StdOutPrinter implements PrinterInterface {

	/**
	 * @param $string
	 */
	public function printLine( $string ) {

		$string = str_replace( [ "\r", "\n" ], [ '\r', '\n' ], $string );
		fwrite( \STDOUT, $string . PHP_EOL );
	}

	/**
	 * @param $string
	 */
	public function printRaw( $string ) {

		fwrite( \STDOUT, $string );
	}
}