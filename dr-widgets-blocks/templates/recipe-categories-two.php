<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-categories-two.php.
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
<div class="dr-widgetBlock_recipe-category-two">
	<div class="dr-widgetBlock_fig-wrap">
		<a href="<?php echo esc_url( $settings['link'] ); ?>" class="dr-widgetBlock_recipe-link">
			<?php
			if ( 'image' === $settings['image_selector'] ) {
				if ( $settings['show_category_image'] ) {
					$image_thumb = wp_get_attachment_image( $settings['tax_image'], $settings['image_size'] );
					if ( $image_thumb ) {
						echo wp_kses_post( $image_thumb );
					} else {
						delicious_recipes_get_fallback_svg( $settings['image_size'] );
					}
				}
			} elseif ( 'icon' === $settings['image_selector'] ) {
				if ( $settings['tax_svg'] ) {
					// Function delicious_recipes_get_svg escapes the output.
					$svg = delicious_recipes_get_svg( $settings['tax_svg'], 'recipe-keys', '#000000' );
					if ( $svg ) {
						echo $svg; 
					}
				} else {
					delicious_recipes_get_fallback_svg( 'medium' );
				}
			}
			?>
		</a>
	</div>
	<div class="dr-widgetBlock_content-wrap" style="--dr_cat-bg-color: <?php echo esc_attr( $settings['tax_color'] ); ?>">
		<?php if ( $settings['show_category_name'] ) : ?>
			<<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?> class="dr_cat-title dr_title--animate-underline">
				<a href="<?php echo esc_url( $settings['link'] ); ?>"><?php echo esc_html( $settings['name'] ); ?></a>
				<?php if ( $settings['show_category_count'] ) : ?>
					<span class="dr_recipe-count"><?php echo esc_html( '(' . $settings['count'] . ')' ); ?></span>
				<?php endif; ?>
			</<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?>>
		<?php endif; ?>
	</div>
</div>
