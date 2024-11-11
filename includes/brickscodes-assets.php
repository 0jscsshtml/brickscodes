<?php
class Brickscodes_Assets {
    private $bricks_settings_string;
    private $bricks_data;
    private $has_acf;
	private $acf_options;
    private $option_name;
    private $option_value;
	private $custom_elements;

    public function __construct() {
        $this->bricks_settings_string = '';
        $this->has_acf = class_exists('ACF');
		if ($this->has_acf) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$fields = get_fields($settings_page->ID);
				$this->acf_options = !empty($fields) ? $fields : [];
			}
		} else {
			$this->acf_options = [];
		}
        $this->option_name = 'brickscodes';
        $this->option_value = get_option($this->option_name);
		$this->custom_elements = ['bc-model-viewer', 'bc-form-submission'];
        add_action('wp_enqueue_scripts', [$this, 'bc_conditionally_enqueue_scripts_styles_on_settings'], 999, 1);
        add_action('wp_enqueue_scripts', [$this, 'bc_builder_scripts'], 999, 1);
		add_action('wp_footer', [$this, 'bc_enqueue_core_style_in_builder'], 999, 1);
    }

    // conditionally load styles and scripts base on specific control settings on page
    public function bc_conditionally_enqueue_scripts_styles_on_settings() {
        $active_templates 		= \Bricks\Database::$active_templates;
        $header_bricks_data 	= [];
        $footer_bricks_data 	= [];
        $content_bricks_data	= [];

        if (!empty($active_templates['header'])) {
            $this->bricks_settings_string .= wp_json_encode(\Bricks\Database::get_data($active_templates['header'], 'header'));
            $header_bricks_data = (\Bricks\Helpers::get_bricks_data($active_templates['post_id'], 'header') !== false) ? \Bricks\Helpers::get_bricks_data($active_templates['post_id'], 'header') : [];
        }
        if (!empty($active_templates['footer'])) {
            $this->bricks_settings_string .= wp_json_encode(\Bricks\Database::get_data($active_templates['footer'], 'footer'));
            $footer_bricks_data = (\Bricks\Helpers::get_bricks_data($active_templates['post_id'], 'footer') !== false) ? \Bricks\Helpers::get_bricks_data($active_templates['post_id'], 'footer') : [];
        }
        if (!empty($active_templates['content'])) {
            $this->bricks_settings_string .= wp_json_encode(\Bricks\Database::get_data($active_templates['content']));
            $content_bricks_data = (\Bricks\Helpers::get_bricks_data($active_templates['post_id'], 'content') !== false) ? \Bricks\Helpers::get_bricks_data($active_templates['post_id'], 'content') : [];
        }

        $this->bricks_data = array_merge($content_bricks_data, $header_bricks_data, $footer_bricks_data);
        $popup_template_ids = !empty($active_templates['popup']) ? $active_templates['popup'] : [];
        $popup_bricks_data = [];

        if (!empty($popup_template_ids)) {
            foreach ($popup_template_ids as $popup_template_id) {
                $this->bricks_settings_string .= wp_json_encode(\Bricks\Database::get_data($popup_template_id));

                // Get popup content and ensure it's an array
                $popup_content = get_post_meta($popup_template_id, BRICKS_DB_PAGE_CONTENT, true);
                $popup_content = ($popup_content !== false && $popup_content !== '' && is_array($popup_content)) ? $popup_content : [];

                if (!empty($popup_content)) {
                    $popup_bricks_data = array_merge($popup_bricks_data, $popup_content);
                }
            }
            $this->bricks_data = array_merge($content_bricks_data, $header_bricks_data, $footer_bricks_data, $popup_bricks_data);
        }

        if (strpos($this->bricks_settings_string, '"global"')) {
            $global_elements = \Bricks\Database::$global_data['elements'] ? \Bricks\Database::$global_data['elements'] : [];
            foreach ($global_elements as $global_element) {
                $global_element_id = !empty($global_element['global']) ? $global_element['global'] : false;
                if (!$global_element_id) {
                    $global_element_id = !empty($global_element['id']) ? $global_element['id'] : false;
                }
                if ($global_element_id) {
                    if (strpos($this->bricks_settings_string, $global_element_id)) {
                        $this->bricks_settings_string .= wp_json_encode($global_element);
                    }
                }
            }
        }
		
		
		if ( !empty($this->acf_options) && isset($this->acf_options['bc_native_bricks_elements_plus']) &&
			isset($this->acf_options['bc_native_bricks_elements_plus']['bc_slider_nested_plus']) && $this->acf_options['bc_native_bricks_elements_plus']['bc_slider_nested_plus'] === true
		) {
			$sliderStrings 		= ['"playToggleButtons":true', '"resetProgress":true', '"enableSync":true', '"enableProgress":true', '"transitionProgress":true', '"fractionPagination":true', '"thumbIsNavigation":true'];
			$hasUrlHash 		= strpos( $this->bricks_settings_string, '"enableUrlHashNav":true' );
			$hasIntersection 	= strpos( $this->bricks_settings_string, '"enableIntersect":true' );
			$hasAutoscroll 		= strpos( $this->bricks_settings_string, '"enableAutoScroll":true' );		
			$sliderStringfound 	= false;
			foreach ($sliderStrings as $sliderString) {
				if (strpos($this->bricks_settings_string, $sliderString) !== false) {
					$sliderStringfound = true;
					break;
				}
			}

			// load assets in frontend head if settings/element found on page
			if (bricks_is_frontend()) {
				if ($hasUrlHash) {
					wp_enqueue_script( 'bc-splide-url-hash-nav', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/splide-extension-url-hash.min.js', ['bricks-scripts', 'bricks-splide'], false, true );
				} 
				if ($hasIntersection) {
					wp_enqueue_script( 'bc-splide-intersection', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/splide-extension-intersection.min.js', ['bricks-scripts', 'bricks-splide'], false, true );
				} 
				if ($hasAutoscroll !== false) {
					wp_enqueue_script( 'bc-splide-autoscroll', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/splide-extension-auto-scroll.min.js', ['bricks-scripts', 'bricks-splide'], false, true );
				} 
				if ($sliderStringfound || $hasAutoscroll || $hasUrlHash || $hasIntersection) {
					wp_enqueue_style( 'bc-slider-nested', BRICKSCODES_PLUGIN_URL . 'public/assets/css/bc-slider-nested.css', [], false, 'all' );
					wp_enqueue_script( 'bc-slider-nested', BRICKSCODES_PLUGIN_URL . 'public/assets/js/bc-slider-nested.js', ['bricks-scripts', 'bricks-splide'], false, true );
				}
			}
		}
	
		if ( !function_exists('CoreFramework') && !empty($this->acf_options) && isset($this->acf_options['bc_classes_variables']) &&
				isset($this->acf_options['bc_classes_variables']['bc_core_integration']) && $this->acf_options['bc_classes_variables']['bc_core_integration'] === true
			) {	
			if (bricks_is_frontend()) {
				$file_url = content_url('uploads/brickscodes/core_framework.css');
				$file_path = WP_CONTENT_DIR . '/uploads/brickscodes/core_framework.css';
				wp_enqueue_style('bc-core-stylesheet', $file_url, [], filemtime($file_path), 'all');
			}
		}
			
		if (!empty($this->bricks_data)) {
			foreach($this->bricks_data as $data) {
				if (bricks_is_frontend()) {
					if ($data['name'] === 'bc-model-viewer' && in_array($data['name'], $this->custom_elements)) {
						wp_enqueue_style( 'bc-model-viewer-style', BRICKSCODES_PLUGIN_URL . 'public/assets/css/bc-model-viewer.css', [], false, 'all' );
					}
				}
				if ($data['name'] === 'bc-form-submission' && in_array($data['name'], $this->custom_elements)) {
					if (!strpos( $this->bricks_settings_string, '"advanceTable":true' ) ) {
						wp_enqueue_style( 'bc-form-submission-style', BRICKSCODES_PLUGIN_URL . 'public/assets/css/bc-form-submission.css', [], false, 'all' );
					} else if (strpos( $this->bricks_settings_string, '"advanceTable":true')) {
						wp_enqueue_style( 'datatables',BRICKSCODES_PLUGIN_URL . 'public/assets/css/lib/datatables/dataTables.dataTables.min.css', [], false, 'all' );
						wp_enqueue_script( 'jquery', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/jquery.js', [], false, true );
						wp_enqueue_script( 'datatables', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/dataTables.min.js', ['jquery'], false, true );
						if ( strpos( $this->bricks_settings_string, '"advanceTableResponsive":true' ) ) {
							wp_enqueue_style( 'datatables-responsive', BRICKSCODES_PLUGIN_URL . 'public/assets/css/lib/datatables/responsive.dataTables.min.css', [], false, 'all' );
							wp_enqueue_script( 'datatables-responsive', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/dataTables.responsive.min.js', [], false, true );
						}
						if (strpos( $this->bricks_settings_string, '"advanceTableColReorder":true' ) ) {
							wp_enqueue_style( 'datatables-colReorder', BRICKSCODES_PLUGIN_URL . 'public/assets/css/lib/datatables/colReorder.dataTables.min.css', [], false, 'all' );
							wp_enqueue_script( 'datatables-colReorder', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/dataTables.colReorder.min.js', [], false, true );
						}
						if (strpos( $this->bricks_settings_string, '"advanceTableExport":true' ) ) {
							wp_enqueue_style( 'buttons.dataTables', BRICKSCODES_PLUGIN_URL . 'public/assets/css/lib/datatables/buttons.dataTables.min.css', [], false, 'all' );
							wp_enqueue_script( 'dataTables-buttons', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/dataTables.buttons.min.js', [], false, true );
							wp_enqueue_script( 'buttons-dataTables', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/buttons.dataTables.min.js', [], false, true );
							wp_enqueue_script( 'jszip', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/jszip.min.js', [], false, true );
							wp_enqueue_script( 'pdfmaker', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/pdfmake.min.js', [], false, true );
							wp_enqueue_script( 'vfs-fonts', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/vfs_fonts.js', [], false, true );
							wp_enqueue_script( 'buttons--html5', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/datatables/buttons.html5.min.js', [], false, true );
						}
					}
				}
			}
		}

    }

    // Builder main script
    public function bc_builder_scripts() {		
        // essential js always load in builder editor
        if (bricks_is_builder_main()) {
            wp_enqueue_style('bc-builder-app', BRICKSCODES_PLUGIN_URL . 'admin/assets/css/bc-builder.css', [], filemtime(BRICKSCODES_PLUGIN_DIR . 'admin/assets/css/bc-builder.css'), 'all');
			wp_enqueue_script('bc-builder-app', BRICKSCODES_PLUGIN_URL . 'admin/assets/js/bc-builder.js', [], filemtime(BRICKSCODES_PLUGIN_DIR . 'admin/assets/js/bc-builder.js'), true);
			wp_localize_script( 'bc-builder-app', 'bc_builder_ajax', array( 'bc_builder_ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('bc-nonce') ) );

			if (!empty($this->acf_options)) {
				unset($this->acf_options['bc_brickscode_api_secret_key']);
				wp_localize_script('bc-builder-app', 'bc_acf_options_page', array(
					'optionSettings' => $this->acf_options
				));
			} else {
				wp_localize_script('bc-builder-app', 'bc_acf_options_page', array(
					'optionSettings' => false
				));
			}
			
			if ( \Bricks\Capabilities::current_user_has_full_access() && !empty($this->acf_options) && isset($this->acf_options['bc_builder_tweaks']) && 
				isset($this->acf_options['bc_builder_tweaks']['bc_query_manager']) && $this->acf_options['bc_builder_tweaks']['bc_query_manager'] === true
			)  {
				wp_enqueue_code_editor( array( 'type' => 'text/javascript' ) );
				wp_localize_script('bc-builder-app', 'cm_settings', array(
					'codemirror' => array(
						'indentUnit' => 4,
						'lineNumbers' => true,
						'lineWrapping' => true,
						'mode' => 'javascript',
						'lint' => false,
						'matchBrackets' => true,
						'autoCloseBrackets' => true,
						'readOnly' => true,
					)
				));
				wp_dequeue_style('wp-codemirror');
				wp_dequeue_style('code-editor');

				$cache_key = 'cached_' . md5('brickscodes' . '_bc_query_manager');
				$cached_value = get_transient($cache_key);

				if ( $cached_value ) {
					$result = isset($cached_value['bc_query_records']) ? $cached_value['bc_query_records'] : [];
				} else {
					$result = Brickscodes_Helpers::bc_get_all_queries_args('all');
				}
	
				if (!empty($result)) {
					$allqTags = array_values(array_unique(array_column($result, 'qTag')));	
				}
			
				wp_localize_script('bc-builder-app', 'bc_query_manager', array(
					'queriesArgs' => $result,
					'queriesTags' => $allqTags
				));	 
			}
			
			if ( \Bricks\Capabilities::current_user_has_full_access() && function_exists('CoreFramework') && !empty($this->acf_options) && isset($this->acf_options['bc_classes_variables']) &&
				isset($this->acf_options['bc_classes_variables']['bc_core_integration']) && $this->acf_options['bc_classes_variables']['bc_core_integration'] === true
			) {	
				$cache_key = 'cached_' . md5('brickscodes' . '_bc_core_integration');
				$cached_value = Brickscodes_Helpers::bc_get_cached_option_part('brickscodes', 'bc_core_integration', $cache_key);
			
				if ( $cached_value ) {
					$classes = $cached_value['classes'];
					$variables = $cached_value['variables'];
					$variables_pair = $cached_value['variables_pair'];
				} else {
					$this->option_value = get_option($this->option_name);
					$classes =  isset($this->option_value['bc_core_integration']['classes']) ? $this->option_value['bc_core_integration']['classes'] : [];
					$variables = isset($this->option_value['bc_core_integration']['variables']) ? $this->option_value['bc_core_integration']['variables'] : [];
					$variables_pair = isset($this->option_value['bc_core_integration']['variables_pair']) ? $this->option_value['bc_core_integration']['variables_pair'] : [];
				}
							
				wp_localize_script('bc-builder-app', 'coreFramework', array(
					'classes' => $classes,
					'variables' => $variables,
					'variables_pair' => $variables_pair,
				));	
			}
			
		}

        // load assets in canvas
        if (bricks_is_builder_iframe()) {
            wp_enqueue_style('bc-builder-canvas-app', BRICKSCODES_PLUGIN_URL . 'admin/assets/css/bc-builder-canvas.css', [], filemtime(BRICKSCODES_PLUGIN_DIR . 'admin/assets/css/bc-builder-canvas.css'), 'all');
			if ( !empty($this->acf_options) && isset($this->acf_options['bc_native_bricks_elements_plus']) &&
				isset($this->acf_options['bc_native_bricks_elements_plus']['bc_slider_nested_plus']) && $this->acf_options['bc_native_bricks_elements_plus']['bc_slider_nested_plus'] === true
			) {
				wp_enqueue_style( 'bc-slider-nested', BRICKSCODES_PLUGIN_URL . 'public/assets/css/bc-slider-nested.css', [], false, 'all' );
			}
        }
    }
	
	public function bc_enqueue_core_style_in_builder() {
		if (bricks_is_builder_iframe()) {
			if ( function_exists('CoreFramework') && !empty($this->acf_options) && isset($this->acf_options['bc_classes_variables']) &&
				isset($this->acf_options['bc_classes_variables']['bc_core_integration']) && $this->acf_options['bc_classes_variables']['bc_core_integration'] === true
			) {	

				$core_file_path = plugin_dir_path( WP_PLUGIN_DIR . '/core-framework/core-framework.php' ) . 'assets/public/css/core_framework.css';
				$backup_file_path = WP_CONTENT_DIR . '/uploads/brickscodes/core_framework.css';
				$file_path = function_exists('CoreFramework') ? $core_file_path : $backup_file_path;
				$stylesheet = '';
				
				if (is_readable($file_path)) {	
					$stylesheet = file_get_contents($file_path); 		
					if ($stylesheet === false) {
						error_log("Failed to read the stylesheet from: $file_path");
						return array();
					}
				} else {
					 error_log("File not found or unreadable: $file_path");
					return array();
				}
			
				if ( !empty($stylesheet) ) {
					echo '<style>' . $stylesheet . '</style>';
				}
			}
        }
	}
}