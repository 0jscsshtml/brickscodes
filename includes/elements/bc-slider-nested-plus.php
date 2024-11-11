<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Bc_Slider_Nested_Plus {
	
	public function __construct() {
		add_filter( "bricks/elements/slider-nested/control_groups", [$this, 'bc_slider_control_groups'], 10, 1 );
		add_filter( "bricks/elements/slider-nested/controls", [$this, 'bc_slider_controls'], 10, 1 );
		add_filter( 'bricks/element/render_attributes', [$this, 'bc_add_element_attributes'], 10, 3);
		add_filter( 'bricks/element/settings', [ $this, 'bc_edit_element_settings' ], 10, 2);
		add_filter( 'bricks/frontend/render_data', [$this, 'bc_add_elements_before_render'], 10, 3);
	}

	public function bc_slider_control_groups($control_groups) {
		$control_groups['autoplayToggleGrp'] = [
			'title'		=> esc_html__('Play/Pause', 'brickscodes'),
			'required' 	=> [ 
				['autoplay', '=', true ],
				[ 'optionsType', '!=', 'custom' ],
				[ 'playToggleButtons', '=', true ],
			],	
		];

		$control_groups['progressBarGrp'] = [
			'title'		=> esc_html__('Progress Bar', 'brickscodes'),
			'required' 	=> [ 'optionsType', '!=', 'custom' ],
		];

		$control_groups['syncSliderGroup'] = [
			'title'		=> esc_html__('Sync Slider', 'brickscodes'),
			'required'	=> [ 'optionsType', '!=', 'custom' ],
		];

		$control_groups['urlHashNavGrp'] = [
			'title'		=> esc_html__('Url Hash Navigation', 'brickscodes'),
			'required' 	=> [ 'optionsType', '!=', 'custom' ],
		];

		$control_groups['intersectGrp'] = [
			'title'		=> esc_html__('Intersection', 'brickscodes'),
			'required' 	=> [ 'optionsType', '!=', 'custom' ],	
		];

		$control_groups['autoScrollGrp'] = [
			'title'		=> esc_html__('Auto Scroll', 'brickscodes'),
			'required' 	=> [ 'optionsType', '!=', 'custom' ],	
		];
		return $control_groups;
	}

	public function bc_slider_controls( $controls ) {
		$startIndex = array_search( 'start', array_keys( $controls ) );
		if ($startIndex !== false ) {
			$controls = array_slice( $controls, 0, $startIndex + 1, true ) +
			   array( 
					'fixedWidth' => [
						'group' => 'options',
						'label'	=> esc_html__( 'fixed Width (slide)', 'brickscodes' ),
						'type'	=> 'number',
						'units'	=> true,
						'inline'=> true,
						'breakpoints' => true,
						'css' => [
							[
								'property' => 'width',
								'selector' => '.splide__list > .splide__slide',
							],
						],
						'required' => [ 'optionsType', '!=', 'custom' ]	
					]
				) +
			array_slice( $controls, $startIndex + 1, NULL, true );
		}

		$controls['perPage']['required'][] = ['fixedWidth', '=', ''];

		$autoplayIndex = array_search( 'autoplay', array_keys( $controls ) );
		if ($autoplayIndex !== false ) {
			$controls = array_slice( $controls, 0, $autoplayIndex + 1, true ) +
			   array( 
				'playToggleButtons' => [
					'group'    => 'options',
					'label'    => esc_html__( 'Play/Pause Toggle Button', 'brickscodes' ),
					'type'     => 'checkbox',
					'fullAccess'  => true,
					'required' => [ 'optionsType', '!=', 'custom' ]	
				],
				'resetProgress' => [
					'group'    => 'options',
					'label'    => esc_html__( 'Continue Autoplay Progress', 'brickscodes' ),
					'type'     => 'checkbox',
					'fullAccess'  => true,
					'required' => [ 
						['autoplay', '=', true ],
						[ 'optionsType', '!=', 'custom' ]	
					]	
				],
			) +
			array_slice( $controls, $autoplayIndex + 1, NULL, true );
		}

		$paginationIndex = array_search( 'pagination', array_keys( $controls ) );
		if ($paginationIndex !== false ) {
			$controls = array_slice( $controls, 0, $paginationIndex + 1, true ) +
			   array( 
				'numberPagination' => [
					'group'    => 'pagination',
					'label'    => esc_html__( 'Number Pagination', 'brickscodes' ),
					'type'     => 'checkbox',
					'fullAccess'  => true,
					'required' => ['pagination', '=', true ],	
				],
				'fractionPagination' => [
					'group'    => 'pagination',
					'label'    => esc_html__( 'Fraction Pagination', 'brickscodes' ),
					'type'     => 'checkbox',
					'fullAccess'  => true,
					'required' => [ 
						['pagination', '=', true ],
						[ 'optionsType', '!=', 'custom' ]	
					]
				],
			) +
			array_slice( $controls,$paginationIndex + 1, NULL, true );
		}

		$paginationBorderIndex = array_search( 'paginationBorder', array_keys( $controls ) );
		if ($paginationBorderIndex !== false ) {
			$controls = array_slice( $controls, 0, $paginationBorderIndex + 1, true ) +
			   array( 
				'numberPaginationTypo' => [
					'group'    => 'pagination',
					'label'    => esc_html__( 'Number Typography', 'brickscodes' ),
					'type'     => 'typography',
					'css'      => [
						[
							'property' => 'font',
							'selector' => '&[data-number-pagination] .splide__pagination__page:before',
						],
					],
					'required' => [ 
						['pagination', '=', true ],
						['numberPagination', '=', true ]
					],
				],
			) +
			array_slice( $controls, $paginationBorderIndex + 1, NULL, true );
		}

		$controls['fractionPaginationSep'] = [
			'label'	   	=> esc_html__( 'Fraction Pagination', 'brickscodes' ),
			'group'    	=> 'pagination',
			'type'     	=> 'separator',
			'required'	=> [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],
		];

		$controls['fractionPaginationPadding'] = [
			'label'		=> esc_html__( 'Padding', 'brickscodes' ),
			'group'		=> 'pagination',
			'type'		=> 'spacing',
			'units'		=> true,
			'css'		=> [
				[
					'property' => 'padding',
					'selector' => '.splide__pagination__fraction',
				],
			],
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],
		];

		$controls['fractionPaginationTypo'] = [
			'group'		=> 'pagination',
			'label'		=> esc_html__( 'Typography', 'brickscodes' ),
			'type'		=> 'typography',
			'css'		=> [
				[
					'property' => 'font',
					'selector' => '.splide__pagination__fraction',
				],
			],
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],
		];

		$controls['fractionPaginationBackground'] = [
			'label'	=> esc_html__( 'Background Color', 'brickscodes' ),
			'group' => 'pagination',
			'type' 	=> 'color',
			'inline'=> true,
			'css' => [
				[
					'property' => 'background-color',
					'selector' => '.splide__pagination__fraction',
				],
			],
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],
		];

		$controls['fractionPaginationBorder'] = [
			'group' => 'pagination',
			'label' => esc_html__( 'Border', 'brickscodes' ),
			'type' => 'border',
			'css' => [
				[
					 'property' => 'border',
					 'selector' => '.splide__pagination__fraction',
				],
			  ],
			'inline' => true,
			'small' => true,
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],
		];

		$controls['fractionPaginationPosSep'] = [
			'label'		=> esc_html__( 'Position', 'brickscodes' ),
			'group'		=> 'pagination',
			'type'		=> 'separator',
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],
		];

		$controls['fractionPaginationTop'] = [
			'group' => 'pagination',
			'label'	=> esc_html__( 'Top', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'top',
					'selector' => '.splide__pagination__fraction',
				],
			],
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],		
		];

		$controls['fractionPaginationRight'] = [
			'group' => 'pagination',
			'label'	=> esc_html__( 'Right', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'right',
					'selector' => '.splide__pagination__fraction',
				],
			],
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],		
		];

		$controls['fractionPaginationBottom'] = [
			'group' => 'pagination',
			'label'	=> esc_html__( 'Bottom', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'bottom',
					'selector' => '.splide__pagination__fraction',
				],
			],
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],		
		];

		$controls['fractionPaginationLeft'] = [
			'group' => 'pagination',
			'label'	=> esc_html__( 'Left', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'left',
					'selector' => '.splide__pagination__fraction',
				],
			],
			'required' => [ 
				['pagination', '=', true ],
				['fractionPagination', '=', true ],
				[ 'optionsType', '!=', 'custom' ]
			],	
		];

		$controls['syncInfo'] = [
			'group'		=> 'syncSliderGroup',
			'content'	=> esc_html__( 'Note: Load on frontend only.', 'brickscodes' ),
			'type'		=> 'info',
			'required'	=> ['enableSync', '=', true ],	
		];

		$controls['syncInfo2'] = [
			'group'    => 'syncSliderGroup',
			'content' => esc_html__( 'Set this on Main Slider', 'brickscodes' ),
			'type' => 'info',
			'required'	=> ['enableSync', '=', true ],	
		];

		$controls['enableSync'] = [
			'group'		=> 'syncSliderGroup',
			'label'		=> esc_html__( 'Enable Sync Slider', 'brickscodes' ),
			'type'		=> 'checkbox',
			'inline'	=> true,
			'fullAccess'  => true,
		];

		$controls['mainSliderId'] = [
			'group'    		=> 'syncSliderGroup',
			'label' 		=> esc_html__( 'Main Slider Id', 'brickscodes' ),
			'type' 			=> 'text',
			'inlineEditing' => false,
			'inline'		=> true,
			'dd'			=> false,
			'required'		=> ['enableSync', '=', true ],
		]; 

		$controls['thumbSliderId'] = [
			'group'    		=> 'syncSliderGroup',
			'label' 		=> esc_html__( 'Thumb Slider Id', 'brickscodes' ),
			'type' 			=> 'text',
			'inlineEditing' => false,
			'inline'		=> true,
			'dd'			=> false,
			'required'		=> ['enableSync', '=', true ],
		];

		$controls['thumbIsNavigationInfo'] = [
			'group'		=> 'syncSliderGroup',
			'content'	=> esc_html__( 'Re-toggle checkbox if you made changes on Thumb Slider Id', 'brickscodes' ),
			'type'		=> 'info',
			'required'	=> ['enableSync', '=', true ],	
		];
		
		$controls['thumbIsNavigation'] = [
			'group'		=> 'syncSliderGroup',
			'label'		=> esc_html__( 'Enable Thumb Slider "isNavigation"', 'brickscodes' ),
			'type'		=> 'checkbox',
			'inline'	=> true,
			'required' 	=> [
				['enableSync', '=', true ],
				['thumbSliderId', '!=', '' ],
			],	
			'fullAccess'  => true,
		];

		$controls['isNavigation'] = [
			'group'		=> 'syncSliderGroup',
			'type'		=> 'checkbox',
			'inline'	=> true,
			'hidden'	=> true,	
			'fullAccess'  => true,	
		];
		
		$controls['progressBarInfo'] = [
			'group'		=> 'progressBarGrp',
			'type'		=> 'info',
			'description' => esc_html__( 'Toggle "Progress Bar" group to rerender if Element is missing (due to is not part of the element).', 'brickscodes' ),
		];	

		$controls['enableProgress'] = [
			'group'		=> 'progressBarGrp',
			'label'		=> esc_html__( 'Enable Slider Progress', 'brickscodes' ),
			'type'		=> 'checkbox',
			'fullAccess'  => true,
		];

		$controls['transitionProgressInfo'] = [
			'group'		=> 'progressBarGrp',
			'content'	=> esc_html__( 'This has no effect on Auto Scroll', 'brickscodes' ),
			'type'		=> 'info',
			'required'	=> ['transitionProgress', '=', true],
		];

		$controls['transitionProgress'] = [
			'group'    => 'progressBarGrp',
			'label'    => esc_html__( 'Enable Transition Progress', 'brickscodes' ),
			'type'     => 'checkbox',
			'fullAccess'  => true,
			'required' => ['autoplay', '=', true ],
		];

		$controls['slideProgressSep'] = [
			'label'	   	=> esc_html__( 'Slide Progress', 'brickscodes' ),
			'group'    	=> 'progressBarGrp',
			'type'     	=> 'separator',
			'required' 	=> ['enableProgress', '=', true ],
		];

		$controls['slideProgressDir'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Direction', 'brickscodes' ),
			'type'	=> 'select',
			'options' => [
				'ltr' => esc_html__( 'Left to Right', 'brickscodes' ),
				'rtl' => esc_html__( 'Right to Left', 'brickscodes' ),
			],
			'inline'=> true,
			'required' 	=> [
				['enableProgress', '=', true ],
				['direction', '!=', 'ttb' ],
			]	
		];	

		$controls['slideProgressVerticalDir'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Direction', 'brickscodes' ),
			'type'	=> 'select',
			'options' => [
				'ttb' => esc_html__( 'Top to Bottom', 'brickscodes' ),
				'btt' => esc_html__( 'Bottom to Top', 'brickscodes' ),
			],
			'inline'=> true,
			'required' 	=> [
				['enableProgress', '=', true ],
				['direction', '=', 'ttb' ],
			]	
		];

		$controls['slideProgressWidth'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Width', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'width',
					'selector' => '&:not(.splide--ttb) .splide__slide__progress',
				],
			],
			'required' 	=> [
				['enableProgress', '=', true ],
				['direction', '!=', 'ttb' ],
			]	
		];

		$controls['slideProgressVertWidth'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Width', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'width',
					'selector' => '&.splide--ttb .splide__slide__progress__bar',
				],
			],
			'required' 	=> [
				['enableProgress', '=', true ],
				['direction', '=', 'ttb' ],
			]	
		];

		$controls['slideProgressHeight'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Height', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'height',
					'selector' => '&:not(.splide--ttb) .splide__slide__progress__bar',
				],
			],
			'required' 	=> [
				['enableProgress', '=', true ],
				['direction', '!=', 'ttb' ],
			]			
		];

		$controls['slideProgressVertHeight'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Height', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'height',
					'selector' => '&.splide--ttb .splide__slide__progress',
				],
			],
			'required' 	=> [
				['enableProgress', '=', true ],
				['direction', '=', 'ttb' ],
			]			
		];

		$controls['slideProgressTrackColor'] = [
			'label'	=> esc_html__( 'Track Background', 'brickscodes' ),
			'group'	=> 'progressBarGrp',
			'type' 	=> 'color',
			'inline'=> true,
			'css' => [
				[
					'property' => 'background-color',
					'selector' => '.splide__slide__progress',
				],
			],
			'required' 	=> ['enableProgress', '=', true ],	
		];

		$controls['slideProgressBarColor'] = [
			'label'	=> esc_html__( 'Bar Background', 'brickscodes' ),
			'group'	=> 'progressBarGrp',
			'type' 	=> 'color',
			'inline'=> true,
			'css' => [
				[
					'property' => 'background-color',
					'selector' => '.splide__slide__progress__bar',
				],
			],
			'required' 	=> ['enableProgress', '=', true ],	
		];

		$controls['slideProgressPosSep'] = [
			'label'	   	=> esc_html__( 'Position', 'brickscodes' ),
			'group'    	=> 'progressBarGrp',
			'type'     	=> 'separator',
			'required' 	=> ['enableProgress', '=', true ],	
		];

		$controls['slideProgressTop'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Top', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'top',
					'selector' => '.splide__slide__progress',
				],
			],
			'required' 	=> ['enableProgress', '=', true ],			
		];

		$controls['slideProgressRight'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Right', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'right',
					'selector' => '.splide__slide__progress',
				],
			],
			'required' 	=> ['enableProgress', '=', true ],		
		];

		$controls['slideProgressBottom'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Bottom', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'bottom',
					'selector' => '.splide__slide__progress',
				],
			],
			'required' 	=> ['enableProgress', '=', true ],			
		];

		$controls['slideProgressLeft'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Left', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'left',
					'selector' => '.splide__slide__progress',
				],
			],
			'required' 	=> ['enableProgress', '=', true ],	
		];

		$controls['transitionProgressSep'] = [
			'label'	   	=> esc_html__( 'Transition Progress', 'brickscodes' ),
			'group'    	=> 'progressBarGrp',
			'type'     	=> 'separator',
			'required' 	=> ['transitionProgress', '=', true ],
		];

		$controls['transitionProgressDir'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Direction', 'brickscodes' ),
			'type'	=> 'select',
			'options' => [
				'ltr' => esc_html__( 'Left to Right', 'brickscodes' ),
				'rtl' => esc_html__( 'Right to Left', 'brickscodes' )
			],
			'inline'=> true,
			'required' 	=> [
				['transitionProgress', '=', true ],
				['direction', '!=', 'ttb' ],
			]	
		];

		$controls['transitionProgressVertDir'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Direction', 'brickscodes' ),
			'type'	=> 'select',
			'options' => [
				'ttb' => esc_html__( 'Top to Bottom', 'brickscodes' ),
				'btt' => esc_html__( 'Bottom to Top', 'brickscodes' )
			],
			'inline'=> true,
			'required' 	=> [
				['transitionProgress', '=', true ],
				['direction', '=', 'ttb' ],
			]	
		];	

		$controls['transitionProgressWidth'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Width', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'width',
					'selector' => '&:not(.splide--ttb) .splide__transition__progress',
				],
			],
			'required' 	=> [
				['transitionProgress', '=', true ],	
				['direction', '!=', 'ttb' ],
			]	
		];

		$controls['transitionProgressVertWidth'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Width', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'width',
					'selector' => '&.splide--ttb .splide__transition__progress__bar',
				],
			],
			'required' 	=> [
				['transitionProgress', '=', true ],	
				['direction', '=', 'ttb' ],
			]	
		];

		$controls['transitionProgressHeight'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Height', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'height',
					'selector' => '&:not(.splide--ttb) .splide__transition__progress__bar',
				],
			],
			'required' 	=> [
				['transitionProgress', '=', true ],	
				['direction', '!=', 'ttb' ],
			]		
		];

		$controls['transitionProgressVertHeight'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Height', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'height',
					'selector' => '&.splide--ttb .splide__transition__progress',
				],
			],
			'required' 	=> [
				['transitionProgress', '=', true ],	
				['direction', '=', 'ttb' ],
			]		
		];

		$controls['transitionProgressTrackColor'] = [
			'label'	=> esc_html__( 'Track Background', 'brickscodes' ),
			'group'	=> 'progressBarGrp',
			'type' 	=> 'color',
			'inline'=> true,
			'css' => [
				[
					'property' => 'background-color',
					'selector' => '.splide__transition__progress',
				],
			],
			'required' 	=> ['transitionProgress', '=', true ],		
		];

		$controls['transitionProgressBarColor'] = [
			'label'	=> esc_html__( 'Bar Background', 'brickscodes' ),
			'group'	=> 'progressBarGrp',
			'type' 	=> 'color',
			'inline'=> true,
			'css' => [
				[
					'property' => 'background-color',
					'selector' => '.splide__transition__progress__bar'
				],
			],
			'required' 	=> ['transitionProgress', '=', true ],		
		];

		$controls['transitionProgressPosSep'] = [
			'label'	   	=> esc_html__( 'Position', 'brickscodes' ),
			'group'    	=> 'progressBarGrp',
			'type'     	=> 'separator',
			'required' 	=> ['transitionProgress', '=', true ],		
		];

		$controls['transitionProgressTop'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Top', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'top',
					'selector' => '.splide__transition__progress'
				],
			],
			'required' 	=> ['transitionProgress', '=', true ],			
		];

		$controls['transitionProgressRight'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Right', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'right',
					'selector' => '.splide__transition__progress'
				],
			],
			'required' 	=> ['transitionProgress', '=', true ],			
		];

		$controls['transitionProgressBottom'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Bottom', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'bottom',
					'selector' => '.splide__transition__progress'
				],
			],
			'required' 	=> ['transitionProgress', '=', true ],			
		];

		$controls['transitionProgressLeft'] = [
			'group'	=> 'progressBarGrp',
			'label'	=> esc_html__( 'Left', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'left',
					'selector' => '.splide__transition__progress'
				],
			],
			'required' 	=> ['transitionProgress', '=', true ],	
		];
		
		$controls['PlayPauseInfo'] = [
			'group'		=> 'autoplayToggleGrp',
			'type'		=> 'info',
			'description' => esc_html__( 'Toggle "Play/Pause" group to rerender if Element is missing (due to is not part of the element).', 'brickscodes' ),
		];

		$controls['playPauseSize'] = [
			'group' => 'autoplayToggleGrp',
			'label'	=> esc_html__( 'Size', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'default' => '40px',
			'css' => [
				[
					'property' => 'width',
					'selector' => '.splide__toggle',
				],
				[
					'property' => 'height',
					'selector' => '.splide__toggle',
				],
			],	
		];
		
		$controls['playPauseIconSize'] = [
			'group' => 'autoplayToggleGrp',
			'label'	=> esc_html__( 'Icon Size', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'default' => '20px',
			'css' => [
				[
					'property' => 'width',
					'selector' => '.splide__toggle > svg',
				],
				[
					'property' => 'height',
					'selector' => '.splide__toggle > svg',
				],
			],	
		];
		
		$controls['playPauseColor'] = [
			'label'	=> esc_html__( 'Fill Color', 'brickscodes' ),
			'group' => 'autoplayToggleGrp',
			'type' 	=> 'color',
			'inline'=> true,
			'css' => [
				[
					'property' => 'fill',
					'selector' => '.splide__toggle > svg',
				],
			],
		];

		$controls['playPauseBackground'] = [
			'label'	=> esc_html__( 'Background Color', 'brickscodes' ),
			'group' => 'autoplayToggleGrp',
			'type' 	=> 'color',
			'inline'=> true,
			'css' => [
				[
					'property' => 'background-color',
					'selector' => '.splide__toggle',
				],
			],
		];

		$controls['playPauseBorder'] = [
			'group' => 'autoplayToggleGrp',
			'label' => esc_html__( 'Border', 'brickscodes' ),
			'type' => 'border',
			'css' => [
				[
					 'property' => 'border',
					 'selector' => '.splide__toggle',
				],
			  ],
			'inline' => true,
			'small' => true,
		];

		$controls['playPausePosSep'] = [
			'label'		=> esc_html__( 'Position', 'brickscodes' ),
			'group'		=> 'autoplayToggleGrp',
			'type'		=> 'separator',
		];

		$controls['playPauseTop'] = [
			'group'	=> 'autoplayToggleGrp',
			'label'	=> esc_html__( 'Top', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'top',
					'selector' => '.splide__toggle',
				],
			],	
		];

		$controls['playPauseRight'] = [
			'group'	=> 'autoplayToggleGrp',
			'label'	=> esc_html__( 'Right', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'right',
					'selector' => '.splide__toggle',
				],
			],			
		];

		$controls['playPauseBottom'] = [
			'group'	=> 'autoplayToggleGrp',
			'label'	=> esc_html__( 'Bottom', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'bottom',
					'selector' => '.splide__toggle',
				],
			],		
		];

		$controls['playPauseLeft'] = [
			'group'	=> 'autoplayToggleGrp',
			'label'	=> esc_html__( 'Left', 'brickscodes' ),
			'type'	=> 'number',
			'units'	=> true,
			'inline'=> true,
			'css' => [
				[
					'property' => 'left',
					'selector' => '.splide__toggle',
				],
			],	
		];

		$controls['urlHashValueInfo'] = [
			'group'		=> 'urlHashNavGrp',
			'content' 	=> esc_html__( 'Note: Load on frontend only.', 'brickscodes' ),
			'type' 		=> 'info',
			'required' 	=> [ 
				['enableUrlHashNav', '=', true],
				['urlHashValue', '!=', ''],
			]  
		];

		$controls['urlHashValueInfo2'] = [
			'group'		=> 'urlHashNavGrp',
			'content' 	=> esc_html__( 'Exp: https://your-domain.com/page/#"your-url-hash-prefix"1', 'brickscodes' ),
			'type' 		=> 'info',
			'required' 	=> [ 
				['enableUrlHashNav', '=', true],
				['urlHashValue', '!=', ''],
			]  
		];

		$controls['enableUrlHashNav'] = [
			'group'    => 'urlHashNavGrp',
			'label'    => esc_html__( 'Enable Url Hash Navigation', 'brickscodes' ),
			'type'     => 'checkbox',
			'fullAccess'  => true,
		];

		$controls['urlHashValue'] = [
			'group'		=> 'urlHashNavGrp',
			'label'		=> esc_html__( 'Url Hash Prefix', 'brickscodes' ),
			'type'		=> 'text',
			'inline'	=> true,
			'inlineEditing' => false,
			'required' 	=> ['enableUrlHashNav', '=', true],
		];

		$controls['intersectInfo'] = [
			'group'		=> 'intersectGrp',
			'content' 	=> esc_html__( 'Note: Load on frontend only.', 'brickscodes' ),
			'type' 		=> 'info',
			'required' 	=> ['enableIntersect', '=', true ], 
		];

		$controls['intersectInfo2'] = [
			'group'		=> 'intersectGrp',
			'content' 	=> esc_html__( 'By default, this will control autoplay/autoscroll when in view, else pause autoplay/autoscroll.', 'brickscodes' ),
			'type' 		=> 'info',
			'required' 	=> ['enableIntersect', '=', true ],  
		];

		$controls['enableIntersect'] = [
			'group'    => 'intersectGrp',
			'label'    => esc_html__( 'Enable Intersection', 'brickscodes' ),
			'type'     => 'checkbox',
			'fullAccess'  => true,
		];

		$controls['intersectRootMargin'] = [
			'group'		=> 'intersectGrp',
			'label'		=> esc_html__( 'Root Margin', 'brickscodes' ),
			'type'		=> 'number',
			'units'		=> true,
			'inline'	=> true,
			'required' 	=> ['enableIntersect', '=', true ], 
		]; 

		$controls['autoScrollInfo'] = [
			'group'		=> 'autoScrollGrp',
			'content' 	=> esc_html__( 'Note: Load on frontend only.', 'brickscodes' ),
			'type' 		=> 'info',
			'required' 	=> ['enableAutoScroll', '=', true ], 
		];

		$controls['autoScrollInfo2'] = [
			'group'		=> 'autoScrollGrp',
			'content' 	=> esc_html__( 'Ideal settings for Auto Scroll, "type = loop", "item to show = (>=3)", "focus = center"', 'brickscodes' ),
			'type' 		=> 'info',
			'required' 	=> ['enableAutoScroll', '=', true ], 
		];

		$controls['enableAutoScroll'] = [
			'group'		=> 'autoScrollGrp',
			'label'		=> esc_html__( 'Enable Auto Scroll', 'brickscodes' ),
			'type'		=> 'checkbox',
			'fullAccess'  => true,
		];

		$controls['autoScrollSpeed'] = [
			'group' => 'autoScrollGrp',
			'label'	=> esc_html__( 'Speed (pixel/frame, from 0.5) ', 'brickscodes' ),
			'type'	=> 'number',
			'min'	=> 0.5,
			'step'	=> 0.1,
			'unitless' => true,
			'inline'=> true,
			'required' 	=> ['enableAutoScroll', '=', true ], 	
		];

		return $controls;
	}

	public function bc_add_element_attributes( $attributes, $key, $element ) {
		if ($element->name === 'slider-nested') {
			$settings 						= $element->settings;
			$dataSplide						= json_decode($attributes['_root']["data-splide"][0], true);
			$optionsType					= !isset($settings['optionsType']) || (isset($settings['optionsType']) && $settings['optionsType'] !== 'custom') ? true : false;
			$direction						= $settings['direction'] ?? 'ltr';
			$isAutoplay						= isset($settings['autoplay']);
			$resetProgress					= isset($settings['resetProgress']);
			$pagination  					= isset($settings['pagination']);
			$isProgress   					= isset($settings['enableProgress']);
			$progressDirection				= $settings['slideProgressDir'] ?? 'ltr';
			$vertProgressDirection			= $settings['slideProgressVerticalDir'] ?? 'ttb';
			$isTransitionProgress 			= isset($settings['transitionProgress']);
			$transitionprogressDirection 	= $settings['transitionProgressDir'] ?? 'ltr';
			$vertTransitionprogressDirection = $settings['transitionProgressVertDir'] ?? 'ttb';
			$mainSliderId					= $settings['mainSliderId'] ?? '';
			$thumbSliderId					= $settings['thumbSliderId'] ?? '';
			$urlHashValue					= !empty($settings['urlHashValue']) ? $settings['urlHashValue'] : 'Slide';
			$autoScrollSpeed				= isset($settings['autoScrollSpeed']) ? floatval($settings['autoScrollSpeed']) : 1;
			$rootMargin						= !empty($settings['intersectRootMargin']) ? $settings['intersectRootMargin'] : '0px';
			
			if ( $optionsType ) {
				$splide_options = [
					'fixedWidth' => ! empty( $settings['fixedWidth'] ) ? $settings['fixedWidth'] : '',
				];

				$breakpoints = [];
				foreach ( \Bricks\Breakpoints::$breakpoints as $breakpoint ) {
					foreach ( array_keys( $splide_options ) as $option ) {
						$setting_key      = $breakpoint['key'] === 'desktop' ? $option : "$option:{$breakpoint['key']}";
						$breakpoint_width = $breakpoint['width'] ?? false;
						$setting_value    = $settings[ $setting_key ] ?? false;		
						if ( $option === 'fixedWidth' ) {
							if ( is_numeric( $setting_value ) ) {
								$setting_value = "{$setting_value}px";
							}
						}
						if ( $breakpoint_width && $setting_value !== false ) {
							$breakpoints[ $breakpoint_width ][ $option ] = $setting_value;
						}
					}
				}

				if ( count( $breakpoints ) ) {
					$splide_options['breakpoints'] = $breakpoints;
					$dataSplide['fixedWidth'] = $splide_options['fixedWidth'];
					foreach($splide_options['breakpoints'] as $width => $options) {
						if (!empty($dataSplide['breakpoints'][$width])) {
							$dataSplide['breakpoints'][$width] = Brickscodes_Helpers::bc_merge_recursive_overwrite($dataSplide['breakpoints'][$width], $splide_options['breakpoints'][$width]);
						} else {
							$dataSplide['breakpoints'][$width] = $splide_options['breakpoints'][$width];
						}			
					}
				}

				$attributes["_root"]['data-splide-mode'] = 'default';
				
				if ( $pagination && isset($settings['fractionPagination']) ) {
					$attributes["_root"]['data-fraction-pagination'] = '';
				}
				
				if ( $isProgress ) {
					$attributes["_root"]['data-slider-progress'] = '';
				}
				
				if ( $direction !== 'ttb' && $isProgress && $progressDirection) {
					$attributes["_root"]['data-slider-progress-direction'] = $progressDirection;
				} else if ( $direction === 'ttb' && $isProgress && $vertProgressDirection) {
					$attributes["_root"]['data-slider-progress-direction'] = $vertProgressDirection;
				}
				
				if ( ($isAutoplay || isset($settings['enableAutoScroll'])) && isset($settings['playToggleButtons']) ) {
					$attributes["_root"]['data-play-toggle'] = '';
				}

				if ( $isAutoplay && $isTransitionProgress ) {
					$attributes["_root"]['data-transition-progress'] = '';
				}
				
				if ( $isAutoplay && $direction !== 'ttb' && $isTransitionProgress && $transitionprogressDirection) {
					$attributes["_root"]['data-transition-progress-direction'] = $transitionprogressDirection;
				} else if ( $isAutoplay && $direction === 'ttb' && $isTransitionProgress && $vertTransitionprogressDirection) {
					$attributes["_root"]['data-transition-progress-direction'] = $vertTransitionprogressDirection;
				}

				if (isset($settings['enableSync']) && $mainSliderId && $thumbSliderId) {
					$dataSplide['waitForTransition'] = false;	
					$attributes["_root"]['data-sync-ids'] = $mainSliderId . ':' . $thumbSliderId;
				}

				if (isset($settings['isNavigation'])) {
					$dataSplide['isNavigation'] = true;	
				}

				if ($isAutoplay) {
					$dataSplide['resetProgress'] = $resetProgress;
				}

				if (isset($settings['enableUrlHashNav'])) {
					$attributes["_root"]['data-url-hash'] = '';
					$attributes["_root"]['data-hash-value'] = $urlHashValue;
				}

				if (isset($settings['enableAutoScroll'])) {
					$attributes["_root"]['data-autoscroll'] = '';
					$dataSplide['autoplay'] = 'pause';
					$dataSplide['autoScroll']['speed'] = $autoScrollSpeed;
					$dataSplide['drag'] = 'free';
				}

				if (isset($settings['enableIntersect'])) {
					$attributes["_root"]['data-intersection'] = '';
					$rootMarginValue = is_numeric($rootMargin) ? $rootMargin . 'px' : $rootMargin;
					if ($isAutoplay && !isset($settings['enableAutoScroll'])) {
						$dataSplide['autoplay'] = 'pause';
						$dataSplide['intersection']['rootMargin'] = $rootMarginValue;
						$dataSplide['intersection']['inView']['autoplay'] = true;
						$dataSplide['intersection']['outView']['autoplay'] = false;
					} else if (isset($settings['enableAutoScroll'])) {
						$dataSplide['autoplay'] = 'pause';
						$dataSplide['intersection']['rootMargin'] = $rootMarginValue;
						$dataSplide['intersection']['inView']['autoScroll'] = true;
						$dataSplide['intersection']['outView']['autoScroll'] = false;
					}
				} else {
					if (isset($settings['enableAutoScroll'])) {
						$dataSplide['autoScroll']['autoStart'] = true;
					}
				}
			}

			if ( $pagination && isset($settings['numberPagination']) ) {
				$attributes["_root"]['data-number-pagination'] = '';
			}
			
			$newDataSplides = wp_json_encode($dataSplide);						
			$attributes['_root']['data-splide'] = $newDataSplides;
		}

		return $attributes;
	}
	
	public function bc_edit_element_settings( $settings, $element ) {
		if ( $element->name === 'slider-nested' ) {
			$isAutoScroll	= $settings['enableAutoScroll'] ?? false;
			if ($isAutoScroll) {
				$settings['type'] = 'loop';
				unset($settings['autoplay']);
				unset($settings['transitionProgress']);
			}
		}
		return $settings;
	}
	
	public function bc_add_elements_before_render( $content, $post, $area ) {
		$content = preg_replace_callback(
			'/<div[^>]*(data-slider-progress|data-transition-progress|data-fraction-pagination|data-play-toggle)[^>]*>/s',
			function($matches) {
				$additional_element = '';
				if (strpos($matches[0], 'data-slider-progress') !== false) {
					$additional_element .= '<div class="splide__progress splide__slide__progress"><div class="splide__slide__progress__bar"></div></div>';
				}
				if (strpos($matches[0], 'data-transition-progress') !== false) {
					$additional_element .= '<div class="splide__progress splide__transition__progress"><div class="splide__transition__progress__bar"></div></div>';
				}
				if (strpos($matches[0], 'data-fraction-pagination') !== false) {
					$additional_element .= '<div class="splide__pagination__fraction"><span class="splide__pagination__current"></span> / <span class="splide__pagination__total"></span></div>';
				}
				if (strpos($matches[0], 'data-play-toggle') !== false) {
					$additional_element .= '<button class="splide__toggle" type="button"><svg class="splide__toggle__play" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m22 12-20 11v-22l10 5.5z"/></svg><svg class="splide__toggle__pause" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m2 1v22h7v-22zm13 0v22h7v-22z"/></svg></button>';
				}

				return $matches[0] . $additional_element;
			},
			$content
		);

		return $content;
	}
}