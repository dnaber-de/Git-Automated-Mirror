<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Printer;

interface PrinterInterface {

	/**
	 * @param $string
	 * @return void
	 */
	public function printLine( $string );
}