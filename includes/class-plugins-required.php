<?php

namespace Payamito\Edd;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Required' ) ) {
	class Required
	{
		public $id;
		public $parent;
		public $slag;

		function __construct()
		{
			if ( class_exists( 'TGM_Plugin_Activation' ) ) {
				$this->id = 'payimitoeddsms';

				$this->slag = 'payimito_eddsms';

				$this->parent = 'plugins.php';
			}
			add_action( 'tgmpa_register', [ $this, 'required_plugins' ] );
		}

		public function required_plugins()
		{
			$plugins = [
				[
					'name'             => 'Easy Digital Downloads',
					'slug'             => "easy-digital-downloads",
					'force_activation' => true,
					'required'         => true,
					'version'          => '2.0.0',
					// E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
				],

			];
			$config  = [
				'id'           => $this->id,
				// Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '',
				// Default absolute path to bundled plugins.
				//	'menu'         => $this->slag, // Menu slug.
				//	'parent_slug'  => $this->parent,            // Parent menu slug.
				'capability'   => 'install_plugins',
				// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true,
				// Show admin notices or not.
				'dismissable'  => false,
				// If false, a user cannot dismiss the nag message.
				'is_automatic' => false,
				// Automatically activate plugins after installation or not.
				'dismiss_msg'  => __( ' Plugin Payamito-EDD requires the installation of Easy Digital Downloads', 'payamito-edd' ),

			];

			tgmpa( $plugins, $config );
		}
	}
}
