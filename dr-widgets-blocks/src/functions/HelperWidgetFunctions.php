<?php 

function delicious_recipes_widgets_wishlists_button( $show ){
	
	if( $show ){
		$wishlist = new Delicious_Recipes_Likes_Wishlists();
		return $wishlist->recipe_wishlist_button();
	}
}

function delicious_recipes_widgets_grid_module_keys( $recipe_metas,$settings ){

	if( ! empty( $recipe_metas->recipe_keys ) && $settings['show_recipe_keys'] ){
	?>
	<div class="dr_recipe-keys">
		<div class="dr_recipe-keys-container">
			<?php
			foreach ( $recipe_metas->recipe_keys as $recipe_key ) :
				$key = get_term_by( 'name', $recipe_key, 'recipe-key' );
				if ( $key ) {
					$recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
					$key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : '';
					?>
					<a href="<?php echo esc_url( get_term_link( $key, 'recipe-key' ) ); ?>" class="dr_recipe-key" data-title="<?php echo esc_attr( $recipe_key ); ?>">
						<?php delicious_recipes_get_tax_icon( $key ); ?>
					</a>
					<?php
				} else {
					error_log( "Term not found for recipe key: " . $recipe_key );
				}
			endforeach; ?>
		</div>
	</div>
	<?php 
	}
}

/**
 * Parse taxonomy and term ID from combined string in format "taxonomy_termid" (e.g. "recipe-course_122")
 * @param array $all_term_id
 * @param bool $return_taxonomy
 * @return string|int
 */
function dr_widgets_blocks_parse_term_id($all_term_id, $return_taxonomy = true) {
	if(!is_string($all_term_id)) {
		return '';
	}

	if ($return_taxonomy) {
		// Return taxonomy (e.g. 'recipe-course')
		return substr($all_term_id, 0, strpos($all_term_id, '_'));
	} else {
		// Return term ID (e.g. '122')
		return (int)substr(strrchr($all_term_id, '_'), 1);
	}
}