<?php
class Brickscodes_Custom_Elements {
	private $options;
	private $custom_elements;
	private $acf_options;
	
	public function __construct() {
		$this->options = get_option('brickscodes');
		$this->custom_elements = ['bc_model_viewer' => 'Bc_Model_Viewer', 'bc_copy_clipboard' => 'Bc_Copy_Clipboard_Button', 'bc_form_submission' => 'Bc_Form_Submission'];
		if (class_exists('ACF')) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$fields = get_fields($settings_page->ID);
				$this->acf_options = !empty($fields) ? $fields : [];
			}
		} else {
			$this->acf_options = [];
		}
		
		if ( !empty($this->acf_options) && isset($this->acf_options['bc_custom_elements'], $this->acf_options['bc_custom_elements']['bc_model_viewer']) 
			&& $this->acf_options['bc_custom_elements']['bc_model_viewer'] === true ) {				
			add_filter('script_loader_tag', [$this, 'bc_add_module_attribute'], 10, 3);
			if (current_user_can('administrator')) {
				add_filter('wp_check_filetype_and_ext', [$this, 'bc_support_gltf_glb_extension'], 10, 5);
				add_filter('upload_mimes', [$this, 'bc_custom_upload_mimes_types'], 10, 1);
			}
		}
		
		add_action( 'init', [$this, 'bc_register_custom_elements'], 11, 1 );
		add_filter( 'bricks/builder/first_element_category', [$this, 'bc_first_elements_category'], 11, 3 );
	}
	
	public function bc_add_module_attribute($tag, $handle, $src) {
		if ($handle === 'bc-model-viewer') {
			$tag = '<script type="module" src="' . esc_url($src) . '"></script>';
		}
		return $tag;	
	}
	
	public function bc_support_gltf_glb_extension($data, $file, $filename, $mime_types, $real_mime_type) {
		if (empty($data['ext'])
			|| empty($data['type'])
		) {
			$file_type = wp_check_filetype($filename, $mime_types);

			if ('gltf' === $file_type['ext']) {
				$data['ext']  = 'gltf';
				$data['type'] = 'model/gltf+json';
			}

			if ('glb' === $file_type['ext']) {
				$data['ext']  = 'glb';
				$data['type'] = 'model/glb-binary';
			}
		}
		return $data;
	}
	
	public function bc_custom_upload_mimes_types( $mimes ) {
		$mimes['gltf'] = 'model/gltf+json';
		$mimes['glb'] = 'model/gltf-binary';
		return $mimes;
	}
	
	public function bc_register_custom_elements() {
		foreach ($this->custom_elements as $option => $class_name) {
			if ( !empty($this->acf_options) && isset($this->acf_options['bc_custom_elements'], $this->acf_options['bc_custom_elements'][$option]) 
			&& $this->acf_options['bc_custom_elements'][$option] === true && !class_exists($class_name) )  {
				$class_file = BRICKSCODES_PLUGIN_DIR . 'includes/custom-elements/' . strtolower(str_replace('_', '-', $class_name)) . '.php';
				require_once $class_file;
				\Bricks\Elements::register_element( $class_file );
			}
		}
	}

	// custom element category on top list
	public function bc_first_elements_category( $category, $post_id, $post_type ) {		
		foreach ( $this->custom_elements as $option => $element ) {
			if ( !empty($this->acf_options) && isset($this->acf_options['bc_custom_elements'], $this->acf_options['bc_custom_elements'][$option]) 
			&& $this->acf_options['bc_custom_elements'][$option] === true ) {
				$category = 'brickscodes';
				break;
			}
		}
		return $category;
	}
	
}