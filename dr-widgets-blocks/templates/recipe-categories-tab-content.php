<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-categories-tab-content.php.
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

if ( 1 === $i % $per_slide ) {
	echo '<div class="dr_recipe-slide-item swiper-slide">';
}
?>
	<div class="dr-widgetBlock_recipe-post content-overlay">
		<div class="dr-widgetBlock_fig-wrap">
			<a href="<?php echo esc_url( $recipe_metas->permalink ); ?>" class="dr-widgetBlock_recipe-link">
				<?php
				if ( $recipe_metas->thumbnail ) :
					the_post_thumbnail( 'full' );
				else :
					delicious_recipes_get_fallback_svg( 'full' );
				endif;
				?>
			</a>
		</div>

		<div class="dr-widgetBlock_content-wrap">
			<?php if ( $settings['show_title'] ) : ?>
				<<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?> class="dr_title dr_title--animate-underline">
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
		</div>
	</div>
<?php
if ( 0 === $i % $per_slide || $count === $i ) {
	echo '</div>';
}
