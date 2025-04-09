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
$term_ids   = array();
$taxonomy   = isset( $attributes['taxonomy'] ) && '' !== $attributes['taxonomy'] ? $attributes['taxonomy'] : 'recipe-course';
$terms      = $taxonomy ? ( isset( $attributes['terms'] ) ? $attributes['terms'] : '' ) : '';
$hide_empty = isset( $attributes['hideEmpty'] ) && $attributes['hideEmpty'] ? true : false;

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
		'hide_empty' => $hide_empty,
		'count'      => true,
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

$layout              = isset( $attributes['layout'] ) ? $attributes['layout'] : 'layout-1';
$per_row             = isset( $attributes['categoriesPerRow']['desktop'] ) ? absint( $attributes['categoriesPerRow']['desktop'] ) : 3;
$per_row_tablet      = isset( $attributes['categoriesPerRow']['tablet'] ) ? absint( $attributes['categoriesPerRow']['tablet'] ) : 2;
$per_row_mobile      = isset( $attributes['categoriesPerRow']['mobile'] ) ? absint( $attributes['categoriesPerRow']['mobile'] ) : 1;
$show_category_image = isset( $attributes['showCategoryImage'] ) && $attributes['showCategoryImage'] ? true : false;
$show_category_count = isset( $attributes['showCategoryCount'] ) && $attributes['showCategoryCount'] ? true : false;
$show_category_name  = isset( $attributes['showCategoryName'] ) && $attributes['showCategoryName'] ? true : false;
$category_tag        = isset( $attributes['categoryTag'] ) ? $attributes['categoryTag'] : 'h3';
$bgcolor_from        = isset( $attributes['bgColorFrom'] ) ? $attributes['bgColorFrom'] : 'default';
$background_color    = isset( $attributes['backgroundColor'] ) ? $attributes['backgroundColor'] : '';

?>
<div id="drwb-recipe-categories-style-<?php echo esc_attr( $attributes['block_id'] ); ?>" class="drwb-recipe-categories-wrapper">
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
				$tax_color         = $bgcolor_from === 'default' ? $tax_color : $background_color;
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
</div>
<?php
