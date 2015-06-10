<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;
use GitAutomatedMirror\Common;

class GitTag implements TagInterface, Common\StringConvertible {

	/**
	 * @type string
	 */
	private $name;

	/**
	 * @param $name
	 */
	public function __construct( $name ) {

		$this->name = (string) $name;
	}

	/**
	 * @return string
	 */
	public function getName() {

		return $this->name;
	}

	/**
	 * @return string
	 */
	public function __toString() {

		return $this->name;
	}
}