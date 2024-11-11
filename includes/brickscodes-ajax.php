<?php
class Brickscodes_Ajax {
	public function __construct() {
		// update exisiting query
		add_action('wp_ajax_bc_update_query_settings', [$this, 'bc_update_query_settings'], 10, 1);
		// new query
		add_action('wp_ajax_bc_add_new_query_settings', [$this, 'bc_add_new_query_settings'], 10, 1);
		// builder save action if deteced query deleted from history
		add_action('wp_ajax_bc_refresh_query_settings', [$this, 'bc_refresh_query_settings'], 10, 1);
		// Every save action on Core Framework setting page
		add_action('wp_ajax_bc_get_core_classes_variables', [$this, 'bc_get_core_classes_variables'], 10, 1);
		// Deactivate plugin and keep Core Framework
		add_action('wp_ajax_bc_import_core_classes_variables', [$this, 'bc_import_core_classes_variables'], 10, 1);
		// Deactivate plugin and delete Core Framework
		add_action('wp_ajax_bc_deactivate_plugin', [$this, 'bc_deactivate_plugin'], 10, 1);
		// export template
		add_action('wp_ajax_bc_export_template', [$this, 'bc_export_template'], 10, 1);
		add_action('wp_ajax_nopriv_bc_export_template', [$this, 'bc_export_template'], 10, 1);
		// get submission form fields
		add_action('wp_ajax_bc_get_submission_form_fields', [$this, 'bc_get_submission_form_fields'], 10, 1);
	}
	
	public function bc_update_query_settings() {	
		Brickscodes_Helpers::bc_get_all_queries_args('update');
	}
	
	public function bc_add_new_query_settings() {	
		Brickscodes_Helpers::bc_get_all_queries_args('new');
	}
	
	public function bc_refresh_query_settings() {
		if ( !check_ajax_referer( 'bricks-nonce-builder', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}
		if (!current_user_can('administrator')) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}
		$cache_key = 'cached_' . md5('brickscodes' . '_bc_query_manager');
		delete_transient($cache_key);		
		Brickscodes_Helpers::bc_get_all_queries_args('all');
	}
	
	public function bc_get_core_classes_variables() {	
		$brickscodes_globals = Brickscodes_Globals::get_instance();
		$brickscodes_globals->bc_integrate_core_framework('ajax');
	}
	
	public function bc_import_core_classes_variables() {
		if ( ! check_ajax_referer( 'bc-nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$variables_pair = Brickscodes_Helpers::bc_get_core_variables_pair();
		$bricks_global_variables = get_option( 'bricks_global_variables', [] );
		$bricks_global_variables_categories = get_option( 'bricks_global_variables_categories', [] );

		// Add the core framework category if it doesn't exist
		$has_core_variable_category = array_search('bcgvcorefrm', array_column($bricks_global_variables_categories, 'id')) !== false;
		if ( ! $has_core_variable_category ) {
			$bricks_global_variables_categories[] = [
				'id' => 'bcgvcorefrm',
				'name' => 'Core Framework (BC)'
			];
			update_option( 'bricks_global_variables_categories', $bricks_global_variables_categories );
		}

		// Filter out existing core variables from global variables
		$bricks_global_variables = array_values(array_filter($bricks_global_variables, function($variable) {
			return $variable['category'] !== 'bcgvcorefrm';
		}));

		// Add new core variables if the pair is not empty
		if ( ! empty( $variables_pair ) ) {
			foreach ( $variables_pair as $key => $value ) {
				$bricks_global_variables[] = [
					'id' => Brickscodes_Helpers::bc_check_unique_id_name( $bricks_global_variables, '', false ),
					'name' => substr( $key, 2 ),
					'value' => $value,
					'category' => 'bcgvcorefrm'
				];
			}
		}

		update_option( 'bricks_global_variables', $bricks_global_variables );
		$this->bc_deactivate_plugin( 'keep' );

		wp_send_json_success( array(
			'message' => 'Core Frameworks Variables Imported Successfully.'
		));
	}

	public function bc_deactivate_plugin($type = '') {
		// Initial nonce and capability checks
		if ($type !== 'keep' && isset($_POST['data']) && $_POST['data'] === 'delete') {
			if (!check_ajax_referer('bc-nonce', 'nonce', false)) {
				wp_send_json_error(['message' => 'Invalid nonce']);
			}
			if (!current_user_can('administrator')) {
				wp_send_json_error(['message' => 'Unauthorized']);
			}
		}

		// Get options and set field group key and page ID
		$options = get_option('brickscodes', []);
		$page_id = isset($options['bc_options_page_id']) ? intval($options['bc_options_page_id']) : 0;
		$field_group_key = isset($options['bc_options_acf_group_key']) ? sanitize_text_field($options['bc_options_acf_group_key']) : (isset($options['bc_options_acf_group_id']) ? intval($options['bc_options_acf_group_id']) : '');

		// Process field group and associated fields
		if (class_exists('ACF') && $field_group_key) {
			if ($field_group = acf_get_field_group($field_group_key)) {
				if ($fields = acf_get_fields($field_group['key'])) {
					foreach ($fields as $field) {
						delete_post_meta($page_id, $field['name']);
					}
				}
				acf_delete_field_group($field_group_key);
				error_log("Field group and all associated values deleted for post ID $page_id.");
			} else {
				error_log("Field group not found for key: $field_group_key");
			}
		}

		// Delete post if page_id exists
		if ($page_id) {
			wp_delete_post($page_id, true);
		}

		// Update options after unsetting specific keys
		unset($options['bc_options_page_id'], $options['bc_options_acf_group_key'], $options['bc_options_acf_group_id']);
		$options['bc_core_integration'] = [];
		update_option('brickscodes', $options);

		// Handle global classes
		$global_classes = get_option('bricks_global_classes', []);
		$global_classes_locked = get_option('bricks_global_classes_locked', []);
		$global_classes_categories = get_option('bricks_global_classes_categories', []);

		// Filter and update classes
		$global_classes = array_filter($global_classes, function($class) {
			return !in_array($class['name'], ['bc-hide-in-canvas', 'bc-hide-in-frontend']);
		});
		update_option('bricks_global_classes', array_values($global_classes));
		
		// Filter categories
		$global_classes_categories = array_filter($global_classes_categories, fn($category) => $category['id'] !== 'brickscodes');
		update_option('bricks_global_classes_categories', array_values($global_classes_categories));

		// Unlock specific classes if they were previously locked
		$global_classes_locked = array_diff($global_classes_locked, array_column($global_classes, 'id'));
		update_option('bricks_global_classes_locked', array_values($global_classes_locked));

		// Additional cleanup for deletion action
		if ($type !== 'keep' && isset($_POST['data']) && $_POST['data'] === 'delete') {
			$this->bc_cleanup_additional_options();
			wp_send_json_success(['message' => 'Core Frameworks deleted Successfully.']);
		}
	}

	// Additional cleanup function
	private function bc_cleanup_additional_options() {
		$color_palettes = array_filter(get_option('bricks_color_palette', []), fn($palette) => !str_ends_with($palette['id'], '_bccpcf'));
		update_option('bricks_color_palette', array_values($color_palettes));

		$global_classes = array_filter(get_option('bricks_global_classes', []), fn($class) => !isset($class['category']) || $class['category'] !== 'bcgccorefrm');
		update_option('bricks_global_classes', array_values($global_classes));

		$global_classes_categories = array_filter(get_option('bricks_global_classes_categories', []), fn($category) => !in_array($category['id'], ['brickscodes', 'bcgccorefrm']));
		update_option('bricks_global_classes_categories', array_values($global_classes_categories));

		delete_transient('cached_' . md5('brickscodes' . '_bc_core_integration'));
	}
	
	public function bc_export_template() {
		if (!check_ajax_referer('bc-nonce', 'nonce', false)) {
			wp_send_json_error(['message' => 'Invalid nonce']);
		}
		if ( !is_user_logged_in() ) {
			wp_send_json_error(['message' => 'User is logged out.']);
		}

		$template_id = isset($_POST['id']) ? $_POST['id'] : 0;
		$template_data = \Bricks\Templates::get_template_by_id( $template_id );
		
		if (empty($template_id || $template_data === false)) {
			wp_send_json_error(['message' => 'export_template:error: Template not found. ']);
		}
		
		Brickscodes_Helpers::bc_export_template($template_id);
	}
	
	public function bc_get_submission_form_fields() {
		if (!check_ajax_referer('bricks-nonce-builder', 'nonce', false)) {
			wp_send_json_error(array('message' => 'Invalid nonce'));
		}
		if (!current_user_can('administrator')) {
			wp_send_json_error(array('message' => 'Unauthorized'));
		}
		
		$form_id = isset($_POST['groupKey']) ? sanitize_text_field($_POST['groupKey']) : '';
		$global_id = isset( $_POST['globalId'] ) ? $_POST['globalId'] : 0;
		$options = [];
			
		if ( !empty($form_id) ) {
			$global_id  = !empty($global_id) ? $global_id : $form_id;
			$post_id = \Bricks\Integrations\Form\Submission_Database::get_post_id( $form_id );
			$formSettings = \Bricks\Integrations\Form\Submission_Database::get_form_settings( $post_id, $form_id, $global_id );
			if ( !empty($formSettings['fields']) ) {
				foreach($formSettings['fields'] as $field) {
					if ($field['type'] !== 'html') {
						$options[$field['id']] = isset($field['label']) && !empty($field['label']) ? $field['label'] : "form-field-{$field['id']}";
					}
				}
			}
		}
			
		wp_send_json_success([
			'fields' => $options
		]);
	}
}
