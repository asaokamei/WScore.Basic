<?php
if( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
    $loader = include( dirname( __DIR__ ) . '/vendor/autoload.php' );
}
elseif( file_exists( dirname(dirname(dirname(dirname( __DIR__ )))) . '/vendor/autoload.php' ) ) {
    $loader = include( dirname(dirname(dirname(dirname( __DIR__ )))) . '/vendor/autoload.php' );
}
else {
    die( 'cannot locate autoloader' );
}
/** @var Composer\Autoload\ClassLoader $loader */
$loader->addPsr4( 'tests\\', __DIR__.'');
$loader->register();