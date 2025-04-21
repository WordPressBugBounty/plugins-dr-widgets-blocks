<?php
/**
 * Main DR_Widgets_Blocks Class
 *
 * @package DR_Widgets_Blocks
 */
namespace DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Main DR_Widgets_Blocks Class
 *
 * @class DR_Widgets_Blocks
 */
#[\AllowDynamicProperties]
final class DR_Widgets_Blocks {
	/**
	 * DR_Widgets_Blocks version.
	 *
	 * @var string
	 */
	public $version = '1.0.9';

	/**
	 * The single instance of the class.
	 *
	 * @var DR_Widgets_Blocks
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Admin settings instance.
	 *
	 * @var DR_Widgets_Blocks_Admin_Settings
	 * @since 1.0.0
	 */
	public $admin_settings;

	/**
	 * Public instance.
	 *
	 * @var DR_Widgets_Blocks_Public
	 * @since 1.0.0
	 */
	public $public;

	/**
	 * Blocks instance.
	 *
	 * @var DR_Widgets_Blocks_Blocks
	 * @since 1.0.0
	 */
	public $blocks;

	/**
	 * Styles instance.
	 *
	 * @var DR_Widgets_Blocks_Styles
	 * @since 1.0.0
	 */
	public $styles;

	/**
	 * Main DR_Widgets_Blocks Instance.
	 *
	 * Ensures only one instance of DR_Widgets_Blocks is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return DR_Widgets_Blocks - Main instance.
	 */
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
		$this->define_constants();
		$this->init_hooks();

		add_action( 'delicious_recipes_free_loaded', array( $this, 'includes' ) );
	}

	/**
	 * Define DR_Widgets_Blocks Constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		$this->define( 'DR_WIDGETS_BLOCKS_PLUGIN_NAME', 'dr-widgets-blocks' );
		$this->define( 'DR_WIDGETS_BLOCKS_VERSION', $this->version );
		$this->define( 'DR_WIDGETS_BLOCKS_ABSPATH', dirname( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/' );
		$this->define( 'DR_WIDGETS_BLOCKS_PLUGIN_URL', $this->plugin_url() );
		$this->define( 'DR_WIDGETS_BLOCKS_PLUGIN_BASENAME', plugin_basename( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) );
		$this->define( 'DR_WIDGETS_BLOCKS_PLUGIN_PATH', plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) );
		$this->define( 'DR_WIDGETS_BLOCKS_TEMPLATE_DEBUG_MODE', false );
		$this->define( 'DRWB_MINIMUM_DELICIOUS_RECIPES_VERSION', '1.3.7' );
		$this->define( 'DRWB_MINIMUM_ELEMENTOR_VERSION', '2.5.0' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.0.0
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		if ( $this->meets_requirements() ) {
			$this->admin_settings = new DR_Widgets_Blocks_Admin();
			require_once plugin_dir_path( __FILE__ ) . '/classes/class-widgets.php';
			require_once plugin_dir_path( __FILE__ ) . '/blocks/class-blocks.php';
			require_once plugin_dir_path( __FILE__ ) . '/classes/class-fonts-manager.php';
			require_once plugin_dir_path( __FILE__ ) . '/helpers/class-block-helpers.php';
			$this->styles = new DR_Widgets_Blocks_Styles();
			$this->blocks = new Blocks();

			if ( $this->is_request( 'frontend' ) ) {
				// Public instances.
				$this->public = new DR_Widgets_Blocks_Public();
			}
		}
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'dr-widgets-blocks', false, dirname( DR_WIDGETS_BLOCKS_PLUGIN_BASENAME ) . '/languages/' );
	}

	/**
	 * Get the plugin URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', DR_WIDGETS_BLOCKS_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Get the template path.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'drwb_template_path', '/dr-widgets-blocks/' );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Output error message and disable plugin if requirements are not met.
	 *
	 * This fires on admin_notices.
	 *
	 * @since 1.0.0
	 */
	public function maybe_disable_plugin() {
		if ( ! $this->meets_requirements() ) {
			if ( ! function_exists( 'DEL_RECIPE' ) ) {
				if ( file_exists( WP_PLUGIN_DIR . '/delicious-recipes/delicious-recipes.php' ) ) {
					$notice_title = __( 'Activate WP Delicious', 'dr-widgets-blocks' );
					$notice_url   = wp_nonce_url( 'plugins.php?action=activate&plugin=delicious-recipes/delicious-recipes.php&plugin_status=all&paged=1', 'activate-plugin_delicious-recipes/delicious-recipes.php' );
				} else {
					$notice_title = __( 'Install WP Delicious', 'dr-widgets-blocks' );
					$notice_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=delicious-recipes' ), 'install-plugin_delicious-recipes' );
				}
				$notice = wp_kses_post(
					sprintf(
					/* translators: 1: Plugin name, 2: WP Delicious, 3: WP Delicious installation link. */
						__( '%1$s requires %2$s to be installed and activated to function properly. %3$s', 'dr-widgets-blocks' ),
						'<p style="margin: 0 0 8px"><strong>' . __( 'Delisho', 'dr-widgets-blocks' ) . '</strong>',
						'<strong>' . __( 'WP Delicious', 'dr-widgets-blocks' ) . '</strong>',
						'</p><a class="button button-primary button-large" href="' . esc_url( $notice_url ) . '">' . $notice_title . '</a>'
					)
				);
			} elseif ( ! defined( 'DELICIOUS_RECIPES_VERSION' ) || version_compare( DELICIOUS_RECIPES_VERSION, DRWB_MINIMUM_DELICIOUS_RECIPES_VERSION, '<' ) ) {
				$notice = sprintf(
					/* translators: 1: Plugin name, 2: WP Delicious, 3: Required WP Delicious version. */
					__( '"%1$s" requires "%2$s" version %3$s or greater.', 'dr-widgets-blocks' ),
					'<strong>' . __( 'Delisho', 'dr-widgets-blocks' ) . '</strong>',
					'<strong>' . __( 'WP Delicious', 'dr-widgets-blocks' ) . '</strong>',
					DRWB_MINIMUM_DELICIOUS_RECIPES_VERSION
				);
			} elseif ( ! did_action( 'elementor/loaded' ) ) {
				if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
					$notice_title = __( 'Activate Elementor', 'dr-widgets-blocks' );
					$notice_url   = wp_nonce_url( 'plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1', 'activate-plugin_elementor/elementor.php' );
				} else {
					$notice_title = __( 'Install Elementor', 'dr-widgets-blocks' );
					$notice_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
				}
				$notice = wp_kses_post(
					sprintf(
					/* translators: 1: Plugin name, 2: Elementor, 3: Elementor installation link. */
					__( '%1$s requires %2$s to be installed and activated to function properly. %3$s', 'dr-widgets-blocks' ),
						'<p style="margin: 0 0 8px"><strong>' . __( 'Delisho', 'dr-widgets-blocks' ) . '</strong>',
						'<strong>' . __( 'Elementor', 'dr-widgets-blocks' ) . '</strong>',
						'</p><a class="button button-primary button-large" href="' . esc_url( $notice_url ) . '">' . $notice_title . '</a>'
					)
				);
			} elseif ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, DRWB_MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
				$notice = sprintf(
					/* translators: 1: Plugin name, 2: Elementor, 3: Required Elementor version. */
					__( '"%1$s" requires "%2$s" version %3$s or greater.', 'dr-widgets-blocks' ),
					'<strong>' . __( 'Delisho', 'dr-widgets-blocks' ) . '</strong>',
					'<strong>' . __( 'Elementor', 'dr-widgets-blocks' ) . '</strong>',
					DRWB_MINIMUM_ELEMENTOR_VERSION
				);
			}
			printf( '<div class="notice notice-warning is-dismissible" style="padding: 13px 16px">%1$s</div>',  wp_kses_post($notice) );
		}
	}

	/**
	 * Check if all plugin requirements are met.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if requirements are met, otherwise false.
	 */
	private function meets_requirements() {
		return ( function_exists( 'DEL_RECIPE' ) && defined( 'DELICIOUS_RECIPES_VERSION' )
			&& version_compare( DELICIOUS_RECIPES_VERSION, DRWB_MINIMUM_DELICIOUS_RECIPES_VERSION, '>=' )
			&& did_action( 'elementor/loaded' ) && defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, DRWB_MINIMUM_ELEMENTOR_VERSION, '>=' ) );
	}
}
