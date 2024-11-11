<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Bc_Copy_Clipboard_Button extends \Bricks\Element {
	public $category     = 'brickscodes';
	public $name         = 'bc-copy-clipboard-button';
	public $icon         = 'ti-clipboard';
	public $scripts      = ['initButtonApp'];
	
	public function get_label() {
		return esc_html__( 'Copy to Clipboard', 'brickscodes' );
	}
	
	public function get_keywords() {
		return [ 'Copy', 'Clipboard' ];
	}
	
	public function set_control_groups() {
		$this->control_groups['copyClipboardButton'] = [
			'title'		=> esc_html__('Button', 'brickscodes'),
			'required'	=> ['copyClipboardSource', '!=', ''],
		];
		
		$this->control_groups['copyClipboardIcons'] = [
			'title'		=> esc_html__('Icons', 'brickscodes'),
			'required'	=> ['copyClipboardSource', '!=', ''],
		];
	}
	
	public function set_controls() {
		$this->controls['copySep'] = [
			'label'			=> esc_html__( 'Before Copy', 'brickscodes' ),
			'type'			=> 'separator',
		];
		
		$this->controls['copyClipboardtext'] = [
			'label'			=> esc_html__( 'Text', 'brickscodes' ),
			'type'			=> 'text',
			'inline' 		=> true,
			'placeholder'	=> esc_html__( 'Copy', 'brickscodes' ),
		];
		
		$this->controls['copyTitleAttribute'] = [
			'label'			=> esc_html__( 'Attribute Title', 'brickscodes' ),
			'type'			=> 'text',
			'placeholder'	=> esc_html__( 'Copy to clipboard', 'brickscodes' ),
		];
		
		$this->controls['copyIcon'] = [
			'label'			=> esc_html__( 'Icon (Svg)', 'brickscodes' ),
			'type'			=> 'svg',
		];
		
		$this->controls['copiedSep'] = [
			'label'			=> esc_html__( 'After Copied', 'brickscodes' ),
			'type'			=> 'separator',
		];
		
		$this->controls['copiedClipboardtext'] = [
			'label'			=> esc_html__( 'Text', 'brickscodes' ),
			'type'			=> 'text',
			'inline' 		=> true,
			'placeholder'	=> esc_html__( 'Copied', 'brickscodes' ),
		];
		
		$this->controls['copiedTitleAttribute'] = [
			'label'			=> esc_html__( 'Attribute Title', 'brickscodes' ),
			'type'			=> 'text',
			'placeholder'	=> esc_html__( 'Content successfully copied to clipboard.', 'brickscodes' ),
		];
		
		$this->controls['copiedIcon'] = [
			'label'			=> esc_html__( 'Icon (Svg)', 'brickscodes' ),
			'type'			=> 'svg',
		];
		
		$this->controls['copySourceSep'] = [
			'label'			=> esc_html__( 'Source', 'brickscodes' ),
			'type'			=> 'separator',
		];
		
		$this->controls['copyClipboardSource'] = [
			'label'		=> esc_html__( 'Source to Copy', 'brickscodes' ),
			'type'		=> 'select',
			'options'	=> [
				'dynamic-content'	=> esc_html__( 'From Dynamic data', 'brickscodes' ),
				'element-content'	=> esc_html__( 'Element Text Content by Class/Id', 'brickscodes' ),
				'element-json'		=> esc_html__( 'Element Json', 'brickscodes' ),
				'template-json'		=> esc_html__( 'Template Json', 'brickscodes' ),
			],
			'clearable'		=> false,
			'default'		=> 'dynamic-content',
			'placeholder'	=> esc_html__( 'From Dynamic data', 'brickscodes' ),
		];
		
		$this->controls['copyClipboardSourceDynamic'] = [
			'label'		=> esc_html__( 'Text & Dynamic Tag', 'brickscodes' ),
			'type'		=> 'text',
			'required'	=> ['copyClipboardSource', '=', 'dynamic-content' ],
		];
		
		$this->controls['copyClipboardSourceSelector'] = [
			'label'		=> esc_html__( 'Class/Id', 'brickscodes' ),
			'type'		=> 'text',
			'dd'		=> false,
			'required'	=> ['copyClipboardSource', '=', 'element-content' ],	
		];
		
		$this->controls['copyClipboardSourceTags'] = [
			'label'			=> esc_html__( 'Enclude Tags (separate each tag by comma)', 'brickscodes' ),
			'type'			=> 'text',
			'dd'			=> false,
			'placeholder'	=> esc_html__( ' h3, p, span', 'brickscodes' ),
			'required'		=> [ 
				['copyClipboardSource', '=', 'element-content' ],
				['copyClipboardSourceSelector', '!=', '' ],
			]	
		];
		
		$this->controls['copyClipboardElementId'] = [
			'label'		=> esc_html__( 'Element Section or Wrapper Bricks Id (element on current builder)', 'brickscodes' ),
			'type'		=> 'text',
			'dd'			=> false,
			'placeholder'	=> esc_html__( 'ahtgdr', 'brickscodes' ),
			'required'		=> ['copyClipboardSource', '=', 'element-json' ],	
		];
		
		$this->controls['copyClipboardTemplateInfo'] = [
			'content'	=> esc_html__( 'Set a visibility condition for this element to control who can copy element or download template. By default, it only checks if the user is logged in.', 'brickscodes' ),
			'type'		=> 'info',
			'required'	=> ['copyClipboardSource', '!=', ['dynamic-content', 'element-content']],	
		];
		
		$template_id = \Bricks\Templates::get_all_template_ids();
		$templates_options = [];

		foreach ( $template_id as $id ) {
			$templates_options[$id] = get_the_title( $id );
		}
		
		$this->controls['copyClipboardTemplateId'] = [
			'label'		=> esc_html__( 'Templates', 'brickscodes' ),
			'type'		=> 'select',
			'options'	=> $templates_options,
			'placeholder'	=> esc_html__( 'select', 'brickscodes' ),
			'required'		=> ['copyClipboardSource', '=', 'template-json' ],	
		];
	
		$this->controls['copyClipboardElementJson'] = [
			'label'		=> esc_html__( 'Copy Element Json', 'brickscodes' ),
			'type'		=> 'textarea',
			'dd'		=> false,
			'placeholder'	=> esc_html__( 'Element Bricks Id', 'brickscodes' ),	
		];
		
		$this->controls['copyRevertSep'] = [
			'type'		=> 'separator',
		];
		
		$this->controls['copyClipboardStateReset'] = [
			'label'		=> esc_html__( 'Reset Button State After Copy', 'brickscodes' ),
			'type'		=> 'checkbox',
		];
		
		$this->controls['copyClipboardStateResetDelay'] = [
			'label'		=> esc_html__( 'Delay (s)', 'brickscodes' ),
			'type'		=> 'number',
			'unitless'	=> true,
			'required'	=> ['copyClipboardStateReset', '=', true ],
		];
		
		$this->controls['copyClipboardButtonSize'] = [
			'label'		=> esc_html__( 'Size', 'brickscodes' ),
			'type'		=> 'select',
			'group'		=> 'copyClipboardButton',
			'inline'	=> true,
			'options'	=> $this->control_options['buttonSizes'],
		];
		
		$this->controls['copyClipboardButtonStyle'] = [
			'label'			=> esc_html__( 'Style', 'brickscodes' ),
			'type'			=> 'select',
			'group'			=> 'copyClipboardButton',
			'options'		=> $this->control_options['styles'],
			'inline'		=> true,
			'reset'			=> true,
			'default'		=> 'primary',
			'placeholder'	=> esc_html__( 'None', 'brickscodes' ),
		];
		
		$this->controls['copyClipboardButtonCircle'] = [
			'label' => esc_html__( 'Circle', 'brickscodes' ),
			'type'  => 'checkbox',
			'group'	=> 'copyClipboardButton',
			'reset' => true,
		];

		$this->controls['copyClipboardButtonOutline'] = [
			'label' => esc_html__( 'Outline', 'brickscodes' ),
			'type'  => 'checkbox',
			'group'	=> 'copyClipboardButton',
			'reset' => true,
		];
		
		$this->controls['copyClipboardIconHeight'] = [
			'label'		=> esc_html__( 'Height', 'brickscodes' ),
			'type'		=> 'number',
			'group'		=> 'copyClipboardIcons',
			'units'		=> true,
			'css'		=> [
				[
					'property' => 'height',
					'selector' => 'svg',
				],
			],
		];
		
		$this->controls['copyClipboardIconWidth'] = [
			'label'		=> esc_html__( 'Width', 'brickscodes' ),
			'type'		=> 'number',
			'group'		=> 'copyClipboardIcons',
			'units'		=> true,
			'css'		=> [
				[
					'property' => 'width',
					'selector' => 'svg',
				],
			],
		];
		
		$this->controls['copyClipboardIconStrokeWidth'] = [
			'label'		=> esc_html__( 'Stroke Width', 'brickscodes' ),
			'type'		=> 'number',
			'group'		=> 'copyClipboardIcons',
			'units'		=> true,
			'css'		=> [
				[
					'property' => 'stroke-width',
					'selector' => 'svg',
				],
			],
		];
		
		$this->controls['copyClipboardIconStrokeColor'] = [
			'label'		=> esc_html__( 'Stroke Color', 'brickscodes' ),
			'type'		=> 'color',
			'group'		=> 'copyClipboardIcons',
			'css'		=> [
				[
					'property' => 'stroke',
					'selector' => 'svg',
				],
			],
		];
		
		$this->controls['copyClipboardIconFillColor'] = [
			'label'		=> esc_html__( 'Fill Color', 'brickscodes' ),
			'type'		=> 'color',
			'group'		=> 'copyClipboardIcons',
			'css'		=> [
				[
					'property' => 'fill',
					'selector' => 'svg',
				],
			],
		];
		
		$this->controls['copyClipboardIconPosition'] = [
			'label'		=> esc_html__( 'Icon Position', 'brickscodes' ),
			'type'		=> 'select',
			'group'		=> 'copyClipboardIcons',
			'inline'	=> true,
			'options'	=> [
				'left'	=> esc_html__( 'Left', 'brickscodes' ),
				'right'	=> esc_html__( 'Right', 'brickscodes' ),
			],
		];
		
		$this->controls['copyClipboardIconGap'] = [
			'label'		=> esc_html__( 'Gap', 'brickscodes' ),
			'type'		=> 'number',
			'group'		=> 'copyClipboardIcons',
			'units'		=> true,
			'css'		=> [
				[
					'property' => 'gap',
				],
			],
			'required' => [ 'copyIcon', '!=', '' ],
		];

		$this->controls['copyClipboardIconSpace'] = [
			'label'		=> esc_html__( 'Space between', 'brickscodes' ),
			'type'		=> 'checkbox',
			'group'		=> 'copyClipboardIcons',
			'css'		=> [
				[
					'property' => 'justify-content',
					'value'    => 'space-between',
				],
			],
			'required' => [ 'copyIcon', '!=', '' ],
		];
	}
	
	public function enqueue_scripts() {
		wp_enqueue_script( 'bc-copy-clipboard', BRICKSCODES_PLUGIN_URL . 'public/assets/js/bc-copy-clipboard-button.js', ['bricks-scripts'], false, true );
		if ( bricks_is_frontend() ) {
			if ( $this->settings['copyClipboardSource'] === 'template-json' && isset($this->settings['copyClipboardTemplateId']) ) {
				wp_localize_script( 'bc-copy-clipboard', 'bc_copy_clipboard_ajax', array( 'bc_copy_clipboard_ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('bc-nonce') ) );
			}
		}
	}
	
	public function render() {
		$settings 			= $this->settings;
		$sourceType			= isset($settings['copyClipboardSource']) ? $settings['copyClipboardSource'] : false;
		$copyText			= !empty($settings['copyClipboardtext']) ? $settings['copyClipboardtext'] : 'Copy';
		$copiedText			= !empty($settings['copiedClipboardtext']) ? $settings['copiedClipboardtext'] : 'Copied';
		$copyTitle			= !empty($settings['copyTitleAttribute']) ? $settings['copyTitleAttribute'] : 'Copy to clipboard';
		$copiedTitle	 	= !empty($settings['copiedTitleAttribute']) ? $settings['copiedTitleAttribute'] : 'Content succssfully copied to clipboard.';
		$iconPost			= isset($settings['copyClipboardIconPosition']) ? $settings['copyClipboardIconPosition'] : 'right';
		$beforeIcon			= !empty($settings['copyIcon']) ? \Bricks\Helpers::file_get_contents(get_attached_file($settings['copyIcon']['id'])) : '';
		$afterIcon			= !empty($settings['copiedIcon']) ? \Bricks\Helpers::file_get_contents(get_attached_file($settings['copiedIcon']['id'])) : '';
		if ($afterIcon) {
			$afterIcon = preg_replace('/<svg([^>]*)>/i', '<svg$1 style="display:none;">', $afterIcon);
		}
		$dynamic_content 	= $sourceType && $sourceType === 'dynamic-content' && !empty($settings['copyClipboardSourceDynamic']) ? $settings['copyClipboardSourceDynamic'] : '';
		$selector_content	= $sourceType && $sourceType === 'element-content' && !empty($settings['copyClipboardSourceSelector']) ? $settings['copyClipboardSourceSelector'] : '';
		$excludeTags 		= !empty($selector_content) && !empty($settings['copyClipboardSourceTags']) ? $settings['copyClipboardSourceTags'] : '';
		$elementId			= $sourceType && $sourceType === 'element-json' && !empty($settings['copyClipboardElementId']) ? $settings['copyClipboardElementId'] : '';
		$elementObj 		= !empty($elementId) && !empty($settings['copyClipboardElementJson']) ? $settings['copyClipboardElementJson'] : '';
		$resetDelay 		= !empty($settings['copyClipboardStateResetDelay']) ? intval($settings['copyClipboardStateResetDelay']) : 5;
		
		$this->set_attribute( '_root', 'title', $copyTitle );
		$this->set_attribute( '_root', 'data-copy-text', $copyText );
		$this->set_attribute( '_root', 'data-copied-text', $copiedText );
		$this->set_attribute( '_root', 'data-copy-title', $copyTitle );
		$this->set_attribute( '_root', 'data-copied-title', $copiedTitle );
		$this->set_attribute( '_root', 'class', ['bricks-button'] );
		
		if ( !is_user_logged_in() && $sourceType && in_array($sourceType, ['element-json', 'template-json']) ) {
			$this->set_attribute( '_root', 'disabled', 'disabled');
		}

		if ( $sourceType ) {
			if ( $sourceType === 'dynamic-content' ) {
				$this->set_attribute( '_root', 'data-copy-content', bricks_render_dynamic_data($dynamic_content) );
			} else if ( $sourceType === 'element-content' ) {
				$this->set_attribute( '_root', 'data-selector-content', $selector_content);
				if ( !empty($excludeTags) ) {
					$this->set_attribute( '_root', 'data-exclude-tags', $excludeTags);
				}
			} 
			
			else if ( is_user_logged_in() && $sourceType === 'element-json' ) {
				$this->set_attribute( '_root', 'data-element-content', $elementObj);
			} else if ( is_user_logged_in() && $sourceType === 'template-json' && isset($settings['copyClipboardTemplateId']) ) {
				$this->set_attribute( '_root', 'data-template-id', intval($settings['copyClipboardTemplateId']));
			}
			
			if ( isset($settings['copyClipboardStateReset']) ) {
				$this->set_attribute( '_root', 'data-reset-delay', $resetDelay);
			}
		}
		
		if ( ! empty( $settings['copyClipboardButtonSize'] ) ) {
			$this->set_attribute( '_root', 'class', $settings['copyClipboardButtonSize'] );
		}
		
		if ( isset( $settings['copyClipboardButtonOutline'] ) ) {
			$this->set_attribute( '_root', 'class', 'outline' );
		}

		if ( ! empty( $settings['copyClipboardButtonStyle'] ) ) {
			// Outline (border)
			if ( isset( $settings['copyClipboardButtonOutline'] ) ) {
				$this->set_attribute( '_root', 'class', "bricks-color-{$settings['copyClipboardButtonStyle']}" );
			}

			// Background (= default)
			else {
				$this->set_attribute( '_root', 'class', "bricks-background-{$settings['copyClipboardButtonStyle']}" );
			}
		}

		// Button circle
		if ( isset( $settings['copyClipboardButtonCircle'] ) ) {
			$this->set_attribute( '_root', 'class', 'circle' );
		}
		
		$output = '<button ' . $this->render_attributes( '_root' ) . '>';
		
		$output .= !empty($beforeIcon)
			? ($iconPost === 'left' ? $beforeIcon . $afterIcon . $copyText : $copyText . $beforeIcon . $afterIcon)
			: $copyText;
		$output .= '</button>';
		
		echo $output;
	}
}
