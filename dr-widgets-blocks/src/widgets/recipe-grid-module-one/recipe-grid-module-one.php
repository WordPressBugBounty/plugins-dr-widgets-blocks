<?php
/**
 * Recipe Grid Module One
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;
use \Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Grid_Module_One extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-grid-module-one';

	public function get_title() {
		return esc_html__( 'Recipe Grid Module 1', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-grid-module';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-grid-module', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeGridModule.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-grid-module',
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
					'layout-4' => esc_html__( 'Layout 4', 'dr-widgets-blocks' ),
					'layout-5' => esc_html__( 'Layout 5', 'dr-widgets-blocks' ),
				),
				'default' => 'layout-1',
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
					'{{WRAPPER}} .dr-grid' => '--column-gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-grid' => '--row-gap: {{SIZE}}{{UNIT}};',
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
			'showWishlist',
			array(
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Bookmark', 'dr-widgets-blocks' ),
				'label_on'     => esc_html__( 'Show', 'dr-widgets-blocks' ),
				'label_off'    => esc_html__( 'Hide', 'dr-widgets-blocks' ),
				'return_value' => 'yes',
				'default'      => '',
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
				'condition'   => array(
					'layout' => 'layout-3',
				),
			)
		);
		$this->add_control(
			'excerptLength',
			array(
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'Excerpt Length', 'dr-widgets-blocks' ),
				'placeholder' => '30',
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
				'default'     => 30,
				'condition'   => array(
					'showExcerpt' => 'yes',
					'layout'      => 'layout-3',
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
					'{{WRAPPER}} .dr-grid__recipe' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .dr-grid--layout-3 .dr-widgetBlock_content-wrap, {{WRAPPER}} .dr-grid__recipe--regular .dr-widgetBlock_content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-widgetBlock_content-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-grid__recipe',
			)
		);
		$this->add_control(
			'borderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-grid__recipe--regular' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'boxShadow',
				'label'    => esc_html__( 'Box Shadow', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-grid__recipe',
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
					'{{WRAPPER}} .dr-grid__recipe , {{WRAPPER}} .dr-widget .dr-widgetBlock_content-wrap' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .dr-grid__recipe .dr_meta-items' => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'layout!' => 'layout-5' ,
				),
			)
		);
		$this->end_controls_section();

		/**
         * Recipe Hero Posts Style Section
         */
        $this->start_controls_section(
            'hero_post_style_section',
            array(
                'label' => esc_html__( 'Hero Post', 'dr-widgets-blocks' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'heroPostColor',
            array(
                'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .dr-widget .dr-grid__recipe--hero :is(.dr_title, .dr_recipe-cats a, .dr_meta-content .dr_meta-items)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dr-widget .dr-grid__recipe--hero .dr_meta-content .dr_meta-item:not(.dr_meta-review)::before' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'heroPostHoverColor',
            array(
                'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .dr-widget .dr-grid__recipe--hero :is(.dr_title, .dr_recipe-cats) a:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'heroPostTitleTypography',
                'label'    => esc_html__( 'Title Typography', 'dr-widgets-blocks' ),
                'selector' => '{{WRAPPER}} .dr-grid__recipe--hero .dr_title',
            )
        );

		$this->add_control(
			'heroImageSize',
			array(
				'label'   => esc_html__( 'Image Size', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => dr_widgets_blocks_get_image_size_options(),
				'default' => 'large',
			)
		);
		$this->add_control(
			'heroImageCustomSize',
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
			'heroImageWidth',
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
					'{{WRAPPER}} .dr-grid__recipe--hero .dr-widgetBlock_fig-wrap img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'heroImageHeight',
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
					'{{WRAPPER}} .dr-grid__recipe--hero .dr-widgetBlock_fig-wrap img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heroImageScale',
			array(
				'label'     => esc_html__( 'Image Scale', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'initial' => esc_html__( 'initial', 'dr-widgets-blocks' ),
					'contain' => esc_html__( 'Contain', 'dr-widgets-blocks' ),
					'cover'   => esc_html__( 'Cover', 'dr-widgets-blocks' ),
					'fill'    => esc_html__( 'Fill', 'dr-widgets-blocks' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-grid__recipe--hero .dr-widgetBlock_fig-wrap img' => 'object-fit: {{VALUE}};',
				),
				'default'   => 'cover',
			)
		);

		$this->add_control(
			'heroImageBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-grid__recipe--hero, {{WRAPPER}} .dr-grid__recipe--hero .dr-widgetBlock_fig-wrap, {{WRAPPER}} .dr-widgetBlock_fig-wrap::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'separatorColor',
			array(
				'label'     => esc_html__( 'Seperator Color', 'dr-widgets-blocks' ),
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
			)
		);
		$this->add_control(
			'imageSize',
			array(
				'label'   => esc_html__( 'Image Size', 'dr-widgets-blocks' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => dr_widgets_blocks_get_image_size_options(),
				'default' => 'delrecpe-structured-data-4_3',
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
					'{{WRAPPER}} .dr-grid__recipe--regular .dr-widgetBlock_fig-wrap img' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-grid__recipe--regular .dr-widgetBlock_fig-wrap img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'imageScale',
			array(
				'label'     => esc_html__( 'Image Scale', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'initial' => esc_html__( 'initial', 'dr-widgets-blocks' ),
					'contain' => esc_html__( 'Contain', 'dr-widgets-blocks' ),
					'cover'   => esc_html__( 'Cover', 'dr-widgets-blocks' ),
					'fill'    => esc_html__( 'Fill', 'dr-widgets-blocks' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .dr-grid__recipe--regular .dr-widgetBlock_fig-wrap img' => 'object-fit: {{VALUE}};',
				),
				'default'   => 'cover',
			)
		);
		$this->add_control(
			'imageBorderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-grid__recipe--regular .dr-widgetBlock_fig-wrap, {{WRAPPER}} .dr-widgetBlock_fig-wrap::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			)
		);
		$this->add_control(
			'titleColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-grid__recipe--regular .dr_title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'titleHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-grid__recipe--regular .dr_title a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .dr-grid__recipe--regular .dr_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'titleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-grid__recipe--regular .dr_title',
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
					'layout'      => 'layout-3',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerptTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_excerpt',
			)
		);
		$this->add_control(
			'excerptColor',
			array(
				'label'     => esc_html__( 'Text Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_excerpt' => 'color: {{VALUE}};',
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
						'step' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-keys .dr_recipe-key' => 'font-size: {{SIZE}}{{UNIT}};',
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
						'step' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widget .dr_recipe-keys .dr_recipe-keys-container' => '--dr_keys-spacing: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .dr-grid__recipe--regular .dr_meta-content .dr_meta-item' => 'color: {{VALUE}};',
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
						'step' => 5,
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
						'step' => 5,
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
		$settings   = $this->get_settings_for_display();
		$recipe_ids = isset( $settings['exclude'] ) ? $settings['exclude'] : false;
		$order_by   = isset( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order      = isset( $settings['order'] ) ? $settings['order'] : 'DESC';
		$offset     = isset( $settings['offset'] ) ? $settings['offset'] : 0;

		$layout         = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';
		if( $layout === 'layout-1' ){
			$posts_per_page = 4;
		}elseif( $layout === 'layout-2' ){
			$posts_per_page = 7;
		}else{
			$posts_per_page = 5;
		}
		
		if(isset( $settings['filterBy'] )){
			$orderby = $settings['filterBy'] === 'rand' ? 'rand' : ($settings['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		$args = array(
			'posts_per_page'   => $posts_per_page,
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
					<?php esc_html_e( 'Please check the widget settings and make sure you have selected a valid query.', 'dr-widgets-blocks' ); ?>
				</p>
			<?php
			return;
		}

		$image_size          = isset( $settings['imageSize'] ) ? $settings['imageSize'] : 'delrecpe-structured-data-4_3';
		$image_custom_size   = isset( $settings['imageCustomSize'] ) ? $settings['imageCustomSize'] : false;
		$image_size          = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$hero_image_size          = isset( $settings['heroImageSize'] ) ? $settings['heroImageSize'] : 'large';
		$hero_image_custom_size   = isset( $settings['heroImageCustomSize'] ) ? $settings['heroImageCustomSize'] : false;
		$hero_image_size          = 'custom' === $hero_image_size && $hero_image_custom_size ? dr_widgets_blocks_get_custom_image_size( $hero_image_custom_size ) : $hero_image_size;
		$title_tag           = isset( $settings['headingTag'] ) ? $settings['headingTag'] : 'h3';
		$show_total_time     = isset( $settings['showTotalTime'] ) && 'yes' === $settings['showTotalTime'] ? true : false;
		$show_difficulty     = isset( $settings['showDifficulty'] ) && 'yes' === $settings['showDifficulty'] ? true : false;
		$show_recipe_keys    = isset( $settings['showRecipeKeys'] ) && 'yes' === $settings['showRecipeKeys'] ? true : false;
		$show_rating         = isset( $settings['showRating'] ) && 'yes' === $settings['showRating'] ? true : false;
		$show_comment        = isset( $settings['showComment'] ) && 'yes' === $settings['showComment'] ? true : false;
		$show_category       = isset( $settings['showCategory'] ) && 'yes' === $settings['showCategory'] ? true : false;
		$show_wishlist       = isset( $settings['showWishlist'] ) && 'yes' === $settings['showWishlist'] ? true : false;
		$separator           = isset( $settings['separator'] ) ? $settings['separator'] : 'dot';
		$show_excerpt        = isset( $settings['showExcerpt'] ) && 'yes' === $settings['showExcerpt'] ? true : false;
		$excerpt_length      = isset( $settings['excerptLength'] ) ? $settings['excerptLength'] : 20;
		?>

		<?php
			$layout_number = preg_replace('/\D/', '', $layout); // Remove all non-digit characters from layout
		?>

		<div class="dr-widget">
			<div class="dr-grid dr-grid--layout-<?php echo esc_attr($layout_number); ?>">
				<?php
				$i = 0; 
				if ( $recipes_query->have_posts() ) {
					while ( $recipes_query->have_posts() ) {
						$recipes_query->the_post();
						$recipe       = get_post( get_the_ID() );
						$recipe_metas = delicious_recipes_get_recipe( $recipe );
						$data         = array(
							'settings'     => array(
								'image_size'          => $image_size,
								'hero_image_size'     => $hero_image_size,
								'title_tag'           => $title_tag,
								'show_total_time'     => $show_total_time,
								'show_difficulty'     => $show_difficulty,
								'show_recipe_keys'    => $show_recipe_keys,
								'show_rating'         => $show_rating,
								'show_comment'        => $show_comment,
								'show_category'       => $show_category,
								'show_wishlist'       => $show_wishlist,
								'separator'           => $separator,
								'layout'              => $layout,
								'show_excerpt'        => $show_excerpt,
								'excerpt_length'      => $excerpt_length,
							),
							'recipe_metas' => $recipe_metas,
							'count' => $i,
							'post_count'  => $recipes_query->post_count,
						);
						dr_widgets_blocks_get_template( 'recipes-grid-module-one.php', $data );
						$i++;
					}
					wp_reset_postdata();
				}
				?>
			</div>
		</div>
		<?php
	}
}