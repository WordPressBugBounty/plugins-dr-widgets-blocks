<?php
/**
 * Recipe Post List 1 Widget
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Post_List_One extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-post-list-one';

	public function get_title() {
		return esc_html__( 'Recipe Post List 1', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-post-list';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-post-list-1', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipePostsListOne.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-post-list-1',
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
				),
				'default' => 'layout-1',
			)
		);
		$this->add_responsive_control( 
			'numOfCol',
			array(
				'type'            => \Elementor\Controls_Manager::NUMBER,
				'label'           => esc_html__( 'No. of Columns', 'dr-widgets-blocks' ),
				'min'             => 1,
				'max'             => 4,
				'step'            => 1,
				'default'         => 1,
				'devices'         => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 1,
				'tablet_default'  => 1,
				'mobile_default'  => 1,
				'condition' => array(
					'layout' => 'layout-1',
				),
			)
		);
		$this->add_responsive_control( 
			'numOfCol2',
			array(
				'type'            => \Elementor\Controls_Manager::NUMBER,
				'label'           => esc_html__( 'No. of Columns', 'dr-widgets-blocks' ),
				'min'             => 1,
				'max'             => 2,
				'step'            => 1,
				'default'         => 1,
				'devices'         => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 1,
				'tablet_default'  => 1,
				'mobile_default'  => 1,
				'condition' => array(
					'layout' => 'layout-2',
				),
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
				'label'       => esc_html__( 'No. of Recipes', 'dr-widgets-blocks' ),
				'placeholder' => '0',
				'min'         => 1,
				'max'         => 30,
				'step'        => 1,
				'default'     => 4,
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
				'condition' => array(
					'layout' => 'layout-2',
				),
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
					'layout' => 'layout-2',
				),
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
					'layout'      => 'layout-2',
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
				'default'      => '',
				'condition' => array(
					'layout' => 'layout-2',
				),
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
				'condition' => array(
					'layout' => 'layout-2',
				),
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
					'{{WRAPPER}} .dr-widget .dr-widgetBlock_content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default' => [
                    'size' => 15,
                ],
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
					'{{WRAPPER}} .dr-widgetBlock_content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .l2rp .dr-widgetBlock_content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'boxShadow',
				'label'    => esc_html__( 'Box Shadow', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-post.l1rp .dr-widgetBlock_fig-wrap',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Posts Image Style Section
		 */
		$this->start_controls_section(
			'image_style_section',
			array(
				'label'     => esc_html__( 'Featured Image', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'imageSize',
			array(
				'label'   => esc_html__( 'Image Size', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => dr_widgets_blocks_get_image_size_options(),
				'default' => 'thumbnail',
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
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr-widgetBlock_fig-wrap img' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-widget .dr-widgetBlock_fig-wrap img' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-widget .dr-widgetBlock_fig-wrap img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'right'  => esc_html__( 'Right', 'dr-widgets-blocks' ),
				),
				'default'   => 'left',
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
					'{{WRAPPER}} .dr-widget .dr-widgetBlock_fig-wrap img' => 'object-fit: {{VALUE}};',
				),
				'default'   => 'cover',
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
			)
		);
		$this->add_control(
			'titleColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'titleHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_title a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'titleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widget .dr_title',
			)
		);
		$this->add_responsive_control(
			'titleBottomSpacing',
			array(
				'label'      => esc_html__( 'Bottom Spacing', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 12,
						'max'  => 100,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_title' => '--bottom-spacing: {{SIZE}}{{UNIT}};',
				),
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
					'layout' 	  => 'layout-2',
				),
			)
		);
		$this->add_control(
			'excerptColor',
			array(
				'label'     => esc_html__( 'Text Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap .dr_recipe-content' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerptTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_content-wrap .dr_recipe-content',
			)
		);
		$this->add_responsive_control(
			'excerptBottomSpacing',
			array(
				'label'      => esc_html__( 'Bottom Spacing', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap .dr_recipe-content' => '--bottom-spacing: {{SIZE}}{{UNIT}};',
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
					'layout' => 'layout-2',
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
						'min'  => 10,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget  .dr_recipe-keys .dr_recipe-keys-container a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'recipeKeysIconSpace',
			array(
				'label'      => esc_html__( 'Item Spacing', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 10,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .layout-2 .dr-post-list__recipe .dr_recipe-keys .dr_recipe-keys-container a' => '--dr_key-spacing: {{SIZE}}{{UNIT}};',
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
						'step' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-meta-content .dr_recipe-meta-item::before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'metaIconColor',
			array(
				'label'     => esc_html__( 'Icon Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-meta-content .dr_recipe-meta-item:not(.dr_meta-review)::before' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'metaTextColor',
			array(
				'label'     => esc_html__( 'Text Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-meta-content .dr_recipe-meta-item' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'metaIconSpacing',
			array(
				'label'      => esc_html__( 'Item Spacing', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-meta-content .dr_recipe-meta-item' => '--dr_item-spacing: {{SIZE}}{{UNIT}};',
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
					'layout' => 'layout-2',
				),
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
			'categoryHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'categorySpaceBetween',
			array(
				'label'      => esc_html__( 'Item Spacing', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats' => '--dr_item-spacing: {{SIZE}}{{UNIT}};',
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
			'separatorColor',
			array(
				'label'     => esc_html__( 'Separator Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats a::after' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'catBottomSpacing',
			array(
				'label'      => esc_html__( 'Bottom Spacing', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-cats' => '--bottom-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
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
			'post_type'        => \DELICIOUS_RECIPE_POST_TYPE,
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
					<?php esc_html_e( 'Please check the widget settings and make sure you have selected a valid query.', 'dr-widgets-blocks' ); ?>
				</p>
			<?php
			return;
		}

		$col_lay_1        = isset( $settings['numOfCol'] ) ? absint( $settings['numOfCol'] ) : 1;
		$col_lay_1_tablet = isset( $settings['numOfCol_tablet'] ) ? absint( $settings['numOfCol_tablet'] ) : 1;
		$col_lay_1_mobile = isset( $settings['numOfCol_mobile'] ) ? absint( $settings['numOfCol_mobile'] ) : 1;
		$col_lay_2        = isset( $settings['numOfCol2'] ) ? absint( $settings['numOfCol2'] ) : 1;
		$col_lay_2_tablet = isset( $settings['numOfCol2_tablet'] ) ? absint( $settings['numOfCol2_tablet'] ) : 1;
		$col_lay_2_mobile = isset( $settings['numOfCol2_mobile'] ) ? absint( $settings['numOfCol2_mobile'] ) : 1;
		$layout_data      = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';

		if( $layout_data === 'layout-2' ){
			$per_row        = $col_lay_2;
			$per_row_tablet = $col_lay_2_tablet;
			$per_row_mobile = $col_lay_2_mobile;
		} else {
			$per_row        = $col_lay_1;
			$per_row_tablet = $col_lay_1_tablet;
			$per_row_mobile = $col_lay_1_mobile;
		}

		$image_size          = isset( $settings['imageSize'] ) ? $settings['imageSize'] : 'thumbnail';
		$image_custom_size   = isset( $settings['imageCustomSize'] ) ? $settings['imageCustomSize'] : false;
		$image_size          = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$show_title          = isset( $settings['showTitle'] ) && 'yes' === $settings['showTitle'] ? true : false;
		$title_tag           = isset( $settings['headingTag'] ) ? $settings['headingTag'] : 'h3';
		$show_total_time     = isset( $settings['showTotalTime'] ) && 'yes' === $settings['showTotalTime'] ? true : false;
		$show_difficulty     = isset( $settings['showDifficulty'] ) && 'yes' === $settings['showDifficulty'] ? true : false;
		$show_recipe_keys    = isset( $settings['showRecipeKeys'] ) && 'yes' === $settings['showRecipeKeys'] ? true : false;
		$show_excerpt        = isset( $settings['showExcerpt'] ) && 'yes' === $settings['showExcerpt'] ? true : false;
		$show_category       = isset( $settings['showCategory'] ) && 'yes' === $settings['showCategory'] ? true : false;
		$show_rating         = isset( $settings['showRating'] ) && 'yes' === $settings['showRating'] ? true : false;
		$excerpt_length      = isset( $settings['excerptLength'] ) ? $settings['excerptLength'] : 20;
		$image_alignment     = isset( $settings['imageAlignment'] ) ? $settings['imageAlignment'] : 'left';
		$separator           = isset( $settings['separator'] ) ? $settings['separator'] : 'dot';
		$show_wishlist       = isset( $settings['showBookmark'] ) && 'yes' === $settings['showBookmark'] ? true : false;
		
		$this->add_render_attribute( 
			'wrapper-classes', 
			'class', 
			[
				'dr-widgetBlock_row',
				'dr-post-list',
				esc_attr( $layout_data ),
				'dr_columns-' . esc_attr( $per_row ) . '-lg',
				'dr_columns-' . esc_attr( $per_row_tablet ) . '-md',
				'dr_columns-' . esc_attr( $per_row_mobile ),
			] 
		);
		?>
		<div class="dr-widget">
			<div <?php $this->print_render_attribute_string( 'wrapper-classes' ); ?>>
				<?php
				if ( $recipes_query->have_posts() ) {
					while ( $recipes_query->have_posts() ) {
						$recipes_query->the_post();
						$recipe       = get_post( get_the_ID() );
						$recipe_metas = delicious_recipes_get_recipe( $recipe );
						$data         = array(
							'settings'     => array(
								'image_size'          => $image_size,
								'show_title'          => $show_title,
								'title_tag'           => $title_tag,
								'show_total_time'     => $show_total_time,
								'show_difficulty'     => $show_difficulty,
								'show_recipe_keys'    => $show_recipe_keys,
								'show_excerpt'        => $show_excerpt,
								'show_category'       => $show_category,
								'show_rating'         => $show_rating,
								'excerpt_length'      => $excerpt_length,
								'image_alignment'     => $image_alignment,
								'separator'           => $separator,
								'layout'			  => $layout_data,
								'show_wishlist'       => $show_wishlist,
							),
							'recipe_metas' => $recipe_metas,
						);
						dr_widgets_blocks_get_template( 'recipe-post-list-one.php', $data );
					}
					wp_reset_postdata();
				}
				?>
			</div>
		</div>
		<?php
	}
}
