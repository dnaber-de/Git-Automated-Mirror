<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Event\ListenerProvider;
use League\Event;

/**
 * Class ListenerMapProvider
 *
 * Provides a map of event listeners to an listener-acceptor
 * E.g.
 * [
 *    'event.name' => new MyEventNameListener,
 *    '*'          => new AllEventsListener
 * ]
 *
 * @package GitAutomatedMirror\Event\ListenerProvider
 */
class ListenerMapProvider implements Event\ListenerProviderInterface {

	/**
	 * @type array
	 */
	private $listeners = [];

	/**
	 * @param array $listenerMap
	 */
	public function __construct( Array $listenerMap ) {

		$this->listeners = $listenerMap;
	}

	/**
	 * Provide event
	 *
	 * @param Event\ListenerAcceptorInterface $listenerAcceptor
	 * @return $this
	 */
	public function provideListeners( Event\ListenerAcceptorInterface $listenerAcceptor ) {

		foreach ( $this->listeners as $event => $listener )
			$listenerAcceptor->addListener( $event, $listener );

		return $this;
	}

} 