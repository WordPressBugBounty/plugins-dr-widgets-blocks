<?php
/**
 * Recipe Posts Widget
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;
use \Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Grid_One extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-grid-one';

	public function get_title() {
		return esc_html__( 'Recipe Grid 1', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-grid';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-grid-one', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeGridOne.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-grid-one',
		);
	}

	public function get_script_depends()	{
		wp_register_script( 'drWidgets-recipe-grid-one', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/widgets/recipe-grid-one/recipe-grid-one.js', array( 'jquery' ), DR_WIDGETS_BLOCKS_VERSION, true );

		// Generate nonce
		wp_localize_script('drWidgets-recipe-grid-one', 'recipeGridOne', array(
			'nonce' => wp_create_nonce('recipe_grid_one_nonce')
		));

		return array( 'drWidgets-recipe-grid-one');

	}

	protected function register_controls() {
		/**
		 * Recipe Posts Layouts Section
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
				),
				'default' => 'layout-1',
				'frontend_available' => true,
			)
		);
		$this->add_responsive_control(
			'recipesPerRow',
			array(
				'type'            => \Elementor\Controls_Manager::NUMBER,
				'label'           => esc_html__( 'Recipes per Row', 'dr-widgets-blocks' ),
				'min'             => 1,
				'max'             => 6,
				'step'            => 1,
				'default'         => 3,
				'devices'         => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'frontend_available' => true,
			)
		);
		$this->add_responsive_control(
			'columnsGap',
			array(
				'label'      => esc_html__( 'Columns Gap', 'dr-widgets-blocks' ),
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
					'{{WRAPPER}} .dr-widgetBlock_row' => '--column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'rowsGap',
			array(
				'label'      => esc_html__( 'Rows Gap', 'dr-widgets-blocks' ),
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
					'{{WRAPPER}} .dr-widgetBlock_row' => '--row-gap: {{SIZE}}{{UNIT}};',
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
				'default'     => 3,
				'frontend_available' => true,
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
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'filterBy',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Filter By', 'dr-widgets-blocks' ),
				'options' => array(
					'latest'   => esc_html__( 'Latest Recipes', 'dr-widgets-blocks' ),
					'rand'     => esc_html__( 'Random Recipes', 'dr-widgets-blocks' ),
					'popular'  => esc_html__( 'Popular Recipes', 'dr-widgets-blocks' ),
					'taxonomy' => esc_html__( 'Taxonomy', 'dr-widgets-blocks' ),
				),
				'default' => 'latest',
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'all_taxonomy',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Show All Taxonomy', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => array(
					'filterBy' => 'taxonomy',
				),
				'frontend_available' => true,
			)
		);
		$taxonomies = delicious_recipes_get_taxonomies();
		$this->add_control(
			'taxonomy',
			array(
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Taxonomy', 'dr-widgets-blocks' ),
				'options'   => $taxonomies,
				'condition' => array(
					'filterBy' => 'taxonomy',
					'all_taxonomy!' => 'yes',
				),
				'default' => 'recipe-course',
				'frontend_available' => true,
			)
		);
		$list_all_tax= [];
		foreach ( $taxonomies as $tax => $label ) {
			$this->add_control(
				"{$tax}_term_id",
				array(
					'type'      => \Elementor\Controls_Manager::SELECT2,
					'label'     => esc_html__( 'Terms', 'dr-widgets-blocks' ),
					'multiple'  => true,
					'options'   => wp_list_pluck( get_terms( $tax ), 'name', 'term_id' ),
					'condition' => array(
						'filterBy'      => 'taxonomy',
						'all_taxonomy!' => 'yes',
						'taxonomy'      => $tax,
					),
					'frontend_available' => true,
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
					'filterBy' => 'taxonomy',
					'all_taxonomy' => 'yes',
				),
				'frontend_available' => true,
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
				'condition' => array(
					'filterBy!' => 'popular',
				),
				'frontend_available' => true,
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
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'exclude',
			array(
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'label'    => esc_html__( 'Exclude Recipes', 'dr-widgets-blocks' ),
				'multiple' => true,
				'options'  => dr_widgets_blocks_get_all_recipe_options(),
				'frontend_available' => true,
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
			'showFeatureImage',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Featured image', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'frontend_available' => true,
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
				'frontend_available' => true,
			)
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
				'condition' => array(
					'showTitle' => 'yes',
				),
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'showExcerpt',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Excerpt', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
                'condition' => array(
					'layout' => 'layout-1',
				),
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'excerptLength',
			array(
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'Excerpt Length', 'dr-widgets-blocks' ),
				'placeholder' => '20',
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
				'default'     => 20,
				'condition'   => array(
					'showExcerpt' => 'yes',
                    'layout' => 'layout-1',
				),
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Grid Pagination Section
		 */
		$this->start_controls_section(
			'pagination_section',
			array(
				'label' => esc_html__( 'Pagination', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'showPagination',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Show Pagination', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'paginationType',
			array(
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Pagination Type', 'dr-widgets-blocks' ),
				'options'   => array(
					'number'   => esc_html__( 'Numbers', 'dr-widgets-blocks' ),
					'loadMore'   => esc_html__( 'Load on click', 'dr-widgets-blocks' ),
				),
				'default'   => 'number',
				'condition' => array(
					'showPagination' => 'yes',
				),
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'prevText',
			array(
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label'       => esc_html__( 'Previous Label', 'dr-widgets-blocks' ),
				'placeholder' => esc_html__( 'Previous', 'dr-widgets-blocks' ),
				'condition' => array(
					'paginationType' => 'number',
					'showPagination' => 'yes',
				),
				'default' => __( 'Previous', 'dr-widgets-blocks' ),
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'nextText',
			array(
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label'       => esc_html__( 'Next Label', 'dr-widgets-blocks' ),
				'placeholder' => esc_html__( 'Next', 'dr-widgets-blocks' ),
				'condition' => array(
					'paginationType' => 'number',
					'showPagination' => 'yes',
				),
				'frontend_available' => true,
				'default' => __( 'Next', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'loadText',
			array(
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label'       => esc_html__( 'Button Label', 'dr-widgets-blocks' ),
				'default' => __( 'Load More', 'dr-widgets-blocks' ),
				'condition' => array(
					'paginationType' => 'loadMore',
					'showPagination' => 'yes',
				),
				'frontend_available' => true,
			)
		);
		$this->add_responsive_control(
			'paginationAlignment',
			array(
				'label'     => esc_html__( 'Alignment', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'start'   => array(
						'title' => esc_html__( 'Left', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-align-start-h',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-align-center-h',
					),
					'end'  => array(
						'title' => esc_html__( 'Right', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-align-end-h',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-alignment: {{VALUE}};',
				),
				'default' => 'center',
				'condition' => array(
					'showPagination' => 'yes',
				),
			)
		);
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-post' => '--background-color: {{VALUE}};',
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
					'{{WRAPPER}} .dr-widget .dr_recipe-cats .dr_recipe-cats-container > a::after' => 'background-color: {{VALUE}};',
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
				'condition' => array(
					'showFeatureImage' => 'yes',
				),
			)
		);
		$this->add_control(
			'imageSize',
			array(
				'label'   => esc_html__( 'Image Size', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => dr_widgets_blocks_get_image_size_options(),
				'default' => 'delrecpe-structured-data-4_3',
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
					'{{WRAPPER}} .dr-widgetBlock_row.recipe-grid-post-one .dr-widgetBlock_recipe-post .dr-widgetBlock_fig-wrap img' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap img' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap, {{WRAPPER}} .l2rp .dr-widgetBlock_fig-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'imageAlignment',
			array(
				'label'     => esc_html__( 'Alignment', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'left'   => esc_html__( 'Left', 'dr-widgets-blocks' ),
					'center' => esc_html__( 'Center', 'dr-widgets-blocks' ),
					'right'  => esc_html__( 'Right', 'dr-widgets-blocks' ),
				),
				'default'   => 'left',
				'condition' => array(
					'layout' => 'layout-2',
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
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap img' => 'object-fit: {{VALUE}};',
				),
				'default'   => 'initial',
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
				'selector' => '{{WRAPPER}} .dr-widgetBlock_row.recipe-grid-post-one .dr-widgetBlock_content-wrap .dr_title',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Excerpt Style Section
		*/
		$this->start_controls_section(
			'excerpt_style_section',
			array(
				'label'     => esc_html__( 'Excerpt', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showExcerpt' => 'yes',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerptTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_content',
			)
		);
		$this->add_control(
			'excerptColor',
			array(
				'label'     => esc_html__( 'Text Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_content' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'excerptAlignment',
			array(
				'label'     => esc_html__( 'Alignment', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr_content' => 'text-align: {{VALUE}};',
				),
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

		/**
		 * Recipe Grid Pagination Style Section
		 * */
		$this->start_controls_section(
			'pagination_style_section',
			array(
				'label'     => esc_html__( 'Pagination', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showPagination' => 'yes',
					'paginationType' => 'number',
				),
			)
		);
		$this->add_responsive_control(
			'paginationPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination ' => '--pagination-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'paginationMargin',
			array(
				'label'      => esc_html__( 'Margin', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination ' => '--pagination-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'paginationTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget-pagination',
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'paginationBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget-pagination',
			)
		);
		$this->add_control(
			'paginationBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination ' => '--pagination-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'paginationStylingTitle',
			array(
				'label'     => esc_html__('Pagination Styling', 'dr-widgets-blocks'),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'paginationNumberPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-number-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'paginationSpacing',
			array(
				'label'      => esc_html__( 'Spacing Between Items', 'dr-widgets-blocks' ),
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
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'pagina_tabs' );
		// Normal Tab
		$this->start_controls_tab(
			'pagination_normal',
			array(
				'label' => esc_html__( 'Normal', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'paginationColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'paginationBackground',
			array(
				'label'     => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-bg: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		// Active Tab
		$this->start_controls_tab(
			'pagination_active',
			array(
				'label' => esc_html__( 'Active', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'paginationActiveColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-active-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'paginationAciveBackgroundColor',
			array(
				'label'     => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-active-bg: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'paginationBorderColor',
			array(
				'label'     => esc_html__( 'Border Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-active-border: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		// Hover Tab
		$this->start_controls_tab(
			'pagination_hover',
			array(
				'label' => esc_html__( 'Hover', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'paginationHoverColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-hover-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'paginationHoverBackgroundColor',
			array(
				'label'     => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-hover-bg: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'paginationHoverBorderColor',
			array(
				'label'     => esc_html__( 'Border Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-hover-border: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();



		$this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'paginationPNColor',
			array(
				'label'     => esc_html__( 'Prev/Next Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-p-n-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'paginationNumberBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget-pagination .page-numbers:not(.prev, .next, .dots)',
			)
		);
		$this->add_control(
			'paginationBorderNumberRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--pagination-number-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Load More Button Style Section
		 * */

		$this->start_controls_section(
			'pagination_button_style_section',
			array(
				'label'     => esc_html__( 'Load More Button', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showPagination' => 'yes',
					'paginationType' => 'loadMore',
				),
			)
		);
		$this->add_responsive_control(
			'buttonMargin',
			array(
				'label'      => esc_html__( 'Margin', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'buttonPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'buttonTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget-pagination .dr-widget-pagination__btn',
			)
		);
		$this->start_controls_tabs( 'button_tabs' );
		$this->start_controls_tab(
			'button_tabs_normal',
			array(
				'label' => esc_html__( 'Normal', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'buttonBackgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-bg: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'buttonColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'buttonBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget-pagination .dr-widget-pagination__btn',
			)
		);
		$this->add_control(
			'buttonBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tabs_hover',
			array(
				'label' => esc_html__( 'Hover', 'dr-widgets-blocks' ),
			)
		);
		$this->add_control(
			'buttonBackgroundColorHover',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-bg-hover: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'buttonColorHover',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-color-hover: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'buttonBorderHover',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget-pagination .dr-widget-pagination__btn:hover',
			)
		);
		$this->add_control(
			'buttonBorderRadiusHover',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget-pagination' => '--button-radius-hover: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$per_page   = isset( $settings['postsPerPage'] ) ? $settings['postsPerPage'] : 3;
		$recipe_ids = isset( $settings['exclude'] ) ? $settings['exclude'] : false;
		$order_by   = isset( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order      = isset( $settings['order'] ) ? $settings['order'] : 'DESC';
		$offset     = isset( $settings['offset'] ) ? $settings['offset'] : 0;

		$paged = max( 1, get_query_var( 'paged', 1 ) );

		if(isset( $settings['filterBy'] )){
			$orderby = $settings['filterBy'] === 'rand' ? 'rand' : ($settings['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'offset'           => $offset,
			'paged'            => $paged,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( isset($settings['filterBy']) && $settings['filterBy'] === 'popular' || 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$taxonomy           = isset( $settings['taxonomy'] ) && '' !== $settings['taxonomy'] ? $settings['taxonomy'] : 'recipe-course';
		$terms              = $taxonomy ? ( isset( $settings[ "{$taxonomy}_term_id" ] ) ? $settings[ "{$taxonomy}_term_id" ] : '' ) : '';
		$all_taxonomy       = isset( $settings['all_taxonomy'] ) && 'yes' === $settings['all_taxonomy'] ? true : false;
		$all_term_id        = isset( $settings['all_term_id'] ) ? $settings['all_term_id'] : [];
		$per_row        	= isset( $settings['recipesPerRow'] ) ? absint( $settings['recipesPerRow'] ) : 3;
		$per_row_tablet 	= isset( $settings['recipesPerRow_tablet'] ) ? absint( $settings['recipesPerRow_tablet'] ) : 2;
		$per_row_mobile 	= isset( $settings['recipesPerRow_mobile'] ) ? absint( $settings['recipesPerRow_mobile'] ) : 1;
		$layout         	= isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';
		$show_pagination    = isset( $settings['showPagination'] ) && 'yes'   === $settings['showPagination'] ? true : false;
		$pagination_type    = isset( $settings['paginationType'] ) ? $settings['paginationType'] : 'number';
		$show_feature_image = isset( $settings['showFeatureImage'] ) && 'yes' === $settings['showFeatureImage'] ? true : false;
		$image_size         = isset( $settings['imageSize'] ) ? $settings['imageSize'] : 'delrecpe-structured-data-4_3';
		$image_size_l2      = isset( $settings['imageSizel2'] ) ? $settings['imageSizel2'] : 'delrecpe-structured-data-1_1';
		$image_custom_size  = isset( $settings['imageCustomSize'] ) ? $settings['imageCustomSize'] : false;
		$image_size         = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$show_title         = isset( $settings['showTitle'] ) && 'yes'        === $settings['showTitle'] ? true : false;
		$title_tag          = isset( $settings['headingTag'] ) ? $settings['headingTag'] : 'h3';
		$show_total_time    = isset( $settings['showTotalTime'] ) && 'yes'    === $settings['showTotalTime'] ? true : false;
		$show_difficulty    = isset( $settings['showDifficulty'] ) && 'yes'   === $settings['showDifficulty'] ? true : false;
		$show_recipe_keys   = isset( $settings['showRecipeKeys'] ) && 'yes'   === $settings['showRecipeKeys'] ? true : false;
		$show_excerpt       = isset( $settings['showExcerpt'] ) && 'yes'      === $settings['showExcerpt'] ? true : false;
		$show_author        = isset( $settings['showAuthor'] ) && 'yes'       === $settings['showAuthor'] ? true : false;
		$show_publish_date  = isset( $settings['showPublishDate'] ) && 'yes'  === $settings['showPublishDate'] ? true : false;
		$show_rating        = isset( $settings['showRating'] ) && 'yes'       === $settings['showRating'] ? true : false;
		$show_comment       = isset( $settings['showComment'] ) && 'yes'      === $settings['showComment'] ? true : false;
		$show_category      = isset( $settings['showCategory'] ) && 'yes'     === $settings['showCategory'] ? true : false;
		$excerpt_length     = isset( $settings['excerptLength'] ) ? $settings['excerptLength'] : 20;
		$image_alignment    = isset( $settings['imageAlignment'] ) ? $settings['imageAlignment'] : 'left';
		$separator          = isset( $settings['separator'] ) ? $settings['separator'] : 'dot';
		$show_wishlist      = isset( $settings['showBookmark'] ) && 'yes'     === $settings['showBookmark'] ? true : false;
		$prev_text          = isset( $settings['prevText'] ) ? $settings['prevText'] : __( 'Previous', 'dr-widgets-blocks' );
		$next_text          = isset( $settings['nextText'] ) ? $settings['nextText'] : __( 'Next', 'dr-widgets-blocks' );
		$load_text          = isset( $settings['loadText'] ) ? $settings['loadText'] : __( 'Load More', 'dr-widgets-blocks' );

		$widget_data = array(
			'layout'  			  => $layout,
			'show_feature_image'  => $show_feature_image,
			'image_size'          => $image_size,
			'image_size_l2'       => $image_size_l2,
			'show_title'          => $show_title,
			'title_tag'           => $title_tag,
			'show_total_time'     => $show_total_time,
			'show_difficulty'     => $show_difficulty,
			'show_recipe_keys'    => $show_recipe_keys,
			'show_excerpt'        => $show_excerpt,
			'show_author'         => $show_author,
			'show_publish_date'   => $show_publish_date,
			'show_rating'         => $show_rating,
			'show_comment'        => $show_comment,
			'show_category'       => $show_category,
			'excerpt_length'      => $excerpt_length,
			'image_alignment'     => $image_alignment,
			'separator'           => $separator,
			'show_wishlist'       => $show_wishlist,
			'pagination_type'     => $pagination_type,
			'current_page'        => $paged,
			'pagination_type'     => $pagination_type,
			'show_pagination'     => $show_pagination,
			'per_row'			  => $per_row,
			'per_row_tablet'	  => $per_row_tablet,
			'per_row_mobile'	  => $per_row_mobile,
			'taxonomy'            => $taxonomy,
			'terms'               => $terms,
			'all_taxonomy'        => $all_taxonomy,
			'all_term_id'         => $all_term_id,
			'args' 				  => $args,
			'paged' 			  => $paged,
			'prev_text' 		  => $prev_text,
			'next_text' 		  => $next_text,
			'load_text' 		  => $load_text
		);
		extract( $widget_data );
		?>
		<div class="dr-widget-wrapper" id="<?php echo esc_attr( $this->get_id() ); ?>">
			<?php include __DIR__ . '/render.php'; ?>
			<?php if( 'loadMore' === $pagination_type ) { ?>
				<div class="dr-widget-pagination">
					<button class="dr-widget-pagination__btn" >
						<?php echo esc_html( $load_text ); ?>
						<svg class="dr-widget-pagination__spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
					</button>
				</div>
				<?php 
				$query = new \WP_Query( $args );
				$max_pages = $query->max_num_pages;
				echo '<div class="dr-max-pages" data-max-pages="' . esc_attr( $max_pages ) . '"></div>'; 
			} ?>
		</div>
		<?php 
	}
}