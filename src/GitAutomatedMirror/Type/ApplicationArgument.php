<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;
use GitAutomatedMirror\Common;

class ApplicationArgument implements ArgumentInterface, Common\StringConvertible {

	/**
	 * @type string
	 */
	private $name = '';

	/**
	 * @type string
	 */
	private $type;

	/**
	 * @type string
	 */
	private $shortName = '';

	/**
	 * @type bool
	 */
	private $isRequired = FALSE;

	/**
	 * @type string
	 */
	private $description = '';

	/**
	 * @param string $name
	 * @param string $type
	 * @param bool $isRequired
	 * @param string $shortName
	 * @param string $description
	 */
	public function __construct( $name, $type, $isRequired, $shortName, $description ){

		$this->name        = (string) $name;
		$this->type        = (string) $type;
		$this->shortName   = (string) $shortName;
		$this->isRequired  = (bool) $isRequired;
		$this->description = (string) $description;
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
	public function getType() {

		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getShortName() {

		return $this->shortName;
	}

	/**
	 * @return bool
	 */
	public function isRequired() {

		return $this->isRequired;
	}

	/**
	 * @return string
	 */
	public function getDescription() {

		return $this->description;
	}

	/**
	 * @return string
	 */
	public function __toString() {

		return $this->name;
	}

}