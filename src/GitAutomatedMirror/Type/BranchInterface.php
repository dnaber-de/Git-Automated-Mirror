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

	/**
	 * @param bool $isLocal
	 * @return void
	 */
	public function setIsLocal( $isLocal );

	/**
	 * @param string $remote
	 * @param string $fullRef (Optional)
	 * @return void
	 */
	public function pushRemote( $remote, $fullRef = '' );

	/**
	 * @param string $remote
	 * @return void
	 */
	public function popRemote( $remote );
}