<?php
/**
 * Recipe Categories Widget
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;
use \Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Widget_Recipe_Categories extends Widget {
	/**
	 * @var $widget_name
	 */
	public $widget_name = 'dr-recipe-categories';

	public function get_title() {
		return esc_html__( 'Recipe Categories 1', 'dr-widgets-blocks' );
	}

	public function get_icon() {
		return 'icon-recipe-categories';
	}

	public function get_style_depends()	{
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-categories', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeCategories.css' );

		return array(
			'dr-widgetsBlocks-layouts',
			'dr-widgetsBlocks-recipe-categories'
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
				'max'             => 4,
				'step'            => 1,
				'default'         => 3,
				'devices'         => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
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

		$list_all_tax= [];
		foreach ( $taxonomies as $tax => $label ) {
			$this->add_control(
				"{$tax}_term_id",
				array(
					'type'      => 'drwb-sortable-select2',
					'label'     => esc_html__( 'Terms', 'dr-widgets-blocks' ),
					'multiple'  => true,
					'options'   => wp_list_pluck( get_terms( $tax ), 'name', 'term_id' ),
					'condition' => array(
						'taxonomy'      => $tax,
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
				'default'      => 'yes',
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
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-category',
			)
		);
		$this->add_control(
			'borderRadius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dr-widgets-blocks' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'boxShadow',
				'label'    => esc_html__( 'Box Shadow', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-category',
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
				'selectors' => array(
					'{{WRAPPER}} .dr-widget .dr-widgetBlock_content-wrap' => 'justify-content: {{VALUE}};',
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
					'{{WRAPPER}} .dr_cat-title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'titleHoverColor',
			array(
				'label'     => esc_html__( 'Hover Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr_cat-title a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'titleTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr_cat-title',
			)
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-category .dr_recipe-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'countColor',
			array(
				'label'     => esc_html__( 'Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category .dr_recipe-count' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'countBgColor',
			array(
				'label'     => esc_html__( 'Background Color', 'dr-widgets-blocks' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dr-widgetBlock_recipe-category .dr_recipe-count' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .dr-widgetBlock_recipe-category .dr_recipe-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'countTypography',
				'label'    => esc_html__( 'Typography', 'dr-widgets-blocks' ),
				'selector' => '{{WRAPPER}} .dr-widgetBlock_recipe-category .dr_recipe-count',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$taxonomy     = isset( $settings['taxonomy'] ) && '' !== $settings['taxonomy'] ? $settings['taxonomy'] : 'recipe-course';
		$terms        = $taxonomy ? ( isset( $settings[ "{$taxonomy}_term_id" ] ) ? $settings[ "{$taxonomy}_term_id" ] : '' ) : '';
		$hide_empty   = isset( $settings['hideEmpty'] ) && 'yes' === $settings['hideEmpty'] ? true : false;
		$all_taxonomy = isset($settings['all_taxonomy']) && 'yes' === $settings['all_taxonomy'] ? true : false;
		$all_term_id  = isset($settings['all_term_id']) ? $settings['all_term_id'] : [];
		$list_term_ids = \array_map(function ($term) {
			return (int)\substr(\strrchr($term, '_'), 1);
		}, $all_term_id);

		$categories = [];

		if(!$all_taxonomy){
			$categories = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'include'    => $terms,
					'hide_empty' => $hide_empty,
					'count'      => true,
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

		$layout              = isset( $settings['layout'] ) ? $settings['layout'] : 'layout-1';
		$per_row             = isset( $settings['categoriesPerRow'] ) ? absint( $settings['categoriesPerRow'] ) : 3;
		$per_row_tablet      = isset( $settings['categoriesPerRow_tablet'] ) ? absint( $settings['categoriesPerRow_tablet'] ) : 2;
		$per_row_mobile      = isset( $settings['categoriesPerRow_mobile'] ) ? absint( $settings['categoriesPerRow_mobile'] ) : 1;
		$show_category_image = isset( $settings['showCategoryImage'] ) && 'yes' === $settings['showCategoryImage'] ? true : false;
		$show_category_count = isset( $settings['showCategoryCount'] ) && 'yes' === $settings['showCategoryCount'] ? true : false;
		$show_category_name  = isset( $settings['showCategoryName'] ) && 'yes' === $settings['showCategoryName'] ? true : false;
		$category_tag        = isset( $settings['categoryTag'] ) ? $settings['categoryTag'] : 'h3';
		$bgcolor_from        = isset( $settings['bgColorFrom'] ) ? $settings['bgColorFrom'] : 'default';
		$background_color    = isset( $settings['backgroundColor'] ) ? $settings['backgroundColor'] : '';

		?>
			<div class="dr-widget">
				<div class="dr-widgetBlock_row dr_columns-<?php echo esc_attr( $per_row ); ?>-lg dr_columns-<?php echo esc_attr( $per_row_tablet ); ?>-md dr_columns-<?php echo esc_attr( $per_row_mobile ); ?>">
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
							),
						);
						dr_widgets_blocks_get_template( 'recipe-categories.php', $data );
					}
					?>
				</div>
			</div>
		<?php
	}
}
