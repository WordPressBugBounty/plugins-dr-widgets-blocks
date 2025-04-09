<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-carousels.php.
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

defined( 'ABSPATH' ) || exit;
use Elementor\Utils;

?>
<div class="swiper-slide">
	<div class="dr-widgetBlock_recipe-post">
		<?php if ( $settings['show_feature_image'] ) : ?>
			<div class="dr-widgetBlock_fig-wrap">
				<a href="<?php echo esc_url( $recipe_metas->permalink ); ?>" class="dr-widgetBlock_recipe-link">
					<?php
					if ( $recipe_metas->thumbnail ) :
						the_post_thumbnail( $settings['image_size'] );
					else :
						delicious_recipes_get_fallback_svg( $settings['image_size'] );
					endif;
					?>
				</a>
			</div>
		<?php endif; ?>

		<div class="dr-widgetBlock_content-wrap">
			<?php if ( 'layout-3' === $settings['layout'] ) : ?>
				<?php if ( ! empty( $recipe_metas->recipe_keys ) && $settings['show_recipe_keys'] ) : ?>
					<div class="dr_recipe-keys">
						<div class="dr_recipe-keys-container">
							<?php
							foreach ( $recipe_metas->recipe_keys as $recipe_key ) :
								$key = get_term_by( 'name', $recipe_key, 'recipe-key' );
								if ( $key ) {
									$recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
									$key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : '';
									?>
									<a href="<?php echo esc_url( get_term_link( $key, 'recipe-key' ) ); ?>" class="dr_recipe-key" data-title="<?php echo esc_attr( $recipe_key ); ?>" data-tooltip="top">
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
			<?php endif; ?>

			<?php if ( $settings['show_title'] ) : ?>
				<<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?> class="dr_title">
					<a href="<?php echo esc_url( $recipe_metas->permalink ); ?>"><?php echo esc_html( $recipe_metas->name ); ?></a>
				</<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?>>
			<?php endif; ?>

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

			<?php if ( 'layout-1' === $settings['layout'] || 'layout-2' === $settings['layout'] ) : ?>
				<?php if ( ! empty( $recipe_metas->recipe_keys ) && $settings['show_recipe_keys'] ) : ?>
					<div class="dr_recipe-keys">
						<div class="dr_recipe-keys-container">
							<?php
							foreach ( $recipe_metas->recipe_keys as $recipe_key ) :
								$key = get_term_by( 'name', $recipe_key, 'recipe-key' );
								if ( $key ) {
									$recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
									$key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : '';
									?>
									<a href="<?php echo esc_url( get_term_link( $key, 'recipe-key' ) ); ?>" class="dr_recipe-key" data-title="<?php echo esc_attr( $recipe_key ); ?>" data-tooltip="top">
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
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
