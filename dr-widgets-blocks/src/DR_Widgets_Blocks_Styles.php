<?php
/**
 * Styles REST API Action.
 *
 * @package DR_Widgets_Blocks
 *
 * @since v.1.0.0
 */

namespace DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Styles class.
 */
class DR_Widgets_Blocks_Styles {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init() {
		// Initialize hooks.
		$this->init_hooks();

		// Allow 3rd party to remove hooks.
		do_action( 'dr_widgets_blocks_styles_unhook', $this );
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action( 'rest_api_init', array( $this, 'save_block_css_callback' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_front_callback' ) );
	}


	/**
	 * REST API Action
	 *
	 * @since v.1.0.0
	 */
	public function save_block_css_callback() {
		register_rest_route(
			'drwidgetsblocks/v1',
			'/save_block_css/',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'save_block_content_css' ),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Save Import CSS in the top of the File
	 *
	 * @since v.1.0.0
	 * @param array $request
	 * @return array
	 */
	public function save_block_content_css( $request ) {
		try {
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$params    = $request->get_params();
			$post_id   = sanitize_text_field( $params['post_id'] );
			$block_css = wp_kses_post( $params['block_css'] );

			if ( 'drwidgetsblocks-widget' === $post_id && $params['has_block'] ) {
				update_option( $post_id, $block_css );
				return array(
					'success' => true,
					'message' => __( 'Widget CSS Saved', 'dr-widgets-blocks' ),
				);
			}

			$post_id        = (int) $post_id;
			$filename       = "dr-widgets-blocks-css-{$post_id}.css";
			$upload_dir_url = wp_upload_dir();
			$dir            = trailingslashit( $upload_dir_url['basedir'] ) . 'drwidgetsblocks/';

			if ( $params['has_block'] ) {
				update_post_meta( $post_id, '_drwidgetsblocks_active', 'yes' );

				// Preview Check.
				if ( $params['preview'] ) {
					set_transient( '_drwidgetsblocks_preview_' . $post_id, $block_css, 60 * 60 );
					return array( 'success' => true );
				}

				WP_Filesystem( false, $upload_dir_url['basedir'], true );
				if ( ! $wp_filesystem->is_dir( $dir ) ) {
					$wp_filesystem->mkdir( $dir );
				}
				if ( ! $wp_filesystem->put_contents( $dir . $filename, $block_css ) ) {
					throw new Exception( __( 'CSS can not be saved due to permission!!!', 'dr-widgets-blocks' ) );
				}
				update_post_meta( $post_id, '_drwidgetsblocks_css', $block_css );
				return array(
					'success' => true,
					'message' => __( 'Delisho css file has been updated.', 'dr-widgets-blocks' ),
				);
			} else {
				delete_post_meta( $post_id, '_drwidgetsblocks_active' );
				if ( file_exists( $dir . $filename ) ) {
					wp_delete_file( $dir . $filename );
				}
				delete_post_meta( $post_id, '_drwidgetsblocks_css' );
				return array(
					'success' => true,
					'message' => __( 'Data Delete Done', 'dr-widgets-blocks' ),
				);
			}
		} catch ( Exception $e ) {
			return array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}

	/**
	 * Only Frontend CSS and JS Scripts
	 *
	 * @since v.1.0.0
	 */
	public function register_scripts_front_callback() {
		// For Widget.
		$has_block     = false;
		$widget_blocks = array();
		global $wp_registered_sidebars, $sidebars_widgets;
		foreach ( $wp_registered_sidebars as $key => $value ) {
			if ( is_active_sidebar( $key ) ) {
				foreach ( $sidebars_widgets[ $key ] as $val ) {
					if ( strpos( $val, 'block-' ) !== false ) {
						if ( empty( $widget_blocks ) ) {
							$widget_blocks = get_option( 'widget_block' );
						}
						foreach ( (array) $widget_blocks as $block ) {
							if ( isset( $block['content'] ) && strpos( $block['content'], 'wp:dr-widgets-blocks' ) !== false ) {
								$has_block = true;
								break;
							}
						}
						if ( $has_block ) {
							break;
						}
					}
				}
			}
		}
		if ( $has_block ) {
			$css = get_option( 'drwidgetsblocks-widget', true );
			if ( $css ) {
				wp_register_style( 'drwidgetsblocks-widget', false );
				wp_enqueue_style( 'drwidgetsblocks-widget' );
				wp_add_inline_style( 'drwidgetsblocks-widget', $css );
			}
		}
	}

}
