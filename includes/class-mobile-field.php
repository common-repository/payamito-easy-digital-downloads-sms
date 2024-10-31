<?php

namespace Payamito\Edd;

/**
 *  Class Payamito_Edd_User
 *
 * @package  "Payamito_Edd
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( "Mobile_Field" ) ) {
	class Mobile_Field
	{
		/**
		 * The single instance of the class.
		 *
		 * @since 1.1.2
		 */

		protected static $instance = null;

		public static function get_instance()
		{
			if ( is_null( self::$instance ) && ! ( self::$instance instanceof User ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct()
		{
			global $edd_payamito_otp_options;

			if ( ! isset( $edd_payamito_otp_options['edd_sms_otp_active'] ) || $edd_payamito_otp_options['edd_sms_otp_active'] != '1' ) {
				return;
			}

			add_action( 'edd_purchase_form_user_info_fields', [ &$this, 'edd_custom_checkout_fields' ] );

			add_action( 'edd_checkout_error_checks', [ &$this, 'edd_validate_custom_fields' ], 10, 2 );

			add_filter( 'edd_payment_meta', [ &$this, 'edd_store_custom_fields' ] );

			add_action( 'edd_payment_personal_details_list', [ &$this, 'edd_store_custom_fields' ], 10, 2 );
		}

		// Output custom field HTML on checkout
		public function edd_custom_checkout_fields()
		{
			global $edd_payamito_otp_options;
			?>
            <p>
                <input type="hidden" id="edd_payamito_nonce" value=<?php
				echo wp_create_nonce( "edd_payamito" ) ?>>

                <input type="hidden" id="edd_payamito_otp_time" value=<?php
				echo esc_attr( $edd_payamito_otp_options["edd_sms_again_send_time"] ) ?>>

                <label class="edd-label"><?php
					esc_html_e( 'Mobile Number', "payamito-edd" ); ?>
					<?php
					if ( $edd_payamito_otp_options["customer_sms_add_mobile_field_enter"] === '1' ) {
						?>
                        <span class="edd-required-indicator">*</span>
						<?php
					}
					?>
                </label>
                <span class="edd-description"><?php
					esc_html_e( "Enter Mobile Number", "payamito-edd" ) ?></span>

                <input class="edd-input" type="text" name="payamito_mobile_field" id="payamito_mobile_field" value=""/>
				<?php
				if ( $edd_payamito_otp_options["customer_sms_add_mobile_field_force_OTP"] !== '1' ) {
					return;
				}
				?>
            <p>
                <label class="edd-label"><?php
					esc_html_e( 'OTP', "payamito-edd" ); ?> <span class="edd-required-indicator">*</span> </label>

                <span class="edd-description"><?php
					esc_html_e( "Enter OTP ", "payamito-edd" ) ?></span>

                <input autocomplete="on" class="edd-input" type="text" name="payamito_otp_field" id="payamito_otp_field"
                       value=""/>

                <input type="button" style="padding: 3px;" class="blue button" id="payamito_send_otp"
                       name="payamito_send_otp" value="<?php
				esc_attr_e( "Send OTP", "payamito-edd" ) ?>"/>
            </p>
            </p>
			<?php
		}

		// Check for errors with custom fields
		function edd_validate_custom_fields( $valid_data, $data )
		{
			global $edd_payamito_otp_options;

			if ( isset( $edd_payamito_otp_options["customer_sms_add_mobile_field_enter"] ) && $edd_payamito_otp_options["customer_sms_add_mobile_field_enter"] !== '1' ) {
				return;
			}
			if ( empty( $data['payamito_mobile_field'] ) ) {
				return edd_set_error( 'invalid_mobile', __( 'Please enter a valid  mobile number', "payamito-edd" ) );
			}
			if ( ! payamito_verify_moblie_number( $data['payamito_mobile_field'] ) ) {
				return edd_set_error( 'invalid_mobile', __( 'Please enter a valid  mobile number', "payamito-edd" ) );
			}

			if ( isset( $edd_payamito_otp_options["customer_sms_add_mobile_field_force_OTP"] ) && $edd_payamito_otp_options["customer_sms_add_mobile_field_force_OTP"] !== '1' ) {
				return;
			}

			if ( empty( $data['payamito_otp_field'] ) || ! \Payamito_OTP::payamito_validation_session( $data['payamito_mobile_field'], $data['payamito_otp_field'],'payamito_edd' ) ) {
				return edd_set_error( 'invalid_website', __( 'Cannot verify mobile number', "payamito-edd" ) );
			}

			return;
		}

		// Store custom field data in the payment meta
		function edd_store_custom_fields( $payment_meta )
		{
			global $edd_payamito_otp_options;

			if ( $edd_payamito_otp_options["customer_sms_add_mobile_field_enter"] !== '1' ) {
				return $payment_meta;
			}
			$payment_meta['mobile'] = isset( $_POST['payamito_mobile_field'] ) ? sanitize_text_field( $_POST['payamito_mobile_field'] ) : 'None';

			if ( isset( $_POST['edd-user-id'] ) && isset( $_POST['payamito_mobile_field'] ) ) {
				$id = sanitize_text_field( $_POST['edd-user-id'] );

				$mobile = sanitize_text_field( $_POST['payamito_mobile_field'] );

				update_user_meta( $id, " edd_payamito_mobile", $mobile );
			}

			return $payment_meta;
		}
	}
}
