<?php
/**
 * Recipe Categories Tab Widget
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Categories_Tab_Two extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-categories-tab-two';

	public function get_title() {
		return esc_html__( 'Recipe Categories Tab 2', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-categories-tab';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-categories-tab-2', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeCategoryTabsTwo.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-categories-tab-2',
			'swiper',
			'e-swiper',
		);
	}

	public function get_script_depends()	{
		wp_register_script( 'drWidgets-categories-tab-2', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/widgets/recipe-categories-tab-two/recipe-categories-tab-two.js', array( 'jquery' ), DR_WIDGETS_BLOCKS_VERSION, true );

		return array(
			'drWidgets-categories-tab-2',
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
					'layout-2' => esc_html__( 'Layout 2', 'dr-widgets-blocks' )
				),
				'default' => 'layout-1',
			)
		);
		$this->add_responsive_control(
			'slidesPerView',
			array(
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'No. of Columns', 'dr-widgets-blocks' ),
				'min'         => 1,
				'max'         => 5,
				'step'        => 1,
				'default'     => 3,
				'laptop_default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
			)
		);
		$this->add_control(
			'gridGap',
			array(
				'label'      => esc_html__( 'Grid Gap', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::NUMBER,
				'default'    => 30,
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
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
		 * Recipe Categories Tab Slider Section
		 */
		$this->start_controls_section(
			'slider_section',
			array(
				'label' => esc_html__( 'Slider', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'autoplay',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Autoplay', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'autoplaydelay',
			array(
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'Autoplay Speed', 'dr-widgets-blocks' ),
				'min'         => 1,
				'max'         => 5000,
				'step'        => 1,
				'default'     => 3000,
			)
		);
		$this->add_control(
			'loop',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Loop', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'speed',
			array(
				'label'      => esc_html__( 'Transition Speed (ms)', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::NUMBER,
				'default'    => 300,
				'min'         => 1,
				'max'         => 1000,
				'step'        => 1,
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
			'showHeadingTitle',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Show HeadingTitle', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'showCategory',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Category', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
		$this->add_control(
			'showRecipeKeys',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Recipe keys', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'showAuthor',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Author', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'showRating',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Rating', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'showComment',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Comment', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'showPublishDate',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Published Date', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'showBookmark',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Bookmark', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
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
				'selector' => '{{WRAPPER}} .dr_tabs-wrapper',
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
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr_tabs-wrapper .dr_tab-widget-title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'headingTitleBackgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr_tabs-wrapper .dr_tab-widget-title' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr_tabs-wrapper .dr_tab-widget-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'headingTitleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr_tabs-wrapper .dr_tab-widget-title',
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
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr_tab-nav ul li span' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tabs_hover',
			array(
				'label' => esc_html__( 'Active/Hover', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'tabsHoverColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr_tab-nav ul li span:hover, {{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two ul li span.dr_active' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'tabsHoverUnderlineColor',
			array(
				'label'     => esc_html__( 'Underline Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-nav .dr_tab-title::after ' => 'color: {{VALUE}};',
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
				'separator' => 'before',
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr_tab-navigation' => '--nav-gap: {{SIZE}}{{UNIT}};',
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
		$this->add_responsive_control(
			'sliderPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tabs-wrapper .dr_swiper-navigation .dr-swiper-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'iconSize',
			array(
				'label'     => esc_html__( 'Icon Size', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-widget' => '--swiper-arrow-size: {{SIZE}}{{UNIT}};',
				),
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
					'{{WRAPPER}} .dr_swiper-navigation .dr-swiper-arrow' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'sliderArrowsBackgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_swiper-navigation .dr-swiper-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'sliderArrowsBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_swiper-navigation .dr-swiper-arrow',
			)
		);
		$this->add_responsive_control(
			'sliderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tabs-wrapper .dr_swiper-navigation .dr-swiper-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_swiper-navigation .dr-swiper-arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'sliderArrowsBackgroundHoverColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_swiper-navigation .dr-swiper-arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'sliderArrowsHoverBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_swiper-navigation .dr-swiper-arrow:hover',
			)
		);
		$this->add_responsive_control(
			'sliderHoverRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_tabs-wrapper .dr_swiper-navigation .dr-swiper-arrow:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/**
		 * Recipe Posts Content Style Section
		 */
		$this->start_controls_section(
			'content_style_section',
			array(
				'label' => esc_html__( 'Content', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'backgroundColor',
			array(
				'label'     => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .l3rp .dr_recipe-keys, {{WRAPPER}} .l1rp .dr_recipe-keys' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'Margin', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-post' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-post',
			)
		);
		$this->add_control(
			'borderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap, {{WRAPPER}} .dr-widgetBlock_fig-wrap svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .dr-widgetBlock_content-wrap' => 'border-radius: 0 0 {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .l2rp .dr-widgetBlock_fig-wrap, {{WRAPPER}} .l2rp .dr-widgetBlock_fig-wrap svg' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .l2rp .dr-widgetBlock_content-wrap' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'boxShadow',
				'label'    => esc_html__( 'Box Shadow', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-post',
			)
		);
		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'start'   => array(
						'title' => esc_html__( 'Left', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-center',
					),
					'end'  => array(
						'title' => esc_html__( 'Right', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-post' => '--grid-alignment: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Title Style Section
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
		$this->add_responsive_control(
			'titleMargin',
			array(
				'label'      => esc_html__( 'Margin', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'titleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr-widgetBlock_content-wrap .dr_title',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Separator Style Section
		 */
		$this->start_controls_section(
			'seperator_style_section',
			array(
				'label' => esc_html__( 'Separator', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showCategory' => 'yes',
				),
			)
		);
		$this->add_control(
			'separator',
			array(
				'label'   => esc_html__( 'Separator', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'dot'   => array(
						'title' => esc_html__( '.', 'dr-widgets-blocks' ),
						'icon'  => 'dr-icon-dot',
					),
					'slash' => array(
						'title' => esc_html__( '/', 'dr-widgets-blocks' ),
						'icon'  => 'dr-icon-slash',
					),
					'dash'  => array(
						'title' => esc_html__( '-', 'dr-widgets-blocks' ),
						'icon'  => 'dr-icon-dash',
					),
					'bar'   => array(
						'title' => esc_html__( '|', 'dr-widgets-blocks' ),
						'icon'  => 'dr-icon-bar',
					),
					'none'  => array(
						'title' => esc_html__( 'None', 'dr-widgets-blocks' ),
						'icon'  => 'dr-icon-none',
					),
				),
				'default' => 'dot',
			)
		);
		$this->add_control(
			'seperatorColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats .dr_recipe-cats-container > a::after, {{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-item::after' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Image Style Section
		 */
		$this->start_controls_section(
			'image_style_section',
			array(
				'label'     => esc_html__( 'Image', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'imageSize',
			array(
				'label'   => esc_html__( 'Image Size', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => dr_widgets_blocks_get_image_size_options(),
				'default' => 'delrecpe-structured-data-1_1',
				'condition' => array(
					'layout' => 'layout-1',
				),
			)
		);
		$this->add_control(
			'imageSizel2',
			array(
				'label'   => esc_html__( 'Image Size', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => dr_widgets_blocks_get_image_size_options(),
				'default' => 'delrecpe-structured-data-1_1',
				'condition' => array(
					'layout' => 'layout-2',
				),
			)
		);
		$this->add_control(
			'imageCustomSize',
			array(
				'label'       => esc_html__( 'Custom Image Size', 'dr-widgets-blocks' ),
				'type'        => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'dr-widgets-blocks' ),
				'condition'   => array(
					'imageSize' => 'custom',
				),
			)
		);
		$this->add_responsive_control(
			'imageWidth',
			array(
				'label'      => esc_html__( 'Width', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr-widgetBlock_recipe-post .dr-widgetBlock_fig-wrap img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'imageHeight',
			array(
				'label'      => esc_html__( 'Height', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr-widgetBlock_recipe-post .dr-widgetBlock_fig-wrap img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'imageBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr-widgetBlock_recipe-post .dr-widgetBlock_fig-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'imageScale',
			array(
				'label'     => esc_html__( 'Image Scale', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'initial' => esc_html__( 'Original', 'dr-widgets-blocks' ),
					'contain' => esc_html__( 'Contain', 'dr-widgets-blocks' ),
					'cover'   => esc_html__( 'Cover', 'dr-widgets-blocks' ),
					'fill'    => esc_html__( 'Fill', 'dr-widgets-blocks' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-tab-two .dr-widgetBlock_recipe-post .dr-widgetBlock_fig-wrap img' => 'object-fit: {{VALUE}};',
				),
				'default'   => 'cover',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Recipe Keys Style Section
		 * */
		$this->start_controls_section(
			'recipe_keys_style_section',
			array(
				'label'     => esc_html__( 'Recipe Keys', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showRecipeKeys' => 'yes',
				),
			)
		);
		$this->add_control(
			'recipeKeysIconSize',
			array(
				'label'      => esc_html__( 'Icon Size', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr-widgetBlock_content-wrap' => '--keys-font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'recipeKeysIconSpace',
			array(
				'label'      => esc_html__( 'Space Between', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-keys .dr_recipe-keys-container' => '--dr_keys-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'recipeMargin',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-keys' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Meta Style Section
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
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-item::before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'metaIconColor',
			array(
				'label'     => esc_html__( 'Icon Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-item:not(.dr_meta-review)::before' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'metaTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-item',
			)
		);
		$this->add_control(
			'metaTextColor',
			array(
				'label'     => esc_html__( 'Text Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-item' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'metaIconSpacing',
			array(
				'label'      => esc_html__( 'Spacing', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-items' => '--dr_item-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Category Style Section
		 * */
		$this->start_controls_section(
			'category_style_section',
			array(
				'label'     => esc_html__( 'Category', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showCategory' => 'yes',
				),
			)
		);
		$this->add_control(
			'categorySpaceBetween',
			array(
				'label'      => esc_html__( 'Space Between', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats' => '--dr_item-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'categoryItemPadding',
			array(
				'label'      => esc_html__( 'Item Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'category_tabs' );
		$this->start_controls_tab(
			'category_normal',
			array(
				'label' => esc_html__( 'Normal', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'categoryColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'categoryBackground',
			array(
				'label'     => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'categoryBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget .dr_recipe-cats a',
			)
		);
		$this->add_control(
			'categoryBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'category_hover',
			array(
				'label' => esc_html__( 'Hover', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'categoryHoverColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'categoryHoverBackground',
			array(
				'label'     => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'categoryHoverBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget .dr_recipe-cats a:hover',
			)
		);
		$this->add_control(
			'categoryHoverBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'categoryMargin',
			array(
				'label'      => esc_html__( 'Margin', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'categoryTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget .dr_recipe-cats a',
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
		$show_heading_title = isset( $settings['showHeadingTitle'] ) && 'yes' === $settings['showHeadingTitle'] ? true : false;
		$heading_title      = isset( $settings['headingTitle'] ) ? $settings['headingTitle'] : __( 'Recipes', 'dr-widgets-blocks' );
		$heading_tag        = isset( $settings['headingTitleTag'] ) ? $settings['headingTitleTag'] : 'h2';
		$show_title         = isset( $settings['showTitle'] ) && 'yes' === $settings['showTitle'] ? true : false;
		$title_tag          = isset( $settings['headingTag'] ) ? $settings['headingTag'] : 'h3';
		$show_total_time    = isset( $settings['showTotalTime'] ) && 'yes' === $settings['showTotalTime'] ? true : false;
		$show_difficulty    = isset( $settings['showDifficulty'] ) && 'yes' === $settings['showDifficulty'] ? true : false;
		$show_recipe_keys   = isset( $settings['showRecipeKeys'] ) && 'yes' === $settings['showRecipeKeys'] ? true : false;
		$show_author        = isset( $settings['showAuthor'] ) && 'yes' === $settings['showAuthor'] ? true : false;
		$show_publish_date  = isset( $settings['showPublishDate'] ) && 'yes' === $settings['showPublishDate'] ? true : false;
		$show_rating        = isset( $settings['showRating'] ) && 'yes' === $settings['showRating'] ? true : false;
		$show_comment       = isset( $settings['showComment'] ) && 'yes' === $settings['showComment'] ? true : false;
		$show_category      = isset( $settings['showCategory'] ) && 'yes' === $settings['showCategory'] ? true : false;
		$separator          = isset( $settings['separator'] ) ? $settings['separator'] : 'dot';
		$show_wishlist      = isset( $settings['showBookmark'] ) && 'yes' === $settings['showBookmark'] ? true : false;
		$layout_class       = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';
		$per_page           = isset( $settings['postsPerPage'] ) ? $settings['postsPerPage'] : 12;
		$recipe_ids         = isset( $settings['exclude'] ) ? $settings['exclude'] : false;
		$order_by           = isset( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order              = isset( $settings['order'] ) ? $settings['order'] : 'DESC';
		$offset             = isset( $settings['offset'] ) ? $settings['offset'] : 0;
		$taxonomy           = isset( $settings['taxonomy'] ) && '' !== $settings['taxonomy'] ? $settings['taxonomy'] : 'recipe-course';
		$terms              = $taxonomy ? ( isset( $settings[ "{$taxonomy}_term_id" ] ) ? $settings[ "{$taxonomy}_term_id" ] : '' ) : '';
		$all_taxonomy       = isset( $settings['all_taxonomy'] ) && 'yes' === $settings['all_taxonomy'] ? true : false;
		
		$image_size          = isset( $settings['imageSize'] ) ? $settings['imageSize'] : 'delrecpe-structured-data-1_1';
		$image_size_l2       = isset( $settings['imageSizel2'] ) ? $settings['imageSizel2'] : 'delrecpe-structured-data-1_1';
		$image_custom_size   = isset( $settings['imageCustomSize'] ) ? $settings['imageCustomSize'] : false;
		$image_size          = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		
		$all_term_id        = isset( $settings['all_term_id'] ) ? $settings['all_term_id'] : [];
		$list_term_ids = array_map(function($term) {
			return (int)substr(strrchr($term, '_'), 1);
		}, $all_term_id);

		//swiper settings
		$slider_settings = array(
			'slidesPerView' => isset( $settings['slidesPerView_mobile'] ) ? $settings['slidesPerView_mobile'] : 1,
			'spaceBetween'  => isset( $settings['gridGap'] ) ? $settings['gridGap'] : 30,
			'loop'          => isset( $settings['loop'] ) ? $settings['loop'] : true,
			'speed'         => isset( $settings['speed'] ) ? $settings['speed'] : 300,
			'breakpoints'   => array(
				768  => array(
					'slidesPerView' => (int) isset( $settings['slidesPerView_tablet'] ) ? $settings['slidesPerView_tablet'] : 1,
				),
				1025 => array(
					'slidesPerView' => (int) isset( $settings['slidesPerView_laptop'] ) ? $settings['slidesPerView_laptop'] : 3,
				),
				1367 => array(
					'slidesPerView' => (int) isset( $settings['slidesPerView'] ) ? $settings['slidesPerView'] : 3,
				),
			)
		);

		if ( isset( $settings['autoplay'] ) && 'yes' === $settings['autoplay'] ) {
			$slider_settings['autoplay'] = array(
				'delay' => (int) isset( $settings['autoplaydelay'] ) ? $settings['autoplaydelay'] : 3000,
				'disableOnInteraction' => false,
			);
		}

		if($layout_class === 'layout-2'){
			$slider_settings['grid'] =array(
				'rows' => 2,
				'fill' => 'row',
			);
		}

		$this->add_render_attribute( 
			'swiper-wrapper',
			[
				'data-swiper-options' => [esc_attr( wp_json_encode( $slider_settings ) )]
			] 
		);

		$categories = [];

		if(!$all_taxonomy){
			$categories = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'include'    => $terms,
					'hide_empty' => true,
					'orderby'    => 'include',
				)
			);
		} else {
			$categories = get_terms(
				array(
					'taxonomy'   => ['recipe-course', 'recipe-badge', 'recipe-cuisine', 'recipe-tag', 'recipe-cooking-method', 'recipe-key', 'recipe-cooking-method', 'recipe-dietary'],
					'hide_empty' => true,
					'include'    => $list_term_ids,
				)
			);
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
		<div class="dr-widget dr-widgetBlock_row recipe-tab-two dr_columns-1 <?php echo esc_attr( $layout_class ); ?> ">
			<div class="dr_column">
				<?php
				$data = array(
					'categories'         => $categories,
					'all_taxonomy'       => $all_taxonomy,
					'all_term_id'        => $all_term_id,
					'show_heading_title' => $show_heading_title,
					'args'               => $args,
					'show_title'         => $show_title,
					'title_tag'          => $title_tag,
					'show_total_time'    => $show_total_time,
					'show_difficulty'    => $show_difficulty,
					'heading_title'      => $heading_title,
					'heading_tag'        => $heading_tag,
					'layout_class'       => $layout_class,
					'block_id'           => $id,
					'show_recipe_keys'   => $show_recipe_keys,
					'show_author'        => $show_author,
					'show_publish_date'  => $show_publish_date,
					'show_rating'        => $show_rating,
					'show_comment'       => $show_comment,
					'show_category'      => $show_category,
					'separator'          => $separator,
					'show_wishlist'      => $show_wishlist,
					'image_size'         => $image_size,
					'image_size_l2'      => $image_size_l2,
					'slider_settings'    => $slider_settings,
				);

				dr_widgets_blocks_get_template( 'recipe-categories-tab-nav-two.php', $data, '', DR_WIDGETS_BLOCKS_PLUGIN_PATH . '/templates/recipe-cat-tab-two/' );

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
							<div class="dr_recipe-slider-tab-2 swiper swiper-container" data-id="<?php echo esc_attr( $id ); ?>" <?php $this->print_render_attribute_string( 'swiper-wrapper' ); ?>>
								<div class="swiper-wrapper">
									
									<?php
									while ( $recipes_query->have_posts() ) {
										$recipes_query->the_post();
										$recipe       = get_post( get_the_ID() );
										$recipe_metas = delicious_recipes_get_recipe( $recipe );
										$data         = array(
											'settings'     => array(
												'layout'  			=> $layout_class,
												'image_size'        => $image_size,
												'image_size_l2'     => $image_size_l2,
												'show_title'        => $show_title,
												'title_tag'         => $title_tag,
												'show_total_time'   => $show_total_time,
												'show_difficulty'   => $show_difficulty,
												'show_recipe_keys'  => $show_recipe_keys,
												'show_author'       => $show_author,
												'show_publish_date' => $show_publish_date,
												'show_rating'       => $show_rating,
												'show_comment'      => $show_comment,
												'show_category'     => $show_category,
												'separator'         => $separator,
												'show_wishlist'     => $show_wishlist,
											),
											'recipe_metas' => $recipe_metas,
											'i'            => $i,
											'count'        => $recipes_query->post_count,
										);

										dr_widgets_blocks_get_template( 'recipe-categories-tab-content-two.php', $data, '', DR_WIDGETS_BLOCKS_PLUGIN_PATH . '/templates/recipe-cat-tab-two/' );
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
		<?php
	}

}