<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;

interface ArgumentInterface {

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return string
	 */
	public function getShortName();

	/**
	 * @return bool
	 */
	public function isRequired();

	/**
	 * @return string
	 */
	public function getDescription();

} 