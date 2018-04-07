<?php
/*
-------------------------------------------------------------------------------
Front-end scripts and styles
-------------------------------------------------------------------------------
*/
add_action( 'demonstrator_header', function () {

	$assets = '' . PHP_EOL;

	$assets .= '<link rel="stylesheet" href="' . dmstr()->assetsURL( 'css/styles.css' ) . '" type="text/css" media="all" />' . PHP_EOL;
	// $assets .= '<script src="'. dmstr()->assetsURL( 'js/jquery-3.2.1.slim.min.js' ) .'"></script>' . PHP_EOL;
	// $assets .= '<script src="'. dmstr()->assetsURL( 'js/Uri.js' ) .'"></script>' . PHP_EOL;
	// $assets .= '<script src="'. dmstr()->assetsURL( 'js/config.js' ) .'"></script>' . PHP_EOL . PHP_EOL;
	$assets .= '<script src="' . dmstr()->assetsURL( 'js/frontend.min.js' ) . '"></script>' . PHP_EOL . PHP_EOL;

	echo $assets;

} );
