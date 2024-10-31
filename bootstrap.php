<?php

/**
 *Plugin Name:  Payamito:easy digital download
 *Plugin URI:  https://payamito.com/
 *Description: Easily Send and control SMS for any action
 *Version: 1.4.0
 *Author: Payamito
 *Tested up to:5.8
 *Author URI: https://payamito.com/
 *License:  GPL-2.0+
 *License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 *Text Domain: payamito-edd
 *Domain Path: /languages
 *Requires PHP: 7.4.0
 */
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	die( 'direct access abort ' );
}

if ( ! defined( 'PAYAMITO_EDD_PLUGIN_FILE' ) ) {
	define( 'PAYAMITO_EDD_PLUGIN_FILE', __FILE__ );
}

register_activation_hook( __FILE__, 'payamito_edd_activate' );
register_deactivation_hook( __FILE__, 'payamito_edd_deactivate' );

function payamito_edd_activate()
{
	do_action( "payamito_edd_activate" );
	require_once PAYAMITO_EDD_DIR . '/includes/functions.php';
	require_once PAYAMITO_EDD_DIR . '/includes/class-install.php';

	Payamito\EDD\Install::install();
	require_once PAYAMITO_EDD_DIR . '/includes/core/payamito-core/includes/class-payamito-activator.php';

	Payamito_Activator::activate();
}

function payamito_edd_deactivate()
{
	do_action( "payamito_edd_deactivate" );
	require_once PAYAMITO_EDD_DIR . '/includes/core/payamito-core/includes/class-payamito-deactivator.php';
	Payamito_Deactivator::deactivate();
}

require_once __DIR__ . '/Define-constants.php';
require_once __DIR__ . '/includes/Autoloader.php';

if ( ! function_exists( "payamito_edd_set_locale" ) ) {
	function payamito_edd_set_locale()
	{
		$dirname = str_replace( '//', '/', wp_normalize_path( dirname( __FILE__ ) ) );
		$mo      = $dirname . '/languages/' . 'payamito-edd-' . get_locale() . '.mo';
		load_textdomain( 'payamito-edd', $mo );
	}
}

payamito_edd_set_locale();

if ( ! class_exists( 'PAYAMITO_EDD_DIR' ) ) {
	include_once PAYAMITO_EDD_DIR . '/includes/payamito-edd.php';
}

/**
 * @return object|Payamito_Edd|null
 */
function payamito_edd()
{
	return Payamito_Edd::get_instance();
}

payamito_edd();
