<?php
/**
 * Recipe Categories Tab Widget
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;
use \Elementor\Controls_Manager;
use Elementor\Plugin;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Categories_Tab extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-categories-tab';

	public function get_title() {
		return esc_html__( 'Recipe Categories Tab', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-categories-tab';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-categories-tab', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeCategoryTabs.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-categories-tab',
			'swiper',
			'e-swiper',
		);
	}

	public function get_script_depends()	{
		wp_register_script( 'drWidgets-categories-tab', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/widgets/recipe-categories-tab/recipe-categories-tab.js', array( 'jquery' ), DR_WIDGETS_BLOCKS_VERSION, true );

		return array(
			'drWidgets-categories-tab',
			'swiper-bundle'
		);
	}


	protected function register_controls() {

		/**
		 * Recipe Categories Tab Layouts Section
		 */
		$this->start_controls_section(
			'layout_section',
			array(
				'label' => esc_html__( 'Layout', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
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
				),
				'default' => 'layout-1',
			)
		);
		$this->add_responsive_control(
			'gridGap',
			array(
				'label'      => esc_html__( 'Grid Gap', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tab' => '--grid-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Query Section
		 */
		$this->start_controls_section(
			'query_section',
			array(
				'label' => esc_html__( 'Query', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'postsPerPage',
			array(
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'No. of Posts', 'dr-widgets-blocks' ),
				'placeholder' => '0',
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
				'default'     => 12,
			)
		);
		$this->add_control(
			'offset',
			array(
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'Offset', 'dr-widgets-blocks' ),
				'placeholder' => '0',
				'min'         => 0,
				'max'         => 100,
				'step'        => 1,
				'default'     => 0,
			)
		);

		$this->add_control(
			'all_taxonomy',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Show All Taxonomy', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$taxonomies = delicious_recipes_get_taxonomies();
		$this->add_control(
			'taxonomy',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Taxonomy', 'dr-widgets-blocks' ),
				'options' => $taxonomies,
				'default' => 'recipe-course',
				'condition' => array(
					'all_taxonomy!' => 'yes',
				),
			)
		);

		$list_all_tax = []; //Store all taxonomy in this array
		foreach ( $taxonomies as $tax => $label ) {
			$this->add_control(
				"{$tax}_term_id",
				array(
					'type'      => 'drwb-sortable-select2',
					'label'     => esc_html__( 'Terms', 'dr-widgets-blocks' ),
					'multiple'  => true,
					'options'   => wp_list_pluck( get_terms( $tax ), 'name', 'term_id' ),
					'condition' => array(
						'taxonomy' => $tax,
						'all_taxonomy!' => 'yes',
					),
				)
			);

			// Update the array to store all taxonomy
			$terms = get_terms( $tax );
			if(is_array($terms) && !is_wp_error($terms)){
				foreach ( $terms as $term ) {
					$list_all_tax["{$tax}_{$term->term_id}"] = $term->name;
				}
			}
		}

		$this->add_control(
			'all_term_id',
			array(
				'type'      => 'drwb-sortable-select2',
				'label'     => esc_html__( 'Terms', 'dr-widgets-blocks' ),
				'multiple'  => true,
				'default'   => [],
				'options'   => $list_all_tax,
				'condition' => array(
					'all_taxonomy' => 'yes',
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Order by', 'dr-widgets-blocks' ),
				'options' => array(
					'date'           => esc_html__( 'Published Date', 'dr-widgets-blocks' ),
					'modified'       => esc_html__( 'Modified Date', 'dr-widgets-blocks' ),
					'title'          => esc_html__( 'Title', 'dr-widgets-blocks' ),
					'rand'           => esc_html__( 'Random', 'dr-widgets-blocks' ),
					'meta_value_num' => esc_html__( 'Views', 'dr-widgets-blocks' ),
					'comment_count'  => esc_html__( 'Comment Count', 'dr-widgets-blocks' ),
					'ID'             => esc_html__( 'ID', 'dr-widgets-blocks' ),
				),
				'default' => 'date',
			)
		);
		$this->add_control(
			'order',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Order', 'dr-widgets-blocks' ),
				'options' => array(
					'asc'  => esc_html__( 'Ascending', 'dr-widgets-blocks' ),
					'desc' => esc_html__( 'Descending', 'dr-widgets-blocks' ),
				),
				'default' => 'desc',
			)
		);
		$this->add_control(
			'exclude',
			array(
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'label'    => esc_html__( 'Exclude Recipes', 'dr-widgets-blocks' ),
				'multiple' => true,
				'options'  => dr_widgets_blocks_get_all_recipe_options(),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Additional Section
		 */
		$this->start_controls_section(
			'additional_section',
			array(
				'label' => esc_html__( 'Additional', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'headingTitle',
			array(
				'label'   => esc_html__( 'Heading Title', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Recipes', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'headingTitleTag',
			array(
				'label'   => esc_html__( 'Heading Tag', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
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
				'default' => 'h2',
			)
		);
		$this->add_control(
			'showTitle',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Title', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'headingTag',
			array(
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Title Tag', 'dr-widgets-blocks' ),
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
				'condition' => array(
					'showTitle' => 'yes',
				),
			)
		);
		$this->add_control(
			'showTotalTime',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Total time', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'showDifficulty',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Difficulty', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Tab Content Style Section
		 */
		$this->start_controls_section(
			'content_style_section',
			array(
				'label' => esc_html__( 'Content', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'overlayColor',
			array(
				'label'     => esc_html__( 'Overlay Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap' => 'background: linear-gradient(to bottom, transparent, {{VALUE}});',
				),
			)
		);
		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'borderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow: hidden',
				),
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
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-post' => 'text-align: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Tabs Style Section
		 */
		$this->start_controls_section(
			'tabs_style_section',
			array(
				'label' => esc_html__( 'Tabs', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'tabsMargin',
			array(
				'label'      => esc_html__( 'Margin', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tabs-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'tabsBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_tabs-wrapper .dr_tab-nav',
			)
		);

		$this->add_control(
			'tabTitleHeading',
			array(
				'label'     => esc_html__( 'Heading Title', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'headingTitleColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-widget-title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'headingTitleBackgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-widget-title' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'headingTitlePadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tab-widget-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'headingTitleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_tab-widget-title',
			)
		);

		$this->add_control(
			'tabsItem',
			array(
				'label'     => esc_html__( 'Tab Items', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->start_controls_tabs( 'categories_tabs' );
		$this->start_controls_tab(
			'tabs_normal',
			array(
				'label' => esc_html__( 'Normal', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'tabsColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-nav ul li span' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'tabsBackgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-nav ul li span' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'tabsPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tab-nav ul li span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'tabsBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tab-nav ul li span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tabs_hover',
			array(
				'label' => esc_html__( 'Hover', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'tabsHoverColor',
			array(
				'label'     => esc_html__( 'Active/Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-nav ul li span:hover, {{WRAPPER}} ul li span.dr_active' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'tabsHoverBackgroundColor',
			array(
				'label'     => esc_html__( 'Active/Hover Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-nav ul li span:hover, {{WRAPPER}} ul li span.dr_active' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'tabsHoverPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tab-nav ul li span:hover, {{WRAPPER}} ul li span.dr_active' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'tabsHoverBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tab-nav ul li span:hover, {{WRAPPER}} ul li span.dr_active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'tabsItemSpacing',
			array(
				'label'     => esc_html__( 'Item Spacing', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-navigation li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dr_tab-nav .dr_tab-dropdown li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'tabsTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_tab-nav ul li',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Tab Slider Arrows Style Section
		 */
		$this->start_controls_section(
			'tabs_slider_arrows_style_section',
			array(
				'label' => esc_html__( 'Slider Arrows', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->start_controls_tabs( 'slider_arrows_tabs' );
		$this->start_controls_tab(
			'slider_arrows_tabs_normal',
			array(
				'label' => esc_html__( 'Normal', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'sliderArrowsColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_swiper-navigation .dr_swiper-arrow' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'sliderArrowsBackgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_swiper-navigation .dr_swiper-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'sliderArrowsBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_swiper-navigation .dr_swiper-arrow',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'slider_arrows_tabs_hover',
			array(
				'label' => esc_html__( 'Hover', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'sliderArrowsHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_swiper-navigation .dr_swiper-arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'sliderArrowsBackgroundHoverColor',
			array(
				'label'     => esc_html__( 'Background Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_swiper-navigation .dr_swiper-arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'sliderArrowsHoverBorder',
				'label'    => esc_html__( 'Hover Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_swiper-navigation .dr_swiper-arrow:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/**
		 * Recipe Categories Tab Title Style Section
		 */
		$this->start_controls_section(
			'title_style_section',
			array(
				'label'     => esc_html__( 'Title', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showTitle' => 'yes',
				),
			)
		);
		$this->add_control(
			'titleColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'titleHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_title a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'titleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_title',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Tab Meta Style Section
		 * */
		$this->start_controls_section(
			'meta_style_section',
			array(
				'label' => esc_html__( 'Recipe Metas', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'metaIconSize',
			array(
				'label'      => esc_html__( 'Icon Size', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr_meta-content .dr_meta-item::before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'metaIconColor',
			array(
				'label'     => esc_html__( 'Icon Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_meta-content .dr_meta-item::before' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'metaTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_meta-content .dr_meta-item',
			)
		);
		$this->add_control(
			'metaTextColor',
			array(
				'label'     => esc_html__( 'Text Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_meta-content .dr_meta-item' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'metaIconSpacing',
			array(
				'label'      => esc_html__( 'Spacing Between', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap' => '--dr_item-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'wrapper',
			array(
				'id' => dr_widgets_blocks_rand_md5(),
			)
		);
		$heading_title   = isset( $settings['headingTitle'] ) ? $settings['headingTitle'] : __( 'Recipes', 'dr-widgets-blocks' );
		$heading_tag     = isset( $settings['headingTitleTag'] ) ? $settings['headingTitleTag'] : 'h2';
		$show_title      = isset( $settings['showTitle'] ) && 'yes' === $settings['showTitle'] ? true : false;
		$title_tag       = isset( $settings['headingTag'] ) ? $settings['headingTag'] : 'h3';
		$show_total_time = isset( $settings['showTotalTime'] ) && 'yes' === $settings['showTotalTime'] ? true : false;
		$show_difficulty = isset( $settings['showDifficulty'] ) && 'yes' === $settings['showDifficulty'] ? true : false;

		$layout       = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';
		$layout_class = 'layout-1' === $layout ? 'l1rp' : ( 'layout-2' === $layout ? 'l2rp' : 'l3rp' );
		$per_slide    = 'layout-1' === $layout ? 3 : 4;
		$per_page     = isset( $settings['postsPerPage'] ) ? $settings['postsPerPage'] : 12;
		$recipe_ids   = isset( $settings['exclude'] ) ? $settings['exclude'] : false;
		$order_by     = isset( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order        = isset( $settings['order'] ) ? $settings['order'] : 'DESC';
		$offset       = isset( $settings['offset'] ) ? $settings['offset'] : 0;
		$taxonomy     = isset( $settings['taxonomy'] ) && '' !== $settings['taxonomy'] ? $settings['taxonomy'] : 'recipe-course';
		$terms        = $taxonomy ? ( isset( $settings[ "{$taxonomy}_term_id" ] ) ? $settings[ "{$taxonomy}_term_id" ] : '' ) : '';
		
		$all_taxonomy  = isset( $settings['all_taxonomy'] ) && 'yes' === $settings['all_taxonomy'] ? true : false;
		$all_term_id   = isset( $settings['all_term_id'] ) ? $settings['all_term_id'] : [];
		$list_term_ids = array_map(function($term) {
			return (int)substr(strrchr($term, '_'), 1);
		}, $all_term_id);

		$categories = [];

		// Normalize terms to ensure proper type handling
		$has_specific_terms = is_array( $terms ) ? ! empty( array_filter( $terms ) ) : ( ! empty( $terms ) && '' !== $terms );

		if(!$all_taxonomy){
			$term_args = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
			);
			// Only add 'include' and 'orderby' if specific terms are selected
			if ( $has_specific_terms ) {
				$term_args['include'] = $terms;
				$term_args['orderby'] = 'include';
			}
			$categories = get_terms( $term_args );
		} else {
			$term_args = array(
				'taxonomy'   => ['recipe-course', 'recipe-badge', 'recipe-cuisine', 'recipe-tag', 'recipe-cooking-method', 'recipe-key', 'recipe-cooking-method', 'recipe-dietary'],
				'hide_empty' => true,
			);
			// Only add 'include' if specific terms are selected
			$has_specific_all_terms = ! empty( array_filter( $list_term_ids ) );
			if ( $has_specific_all_terms ) {
				$term_args['include'] = $list_term_ids;
			}
			$categories = get_terms( $term_args );
		}

		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			?>
				<p>
					<?php esc_html_e( 'Please check the widget settings and make sure you have selected a valid taxonomy and term.', 'dr-widgets-blocks' ); ?>
				</p>
			<?php
			return;
		}

		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'offset'           => $offset,
			'orderby'          => $order_by,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$wrapper = $this->get_render_attribute_string( 'wrapper' );
		parse_str( $wrapper, $matches );
		$id = trim( $matches['id'], '"' );

		?>
		<div class="dr-widget">
			<div class="dr-widgetBlock_row dr_columns-1">
				<div class="dr_column">

				<?php
				$data = array(
					'categories'      => $categories,
					'all_taxonomy'    => $all_taxonomy,
					'all_term_id'     => $all_term_id,
					'args'            => $args,
					'per_slide'       => $per_slide,
					'show_title'      => $show_title,
					'title_tag'       => $title_tag,
					'show_total_time' => $show_total_time,
					'show_difficulty' => $show_difficulty,
					'heading_title'   => $heading_title,
					'heading_tag'     => $heading_tag,
					'layout_class'    => $layout_class,
					'block_id'        => $id,
				);

				dr_widgets_blocks_get_template( 'recipe-categories-tab-nav.php', $data );

				foreach ( $categories as $category ) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => $category->taxonomy,
							'terms'    => $category->term_id,
							'field'    => 'term_id',
						),
					);

					$recipes_query = new \WP_Query( $args );

					$i = 1;
					if ( $recipes_query->have_posts() ) {
						?>
						<div id="dr_tab-content-<?php echo esc_attr( $category->term_id ); ?>-<?php echo esc_attr( $id ); ?>" class="dr_tab-content">
							<div class="dr_recipe-slider swiper swiper-container" data-id="<?php echo esc_attr( $id ); ?>">
								<div class="swiper-wrapper">
									
									<?php
									while ( $recipes_query->have_posts() ) {
										$recipes_query->the_post();
										$recipe       = get_post( get_the_ID() );
										$recipe_metas = delicious_recipes_get_recipe( $recipe );
										$data         = array(
											'settings'     => array(
												'show_title'         => $show_title,
												'title_tag'          => $title_tag,
												'show_total_time'    => $show_total_time,
												'show_difficulty'    => $show_difficulty,
											),
											'recipe_metas' => $recipe_metas,
											'per_slide'    => $per_slide,
											'i'            => $i,
											'count'        => $recipes_query->post_count,
										);

										dr_widgets_blocks_get_template( 'recipe-categories-tab-content.php', $data );
										$i++;
									}
									?>

								</div>
							</div>
						</div>
						<?php
					}
					wp_reset_postdata();
				}

				dr_widgets_blocks_get_template( 'recipe-categories-tab-footer.php' );

				?>
				</div>
			</div>
		</div>
		<?php
	}

}
