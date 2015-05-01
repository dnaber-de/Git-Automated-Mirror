<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Autoload;
use Requisite;

/**
 * as this file is included during bootstrap
 * the interface must be loaded here
 */
if ( ! interface_exists( __NAMESPACE__ . '\LoaderInterface' ) )
	require_once __DIR__ . '/LoaderInterface.php';

class GitAutomatedMirrorLoader implements LoaderInterface {

	/**
	 * @type string
	 */
	private $base_dir;

	public function __construct( $base_dir = '' ) {

		$this->base_dir = $base_dir
			? rtrim( $base_dir, '\\/' )
			: dirname( dirname( dirname( __DIR__ ) ) );
	}
	/**
	 * should load all dependencies or register autoloading for it
	 *
	 * @return void
	 */
	public function load_dependencies() {

		require_once $this->base_dir . '/vendor/autoload.php';
	}

	/**
	 * should load the source files
	 *
	 * @return void
	 */
	public function load_source() {

		$spl_autoload = new Requisite\SPLAutoLoader;
		$spl_autoload->addRule(
			new Requisite\Rule\NamespaceDirectoryMapper(
				$this->base_dir . '/src/GitAutomatedMirror',
				'GitAutomatedMirror'
			)
		);
	}
}