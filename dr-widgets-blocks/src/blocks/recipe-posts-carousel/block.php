<?php
/**
 * Recipe Posts Carousel Block Template
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
$per_page   = isset( $attributes['postsPerPage'] ) ? $attributes['postsPerPage'] : 3;
$recipe_ids = isset( $attributes['exclude'] ) ? $attributes['exclude'] : false;
$order_by   = isset( $attributes['orderby'] ) ? $attributes['orderby'] : 'date';
$order      = isset( $attributes['order'] ) ? $attributes['order'] : 'DESC';
$offset     = isset( $attributes['offset'] ) ? $attributes['offset'] : 0;

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

$term_ids = array();
$taxonomy = isset( $attributes['taxonomy'] ) && '' != $attributes['taxonomy'] ? $attributes['taxonomy'] : false;
$terms    = $taxonomy ? ( isset( $attributes['terms'] ) ? $attributes['terms'] : false ) : false;

if ( $taxonomy && $terms ) {
	foreach ( $terms as $term ) {
		$term_id = get_term_by( 'term_id', $term, $taxonomy )->term_id;
		if ( $term_id ) {
			$term_ids[] = $term_id;
		}
	}
}

if ( $term_ids ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => $taxonomy,
			'terms'    => $term_ids,
			'field'    => 'term_id',
		),
	);
} elseif ( $taxonomy ) {
	$args['taxonomy'] = $taxonomy;
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

$layout           = isset( $attributes['layout'] ) ? $attributes['layout'] : 'layout-1';
$layout_class     = 'carousel_' . $layout;
$per_slide        = 'layout-1' === $layout ? 1 : ( isset( $attributes['recipesPerSlide']['desktop'] ) ? absint( $attributes['recipesPerSlide']['desktop'] ) : 4 );
$per_slide_tablet = 'layout-1' === $layout ? 1 : ( isset( $attributes['recipesPerSlide']['tablet'] ) ? absint( $attributes['recipesPerSlide']['tablet'] ) : 3 );
$per_slide_mobile = 'layout-1' === $layout ? 1 : ( isset( $attributes['recipesPerSlide']['mobile'] ) ? absint( $attributes['recipesPerSlide']['mobile'] ) : 1 );
$navigation       = isset( $attributes['showSliderArows'] ) && $attributes['showSliderArows'] ? true : false;
$nav_class        = $navigation ? '' : 'dr_swiper-navigation-hidden';
$item_gap         = 'layout-1' === $layout ? 0 : ( isset( $attributes['slideItemGap'] ) && '' !== $attributes['slideItemGap'] ? absint( $attributes['slideItemGap'] ) : 30 );
$swiper_options   = array(
	'slidesPerView' => $per_slide_mobile,
	'spaceBetween'  => $item_gap,
	'breakpoints'   => array(
		'768'  => array(
			'slidesPerView' => $per_slide_tablet,
		),
		'1025' => array(
			'slidesPerView' => $per_slide,
		),
	),
);

$show_feature_image = isset( $attributes['showFeatureImage'] ) && $attributes['showFeatureImage'] ? true : false;
$image_size         = isset( $attributes['imageSize'] ) ? $attributes['imageSize'] : 'recipe-archive-grid';
$image_custom_size  = isset( $attributes['imageCustomSize'] ) ? $attributes['imageCustomSize'] : false;
$image_size         = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
$show_title         = isset( $attributes['showTitle'] ) && $attributes['showTitle'] ? true : false;
$title_tag          = isset( $attributes['headingTag'] ) ? $attributes['headingTag'] : 'h3';
$show_total_time    = isset( $attributes['showTotalTime'] ) && $attributes['showTotalTime'] ? true : false;
$show_difficulty    = isset( $attributes['showDifficulty'] ) && $attributes['showDifficulty'] ? true : false;
$show_recipe_keys   = isset( $attributes['showRecipeKeys'] ) && $attributes['showRecipeKeys'] ? true : false;
?>
<div id="drwb-recipe-posts-carousel-style-<?php echo esc_attr( $attributes['block_id'] ); ?>" class="drwb-recipe-posts-carousel-wrapper">
	<div class="dr-widget">
		<div class="dr-widgetBlock_recipe-carousel <?php echo esc_attr( $layout_class ); ?>">
			<div class="swiper swiper-container" data-swiper="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>" data-id="<?php echo esc_attr( $attributes['block_id'] ); ?>">
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
									'image_size'         => $image_size,
									'show_title'         => $show_title,
									'title_tag'          => $title_tag,
									'show_total_time'    => $show_total_time,
									'show_difficulty'    => $show_difficulty,
									'show_recipe_keys'   => $show_recipe_keys,
									'layout'             => $layout,
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
				<div id="dr_swiper-next-<?php echo esc_attr( $attributes['block_id'] ); ?>" class="swiper-button-next dr_swiper-next"></div>
				<div id="dr_swiper-prev-<?php echo esc_attr( $attributes['block_id'] ); ?>" class="swiper-button-prev dr_swiper-prev"></div>
			</div>
		</div>
	</div>
</div>
<?php
