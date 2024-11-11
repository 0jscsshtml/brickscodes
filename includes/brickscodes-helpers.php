<?php
class Brickscodes_Helpers {
	public static function bc_generate_acf_structure($field_group_key, $update_values = []) {
		// Retrieve fields for the specified field group
		$fields = acf_get_fields($field_group_key);
		if (empty($fields)) {
			return null;
		}

		// Generate the ACF structure recursively
		return self::bc_generate_nested_acf_fields($fields, $update_values);
	}

	public static function bc_generate_nested_acf_fields($fields, $update_values = []) {
		$acf_data = [];

		foreach ($fields as $field) {
			// Include 'tab' and 'accordion' with empty value for structure integrity
			if ($field['type'] === 'tab' || $field['type'] === 'accordion') {
				$acf_data[$field['key']] = '';
				continue;
			}

			// If there's an update value for this field, use it
			$value = $update_values[$field['key']] ?? null;

			// Handle field value types based on the field type
			switch ($field['type']) {
				case 'true_false':
					$acf_data[$field['key']] = $value ?? 0;
					break;
				case 'text':
				case 'textarea':
				case 'color_picker':
					$acf_data[$field['key']] = $value ?? '';
					break;
				case 'select':
				case 'checkbox':
				case 'radio':
					// Ensure value is an array for select, checkbox, radio fields
					$acf_data[$field['key']] = is_array($value) ? $value : (array)($value ?? [$field['choices'][0] ?? '']);
					break;
				case 'group':
					// Recursively handle 'group' fields with sub_fields
					$acf_data[$field['key']] = self::bc_generate_nested_acf_fields($field['sub_fields'] ?? [], $update_values);
					break;
				case 'repeater':
					// If value is provided, use it; otherwise, create default structure
					$acf_data[$field['key']] = is_array($value) && !empty($value)
						? $value
						: [self::bc_generate_nested_acf_fields($field['sub_fields'] ?? [], $update_values)];
					break;
				case 'flexible_content':
					// Handle flexible content fields
					$acf_data[$field['key']] = $value ?? [];
					break;
				default:
					// For unsupported types, use empty string or value provided
					$acf_data[$field['key']] = $value ?? '';
			}
		}

		return $acf_data;
	}

	
	public static function bc_update_acf_fields($acf_data, $post_id) {
		foreach ($acf_data as $field_key => $value) {
			// Handle nested 'group' or 'repeater' fields
			if (is_array($value) && acf_is_field_group_key($field_key)) {
				self::bc_update_acf_fields($value, $post_id);
			} else {
				// Update the ACF field
				update_field($field_key, $value, $post_id);
			}
		}
	}

	// splide breakpoints helper for slider-nested plus element
	public static function bc_merge_recursive_overwrite($array1, $array2) {
		foreach ($array2 as $key => $value) {
			if (isset($array1[$key]) && is_array($array1[$key]) && is_array($value)) {
				$array1[$key] = bc_merge_recursive_overwrite($array1[$key], $value);
			} else {
				$array1[$key] = $value;
			}
		}
		return $array1;
	}
	
	public static function bc_get_user_ip_address() {
		$ipAddress = '';
		if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
			// to get shared ISP IP address
			$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// check for IPs passing through proxy servers
			// check if multiple IP addresses are set and take the first one
			$ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			foreach ($ipAddressList as $ip) {
				if (! empty($ip)) {
					// if you prefer, you can check for valid IP address here
					$ipAddress = $ip;
					break;
				}
			}
		} else if (! empty($_SERVER['HTTP_X_FORWARDED'])) {
			$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if (! empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
			$ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		} else if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if (! empty($_SERVER['HTTP_FORWARDED'])) {
			$ipAddress = $_SERVER['HTTP_FORWARDED'];
		} else if (! empty($_SERVER['REMOTE_ADDR'])) {
			$ipAddress = $_SERVER['REMOTE_ADDR'];
		}

		// Validate IP address - compatible with both IPv4 & IPv6
		$ipAddress = filter_var( $ipAddress, FILTER_VALIDATE_IP );
		return $ipAddress;
	}
	
	public static function bc_get_post_type_taxonomies($post_type, $taxonomy = '') {
		$taxonomy_objects = get_object_taxonomies($post_type, 'objects');
		if (!empty($taxonomy) && !empty($taxonomy_objects) && isset($taxonomy_objects[$taxonomy])) {
			$post_terms = get_terms([
				'taxonomy' => $taxonomy,
				'hide_empty' => false,
			]);
			
			$terms = '';
			if (!is_wp_error($post_terms)) {
				foreach ($post_terms as $term) {
					$terms .= $term->term_id . ":" . $term->name . "\n";
				}
			}
			return $terms;
		}
		
		return '';	
	}
	
	public static function bc_get_cached_option_part($option_name, $option_key, $cache_key = '') {
		$cache_key = !empty($cache_key) ? $cache_key : 'cached_' . md5($option_name . '_' . $option_key);
		$cached_value = get_transient($cache_key);

		if ($cached_value === false) {
			$full_option = get_option($option_name);

			if (isset($full_option[$option_key])) {
				$cached_value = $full_option[$option_key];
				set_transient($cache_key, $cached_value, 0);
			} else {
				$cached_value = null;
			}
		}

		return $cached_value;
	}
	
	public static function bc_export_template( $template_id = 0 ) {
		$template_data = \Bricks\Templates::get_template_by_id( $template_id );
		$template_type = get_post_meta( $template_id, BRICKS_DB_TEMPLATE_TYPE, true );
		$template_data['templateType'] = $template_type;

		if ( $template_type === 'header' || $template_type === 'footer' ) {
			$template_elements = isset( $template_data[ $template_type ] ) ? $template_data[ $template_type ] : [];
		} else {
			$template_elements = isset( $template_data['content'] ) ? $template_data['content'] : [];
		}

		$template_settings = \Bricks\Helpers::get_template_settings( $template_id );

		// Remove template conditions
		if ( isset( $template_settings['templateConditions'] ) ) {
			unset( $template_settings['templateConditions'] );
		}

		// Save as templateSettings, to be imported later
		if ( is_array( $template_settings ) && ! empty( $template_settings ) ) {
			$template_data['templateSettings'] = $template_settings;
		}

		$template_classes = [];

		foreach ( $template_elements as $element ) {
			if ( ! empty( $element['settings']['_cssGlobalClasses'] ) ) {
				$template_classes = array_unique( array_merge( $template_classes, $element['settings']['_cssGlobalClasses'] ) );
			}
		}

		// Add class definition to template data
		$global_classes        = get_option( BRICKS_DB_GLOBAL_CLASSES, [] );
		$global_classes_to_add = [];

		foreach ( $global_classes as $global_class ) {
			if ( in_array( $global_class['id'], $template_classes ) ) {
				$global_classes_to_add[] = $global_class;
			}
		}

		if ( count( $global_classes_to_add ) ) {
			$template_data['global_classes'] = $global_classes_to_add;
		}

		// Add all global variables to template data (@since 1.9.8)
		$global_variables = ! \Bricks\Database::get_setting( 'disableVariablesManager', false ) ? get_option( BRICKS_DB_GLOBAL_VARIABLES, [] ) : [];
		if ( count( $global_variables ) ) {
			$template_data['globalVariables'] = $global_variables;
		}

		// Add all global variables categories to template data (@since 1.9.8)
		$global_variables_categories = ! \Bricks\Database::get_setting( 'disableVariablesManager', false ) ? get_option( BRICKS_DB_GLOBAL_VARIABLES_CATEGORIES, [] ) : [];
		if ( count( $global_variables_categories ) ) {
			$template_data['globalVariablesCategories'] = $global_variables_categories;
		}

		// Lowercase
		$file_name = ! empty( $template_data['title'] ) ? strtolower( $template_data['title'] ) : 'no-title';

		// Make alphanumeric (removes all other characters)
		$file_name = preg_replace( '/[^a-z0-9_\s-]/', '', $file_name );

		// Clean up multiple dashes or whitespaces
		$file_name = preg_replace( '/[\s-]+/', ' ', $file_name );

		// Convert whitespaces and underscore to dashes
		$file_name = preg_replace( '/[\s_]/', '-', $file_name );

		// Final file name
		$file_name = 'template-' . $file_name . '-' . date( 'Y-m-d' ) . '.json';

		// Bulk action: Export
		wp_send_json_success( array(
			'name'    => $file_name,
			'content' => wp_json_encode( $template_data ),
			'message' => 'Template exported',
		));

	}
	
	public static function bc_format_query_obj($query_settings, $query_id, $query_title, $query_tag, $query_desc) {
		$query_obj[$query_id] = [
			'qId' 	=> $query_id,
			'query' => $query_settings,
			'qTitle'=> $query_title,
			'qTag' 	=> $query_tag,
			'qDesc' => $query_desc
		];
		
		return $query_obj;
	}
	
	public static function bc_get_all_queries_args($type = '') {
		$all_post_ids 		= \Bricks\Helpers::get_all_bricks_post_ids();
		$all_template_ids 	= \Bricks\Templates::get_all_template_ids();
		$all_ids 			= array_merge($all_post_ids, $all_template_ids);
		$options 			= get_option('brickscodes');
		$unique_qIds		= [];
		$result				= [];
		$loop_elements		= ['block', 'container', 'div', 'section', 'accordion', 'accordion-nested', 'slider', 'slider-nested', 'tabs-nested'];

		$new_attribute_id = [
			'id' => \Bricks\Helpers::generate_random_id(false),
			'name' => 'data-query-id',
		];
		$new_attribute_category = [
			'id' => \Bricks\Helpers::generate_random_id(false),
			'name' => 'data-query-tag',
		];
		
		if (!isset($options['bc_query_manager']['bc_query_records'])) {
			$options['bc_query_manager']['bc_query_records'] = [];
		}
		
		if ( $type === 'update' || $type === 'new' ) {
			if ( !check_ajax_referer( 'bricks-nonce-builder', 'nonce', false ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
			}
			if (!current_user_can('administrator')) {
				wp_send_json_error( array( 'message' => 'Unauthorized' ) );
			}

			$query_settings = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : array();
			$qId 			= isset($_POST['qId']) ? sanitize_text_field($_POST['qId']) : '';
			$qName 			= isset($_POST['qName']) ? sanitize_text_field($_POST['qName']) : '';
			$qDesc 			= isset($_POST['qDesc']) ? sanitize_textarea_field($_POST['qDesc']) : '';
			$qTag			= isset($_POST['qTag']) ? sanitize_textarea_field($_POST['qTag']) : '';

			if (empty($query_settings) || empty($qId)) {
				wp_send_json_error(array('message' => 'No query control settings found.'));
			} else {
				if ( $type === 'update' ) {
					if ( isset($options['bc_query_manager']['bc_query_records'][$qId]) ) {
						$options['bc_query_manager']['bc_query_records'][$qId]['query'] = $query_settings;
						$options['bc_query_manager']['bc_query_records'][$qId]['qTitle'] = !empty($qName) ? $qName : $options['bc_query_manager']['bc_query_records'][$qId]['qTitle'];
						$options['bc_query_manager']['bc_query_records'][$qId]['qDesc'] = !empty($qDesc) ? $qDesc  : $options['bc_query_manager']['bc_query_records'][$qId]['qDesc'];
						$options['bc_query_manager']['bc_query_records'][$qId]['qTag'] = !empty($qDesc) ? $qTag  : $options['bc_query_manager']['bc_query_records'][$qId]['qTag'];
					}
				} else if ( $type === 'new' ) {
					$options['bc_query_manager']['bc_query_records'][$qId] = [];
					$options['bc_query_manager']['bc_query_records'][$qId]['query'] = $query_settings;
					$options['bc_query_manager']['bc_query_records'][$qId]['qTitle'] = $qName;
					$options['bc_query_manager']['bc_query_records'][$qId]['qDesc'] = $qDesc;
					$options['bc_query_manager']['bc_query_records'][$qId]['qTag'] = $qTag;
				}
				update_option('brickscodes', $options);
				$cache_key = 'cached_' . md5('brickscodes' . '_bc_query_manager');
				delete_transient($cache_key);
				self::bc_get_cached_option_part('brickscodes', 'bc_query_manager', $cache_key);
			}
		}

		if ( !empty($all_ids) && !in_array($type, ['option-disabled', 'new']) ) {
			foreach ($all_ids as $post_id) {
				$template_type = \Bricks\Templates::get_template_type( $post_id );
				switch ($template_type) {
					case 'header':
						$post_meta_key = BRICKS_DB_PAGE_HEADER;
						break;
					case 'footer':
						$post_meta_key = BRICKS_DB_PAGE_FOOTER;
						break;
					default:
						$post_meta_key = BRICKS_DB_PAGE_CONTENT;
						break;
				}
				
				$bricks_data = get_post_meta($post_id, $post_meta_key, true);
				
				if (!empty($bricks_data)) {
					$update_post_meta = false;
					foreach ($bricks_data as &$data) {
						if (in_array($data['name'], $loop_elements) && isset($data['settings']['hasLoop'], $data['settings']['query'])) {
							if ( empty($options['bc_query_manager']['bc_query_records']) ) {
								$has_query_id = false;
								if ( isset($data['settings']['_attributes']) ) {
									foreach($data['settings']['_attributes'] as $attribute) {
										if ( isset($attribute['name']) && $attribute['name'] === 'data-query-id' ) {
											$query_id = $attribute['value'];
											$has_query_id = true;
										} else if ( isset($attribute['name']) && $attribute['name'] === 'data-query-tag' ) {
											$query_category = $attribute['value'];
										}
									}
								} 
								if ( !isset($data['settings']['_attributes']) || !$has_query_id ) {
									$new_attribute_id['value'] = "qid_{$data['id']}";
									$query_id = $new_attribute_id['value'];
									$new_attribute_category['value'] = ucwords($data['settings']['query']['objectType'] . ' Query');
									$query_category = $new_attribute_category['value'];
									$data['settings']['_attributes'] = [];
									$data['settings']['_attributes'][] = $new_attribute_id;
									$data['settings']['_attributes'][] = $new_attribute_category;
								}
								
								$query_name = ucwords("Element {$data['name']} ({$data['id']}) - {$data['settings']['query']['objectType']} Query");
								$query_desc = ucwords("Element {$data['name']} ({$data['id']}) - {$data['settings']['query']['objectType']} Query at template {$template_type} ({$post_id})");
								$query_setting = $data['settings']['query'];
								
								if ( !isset($unique_qIds[$query_id]) ) {
									$unique_qIds[$query_id] = true;							
									$result = array_merge($result, self::bc_format_query_obj($query_setting, $query_id, $query_name, $query_category, $query_desc));
								}	
							} else {	
								if ( isset($data['settings']['_attributes']) && $type === 'update' ) {
									foreach($data['settings']['_attributes'] as $attribute) {
										if ( isset($attribute['name']) && $attribute['name'] === 'data-query-id' && $attribute['value'] === $qId ) {
											$data['settings']['query'] = $query_settings;
											$update_post_meta = true;
										}
										if ( isset($attribute['name']) && $attribute['name'] === 'data-query-tag' ) {
											$attribute['value'] = $qTag;
											$update_post_meta = true;
										}
									}
								}  else if ( isset($data['settings']['_attributes']) && in_array($type, ['option-enabled',  'all']) ) {								
									if ( isset($data['settings']['_attributes']) ) {
										foreach($data['settings']['_attributes'] as $attribute) {
											if ( isset($attribute['name']) && $attribute['name'] === 'data-query-id' ) {
												$query_id = $attribute['value'];
											} else if ( isset($attribute['name']) && $attribute['name'] === 'data-query-tag' ) {
												$query_category = $attribute['value'];
											}
										}
										$query_setting = $data['settings']['query'];
										$query_name = isset($options['bc_query_manager']['bc_query_records'][$query_id]) ? $options['bc_query_manager']['bc_query_records'][$query_id]['qTitle'] : ucwords("Element {$data['name']} ({$data['id']}) - {$data['settings']['query']['objectType']} Query");
										$query_desc = isset($options['bc_query_manager']['bc_query_records'][$query_id]) ? $options['bc_query_manager']['bc_query_records'][$query_id]['qDesc'] : ucwords("Element {$data['name']} ({$data['id']}) - {$data['settings']['query']['objectType']} Query at template {$template_type} ({$post_id})");
										if ( !isset($unique_qIds[$query_id]) ) {
											$unique_qIds[$query_id] = true;							
											$result = array_merge($result, self::bc_format_query_obj($query_setting, $query_id, $query_name, $query_category, $query_desc));
										}	
									}
								}
							}
						}      
					}
					unset($data);
					if ($update_post_meta) {
						update_post_meta($post_id, $post_meta_key, $bricks_data);	
					}						
				}		
			}
		}
	
		if ( !in_array($type, ['update',  'new', 'all']) ) {			
			$options['bc_query_manager']['bc_query_records'] = [];
			$options['bc_query_manager']['bc_query_records'] = $result;
			update_option('brickscodes', $options);
			$cache_key = 'cached_' . md5('brickscodes' . '_bc_query_manager');
			delete_transient($cache_key);
			self::bc_get_cached_option_part('brickscodes', 'bc_query_manager', $cache_key);
		} else if ( $type === 'update' || $type === 'new' || $type === 'all') {
			$message = $type === 'update' || $type === 'all' ? 'Query updated successfully across entire site.' : 'New query record created successfully';			
			if ( $type === 'all' ) {
				$options['bc_query_manager']['bc_query_records'] = [];
				$options['bc_query_manager']['bc_query_records'] = $result;				
				update_option('brickscodes', $options);
				$cache_key = 'cached_' . md5('brickscodes' . '_bc_query_manager');
				delete_transient($cache_key);
				self::bc_get_cached_option_part('brickscodes', 'bc_query_manager', $cache_key);
			}			
			wp_send_json_success( array(
				'queriesArgs' => $options['bc_query_manager']['bc_query_records'],
				'queriesTags' => array_values(array_unique(array_column($options['bc_query_manager']['bc_query_records'], 'qTag'))),
				'message' => $message
			));
		}		
	}
	
	public static function bc_copy_core_stylesheet() {
		$source = plugin_dir_path( WP_PLUGIN_DIR . '/core-framework/core-framework.php' ) . 'assets/public/css/core_framework.css';
		$destination = WP_CONTENT_DIR . '/uploads/brickscodes/core_framework.css';
		
		if (file_exists($source)) {
			// Create the destination directory if it doesn't exist
			$destination_dir = dirname($destination);
			if (!file_exists($destination_dir)) {
				mkdir($destination_dir, 0755, true);
			}

			// Copy the file to the new location
			if (copy($source, $destination)) {
				error_log('CSS file copied successfully to ' . $destination);
			} else {
				error_log('Failed to copy CSS file to ' . $destination);
			}
		} else {
			error_log('Source CSS file not found: ' . $source);
		}
	}

	private static function bc_str_replace_first( $needle, $replace, $haystack ): string {
		if ( $needle === '' ) {
			return $haystack;
		}

		$pos = strpos( $haystack, $needle );
		if ( $pos !== false ) {
			$haystack = substr_replace( $haystack, $replace, $pos, strlen( $needle ) );
		}

		return $haystack;
	}
	
	private static function bc_extract_variables( $block, &$variables, $isNested = false ) {
		preg_match_all( '/--([^:]+):\s*([^;]+);/', $block, $varMatches, PREG_SET_ORDER );	
		foreach ( $varMatches as $varMatch ) {
			$name  = trim( $varMatch[1] );
			$value = trim( $varMatch[2] );

			if ( ! array_key_exists( $name, $variables ) ) {
				$variables[ $name ] = $isNested ? 'var(--' . self::bc_str_replace_first( '--', '', $name ) . ')' : $value;
			}
		}
	}
	
	// get css variables from stylesheet
	public static function bc_get_variables_from_styleSheet($filename, $type) {
		$upload_dir = wp_upload_dir();
		$stylesheet_dir = $upload_dir['basedir'] . '/brickscodes/';
		$file_path = $stylesheet_dir;
		
		if (file_exists($file_path)) {		
			$stylesheet = file_get_contents($file_path . $filename);			
			if ($stylesheet === false) {
				return array();
			}
		} else {
			return array();
		}

		if ( $type === 'variables' ) {
			$variables    = array();
			$rootPattern  = '/:root[^{]*\{([^}]+)\}/';
			$mediaPattern = '/@media[^{]+\{[^}]*:root[^{]*\{([^}]+)\}/';

			if ( preg_match( $rootPattern, $stylesheet, $rootMatch ) ) {
				self::bc_extract_variables( $rootMatch[1], $variables );
			}

			preg_match_all( $mediaPattern, $stylesheet, $mediaMatches, PREG_SET_ORDER );
			foreach ( $mediaMatches as $mediaMatch ) {
				self::bc_extract_variables( $mediaMatch[1], $variables, true );
			}

			return $variables;
		} else {
			return $stylesheet;
		}
	}
	
	// get flat array variables key/value pair
	public static function bc_get_core_variables_pair() {
		$cssString = get_option( 'core_framework_selected_preset_backup', '' );
		if ( ! $cssString ) {
			return '';
		}
		$variables_pair = [];
		$rootPattern = '/(:root[^{]*\{[^}]*--[^}]*\})/s';
		if (preg_match($rootPattern, $cssString, $matches)) {
		
			$rootContent = $matches[1]; 
			$varPattern = '/(--[\w-]+)\s*:\s*([^;]+)\s*;/';
			preg_match_all($varPattern, $rootContent, $varMatches, PREG_SET_ORDER);

			// Loop through matches and store them in the array
			foreach ($varMatches as $match) {
				$variables_pair[$match[1]] = trim($match[2]);  // $match[1] is the key, $match[2] is the value
			}
		}
		return $variables_pair;
	}
	
	public static function bc_check_unique_id_name($data, $suffix = '', $checkName = false, $name = '') {
		$id_suffix = $suffix;
		$id = \Bricks\Helpers::generate_random_id(false) . $id_suffix;
		$all_ids = [];
		$all_names = [];

		// Helper function to collect ids and names recursively
		$collect_ids_and_names = function($items) use (&$collect_ids_and_names, &$all_ids, &$all_names, $checkName) {
			foreach ($items as $key => $item) {
				if (is_array($item)) {
					if (isset($item['id'])) {
						$all_ids[] = $item['id'];
					}
					if ($checkName && isset($item['name'])) {
						$all_names[] = $item['name'];
					}
					// Recursively check deeper arrays
					$collect_ids_and_names($item);
				}
			}
		};

		// Collect ids and names from the data
		$collect_ids_and_names($data);

		// Check if the generated ID is unique
		if (in_array($id, $all_ids)) {
			return self::bc_check_unique_id_name($data, $suffix, $checkName, $name); // Recursively call to generate a new one
		}

		// Ensure name is unique if name check is enabled
		if ($checkName && in_array($name, $all_names)) {
			$counter = 1;
			$original_name = $name;
			while (in_array($name, $all_names)) {
				$name = $original_name . '-' . $counter;
				$counter++;
			}
		}

		// Return the unique ID and name if name checking is enabled
		return $checkName ? ['id' => $id, 'name' => $name] : $id;
	}
	
}