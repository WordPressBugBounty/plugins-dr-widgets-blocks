<?php
namespace DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * DR_Widgets_Blocks Blocks
 *
 * @package DR_Widgets_Blocks
 *
 * @since 1.0.0
 */
class Blocks {
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
				do_action( 'dr_widgets_blocks_unhook', $this );
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
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_block_assets' ) );
		add_filter( 'dr_widgets_blocks_settings', array( $this, 'get_blocks_settings' ) );
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );
	}

	/**
	 * Get block settings
	 *
	 * @since 1.0.0
	 */
	public function get_blocks_settings( $attributes = array() ) {
		return wp_parse_args(
			$attributes,
			array(
				'recipe-posts'          => array(
					'title' => __( 'Recipe Posts', 'dr-widgets-blocks' ),
				),
				'recipe-categories'     => array(
					'title' => __( 'Recipe Categories', 'dr-widgets-blocks' ),
				),
				'recipe-categories-tab' => array(
					'title' => __( 'Recipe Categories Tab', 'dr-widgets-blocks' ),
				),
				'recipe-posts-carousel' => array(
					'title' => __( 'Recipe Posts Carousel', 'dr-widgets-blocks' ),
				),
			)
		);
	}

	/**
	 * Register blocks
	 *
	 * @since 1.0.0
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$block_types = apply_filters( 'dr_widgets_blocks_settings', array() );
		foreach ( $block_types as $block => $args ) {
			register_block_type(
				__DIR__ . "/{$block}",
				array(
					'render_callback' => function( $attributes, $content ) use ( $block ) {
						return $this->render_block( $attributes, $content, $block );
					},
					'editor_script'   => 'dr-widgets-blocks-js',
				)
			);
		}
	}

	/**
	 * Render block
	 *
	 * @since 1.0.0
	 */
	public function render_block( $attributes, $content, $block ) {
		ob_start();
		include __DIR__ . "/{$block}/block.php";
		$block_render = ob_get_clean();
		return $block_render;
	}

	/**
	 * Enqueue block editor assets
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_editor_assets() {
		$block_deps = include_once plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/assets/build/blocks.asset.php';
		wp_register_script(
			'dr-widgets-blocks-js',
			plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/blocks.js',
			$block_deps['dependencies'],
			$block_deps['version'],
			true
		);
		wp_register_style( 'dr-widgets-blocks-css', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/blocks.css', array(), $block_deps['version'] );
		wp_register_style( 'dr-blocks-editor', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/editorCSS.css' );
		wp_register_style( 'swiper-bundle', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/css/swiper-bundle.min.css', array(), '8.1.4' );
		wp_register_script( 'swiper-bundle', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/js/swiper-bundle.js', array(), '8.1.4', true );
		$dr_widgets_blocks_deps                   = include plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/drWidgetsBlocks.asset.php';
		$dr_widgets_blocks_deps['dependencies'][] = 'swiper-bundle';
		wp_register_script( 'drWidgetsBlocks-common', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/drWidgetsBlocks.js', $dr_widgets_blocks_deps['dependencies'], $dr_widgets_blocks_deps['version'], true );
		wp_localize_script( 'dr-widgets-blocks-js', 'DRWB_Blocks', dr_widgets_blocks_get_disabled_blocks() );
	}

	/**
	 * Enqueue block assets
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_assets() {
		wp_register_style( 'dr-widgetsBlocks-layouts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css', array(), filemtime(plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/layouts.css') );
		wp_register_style( 'dr-widgetsBlocks-recipe-posts', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipePosts.css' );
		wp_register_style( 'dr-widgetsBlocks-recipe-categories', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeCategories.css' );
		wp_register_style( 'dr-widgetsBlocks-recipe-categories-tab', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipeCategoryTabs.css' );
		wp_register_style( 'swiper-bundle', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/css/swiper-bundle.min.css', array(), '8.1.4' );
		wp_register_script( 'swiper-bundle', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/js/swiper-bundle.js', array(), '8.1.4', true );
		$dr_widgets_blocks_deps                   = include plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/assets/build/drWidgetsBlocks.asset.php';
		$dr_widgets_blocks_deps['dependencies'][] = 'swiper-bundle';
		wp_register_script( 'drWidgetsBlocks-common', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/drWidgetsBlocks.js', $dr_widgets_blocks_deps['dependencies'], $dr_widgets_blocks_deps['version'], true );
		wp_register_style( 'dr-widgetsBlocks-recipe-posts-carousel', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/recipePostsCarousel.css' );
	}

}
