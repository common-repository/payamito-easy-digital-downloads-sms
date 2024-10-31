<?php

namespace Payamito\Edd;

use PEDD_Functions;
use Payamito_OTP;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( "Ajax" ) ) {
	class Ajax
	{

		/**
		 * The single instance of the class.
		 *
		 * @since 1.1.2
		 */

		protected static $instance = null;
		public $send;

		public static function get_instance()
		{
			if ( is_null( self::$instance ) && ! ( self::$instance instanceof User ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct()
		{
			add_action( 'wp_ajax_Payamito_request', [ $this, 'ajax' ] );
			add_action( 'wp_ajax_nopriv_Payamito_request', [ $this, 'ajax' ] );
		}

		/**
		 * handling OTP ajax request
		 *
		 * @return void
		 * @since 1.0.0
		 */

		public function ajax()
		{
			if ( ! payamito_is_request( 'ajax' ) ) {
				wp_die();
			}
			$mobile = sanitize_text_field( payamito_to_english_number( $_REQUEST['mobile'] ) );

			if ( ! isset( $mobile ) || empty( $mobile ) || ! is_numeric( $mobile ) ) {
				wp_die();
			}
			$this->mobile_confirmation( $mobile );
		}

		/**
		 * Mobile number validation and sending SMS
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function mobile_confirmation( $mobile )
		{
			global $edd_payamito_otp_options;

			if ( $edd_payamito_otp_options[ 'edd_sms_otp_active' != '1' ] ) {
				return;
			}
			if ( ! payamito_verify_moblie_number( $mobile ) ) {
				PEDD_Functions::ajax_response( - 1, PEDD_Functions::message()[0] );
			}
			Payamito_OTP::payamito_resent_time_check( $mobile );

			$pattern_id = $edd_payamito_otp_options['edd_sms_otp_p'];

			if ( $edd_payamito_otp_options['edd_sms_otp_active_p'] != '1' ) {
				payamito_edd()->send->send_type = 'send';
			} else {
				payamito_edd()->send->send_type = 'send_by_pattern';

				payamito_edd()->send->pattern_id = $pattern_id;
			}
			payamito_edd()->send->setParams( (string) $mobile, $edd_payamito_otp_options['edd_sms_otp_sms'], $edd_payamito_otp_options['edd_sms_otp_sms_repeater'], null );
			$send = payamito_edd()->send->Send( true );

			if ( $send['result'] === true ) {
				$success = true;
			} else {
				$success = false;
			}
			if ( ! $success && current_user_can( "manage_options" ) ) {
				PEDD_Functions::ajax_response( - 1, $send["message"] );
			}
			if ( ! $success ) {
				PEDD_Functions::ajax_response( - 1, PEDD_Functions::message()[2] );
			}
			if ( $success ) {
				if ( ! is_null( $send["OTP"] ) ) {
					Payamito_OTP::payamito_set_session( $mobile, $send["OTP"],'payamito_edd' );
				}
				PEDD_Functions::ajax_response( 1, PEDD_Functions::message()[1] );
			}
		}
	}
}
