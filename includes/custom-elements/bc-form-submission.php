<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Bc_Form_Submission extends \Bricks\Element {
	public $category     = 'brickscodes';
	public $name         = 'bc-form-submission';
	public $icon         = 'ti-server';
	public $scripts      = ['saveFormSubmissionFn'];
	private $datatables_modal_controls;
	private $datatables_modal_desktop_css = '';
	private $datatables_modal_css = '';
	
	public function get_label() {
		return esc_html__( 'View Form Submission', 'brickscodes' );
	}
	
	public function get_keywords() {
		return [ 'Form', 'Submission', 'Query' ];
	}
	
	public function set_control_groups() {
		if ( isset(\Bricks\Database::$global_data['settings']['saveFormSubmissions']) ) {
			$this->control_groups['tableStyle'] = [
				'title' 	=> esc_html__( 'Table', 'brickscodes' ),
				'required' 	=> [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
				]	
			];
			
			$this->control_groups['datatableStyle'] = [
				'title' 	=> esc_html__( 'Datatable', 'brickscodes' ),
				'required' 	=> [
					['queryFormId', '!=', '' ],
					['advanceTable', '=', true],
				]	
			]; 
		}
		
	}
	
	private function bc_get_forms_options() {
		$form_options = [];
		if ( isset(\Bricks\Database::$global_data['settings']['saveFormSubmissions']) && \Bricks\Capabilities::current_user_has_full_access() && \Bricks\Integrations\Form\Submission_Database::check_managed_db_access() ) {
			global $wpdb;
			$table_name = \Bricks\Integrations\Form\Submission_Database::get_table_name();			
			$query = "SELECT DISTINCT form_id FROM $table_name";	
			$all_forms_ids = $wpdb->get_col($query);
			
			if ( !empty($all_forms_ids) ) {
				foreach($all_forms_ids as $form_id) {
					$deleted_form = empty(\Bricks\Integrations\Form\Submission_Database::get_form_name_by_id( $form_id )) ? true : false;
					$form_options[$form_id] = !$deleted_form ? \Bricks\Integrations\Form\Submission_Database::get_form_name_by_id( $form_id ) : $form_id . '(deleted)';
				}
			}		
		}

		return $form_options;
	}
	
	public function set_controls() {
		if ( !isset(\Bricks\Database::$global_data['settings']['saveFormSubmissions']) ) {
			$this->controls['saveSubmissionInfo'] = [
				'content' 	=> esc_html__( 'Save Form Submission is disable.', 'brickscodes' ),
				'type' 		=> 'info',
			];
		} else {
		
			$this->controls['queryFormId'] = [
				'label'       => esc_html__( 'Select Available Form', 'brickscodes' ),
				'type'        => 'select',
				'options'	  => $this->bc_get_forms_options(),
				'placeholder' => esc_html__( 'Select', 'brickscodes' ),
				'fullAccess'  => true,
			];
			
			$all_roles = wp_roles()->roles;
			$roles = [];
			$roles[ 'grantedRoles' ] = [];
			
			foreach ( $all_roles as $role => $role_data ) {
				// Check if the role can publish posts but cannot manage options
				if ( 
					isset($role_data['capabilities']['publish_posts']) && 
					$role_data['capabilities']['publish_posts'] && 
					(!isset($role_data['capabilities']['manage_options']) || !$role_data['capabilities']['manage_options']) 
				) {
					;
					$roles[ 'grantedRoles' ][ $role ] = $role_data['name'];
				}
				if ( !isset($role_data['capabilities']['manage_options']) || !$role_data['capabilities']['manage_options'] ) {
					$roles[ 'exportRoles' ][ $role ] = $role_data['name'];
				}
			}
			
			$this->controls['grantUserRoles'] = [
				'label'       => esc_html__( 'Grant User Roles to Access This Form Submission (Roles that can "publish_posts" but not "manage_options")', 'brickscodes' ),
				'type'        => 'select',
				'options'	  => $roles[ 'grantedRoles' ],
				'multiple'	  => true,
				'placeholder' => esc_html__( 'Select', 'brickscodes' ),
				'description' => esc_html__( 'Return all submission of this form', 'brickscodes' ),
				'required' 	  => ['queryFormId', '!=', '' ],
			];
			
			$this->controls['excludeFormField'] = [
				'label'       => esc_html__( 'Exclude Form Fields', 'brickscodes' ),
				'description' => esc_html__( 'Exclude Sensitive Data Field', 'brickscodes' ),
				'type'        => 'select',
				'options'	  => [],
				'multiple'	  => true,
				'placeholder' => esc_html__( 'Select', 'brickscodes' ),
				'required'    => ['queryFormId', '!=', '' ],
			];
			
			$this->controls['advanceTable'] = [
				'label' 		=> esc_html__( 'Use Advance Table (Datatables)', 'brickscodes' ),
				'description' 	=> esc_html__( 'This will enqueue extra scripts (jQuery & Datatables). Please reload builder for each toggle.', 'brickscodes' ),
				'type' 			=> 'checkbox',
				'fullAccess'  	=> true,
				'required'    	=> ['queryFormId', '!=', '' ],
			];
			
			$this->controls['advanceTableResponsive'] = [
				'label' 		=> esc_html__( 'Responsive', 'brickscodes' ),
				'description' 	=> esc_html__( 'This will enqueue extra scripts (Datatables)', 'brickscodes' ),
				'type' 			=> 'checkbox',
				'required'    	=> [
					['queryFormId', '!=', '' ],
					['advanceTable', '=', true]
				]	
			];
			
			$this->controls['advanceTableColReorder'] = [
				'label' 		=> esc_html__( 'Column Reorder', 'brickscodes' ),
				'description' 	=> esc_html__( 'This will enqueue extra scripts (Datatables)', 'brickscodes' ),
				'type' 			=> 'checkbox',
				'required'    	=> [
					['queryFormId', '!=', '' ],
					['advanceTable', '=', true]
				]	
			];
			
			$this->controls['advanceTableExport'] = [
				'label' 		=> esc_html__( 'Enable Export (PDF, CSV, XLSX)', 'brickscodes' ),
				'description' 	=> esc_html__( 'This will enqueue extra scripts (Datatables)', 'brickscodes' ),
				'type' 			=> 'checkbox',
				'required'    	=> [
					['queryFormId', '!=', '' ],
					['advanceTable', '=', true]
				]	
			];
			
			unset($roles['administrator']);
			
			$this->controls['grantUserExportRoles'] = [
				'label'       => esc_html__( 'User Roles Can Export', 'brickscodes' ),
				'type'        => 'select',
				'options'	  => $roles[ 'exportRoles' ],
				'multiple'	  => true,
				'placeholder' => esc_html__( 'Select', 'brickscodes' ),
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '=', true],
					['advanceTableExport', '=', true]
				]
			];
			
			$this->controls['filterByDate'] = [
				'label' 	=> esc_html__( 'Filter By Start/End Date', 'brickscodes' ),
				'type' 		=> 'checkbox',
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true]
				]	
			];
			
			$this->controls['startDate'] = [
				'label' 	=> esc_html__('Start Date', 'brickscodes'),
				'type' 		=> 'datepicker',
				'options' 	=> [
					'enableTime' 	=> false,
					'time_24hr' 	=> false,
					'altFormat' 	=> get_option('date_format'),
					'dateFormat' 	=> get_option('date_format'),
					'mode'			=> 'single',
					'locale'		=> ['firstDayOfWeek' => intval(get_option('start_of_week'))],
				],
				'inline'	=> true,
				'required'	=> [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['filterByDate', '=', true]
				]	
			];
			
			$this->controls['endDate'] = [
				'label' 	=> esc_html__('End Date', 'brickscodes'),
				'type' 		=> 'datepicker',
				'options' 	=> [
					'enableTime' 	=> false,
					'time_24hr' 	=> false,
					'altFormat' 	=> get_option('date_format'),
					'dateFormat' 	=> get_option('date_format'),
					'mode'			=> 'single',
					'locale'		=> ['firstDayOfWeek' => intval(get_option('start_of_week'))],
				],
				'inline'	=> true,
				'required'  => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['filterByDate', '=', true]
				]
			];
			
			$this->controls['limitEntries'] = [
				'label'       => esc_html__( 'Limit Total Entries Return', 'brickscodes' ),
				'type'        => 'number',
				'unitless'	  => true,
				'step'		  => 1,
				'min'		  => 10,
				'default'	  => 50,
				'placeholder' => esc_html__( '50', 'brickscodes' ),
				'required'    => ['queryFormId', '!=', '' ],
			];
			
			$this->controls['pagination'] = [
				'label' 	=> esc_html__( 'Enable Pagination', 'brickscodes' ),
				'type' 		=> 'checkbox',
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true]
				]	
			];
			
			$this->controls['rowPerPage'] = [
				'label'       => esc_html__( 'Rows per page', 'brickscodes' ),
				'type'        => 'number',
				'unitless'	  => true,
				'step'		  => 1,
				'default'	  => 10,
				'placeholder' => esc_html__( '50', 'brickscodes' ),
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]	
			];
			
			$this->controls['tableTitle'] = [
				'label' 		=> esc_html__( 'Table Title', 'brickscodes' ),
				'type' 			=> 'text',
				'inlineEditing' => false,
				'required'    => ['queryFormId', '!=', '' ],
			];
			
			$this->controls['noRecordsMessage'] = [
				'label' 		=> esc_html__( 'No Records Message', 'brickscodes' ),
				'type' 			=> 'text',
				'inlineEditing' => false,
				'placeholder' 	=> esc_html__( 'No records found.', 'brickscodes' ),
				'required'    => ['queryFormId', '!=', '' ],
			];

			$this->controls['tableTypography'] = [
				'label' => esc_html__( 'Typography', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => '.table-wrapper > table.bc-table',
					],
				],
				'inline' => true,
			];
			
			$this->controls['tableCaption'] = [
				'label'	=> esc_html__( 'Table Caption', 'brickscodes' ),
				'type'  => 'separator',
				'group'	=> 'tableStyle',
			];
			
			$this->controls['tableCaptionTypography'] = [
				'label' => esc_html__( 'Typography', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => '.table-caption',
					],
				],
				'inline' => true,
			];
			
			$this->controls['tableThead'] = [
				'label'	=> esc_html__( 'THead', 'brickscodes' ),
				'type'  => 'separator',
				'group'	=> 'tableStyle',
			];
			
			$this->controls['tableTheadPadding'] = [
				'label' => esc_html__( 'Padding', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'spacing',
				'css' 	=> [
					[
					  'property' => 'padding',
					  'selector' => '.table-wrapper > table.bc-table th',
					]
				],
				'default' => [
					'top' => '12px',
					'right' => '16px',
					'bottom' => '12px',
					'left' => '16px',
				],
			];
			
			$this->controls['tableTheadTypography'] = [
				'label' => esc_html__( 'Typography', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => '.table-wrapper > .bc-table th',
					],
				],
				'inline' => true,
			];
			
			$this->controls['tableTheadBackgroundColor'] = [
				'label' => esc_html__( 'Background Color', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > table.bc-table th',
					]
				],
			];
			
			$this->controls['tableTheadBorder'] = [
				'label' => esc_html__( 'Border', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'border',
				'css' 	=> [
					[
						'property' => 'border',
						'selector' => '.table-wrapper > table.bc-table th',
					],
				],
				'inline' => true,
				'small'  => true,
			];
			
			$this->controls['tableTBody'] = [
				'label'	=> esc_html__( 'TBody', 'brickscodes' ),
				'type'  => 'separator',
				'group'	=> 'tableStyle',
			];
			
			$this->controls['tableTbodyPadding'] = [
				'label' => esc_html__( 'Padding', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'spacing',
				'css' 	=> [
					[
					  'property' => 'padding',
					  'selector' => '.table-wrapper > table.bc-table td',
					]
				],
				'default' => [
					'top' => '12px',
					'right' => '16px',
					'bottom' => '12px',
					'left' => '16px',
				],
			];
			
			$this->controls['tableTbodyBackgroundColor'] = [
				'label' => esc_html__( 'Background Color', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > table.bc-table td',
					]
				],
			];
			
			$this->controls['tableTbodyOddBackgroundColor'] = [
				'label' => esc_html__( 'Odd Rows Background Color', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > table.bc-table tbody > tr:nth-child(odd)',
					],
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > table.bc-table tbody > tr:nth-child(odd) td',
					]
				],
			];
			
			$this->controls['tableTbodyEvenBackgroundColor'] = [
				'label' => esc_html__( 'Even Rows Background Color', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > table.bc-table tbody > tr:nth-child(even)',
					],
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > table.bc-table tbody > tr:nth-child(even) td',
					]
				],
			];
			
			$this->controls['tableTbodyBorder'] = [
				'label' => esc_html__( 'Border', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'border',
				'css' 	=> [
					[
						'property' => 'border',
						'selector' => '.table-wrapper > table.bc-table td',
					],
				],
				'inline' => true,
				'small' => true,
			];
			
			$this->controls['tablePaginationSep'] = [
				'label'	=> esc_html__( 'Pagination', 'brickscodes' ),
				'type'  => 'separator',
				'group'	=> 'tableStyle',
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];
			
			$this->controls['tablePaginationPadding'] = [
				'label' => esc_html__( 'Padding', 'brickscodes' ),
				'type' => 'spacing',
				'group'	=> 'tableStyle',
				'css' => [
					[
						'property' => 'padding',
						'selector' => '.table-wrapper > .pagination',
					]
				],
				'default' => [
					'top' => '12px',
					'right' => '16px',
					'bottom' => '12px',
					'left' => '16px',
				],
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];

			$this->controls['tablePaginationJustifyContent'] = [
				'label' => esc_html__( 'Justify content', 'brickscodes' ),
				'type'  => 'justify-content',
				'group'	=> 'tableStyle',
				'css'   => [
					[
						'property' => 'justify-content',
						'selector' => '.table-wrapper > .pagination',
					],
				],
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];

			$this->controls['tablePaginationGap'] = [
				'label'       => esc_html__( 'Gap', 'brickscodes' ),
				'type'        => 'number',
				'group'		  => 'tableStyle',
				'units'	  	  => true,
				'css' 	=> [
					[
						'property' => 'gap',
						'selector' => '.table-wrapper > .pagination',
					],
				],
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];
			
			$this->controls['tablePaginationBackgroundColor'] = [
				'label' => esc_html__( 'Background Color', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > .pagination',
					],
				],
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];
			
			$this->controls['tablePaginationLinkPadding'] = [
				'label' => esc_html__( 'Link Padding', 'brickscodes' ),
				'type' => 'spacing',
				'group'	=> 'tableStyle',
				'css' => [
					[
						'property' => 'padding',
						'selector' => '.table-wrapper > .pagination > a',
					]
				],
				'default' => [
					'top' => '4px',
					'right' => '12px',
					'bottom' => '4px',
					'left' => '12px',
				],
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];
			
			$this->controls['tablePaginationLinkTypography'] = [
				'label' => esc_html__( 'Link Typography', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => '.table-wrapper > .pagination > a',
					],
				],
				'inline' => true,
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];
			
			$this->controls['tablePaginationLinkBackgroundColor'] = [
				'label' => esc_html__( 'Link Background Color', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => '.table-wrapper > .pagination > a',
					],
				],
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];
			
			$this->controls['tablePaginationLinkBorder'] = [
				'label' => esc_html__( 'Link Border', 'brickscodes' ),
				'group'	=> 'tableStyle',
				'type' 	=> 'border',
				'css' 	=> [
					[
						'property' => 'border',
						'selector' => '.table-wrapper > .pagination > a',
					],
				],
				'inline' => true,
				'small' => true,
				'required'    => [
					['queryFormId', '!=', '' ],
					['advanceTable', '!=', true],
					['pagination', '=', true]
				]
			];
			
			$this->controls['datatableThead'] = [
				'label'	=> esc_html__( 'THead', 'brickscodes' ),
				'type'  => 'separator',
				'group'	=> 'datatableStyle',
			];
			
			$this->controls['datatableTheadTypography'] = [
				'label' => esc_html__( 'Typography', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => 'table.bc-advance-table thead th',
					],
					[
						'property' => 'typography',
						'selector' => 'table.bc-advance-table tfoot th',
					],
				],
				'inline' => true,
			];
			
			$this->controls['datatableTheadBackgroundColor'] = [
				'label' => esc_html__( 'Background Color', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => 'table.bc-advance-table thead',
					],
					[
						'property' => 'background-color',
						'selector' => 'table.bc-advance-table thead',
					]
				],
			];
			
			$this->controls['datatableTbody'] = [
				'label'	=> esc_html__( 'Tbody', 'brickscodes' ),
				'type'  => 'separator',
				'group'	=> 'datatableStyle',
			];
			
			$this->controls['datatableTbodyTypography'] = [
				'label' => esc_html__( 'Typography', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => 'table.bc-advance-table.stripe > tbody',
					],
				],
				'inline' => true,
			];
			
			$this->controls['datatableTbodyRowsBorder'] = [
				'group'	=> 'datatableStyle',
				'label' => esc_html__( 'Rows border', 'brickscodes' ),
				'type' => 'border',
				'css' => [
					[
						'property' => 'border',
						'selector' => 'table.bc-advance-table.stripe tbody > tr:not(:last-child) > td',
					],
				],
				'inline' => true,
				'small' => true,
			];
			
			$this->controls['datatableTbodyOddBackgroundColor'] = [
				'label' => esc_html__( 'Odd Rows Background Color', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' => 'box-shadow',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'box-shadow',
						'selector' => 'table.bc-advance-table.stripe tbody > tr:nth-child(odd) > *',
					],
				],
				'inline' => true,
				'small' => true,
				'default' => [
					'values' => [
						'offsetX' => 0,
						'offsetY' => 0,
						'blur' => 0,
						'spread' => 9999,
					],
					'color' => [
					  'rgb' => 'rgba(0, 0, 0, .023)',
					],
					'inset' => true,
				],
			];
			
			$this->controls['datatableTbodyEvenBackgroundColor'] = [
				'label' => esc_html__( 'Even Rows Background Color', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' => 'box-shadow',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'box-shadow',
						'selector' => 'table.bc-advance-table.stripe tbody > tr:nth-child(even) > *',
					],
				],
				'inline' => true,
				'small' => true,
				'default' => [
					'values' => [
						'offsetX' => 0,
						'offsetY' => 0,
						'blur' => 0,
						'spread' => 9999,
					],
					'color' => [
					  'rgb' => 'rgba(255, 255, 255, 1)',
					],
					'inset' => true,
				],
			];
			
			$this->controls['datatableModalSep'] = [
				'label'	=> esc_html__( 'Modal', 'brickscodes' ),
				'type'  => 'separator',
				'group'	=> 'datatableStyle',
				'required'    => [
					['advanceTable', '=', true],
					['advanceTableResponsive', '=', true]
				]
			];
			
			$this->controls['datatableModalHeadingTypography'] = [
				'label' => esc_html__( 'Heading Typography', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => 'div.dtr-modal-content h2',
					],
				],
				'inline' => true,
			];
			$this->datatables_modal_controls['datatableModalHeadingTypography'] = $this->controls['datatableModalHeadingTypography'];
			
			$this->controls['datatableModalTdTypography'] = [
				'label' => esc_html__( 'Cell Typography', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' 	=> 'typography',
				'css' 	=> [
					[
						'property' => 'typography',
						'selector' => 'table tr > td',
					],
				],
				'inline' => true,
			];
			$this->datatables_modal_controls['datatableModalTdTypography'] = $this->controls['datatableModalTdTypography'];
			
			$this->controls['datatableModalBackgroundColor'] = [
				'label' => esc_html__( 'Background Color', 'brickscodes' ),
				'group'	=> 'datatableStyle',
				'type' 	=> 'color',
				'inline' => true,
				'css' 	=> [
					[
						'property' => 'background-color',
						'selector' => 'div.dtr-modal-display',
					],
				],
			];
			$this->datatables_modal_controls['datatableModalBackgroundColor'] = $this->controls['datatableModalBackgroundColor'];
			
			$this->controls['datatableModalRowsBorder'] = [
				'group'	=> 'datatableStyle',
				'label' => esc_html__( 'Rows border', 'brickscodes' ),
				'type' => 'border',
				'css' => [
					[
						'property' => 'border',
						'selector' => 'table tr',
					],
				],
				'inline' => true,
				'small' => true,
			];
			$this->datatables_modal_controls['datatableModalRowsBorder'] = $this->controls['datatableModalRowsBorder'];
		}
	}
	
	private function bc_map_replace_keys($formData, $form_setting, $excludes) {
		foreach ($formData as $key => $value) {
			if ( !empty($form_setting) && isset($form_setting['fields']) ) {
				foreach ($form_setting['fields'] as $field) {
					if ( $field['id'] === $key ) {
						unset($formData[$key]);

						if ( !in_array($key, $excludes) ) {
							$formData[$field['label']] = array(
								'id' => $key,
								'type' => $field['type'],
								'value' => $value
							);
						}
						break; 
					} 
				}
			} else {
				if ( !in_array($key, ['PID', 'User', 'Created On' , 'Referrer']) ) {
					unset($formData[$key]);
					if ( is_array($value) && isset($value['type']) && !in_array($key, $excludes) ) {
						$formData[$key] = array(
							'id' => $key,
							'type' => $value['type'],
							'value' => $value
						);
					}
				}
			}
		}

		return $formData;
	}
	
	private function bc_generate_html_table($dataArray, $formId, $tableTitle, $settings, $current_user) {
		$isResponsive 	= $settings['advanceTableResponsive'] ?? false;
		$isExport		= $settings['advanceTableExport'] ?? false;
		$isColReorder	= $settings['advanceTableColReorder'] ?? false;
		
		$html = '';
		$this->set_attribute( 'table', 'style', 'width:100%;' );
		$this->set_attribute( 'table', 'data-element-id', $this->id );
		if ( !isset($settings['advanceTable']) ) {
			$this->set_attribute( 'table', 'class', ['bc-table'] );	
			$html .= '<div id="table-' . $formId . '" class="table-caption">' . $tableTitle . '</div>';
			$html .= '<div class="table-wrapper"><table ' . $this->render_attributes( 'table' ) . '></div>';
		} else {
			$this->set_attribute( 'table', 'class', ['bc-advance-table', 'nowrap', 'stripe'] );
			$exportRoles = $settings['grantUserExportRoles'] ?? [];
			$isGrantedExportRoles = array_intersect($exportRoles, ( array )$current_user->roles);
			
			if ( $isResponsive && bricks_is_frontend() ) {
				$this->set_attribute( 'table', 'data-table-responsive', '' );
			}
			if ( (in_array('administrator', $current_user->roles) || !empty($isGrantedExportRoles)) && $isExport && bricks_is_frontend() ) {
				$this->set_attribute( 'table', 'data-table-export', '' );
			}
			if ( $isColReorder && bricks_is_frontend() ) {
				$this->set_attribute( 'table', 'data-table-colReorder', '' );
			}
			$html .= '<table ' . $this->render_attributes( 'table' ) . '>';
		}
		$tableColumns  = [];
		$html .= '<thead role="rowgroup"><tr role="row">';
			
		foreach ($dataArray[0] as $columnName => $columnData) {		
			$html .= '<th role="columnheader"><p>' . $columnName . '</p></th>';
			$tableColumns[] = $columnName;
		}
		$html .= '</tr></thead>';
		$html .= '<tbody role="rowgroup">';
		foreach ($dataArray as $dataItem) {			
			$html .= '<tr role="row">';			
			foreach ($dataItem as $index => $columnData) {				
				if ( !is_array($columnData) ) {			
					if ( $index === 'Referrer' ) {
						$post_id = url_to_postid($columnData);
						if ( $post_id > 0 ) {
							$post_title = get_the_title( $post_id );
						}
						$html .= '<td class="url" role="cell" data-label="' . $index . '"><a href="' . esc_url($columnData) . '" target="_blank" style="color: blue;">' . sanitize_text_field($post_title) . '</a></td>';
					} else if ( $index === 'User' ) {
						$user = get_user_by('id', $columnData);
						if ($user) {
							$username = $user->user_login;
							$user_profile_link = get_edit_user_link($columnData);
							if ( in_array('administrator', $current_user->roles) || $current_user->id === $columnData ) {
								$html .= '<td class="url" role="cell" data-label="' . $index . '"><a href="' . esc_url($user_profile_link) . '" target="_blank" style="color: blue;">' . sanitize_text_field($username) . '</a></td>';
							} else {
								$html .= '<td class="url" role="cell" data-label="' . $index . '"><p>' . sanitize_text_field($username) . '</p></td>';							
							}
						} else {
							$html .= '<td class="url" role="cell" data-label="' . $index . '"><p>N/A</p></td>';	
						}
					} else if ( $index === 'Created On' ) {
						$timestamp = strtotime($columnData);
						$wp_timezone = wp_timezone();
						$date_format = get_option('date_format');
						$time_format = get_option('time_format');
						$combined_format = $date_format . ' ' . $time_format;
						$formatted_date_time = wp_date($combined_format, $timestamp, $wp_timezone);
						$html .= '<td role="cell" data-label="' . $index . '"><p>' . $formatted_date_time . '</p></td>';
					} else {
						$html .= '<td role="cell" data-label="' . $index . '"><p>' . $columnData . '</p></td>';
					}
				} else {			
					if ( isset($columnData['value'], $columnData['value']['value']) && !is_array($columnData['value']['value']) ) {
						if ($columnData['value']['type'] === 'url') {
							$value = '';
							$value .= '<a href="' . esc_url($columnData['value']['value']) . '" target="_blank" style="color: blue;">' . sanitize_text_field($columnData['value']['value']) . '</a>';
							$html .= '<td class="' . $columnData['value']['type'] . '" role="cell" data-label="' . $index . '"><p>' . $value . '</p></td>';
						} else if ($columnData['value']['type'] === 'password') {
							$hasPwd = !empty($columnData['value']['value']) ? '*********' . substr($columnData['value']['value'], -3) . '' : '';
							$html .= '<td class="' . $columnData['value']['type'] . '" role="cell" data-label="' . $index . '"><p>' . $hasPwd . '</p></td>';
						} else {
							$html .= '<td class="' . $columnData['value']['type'] . '" role="cell" data-label="' . $index . '"><p>' . $columnData['value']['value'] . '</p></td>';
						}
					} else if ( is_array($columnData['value']['value'] ) && $columnData['value']['type'] === 'file' ) {
						$value = '';
						foreach( $columnData['value']['value'] as $subItem ) {
							$value .= '<a href="' . esc_url($subItem['url']) . '" target="_blank" style="color: blue;">' . sanitize_text_field($subItem['name']) . '</a><br>';
						}
						$html .= '<td class="' . $columnData['value']['type'] . '" role="cell" data-label="' . $index . '"><p>' . $value . '</p></td>';
					} else if ( is_array($columnData['value']['value'] ) && ($columnData['value']['type'] === 'checkbox' || $columnData['value']['type'] === 'radio') ) {
						$value = '';
						foreach ($columnData['value']['value'] as $item) {
							$value .= $item . ', '; // Adjust this as per your data structure
						}
						$value = substr($value, 0, -2);
						$html .= '<td class="' . $columnData['value']['type'] . '" role="cell" data-label="' . $index . '"><p>' . $value . '</p></td>';
					} 
				}			
			}	
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}
	
	private function bc_render_pagination($current_page, $total_pages) {
		$base_url = get_permalink(); // Base URL of the current page without query params

		$pagination_html = '<div class="pagination">';
		if ($current_page > 1) {
			$prev_page = $current_page - 1;
			$pagination_html .= '<a href="' . add_query_arg('table_content', $prev_page, $base_url) . '">&laquo; Previous</a>';
		}

		for ($i = 1; $i <= $total_pages; $i++) {
			$class = ($i == $current_page) ? 'class="current-page"' : '';
			$pagination_html .= '<a href="' . add_query_arg('table_content', $i, $base_url) . '" ' . $class . '>' . $i . '</a>';
		}

		if ($current_page < $total_pages) {
			$next_page = $current_page + 1;
			$pagination_html .= '<a href="' . add_query_arg('table_content', $next_page, $base_url) . '">Next &raquo;</a>';
		}
		$pagination_html .= '</div>';
		return $pagination_html;
	}

	
	public function render() {
		$settings 				= $this->settings;
		$settingOptions 		= \Bricks\Database::$global_data['settings'];
		$enableSaveSubmission	= isset($settingOptions['saveFormSubmissions']);
		$formId 				= isset($settings['queryFormId']) ? sanitize_text_field($settings['queryFormId']) : false;
		$formName 				= \Bricks\Integrations\Form\Submission_Database::get_form_name_by_id( $formId );
		$granted_roles			= $settings['grantUserRoles'] ?? [];
		$entries 				= isset($settings['limitEntries']) && intval($settings['limitEntries']) > 0 ? intval($settings['limitEntries']) : 50;
		$excludes				= !empty($settings['excludeFormField']) ? $settings['excludeFormField'] : [];
		$advanceTable			= $settings['advanceTable'] ?? false;
		$filterByDate			= !$advanceTable && isset($settings['filterByDate']) ? $settings['filterByDate'] : false;
		$pagination 			= !$advanceTable && isset($settings['pagination']) ? $settings['pagination'] : false;
		$row_per_page 			= !$advanceTable && $pagination && isset($settings['rowPerPage']) ? (int)$settings['rowPerPage'] : 10;
		$noRecordsMessage		= !empty($settings['noRecordsMessage']) ? sanitize_text_field($settings['noRecordsMessage']) : 'No records found';
		
		if ( !is_user_logged_in() ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'Please login to view form submission.', 'brickscodes' ) ] );
		}
		
		if ( !$enableSaveSubmission ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'Save Form Submission is disable.', 'brickscodes' ) ] );
		}
		
		if ( !$formId ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'No form selected.', 'brickscodes' ) ] );
		}
		
		global $wpdb;
		$table_name =\Bricks\Integrations\Form\Submission_Database::get_table_name();
		$current_user = wp_get_current_user();
		$is_granted_roles = array_intersect($granted_roles, ( array )$current_user->roles);
		
		if ( $pagination ) {
			$current_page = isset($_GET['table_content']) ? max(1, (int)$_GET['table_content']) : 1;
			$offset = ($current_page - 1) * $row_per_page;
			// Adjusted limit should only check `min` when `$entries` limit is set
			$adjusted_limit = ($entries > 0) ? min($row_per_page, max(0, $entries - $offset)) : $row_per_page;
		}

		$date_filter = "";
		if ($filterByDate) {
			// Get the timezone configured in WordPress
			$wp_timezone = wp_timezone();
			
			// Convert start and end date to WordPress timezone
			$start_date = isset($settings['startDate']) ? wp_date('Y-m-d', strtotime($settings['startDate']), $wp_timezone) : null;
			$end_date = isset($settings['endDate']) ? wp_date('Y-m-d', strtotime($settings['endDate']), $wp_timezone) : null;

			// Build date filter query
			if ($start_date && $end_date) {
				$date_filter = $wpdb->prepare("AND DATE(created_at) BETWEEN %s AND %s", $start_date, $end_date);
			} elseif ($start_date) {
				$date_filter = $wpdb->prepare("AND DATE(created_at) >= %s", $start_date);
			} elseif ($end_date) {
				$date_filter = $wpdb->prepare("AND DATE(created_at) <= %s", $end_date);
			}
		}
		
		if ( in_array('administrator', (array)$current_user->roles) || !empty($is_granted_roles) ) {
			if ($pagination) {
				$query = $wpdb->prepare(
					"SELECT * FROM $table_name WHERE form_id = %s $date_filter ORDER BY created_at DESC LIMIT %d OFFSET %d",
					$formId, $adjusted_limit, $offset
				);
				$count_query = $wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name WHERE form_id = %s $date_filter",
					$formId
				);
			} else {
				$query = $wpdb->prepare(
					"SELECT * FROM $table_name WHERE form_id = %s AND user_id = %d $date_filter ORDER BY created_at DESC LIMIT %d",
					$formId, $current_user->id, $entries
				);
			}
		} else {
			if ($pagination) {
				$query = $wpdb->prepare(
					"SELECT * FROM $table_name WHERE form_id = %s AND user_id = %d $date_filter ORDER BY created_at DESC LIMIT %d OFFSET %d",
					$formId, $current_user->id, $adjusted_limit, $offset
				);
				$count_query = $wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name WHERE form_id = %s AND user_id = %d $date_filter",
					$formId, $current_user->id
				);
			} else {
				$query = $wpdb->prepare(
					"SELECT * FROM $table_name WHERE form_id = %s AND user_id = %d $date_filter ORDER BY created_at DESC LIMIT %d",
					$formId, $current_user->id, $entries
				);
			}
		}
		
		$results = $wpdb->get_results($query, ARRAY_A);	
		
		if ($pagination) {
			$total_entries = min((int) $wpdb->get_var($count_query), $entries);
			$total_pages = ceil($total_entries / $row_per_page);
		}
		
		if (empty($results) && $filterByDate) {
			if (in_array('administrator', $current_user->roles) || !empty($is_granted_roles)) {
				// Nearest date query for administrators or users with granted roles
				if ($start_date) {
					$query_nearest = $wpdb->prepare(
						"SELECT * FROM $table_name WHERE form_id = %s AND DATE(created_at) < %s ORDER BY created_at DESC LIMIT 1",
						$formId, $start_date
					);
				} else {
					$query_nearest = $wpdb->prepare(
						"SELECT * FROM $table_name WHERE form_id = %s ORDER BY created_at DESC LIMIT 1",
						$formId
					);
				}
			} else {
				// Nearest date query for other users (restricted to user_id)
				if ($start_date) {
					$query_nearest = $wpdb->prepare(
						"SELECT * FROM $table_name WHERE form_id = %s AND user_id = %d AND DATE(created_at) < %s ORDER BY created_at DESC LIMIT 1",
						$formId, $current_user->id, $start_date
					);
				} else {
					$query_nearest = $wpdb->prepare(
						"SELECT * FROM $table_name WHERE form_id = %s AND user_id = %d ORDER BY created_at DESC LIMIT 1",
						$formId, $current_user->id
					);
				}
			}

			// Execute the nearest date query
			$results = $wpdb->get_results($query_nearest, ARRAY_A);
		}
		
		if (empty($results)) {
			return $this->render_element_placeholder(['title' => esc_html($noRecordsMessage)]);
		}
		
		$newResults = [];
		$form_settings = [];
		$tableTitle = !empty($settings['tableTitle']) ? sanitize_text_field($settings['tableTitle']) : ($formName ? $formName . ' Records' : 'Deleted Form Records(' . $formId . ')' );
		
		if ($advanceTable && isset($settings['advanceTableResponsive']) && !empty($this->datatables_modal_controls)) {
			$desktop_css = [];
			foreach ( $settings as $setting_key => $setting_value ) {				
				$controlkey_css = \Bricks\Assets::generate_css_rules_from_setting( $settings, $setting_key, $setting_value, $this->datatables_modal_controls, 'div.dtr-modal.' . $this->id, 'content' ); // $css_type String global/page/header/content/footer/mobile.
				if ( !empty($controlkey_css) && count($controlkey_css) > 0 ) {
					$desktop_css[] = $controlkey_css;
					$this->datatables_modal_desktop_css .= \Bricks\Assets::generate_inline_css_for_breakpoints( 'content', $controlkey_css );					
				}		
			}
			$this->datatables_modal_css .= \Bricks\Assets::generate_inline_css_for_breakpoints( 'content', $this->datatables_modal_desktop_css );	
			$final_css = \Bricks\Assets::minify_css($this->datatables_modal_css);
			if ( bricks_is_frontend() ) {
				wp_enqueue_style( 'bc-datatables-modal', BRICKSCODES_PLUGIN_URL . 'public/assets/css/bc-datatables-modal.css', [], false, 'all' );
				wp_add_inline_style( 'bc-datatables-modal', $final_css );
			}
		}
		
		$this->set_attribute( '_root', 'role', 'table' );
		$this->set_attribute( '_root', 'class', 'bc-form-table' );
		$this->set_attribute( '_root', 'tabindex', '0' );
		if ( $advanceTable ) {
			$this->set_attribute( '_root', 'aria-labelledby', 'table-' . $formId . '' );
			$this->set_attribute( '_root', 'data-bc-advance-table', '' );	
		} else {
			$this->set_attribute( '_root', 'aria-labelledby', 'table-' . $formId . '' );
			$this->set_attribute( '_root', 'data-bc-table', '' );
		};
		
		if ( $advanceTable && bricks_is_frontend() && !bricks_is_builder_iframe() ) {
			$output = "<div " . $this->render_attributes( '_root' ) . " data-frontend>";
		} else {
			$output = "<div " . $this->render_attributes( '_root' ) . ">";
		}

		foreach( $results as $result ) {
			$form_settings[] = \Bricks\Integrations\Form\Submission_Database::get_form_settings( $post_id = $result['post_id'], $form_id = $result['form_id'], $global_id = 0 );
		}
	
		foreach( $results as $index => $result ) {
			$formData = $result['form_data'];
			$formDataArray = json_decode($formData, true); 			
			$formDataArray['PID'] = $result['id'];
			$formDataArray['User'] = $result['user_id'];
			$formDataArray['Created On'] = $result['created_at'];
			$formDataArray['Referrer'] = $result['referrer'];		
			$newResults[] = $this->bc_map_replace_keys($formDataArray, $form_settings[$index], $excludes);
		}
			
		if ( !empty($newResults) ) {
			$htmlTable = $this->bc_generate_html_table($newResults, $formId, $tableTitle, $settings, $current_user);
		}
		
		if ( !empty($htmlTable) ) {			
			$output .= $htmlTable;	
		}
		
		if ($pagination) {
			$pagination_html = $this->bc_render_pagination($current_page, $total_pages, $row_per_page, $total_entries);
			$output .= $pagination_html;
		}
		
		$output .= "</div>";
		echo $output;
		
	}
	
}
