<?php

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	die( 'direct access abort ' );
}

if ( ! defined( 'PAYAMITO_EDD_BASENAME' ) ) {
	defined( 'PAYAMITO_EDD_BASENAME' ) || define( 'PAYAMITO_EDD_BASENAME', __DIR__ );
}

if ( ! defined( 'PAYAMITO_EDD_DIR' ) ) {
	define( 'PAYAMITO_EDD_DIR', PAYAMITO_EDD_BASENAME );
}

if ( ! defined( 'PAYAMITO_EDD_URL' ) ) {
	define( 'PAYAMITO_EDD_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PAYAMITO_EDD_VER' ) ) {
	define( 'PAYAMITO_EDD_VER', '1.4.0' );
}

if ( ! defined( 'PAYAMITO_EDD_CORE' ) ) {
	define( 'PAYAMITO_EDD_CORE', '2.1.8' );
}
if ( ! defined( 'PAYAMITO_EDD_CORE_DIR' ) ) {
	define( 'PAYAMITO_EDD_CORE_DIR', PAYAMITO_EDD_DIR . '/includes/core/payamito-core' );
}

