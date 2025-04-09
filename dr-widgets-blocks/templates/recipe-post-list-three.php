<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-post-list-three.php.
 *
 * HOWEVER, on occasion WP Delicious will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://wpdelicious.com/docs-category/delisho/
 * @package DR_Widgets_Blocks/Templates
 * @version 1.0.0
 */

use Elementor\Utils;
defined( 'ABSPATH' ) || exit;

$dynamic_class = ( 0 === $count ) ? 'dr-post-list--hero' : 'dr-post-list--regular';
$imageSize     = ( 0 === $count ) ? $settings['hero_image_size'] : $settings['image_size'];
?>
<div class="dr-post-list__recipe <?php echo esc_attr( $dynamic_class ); ?>">
	<div class="dr-widgetBlock_recipe-post">
		<div class="dr-widgetBlock_fig-wrapper">
			<?php if ( 0 !== $count && 'layout-3' === $settings['layout'] ) { ?>
				<span class="dr-counter <?php echo esc_attr( $settings['counter'] ); ?>">
					<?php echo esc_html( $count ); ?>
				</span>
			<?php } else { ?>
				<a href="<?php echo esc_url( $recipe_metas->permalink ); ?>" class="dr-widgetBlock_recipe-link">
					<?php
					if ( $recipe_metas->thumbnail ) :
						the_post_thumbnail( $imageSize );
					else :
						delicious_recipes_get_fallback_svg( $imageSize );
					endif;
					?>
				<?php
				if ( 0 === $count ) {
					delicious_recipes_widgets_wishlists_button( $settings['show_wishlist'] );}
				?>
				</a>
			<?php } ?>
		</div>
		<div class="dr-widgetBlock_content-wrap">
			<?php if ( 0 === $count && 'layout-2' !== $settings['layout'] && ! empty( $recipe_metas->recipe_keys ) && $settings['show_recipe_keys'] ) : ?>
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
			<?php endif; ?>
			<?php if ( 0 === $count && $settings['show_category'] && ! empty( $recipe_metas->recipe_course ) ) : ?>
				<div class="dr_recipe-cats" data-separator="<?php echo esc_attr( $settings['separator'] ); ?>">
					<div class="dr_recipe-cats-container">
						<?php the_terms( $recipe_metas->ID, 'recipe-course', '', '', '' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?> class="dr_title dr_title--animate-underline">
				<a href="<?php echo esc_url( $recipe_metas->permalink ); ?>"><?php echo esc_html( $recipe_metas->name ); ?></a>
			</<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?>>

			<div class="dr_footer-meta">
				<div class="dr_footer-meta-container">
					<div class="dr_meta-content">
						<div class="dr_meta-items">
							<?php if ( $recipe_metas->total_time && $settings['show_total_time'] ) : ?>
								<span class="dr_meta-item dr_meta-duration">
									<?php echo sprintf( '%1$s', esc_html( $recipe_metas->total_time ) ); ?>
								</span>
							<?php endif; ?>

							<?php if ( $recipe_metas->difficulty_level && $settings['show_difficulty'] ) : ?>
								<span class="dr_meta-item dr_meta-level">
									<?php echo esc_html( $recipe_metas->difficulty_level ); ?>
								</span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php if ( 0 === $count && 'layout-2' === $settings['layout'] && ! empty( $recipe_metas->recipe_keys ) && $settings['show_recipe_keys'] ) : ?>
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
			<?php endif; ?>
		</div>
	</div>
</div>
