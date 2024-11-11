<?php

class Brickscodes_Globals {
	private static $instance = null;
	private $global_classes;
	private $global_locked_classes;
	private $global_class_categories;
	private $global_variables;
	private $global_variables_categories;
	private $color_palettes;
	private $brickscodes_options;
	private $acf_options;
	
	public function __construct() {}
	
	public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	public function bc_get_global_classes() {
		return get_option('bricks_global_classes');
	}
	
	public function bc_get_global_locked_classes() {
		return get_option('bricks_global_classes_locked');
	}
	
	public function bc_get_global_class_categories() {
		return get_option('bricks_global_classes_categories');
	}
	
	public function bc_get_global_variables() {
		return get_option('bricks_global_variables');
	}
	
	public function bc_get_global_variables_categories() {
		return get_option('bricks_global_variables_categories');
	}
	
	public function bc_get_color_palettes() {
		return get_option('bricks_color_palette');
	}
	
	public function bc_get_brickscodes_options() {
		return get_option('brickscodes');
	}
	
	public function bc_get_acf_options() {
		if (class_exists('ACF')) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$fields = !empty(get_fields($settings_page->ID)) ? get_fields($settings_page->ID) : [];
			}
		} else {
			$fields = [];
		}
		return $fields;
	}
	
	public function bc_integrate_core_framework($type) {
		$this->acf_options = $this->bc_get_acf_options();
		
		if ($type === 'ajax') {
			$this->bc_handle_ajax_request();
			$integration_enabled = function_exists('CoreFramework') && !empty($this->acf_options) && $this->acf_options['bc_classes_variables']['bc_core_integration'] ?? false;
			$type = $integration_enabled ? 'option-enabled-ajax' : 'option-disabled';
		}

		if (in_array($type, ['option-enabled', 'option-enabled-ajax'])) {
			Brickscodes_Helpers::bc_copy_core_stylesheet();
			$this->bc_manage_class_categories($type);
		} else {
			$this->bc_remove_coreframe_category();
		}

		if (function_exists('CoreFramework')) {
			$this->process_core_framework_data($type);
		}
	}
	
	private function bc_handle_ajax_request() {
		if (!check_ajax_referer('bc-nonce', 'nonce', false)) {		
			wp_send_json_error(['message' => 'Invalid nonce']);
		}
		if (!current_user_can('administrator')) {
			wp_send_json_error(['message' => 'Unauthorized']);
		}
	}

	private function bc_manage_class_categories($type) {
		$this->global_class_categories = $this->bc_get_global_class_categories() ?? [];
		$has_coreframe_category = false;

		foreach ($this->global_class_categories as $category) {
			if ($category['id'] === 'bcgccorefrm') {
				$has_coreframe_category = true;
				break;
			}
		}

		if (!$has_coreframe_category && $type === 'option-enabled') {
			$this->global_class_categories[] = [
				'id' => 'bcgccorefrm',
				'name' => 'Core Framework (BC)'
			];
			update_option('bricks_global_classes_categories', $this->global_class_categories);
		}
	}

	private function bc_remove_coreframe_category() {
		$this->global_class_categories = $this->bc_get_global_class_categories() ?? [];
		$this->global_class_categories = array_filter(
			$this->global_class_categories,
			fn($category) => $category['id'] !== 'bcgccorefrm'
		);
		update_option('bricks_global_classes_categories', array_values($this->global_class_categories));
	}

	private function process_core_framework_data($type) {
		$core_instance = new \CoreFramework\Helper();
		$class_name = $core_instance->getClassNames(['group_by_category' => false]);
		$variables = $core_instance->getVariablesGroupedByCategoriesAndGroups(['group_by_category' => true]);
		$colors = $core_instance->getVariablesGroupedByCategoriesAndGroups([
			'group_by_category' => true,
			'excluded_keys' => ['typographyStyles', 'spacingStyles', 'layoutsStyles', 'designStyles', 'componentsStyles', 'otherStyles']
		]);

		$this->bc_update_color_palettes($colors, $type);
		$this->bc_update_global_variables($type);
		$this->bc_update_global_classes($class_name, $type);

		$cache_key = 'cached_' . md5('brickscodes' . '_bc_core_integration');
		$this->brickscodes_options = $this->bc_get_brickscodes_options();

		if (in_array($type, ['option-enabled', 'option-enabled-ajax'])) {
			$this->brickscodes_options['bc_core_integration'] = [
				'classes' => $class_name,
				'variables' => $variables,
				'variables_pair' => Brickscodes_Helpers::bc_get_core_variables_pair()
			];
			update_option('brickscodes', $this->brickscodes_options);
			delete_transient($cache_key);
			$cached_value = Brickscodes_Helpers::bc_get_cached_option_part('brickscodes', 'bc_core_integration', $cache_key);
		} else {
			$this->brickscodes_options['bc_core_integration'] = [];
			update_option('brickscodes', $this->brickscodes_options);
			delete_transient($cache_key);
		}

		if ($type === 'option-enabled-ajax') {
			wp_send_json_success(['message' => 'Core Framework Updated Successfully.']);
		}
	}

	private function bc_update_color_palettes($colors, $type) {
		$this->color_palettes = $this->bc_get_color_palettes() ?? [];
		$has_core_palette = false;
		$cf_palette_index = 0;
		$exisiting_cf_color_palette = [];

		foreach ($this->color_palettes as $index => $palette) {
			if (str_ends_with($palette['id'], '_bccpcf')) {
				$has_core_palette = true;
				$exisiting_cf_color_palette = $palette;
				$cf_palette_index = $index;
				break;
			}
		}

		if ($type === 'option-disabled') {
			// Remove any core framework color palettes
			$this->color_palettes = array_values(array_filter($this->color_palettes, fn($palette) => !str_ends_with($palette['id'], '_bccpcf')));
		} else {
			if ($has_core_palette) {
				// Clear existing colors if core palette exists
				$this->color_palettes[$cf_palette_index]['colors'] = [];
			} else {
				// Initialize a new core color palette if it doesn't exist
				$cf_color_palette = [
					'id' => Brickscodes_Helpers::bc_check_unique_id_name($this->color_palettes, '_bccpcf', false),
					'name' => 'Core Framework (BC)',
					'colors' => [],
					'default' => false
				];
			}

			// Process colors from colorStyles and add unique colors to the core palette
			if (!empty($colors)) {
				foreach ($colors['colorStyles'] as $category => $vars) {
					if (is_array($vars) && !empty($vars)) {
						foreach ($vars as $var) {
							$color_raw = 'var(--' . $var . ')';
							$color_found = false;

							if ($has_core_palette) {
								foreach ($exisiting_cf_color_palette['colors'] as $item) {
									if ($item['raw'] === $color_raw) {
										$this->color_palettes[$cf_palette_index]['colors'][] = $item;
										$color_found = true;
										break;
									}
								}
							} else {
								$new_color = [
									'raw' => $color_raw,
									'id' => Brickscodes_Helpers::bc_check_unique_id_name($this->color_palettes, '', false),
									'name' => $color_raw,
								];
								$cf_color_palette['colors'][] = $new_color;
							}

							// If the color was not found, add a new color entry
							if ($has_core_palette && !$color_found) {
								$new_color = [
									'raw' => $color_raw,
									'id' => Brickscodes_Helpers::bc_check_unique_id_name($this->color_palettes, '', false),
									'name' => $color_raw,
								];
								$this->color_palettes[$cf_palette_index]['colors'][] = $new_color;
							}
						}
					}
				}
				if (!$has_core_palette) {
					$this->color_palettes[] = $cf_color_palette;
				}
			}
		}

		update_option('bricks_color_palette', $this->color_palettes);
	}


	private function bc_update_global_variables($type) {
		if ($type === 'option-enabled' || $type === 'option-enabled-ajax') {
			$this->global_variables = array_filter(
				$this->bc_get_global_variables(),
				fn($var) => ($var['category'] ?? null) !== 'bcgvcorefrm'
			);
			update_option('bricks_global_variables', array_values($this->global_variables));
			$this->global_variables_categories = array_filter($this->bc_get_global_variables_categories(), fn($cat) => $cat['id'] !== 'bcgvcorefrm');
			update_option('bricks_global_variables_categories', array_values($this->global_variables_categories));
		}
	}

	private function bc_update_global_classes($class_name, $type) {
		$this->global_locked_classes = $this->bc_get_global_locked_classes() ?? [];
		$this->global_classes = $this->bc_get_global_classes() ?? [];

		// Filter and separate cf_global_classes from global_classes
		$cf_global_classes = array_values(array_filter($this->global_classes, fn($item) => isset($item['category']) && $item['category'] === 'bcgccorefrm'));
		$this->global_classes = array_values(array_filter($this->global_classes, fn($item) => !isset($item['category']) || $item['category'] !== 'bcgccorefrm'));

		if (!empty($cf_global_classes)) {
			// Remove cf_global_classes IDs from global_locked_classes
			$this->global_locked_classes = array_values(array_diff($this->global_locked_classes, array_column($cf_global_classes, 'id')));
		}

		if ($type === 'option-enabled' || $type === 'option-enabled-ajax') {
			if (!empty($class_name)) {
				foreach($class_name as $cls) {
					$class_found = false;
					foreach($cf_global_classes as $item) {
						if ( $cls === $item['name'] ) {
							$new_cf_global_classes = [
								'id' => $item['id'],
								'name' => $cls,
								'settings' => [],
								'category' => 'bcgccorefrm',
							];
							$this->global_classes[] = $new_cf_global_classes;
							$this->global_locked_classes[] = $item['id'];
							$class_found = true;
							break;
						}
					}
					if ( !$class_found ) {
						$id = Brickscodes_Helpers::bc_check_unique_id_name($this->global_classes, '', false);
						$new_cf_global_classes = [
							'id' => $id,
							'name' => $cls,
							'settings' => [],
							'category' => 'bcgccorefrm',
						];
						$this->global_classes[] = $new_cf_global_classes;
						$this->global_locked_classes[] = $id;
					}
				}
			}
		}
		
		update_option('bricks_global_classes', $this->global_classes);
		update_option('bricks_global_classes_locked', $this->global_locked_classes);

	}
	
	public function bc_add_show_hide_global_classes($type = '') {
		$this->global_classes = $this->bc_get_global_classes() ?: [];
		$this->global_class_categories = $this->bc_get_global_class_categories() ?: [];
		
		$brickscodes_category = ['id' => 'brickscodes', 'name' => 'Brickscodes'];
		$has_brickscodes_category = false;
		
		// Separate categories and check for 'brickscodes' category
		$this->global_class_categories = array_filter($this->global_class_categories, function($category) use (&$has_brickscodes_category, $brickscodes_category) {
			if ($category['id'] === 'brickscodes') {
				$has_brickscodes_category = true;
				return true;
			}
			return true;
		});
		
		if (!$has_brickscodes_category) {
			$this->global_class_categories[] = $brickscodes_category;
		}

		if ($type === 'option-enabled') {
			update_option('bricks_global_classes_categories', $this->global_class_categories);
		} elseif ($type === 'option-disabled') {
			$this->global_class_categories = array_values(array_filter($this->global_class_categories, fn($cat) => $cat['id'] !== 'brickscodes'));
			update_option('bricks_global_classes_categories', $this->global_class_categories);
		}
		
		$canvas_class = $frontend_class = false;
		$this->global_classes = array_filter($this->global_classes, function($class) use (&$canvas_class, &$frontend_class) {
			if ($class['name'] === 'bc-hide-in-canvas') {
				$canvas_class = true;
			} elseif ($class['name'] === 'bc-hide-in-frontend') {
				$frontend_class = true;
			}
			return true;
		});

		if ($type === 'option-enabled') {
			$updated = false;
			
			foreach (['bc-hide-in-canvas' => &$canvas_class, 'bc-hide-in-frontend' => &$frontend_class] as $class_name => &$exists) {
				if (!$exists) {
					$id = Brickscodes_Helpers::bc_check_unique_id_name($this->global_classes, '', false);
					$this->global_classes[] = ['id' => $id, 'name' => $class_name, 'settings' => [], 'category' => 'brickscodes'];
					$updated = true;
				}
			}
			
			if ($updated) {
				update_option('bricks_global_classes', $this->global_classes);
			}
		} elseif ($type === 'option-disabled') {
			$this->global_classes = array_filter($this->global_classes, fn($class) => $class['name'] !== 'bc-hide-in-canvas' && $class['name'] !== 'bc-hide-in-frontend');
			update_option('bricks_global_classes', array_values($this->global_classes));
		}
	}

}