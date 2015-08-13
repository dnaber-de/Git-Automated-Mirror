<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror;

/**
 * suppress notices
 * @link https://github.com/dnaber-de/Git-Automated-Mirror/issues/4
 */
error_reporting( E_ALL ^ E_NOTICE );

$base_dir = dirname( __DIR__ );
require_once $base_dir . '/src/GitAutomatedMirror/Autoload/GitAutomatedMirrorLoader.php';

$loader = new Autoload\GitAutomatedMirrorLoader( $base_dir );
$loader->load_dependencies();
$loader->load_source();
$diContainer = new \Dice\Dice;
$app = new App\GitAutomatedMirror( $diContainer, new Config\DiceConfigurator( $diContainer ) );
$app->init();
$app->run( $GLOBALS[ 'argv' ] );
$app->shutdown();

