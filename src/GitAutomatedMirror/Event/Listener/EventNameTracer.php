<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Event\Listener;
use GitAutomatedMirror\Printer;
use League\Event;

/**
 * Class EventNameTracer
 *
 * An event listener which prints the event name
 * using a given printer
 *
 * @package GitAutomatedMirror\Event\Listener
 */
class EventNameTracer implements Event\ListenerInterface {

	/**
	 * @type Printer\PrinterInterface
	 */
	private $printer;

	/**
	 * @param Printer\PrinterInterface $printer
	 */
	public function __construct( Printer\PrinterInterface $printer ) {

		$this->printer = $printer;
	}

	/**
	 * Check weather the listener is the given parameter.
	 *
	 * @param mixed $listener
	 *
	 * @return bool
	 */
	public function isListener( $listener ) {

		return $this === $listener;
	}

	/**
	 * @param Event\EventInterface $event
	 */
	public function handle( Event\EventInterface $event ) {

		$msg = "Event '{$event->getName()}' triggered.";
		$this->printer->printLine( $msg );
	}
} 