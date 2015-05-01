<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;

interface RemoteInterface {

	/**
	 * @return string
	 */
	public function getUpstreamUri();

	/**
	 * @return string
	 */
	public function getDownstreamUri();

	/**
	 * @return string
	 */
	public function getName();
}