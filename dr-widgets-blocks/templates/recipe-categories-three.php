<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-categories-three.php.
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

if ( 'layout-2' === $settings['layout'] ){
	$class = 'l2rc';
}elseif( 'layout-3' === $settings['layout'] ){
	$class = 'l3rc';
}else $class= '';
?>
<div class="dr-widgetBlock_recipe-category-three <?php echo esc_attr( $class ); ?>">
	<div class="dr-widgetBlock_fig-wrap"style="--dr_cat-bg-color: <?php echo esc_attr( $settings['tax_color'] ); ?>" >
		<a href="<?php echo esc_url( $settings['link'] ); ?>" class="dr-widgetBlock_recipe-link">
			<?php
			if ( $settings['show_category_image'] ) {
				$image_thumb = wp_get_attachment_image( $settings['tax_image'], $settings['image_size'] );
				if($image_thumb) {
					echo wp_kses_post( $image_thumb );
				} else {
					delicious_recipes_get_fallback_svg($settings['image_size']);
				}
			}
			?>
		</a>
	</div>
	<div class="dr-widgetBlock_content-wrap" style="--dr_cat-bg-color: <?php echo esc_attr( $settings['tax_color'] ); ?>" >
		<?php if ( $settings['show_category_name'] ) : ?>
			<<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?> class="dr_cat-title dr_title--animate-underline">
			<div class="content">
				<a href="<?php echo esc_url( $settings['link'] ); ?>"><?php echo esc_html( $settings['name'] ); ?></a>
				<?php if ( $settings['show_category_count'] ) : ?>
					<span class="dr_recipe-count"><?php echo esc_html( '(' . $settings['count'] . ' Recipes' ) . ')'; ?></span>
				<?php endif; ?>
			</div>
			<?php if( 'layout-3' === $settings['layout'] ){
				?><div class="icon">
				<a href="<?php echo esc_url( $settings['link'] ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>

				</a>
			</div>
			<?php } ?>
			</<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?>>
		<?php endif; ?>
	</div>
</div>
