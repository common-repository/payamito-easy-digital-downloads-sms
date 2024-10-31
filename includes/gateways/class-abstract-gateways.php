<?php

/**
 * Abstract Class Gateways
 *
 * @package " Payamito_Edd
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

abstract class Payamito_Edd_Send_Abstract
{
	/**
	 * user variable
	 *
	 * @since 1.0
	 * @var int
	 */
	public $user = null;

	/**
	 * OTP variable
	 *
	 * @since 1.0
	 * @var int
	 */
	public $OTP;

	/**
	 * mobile variable
	 *
	 * @since 1.0
	 * @var int
	 */
	public $mobile;

	/**
	 * mobile variable
	 *
	 * @since 1.0
	 * @var string
	 */
	public $message;

	/**
	 * mobile variable
	 *
	 * @since 1.0
	 * @var string
	 */
	public $tags_value;

	/**
	 * mobile variable
	 *
	 * @since 1.0
	 * @var array
	 */
	public $tags;

	/**
	 * payment  id variable
	 *
	 * @since 1.0
	 * @var int
	 */
	public $payment_id;

	/**
	 * pattern id variable
	 *
	 * @since 1.0
	 * @var int
	 */
	public $pattern_id;
	/**
	 * success variable
	 *
	 * @since 1.0
	 * @var boolean
	 */
	public $success = false;

	private function init_tags()
	{
		$this->tags       = [
			'{OTP}' => '{OTP}',
			'{name}',
			'{fullname}',
			'{username}',
			'{user_email}',
			'{billing_address}',
			'{subtotal}',
			'{tax}',
			'{price}',
			'{payment_id}',
			'{receipt_id}',
			'{payment_method}',
			'{sitename}',
		];
		$this->tags_value = [
			'{OTP}'             => $this->genarate_otp(),
			'{name}'            => edd_email_tag_first_name( $this->payment_id ),
			'{fullname}'        => edd_email_tag_fullname( $this->payment_id ),
			'{username}'        => edd_email_tag_username( $this->payment_id ),
			'{user_email}'      => edd_email_tag_user_email( $this->payment_id ),
			'{billing_address}' => edd_email_tag_billing_address( $this->payment_id ),
			'{subtotal}'        => edd_email_tag_subtotal( $this->payment_id ),
			'{tax}'             => edd_email_tag_tax( $this->payment_id ),
			'{price}'           => edd_email_tag_price( $this->payment_id ),
			'{payment_id}'      => edd_email_tag_payment_id( $this->payment_id ),
			'{receipt_id}'      => edd_email_tag_receipt_id( $this->payment_id ),
			'{payment_method}'  => edd_email_tag_payment_method( $this->payment_id ),
			'{sitename}'        => edd_email_tag_sitename( $this->payment_id ),
		];
	}

	/**
	 * Generate OTP
	 */
	final private function genarate_otp(): string
	{
		global $edd_payamito_otp_options;

		$count = $edd_payamito_otp_options['edd_sms_number_of_code'];

		$OTP = Payamito_OTP::payamito_generate_otp( $count );

		return $this->OTP = $OTP;
	}

	/**
	 * Generate message
	 */
	public function generate_message( $OTP = false )
	{
		if ( ! $OTP ) {
			$pattern_with_value = [];
			foreach ( $this->pattern_code as $index => $pattern ) {
				$value                                      = edd_do_email_tags( $pattern['tag'], $this->payment_id );
				$pattern_with_value[ $pattern['user_tag'] ] = $value;
			}

			return $pattern_with_value;
		} else {
			return $this->init_tags_OTP();
		}
	}

	/**
	 * Tags are ready
	 *And are set
	 *     * @param OTP
	 */
	public function pattern_message( $OTP = false )
	{
		if ( ! $OTP ) {
			$this->init_tags();
		} else {
			$this->init_tags_OTP();
		}
		$message = explode( "\n", trim( $this->message ) );

		$result = [];

		$tags_value = $this->tags_value;

		foreach ( $message as $m ) {
			$i = stripos( $m, "=" );

			if ( $i !== false ) {
				$tag = explode( "=", $m );

				foreach ( $tag as $index => $t ) {
					$i = stripos( $t, "{" );

					$i = stripos( $t, "}" );

					$t = trim( $t );

					$value = $tags_value[ $t ];

					if ( $i !== false && $value ) {
						if ( $index == 1 ) {
							$key = str_replace( [ "}", "{" ], "", $tag[0] );

							$result[ $key ] = $value;
						}

						if ( $index == 0 ) {
							$key            = str_replace( [ "}", "{" ], "", $tag[1] );
							$result[ $key ] = $value;
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Create message without pattern
	 *     * @param OTP
	 */
	public function generate_message_string( $OTP = false )
	{
		if ( ! $OTP ) {
			$message = edd_do_email_tags( $this->message, $this->payment_id );

			return $message;
		} else {
			$this->init_tags_OTP_string();

			$message = str_replace( $this->tags, $this->tags_value, $this->message );

			$message = str_replace( $this->tags, "", $message );

			$message = str_replace( "}", "", $message );

			$message = str_replace( "{", "", $message );

			return $message;
		}
	}

	/**
	 * init OTP tags without pattern
	 *
	 * @param OTP
	 */
	public function init_tags_OTP_string()
	{
		$this->tags       = [

			'{OTP}' => '{OTP}',

			'{sitename}' => '{sitename}',
		];
		$this->tags_value = [

			'{OTP}' => $this->genarate_otp(),

			'{sitename}' => get_bloginfo( "name" ),
		];
	}

	/**
	 * init OTP tags with pattern
	 *
	 * @param OTP
	 */
	public function init_tags_OTP()
	{
		$pattern_with_value = [];

		foreach ( $this->pattern_code as $pattern ) {
			if ( $pattern['edd_sms_opt_tags'] == "{OTP}" ) {
				$pattern_with_value[ $pattern['edd_sms_otp_user_otp'] ] = $this->genarate_otp();
			}
			if ( $pattern['edd_sms_opt_tags'] == "{sitename}" ) {
				$pattern_with_value[ $pattern['edd_sms_otp_user_otp'] ] = get_bloginfo( 'name' );
			}
		}

		return $pattern_with_value;
	}
}
