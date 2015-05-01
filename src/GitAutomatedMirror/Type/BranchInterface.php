<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;

interface BranchInterface {

	/**
	 * @return bool
	 */
	public function isLocal();

	/**
	 * @return array
	 */
	public function getRemotes();

	/**
	 * @return string
	 */
	public function getName();
}