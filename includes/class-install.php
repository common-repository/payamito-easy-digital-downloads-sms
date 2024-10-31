<?php

namespace Payamito\EDD;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Install' ) ) :

	class Install
	{
		/**
		 * Install PayamitoUltimateMember.
		 */
		public static function install()
		{
			if ( ! is_blog_installed() ) {
				wp_die( 'WordPress is not already installed' );
			}

			set_transient( 'payamito_edd_installing', 'yes' );

			self::set_core_version();
		}

		private static function set_core_version()
		{
			$core_version = get_option( "payamito_core_version" );
			$dir_name     = self::get_fil_name( __DIR__ );
			$file_name    = basename( PAYAMITO_EDD_PLUGIN_FILE );

			$update = [
				'version'       => payamito_edd()->core_version,
				'absolute_path' => $dir_name . '/' . $file_name,
				'core_path'     => PAYAMITO_EDD_CORE_DIR,
			];

			if ( $core_version === false ) {
				update_option( "payamito_core_version", serialize( $update ) );
			} else {
				$self_version  = payamito_edd()->core_version;
				$other_version = unserialize( $core_version )['version'];

				if ( $self_version > $other_version ) {
					update_option( "payamito_core_version", serialize( $update ) );
				}
			}
		}

		private static function get_fil_name( $__DIR__ )
		{
			$dir_name = basename( dirname( $__DIR__, 1 ) );

			if ( $dir_name === 'plugins' ) {
				$dir_name = dirname( plugin_basename( __FILE__ ) );
			}

			return $dir_name;
		}
	}
endif;
