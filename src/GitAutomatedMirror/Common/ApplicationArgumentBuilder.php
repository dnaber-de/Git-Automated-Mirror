<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Common;
use GitAutomatedMirror\Type;

class ApplicationArgumentBuilder {

	/**
	 * @param string $name
	 * @param string $type
	 * @param bool $isRequired
	 * @param string $shortName
	 * @param string $description
	 *
	 * @return Type\ApplicationArgument
	 */
	public function buildArgument( $name, $type, $isRequired, $shortName, $description ) {

		return new Type\ApplicationArgument(
			(string) $name,
			(string) $type,
			(bool) $isRequired,
			(string) $shortName,
			(string) $description
		);
	}
} 