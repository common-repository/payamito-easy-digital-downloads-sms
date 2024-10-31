<?php

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	die( 'direct access abort ' );
}

if ( function_exists( 'pedd_autoload' ) && is_callable( 'pedd_autoload' ) ) {
	spl_autoload_register( 'pedd_autoload' );
}

function pedd_autoload( $class_name )
{
	$namespace = 'Payamito\EDD';
	if ( 0 !== strpos( $class_name, $namespace ) ) {
		return;
	}

	$class_name = str_replace( $namespace, '', $class_name );
	$class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );

	$path = PAYAMITO_EDD_DIR . $class_name . '.php';
	if ( file_exists( $path ) ) {
		include_once $path;
	}
}