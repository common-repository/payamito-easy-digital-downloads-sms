<?php

/**
 * Register an options panel.
 *
 * @package     Payamito
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Payamito_Edd_Options_Panel
{
	/**
	 * Holds the options panel controller.
	 *
	 * @var object
	 */
	protected $panel;

	public $statuses;

	/**
	 * Get things started.
	 */
	public function __construct()
	{
		add_filter( 'payamito_add_section', [ $this, 'register_settings' ], 1 );

		add_action( 'admin_footer', [ $this, "print_tags" ] );
		add_action( 'kianfr_' . 'payamito' . '_save_before', [ $this, 'option_save' ], 10, 1 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	public function admin_enqueue_scripts()
	{
		wp_enqueue_script( "Payamito_ModalJS", PAYAMITO_EDD_URL . "/includes/admin/assets/js/modal.js", [ 'jquery' ], false, true );
		wp_enqueue_style( "Payamito_ModalCSS", PAYAMITO_EDD_URL . "/includes/admin/assets/css/modal.css" );

		wp_enqueue_style( "Payamito_AdminCSS", PAYAMITO_EDD_URL . "/includes/admin/assets/css/admin-app.css" );
		wp_enqueue_script( "Payamito_AppAdmin", PAYAMITO_EDD_URL . "/includes/admin/assets/js/admin-app.js", [ 'jquery' ], false, true );
		wp_enqueue_script( "Payamito_Copy", PAYAMITO_EDD_URL . "/includes/admin/assets/js/copy.min.js", [ 'jquery' ], false, true );

		wp_enqueue_script( "Payamito_Edd_TooltipsterJS", PAYAMITO_EDD_URL . "/includes/admin/assets/js/tooltipster.main.min.js", [ 'jquery' ], false, true );
		wp_enqueue_style( "Payamito_Edd_TooltipsterCSS", PAYAMITO_EDD_URL . "/includes/admin/assets/css/tooltipster.main.min.css" );
	}

	/**
	 * Save Plugin options .
	 * Save all options  in external row in data base form payamito options   .
	 *
	 * @param 1 param
	 *
	 * @return void
	 * @since 1.0
	 */
	public function option_save( $options )
	{
		$user_type = [ 'administrator', 'customer', 'vendor' ];

		$init = [];

		$statuses = PEDD_Functions::status();

		foreach ( $statuses as $status ) {
			foreach ( $user_type as $type ) {
				if ( isset( $options['edd_sms_message'][ $type . "_" . $status . "_accordion" ] ) ) {
					array_push( $init, $options['edd_sms_message'][ $type . "_" . $status . "_accordion" ] );
				}
				if ( isset( $options['edd_sms_message'][ $type . "_" . $status . "_accordion" ] ) ) {
					unset( $options['edd_sms_message'][ $type . "_" . $status . "_accordion" ] );
				}
			}
		}
		foreach ( $init as $ini ) {
			foreach ( $ini as $index => $in ) {
				$options['edd_sms_message'][ $index ] = $in;
			}
		}
		update_option( 'edd_payamito_options', $options );
	}

	/**
	 * print tags for modal
	 */
	public function print_tags()
	{
		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != 'payamito' ) {
			return;
		}
		$tags = PEDD_Functions::get_tags();
		$html = "<div id='payamito-edd-modal' class='modal' >";
		$html .= "<div>";
		foreach ( $tags as $tag ) {
			$html .= "<div  class='payamito-tags-modal'><p class='payamito-edd-tag-modal' >" . $tag['tag'] . "</p>";
			$html .= "<span>" . $tag['desc'] . "</span></div>";
		}
		$html .= '</div>';
		echo $html;
	}

	public function register_settings( $section )
	{
		$edd_sms_settings = [
			'title'  => esc_html__( 'Easy Digital Downloads', 'payamito-edd' ),
			'fields' => [
				[
					'id'         => 'edd_sms_otp',
					'type'       => 'accordion',
					'title'      => esc_html__( 'OTP', 'payamito-edd' ),
					'accordions' => [
						[
							'title'  => esc_html__( 'OTP', 'payamito-edd' ),
							'fields' => [
								[
									'id'    => 'edd_sms_otp_active',
									'type'  => 'switcher',
									'title' => esc_html__( 'Active', 'payamito-edd' ),

								],
								[
									'type'       => 'notice',
									'style'      => 'warning',
									'content'    => esc_html__( '"توجه" توصیه می شود گزینه ارسال پترن را انتخاب نمایید و در صورتیکه با این بخش آشنایی ندارید با پشتیبانی پیامیتو در ارتباط باشید', 'payamito-edd' ),
									'dependency' => [ "edd_sms_otp_active", '==', 'true' ],
									'class'      => 'pattern_background',
								],
								[
									'id'         => 'edd_sms_otp_active_p',
									'type'       => 'switcher',
									'title'      => payamito_dynamic_text( 'pattern_active_title' ),
									'desc'       => payamito_dynamic_text( 'pattern_active_desc' ),
									'help'       => payamito_dynamic_text( 'pattern_active_help' ),
									'dependency' => [ "edd_sms_otp_active", '==', 'true' ],
									'class'      => 'pattern_background',

								],
								[
									'id'         => 'edd_sms_otp_p',
									'type'       => 'text',
									'title'      => payamito_dynamic_text( 'pattern_ID_title' ),
									'desc'       => payamito_dynamic_text( 'pattern_ID_desc' ),
									'help'       => payamito_dynamic_text( 'pattern_ID_help' ),
									'dependency' => [ "edd_sms_otp_active|edd_sms_otp_active_p", '==|==', 'true|true' ],
									'class'      => 'pattern_background',

								],
								[
									'id'         => 'edd_sms_otp_sms_repeater',
									'type'       => 'repeater',
									'title'      => payamito_dynamic_text( 'pattern_Variable_title' ),
									'desc'       => payamito_dynamic_text( 'pattern_Variable_desc' ),
									'help'       => payamito_dynamic_text( 'pattern_Variable_help' ),
									'max'        => '2',
									'dependency' => [ "edd_sms_otp_active|edd_sms_otp_active_p", '==|==', 'true|true' ],
									'class'      => 'pattern_background',
									'fields'     => [
										[
											'id'          => 'edd_sms_opt_tags',
											'placeholder' => esc_html__( "Tags", "payamito-edd" ),
											'type'        => 'select',
											'class'       => 'pattern_background',

											'options' => [
												"{OTP}"      => esc_html__( 'OTP', 'payamito-edd' ),
												"{sitename}" => esc_html__( 'Wordpress title', 'payamito-edd' ),
												'class'      => 'pattern_background',

											],
										],
										[
											'id'          => 'edd_sms_otp_user_otp',
											'type'        => 'number',
											'placeholder' => esc_html__( "Your tag", "payamito-edd" ),
											'class'       => 'pattern_background',
											'default'     => 0,

										],
									],
								],
								[
									'id'         => 'edd_sms_otp_sms',
									'title'      => payamito_dynamic_text( 'send_content_title' ),
									'desc'       => payamito_dynamic_text( 'send_content_desc' ),
									'help'       => payamito_dynamic_text( 'send_content_help' ),
									'type'       => 'textarea',
									'dependency' => [ "edd_sms_otp_active|edd_sms_otp_active_p", '==|!=', 'true|true' ],
									'class'      => 'pattern_background',

								],
								[
									'id'         => 'customer_sms_add_mobile_field_enter',
									'title'      => esc_html__( 'Force mobile number ', 'payamito-edd' ),
									'desc'       => esc_html__( 'Force users to Enter mobile number  ', 'payamito-edd' ),
									'dependency' => [ "edd_sms_otp_active", '==', 'true' ],
									'type'       => 'switcher',
								],
								[
									'id'         => 'customer_sms_add_mobile_field_force_OTP',
									'title'      => esc_html__( 'Forcing OTP ', 'payamito-edd' ),
									'desc'       => esc_html__( 'Force users to mobile confirmation ', 'payamito-edd' ),
									'dependency' => [ "edd_sms_otp_active", '==', 'true' ],

									'type' => 'switcher',
								],

								[
									'id'         => 'edd_sms_number_of_code',
									'title'      => esc_html__( 'Number of OTP code', 'payamito-edd' ),
									'desc'       => esc_html__( 'Number of OTP code that you want send for user', 'payamito-edd' ),
									'type'       => 'select',
									'dependency' => [ "edd_sms_otp_active", '==', 'true' ],
									'options'    => apply_filters( "edd_payamito_again_send_number", [
										"4"  => "4",
										"5"  => "5",
										"6"  => "6",
										"7"  => "7",
										"8"  => "8",
										"9"  => "9",
										"10" => "10",
									] ),
								],
								[
									'id'         => 'edd_sms_again_send_time',
									'title'      => esc_html__( 'Send Again', 'payamito-edd' ),
									'desc'       => esc_html__( 'When you want the user to re-request OTP.', 'payamito-edd' ),
									'type'       => 'select',
									'dependency' => [ "edd_sms_otp_active", '==', 'true' ],
									'options'    => apply_filters( "edd_payamito_again_send_time", [
										"30"  => "30",
										"60"  => "60",
										"90"  => "90",
										"120" => "120",
									] ),
								],
							],
						],
					],
				],
				[
					'id'    => 'edd_sms_message',
					'type'  => 'tabbed',
					'title' => esc_html__( 'Message', 'payamito-edd' ),
					'tabs'  => $this->tabs(),
				],
			],
		];
		array_push( $section, $edd_sms_settings );

		return $section;
	}

	public function tabs()
	{
		$tabs   = [];
		$tabs[] = $this->admin_tab();
		$tabs[] = $this->customer_tab();
		if ( $this->is_installed_edd_fes() ) {
			$tabs[] = $this->vendor_tab();
		}

		return apply_filters( 'edd_payamito_tabs', $tabs );
	}

	public function admin_tab()
	{
		$admin_tab = [
			'title'  => esc_html__( 'Admin', 'payamito-edd' ),
			'fields' => [
				[
					'type'    => 'heading',
					'content' => esc_html__( 'Administrator Message', 'payamito-edd' ),
				],

				[
					'id'    => 'administrator_sms_active',
					'title' => esc_html__( 'Active', 'payamito-edd' ),
					'desc'  => esc_html__( 'Are you want send sms to admin ', 'payamito-edd' ),
					'type'  => 'switcher',
				],
				$this->option_get_admin_phone_number(),
			],
		];
		$statuses  = payamito_edd()->functions::status();

		foreach ( $statuses as $status ) {
			array_push( $admin_tab['fields'], $this->set_status_field( 'administrator', $status ) );
		}

		return apply_filters( 'edd_payamito_admin_tab', $admin_tab );
	}

	public function customer_tab()
	{
		$customer_tab = [
			'title'  => esc_html__( 'Customer', 'payamito-edd' ),
			'fields' => [
				[
					'type'    => 'heading',
					'content' => esc_html__( 'Customer Message', 'payamito-edd' ),
				],
				[
					'id'    => 'customer_sms_active',
					'title' => esc_html__( 'Active', 'payamito-edd' ),
					'desc'  => esc_html__( 'Are you want send sms to customer ', 'payamito-edd' ),
					'type'  => 'switcher',
				],
				[
					'id'         => 'customer_meta_key',
					'title'      => esc_html__( 'Meta keys', 'payamito-edd' ),
					'desc'       => esc_html__( 'Customer meta key plugin', 'payamito-edd' ),
					'type'       => 'text',
					'dependency' => [ "customer_sms_active", '==', 'true' ],
				],
			],
		];
		$statuses     = payamito_edd()->functions::status();

		foreach ( $statuses as $status ) {
			$customer_tab['fields'][] = $this->set_status_field( 'customer', $status );
		}

		return apply_filters( 'edd_payamito_customer_tab', $customer_tab );
	}

	public function vendor_tab()
	{
		$vendor_tab = [
			'title'  => esc_html__( 'Vendor', 'payamito-edd' ),
			'fields' => [
				[
					'type'    => 'heading',
					'content' => esc_html__( 'Vendor Message', 'payamito-edd' ),
				],
				[
					'id'    => 'vendor_meta_key',
					'title' => esc_html__( 'Meta keys', 'payamito-edd' ),
					'desc'  => esc_html__( 'Vendor meta key', 'payamito-edd' ),
					'type'  => 'text',
				],
				[
					'id'    => 'vendor_sms_active',
					'title' => esc_html__( 'Active', 'payamito-edd' ),
					'desc'  => esc_html__( 'Are you want send sms to vendors ', 'payamito-edd' ),
					'type'  => 'switcher',
				],
			],
		];
		$statuses   = payamito_edd()->functions::status();

		foreach ( $statuses as $status ) {
			$vendor_tab['fields'][] = $this->set_status_field( 'vendor', $status );
		}

		return apply_filters( 'edd_payamito_vendor_tab', $vendor_tab );
	}

	public function is_installed_edd_fes()
	{
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'edd-fes/edd-fes.php' );
	}

	public function option_set_pattern( $user_type, $status )
	{
		return [
			'id' => $user_type . '_' . $status . '_pattern_message',

			'type' => 'repeater',

			'title'      => payamito_dynamic_text( 'pattern_Variable_title' ),
			'desc'       => payamito_dynamic_text( 'pattern_Variable_desc' ),
			'help'       => payamito_dynamic_text( 'pattern_Variable_help' ),
			'max'        => '15',
			'class'      => "edd-payamito-repeater",
			'dependency' => [ $user_type . "_" . $status . "_active_p", '==', 'true' ],

			'fields' => [
				[
					'id'          => $user_type . '_' . $status . '_pattern_tags',
					'type'        => 'select',
					'placeholder' => esc_html__( "Select tag", "payamito-edd" ),
					'options'     => $this->get_tags(),
					'class'       => 'pattern_background',
				],
				[
					'id'          => $user_type . '_' . $status . '_user_tag',
					'type'        => 'number',
					'placeholder' => esc_html__( "Your tag", "payamito-edd" ),
					'class'       => 'pattern_background',
					'default'     => 0,
				],
			],
		];
	}

	public function option_get_admin_phone_number()
	{
		return [
			'id'    => 'admin_phone_number_repeater',
			'type'  => 'repeater',
			'title' => esc_html__( "phone number", "payamito-edd" ),
			'max'   => '100',

			'dependency' => [ "administrator_sms_active", '==', 'true' ],
			'fields'     => [
				[
					'id'          => 'admin_phone_number',
					'type'        => 'text',
					'placeholder' => esc_html__( "Phone number ", "payamito-edd" ),
					'icon'        => 'fa fa-mobile',
					'validate'    => 'kianfr_validate_email',
					'class'       => 'edd_payamito-phone-number ',
					'attributes'  => [
						'type'      => 'tel',
						'maxlength' => 11,
						'minlength' => 11,
						"pattern"   => "[0-9]{3}-[0-9]{3}-[0-9]{4}",
					],
				],
			],
		];
	}

	public function get_tags()
	{
		return [
			"{billing_address}" => esc_html__( " Billing address", 'payamito-edd' ),
			"{download_list}"   => esc_html__( " Download list", 'payamito-edd' ),
			"{date}"            => esc_html__( "Date", 'payamito-edd' ),
			"{file_urls}"       => esc_html__( "  File urls", 'payamito-edd' ),
			"{fullname}"        => esc_html__( " Fullname", 'payamito-edd' ),
			"{subtotal}"        => esc_html__( " Subtotal", 'payamito-edd' ),
			"{payment_id}"      => esc_html__( " Payment id", 'payamito-edd' ),
			"{price}"           => esc_html__( " Price", 'payamito-edd' ),
			"{name} "           => esc_html__( " Name", 'payamito-edd' ),
			"{user_email}"      => esc_html__( "User email", 'payamito-edd' ),
			"{tax}"             => esc_html__( "Tax", 'payamito-edd' ),
			"{receipt_id}"      => esc_html__( " Receipt id", 'payamito-edd' ),
			"{sitename}"        => esc_html__( " Sitename", 'payamito-edd' ),
			"{discount_codes}"  => esc_html__( " Discount codes", 'payamito-edd' ),
		];
	}

	public function set_status_field( $user_type, $status )
	{
		$title  = "";
		$active = __( "Active", "payamito-edd" );
		switch ( $status ) {
			case "pending":
				$title = __( "Pending", "payamito-edd" );
				break;
			case "publish":
				$title = __( "Publish", "payamito-edd" );
				break;
			case "refunded":
				$title = __( "Refunded", "payamito-edd" );
				break;
			case "failed":
				$title = __( "Failed", "payamito-edd" );
				break;
			case "abandoned":
				$title = __( "Abandoned", "payamito-edd" );
				break;
			case "revoked":
				$title = __( "Revoked", "payamito-edd" );
				break;
			case "processing":
				$title = __( "Processing", "payamito-edd" );
				break;
		}

		return [
			'id'         => $user_type . '_' . $status . '_accordion',
			'type'       => 'accordion',
			'dependency' => [ $user_type . "_sms_active", '==', 'true' ],
			'accordions' => [
				[
					'title'  => esc_html__( ucfirst( $title ), 'payamito-edd' ),
					'fields' => [
						[
							'id'    => $user_type . "_" . $status . "_payment_active",
							'title' => ucfirst( $title ) . " " . $active,
							'type'  => 'switcher',
						],
						[
							'type'    => 'notice',
							'style'   => 'warning',
							'content' => esc_html__( '"توجه" توصیه می شود گزینه ارسال پترن را انتخاب نمایید و در صورتیکه با این بخش آشنایی ندارید با پشتیبانی پیامیتو در ارتباط باشید', 'payamito-edd' ),
							'class'   => 'pattern_background',
						],
						[

							'id'    => $user_type . "_" . $status . "_active_p",
							'type'  => 'switcher',
							'title' => payamito_dynamic_text( 'pattern_active_title' ),
							'desc'  => payamito_dynamic_text( 'pattern_active_desc' ),
							'help'  => payamito_dynamic_text( 'pattern_active_help' ),
							'class' => 'pattern_background',
						],
						[

							'id'         => $user_type . "_" . $status . "_p",
							'type'       => 'text',
							'title'      => payamito_dynamic_text( 'pattern_ID_title' ),
							'desc'       => payamito_dynamic_text( 'pattern_ID_desc' ),
							'help'       => payamito_dynamic_text( 'pattern_ID_help' ),
							'dependency' => [ $user_type . "_" . $status . "_active_p", '==', 'true' ],
							'class'      => 'pattern_background',
						],
						$this->option_set_pattern( $user_type, $status ),
						[
							'id'         => $user_type . "_" . $status . "_text",
							'title'      => payamito_dynamic_text( 'send_content_title' ),
							'desc'       => payamito_dynamic_text( 'send_content_desc' ),
							'help'       => payamito_dynamic_text( 'send_content_help' ),
							'default'    => esc_html__( 'مشتری گرامی سفارش با مبلغ { price } و وضعیت سفارش در حالت {status} ایجاد شد. ', 'payamito-edd' ),
							'type'       => 'textarea',
							'dependency' => [ $user_type . "_" . $status . "_active_p", '!=', 'true' ],
							'class'      => 'pattern_background',
						],
						[
							'type'       => 'callback',
							'class'      => "open-call-back",
							'dependency' => [ $user_type . "_" . $status . "_active_p", '!=', 'true' ],
							'function'   => 'payamito_print_tags',
						],
					],
				],
			],
		];
	}
}

function payamito_print_tags()
{
	echo "<h3 class='payamito-tags payamito-edd-open-modal ' >" . esc_html__( 'Tags', 'payamito-edd' ) . "</h3>";
}
