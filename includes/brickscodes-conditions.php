<?php
class Brickscodes_Conditions {
	private $options;
	private $acf_options;
	
	public function __construct() {
		if (class_exists('ACF')) {
			$settings_page = get_page_by_path('brickscodes-options', OBJECT, 'page');
			if ($settings_page) {
				$this->acf_options = !empty(get_fields($settings_page->ID)) ? get_fields($settings_page->ID) : [];
			}
		} else {
			$this->acf_options = [];
		}
		
		$this->options = get_option('brickscodes');
		
		if ( !empty($this->acf_options) && isset($this->acf_options['bc_custom_conditions'], $this->acf_options['bc_custom_conditions']['bc_acf_template_conditions']) &&
			$this->acf_options['bc_custom_conditions']['bc_acf_template_conditions'] === true ) {
			add_filter( 'builder/settings/template/controls_data', [$this, 'bc_extra_template_condition_controls'], 10, 1 );
			add_filter( 'bricks/screen_conditions/scores', [$this, 'bc_set_active_templates'], 10, 4 );
		}
	}
	
	public function bc_extra_template_condition_controls($data) {	
		$data['controls']['templateConditions']['fields']['main']['options']['templateCustomField'] = 'Custom Fiedls Dynamic Tag';

		$data['controls']['templateConditions']['fields']['templateCustomFieldName'] = [
			'label'			=> esc_html__( 'Custom Field', 'brickscodes' ),
			'type' 			=> 'text',
			'inlineEditing' => false,
			'required' 		=> ['main', '=', 'templateCustomField' ],
		]; 

		$data['controls']['templateConditions']['fields']['templateCustomFieldOperator'] = [
			'type' 		=> 'select',
			'options'	=> [
				'==' => '==',
				'!=' => '!=',
				'>=' => '>=',
				'<=' => '<=',
				'>'  => '>',
				'<'  => '<',
			],
			'placeholder' => esc_html__( '==', 'brickscodes' ),
			'required' 		=> [
				['main', '=', 'templateCustomField' ],
			],
		]; 

		$data['controls']['templateConditions']['fields']['templateCustomFieldValue'] = [
			'type' 			=> 'text',
			'inlineEditing' => false,
			'description' => esc_html__( 'Condition true add 9 to Scores', 'brickscodes' ),
			'required' 		=> ['main', '=', 'templateCustomField' ],
		]; 

		$all_roles = wp_roles()->roles;
		$roles = [];

		foreach ( $all_roles as $role => $role_data ) {
			$roles[ $role ] = $role_data['name'];
		}

		// Add control to select the user roles for an author archive template type
		$data['controls']['templateConditions']['fields']['templateArchiveAuthorRoles'] = [
			'type'        => 'select',
			'label'       => esc_html__( 'Author roles', 'brickscodes' ),
			'options'     => $roles,
			'multiple'    => true,
			'placeholder' => esc_html__( 'Select role', 'brickscodes' ),
			'description' => esc_html__( 'Leave empty to apply template to all roles.', 'brickscodes' ),
			'required'    => [ 
				['archiveType', '=', 'author' ],
				['main', '=', 'archiveType'],
			]	
		];	
		
		return $data;
	}
	
	public function bc_set_active_templates($scores, $condition, $post_id, $preview_type) {	
		if ( is_author() && $condition['main'] === 'archiveType' && isset( $condition['archiveType'] ) && in_array( 'author', $condition['archiveType'] ) && isset( $condition['templateArchiveAuthorRoles'] ) ) { 
			$user = get_queried_object();

			if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role_name ) {
					if ( in_array( $role_name, $condition['templateArchiveAuthorRoles'] ) ) {
						$scores[] = 9;
					}
				}
			}
		}
  
		if ( $condition['main'] === 'templateCustomField' && isset($condition['templateCustomFieldName'], $condition['templateCustomFieldOperator'], $condition['templateCustomFieldValue']) && !isset( $condition['exclude'] ) ) {
			$dynamic_tag_field = $condition['templateCustomFieldName'];
			$dynamic_tag_field_value = bricks_render_dynamic_data( $dynamic_tag_field, $post_id);		
			$compare_value = trim($condition['templateCustomFieldValue']);
		
			if (in_array($compare_value, ['1', 'true', 'True'])) {
				$compare_value = 'True';
			} elseif (in_array($compare_value, ['0', 'false', 'False'])) {
				$compare_value = 'False';
			} elseif (is_numeric($compare_value)) {
				$compare_value = strpos($compare_value, '.') !== false ? (float)$compare_value : (int)$compare_value;
			} else {
				$compare_value = filter_var($compare_value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			}
		
			$isTrue = false;
			switch ($condition['templateCustomFieldOperator']) {
				case '==':
					$isTrue = $dynamic_tag_field_value == $compare_value ? true : false;				
				break;
				case '!=':
					$isTrue = $dynamic_tag_field_value != $compare_value ? true : false;
				break;
				case '>=':
					$isTrue = intval($dynamic_tag_field_value) >= $compare_value ? true : false;
				break;
				case '<=':
					$isTrue = intval($dynamic_tag_field_value) <= $compare_value ? true : false;
				break;
				case '>':
					$isTrue = intval($dynamic_tag_field_value) > $compare_value ? true : false;
				break;
				case '<':
					$isTrue = intval($dynamic_tag_field_value) < $compare_value ? true : false;
				break;
			}

			if ($isTrue) {
				$scores[] = 10;
			}	
		}
			
		return $scores;
	}	
	
}