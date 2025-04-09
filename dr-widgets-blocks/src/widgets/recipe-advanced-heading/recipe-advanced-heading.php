<?php
/**
 * Recipe Grid Module Two
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Advanced_Heading extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-advanced-heading';

	public function get_title() {
		return esc_html__( 'Advanced Heading', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-advanced-heading';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-advanced-heading', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeAdvancedHeading.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-advanced-heading'
		);
	}

	protected function register_controls() {
		/**
		 * Recipe Posts Layouts Section
		 */
		$this->start_controls_section(
			'general_section',
			array(
				'label' => esc_html__( 'General', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		
		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'dr-widgets-blocks' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'ai' => [
					'type' => 'text',
				],
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your title', 'dr-widgets-blocks' ),
				'default' => esc_html__( 'Add Your Heading Text Here', 'dr-widgets-blocks' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'dr-widgets-blocks' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'headingTag',
			array(
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Heading Tag', 'dr-widgets-blocks' ),
				'options'   => array(
					'h1'   => esc_html__( 'H1', 'dr-widgets-blocks' ),
					'h2'   => esc_html__( 'H2', 'dr-widgets-blocks' ),
					'h3'   => esc_html__( 'H3', 'dr-widgets-blocks' ),
					'h4'   => esc_html__( 'H4', 'dr-widgets-blocks' ),
					'h5'   => esc_html__( 'H5', 'dr-widgets-blocks' ),
					'h6'   => esc_html__( 'H6', 'dr-widgets-blocks' ),
					'div'  => esc_html__( 'Div', 'dr-widgets-blocks' ),
					'span' => esc_html__( 'span', 'dr-widgets-blocks' ),
					'p'    => esc_html__( 'p', 'dr-widgets-blocks' ),
				),
				'default'   => 'h3',
			)
		);
		
		$this->add_control(
			'layout',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Layout', 'dr-widgets-blocks' ),
				'options' => array(
					'layout-1' => esc_html__( 'Layout 1', 'dr-widgets-blocks' ),
					'layout-2' => esc_html__( 'Layout 2', 'dr-widgets-blocks' ),
					'layout-3' => esc_html__( 'Layout 3', 'dr-widgets-blocks' ),
					'layout-4' => esc_html__( 'Layout 4', 'dr-widgets-blocks' ),
					'layout-5' => esc_html__( 'Layout 5', 'dr-widgets-blocks' ),
					'layout-6' => esc_html__( 'Layout 6', 'dr-widgets-blocks' ),
					'layout-7' => esc_html__( 'Layout 7', 'dr-widgets-blocks' ),
					'layout-8' => esc_html__( 'Layout 8', 'dr-widgets-blocks' ),
					'layout-9' => esc_html__( 'Layout 9', 'dr-widgets-blocks' ),
				),
				'default' => 'layout-1',
			)
		);

		$this->end_controls_section();

		/**
		 * Recipe Posts Content Style Section
		 */
		$this->start_controls_section(
			'content_style_section',
			array(
				'label' => esc_html__( 'Style', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'condition' => array(
					'layout!' => array('layout-5', 'layout-9'),
				),
			)
		);

		$this->add_control(
			'titleColor',
			[
				'label' => esc_html__( 'Text Color', 'dr-widgets-blocks' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dr-heading-wrapper .dr-heading-title , {{WRAPPER}} .dr-heading-wrapper .dr-heading-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'titleHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-heading-wrapper .dr-heading-title:hover , {{WRAPPER}} .dr-heading-wrapper .dr-heading-title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .dr-heading-title',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .dr-heading-title',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .dr-heading-title',
			]
		);

		$this->add_control(
			'backgroundColor',
			array(
				'label'     => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-heading-title' => '--background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'underlineColor',
			array(
				'label'     => esc_html__( 'Underline Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-heading-wrapper' => '--underline-color: {{VALUE}};',
				),
				'condition' => array(
					'layout!' => array('layout-8'),
				),
			)
		);
		$this->add_control(
			'underlineHeight',
			array(
				'label'     => esc_html__( 'Underline Height', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-heading-wrapper' => '--underline-height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'headingPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-heading-wrapper .dr-heading-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		
		$settings  = $this->get_settings_for_display();

		if ( '' === $settings['title'] ) {
			return;
		}
		
		$layout  = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';

		$this->add_render_attribute( 'title', 'class', 'dr-heading-title' );

		if ( ! empty( $settings['layout'] ) ) {
			$layout  =  $settings['layout'];
		} else {
			$layout = 'layout-1';
		}

		if ( ! empty( $settings['alignment'] ) ) {
			$alignment  =  $settings['alignment'];
		}else {
			$alignment = '';
		}
	
		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'dr-size-' . $settings['size'] );
		} else {
			$this->add_render_attribute( 'title', 'class', 'dr-size-default' );
		}

		$this->add_inline_editing_attributes( 'title' );

		$title = $settings['title'];

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'url', $settings['link'] );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}
		$title_html = '<div class="dr-heading-wrapper dr-'. esc_attr( $alignment ) .' dr-'. esc_attr( $layout ) .'">';
		$title_html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', \Elementor\Utils::validate_html_tag( $settings['headingTag'] ), $this->get_render_attribute_string( 'title' ), $title );
		$title_html .= '</div>';
		// PHPCS - the variable $title_html holds safe data.
		echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		
	}
}