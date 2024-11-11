<?php

/**
 * Fired during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 */
 
// Minimum Bricks Version 
if ( ! defined( 'MINIMUM_BRICKS_VERSION' ) ) {
	define( 'MINIMUM_BRICKS_VERSION', '1.9.6.1' );
}

// Minimum Php Version 
if ( ! defined( 'MINIMUM_PHP_VERSION' ) ) {
	define( 'MINIMUM_PHP_VERSION', '7.3' );
} 
 
class Brickscodes_Activator {
	public static function activate() {
		// Check Bricks theme is installed and activated		
		$theme = wp_get_theme();
		if ( ('Bricks' != $theme->name && 'Bricks' != $theme->parent_theme) && ! function_exists( 'bricks_is_builder' ) ) {
			self::brickscodes_plugin_dependency_error(__('Please install and activate Bricks theme.', 'brickscodes'));
		}

		// Check Bricks theme minimum version
		if ( ! version_compare( $theme->get( 'Version' ), MINIMUM_BRICKS_VERSION, '>=' ) ) {
			self::brickscodes_plugin_dependency_error(sprintf(__('Please update Bricks theme to minimum version %s.', 'brickscodes'), MINIMUM_BRICKS_VERSION));
		}

		// Check for PHP minimum version
		if ( version_compare( PHP_VERSION, MINIMUM_PHP_VERSION, '<' ) ) {
			self::brickscodes_plugin_dependency_error(sprintf(__('Please update PHP to minimum version %s.', 'brickscodes'), MINIMUM_PHP_VERSION));
		}

		$brickscodes_options = get_option('brickscodes');
		if ( !is_array($brickscodes_options) ) {
			$brickscodes_options = [];	
		}
		if ( !isset($brickscodes_options['bc_api_secret_keys']) ) {
			$brickscodes_options['bc_api_secret_keys'] 	= [];
		}
		if ( !isset($brickscodes_options['bc_query_manager']) ) {
			$brickscodes_options['bc_query_manager']	= [];
		}
		if ( !isset($brickscodes_options['bc_core_integration']) ) {
			$brickscodes_options['bc_core_integration'] = [];
		}
		
		$check_page_exist = get_page_by_path('brickscodes-options', 'OBJECT', 'page');
		if (empty($check_page_exist)) {
			$options_page_id = wp_insert_post(
				array(
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_title' => ucwords('Brickscodes Options'),
					'post_name' => sanitize_title('Brickscodes Options'),
					'post_status' => 'publish',
					'post_type' => 'page',
				)
			);		
			$brickscodes_options['bc_options_page_id'] = intval($options_page_id);
		} else {			
			$brickscodes_options['bc_options_page_id'] = intval($check_page_exist->ID);
		}
		
		$brickscodes_options['bc_plugin_activated'] = true;
		update_option('brickscodes', $brickscodes_options);

		// Check if WooCommerce is install and activated
/*		if( !class_exists( 'WooCommerce' ) ) {
			self::bricksfree_plugin_dependency_error(sprintf(__('Please install and Activate WooCommerce.', 'brickscodes')));
		}
*/
	}

	// Did not meet requirement. Activation failed.
	public static function brickscodes_plugin_dependency_error($message) {
		deactivate_plugins(plugin_basename( __FILE__ ));
		wp_die($message, __('Plugin dependency check', 'brickscodes'), array('back_link' => true));
	}

}
