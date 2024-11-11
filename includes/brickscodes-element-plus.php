<?php
Class Brickscodes_Element_Plus {
	private $acf_options;
	private $bc_element_plus;
	
	public function __construct() {
		$this->bc_element_plus = ['bc_slider_nested_plus' => 'Bc_Slider_Nested_Plus'];
		
		if (class_exists('ACF')) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$fields = get_fields($settings_page->ID);
				$this->acf_options = !empty($fields) ? $fields : [];
			}
		} else {
			$this->acf_options = [];
		}
		
		$this->bc_include_element_plus();
	}
	
	public function bc_include_element_plus() {
		foreach($this->bc_element_plus as $element => $class_name) {
			if ( !empty($this->acf_options) && isset($this->acf_options['bc_native_bricks_elements_plus']) &&
				isset($this->acf_options['bc_native_bricks_elements_plus'][$element]) && $this->acf_options['bc_native_bricks_elements_plus'][$element] === true && !class_exists($class_name)
			) {
				$class_file = BRICKSCODES_PLUGIN_DIR . 'includes/elements/' . strtolower(str_replace('_', '-', $class_name)) . '.php';
				require_once $class_file;
				new $class_name();
			}
		}
	}
}
