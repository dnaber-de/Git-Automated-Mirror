<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror;

$base_dir = dirname( __DIR__ );
require_once $base_dir . '/src/GitAutomatedMirror/Autoload/GitAutomatedMirrorLoader.php';

$loader = new Autoload\GitAutomatedMirrorLoader( $base_dir );
$loader->load_dependencies();
$loader->load_source();

$app = new App\GitAutomatedMirror( new \Dice\Dice );
$app->run( $GLOBALS[ 'argv' ] );

