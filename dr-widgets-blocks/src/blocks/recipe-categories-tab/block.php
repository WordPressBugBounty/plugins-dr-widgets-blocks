<?php
/**
 * Recipe Categories Block Template
 *
 * @package DR_Widgets_Blocks
 *
 * @since 1.0.0
 *
 * @param array $attributes
 * @param array $content
 * @param string $context
 */

if ( ! isset( $attributes ) ) {
	return;
}

$heading_title   = isset( $attributes['headingTitle'] ) ? $attributes['headingTitle'] : __( 'Recipes', 'dr-widgets-blocks' );
$heading_tag     = isset( $attributes['headingTitleTag'] ) ? $attributes['headingTitleTag'] : 'h2';
$show_title      = isset( $attributes['showTitle'] ) && $attributes['showTitle'] ? true : false;
$title_tag       = isset( $attributes['headingTag'] ) ? $attributes['headingTag'] : 'h3';
$show_total_time = isset( $attributes['showTotalTime'] ) && $attributes['showTotalTime'] ? true : false;
$show_difficulty = isset( $attributes['showDifficulty'] ) && $attributes['showDifficulty'] ? true : false;

$layout       = isset( $attributes['layout'] ) ? $attributes['layout'] : 'layout-1';
$layout_class = 'layout-1' === $layout ? 'l1rp' : ( 'layout-2' === $layout ? 'l2rp' : 'l3rp' );
$per_slide    = 'layout-1' === $layout ? 3 : 4;
$per_page     = isset( $attributes['postsPerPage'] ) ? $attributes['postsPerPage'] : 12;
$recipe_ids   = isset( $attributes['exclude'] ) ? $attributes['exclude'] : false;
$order_by     = isset( $attributes['orderby'] ) ? $attributes['orderby'] : 'date';
$order        = isset( $attributes['order'] ) ? $attributes['order'] : 'DESC';
$offset       = isset( $attributes['offset'] ) ? $attributes['offset'] : 0;
$taxonomy     = isset( $attributes['taxonomy'] ) && '' !== $attributes['taxonomy'] ? $attributes['taxonomy'] : 'recipe-course';
$terms        = $taxonomy ? ( isset( $attributes['terms'] ) ? $attributes['terms'] : '' ) : '';

$term_ids = array();
if ( $taxonomy && $terms ) {
	foreach ( $terms as $term ) {
		$term_id = get_term_by( 'term_id', $term, $taxonomy )->term_id;
		if ( $term_id ) {
			$term_ids[] = $term_id;
		}
	}
}

$categories = get_terms(
	array(
		'taxonomy'   => $taxonomy,
		'include'    => $term_ids,
		'hide_empty' => true,
		'orderby'    => 'include',
	)
);

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

if ( $order_by === 'meta_value_num' ) {
	$args['meta_key'] = '_delicious_recipes_view_count';
}
?>
<div id="drwb-recipe-categories-tab-style-<?php echo esc_attr( $attributes['block_id'] ); ?>" class="drwb-recipe-categories-tab-wrapper">
	<div class="dr-widget">
		<div class="dr-widgetBlock_row dr_columns-1">
			<div class="dr_column">
			
				<?php
					$data = array(
						'categories'      => $categories,
						'args'            => $args,
						'per_slide'       => $per_slide,
						'show_title'      => $show_title,
						'title_tag'       => $title_tag,
						'show_total_time' => $show_total_time,
						'show_difficulty' => $show_difficulty,
						'heading_title'   => $heading_title,
						'heading_tag'     => $heading_tag,
						'layout_class'    => $layout_class,
						'block_id'        => $attributes['block_id'],
						'all_taxonomy'    => false,
					);

					dr_widgets_blocks_get_template( 'recipe-categories-tab-nav.php', $data );

					foreach ( $categories as $category ) {
						$args['tax_query'] = array(
							array(
								'taxonomy' => $taxonomy,
								'terms'    => $category->term_id,
								'field'    => 'term_id',
							),
						);

						$recipes_query = new \WP_Query( $args );

						$i = 1;
						if ( $recipes_query->have_posts() ) {
							?>
							<div id="dr_tab-content-<?php echo esc_attr( $category->term_id ); ?>-<?php echo esc_attr( $attributes['block_id'] ); ?>" class="dr_tab-content">
								<div class="dr_recipe-slider swiper swiper-container" data-id="<?php echo esc_attr( $attributes['block_id'] ); ?>">
									<div class="swiper-wrapper">
										
										<?php
										while ( $recipes_query->have_posts() ) {
											$recipes_query->the_post();
											$recipe       = get_post( get_the_ID() );
											$recipe_metas = delicious_recipes_get_recipe( $recipe );
											$data         = array(
												'settings' => array(
													'show_title'         => $show_title,
													'title_tag'          => $title_tag,
													'show_total_time'    => $show_total_time,
													'show_difficulty'    => $show_difficulty,
												),
												'recipe_metas' => $recipe_metas,
												'per_slide' => $per_slide,
												'i'        => $i,
												'count'    => $recipes_query->post_count,
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
</div>
<?php
