<?php

/**
 * Recipe Posts Block Template
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
$taxonomy = isset( $attributes['taxonomy'] ) && '' !== $attributes['taxonomy'] ? $attributes['taxonomy'] : false;
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
		<?php esc_html_e( 'Please check the block settings and make sure you have selected a valid query.', 'dr-widgets-blocks' ); ?>
	</p>
	<?php
	return;
}

$per_row        = isset( $attributes['recipesPerRow']['desktop'] ) ? absint( $attributes['recipesPerRow']['desktop'] ) : 3;
$per_row_tablet = isset( $attributes['recipesPerRow']['tablet'] ) ? absint( $attributes['recipesPerRow']['tablet'] ) : 2;
$per_row_mobile = isset( $attributes['recipesPerRow']['mobile'] ) ? absint( $attributes['recipesPerRow']['mobile'] ) : 1;
$layout         = isset( $attributes['layout'] ) ? $attributes['layout'] : 'layout-1';
$layout         = 'recipes-grid-' . preg_replace( '/[^0-9]/', '', $layout ) . '.php';

$show_feature_image  = isset( $attributes['showFeatureImage'] ) && $attributes['showFeatureImage'] ? true : false;
$image_size          = isset( $attributes['imageSize'] ) ? $attributes['imageSize'] : 'recipe-archive-grid';
$image_custom_size   = isset( $attributes['imageCustomSize'] ) ? $attributes['imageCustomSize'] : false;
$image_size          = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
$show_title          = isset( $attributes['showTitle'] ) && $attributes['showTitle'] ? true : false;
$title_tag           = isset( $attributes['headingTag'] ) ? $attributes['headingTag'] : 'h3';
$show_total_time     = isset( $attributes['showTotalTime'] ) && $attributes['showTotalTime'] ? true : false;
$show_difficulty     = isset( $attributes['showDifficulty'] ) && $attributes['showDifficulty'] ? true : false;
$show_recipe_keys    = isset( $attributes['showRecipeKeys'] ) && $attributes['showRecipeKeys'] ? true : false;
$show_excerpt        = isset( $attributes['showExcerpt'] ) && $attributes['showExcerpt'] ? true : false;
$show_author         = isset( $attributes['showAuthor'] ) && $attributes['showAuthor'] ? true : false;
$show_publish_date   = isset( $attributes['showPublishDate'] ) && $attributes['showPublishDate'] ? true : false;
$show_rating         = isset( $attributes['showRating'] ) && $attributes['showRating'] ? true : false;
$show_comment        = isset( $attributes['showComment'] ) && $attributes['showComment'] ? true : false;
$show_category       = isset( $attributes['showCategory'] ) && $attributes['showCategory'] ? true : false;
$show_readmore       = isset( $attributes['showReadmore'] ) && $attributes['showReadmore'] ? true : false;
$readmore_text       = isset( $attributes['readmoreText'] ) ? $attributes['readmoreText'] : esc_html__( 'Read More', 'dr-widgets-blocks' );
$show_readmore_arrow = isset( $attributes['showReadMoreArrow'] ) && $attributes['showReadMoreArrow'] ? true : false;
$excerpt_length      = isset( $attributes['excerptLength'] ) ? $attributes['excerptLength'] : 20;
$image_alignment     = isset( $attributes['imageAlignment'] ) ? $attributes['imageAlignment'] : 'left';
$separator           = isset( $attributes['separator'] ) ? $attributes['separator'] : 'dot';
?>
<div id="drwb-recipe-posts-style-<?php echo esc_attr( $attributes['block_id'] ); ?>" class="drwb-recipe-posts-wrapper">
	<div class="dr-widget">
		<div class="dr-widgetBlock_row dr_columns-<?php echo esc_attr( $per_row ); ?>-lg dr_columns-<?php echo esc_attr( $per_row_tablet ); ?>-md dr_columns-<?php echo esc_attr( $per_row_mobile ); ?>">
			<?php
			if ( $recipes_query->have_posts() ) {
				while ( $recipes_query->have_posts() ) {
					$recipes_query->the_post();
					$recipe       = get_post( get_the_ID() );
					$recipe_metas = delicious_recipes_get_recipe( $recipe );
					$data         = array(
						'settings'     => array(
							'show_feature_image'  => $show_feature_image,
							'image_size'          => $image_size,
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
							'show_readmore'       => $show_readmore,
							'readmore_text'       => $readmore_text,
							'show_readmore_arrow' => $show_readmore_arrow,
							'excerpt_length'      => $excerpt_length,
							'image_alignment'     => $image_alignment,
							'separator'           => $separator,
						),
						'recipe_metas' => $recipe_metas,
					);
					dr_widgets_blocks_get_template( $layout, $data );
				}
				wp_reset_postdata();
			}
			?>
		</div>
	</div>
</div>
<?php
