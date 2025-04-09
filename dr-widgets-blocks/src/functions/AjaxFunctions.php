<?php
/**
 * Ajax of DR_Widgets_Blocks.
 *
 * @package DR_Widgets_Blocks
 */

namespace DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Ajax class
 *
 * @package DR_Widgets_Blocks
 */
class DR_Widgets_Blocks_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {
		// Initialize hooks.
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_dr_widgets_blocks_get_image_sizes', array( $this, 'get_image_sizes' ) );
		add_action( 'wp_ajax_dr_widgets_blocks_get_recipe_posts', array( $this, 'get_recipe_posts' ) );

		add_action( 'wp_ajax_dr_widgets_blocks_get_block_settings', array( $this, 'get_block_settings' ) );
		add_action( 'wp_ajax_dr_widgets_blocks_save_block_settings', array( $this, 'save_block_settings' ) );

		add_action( 'wp_ajax_dr_widgets_blocks_get_widget_settings', array( $this, 'get_widget_settings' ) );
		add_action( 'wp_ajax_dr_widgets_blocks_save_widget_settings', array( $this, 'save_widget_settings' ) );

		add_action( 'wp_ajax_dr_widgets_blocks_get_latest_changelog', array( $this, 'get_latest_changelog' ) );
	}

	/**
	 * Get image sizes.
	 */
	public function get_image_sizes() {
		$block = true;
		$sizes = dr_widgets_blocks_get_image_size_options( $block );

		wp_send_json_success( $sizes );
	}

	/**
	 * Get recipe posts.
	 */
	public function get_recipe_posts() {
		$attributes = isset( $_POST['attributes'] ) ? dr_widgets_blocks_clean_vars( json_decode( wp_unslash( $_POST['attributes'] ), true ) ) : array();

		if ( ! isset( $attributes ) ) {
			return;
		}
		$per_page   = isset( $attributes['postsPerPage'] ) ? $attributes['postsPerPage'] : 3;
		$recipe_ids = isset( $attributes['exclude'] ) ? $attributes['exclude'] : false;
		$order_by   = isset( $attributes['orderby'] ) ? $attributes['orderby'] : 'date';
		$order      = isset( $attributes['order'] ) ? $attributes['order'] : 'DESC';
		$offset     = isset( $attributes['offset'] ) ? $attributes['offset'] : 0;

		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'offset'           => $offset,
			'orderby'          => $order_by,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$term_ids = array();
		$taxonomy = isset( $attributes['taxonomy'] ) && '' !== $attributes['taxonomy'] ? $attributes['taxonomy'] : false;
		$terms    = $taxonomy ? ( isset( $attributes['terms'] ) ? $attributes['terms'] : false ) : false;

		if ( $taxonomy && $terms ) {
			foreach ( $terms as $term ) {
				$term_id = get_term_by( 'term_id', $term, $taxonomy )->term_id;
				if ( $term_id ) {
					$term_ids[] = $term_id;
				}
			}
		}

		if ( $term_ids ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'terms'    => $term_ids,
					'field'    => 'term_id',
				),
			);
		} elseif ( $taxonomy ) {
			$args['taxonomy'] = $taxonomy;
		}

		$recipes_query = new \WP_Query( $args );

		$image_size = isset( $attributes['imageSize'] ) ? $attributes['imageSize'] : 'recipe-archive-grid';
		$recipes    = array();

		if ( $recipes_query->have_posts() ) {
			while ( $recipes_query->have_posts() ) {
				$recipes_query->the_post();
				$recipe       = get_post( get_the_ID() );
				$recipe_metas = delicious_recipes_get_recipe( $recipe );

				$thumbnail_id = has_post_thumbnail( $recipe_metas->ID ) ? get_post_thumbnail_id( $recipe_metas->ID ) : '';
				$thumbnail    = $thumbnail_id ? get_the_post_thumbnail( $recipe_metas->ID, $image_size ) : '';
				$fallback_svg = delicious_recipes_get_fallback_svg( $image_size, true );

				$recipe_keys = array();

				if ( ! empty( $recipe_metas->recipe_keys ) ) {
					foreach ( $recipe_metas->recipe_keys as $recipe_key ) {
						$key           = get_term_by( 'name', $recipe_key, 'recipe-key' );
						if ( $key ) {
							$link          = get_term_link( $key, 'recipe-key' );
							$icon          = delicious_recipes_get_tax_icon( $key, true );
							$recipe_keys[] = array(
								'key'  => $recipe_key,
								'link' => $link,
								'icon' => $icon,
							);
						} else {
							error_log( "Term not found for recipe key: " . $recipe_key );
						}
					}
				}

				$recipes[] = array(
					'recipe_id'        => $recipe_metas->ID,
					'title'            => $recipe_metas->name,
					'permalink'        => $recipe_metas->permalink,
					'thumbnail_id'     => $recipe_metas->thumbnail_id,
					'thumbnail_url'    => $recipe_metas->thumbnail,
					'thumbnail'        => $thumbnail,
					'fallback_svg'     => $fallback_svg,
					'recipe_keys'      => $recipe_keys,
					'total_time'       => $recipe_metas->total_time,
					'difficulty_level' => $recipe_metas->difficulty_level,
				);
			}
			wp_reset_postdata();
		}
		wp_send_json_success( $recipes );
	}

	/**
	 * Get Block Settings values.
	 */
	public function get_block_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = dr_widgets_blocks_get_block_settings();
		wp_send_json_success( $data );
	}

	/**
	 * Save Block Settings values.
	 */
	public function save_block_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = isset( $_POST['blocks'] ) ? dr_widgets_blocks_clean_vars( json_decode( wp_unslash( $_POST['blocks'] ), true ) ) : array();
		update_option( 'drwb_block_settings', $data );

		wp_send_json_success( __( 'Saved successfully.', 'dr-widgets-blocks' ) );
	}

	/**
	 * Get Widget Settings values.
	 */
	public function get_widget_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = dr_widgets_blocks_get_widget_settings();
		wp_send_json_success( $data );
	}

	/**
	 * Save Widget Settings values.
	 */
	public function save_widget_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = isset( $_POST['widgets'] ) ? dr_widgets_blocks_clean_vars( json_decode( wp_unslash( $_POST['widgets'] ), true ) ) : array();
		update_option( 'drwb_widget_settings', $data );

		wp_send_json_success( __( 'Saved successfully.', 'dr-widgets-blocks' ) );
	}

	/**
	 * Get Latest Changelog
	 *
	 * @return void
	 */
	public function get_latest_changelog() {
		$changelog     = null;
		$pro_changelog = null;
		$access_type   = get_filesystem_method();

		if ( 'direct' === $access_type ) {
			$creds = request_filesystem_credentials(
				site_url() . '/wp-admin/',
				'',
				false,
				false,
				array()
			);

			if ( WP_Filesystem( $creds ) ) {
				global $wp_filesystem;

				$changelog = $wp_filesystem->get_contents(
					plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/changelog.txt'
				);
			}
		}

		wp_send_json_success(
			array(
				'changelog' => apply_filters(
					'drwb_changelogs_list',
					array(
						array(
							'title'     => __( 'Free', 'dr-widgets-blocks' ),
							'changelog' => $changelog,
						),
						array(
							'title'     => __( 'Pro', 'dr-widgets-blocks' ),
							'changelog' => $pro_changelog,
						),
					)
				),
			)
		);
	}
}

new DR_Widgets_Blocks_Ajax();
