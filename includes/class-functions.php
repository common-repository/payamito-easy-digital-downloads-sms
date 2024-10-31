<?php

/**
 * Plugin public functions
 *
 * @package "Payamito_Edd
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;
if ( ! class_exists( "PEDD_Functions" ) ) {
	class PEDD_Functions
	{
		/**
		 * What type of request is this?
		 *
		 * @param string $type admin, ajax, cron or frontend.
		 *
		 * @return bool
		 */

		public static function is_request( $type )
		{
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		public static function get_tags()
		{
			return [
				[ 'tag' => '{download_list}', 'desc' => __( "download list", "payamito-edd" ) ],
				[ 'tag' => '{date}', 'desc' => __( "date", "payamito-edd" ) ],
				[ 'tag' => '{file_urls}', 'desc' => __( "file urls", "payamito-edd" ) ],
				[ 'tag' => '{subtotal}', 'desc' => __( "subtotal", "payamito-edd" ) ],
				[ 'tag' => '{payment_id}', 'desc' => __( "payment id", "payamito-edd" ) ],
				[ 'tag' => '{price}', 'desc' => __( "price", "payamito-edd" ) ],
				[ 'tag' => '{name}', 'desc' => __( "name", "payamito-edd" ) ],
				[ 'tag' => '{user_email}', 'desc' => __( "user email", "payamito-edd" ) ],
				[ 'tag' => '{tax}', 'desc' => __( "tax", "payamito-edd" ) ],
				[ 'tag' => '{receipt_id}', 'desc' => __( "receipt id", "payamito-edd" ) ],
				[ 'tag' => '{sitename}', 'desc' => __( "sitename", "payamito-edd" ) ],
				[ 'tag' => '{desccount_codes}', 'desc' => __( "desccount codes", "payamito-edd" ) ],

			];
		}

		/**
		 * Getting pattern when is send sms. Active patterns are passed. If there is no active pattern, an empty array is passed
		 *
		 * @access public
		 * @return array
		 * @static
		 * @since  1.0.0
		 */
		public static function get_patterns( $type_user, $type_status )
		{
			global $edd_payamito_messages_options;

			$pattern = [];

			$pattern_id = $edd_payamito_messages_options[ $type_user . "_" . $type_status . "_p" ];

			if ( $edd_payamito_messages_options[ $type_user . "_" . $type_status . "_active_p" ] != '0' ) {
				payamito_edd()->send->send_type = 'send_by_pattern';

				payamito_edd()->send->pattern_id = $pattern_id;
			} else {
				payamito_edd()->send->send_type = 'send';
			}
			$key = $type_user . "_" . $type_status . "_pattern_message";

			if ( isset( $edd_payamito_messages_options[ $key ] ) && is_array( $edd_payamito_messages_options[ $key ] ) && count( $edd_payamito_messages_options[ $key ] ) > 0 ) {
				foreach ( $edd_payamito_messages_options[ $key ] as $p ) {
					$tag = $p[ $type_user . "_" . $type_status . '_pattern_tags' ];

					$user_tag = $p[ $type_user . "_" . $type_status . '_user_tag' ];

					$pattern[] = [ 'tag' => $tag, 'user_tag' => $user_tag ];
				}

				return $pattern;
			}

			return [];
		}

		/**
		 * Getting messge when is send sms
		 *
		 * @access public
		 * @return string
		 * @static
		 * @since  1.0.0
		 */
		public static function get_message( $type_user, $type_status )
		{
			global $edd_payamito_messages_options;

			$arg = $type_user . "_" . $type_status . "_text";

			$message = $edd_payamito_messages_options[ $arg ] ?? "";

			if ( trim( $message ) == "" ) {
				return $message;
			}

			return $message;
		}

		/**
		 * Getting status  paymant
		 *
		 * @access public
		 * @return array
		 * @static
		 * @since  1.0.0
		 */

		public static function status()
		{
			return [ "publish", "pending", "processing", "refunded", "revoked", "failed", "abandoned" ];
		}

		/**
		 * Getting status  paymant
		 *Deprecated and does not work
		 *
		 * @access public
		 * @return array
		 * @static
		 * @since  1.0.0
		 */
		public static function gateways()
		{
			$gateways = [
				"payamito" => "payamito",
			];

			return apply_filters( "edd_payamito_list_gateways", $gateways );
		}

		/**
		 * verify moblie number
		 *Mobile number must start with 09
		 *
		 * @access public
		 * @return boolean
		 * @static
		 * @since  1.0.0
		 */
		public static function verify_moblie_number( $mobile )
		{
			$validate = false;
			if ( preg_match( '/^[0-9]{10}$/', $mobile ) ) {
				$validate = true;
			}
			if ( preg_match( '/^[0-9]{11}$/', $mobile ) ) {
				$validate = true;
			}

			return $validate;
		}

		/**
		 * ajax response
		 *The response to the OTP request is given in Ajax
		 *
		 * @access public
		 * @return boolean
		 * @static
		 * @since  1.0.0
		 */
		public static function ajax_response( int $type = - 1, string $message, $redirect = null )
		{
			wp_send_json( [ 'e' => $type, 'message' => $message, "re" => $redirect ] );
			die;
		}

		/**
		 * ajax response message
		 *
		 * @access public
		 * @return array
		 * @static
		 * @since  1.0.0
		 */
		public static function message()
		{
			return [
				__( 'Mobile number is incorrect', 'payamito-edd' ),
				__( 'OTP sent successfully', 'payamito-edd' ),
				__( 'Failed to send OTP ', 'payamito-edd' ),
				__( 'An unexpected error occurred. Please contact support ', 'payamito-edd' ),
				__( 'Enter OTP number ', 'payamito-edd' ),
				__( ' OTP is Incorrect ', 'payamito-edd' ),
			];
		}

		/**
		 * Convert Arabic or Persian numbers to English numbers
		 *
		 * @access public
		 * @return array
		 * @static
		 * @since  1.0.0
		 */
		public static function toEngNumbers( $string )
		{
			$persian = [ '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ];
			$english = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ];

			return str_replace( $persian, $english, $string );
		}

		/**
		 * Getting user meta key from database
		 *
		 * @access public
		 * @return array
		 * @static
		 * @since  1.0.0
		 */
		public static function get_meta_keys()
		{
			global $wpdb;
			$final   = [];
			$results = $wpdb->get_results( "SELECT DISTINCT `meta_key` FROM $wpdb->usermeta ", ARRAY_A );
			foreach ( $results as $result ) {
				 $final[$result['meta_key']] =$result['meta_value']  ;
			}

			return apply_filters( 'edd_payamito_meta_key', $final );
		}
	}
}
