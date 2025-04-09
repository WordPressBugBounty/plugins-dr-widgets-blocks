<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/dr-widgets-blocks/recipe-categories-tab-nav.php.
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
<div class="dr_tab <?php echo esc_attr( $layout_class ); ?>">
	<div class="dr_tabs-wrapper">
		<<?php Utils::print_validated_html_tag( $heading_tag ); ?> class="dr_tab-widget-title">
			<?php echo esc_html( $heading_title ); ?>
		</<?php Utils::print_validated_html_tag( $heading_tag ); ?>>

		<nav class="dr_tab-nav">
			<div class="dr_tab-nav-container">
				<ul class="dr_tab-navigation">
					<li>
						<span class="dr_tab-title dr_active" aria-selected="true" aria-controls="dr_tab-content-all-<?php echo esc_attr( $block_id ); ?>"><?php echo esc_html__( 'All', 'dr-widgets-blocks' ); ?></span>
					</li>
					<?php
					$i = 1;
					if ( !$all_taxonomy && ! empty( $categories ) || ($all_taxonomy && ! empty( $all_term_id )) ) {
						foreach ( $categories as $category ) {
							?>
								<li>
									<span class="dr_tab-title" aria-selected="<?php echo 1 === $i ? 'true' : 'false'; ?>" aria-controls="dr_tab-content-<?php echo esc_attr( $category->term_id ); ?>-<?php echo esc_attr( $block_id ); ?>"><?php echo esc_html( $category->name ); ?></span>
								</li>
							<?php
							$i++;
						}
					}
					?>
				</ul>
				<div class="dr_tab-dropdown hidden">
					<button class="dr_tab-dropdown-btn">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16">
							<path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
						</svg>
					</button>
					<ul class="dr_tab-dropdown-menu"></ul>
				</div>
			</div>
			<div class="dr_swiper-navigation">
				<div id="dr_swiper-prev-<?php echo esc_attr( $block_id ); ?>" class="dr_swiper-prev dr_swiper-arrow"></div>
				<div id="dr_swiper-next-<?php echo esc_attr( $block_id ); ?>" class="dr_swiper-next dr_swiper-arrow"></div>
			</div>
		</nav>
	</div>
	<div class="dr_tabs-content-wrapper">
		<?php
		$all_recipes_query = new \WP_Query( $args );
		if ( $all_recipes_query->have_posts() ) {
			?>
			<div id="dr_tab-content-all-<?php echo esc_attr( $block_id ); ?>" class="dr_tab-content dr_active">
				<div class="dr_recipe-slider swiper swiper-container" data-id="<?php echo esc_attr( $block_id ); ?>">
					<div class="swiper-wrapper">
						<?php
						$i = 1;
						while ( $all_recipes_query->have_posts() ) {
							$all_recipes_query->the_post();
							$recipe       = get_post( get_the_ID() );
							$recipe_metas = delicious_recipes_get_recipe( $recipe );
							$data         = array(
								'settings'     => array(
									'show_title'      => $show_title,
									'title_tag'       => $title_tag,
									'show_total_time' => $show_total_time,
									'show_difficulty' => $show_difficulty,
								),
								'recipe_metas' => $recipe_metas,
								'per_slide'    => $per_slide,
								'i'            => $i,
								'count'        => $all_recipes_query->post_count,
							);

							dr_widgets_blocks_get_template( 'recipe-categories-tab-content.php', $data );
							$i++;
						}
						?>

					</div>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();

		?>

<?php
