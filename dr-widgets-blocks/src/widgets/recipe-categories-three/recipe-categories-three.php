<?php

/**
 * Recipe Categories Two Widget
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

 namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;
use \Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Categories_Three extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-categories-three';

	public function get_title() {
		return esc_html__( 'Recipe Categories 3', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-categories';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-category-three', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeCategoryThree.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-category-three',
		);
	}

	protected function register_controls() {

		/**
		 * Recipe Categories Layouts Section
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
			'categoriesPerRow',
			array(
				'type'            => \Elementor\Controls_Manager::NUMBER,
				'label'           => esc_html__( 'Categories per Row', 'dr-widgets-blocks' ),
				'min'             => 1,
				'max'             => 6,
				'step'            => 1,
				'default'         => 6,
				'devices'         => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 6,
				'tablet_default'  => 3,
				'mobile_default'  => 2,
				'condition' => array(
					'layout!' => 'layout-3',
				),
			)
		);

		$this->add_responsive_control(
			'categoriesPerRowThree',
			array(
				'type'            => \Elementor\Controls_Manager::NUMBER,
				'label'           => esc_html__( 'Categories per Row', 'dr-widgets-blocks' ),
				'min'             => 1,
				'max'             => 4,
				'step'            => 1,
				'default'         => 4,
				'devices'         => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 4,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'condition' => array(
					'layout' => 'layout-3',
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
		 * Recipe Categories Query Section
		 */
		$this->start_controls_section(
			'query_section',
			array(
				'label' => esc_html__( 'Query', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
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
			'hideEmpty',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Hide Empty', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Yes', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'No', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Additional Section
		 */
		$this->start_controls_section(
			'additional_section',
			array(
				'label' => esc_html__( 'Additional', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'showCategoryImage',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Category Image', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'showCategoryName',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Category Name', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'categoryTag',
			array(
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Category Tag', 'dr-widgets-blocks' ),
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
					'showCategoryName' => 'yes',
				),
			)
		);
		$this->add_control(
			'showCategoryCount',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Category Count', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Content Style Section
		 */
		$this->start_controls_section(
			'content_style_section',
			array(
				'label' => esc_html__( 'Content', 'dr-widgets-blocks' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'bgColorFrom',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Background', 'dr-widgets-blocks' ),
				'options' => array(
					'default' => esc_html__( 'Default', 'dr-widgets-blocks' ),
					'custom'  => esc_html__( 'Custom', 'dr-widgets-blocks' ),
				),
				'default' => 'default',
			)
		);
		$this->add_control(
			'backgroundColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => array(
					'bgColorFrom' => 'custom',
				),
			)
		);
		$this->add_responsive_control(
			'titlePadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr-widgetBlock_content-wrap .dr_cat-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'Margin', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition' => array(
					'layout' => 'layout-1',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr-widgetBlock_content-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr_cat-title',
			)
		);
		$this->add_control(
			'borderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category-three ' => '--border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'boxShadow',
				'label'    => esc_html__( 'Box Shadow', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr_cat-title',
			)
		);
		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'dr-widgets-blocks' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'condition' => array(
					'layout!' => 'layout-3',
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr-widgetBlock_content-wrap' => 'justify-self: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Title Style Section
		 */
		$this->start_controls_section(
			'title_style_section',
			array(
				'label'     => esc_html__( 'Category Name', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showCategoryName' => 'yes',
				),
			)
		);
		$this->add_control(
			'titleColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap .dr_cat-title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'titleHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_content-wrap .dr_cat-title a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'titleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_content-wrap .dr_cat-title',
			)
		);
		$this->end_controls_section();

		/**
		 * Recipe Categories Image Style Section
		 */

		$this->start_controls_section(
			'categories_image_style',
			array(
				'label'     => esc_html__( 'Image Settings', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showCategoryImage' => 'yes',
				),
			)
		);

		$this->add_control(
			'categories_image_size',
			array(
				'label'   => esc_html__( 'Image Size', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => dr_widgets_blocks_get_image_size_options(),
				'default' => 'delrecpe-structured-data-1_1',
			)
		);

		$this->add_control(
			'categories_image_custom_size',
			array(
				'label'       => esc_html__( 'Custom Image Size', 'dr-widgets-blocks' ),
				'type'        => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'dr-widgets-blocks' ),
				'condition'   => array(
					'categories_image_size' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'categories_image_width',
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
					'{{WRAPPER}} .recipe-category-three .dr-widgetBlock_recipe-category-three .dr-widgetBlock_fig-wrap img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

        $this->add_responsive_control(
			'categories_image_height',
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
					'{{WRAPPER}} .recipe-category-three .dr-widgetBlock_recipe-category-three .dr-widgetBlock_fig-wrap img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
            'categories_image_object_fit',
            [
                'label' => esc_html__('Object Fit', 'dr-widgets-blocks'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'initial' => esc_html__('Default', 'dr-widgets-blocks'),
                    'fill'    => esc_html__('Fill', 'dr-widgets-blocks'),
                    'cover'   => esc_html__('Cover', 'dr-widgets-blocks'),
                    'contain' => esc_html__('Contain', 'dr-widgets-blocks'),
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .recipe-category-three .dr-widgetBlock_recipe-category-three .dr-widgetBlock_fig-wrap img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );


		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'categories_image_border',
                'selector'  => '{{WRAPPER}} .recipe-category-three .dr-widgetBlock_recipe-category-three .dr-widgetBlock_fig-wrap',
            ]
        );

        $this->add_responsive_control(
            'categories_image_border_radius',
            [
                'label'     => __( 'Border Radius', 'dr-widgets-blocks' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .recipe-category-three .dr-widgetBlock_recipe-category-three .dr-widgetBlock_fig-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'categories_image_box_shadow',
                'selector'  => '{{WRAPPER}} .dr-widgetBlock_fig-wrap',
            ]
        );

      
		$this->end_controls_section();

		/**
		 * Recipe Categories Count Style Section
		 */
		$this->start_controls_section(
			'count_style_section',
			array(
				'label'     => esc_html__( 'Count', 'dr-widgets-blocks' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'showCategoryCount' => 'yes',
				),
			)
		);
		$this->add_control(
			'countPadding',
			array(
				'label'      => esc_html__( 'Padding', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .recipe-category-three .dr-widgetBlock_recipe-category-three.l3rc .dr_recipe-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'countColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr_recipe-count' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'countBgColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr_recipe-count' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'countBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr_recipe-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'countTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-category-three .dr_recipe-count',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$taxonomy   = isset( $settings['taxonomy'] ) && '' !== $settings['taxonomy'] ? $settings['taxonomy'] : 'recipe-course';
		$terms      = $taxonomy ? ( isset( $settings[ "{$taxonomy}_term_id" ] ) ? $settings[ "{$taxonomy}_term_id" ] : '' ) : '';
		$hide_empty = isset( $settings['hideEmpty'] ) && 'yes' === $settings['hideEmpty'] ? true : false;
		$all_taxonomy = isset( $settings['all_taxonomy'] ) && 'yes' === $settings['all_taxonomy'] ? true : false;
		$all_term_id  = isset( $settings['all_term_id'] ) ? $settings['all_term_id'] : [];
		$list_term_ids = array_map(function($term) {
			return (int)substr(strrchr($term, '_'), 1);
		}, $all_term_id);

		// Normalize terms to ensure proper type handling
		$has_specific_terms = is_array( $terms ) ? ! empty( array_filter( $terms ) ) : ( ! empty( $terms ) && '' !== $terms );

		$categories = [];
		if(!$all_taxonomy){
			$term_args = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => $hide_empty,
				'count'      => true,
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

		$layout              = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';
		$per_row             = isset( $settings['categoriesPerRow'] ) ? absint( $settings['categoriesPerRow'] ) : 6;
		$per_row_tablet      = isset( $settings['categoriesPerRow_tablet'] ) ? absint( $settings['categoriesPerRow_tablet'] ) : 3;
		$per_row_mobile      = isset( $settings['categoriesPerRow_mobile'] ) ? absint( $settings['categoriesPerRow_mobile'] ) : 2;
		$per_row_three           = isset( $settings['categoriesPerRowThree'] ) ? absint( $settings['categoriesPerRowThree'] ) : 4;
		$per_row_tablet_three      = isset( $settings['categoriesPerRowThree_tablet'] ) ? absint( $settings['categoriesPerRowThree_tablet'] ) : 2;
		$per_row_mobile_three      = isset( $settings['categoriesPerRowThree_mobile'] ) ? absint( $settings['categoriesPerRowThree_mobile'] ) : 1;
		$show_category_image = isset( $settings['showCategoryImage'] ) && 'yes' === $settings['showCategoryImage'] ? true : false;
		$show_category_count = isset( $settings['showCategoryCount'] ) && 'yes' === $settings['showCategoryCount'] ? true : false;
		$show_category_name  = isset( $settings['showCategoryName'] ) && 'yes' === $settings['showCategoryName'] ? true : false;
		$category_tag        = isset( $settings['categoryTag'] ) ? $settings['categoryTag'] : 'h3';
		$bgcolor_from        = isset( $settings['bgColorFrom'] ) ? $settings['bgColorFrom'] : 'default';
		$background_color    = isset( $settings['backgroundColor'] ) ? $settings['backgroundColor'] : '';
		$categories_image_size          = isset( $settings['categories_image_size'] ) ? $settings['categories_image_size'] : 'delrecpe-structured-data-1_1';
		$categories_image_custom_size   = isset( $settings['categories_image_custom_size'] ) ? $settings['categories_image_custom_size'] : false;
		$categories_image_size          = 'custom' === $categories_image_size && $categories_image_custom_size ? dr_widgets_blocks_get_custom_image_size( $categories_image_custom_size ) : $categories_image_size;

		if($settings['layout'] ==='layout-3'){
			$per_row = $per_row_three;
			$per_row_tablet = $per_row_tablet_three;
			$per_row_mobile = $per_row_mobile_three;

		}else{
			$per_row = $per_row;
			$per_row_tablet = $per_row_tablet;
			$per_row_mobile = $per_row_mobile;
		}
	
		?>
			<div class="dr-widget">
				<div class="dr-widgetBlock_row recipe-category-three columns-<?php echo esc_attr( $per_row_tablet ); ?>-tb columns-<?php echo esc_attr( $per_row_mobile  ); ?>-mb columns-<?php echo esc_attr( $per_row ); ?>">
					<?php
					foreach ( $categories as $category ) {
						$tid               = $category->term_id;
						$name              = $category->name;
						$count             = $category->count;
						$link              = get_term_link( $category, $category->taxonomy );
						$dr_taxonomy_metas = get_term_meta( $tid, 'dr_taxonomy_metas', true );
						$tax_color         = isset( $dr_taxonomy_metas['taxonomy_color'] ) ? $dr_taxonomy_metas['taxonomy_color'] : '';
						$tax_color         = 'default' === $bgcolor_from ? $tax_color : $background_color;
						$tax_image         = isset( $dr_taxonomy_metas['taxonomy_image'] ) ? $dr_taxonomy_metas['taxonomy_image'] : false;
						$tax_svg           = isset( $dr_taxonomy_metas['taxonomy_svg'] ) ? $dr_taxonomy_metas['taxonomy_svg'] : '';
						$data              = array(
							'settings' => array(
								'layout'              => $layout,
								'tid'                 => $tid,
								'name'                => $name,
								'count'               => $count,
								'link'                => $link,
								'tax_color'           => $tax_color,
								'tax_image'           => $tax_image,
								'tax_svg'             => $tax_svg,
								'count'               => $count,
								'show_category_image' => $show_category_image,
								'show_category_count' => $show_category_count,
								'show_category_name'  => $show_category_name,
								'category_tag'        => $category_tag,
								'image_size'     	  => $categories_image_size,
							),
						);
						dr_widgets_blocks_get_template( 'recipe-categories-three.php', $data );
					}
					?>
				</div>
			</div>
		<?php
	}
}