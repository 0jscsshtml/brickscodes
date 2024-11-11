<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Bc_Model_Viewer extends \Bricks\Element {
	public $category     = 'brickscodes';
	public $name         = 'bc-model-viewer';
	public $icon         = 'ti-world';
	public $scripts      = ['bcModelViewerFn'];
	
	public function get_label() {
		return esc_html__( 'Model Viewer', 'brickscodes' );
	}
	
	public function get_keywords() {
		return [ '3D', 'Model' ];
	}
	
	public function set_control_groups() {
		$this->control_groups['modelOptions'] = [
			'title' 	=> esc_html__( 'Model Options', 'brickscodes' ),
			'required' 	=> ['gltfLink.type', '!=', '' ],
		]; 
		$this->control_groups['loadButton'] = [
			'title' 	=> esc_html__( 'Load Button', 'brickscodes' ),
			'required' 	=> [
				['gltfLink.type', '!=', '' ],
				['gltfLoadingMode', '=', 'manual' ],
			],	
		]; 
		$this->control_groups['modelProgress'] = [
			'title' 	=> esc_html__( 'Progress Bar', 'brickscodes' ),
			'required' 	=> ['gltfLoadingMode', '=', ['manual', 'lazy'] ],
		]; 
	}
	
	public function set_controls() {
		$this->controls['gltfLink'] = [
			'label'       => esc_html__( 'glTF/GLB Url', 'brickscodes' ),
			'type'        => 'link',
			'pasteStyles' => false,
			'exclude'     => [
				'external',
				'rel',
				'newTab',
				'lightboxVideo',
				'lightboxImage',
				'internal',
				'taxonomy'
			],
			'fullAccess'  => true,
		];
		
		$this->controls['gltfAltText'] = [
			'label' 		=> esc_html__( 'glTF/GLB Alt Text', 'brickscodes' ),
			'type' 			=> 'text',
			'inlineEditing' => true,
			'inline' 		=> true,
			'required' 		=> ['gltfLink.type', '!=', '' ],
		];
		
		$this->controls['gltfLoadingMode'] = [
			'label' 	=> esc_html__( 'Loading Mode', 'brickscodes' ),
			'type' 		=> 'select',
			'options' 	=> [
				'auto' 	=> 'Auto',
				'eager' => 'Eager',
				'lazy' 	=> 'Lazy',
				'manual' => 'Manual',
			],
			'inline' 		=> true,
			'placeholder' 	=> esc_html__( 'Auto', 'brickscodes' ),
			'clearable' 	=> false,
			'default' 		=> 'auto',
			'required' 	=> ['gltfLink.type', '!=', '' ],
		];
		
		$this->controls['gltfPosterSep'] = [
			'label'	   	=> esc_html__( 'Manual Loading', 'brickscodes' ),
			'type'     	=> 'separator',
			'required' 	=> [
				['gltfLink.type', '!=', '' ],
				['gltfLoadingMode', '=', 'manual' ],
			]	
		];
		$this->controls['gltfPoster'] = [
			'label'	   	=> esc_html__( 'Cover Image', 'brickscodes' ),
			'type'      => 'image',
			'required' 	=> [
				['gltfLink.type', '!=', '' ],
				['gltfLoadingMode', '=', ['manual', 'lazy']]
			]	
		];
		
		$this->controls['gltfLoadButtonText'] = [
			'label' 		=> esc_html__( 'Load Button Text', 'brickscodes' ),
			'type' 			=> 'text',
			'inlineEditing' => true,
			'inline' 		=> true,
			'default'		=> esc_html__( 'Load Model', 'brickscodes' ),
			'required' 		=> [
				['gltfLink.type', '!=', '' ],
				['gltfLoadingMode', '=', 'manual']
			]
		];

		$this->controls['modelInfo'] = [
			'group' => 'modelOptions',
			'description' => sprintf(
				'%s <a href="https://modelviewer.dev/" target="_blank" rel="noopener">%s</a>',
				esc_html__( 'Check Model Viewer Documentation', 'brickscodes' ), esc_html__( 'here', 'brickscodes' )
			),
			'type'     	=> 'info',
			'required' 	=> ['gltfLink.type', '!=', '' ],
		];
		
		$this->controls['modelInteraction'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Camera Control', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
			'default' => true,
			'fullAccess'  => true,
		];
		
		$this->controls['modelInteractionPrompt'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Interaction Prompt', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
			'required' 	=> ['modelInteraction', '=', true],	
		];
		
		$this->controls['modelInteractionPromptThreshold'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Interaction Prompt Threshold (ms)', 'brickscodes' ),
			'type' 	=> 'number',
			'min'	=> 0.2,
			'unitless' 	=> true,
			'inline'=> false,
			'small' => true,
			'placeholder' => esc_html__( '3000', 'brickscodes' ),
			'required' 	=> [
				['modelInteraction', '=', true],
				['modelInteractionPrompt', '=', true]
			]	
		];
		
		$this->controls['modelDisablePan'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Disable Pan', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
		];
		
		$this->controls['modelPanSensitivity'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Pan Sensitivity', 'brickscodes' ),
			'type' 	=> 'number',
			'min'	=> 0.2,
			'step'	=> 0.1,
			'unitless' 	=> true,
			'inline'=> true,
			'small' => true,
			'placeholder' => esc_html__( '1', 'brickscodes' ),
			'required' 	=> [
				['modelInteraction', '=', true],
				['modelDisablePan', '=', ''],
			]	
		];
		
		$this->controls['modelRotate'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Auto Rotate', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
		];
		
		$this->controls['modelRotateSpeed'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Rotate Speed (deg/rad)', 'brickscodes' ),
			'type' 	=> 'text',
			'inline'=> true,
			'small' => true,
			'dd'	=> false,
			'placeholder' => esc_html__( '30deg or 0.5rad', 'brickscodes' ),
			'required' 	=> ['modelRotate', '=', true],	
		];
		
		$this->controls['modelRotateDelay'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Auto Rotate Delay (ms)', 'brickscodes' ),
			'type' 	=> 'number',
			'min'	=> 1,
			'unitless' => true,
			'inline'=> true,
			'small' => true,
			'placeholder' => esc_html__( '3000', 'brickscodes' ),
			'required' 	=> ['modelRotate', '=', true],		
		];
		
		$this->controls['modelZoomDisable'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Disable User Zoom', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
			'small' => true,
			'required' 	=> ['modelInteraction', '=', true],
		];
		
		$this->controls['modelZoomSensitivity'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Zoom Sensitivity', 'brickscodes' ),
			'type' 	=> 'number',
			'min'	=> 0.2,
			'step'	=> 0.1,
			'unitless' 	=> true,
			'inline'=> true,
			'small' => true,
			'placeholder' => esc_html__( '1', 'brickscodes' ),
			'required' 	=> [
				['modelZoomDisable', '=', ''],
				['modelInteraction', '=', true],
			]	
		];
		
		$this->controls['modelFov'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Field of View (deg/rad)', 'brickscodes' ),
			'type' 	=> 'text',
			'inline'=> true,
			'small' => true,
			'placeholder' => esc_html__( 'auto', 'brickscodes' ),
			'dd' 	=> false,
		];
		
		$this->controls['modelMinFov'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Min Field of View (deg/rad)', 'brickscodes' ),
			'type' 	=> 'text',
			'inline'=> true,
			'small' => true,
			'placeholder' => esc_html__( '25deg', 'brickscodes' ),
			'dd' 	=> false,
		];
		
		$this->controls['modelMaxFov'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Max Field of View (deg/rad)', 'brickscodes' ),
			'type' 	=> 'text',
			'inline'=> true,
			'small' => true,
			'placeholder' => esc_html__( 'auto', 'brickscodes' ),
			'dd' 	=> false,	
		];
		
		$this->controls['modelScrollSync'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Sync Scroll', 'brickscodes' ),
			'type' 	=> 'textarea',
			'dd'	=> false,
			'rows'	=> 5,
			'placeholder' => esc_html__( 'calc(-1.5rad + env(window-scroll-y) * 4rad) calc(0deg + env(window-scroll-y) * 180deg) calc(5m - env(window-scroll-y) * 10m)', 'brickscodes' ),	
		];
		
		$this->controls['modelInitialPost'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Initial Camera Position', 'brickscodes' ),
			'type' 	=> 'text',
			'inline'=> true,
			'dd'	=> false,
			'placeholder' => esc_html__( '0deg 75deg 105%', 'brickscodes' ),
			'required' 	=> ['modelScrollSync', '=', '']
		];
		
		$this->controls['modelShadowIntensity'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Shadow Intensity (0 to 1)', 'brickscodes' ),
			'type' 	=> 'number',
			'min'	=> 0.1,
			'max'	=> 1,
			'unitless' => true,
			'inline'=> true,
			'small' => true,
			'placeholder' => esc_html__( '0', 'brickscodes' ),
		];
		
		$this->controls['modelShadowSoftness'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Shadow Softness (0 to 1)', 'brickscodes' ),
			'type' 	=> 'number',
			'min'	=> 0.1,
			'max'	=> 1,
			'unitless' => true,
			'inline'=> true,	
			'small' => true,
			'placeholder' => esc_html__( '1', 'brickscodes' ),
		];
		
		$this->controls['modelAnimationSelect'] = [
			'group' => 'modelOptions',
			'label' 	=> esc_html__( 'Select Animation (if model support)', 'brickscodes' ),
			'type' 		=> 'select',
			'options' 	=> [],
			'inline' 	=> false,
			'placeholder' 	=> esc_html__( 'Select', 'brickscodes' ),
		];
		
		$this->controls['modelAnimationInfo'] = [
			'group' => 'modelOptions',
			'description' => sprintf('%s', esc_html__( 'Available only if model supported. To update the available animation name list, set loading mode to Eager first, animation name list will be updated if supported. Once updated, you can set any loading mode you want.', 'brickscodes' ),
			),
			'type'     	=> 'info',
		];
		
		$this->controls['modelAnimationAutoplay'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'Autoplay', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
			'required' 	=> [
				['modelAnimationSelect', '!=', ''],
				['modelAnimationVisiblePlay', '!=', true]
			]
		];
		
		$this->controls['modelAnimationVisiblePlay'] = [
			'group' => 'modelOptions',
			'label' => esc_html__( 'In View Play/Pause', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
			'required' 	=> [
				['modelAnimationSelect', '!=', ''],
				['modelAnimationAutoplay', '!=', true]
			]
		];
		
		$this->controls['loadButtonSpacing'] = [
			'group'    	=> 'loadButton',
			'label' 	=> esc_html__( 'Padding', 'brickscodes' ),
			'type' 		=> 'spacing',
			'css'		=> [
				[
					'property' => 'padding',
					'selector' => '.load-model',
				],
			],
		];
		
		$this->controls['loadButtonTypography'] = [
			'group' => 'loadButton',
			'label' => esc_html__( 'Typography', 'brickscodes' ),
			'type' 	=> 'typography',
			'css' 	=> [
				[
					'property' => 'typography',
					'selector' => '.load-model',
				],
			],
			'inline' => true,
		];
		
		$this->controls['loadButtonColor'] = [
			'group' 	=> 'loadButton',
			 'label' 	=> esc_html__( 'Background', 'brickscodes' ),
			 'type' 	=> 'color',
			 'inline' 	=> true,
			 'css' 		=> [
				[
				  'property' => 'background-color',
				  'selector' => '.load-model',
				]
			 ],
		];
		
		$this->controls['loadButtonBorder'] = [
			'group' => 'loadButton',
			'label' => esc_html__( 'Border', 'brickscodes' ),
			'type' 	=> 'border',
			'css' 	=> [
				[
					'property' => 'border',
					'selector' => '.load-model',
				],
			],
			'inline' 	=> true,
			'small' 	=> true,
		];
		
		$this->controls['loadButtonPositionSep'] = [
			'group' => 'loadButton',
			'label'	   	=> esc_html__( 'Position', 'brickscodes' ),
			'type'     	=> 'separator',
		];
		
		$this->controls['loadButtonCenter'] = [
			'group' => 'loadButton',
			'label' => esc_html__( 'Center', 'brickscodes' ),
			'type' 	=> 'checkbox',
			'inline'=> true,
			'small' => true,
			'default' => true,
			'css' 	=> [
				[
					'property' 	=> 'transform',
					'selector' 	=> '.load-model',
					'value'		=> 'translate3d(-50%, -50%, 0)',
				],
				[
					'property' 	=> 'left',
					'selector' 	=> '.load-model',
					'value'		=> '50%',
				],
				[
					'property' 	=> 'top',
					'selector' 	=> '.load-model',
					'value'		=> '50%',
				],
			],
			'default'	=> true,
		];
		
		$this->controls['loadButtonTop'] = [
			'group' => 'loadButton',
			'label' => esc_html__( 'Top', 'brickscodes' ),
			'type' 	=> 'number',
			'units' => true,
			'inline' => true,
			'css' 	=> [
				[
					'property' 	=> 'top',
					'selector' => '.load-model',
				],
			],	
			'required' 	=> ['loadButtonCenter', '=', ''],
		];
		
		$this->controls['loadButtonRight'] = [
			'group' => 'loadButton',
			'label' => esc_html__( 'Right', 'brickscodes' ),
			'type' 	=> 'number',
			'units' => true,
			'inline' => true,
			'css' 	=> [
				[
					'property' 	=> 'right',
					'selector' => '.load-model',
				],
			],	
			'required' 	=> ['loadButtonCenter', '=', ''],
		];
		
		$this->controls['loadButtonBottom'] = [
			'group' => 'loadButton',
			'label' => esc_html__( 'Bottom', 'brickscodes' ),
			'type' 	=> 'number',
			'units' => true,
			'inline' => true,
			'css' 	=> [
				[
					'property' 	=> 'bottom',
					'selector' 	=> '.load-model',
				],
			],	
			'required' 	=> ['loadButtonCenter', '=', ''],
		];
		
		$this->controls['loadButtonLeft'] = [
			'group' => 'loadButton',
			'label' => esc_html__( 'Left', 'brickscodes' ),
			'type' 	=> 'number',
			'units' => true,
			'inline' => true,
			'css' 	=> [
				[
					'property' 	=> 'left',
					'selector' => '.load-model',
				],
			],	
			'required' 	=> ['loadButtonCenter', '=', ''],
		];
		
		$this->controls['modelProgressBackground'] = [
			'group' 	=> 'modelProgress',
			'label' 	=> esc_html__( 'Background Color', 'brickscodes' ),
			'type' 	=> 'color',
			'inline' 	=> true,
			'css' 		=> [
				[
				  'property' => 'background-color',
				  'selector' => '.progress-bar-wrapper > .progress-bar',
				]
			],
		];
		
		$this->controls['modelProgressHeight'] = [
			'group' => 'modelProgress',
			'label' => esc_html__( 'Height', 'brickscodes' ),
			'type' 	=> 'number',
			'unit'  => 'px',
			'inline' => true,
			'css' 	=> [
				[
					'property' 	=> 'height',
					'selector' => '.progress-bar-wrapper > .progress-bar',
				],
			],	
		];
		
	}
	
	public function enqueue_scripts() {
		wp_enqueue_script( 'bc-model-viewer', BRICKSCODES_PLUGIN_URL . 'public/assets/js/lib/model-viewer.min.js', [], '', true );
		wp_enqueue_script( 'bc-model-viewer-app', BRICKSCODES_PLUGIN_URL . 'public/assets/js/bc-model-viewer.js', ['bc-model-viewer'], '', true );
		if ( bricks_is_builder_iframe() ) {
			wp_enqueue_style( 'bc-model-viewers', BRICKSCODES_PLUGIN_URL . 'public/assets/css/bc-model-viewer.css');
		}
	}
	
	public function render() {	
		$settings 		= $this->settings;
		$loadingMode	= isset($settings['gltfLoadingMode']) && $settings['gltfLoadingMode'] === 'manual' ? 'manual' : $settings['gltfLoadingMode'];
		$modelAlt		= !empty($settings['gltfAltText']) ? $settings['gltfAltText'] : '';
		$loadButtonText = $loadingMode === 'manual' && !empty($settings['gltfLoadButtonText']) ? $settings['gltfLoadButtonText'] : '';
		$modelSyncSroll = !empty($settings['modelScrollSync']) ? true : false; 
		$modelFov		= !empty($settings['modelFov']) ? $settings['modelFov'] : 'auto';
		$modelMinFov	= !empty($settings['modelMinFov']) ? $settings['modelMinFov'] : '25deg';
		$modelMaxFov	= !empty($settings['modelMaxFov']) ? $settings['modelMaxFov'] : 'auto';
		$glTfModel		= isset($settings['gltfLink']) ? $settings['gltfLink'] : false;
		
		if ( !$glTfModel ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'No model found.', 'brickscodes' )
				]
			);
		}
		
		if ( isset($glTfModel['type']) && $glTfModel['type'] === 'media' ) {
			$model_url		= !empty($glTfModel['mediaData']['url']) ? $glTfModel['mediaData']['url'] : '';
		} else if ( isset($glTfModel['type']) && $glTfModel['type'] === 'meta' && !empty($glTfModel['useDynamicData']) ) {
			$glTfModels = $this->render_dynamic_data_tag( $glTfModel['useDynamicData'], 'image', '' );
			if ( ! empty( $glTfModels[0] ) ) {
				if ( is_numeric( $glTfModels[0] ) ) {
					$model_id = $glTfModels[0];
				} else {
					$model_url = $glTfModels[0];
				}
			}

			if ( empty($model_url) && !empty($model_id) ) {
				$model_url = wp_get_attachment_url( $model_id );
			} else {
				$model_url = $this->render_dynamic_data( $model_url );
			}
		}
		
		$path_info = pathinfo($model_url);
		if (empty($model_url) || (isset($path_info['extension']) && ($path_info['extension'] !== 'gltf' && $path_info['extension'] !== 'glb'))) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'No model found/Invalid model format.', 'brickscodes' )
				]
			);
		}
		
		if ( $loadingMode === 'manual' || $loadingMode === 'lazy' ) {
			$poster			= isset($settings['gltfPoster']) ? $settings['gltfPoster'] : '';
			$poster_size	= isset($poster['size']) ? $poster['size'] : BRICKS_DEFAULT_IMAGE_SIZE;
			if ( empty($poster['useDynamicData']) ) {
				if (isset($poster['id'])) {
					$poster_id = $poster['id'];
					$poster_url = wp_get_attachment_image_url( $poster_id, $poster_size );
					$poster_sizes = wp_get_attachment_image_sizes($poster_id, $poster_size);
					$poster_srcset = wp_get_attachment_image_srcset($poster_id, $poster_size);
				} else {
					$poster_url = isset($poster['url']) ? $poster['url'] : '';
				}
			} else {
				$posters = $this->render_dynamic_data_tag( $poster['useDynamicData'], 'image', [ 'size' => $poster_size ] );			
				if ( ! empty( $posters[0] ) ) {
					if ( is_numeric( $posters[0] ) ) {
						$poster_id = $posters[0];
					} else {
						$poster_url = $posters[0];								
					}
				}

				if ( empty($poster_url) ) {
					$poster_url = !empty($poster_id) ? wp_get_attachment_image_url( $poster_id, $poster_size ) : '';
					$poster_sizes = !empty($poster_id) ? wp_get_attachment_image_sizes($poster_id, $poster_size) : '';
					$poster_srcset = !empty($poster_id) ? wp_get_attachment_image_srcset($poster_id, $poster_size) : '';
				}  else {
					$poster_url = $this->render_dynamic_data( $poster_url );			
				}
			}
		}
		
		
		if (isset($settings['modelAnimationSelect'])) {
			$this->set_attribute( '_root', 'animation-name', esc_attr($settings['modelAnimationSelect']) );
		}
		if ( isset($settings['modelAnimationSelect']) && isset($settings['modelAnimationAutoplay']) ) { 
			$this->set_attribute( '_root', 'autoplay', '');
		}
		
		if ( $loadingMode === 'manual' ) {
			$this->set_attribute( '_root', 'reveal', 'manual' );
		} else {
			$this->set_attribute( '_root', 'loading', $loadingMode );
		}
		
		$this->set_attribute( '_root', 'alt', $modelAlt );
		
		if ( isset($settings['modelInteraction']) ) {
			$this->set_attribute( '_root', 'camera-controls', '' );
			$this->set_attribute( '_root', 'touch-action', 'pan-y');
			if ( isset($settings['modelInteractionPrompt']) ) {
				$this->set_attribute( '_root', 'interaction-prompt', 'auto');
			}
			if ( isset($settings['modelInteractionPrompt']) && isset($settings['modelInteractionPromptThreshold']) ) {
				$this->set_attribute( '_root', 'interaction-prompt-threshold', intval($settings['modelInteractionPromptThreshold']));
			}
			if ( isset($settings['modelDisablePan']) ) {
				$this->set_attribute( '_root', 'disable-pan', '');
			}
			if ( !isset($settings['modelDisablePan']) && isset($settings['modelPanSensitivity']) ) {
				$this->set_attribute( '_root', 'pan-sensitivity', floatVal($settings['modelPanSensitivity']) );
			}
			if ( isset($settings['modelZoomDisable']) ) {
				$this->set_attribute( '_root', 'disable-zoom', '' );
			}
			if ( !isset($settings['modelZoomDisable']) && isset($settings['modelZoomSensitivity']) ) {
				$this->set_attribute( '_root', 'zoom-sensitivity', floatVal($settings['modelZoomSensitivity']) );
			}
		}
		
		if ( isset($settings['modelRotate']) ) {
			$this->set_attribute( '_root', 'auto-rotate', '' );
		}
		if ( isset($settings['modelRotate']) && isset($settings['modelRotateSpeed']) ) {
			$this->set_attribute( '_root', 'rotation-per-second', esc_attr($settings['modelRotateSpeed']) );
		}
		if ( isset($settings['modelRotate']) && isset($settings['modelRotateDelay']) ) {
			$this->set_attribute( '_root', 'auto-rotate-delay', intVal($settings['modelRotateDelay']) );
		}
		
		if ( $modelFov ) {
			$this->set_attribute( '_root', 'field-of-view', esc_attr($modelFov) );
		}
		if ( $modelMinFov ) {
			$this->set_attribute( '_root', 'min-field-of-view', esc_attr($modelMinFov) );
		}
		if ( $modelMaxFov ) {
			$this->set_attribute( '_root', 'max-field-of-view', esc_attr($modelMaxFov) );
		}
		
		if ( isset($settings['modelShadowIntensity']) ) {
			$this->set_attribute( '_root', 'shadow-intensity', floatVal($settings['modelShadowIntensity']) );
		}
		if (  isset($settings['modelShadowIntensity']) && isset($settings['modelShadowSoftness']) ) {
			$this->set_attribute( '_root', 'shadow-softness', floatVal($settings['modelShadowSoftness']) );
		}
		
		if ($modelSyncSroll) {
			$this->set_attribute( '_root', 'camera-orbit', esc_attr($settings['modelScrollSync']) );
		}
		if ( !$modelSyncSroll && !empty($settings['modelInitialPost']) ) {
			$this->set_attribute( '_root', 'camera-orbit', esc_attr($settings['modelInitialPost']) );
		}
		
		$this->set_attribute( '_root', 'src', $model_url );
		if ( $loadingMode === 'manual' && $poster_url ) {
			$this->set_attribute( 'img', 'src', $poster_url );
		}
		if ( $loadingMode === 'manual' && !empty($poster_srcset) ) {
			$this->set_attribute( 'img', 'srcset', $poster_srcset );
		}
		if ( $loadingMode === 'manual' && !empty($poster_sizes) ) {
			$this->set_attribute( 'img', 'sizes', $poster_sizes );
		}
		
		$output = "<model-viewer " . $this->render_attributes( '_root' ) . ">";
		if ( ($loadingMode === 'manual' || $loadingMode === 'lazy') && $poster_url ) {
			$output .= "<img class='lazy-load-poster' slot='poster' " . $this->render_attributes( 'img' ) . ">";
		}		
		if ( $loadingMode === 'manual' ) {
			$output .= "<div class='load-model' slot='poster'>" . $loadButtonText . "</div>";
		}
		$output .= "<div class='progress-bar-wrapper' aria-hidden='true' slot='progress-bar'><div class='progress-bar'></div></div>";

		$output .= "</model-viewer>";
		echo $output;
		
	}
}