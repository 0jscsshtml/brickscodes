<?php

/**
 * The file that defines the core plugin class
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Brickscodes {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */
	protected $loader;
	protected $plugin_name;
	protected $version;
	protected $options;
	
	public function __construct() {
		if ( defined( 'BRICKSCODES_VERSION' ) ) {
			$this->version = BRICKSCODES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		
		if ( defined( 'BRICKSCODES_NAME' ) ) {
			$this->plugin_name = BRICKSCODES_NAME;
		} else {
			$this->plugin_name = 'brickscodes';
		}
			
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bricksfree_Loader. Orchestrates the hooks of the plugin.
	 * - Bricksfree_i18n. Defines internationalization functionality.
	 * - Bricksfree_Admin. Defines all hooks for the admin area.
	 * - Bricksfree_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once BRICKSCODES_PLUGIN_DIR . 'includes/brickscodes-loader.php';
		
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once BRICKSCODES_PLUGIN_DIR . 'includes/brickscodes-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once BRICKSCODES_PLUGIN_DIR . 'admin/brickscodes-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once BRICKSCODES_PLUGIN_DIR . 'public/brickscodes-public.php';
		require_once BRICKSCODES_PLUGIN_DIR . 'includes/brickscodes-helpers.php';
		require_once BRICKSCODES_PLUGIN_DIR . 'includes/brickscodes-globals.php';
		
		$this->loader = new Brickscodes_Loader();
		
	}
	
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Brickscodes_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() {
		$plugin_i18n = new Brickscodes_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'bc_load_plugin_textdomain' );
	}
	
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Brickscodes_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->options = get_option('brickscodes');
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( (!is_plugin_active('advanced-custom-fields/acf.php') && !file_exists( WP_PLUGIN_DIR . '/advanced-custom-fields/acf.php' )) &&
			(!is_plugin_active('advanced-custom-fields-pro/acf.php') && !file_exists( WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php' )) && 
			!class_exists('ACF') && isset($this->options['bc_options_page_id']) && !empty($this->options['bc_options_page_id'])
		) {
			require_once BRICKSCODES_ACF_PATH . 'acf.php';
			add_filter( 'acf/settings/show_admin', '__return_false' );
			add_filter( 'acf/settings/show_updates', '__return_false', 100 );
			$this->loader->add_action( 'acf/settings/url', $plugin_admin, 'bc_include_acf', 10, 1 );
		}

		if ( class_exists( 'ACF' ) && isset($this->options['bc_options_page_id']) && !empty($this->options['bc_options_page_id']) ) {
			if ( isset($this->options['bc_plugin_activated']) && $this->options['bc_plugin_activated'] === true ) {
				$this->loader->add_action( 'admin_init', $plugin_admin, 'bc_import_options_field', 10, 1 );
			}
			$this->loader->add_filter( 'use_block_editor_for_post', $plugin_admin, 'bc_disable_gutenberg_on_settings_page', 5, 2);
			$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'bc_hide_settings_page', 10, 1);
			$this->loader->add_action('admin_menu', $plugin_admin, 'bc_add_site_settings_to_menu', 10, 1);
			$this->loader->add_filter('parent_file', $plugin_admin, 'bc_higlight_custom_settings_page', 10, 1);
			$this->loader->add_action('admin_title', $plugin_admin, 'bc_edit_site_settings_title', 10, 1);
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'bc_enqueue_custom_admin_styles', 10, 1);					
			$this->loader->add_filter('acf/prepare_field', $plugin_admin, 'bc_prepare_acf_fields', 10, 1);
			$this->loader->add_action('acf/save_post', $plugin_admin, 'bc_options_save_actions', 5, 1);
			
			if ( (is_plugin_active('advanced-custom-fields/acf.php') && file_exists( WP_PLUGIN_DIR . '/advanced-custom-fields/acf.php' )) ||
				(is_plugin_active('advanced-custom-fields-pro/acf.php') && file_exists( WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php' ))
			) {
				$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'bc_hide_acf_field_group', 10, 1);
			}
		}
		
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'bc_enqueue_scripts_in_core', 999, 1);	
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'bc_confirm_deactivate', 10, 1);
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'bc_plugin_row_meta', 10, 2 );	
		$this->loader->add_action( 'init', $plugin_admin, 'bc_include_dependencies', 10, 1 );
	}
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 * 
	 */
	private function define_public_hooks() {
		
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 */
	public function run() {
		$this->loader->run();
	}
	
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 */
	public function get_version() {
		return $this->version;
	}
}