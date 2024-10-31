<?php

namespace Payamito\Edd;

/**
 *  Class EDD
 *
 * @package Payamito
 * @since   1.0.0
 */
defined( 'ABSPATH' ) || exit;

class EDD
{

	/**
	 * The single instance of the class.
	 *
	 * @since 1.1.2
	 */

	protected static $instance = null;

	public $gateway;

	public $status;

	public static function get_instance()
	{
		if ( is_null( self::$instance ) && ! ( self::$instance instanceof User ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct()
	{
		add_action( "edd_update_payment_status", [ &$this, "Status" ], 10, 3 );
	}

	/**
	 * Main Plugin function send sms.
	 *Activated statuses in this function, they are  ready to send SMS
	 *
	 * @param Not $ID param
	 *
	 * @return void
	 * @since 1.0
	 * @static
	 */
	public function Status( $ID, $new, $old )
	{
		if ( $new == $old ) {
			return;
		}
		$count = 0;

		$new === 'complete' ? $new = 'publish' : $new = $new;

		$this->status = payamito_edd()->user->status();

		if ( $count > 1 ) {
			return;
		}
		foreach ( $this->status as $index => $st ) {
			if ( $index === "administrator" && $st['active'] == true ) {
				$mobiles = payamito_edd()->user->admin_mobile();

				$this->EDD_Send( $this->status["administrator"], $mobiles, $ID, "administrator", $new );
			}
			if ( $index == "customer" && $st['active'] == true ) {
				$id = get_post_meta( $ID, "_edd_payment_user_id", true );

				$mobiles = payamito_edd()->user->customer_mobile( $id );

				if ( ! is_null( $mobiles ) ) {
					$this->EDD_Send( $this->status["customer"], [ $mobiles ], $ID, "customer", $new );
				}
			}

			if ( $index == "vendor" && $st['active'] == true ) {
				$data = get_post_meta( $ID, "_edd_payment_meta", true );

				$downloads = $data["downloads"];

				$mobiles = payamito_edd()->user->vendor_mobile( $downloads );

				$this->EDD_Send( $this->status["vendor"], $mobiles, $ID, "vendor", $new );
			}
		}
		$count ++;
	}

	/**
	 * finally sms is send
	 *
	 * @param status $status ,mobiles,ID,flag,new
	 *
	 * @return void
	 * @since 1.0
	 * @static
	 */
	public function EDD_Send( $status, $mobiles, $ID, $flag, $new )
	{
		if ( ! is_array( $mobiles ) && count( $mobiles ) <= 1 ) {
			return;
		}
		foreach ( $status as $index => $st ) {
			foreach ( $mobiles as $mobile ) {
				if ( $index != "active" && $st === true ) {
					if ( $new === $index ) {
						if ( is_numeric( $mobile ) ) {
							$pattern = payamito_edd()->functions::get_patterns( $flag, $index );

							$message = payamito_edd()->functions::get_message( $flag, $index );

							payamito_edd()->send->setParams( $mobile, $message, $pattern, $ID );

							payamito_edd()->send->Send();
						}
					}
				}
			}
		}
	}
}
