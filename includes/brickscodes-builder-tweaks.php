<?php
class Brickscodes_Builders {
	private $acf_options = [];
	
	public function __construct() {
		if (class_exists('ACF')) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$fields = get_fields($settings_page->ID);
				$this->acf_options = !empty($fields) ? $fields : [];
				add_filter( 'bricks/acf/filter_field_groups', [$this, 'bc_filter_acf_field_groups'], 11, 1);
			}	
		} else {
			$this->acf_options = [];
		}
		
		if ( !empty($this->acf_options) && isset($this->acf_options['bc_builder_tweaks']) && 
			( (isset($this->acf_options['bc_builder_tweaks']['bc_disable_elements_header']) && !empty($this->acf_options['bc_builder_tweaks']['bc_disable_elements_header'])) ||
				(isset($this->acf_options['bc_builder_tweaks']['bc_disable_elements_content']) && !empty($this->acf_options['bc_builder_tweaks']['bc_disable_elements_content'])) ||
				(isset($this->acf_options['bc_builder_tweaks']['bc_disable_elements_footer']) && !empty($this->acf_options['bc_builder_tweaks']['bc_disable_elements_footer']))
			) ) {
			add_filter( 'page_row_actions', [$this, 'bc_add_post_id_to_builder_link'], 11, 2);
			add_filter( 'post_row_actions', [$this, 'bc_add_post_id_to_builder_link'], 11, 2 );
			add_action( 'admin_enqueue_scripts', [$this, 'bc_gutenberg_scripts'], 11, 1 );
			add_filter( 'bricks/builder/elements', [$this, 'bc_disable_elements_by_template_type_in_builder'], 11, 1);
		}
		if ( !empty($this->acf_options) && isset($this->acf_options['bc_builder_tweaks']) && 
			isset($this->acf_options['bc_builder_tweaks']['bc_save_message']) && !empty($this->acf_options['bc_builder_tweaks']['bc_save_message'])
		)  {
			add_filter( 'bricks/builder/save_messages', [$this, 'bc_custom_saved_messages'], 11, 1 );
		}
		if ( !empty($this->acf_options) && isset($this->acf_options['bc_builder_tweaks']) && 
			isset($this->acf_options['bc_builder_tweaks']['bc_show_hide_elements']) && $this->acf_options['bc_builder_tweaks']['bc_show_hide_elements'] === true
		)  {
			add_filter( 'bricks/element/render', [$this, 'bc_hide_elements_in_frontend_by_class'], 10, 2 );
		}
	}
	
	public function bc_filter_acf_field_groups($groups) {
		$group_key = '';
		foreach($groups as $group) {
			if ($group['title'] === 'Brickscodes Settings') {
				$group_key = $group['key'];
				break;
			}
		}
		
		if ( !empty($group_key) ) {
			$filtered_groups = array_filter($groups, function($group) use ($group_key) {
				return $group['key'] !== $group_key;
			});

			return $filtered_groups;
		}
		
		return $groups;
	}
	
	public function bc_add_post_id_to_builder_link($actions, $post) {
		$supported_post_types 	= \Bricks\Helpers::get_supported_post_types();
		$template_type 			= \Bricks\Templates::get_template_type($post->ID);
		$post_type 				= $post->post_type;
		if (array_key_exists($post_type, $supported_post_types) || $post_type === 'bricks_template') {
			$builder_edit_link 	= \Bricks\Helpers::get_builder_edit_link( $post->ID );
			$actions['edit_with_bricks'] = '<a href="' . $builder_edit_link . '&post_id=' . $post->ID . '&post_type=' . $post_type . '&template_type=' . $template_type . '">' . __('Edit with Bricks') . '</a>';
		}
		return $actions;
	}

	public function bc_gutenberg_scripts($hook) {
		$supported_post_types = \Bricks\Database::get_setting( 'postTypes', [] );
		$supported_post_types[] = 'bricks_template';

		global $post;
		if (($hook == 'post.php' || $hook == 'post-new.php') && isset($post) && in_array($post->post_type, $supported_post_types)) {
			wp_enqueue_script( 'bc-gutenberg-scripts', BRICKSCODES_PLUGIN_URL . 'admin/assets/js/bc-gutenberg-scripts.js', [], false, true ); 
		}
	}
	
	public function bc_disable_elements_by_template_type_in_builder( $elements ) {
		if (isset($_GET['template_type'])) {
			$template_type = sanitize_text_field($_GET['template_type']);
		} elseif (isset($_GET['post_id'])) {
			$template_type = \Bricks\Templates::get_template_type(intval($_GET['post_id']));
		} else {
			return $elements;
		}
		
		$builder_tweaks = $this->acf_options['bc_builder_tweaks'];
    	$disable_elements = [];
		
		if ($template_type !== 'header' && $template_type !== 'footer') {
			$disable_elements = !empty($builder_tweaks['bc_disable_elements_content']) ? $builder_tweaks['bc_disable_elements_content'] : [];
		} elseif ($template_type === 'header') {
			$disable_elements = !empty($builder_tweaks['bc_disable_elements_header']) ? $builder_tweaks['bc_disable_elements_header'] : [];
		} elseif ($template_type === 'footer') {
			$disable_elements = !empty($builder_tweaks['bc_disable_elements_footer']) ? $builder_tweaks['bc_disable_elements_footer'] : [];
		}

		$elements = array_diff($elements, $disable_elements);
		return $elements;
	}
	
	public function bc_custom_saved_messages( $messages ) {	
		$split_messages = explode("\n", $this->acf_options['bc_builder_tweaks']['bc_save_message']);
		if ( empty($split_messages) ) {
			return $messages;
		} else {
			$split_messages = array_map(function($message) {
				return sanitize_text_field(rtrim(trim($message), ','));
			}, $split_messages);
			return $split_messages;
		}	
	}
	
	public function bc_hide_elements_in_frontend_by_class($render, $element) {	
		if ( !empty( $element->attributes['_root']['class'] ) ) {
			$classes = $element->attributes['_root']['class'];
			if ( $classes && in_array( 'bc-hide-in-frontend', $classes ) ) {
				return false;
			}
		}
		return $render;
	}
}