<?php

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'PAYAMITO_PLUGIN_FILE' ) ) {
	define( 'PAYAMITO_PLUGIN_FILE', __FILE__ );
}

if ( ! class_exists( 'Payamito_Edd' ) ) :

	final class  Payamito_Edd
	{

		/**
		 * Plugin version.
		 *
		 * @var string
		 */

		public $version = '1.2.1';
		/**
		 * Plugin slag.
		 *
		 * @var string
		 */
		public static $slug = 'payamito_edd';

		/**
		 * The single instance of the class.
		 *
		 * @var Payamito_Edd
		 * @since 1.0
		 */

		protected static $instance = null;
		/**
		 * Minimum PHP version required
		 *
		 * @var string
		 */

		private $min_php = '7.0.0';

		/**
		 * User container
		 *
		 * @since 1.0
		 * @var object
		 */

		public $user;

		/**
		 * Gateway container
		 *
		 * @since 1.0
		 * @var object
		 */

		public $gateway;

		/**
		 * Session container
		 *
		 * @since 1.0
		 * @var object
		 */

		public $session;

		/**
		 * EDD container
		 *
		 * @since 1.0
		 * @var object
		 */

		public $edd;
		/**
		 * Options container
		 *
		 * @since 1.0
		 * @var object
		 */
		public $options;

		/**
		 * Functions container
		 *
		 * @since 1.0
		 * @var object
		 */

		public $functions;
		/**
		 * Mobile field container
		 *
		 * @since 1.0
		 * @var object
		 */
		public $mobile_field;

		/**
		 * Send container
		 *
		 * @since 1.0
		 * @var object
		 */

		public $send;

		public $core_version = '2.1.8';

		/**
		 * Main Plugin Instance.
		 * Ensures only one instance of Payamito_Plugin is loaded or can be loaded.
		 *
		 * @param Not param
		 *
		 * @since 1.0
		 * @static
		 */
		public static function get_instance()
		{
			if ( is_null( self::$instance ) && ! ( self::$instance instanceof Payamito_Edd ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor for the Payamito_Plugin class
		 * Sets up  load_textdomain
		 * within our plugin.
		 *     * @param Not param
		 */
		public function __construct()
		{
			$this->init();

			add_action( 'plugins_loaded', [ $this, 'plugin_setup' ], 1 );
		}

		/**
		 * Constructors plugin Setup
		 *
		 * @param Not param
		 */
		public function plugin_setup()
		{
			load_plugin_textdomain( 'payamito-edd', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/lang' );
		}

		/**
		 * Throw error on object clone
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 */
		public function __clone()
		{
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'payamito-edd' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class
		 * Attempting to wakeup an FES instance will throw a doing it wrong notice.
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 */
		public function __wakeup()
		{
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'payamito-edd' ), '1.0.0' );
		}

		/**
		 * Check if the PHP version is supported
		 *
		 * @return bool
		 */
		public function is_supported_php()
		{
			if ( version_compare( PHP_VERSION, $this->min_php, '<=' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * initialization Plugin  .
		 * The most important plugin function for initialization All the basic items are set in this function
		 *
		 * @param 0 param
		 *
		 * @return void
		 * @since 1.0
		 */
		public function init()
		{
			$this->init_includes();

			$this->get_options();

			$this->init_class();

			$this->add_action();
		}

		/**
		 * initialization Class Plugin  .
		 * Initialization of containers and Prototyping of classes
		 *
		 * @param 0 param
		 *
		 * @return void
		 * @since 1.0
		 */
		public function init_class()
		{
			add_action( 'plugins_loaded', [ $this, 'load_tgm_object' ] );
			if ( ! $this->is_edd_installed() ) {
				return;
			}
			require_once payamito_edd_load_core() . '/payamito.php';
			run_payamito();

			if ( ( isset( $_POST['action'] ) && $_POST['action'] == "kianfr_payamito_ajax_save" ) || isset( $_GET['page'] ) && $_GET['page'] == "payamito" ) {
				$this->options = new Payamito_Edd_Options_Panel;
			}

			if ( PAYAMITO_VERSION < '1.1.2' ) {
				return;
			}
			$this->user = Payamito\Edd\User::get_instance();

			$this->edd = Payamito\Edd\EDD::get_instance();

			$this->send = new Payamito_Edd_Send;

			$this->functions = new PEDD_Functions;

			Payamito\Edd\Mobile_Field::get_instance();

			Payamito\Edd\Ajax::get_instance();

			new  Payamito\Edd\Required();
		}

		public function load_tgm_object()
		{
			new Payamito\Edd\Required();
		}

		/**
		 * Initialize the actions  .
		 *
		 * @param 0 param
		 *
		 * @return void
		 * @since 1.0
		 */
		public function add_action()
		{
			add_action( 'plugins_loaded', [ $this, 'plugin_setup' ], 1 );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		/**
		 * Check whether EDD is installed and active
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function is_edd_installed()
		{
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' );
		}

		/**
		 *   enqueue  scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_scripts()
		{
			wp_enqueue_style( "Payamito_AppCSS", PAYAMITO_EDD_URL . "/assets/css/app.css" );

			wp_enqueue_script( "Payamito_AppJS", PAYAMITO_EDD_URL . "/assets/js/app.js", [ 'jquery' ], false, true );

			wp_enqueue_script( "Payamito_SpinnerJS", PAYAMITO_EDD_URL . "/assets/js/spinner.js", [ 'jquery' ], false, true );

			wp_enqueue_style( "Payamito_SpinnerCSS", PAYAMITO_EDD_URL . "/assets/css/spinner.css" );

			wp_enqueue_script( "Payamito_NotificationJS", PAYAMITO_EDD_URL . "/assets/js/notification.js", [ 'jquery' ], false, true );

			wp_enqueue_style( "Payamito_NotificationCSS", PAYAMITO_EDD_URL . "/assets/css/notification.css" );

			wp_enqueue_style( "Payamito_AppCSS", PAYAMITO_EDD_URL . "/assets/css/app.css", [], false, true );

			wp_localize_script( "Payamito_NotificationJS", "PayamitoGeneral", [
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'Payamito' ),
				"OTP_Success" => __( "Send OTP success", "payamito-edd" ),
				"OTP_Fail"    => __( "Send OTP failed", "payamito-edd" ),
				'Send'        => __( "Send request failed please contact with support team ", "payamito-edd" ),
				'OTP_Wrong'   => __( "OTP is wrong", "payamito-edd" ),
				'OTP_Correct' => __( "OTP is wrong", "payamito-edd" ),
				'invalid'     => __( "Mobile number is incorrect", "payamito-edd" ),
				'error'       => __( "Error", "payamito-edd" ),
				'success'     => __( "Success", "payamito-edd" ),
				"warning"     => __( "Warning", "payamito-edd" ),
				'enter'       => __( 'Enter OTP number ', 'payamito-edd' ),
				'second'      => __( 'Second', 'payamito-edd' ),
			] );
		}

		/**
		 *  get option settings
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function get_options()
		{
			$edd_payamito_options = get_option( 'edd_payamito_options' );

			$this->init_otp_options( $edd_payamito_options );
		}

		/**
		 *   init global option value
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function init_otp_options( $options )
		{
			if ( ! is_array( $options ) ) {
				return [];
			}
			global $edd_payamito_otp_options, $edd_payamito_messages_options;

			if ( isset( $options['edd_sms_otp'] ) ) {
				$edd_payamito_otp_options = $options['edd_sms_otp'];
			}
			if ( isset( $options['edd_sms_message'] ) ) {
				$edd_payamito_messages_options = $options['edd_sms_message'];
			}
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @return void
		 * @since  1.0.0
		 */
		private function init_includes()
		{
			require_once PAYAMITO_EDD_DIR . "/includes/functions.php";
			require_once PAYAMITO_EDD_DIR . "/includes/libraries/tgm/class-tgm-plugin-activation.php";
			require_once PAYAMITO_EDD_DIR . "/includes/class-functions.php";
			require_once PAYAMITO_EDD_DIR . "/includes/class-plugins-required.php";
			require_once PAYAMITO_EDD_DIR . "/includes/class-user.php";
			require_once PAYAMITO_EDD_DIR . "/includes/class-edd.php";
			require_once PAYAMITO_EDD_DIR . "/includes/admin/class-settings.php";
			include_once PAYAMITO_EDD_DIR . "/includes/gateways/class-abstract-gateways.php";
			include_once PAYAMITO_EDD_DIR . "/includes/gateways/api/class-send.php";
			require_once PAYAMITO_EDD_DIR . "/includes/class-mobile-field.php";
			require_once PAYAMITO_EDD_DIR . "/includes/class-ajax.php";
		}
	}

endif;
