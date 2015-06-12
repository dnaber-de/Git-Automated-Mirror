<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Build;
use GitAutomatedMirror\Autoload;
use GitAutomatedMirror\Config;

if ( ! \Phar::canWrite() )
	exit( 'Phar write support disabled!' );

$base_dir = dirname( __DIR__ );
require_once $base_dir . '/src/GitAutomatedMirror/Autoload/GitAutomatedMirrorLoader.php';

$loader = new Autoload\GitAutomatedMirrorLoader( $base_dir );
$loader->load_dependencies();
$loader->load_source();

$phar = new \Phar( __DIR__ . '/git-automated-mirror.phar' );
$pharBuilder = new PharBuilder(
	$phar,
	new Config\SourceDirectoriesList( dirname( __DIR__ ) )
);
$phar->startBuffering();
$pharBuilder->buildDependencies();
$pharBuilder->buildSource();
$pharBuilder->buildEntryScript();

$phar->stopBuffering();

echo "Created git-automated-archive.phar\n";
