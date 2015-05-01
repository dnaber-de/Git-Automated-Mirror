<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Type;

interface VcsRepositoryInterface {

	/**
	 * returns the absolute path to the
	 * repository
	 *
	 * @return string
	 */
	public function getDirectory();
} 