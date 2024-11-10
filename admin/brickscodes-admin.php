<?php

/**
 * The admin-specific functionality of the plugin.
 *

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Brickscodes_Admin {
	// The ID of this plugin.
	private $plugin_name;

	// The version of this plugin.
	private $version;
	private $options;
	private $upload_dir;
	private $is_upload_dir_filter_added;
	
	// Initialize the class and set its properties.
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name 					= $plugin_name;
		$this->version 						= $version;
		$this->options 						= get_option('brickscodes');
		$this->upload_dir 					= wp_upload_dir();
		$this->is_upload_dir_filter_added 	= false;
	}

	public function bc_include_acf($url) {
		return BRICKSCODES_ACF_URL;
	}
	
	private function bc_get_backup_data() {
		$backup_file_dir = $this->upload_dir['basedir'] . '/brickscodes/';
		$backup_files = glob($backup_file_dir . 'brickscode_options_backup_*.json');

		if (!empty($backup_files)) {
			$latest_backup_file = $backup_files[0];
		} else {
			$latest_backup_file = BRICKSCODES_PLUGIN_DIR . 'includes/acf/acf_import/brickscodes-options-fresh.json';
		}

		$backup_file_contents = file_get_contents($latest_backup_file);
		if ( $backup_file_contents === false ) {
			return [];
		}

		$backup_data = json_decode($backup_file_contents, true);
		return json_last_error() === JSON_ERROR_NONE && !empty($backup_data) ? $backup_data : [];
	}

	private function bc_apply_option_settings($backup_data) {
		$brickscodes_globals = null;

		foreach ($backup_data as $key => $value) {
			if ($key === 'field_66aa030c0b1ee') { // show hide elements
				$brickscodes_globals = $brickscodes_globals ?: Brickscodes_Globals::get_instance();
				$feature = $value == '1' ? 'option-enabled' : 'option-disabled';
				$brickscodes_globals->bc_add_show_hide_global_classes($feature);
			} elseif ($key === 'field_6704929404b2d') { // core framework integration
				$brickscodes_globals = $brickscodes_globals ?: Brickscodes_Globals::get_instance();
				$feature = $value == '1' ? 'option-enabled' : 'option-disabled';
				$brickscodes_globals->bc_integrate_core_framework($feature);
			} elseif ($key === 'field_66a9d4b86cf42') { // query manager
				$feature = $value == '1' ? 'option-enabled' : 'option-disabled';
				Brickscodes_Helpers::bc_get_all_queries_args($feature);
			}
		}
	}
	
	private function bc_deactivate_plugin($message) {
		deactivate_plugins(BRICKSCODES_PLUGIN_BASE);
		wp_die($message, __('Plugin dependency check', 'brickscodes'), array('back_link' => true));
	}
	
	public function bc_import_options_field() {
		if ( !class_exists('ACF') ) {
			$this->bc_deactivate_plugin('ACF plugin is not activated.');
		}
		
		$file_path = BRICKSCODES_PLUGIN_DIR . 'includes/acf/acf_import/acf-brickscodes.json';
		if ( !file_exists($file_path) ) {
			$this->bc_deactivate_plugin('ACF JSON file not found.');
		}
		
		$file_contents = file_get_contents($file_path);
		if ( $file_contents === false ) {
			$this->bc_eactivate_plugin('Failed to read ACF JSON file.');
		}
		
		$group = json_decode($file_contents, true);
		if ( json_last_error() !== JSON_ERROR_NONE || !is_array($group) || empty($group) || acf_get_field_group($group[0]['key']) ) {
			$this->bc_deactivate_plugin('Invalid JSON structure or ACF group already exists.');
		}
		
		$this->options['bc_options_acf_group_key'] = sanitize_text_field($group[0]['key']);
		$group[0]['location'][0][0]['param'] = 'page';
		$group[0]['location'][0][0]['value'] = $this->options['bc_options_page_id'];
		
		$field_group_id = acf_import_field_group($group[0]);
		if ( !$field_group_id ) {
			$this->deactivate_plugin('Failed to import ACF field group.');
		}
		
		$backup_data = $this->bc_get_backup_data();
		$acf_post_fields = Brickscodes_Helpers::bc_generate_acf_structure($this->options['bc_options_acf_group_key'], $backup_data);
		Brickscodes_Helpers::bc_update_acf_fields($acf_post_fields, $this->options['bc_options_page_id']);

		$this->options['bc_options_acf_group_id'] = $field_group_id['ID'];
		
		if ( !empty($backup_data) ) {
			$this->bc_apply_option_settings($backup_data);
		}

		unset($this->options['bc_plugin_activated']);
		update_option('brickscodes', $this->options);
	}

	public function bc_disable_gutenberg_on_settings_page($can, $post) {
        if ($post && $post->post_name === "brickscodes-options") {
            return false;
        }
        return $can;
    }
	
	// remove Brickscodes Options Page from the query.
    public function bc_hide_settings_page($query) {
		if ( !is_admin() && !is_main_query() ) {
			return;
		} 
        global $typenow;
        if ($typenow === "page") {
			$settings_page = get_page_by_path('brickscodes-options', NULL, 'page');
			if (!$settings_page) return;
            $query->set('post__not_in', array($settings_page->ID));
        }
    }
	
	// Add Brickscodes Options Page to admin menu
    public function bc_add_site_settings_to_menu() {
		$settings_page = get_page_by_path("brickscodes-options",NULL,"page");
        if (!$settings_page)
            return;
		add_menu_page('Brickscodes Settings', 'Brickscodes Setttings', 'manage_options', 'post.php?post=' . $settings_page->ID . '&action=edit', '', 'dashicons-admin-tools', 20);
    }
	
	// highlight Brickscodes Options Page
    public function bc_higlight_custom_settings_page($file) {
        global $parent_file;
        global $pagenow;
        global $typenow, $self;

		$settings_page = get_page_by_path("brickscodes-options",NULL,"page");
		if (!$settings_page) return;

        $post = isset($_GET["post"]) ? (int) $_GET["post"] : null;
        if ($pagenow === "post.php" && $post !== null && $post === $settings_page->ID) {
            $file = "post.php?post=$settings_page->ID&action=edit";
        }

        return $file;
    }
	
	// custom title to the Brickscodes Options Page
    public function bc_edit_site_settings_title() {
        global $post, $title, $action, $current_screen;
        if (isset($current_screen->post_type) && $current_screen->post_type === 'page' && $action == 'edit' && $post->post_name === "brickscodes-options") {
            $title = $post->post_title . ' - ' . get_bloginfo('name');
        }
        return $title;
    }
	
    // enqueue Brickscodes Options Page stylesheet
    public function bc_enqueue_custom_admin_styles($hook) {
        global $pagenow;
		$settings_page = get_page_by_path('brickscodes-options', NULL, 'page');
		if (!$settings_page) return;
        if ($pagenow === 'post.php') {
            $current_post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
            if ($current_post_id === $settings_page->ID) {
                wp_enqueue_style('bc-settings', BRICKSCODES_PLUGIN_URL . 'admin/assets/css/bc-options.css', [], filemtime(BRICKSCODES_PLUGIN_DIR . 'admin/assets/css/bc-options.css'), 'all');
            }
        }
    }
	
	// customize acf field instruction as tooltips, mask api/secret key
    public function bc_prepare_acf_fields($field) {	
		if (is_admin()) {
            if (function_exists('get_current_screen')) {
                $screen = get_current_screen();
                if ($screen && $screen->id === 'page' && isset($_GET['post'])) {
                    $post = get_post($_GET['post']);
                    if ( isset($post->post_name) && $post->post_name === 'brickscodes-options' ) {
						$field_types = ['color_picker', 'true_false', 'textarea', 'select', 'file'];
						foreach ($field_types as $type) {
							if ($field['type'] === $type && !empty($field['instructions'])) {
								$field['instructions'] = '<p class="bc-acf-tooltip" data-tooltip="' . esc_attr($field['instructions']) . '">?</p>';
							}
						}

						if ( $field['label'] === 'Webhook HMAC Secret Key' || $field['label'] === 'Email Validation Activation Link Secret Key' ) {
							if (!empty($field['value'])) {
								$length = strlen($field['value']);
								if ($length <= 13) {
									return $field['value'];
								}

								$first_part = substr($field['value'], 0, 8);
								$last_part = substr($field['value'], -5);
								$middle_length = $length - 13;
								$middle_mask = str_repeat('*', $middle_length);

								$field['value'] = $first_part . $middle_mask . $last_part;
							}
						}
						
						if ( $field['label'] === 'Core Framework Integration' ) {
							if ( !function_exists('CoreFramework') ) {
								$field['value'] = 0;
								$field['wrapper']['class'] = 'bc_checkbox disabled-by-core';
							}
						}
						
						if ($field['label'] === 'WS Form Styler') {
							if ( !function_exists('run_ws_form') ) {
								$field['value'] = 0;
								$field['wrapper']['class'] = 'bc_checkbox disabled-by-core';
							}
						}
						
						if ( in_array($field['_name'], ['bc_info_options', 'bc_info_core_framework', 'bc_info_uninstall', 'bc_info_reference']) ) {
							$field['disabled'] = true;
						}
					}
                }
            }
        }
		return $field;
    }
	
	// set/get api/secret key from option
    public function bc_options_save_actions($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
		
		$this->options = get_option( 'brickscodes' );
		$options_page_id = isset($this->options['bc_options_page_id']) && !empty($this->options['bc_options_page_id']) ? intval($this->options['bc_options_page_id']) : 0;
		
		if ($post_id !== $options_page_id) {
			return;
		}
				
		$backup_acf_fields = [];	
		$prev_field_values = get_fields($options_page_id);
		$field_values = isset($_POST['acf']) ? $_POST['acf'] : [];
		$brickscodes_globals = null;
	
		$prev_core_integration = $prev_field_values['bc_classes_variables']['bc_core_integration'];
		$core_integration = $field_values['field_6704929404b2c']['field_6704929404b2d'];
		if ( $core_integration !== $prev_core_integration ) {
			$brickscodes_globals = $brickscodes_globals ?: Brickscodes_Globals::get_instance();
			if ($core_integration) {
				$brickscodes_globals->bc_integrate_core_framework('option-enabled');
			} else {
				$brickscodes_globals->bc_integrate_core_framework('option-disabled');
			}
		}
		
		$prev_show_hide_elements = $prev_field_values['bc_builder_tweaks']['bc_show_hide_elements'];
		$show_hide_elements = $field_values['field_669fd45b6f7fc']['field_66aa030c0b1ee'];
		if ( $show_hide_elements !== $prev_show_hide_elements ) {
			$brickscodes_globals = $brickscodes_globals ?: Brickscodes_Globals::get_instance();
			if ($show_hide_elements) {
				$brickscodes_globals->bc_add_show_hide_global_classes('option-enabled');
			} else {
				$brickscodes_globals->bc_add_show_hide_global_classes('option-disabled');
			}
		}
		
		$prev_query_manager = $prev_field_values['bc_builder_tweaks']['bc_query_manager'];
		$query_manager = $field_values['field_669fd45b6f7fc']['field_66a9d4b86cf42'];
		if ($query_manager !== $prev_query_manager) {
			if ($query_manager) {
				if ( !isset($this->options['bc_query_manager']['bc_query_records']) ) {
					Brickscodes_Helpers::bc_get_all_queries_args('option-enabled');
				}
			}
		}

		foreach ($field_values as $parent_key => $sub_array) {	
			if (is_array($sub_array)) {
				foreach ($sub_array as $key => $value) {				
					if ( !in_array($key, ['field_66ec124e14ab9', 'field_66ec20c2cc05d']) ) {
						$backup_acf_fields[$key] = $value;
					}
					
				}
			}
		}
		$backup_acf_fields['field_6715b116c6b88'] = acf_get_field('field_6715b116c6b88')['default_value'];
		$backup_acf_fields['field_6715b2ebe2f1a'] = acf_get_field('field_6715b2ebe2f1a')['default_value'];
		$backup_acf_fields['field_6715bfc4457d8'] = acf_get_field('field_6715bfc4457d8')['default_value'];
		$backup_acf_fields['field_6715badf1d1fe'] = acf_get_field('field_6715badf1d1fe')['default_value'];

		$backup_dir = $this->upload_dir['basedir'] . '/brickscodes/';
		$backup_file_path = $backup_dir . 'brickscode_options_backup_' . time() . '.json';
				
		if (file_exists($backup_dir)) {
			// Get all JSON files in the directory
			$files = glob($backup_dir . 'brickscode_options_backup*.json');
			
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file); // Delete the JSON file
				}
			}
		} else {
			// Create the backup directory if it doesn't exist
			mkdir($backup_dir, 0755, true);
		}
		file_put_contents($backup_file_path, acf_json_encode($backup_acf_fields));
		
    }
	
	public function bc_hide_acf_field_group() {	
		$this->options = get_option('brickscodes');	
		$field_group_id_to_hide = isset($this->options['bc_options_acf_group_id']) ? intval($this->options['bc_options_acf_group_id']) : 0;
		$screen = get_current_screen();		
		if ($screen->post_type === 'acf-field-group') {
			 ?>
			<script type="text/javascript">
                document.addEventListener('DOMContentLoaded', () => {
                    // Hides the specific field group row based on the ID
                    const row = document.querySelector('form#posts-filter > table > tbody > tr#post-<?php echo esc_js($field_group_id_to_hide); ?>');
                    if (row) {
                      row.style.display = 'none';
                    }
                });
            </script>
			<?php
		}
	}
	
	public function bc_enqueue_scripts_in_core() {
		$admin_page = \get_current_screen();
		if ( is_null( $admin_page ) || $admin_page->id !== 'toplevel_page_core-framework' ) {
			return;
		}
		
		if (!class_exists('ACF')) {	
			return;
		}
				
		$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
		if ($settings_page) {
			$fields = get_fields($settings_page->ID);			
			$acf_options = !empty($fields) ? $fields : [];
		}
		
		if ( !empty($acf_options) && isset($acf_options['bc_classes_variables']) &&
			isset($acf_options['bc_classes_variables']['bc_core_integration']) && $acf_options['bc_classes_variables']['bc_core_integration'] === true
		) {			
			wp_enqueue_script('bc-core', BRICKSCODES_PLUGIN_URL . 'admin/assets/js/bc-core.js', [], filemtime(BRICKSCODES_PLUGIN_DIR . 'admin/assets/js/bc-core.js'), true);
			wp_localize_script( 'bc-core', 'bc_core_ajax', array( 'bc_core_ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('bc-nonce') ) );
		}
	}
	
	public function bc_confirm_deactivate($hook) {
		 if ($hook === 'plugins.php') {
			wp_enqueue_script('bc-confirm-deactivate', BRICKSCODES_PLUGIN_URL . 'admin/assets/js/bc-deactivate.js', array(), null, true);
			wp_localize_script( 'bc-confirm-deactivate', 'bc_deactivate_ajax', array( 'bc_deactivate_ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('bc-nonce') ) );
		}
	}
	
	public function bc_plugin_row_meta($plugin_meta, $plugin_file) {
		// Check if the current plugin is your plugin
		if ($plugin_file === 'brickscodes/brickscodes.php') { 
			// Add custom meta links
			$plugin_meta[] = '<a href="https://brickscode.gumroad.com/l/brickscodes-pro" target="_blank">' . __('Check out Pro Version', 'brickscodes') . '</a>';
		}
		
		return $plugin_meta;
	}
	
	public function bc_include_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/brickscodes-builder-tweaks.php';
		$builder_instance = new Brickscodes_Builders();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/brickscodes-assets.php';
		$assets_instance = new Brickscodes_Assets();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/brickscodes-custom-elements.php';
		$custom_element_instance = new Brickscodes_Custom_Elements();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/brickscodes-element-plus.php';
		$element_plus_instance = new Brickscodes_Element_Plus();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/brickscodes-dynamic-tags.php';
		$dynamic_tags_instance = new Brickscodes_Dynamic_Tags();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/brickscodes-conditions.php';
		$conditions_instance = new Brickscodes_Conditions();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/brickscodes-ajax.php';
		$ajax_instance = new Brickscodes_Ajax();
		
		$acf_options = [];
		if (class_exists('ACF')) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$fields = get_fields($settings_page->ID);
				$acf_options = !empty($fields) ? $fields : [];
			}
		}
		
		if ( !empty($acf_options) && isset($acf_options['bc_wordpress_group']) &&
			isset($acf_options['bc_wordpress_group']['bc_media_access_control']) && $acf_options['bc_wordpress_group']['bc_media_access_control'] === true )
		{
			add_filter('ajax_query_attachments_args', [$this, 'bc_restrict_media_library'], 10, 1 );
			add_filter('map_meta_cap', [$this, 'bc_restrict_media_edit_delete'], 10, 4);
		}
	}
	
	// Ensure users only see their own media in the library
	public function bc_restrict_media_library($query) {
		if (!is_admin()) {
			return $query; // Only modify backend requests
		}

		$user_id = get_current_user_id();
		if ($user_id && !current_user_can('administrator')) { // Check if the user is not an admin
			$query['author'] = $user_id; // Restrict media items to those uploaded by the current user
		}

		return $query;
	}

	// Restrict users from editing or deleting media that isn't theirs
	public function bc_restrict_media_edit_delete($caps, $cap, $user_id, $args) {
		if ('edit_post' === $cap || 'delete_post' === $cap) {
			$post = get_post($args[0]); // Get the post to check its author
			if ($post && $post->post_author != $user_id && !current_user_can('administrator')) { // Check if the user is not the author of the post and not an admin
				$caps[] = 'do_not_allow'; // Disallow the capability
			}
		}

		return $caps;
	}
	
}