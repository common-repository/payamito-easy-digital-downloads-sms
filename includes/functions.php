<?php

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! function_exists( 'payamito_edd_load_core' ) ) {
	function payamito_edd_load_core()
	{
		$core = get_option( "payamito_core_version" );
		if ( $core === false ) {
			return PAYAMITO_EDD_CORE_DIR;
		}
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$core = unserialize( $core );
		if (
			file_exists( $core['core_path'] ) && is_plugin_active( $core['absolute_path'] )
		) {
			return $core['core_path'];
		} else {
			return PAYAMITO_EDD_CORE_DIR;
		}

		return PAYAMITO_EDD_CORE_DIR;
	}
}
