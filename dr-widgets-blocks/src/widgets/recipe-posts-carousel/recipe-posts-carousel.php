<?php
/**
 * Recipe Posts Carousel Widget
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;
use \Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Posts_Carousel extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-posts-carousel';

	public function get_title() {
		return esc_html__( 'Recipe Posts Carousel', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-post-carousel';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-posts-carousel', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipePostsCarousel.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-posts-carousel',
			'swiper',
			'e-swiper',
		);
	}

	public function get_script_depends()	{
		wp_register_script( 'drWidgets-posts-carousel', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/widgets/recipe-posts-carousel/recipe-posts-carousel.js', array( 'jquery' ), DR_WIDGETS_BLOCKS_VERSION, true );

		return array(
			'drWidgets-posts-carousel',
			'swiper-bundle'
		);
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
					'layout-3' => esc_html__( 'Layout 3', 'dr-widgets-blocks' ),
				),
				'default' => 'layout-1',
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
				'default'     => 8,
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
			'showFeatureImage',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Featured image', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
					'{{WRAPPER}} .carousel_layout-2 .dr-widgetBlock_content-wrap' => 'background: linear-gradient(to bottom, transparent, {{VALUE}});',
					'{{WRAPPER}} .carousel_layout-2 .dr-widgetBlock_content-wrap' => 'background-color: transparent;',
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
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap, {{WRAPPER}} .dr-widgetBlock_fig-wrap svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .carousel_layout-3 .dr-widgetBlock_content-wrap' => 'border-radius: 0 0 {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'boxShadow',
				'label'    => esc_html__( 'Box Shadow', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-post, {{WRAPPER}} .dr-widgetBlock_recipe-post.l3rp, {{WRAPPER}} .dr-widgetBlock_recipe-post.l1rp .dr-widgetBlock_fig-wrap',
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
				'default' => 'delrecpe-structured-data-1_1',
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
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap img, {{WRAPPER}} .dr-widgetBlock_fig-wrap svg' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap img, {{WRAPPER}} .dr-widgetBlock_fig-wrap svg' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-widgetBlock_fig-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .dr_title',
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-carousel' => '--key-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'recipeKeysIconSpace',
			array(
				'label'      => esc_html__( 'Space Between', 'dr-widgets-blocks' ),
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
					'{{WRAPPER}} .dr_recipe-keys .dr_recipe-keys-container' => '--dr_keys-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'recipeKeysIconAlignment',
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-post .dr_recipe-keys' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .dr-widgetBlock_recipe-post .dr_recipe-keys .dr_recipe-keys-container' => 'justify-content: {{VALUE}};',
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
					'{{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-items' => '--dr_item-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'metaAlignment',
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
					'{{WRAPPER}} .dr-widget .dr_meta-content .dr_meta-items' => 'text-align: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Carousel Slider Style Section
		 * */
		$this->start_controls_section(
			'slider_style_section',
			array(
				'label' => esc_html__( 'Slider', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'recipesPerSlide',
			array(
				'type'            => \Elementor\Controls_Manager::NUMBER,
				'label'           => esc_html__( 'Recipes per Slide', 'dr-widgets-blocks' ),
				'min'             => 1,
				'max'             => 4,
				'step'            => 1,
				'default'         => 4,
				'devices'         => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 4,
				'tablet_default'  => 3,
				'mobile_default'  => 1,
				'condition'       => array(
					'layout' => array( 'layout-2', 'layout-3' ),
				),
			)
		);
		$this->add_responsive_control(
			'slideItemGap',
			array(
				'label'     => esc_html__( 'Slide Item Gap', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'condition' => array(
					'layout' => array( 'layout-2', 'layout-3' ),
				),
			)
		);
		$this->add_control(
			'showSliderArows',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Navigation', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'navSize',
			array(
				'label'      => esc_html__( 'Navigation Size', 'dr-widgets-blocks' ),
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'showSliderArows' => 'yes',
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'sliderArrowsBackgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'sliderArrowsBorder',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]',
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'sliderArrowsBackgroundHoverColor',
			array(
				'label'     => esc_html__( 'Background Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'sliderArrowsHoverBorder',
				'label'    => esc_html__( 'Hover Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'sliderArrowsBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-carousel [class*="swiper-button"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();

		$this->add_render_attribute(
			'wrapper',
			array(
				'id' => dr_widgets_blocks_rand_md5(),
			)
		);
		$per_page   = isset( $settings['postsPerPage'] ) ? $settings['postsPerPage'] : 3;
		$recipe_ids = isset( $settings['exclude'] ) ? $settings['exclude'] : false;
		$order_by   = isset( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order      = isset( $settings['order'] ) ? $settings['order'] : 'DESC';
		$offset     = isset( $settings['offset'] ) ? $settings['offset'] : 0;

		if(isset( $settings['filterBy'] )){
			$orderby = $settings['filterBy'] === 'rand' ? 'rand' : ($settings['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'offset'           => $offset,
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

		if(!$all_taxonomy){
			if ( $taxonomy && $terms ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $taxonomy,
						'terms'    => $terms,
						'field'    => 'term_id',
					),
				);
			} elseif ( $taxonomy ) {
				$args['taxonomy'] = $taxonomy;
			}
		} else {
			// Build tax_query args for filtering recipes by multiple taxonomy terms
			// Loops through selected term IDs, parses taxonomy and term from each,
			// and creates an array of tax queries with OR relation between them
			if ( !empty($all_term_id) && is_array($all_term_id) ) {
				$tax_array = array();
				foreach($all_term_id as $item) {
					$tax_array[] = array(
						'taxonomy' => dr_widgets_blocks_parse_term_id($item, true),
						'terms'    => dr_widgets_blocks_parse_term_id($item, false),
						'field'    => 'term_id',
					);
				}

				// Merge the tax_query args with OR relation between them
				$args['tax_query'] = array_merge(array(
					'relation' => 'OR',
				), $tax_array);
			}
		}

		$recipes_query = new \WP_Query( $args );

		if ( 0 === $recipes_query->post_count ) {
			?>
				<p>
					<?php esc_html_e( 'Please check the widget settings and make sure you have selected a valid taxonomy and term.', 'dr-widgets-blocks' ); ?>
				</p>
			<?php
			return;
		}

		$wrapper = $this->get_render_attribute_string( 'wrapper' );
		parse_str( $wrapper, $matches );
		$id = trim( $matches['id'], '"' );

		$layout           = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';
		$layout_class     = 'carousel_' . $layout;
		$per_slide        = 'layout-1' === $layout ? 1 : ( isset( $settings['recipesPerSlide'] ) ? absint( $settings['recipesPerSlide'] ) : 4 );
		$per_slide_tablet = 'layout-1' === $layout ? 1 : ( isset( $settings['recipesPerSlide_tablet'] ) ? absint( $settings['recipesPerSlide_tablet'] ) : 3 );
		$per_slide_mobile = 'layout-1' === $layout ? 1 : ( isset( $settings['recipesPerSlide_mobile'] ) ? absint( $settings['recipesPerSlide_mobile'] ) : 1 );
		$navigation       = isset( $settings['showSliderArows'] ) && 'yes' === $settings['showSliderArows'] ? true : false;
		$nav_class        = $navigation ? '' : 'dr_swiper-navigation-hidden';
		$item_gap         = 'layout-1' === $layout ? 0 : ( isset( $settings['slideItemGap']['size'] ) ? absint( $settings['slideItemGap']['size'] ) : 30 );
		$item_gap_tablet  = 'layout-1' === $layout ? 0 : ( isset( $settings['slideItemGap_tablet']['size'] ) ? absint( $settings['slideItemGap_tablet']['size'] ) : 20 );
		$item_gap_mobile  = 'layout-1' === $layout ? 0 : ( isset( $settings['slideItemGap_mobile']['size'] ) ? absint( $settings['slideItemGap_mobile']['size'] ) : 20 );
		$swiper_options   = array(
			'slidesPerView' => $per_slide_mobile,
			'spaceBetween'  => $item_gap_mobile,
			'breakpoints'   => array(
				'768'  => array(
					'slidesPerView' => $per_slide_tablet,
					'spaceBetween'  => $item_gap_tablet,
				),
				'1025' => array(
					'slidesPerView' => $per_slide,
					'spaceBetween'  => $item_gap,
				),
			),
		);

		$show_feature_image = isset( $settings['showFeatureImage'] ) && 'yes' === $settings['showFeatureImage'] ? true : false;
		$image_size         = isset( $settings['imageSize'] ) ? $settings['imageSize'] : 'delrecpe-structured-data-1_1';
		$image_custom_size  = isset( $settings['imageCustomSize'] ) ? $settings['imageCustomSize'] : false;
		$image_size         = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$show_title         = isset( $settings['showTitle'] ) && 'yes' === $settings['showTitle'] ? true : false;
		$title_tag          = isset( $settings['headingTag'] ) ? $settings['headingTag'] : 'h3';
		$show_total_time    = isset( $settings['showTotalTime'] ) && 'yes' === $settings['showTotalTime'] ? true : false;
		$show_difficulty    = isset( $settings['showDifficulty'] ) && 'yes' === $settings['showDifficulty'] ? true : false;
		$show_recipe_keys   = isset( $settings['showRecipeKeys'] ) && 'yes' === $settings['showRecipeKeys'] ? true : false;
		?>
		<div class="dr-widget">
			<div class="dr-widgetBlock_recipe-carousel <?php echo esc_attr( $layout_class ); ?>">
				<div class="swiper swiper-container" data-swiper="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>" data-id="<?php echo esc_attr( $id ); ?>">
					<div class="swiper-wrapper">
						<?php
						if ( $recipes_query->have_posts() ) {
							while ( $recipes_query->have_posts() ) {
								$recipes_query->the_post();
								$recipe       = get_post( get_the_ID() );
								$recipe_metas = delicious_recipes_get_recipe( $recipe );
								$data         = array(
									'settings'     => array(
										'show_feature_image' => $show_feature_image,
										'image_size'       => $image_size,
										'show_title'       => $show_title,
										'title_tag'        => $title_tag,
										'show_total_time'  => $show_total_time,
										'show_difficulty'  => $show_difficulty,
										'show_recipe_keys' => $show_recipe_keys,
										'layout'           => $layout,
									),
									'recipe_metas' => $recipe_metas,
								);

								dr_widgets_blocks_get_template( 'recipe-carousels.php', $data );
							}
							wp_reset_postdata();
						}
						?>
					</div>
				</div>
				<div class="dr-recipe_carousel-navigation <?php echo esc_attr( $nav_class ); ?>">
					<div id="dr_swiper-next-<?php echo esc_attr( $id ); ?>" class="swiper-button-next dr_swiper-next"></div>
					<div id="dr_swiper-prev-<?php echo esc_attr( $id ); ?>" class="swiper-button-prev dr_swiper-prev"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
