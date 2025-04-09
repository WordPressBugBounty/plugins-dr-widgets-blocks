<?php
/**
 * Public Class
 *
 * @package DR_Widgets_Blocks
 */

namespace DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Handle public functions
 */
class DR_Widgets_Blocks_Public {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {
		$this->init_hooks();

		add_action(
			'plugins_loaded',
			function() {
				// Allow 3rd party to remove hooks.
				do_action( 'dr_widgets_blocks_public_unhook', $this );
			},
			999
		);
	}

	/**
	 * Initialize hooks
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {
		$fontsmanager = new Fonts_Manager();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp', array( $fontsmanager, 'generate_assets' ), 99 );
		add_action( 'wp_head', array( $this, 'generate_stylesheet' ), 80 );

		if ( isset( $_GET['preview_id'] ) && isset( $_GET['preview_nonce'] ) ) {
			add_action( 'wp_head', array( $this, 'add_block_inline_css' ), 100 );
		}

	}

	/**
	 * Enqueue styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$fontsmanager = new Fonts_Manager();
		$fontsmanager->load_dynamic_google_fonts();
	}

	/**
	 * Generates stylesheet and appends in head tag.
	 *
	 * @since 1.0.0
	 */
	public function generate_stylesheet() {
		$fontsmanager = new Fonts_Manager();
		$stylesheet   = $fontsmanager::$stylesheet;

		if ( is_null( $stylesheet ) || '' === $stylesheet ) {
			return;
		}
		ob_start();
		?>
			<style id="dr-widgets-blocks-styles-frontend"><?php echo $stylesheet; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?></style>
		<?php
		ob_end_flush();
	}

	/**
	 * Add Blcok CSS in preview
	 *
	 * @return void
	 */
	public function add_block_inline_css() {
		$post_id = isset( $_GET['preview_id'] ) ? absint( $_GET['preview_id'] ) : '';
		$css     = get_transient( '_drwidgetsblocks_preview_' . $post_id, true );

		if ( ! $css ) {
			$css = get_post_meta( $post_id, '_drwidgetsblocks_css', true );
		}

		if ( $css ) {
			echo '<style id="dr-widgets-blocks-styles-frontend">' . esc_html( $css ) . '</style>';
		}
	}

}
