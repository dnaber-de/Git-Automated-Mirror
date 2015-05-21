<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Test;
use GitAutomatedMirror\Autoload;
use Requisite;

/**
 * allow to override the bootstraping process locally
 */
if ( file_exists( __DIR__ . '/bootstrap.php' ) )
	return require_once __DIR__ . '/bootstrap.php';

$base_dir = dirname( __DIR__ );

require_once $base_dir . '/src/GitAutomatedMirror/Autoload/GitAutomatedMirrorLoader.php';

$loader = new Autoload\GitAutomatedMirrorLoader( $base_dir );
$loader->load_dependencies();
$loader->load_source();

$loader = new Requisite\SPLAutoLoader;
$loader->addRule(
	new Requisite\Rule\NamespaceDirectoryMapper( __DIR__ . '/Asset', __NAMESPACE__ . '\Asset' )
);