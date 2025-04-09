<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-categories.php.
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

if ( 'layout-1' === $settings['layout'] ) : ?>
	<div class="dr_column">
		<div class="dr-widgetBlock_recipe-category">
			<div class="dr-widgetBlock_content-wrap" style="--dr_cat-bg-color: <?php echo esc_attr( $settings['tax_color'] ); ?>">
				<?php if ( $settings['show_category_name'] ) : ?>
					<<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?> class="dr_cat-title">
						<a href="<?php echo esc_url( $settings['link'] ); ?>"><?php echo esc_html( $settings['name'] ); ?></a>
						<?php if ( $settings['show_category_count'] ) : ?>
							<span class="dr_recipe-count"><?php echo esc_html( $settings['count'] ); ?></span>
						<?php endif; ?>
					</<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?>>
				<?php endif; ?>
			</div>
			<div class="dr-widgetBlock_fig-wrap">
				<a href="<?php echo esc_url( $settings['link'] ); ?>" class="dr-widgetBlock_recipe-link">
					<?php
					if ( $settings['show_category_image'] && $settings['tax_image'] ) {
						$image_thumb = wp_get_attachment_image( $settings['tax_image'], 'full' );
						echo wp_kses_post( $image_thumb );
					}
					?>
				</a>
			</div>
		</div>
	</div>
<?php elseif ( 'layout-2' === $settings['layout'] ) : ?>
	<div class="dr_column">
		<div class="dr-widgetBlock_recipe-category l2rc">
			<div class="dr-widgetBlock_content-wrap" style="--dr_cat-bg-color: <?php echo esc_attr( $settings['tax_color'] ); ?>">
				<?php if ( $settings['show_category_name'] ) : ?>
					<<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?> class="dr_cat-title">
						<a href="<?php echo esc_url( $settings['link'] ); ?>"><?php echo esc_html( $settings['name'] ); ?></a>
						<?php if ( $settings['show_category_count'] ) : ?>
							<span class="dr_recipe-count"><?php echo esc_html( $settings['count'] ); ?></span>
						<?php endif; ?>
					</<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?>>
				<?php endif; ?>
			</div>
			<div class="dr-widgetBlock_fig-wrap">
				<a href="<?php echo esc_url( $settings['link'] ); ?>" class="dr-widgetBlock_recipe-link">
					<?php
					if ( $settings['show_category_image'] && $settings['tax_image'] ) {
						$image_thumb = wp_get_attachment_image( $settings['tax_image'], 'full' );
						echo wp_kses_post( $image_thumb );
					}
					?>
				</a>
			</div>
		</div>
	</div>
<?php elseif ( 'layout-3' === $settings['layout'] ) : ?>
	<div class="dr_column">
		<div class="dr-widgetBlock_recipe-category l3rc">
			<div class="dr-widgetBlock_fig-wrap">
				<a href="<?php echo esc_url( $settings['link'] ); ?>" class="dr-widgetBlock_recipe-link">
					<?php
					if ( $settings['show_category_image'] && $settings['tax_image'] ) {
						$image_thumb = wp_get_attachment_image( $settings['tax_image'], 'full' );
						echo wp_kses_post( $image_thumb );
					}
					?>
				</a>
			</div>
			<div class="dr-widgetBlock_content-wrap" style="--dr_cat-bg-color: <?php echo esc_attr( $settings['tax_color'] ); ?>">
				<?php if ( $settings['show_category_name'] ) : ?>
					<<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?> class="dr_cat-title">
						<a href="<?php echo esc_url( $settings['link'] ); ?>"><?php echo esc_html( $settings['name'] ); ?></a>
						<?php if ( $settings['show_category_count'] ) : ?>
							<span class="dr_recipe-count"><?php echo esc_html( $settings['count'] ); ?></span>
						<?php endif; ?>
					</<?php Utils::print_validated_html_tag( $settings['category_tag'] ); ?>>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
endif;
