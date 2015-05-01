<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Autoload;

interface LoaderInterface {

	/**
	 * should load all dependencies or register autoloading for it
	 *
	 * @return void
	 */
	public function load_dependencies();

	/**
	 * should load the source files
	 *
	 * @return void
	 */
	public function load_source();
} 