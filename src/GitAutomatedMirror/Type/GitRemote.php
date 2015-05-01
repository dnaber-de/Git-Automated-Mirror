<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;
use GitAutomatedMirror\Common;

class GitRemote implements RemoteInterface, Common\StringConvertible {

	/**
	 * @type string
	 */
	private $name = '';

	/**
	 * @type string
	 */
	private $upstreamUri = '';

	/**
	 * @type string
	 */
	private $downstreamUri = '';

	/**
	 * @param string $name
	 * @param string $upstreamUri
	 * @param string $downstreamUri (Optional, $upstreamUri used if not given)
	 */
	public function __construct( $name, $upstreamUri, $downstreamUri = NULL ) {

		$this->name          = (string) $name;
		$this->upstreamUri   = (string) $upstreamUri;
		$this->downstreamUri = $downstreamUri
			? (string) $downstreamUri
			: $upstreamUri;
	}

	/**
	 * @return string
	 */
	public function getUpstreamUri() {

		return $this->upstreamUri;
	}

	/**
	 * @return string
	 */
	public function getDownstreamUri() {

		return $this->downstreamUri;
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