<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-grid-one.php.
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

$imageSize = ( 'layout-2' === $settings['layout'] ) ? $settings['image_size_l2'] : $settings['image_size'];

?>
<div class="dr_column">
	<div class="dr-widgetBlock_recipe-post">
		<?php if ( $settings['show_feature_image'] ) : ?>
			<div class="dr-widgetBlock_fig-wrap">
				<a href="<?php echo esc_url( $recipe_metas->permalink ); ?>" class="dr-widgetBlock_recipe-link">
					<?php
					if ( $recipe_metas->thumbnail ) :
						the_post_thumbnail( $imageSize );
					else :
						delicious_recipes_get_fallback_svg( $imageSize );
					endif;
					?>
				</a>
				<?php delicious_recipes_widgets_wishlists_button( $settings['show_wishlist'] ); ?>
			</div>
		<?php endif; ?>

		<div class="dr-widgetBlock_content-wrap">
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

			<?php if ( $settings['show_category'] && ! empty( $recipe_metas->recipe_course ) ) : ?>
				<div class="dr_recipe-cats" data-separator="<?php echo esc_attr( $settings['separator'] ); ?>">
					<div class="dr_recipe-cats-container">
						<?php the_terms( $recipe_metas->ID, 'recipe-course', '', '', '' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $settings['show_title'] ) : ?>
				<<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?> class="dr_title dr_title--animate-underline">
					<a href="<?php echo esc_url( $recipe_metas->permalink ); ?>"><?php echo esc_html( $recipe_metas->name ); ?></a>
				</<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?>>
				<?php
			endif;

			if ( ( $settings['show_author'] && $recipe_metas->author ) || ( $recipe_metas->date_published && $settings['show_publish_date'] ) || ( $settings['show_comment'] && $recipe_metas->comments_number ) ) {
				?>

				<div class="dr_meta-content" data-separator="<?php echo esc_attr( $settings['separator'] ); ?>">
					<div class="dr_meta-items">
						<?php if ( $settings['show_author'] && $recipe_metas->author ) : ?>
							<div class="dr_meta-item dr_byline">
								<?php echo get_avatar( $recipe_metas->author_id, 32 ); ?>
								<a href="<?php echo esc_url( get_author_posts_url( $recipe_metas->author_id ) ); ?>" class="fn"><?php echo esc_html( $recipe_metas->author ); ?></a>
							</div>
						<?php endif; ?>

						<?php if ( $recipe_metas->date_published && $settings['show_publish_date'] ) : ?>
							<span class="dr_meta-item dr_meta-date">
								<?php echo esc_html( delicious_recipes_get_formated_date( $recipe_metas->date_published ) ); ?>
							</span>
						<?php endif; ?>

						<?php if ( $settings['show_comment'] && $recipe_metas->comments_number ) : ?>
							<span class="dr_meta-item dr_meta-comment">
							<?php
								echo sprintf(
									esc_html(
										/* translators: %s: number of comments */
										_nx( '%s Comment', '%s Comments', $recipe_metas->comments_number, 'number of comments', 'dr-widgets-blocks' )
									),
									esc_html( number_format_i18n( $recipe_metas->comments_number ) )
								);
							?>
							</span>
						<?php endif; ?>
					</div>
				</div>
				<?php
			}

			if ( $settings['show_excerpt'] && $recipe_metas->excerpt && 'layout-1' === $settings['layout'] ) :
				?>
				<div class="dr_content">
					<?php echo wp_kses_post( wp_trim_words( $recipe_metas->excerpt, absint( $settings['excerpt_length'] ), '...' ) ); ?>
				</div>
				<?php
			endif;

			if ( ( $recipe_metas->total_time && $settings['show_total_time'] ) || ( $recipe_metas->difficulty_level && $settings['show_difficulty'] ) || ( $recipe_metas->rating && $settings['show_rating'] ) ) {
				?>
				<div class="dr_footer-meta" style="--dr_meta-margin: 30px 0 0;">
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

								<?php if ( $recipe_metas->rating && $settings['show_rating'] ) : ?>
									<span class="dr_meta-item dr_meta-review">
										<?php echo esc_html( $recipe_metas->rating ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php
