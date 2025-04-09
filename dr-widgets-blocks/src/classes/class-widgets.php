<?php
namespace DR_Widgets_Blocks;

use \Elementor\Plugin;
use \DR_Widgets_Blocks\Widget;
use \DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

class Widgets_Controller {

	protected static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'elementor/controls/register', array( $this, 'register_custom_control' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_categories' ) );
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );
		add_filter( 'dr_elementor_widgets_file_names', array( $this, 'get_allowed_widgets_name' ) );
	}

	public function add_elementor_categories( $elements_manager ) {
		$elements_manager->add_category(
			'deliciousrecipes',
			array(
				'title' => __( 'Delisho', 'dr-widgets-blocks' ),
			)
		);
	}

	/**
	 * Register Widgets.
	 *
	 * @since 1.0.0
	 */
	public function register_elementor_widgets( $widgets_manager ) {
		include_once DR_WIDGETS_BLOCKS_PLUGIN_PATH . 'src/classes/class-widget.php';

		$core_widgets = array( 
			'recipe-posts',
			'recipe-post-list-one',
			'recipe-post-list-two',
			'recipe-post-list-three', 
			'recipe-categories', 
			'recipe-categories-tab',
			'recipe-categories-tab-two',
			'recipe-posts-carousel', 
			'recipe-posts-carousel-two', 
			'recipe-posts-carousel-three', 
			'recipe-categories-two', 
			'recipe-categories-three', 
			'recipe-grid-one', 
			'recipe-grid-two',
			'recipe-grid-module-one',
			'recipe-grid-module-two',
			'recipe-advanced-heading'
		);
		$core_widgets = apply_filters( 'dr_elementor_widgets_file_names', $core_widgets );

		foreach ( $core_widgets as $core_widget ) {
			$core_widget = str_replace( 'dr-', '', $core_widget );

			if ( file_exists( DR_WIDGETS_BLOCKS_PLUGIN_PATH . "src/widgets/{$core_widget}/{$core_widget}.php" ) ) {
				include_once DR_WIDGETS_BLOCKS_PLUGIN_PATH . "src/widgets/{$core_widget}/{$core_widget}.php";
				$class_name = str_replace( '-', '_', $core_widget );
				$class_name = __NAMESPACE__ . "\Widget_{$class_name}";

				$widgets_manager->register( new $class_name() );
			}
		}

	}

	/**
	 * Register Custom Controls.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_control( $controls_manager ) {
		include_once DR_WIDGETS_BLOCKS_PLUGIN_PATH . 'src/controls/sortable-select.php';
		$sortable_select = __NAMESPACE__ . '\Elementor_Sortable_Select_Control';
		$controls_manager->register( new $sortable_select() );
	}

	/**
	 * Enqueue editor scripts.
	 */
	public function enqueue_editor_styles() {
		wp_register_style( 'dr-editor-icons', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/assets/build/iconsCSS.css' );
		wp_enqueue_style( 'dr-editor-icons' );
		wp_register_style( 'dr-widget-icons', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/assets/css/dr-widget-icons.css' );
		wp_enqueue_style( 'dr-widget-icons' );
	}

	/**
	 * Get allowed widgets name.
	 *
	 * @since 1.0.0
	 */
	public function get_allowed_widgets_name( $widgets ) {
		$all_widgets     = dr_widgets_blocks_get_widget_settings();
		$allowed_widgets = array_filter(
			$all_widgets,
			function( $value ) {
				return $value;
			}
		);
		$widgets = array_keys( $allowed_widgets );

		return $widgets;
	}

}

Widgets_Controller::instance();
