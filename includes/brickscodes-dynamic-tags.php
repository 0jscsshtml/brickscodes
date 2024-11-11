<?php
class Brickscodes_Dynamic_Tags {
	private $options;
	private $acf_options;
	private $bc_dd_tags;
	
	public function __construct() {
		$this->options = get_option('brickscodes');
		$this->bc_dd_tags = ['bc_user_ip' => 'User IP Address', 'bc_user_last_login' => 'Add number of days after :', 'bc_user_register_date' => 'Get User Registered Date', 'bc_post_type_taxonomy_terms' => 'Post Type Taxonomy Terms'];
		if (class_exists('ACF')) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$fields = get_fields($settings_page->ID);
				$this->acf_options = !empty($fields) ? $fields : [];
			}
		} else {
			$this->acf_options = [];
		}
		
		if ( !empty($this->acf_options) && isset($this->acf_options['bc_custom_dynamic_tags'], $this->acf_options['bc_custom_dynamic_tags']['bc_user_last_login']) 
			&& $this->acf_options['bc_custom_dynamic_tags']['bc_user_last_login'] === true ) {
				add_action('wp_login', [ $this, 'bc_get_set_user_login' ], 10, 2);
		}
		
		add_filter( 'bricks/dynamic_tags_list', [ $this, 'bc_tag_group' ], 10, 1);
		add_filter( 'bricks/dynamic_data/render_tag', [ $this, 'bc_get_tag_value' ], 10, 3 );
		add_filter( 'bricks/dynamic_data/render_content', [ $this, 'bc_render_tag' ], 10, 3 );
		add_filter( 'bricks/frontend/render_data', [ $this, 'bc_render_tag' ], 10, 2 );
		
	}
	
	public function bc_get_set_user_login($user_login, $user) {
		if ( metadata_exists('user', $user->ID, 'bc_user_current_login' ) ) {
			$last_login = get_user_meta($user->ID, 'bc_user_current_login', true);
		} else {
			update_user_meta( $user->ID, 'bc_user_current_login', time() );
			$last_login = time();
		}
		update_user_meta( $user->ID, 'bc_user_last_login', $last_login );
		update_user_meta( $user->ID, 'bc_user_current_login', time() );
	}
	
	public function bc_tag_group($tags) {
		$new_tags = [];

		foreach ($this->bc_dd_tags as $tag => $label) {
			if ( !empty($this->acf_options) && isset($this->acf_options['bc_custom_dynamic_tags'], $this->acf_options['bc_custom_dynamic_tags'][$tag]) 
				&& $this->acf_options['bc_custom_dynamic_tags'][$tag] === true ) {
				$new_tags[] = [
					'name'  => '{' . $tag . '}',
					'label' => $label,
					'group' => 'Brickscodes'
				];
			}
		}
		return array_merge($new_tags, $tags);
	}
	
	public function bc_get_tag_value( $tag, $post, $context = 'text' ) {		
		foreach( $this->bc_dd_tags as $dynamicTag => $label ) {	
			// image element with dd return in array !!
			if (is_array($tag)) {
				continue;
			}	
			// $tag is with curly {dd_tag}
			if (strpos($tag, '{' . $dynamicTag . ':') !== false && strpos($tag, '{' . $dynamicTag . ' @fallback:') !== true) {					
				$argument = str_replace($dynamicTag . ':', '', $tag); // with curly braket	
				$argument = str_replace(['{', '}'], '', $argument); // strip curly braket
				return $this->bc_run_dd_tag_arg($argument, $tag);
			} else if ( $tag === '{' . $dynamicTag . '}' || (strpos($tag, '{' . $dynamicTag . ' @fallback:') !== false) ) {				
				return $this->bc_run_dd_tag($tag);
			}
		}	
		return $tag;
	}
	
	private function bc_run_dd_tag_arg($argument, $tag) {
		$output = '';
		if (strpos($tag, '{bc_user_last_login:') !== false && strpos($tag, '{bc_user_last_login @fallback:') !== true) {
			$user_id = intval(get_current_user_id());
			if ( $user_id ) {	
				$last_login = !empty(get_user_meta($user_id, 'bc_user_last_login', true)) ? intval(get_user_meta($user_id, 'bc_user_last_login', true)) : 0;
				$current_time = time();
				$days_ago = $current_time - (intval($argument) * 24 * 60 * 60);	
				if ( !empty($last_login) ) {
					if ($last_login < $days_ago) {
						$output = esc_html( 'Last login on: ' . date(get_option('date_format'), $last_login) );
					} else {
						$output = sprintf( __( 'Last login on: %s ago', 'brickscode'), human_time_diff( $last_login, $current_time ) );
					}
				} else {
					$output = esc_html('No record');
				}
			} else {
				$output = esc_html('User is logged out.');
			}
		} else if (strpos($tag, '{bc_post_type_taxonomy_terms:') !== false && strpos($tag, '{bc_post_type_taxonomy_terms @fallback:') !== true) {
			$args = explode(':', $argument);
			if ( count($args) === 2 ) {
				$post_type_slug = $args[0];
				$taxonomy_slug = $args[1];
				$output = Brickscodes_Helpers::bc_get_post_type_taxonomies($post_type_slug, $taxonomy_slug );		
			} else if ( count($args) === 1 ) {
				$post_type_slug = $args[0];
				$output = Brickscodes_Helpers::bc_get_post_type_taxonomies($post_type_slug, '' );	
			}		
		}
		return $output;
	}
	
	public function bc_run_dd_tag($tag) {
		$output = '';
		if ( $tag === '{bc_user_ip}' || strpos($tag, '{bc_user_ip @fallback:') !== false ) {
			$output = Brickscodes_Helpers::bc_get_user_ip_address();
		} else if ( $tag === '{bc_user_last_login}' || strpos($tag, '{bc_user_last_login @fallback:') !== false ) {
			$user_id = intval(get_current_user_id());
			if ( $user_id ) {
				$last_login = !empty(get_user_meta($user_id, 'bc_user_last_login', true)) ? get_user_meta($user_id, 'bc_user_last_login', true) : 0;
				$current_time = time();
				if ( !empty($last_login) ) {
					$output = esc_html( 'Last login on: ' . date(get_option('date_format'), $last_login) );
				} else {
					$output = esc_html('No record');
				}
			} else {
				$output = esc_html('User is logged out.');
			}
		} else if ( $tag === '{bc_user_register_date}' || strpos($tag, '{bc_user_register_date @fallback:') !== false ) {
			$user_id = intval(get_current_user_id());
			if ( $user_id ) {
				$user_data = get_userdata( $user_id );
				$registered_date = $user_data->user_registered;
				$output = date_i18n(get_option('date_format'), strtotime($registered_date));
			} else {
				$output = '';
			}
		}

	//	$output = ''; //simulate empty output to test fallback
		if (empty($output)) {
			$value = strpos($tag, '@fallback:');
			if ($value !== false) {
    			$fallback_value = strpos($tag, ':', $value);
				if ($fallback_value !== false) {
					$output = substr($tag, $fallback_value + 1);
					$output = trim($output, "'} ");
				}
			}
		}		
		return $output;
	}
	
	public function bc_render_tag($content, $post, $context = 'text') {	
		// $tag in $content with curly bracket
		foreach ($this->bc_dd_tags as $dynamicTag => $label) {
			if (strpos($content, '{' . $dynamicTag . ':') !== false && strpos($content, '{' . $dynamicTag . ' @fallback:') !== true) {
			//	$pattern = '/\{' . preg_quote($dynamicTag, '/') . '\s*@:\'[^}]*\'\}/';
				preg_match_all('/{(' . $dynamicTag . ':[^}]+)}/', $content, $matches);	// $matches[0] fulltag, $matches[1] without curly bracket			
				if (!empty($matches[0])) {								
					foreach ( $matches[1] as $key => $match ) {
						$parts = explode(':', $match);		
						if ( count($parts) === 2 ) {
							$value = $this->bc_run_dd_tag_arg($parts[1], $matches[0][0]);
						} else if ( count($parts) === 3 ) {
							$argument = $parts[1] . ':' . $parts[2];
							$value = $this->bc_run_dd_tag_arg($argument, $matches[0][0]);
						}
						$content = str_replace($matches[0][0], $value, $content);	
					}
				}
			} else if (strpos($content, '{' . $dynamicTag . '}') !== false || strpos($content, '{' . $dynamicTag . ' @fallback:') !== false) {				
				$pattern = '/\{' . preg_quote($dynamicTag, '/') . '\s*@fallback:\'[^}]*\'\}/';			
				if (preg_match($pattern, $content, $matches)) {				
					$fullTag = $matches[0];				
					$value = $this->bc_run_dd_tag($fullTag);
					$content = preg_replace($pattern, $value, $content);
				} else {
					$value = $this->bc_run_dd_tag('{' . $dynamicTag . '}');
					$content = str_replace( '{' . $dynamicTag . '}', $value, $content );
				}
			}
		}	

		return $content;
	}
	
}