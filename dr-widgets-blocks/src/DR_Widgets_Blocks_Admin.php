<?php
/**
 * Admin Class
 *
 * @package DR_Widgets_Blocks
 */

namespace DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

class DR_Widgets_Blocks_Admin {

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
		do_action( 'dr_widgets_blocks_admin_unhook', $this );
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'block_categories_all', array( $this, 'block_categories' ), 10, 2 );
		// Register pages.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( DR_WIDGETS_BLOCKS_PLUGIN_FILE ), array( $this, 'add_action_links' ) );
		add_filter( 'network_admin_plugin_action_links_' . plugin_basename( DR_WIDGETS_BLOCKS_PLUGIN_FILE ), array( $this, 'add_action_links' ) );
		/**
		 * Add custom templates import functionality if elementor is activated
		 */
		if( class_exists( 'Elementor\\Plugin' ) ){
			require_once plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/src/import-templates/elementor-import-templates.php';
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_dr-widgets-blocks' === $hook ) {
			$admin_deps = include_once plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/assets/build/dashboard.asset.php';
			wp_register_script( 'dr-widgets-blocks-admin', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/dashboard.js', $admin_deps['dependencies'], $admin_deps['version'], true );
			$ajax_nonce = wp_create_nonce( 'dr_widgets_blocks_ajax_nonce' );
			wp_localize_script(
				'dr-widgets-blocks-admin',
				'DRWidgetsBlocksAdmin',
				array(
					'ajax_nonce'         => $ajax_nonce,
					'is_pro_activated'   => function_exists( 'delisho_pro_init' ),
					'plugin_version'     => DR_WIDGETS_BLOCKS_VERSION,
					'siteURL'            => esc_url( admin_url() ),
					'website'            => esc_url( 'https://wpdelicious.com/delisho/?utm_source=delisho&utm_medium=dashboard&utm_campaign=upgrade_to_pro' ),
					'docs'               => esc_url( 'https://wpdelicious.com/docs-category/delisho/?utm_source=delisho&utm_medium=dashboard&utm_campaign=docs' ),
					'support'            => esc_url( 'https://wpdelicious.com//support-ticket/?utm_source=delisho&utm_medium=dashboard&utm_campaign=support' ),
					'videotutorial'      => esc_url( 'https://www.youtube.com/@wpdelicious' ),
					'bundle_pricing'     => esc_url( 'https://wpdelicious.com/bundle-pricing/?utm_source=delisho&utm_medium=dashboard&utm_campaign=upgrade_to_pro' ),
					'pricing'            => esc_url( 'https://wpdelicious.com/pricing/?utm_source=delisho&utm_medium=dashboard&utm_campaign=upgrade_to_pro' ),
					'keywords'           => esc_url( 'https://wpdelicious.com/keyword-research-for-food-bloggers/?utm_source=delisho&utm_medium=dashboard&utm_campaign=recipe_keywords' ),
					'get_pro'            => esc_url( 'https://wpdelicious.com/delisho/?utm_source=delisho&utm_medium=dashboard&utm_campaign=upgrade_to_pro#pricing' ),
					'yummy_bites_pro'    => esc_url( 'https://wpdelicious.com/wordpress-themes/yummy-bites-pro/?utm_source=delisho&utm_medium=dashboard&utm_campaign=pro_theme' ),
					'yummy_bites'        => esc_url( 'https://wpdelicious.com/wordpress-themes/yummy-bites/?utm_source=delisho&utm_medium=dashboard&utm_campaign=free_theme' ),
					'cookery'            => esc_url( 'https://blossomthemes.com/wordpress-themes/cookery/?utm_source=delisho&utm_medium=dashboard&utm_campaign=pro_theme' ),
					'cookery_lite'       => esc_url( 'https://blossomthemes.com/wordpress-themes/cookery-lite/?utm_source=delisho&utm_medium=dashboard&utm_campaign=free_theme' ),
					'blossom_recipe_pro' => esc_url( 'https://blossomthemes.com/wordpress-themes/blossom-recipe-pro/?utm_source=delisho&utm_medium=dashboard&utm_campaign=pro_theme' ),
					'blossom_recipe'     => esc_url( 'https://blossomthemes.com/wordpress-themes/blossom-recipe/?utm_source=delisho&utm_medium=dashboard&utm_campaign=free_theme' ),
					'vilva_pro'          => esc_url( 'https://blossomthemes.com/wordpress-themes/vilva-pro/?utm_source=delisho&utm_medium=dashboard&utm_campaign=pro_theme' ),
					'vilva'              => esc_url( 'https://blossomthemes.com/wordpress-themes/vilva/?utm_source=delisho&utm_medium=dashboard&utm_campaign=free_theme' ),
					'cook_recipe'        => esc_url( 'https://blossomthemes.com/wordpress-themes/cook-recipe/?utm_source=delisho&utm_medium=dashboard&utm_campaign=free_theme' ),
					'yummy_recipe'       => esc_url( 'https://blossomthemes.com/wordpress-themes/yummy-recipe/?utm_source=delisho&utm_medium=dashboard&utm_campaign=free_theme' ),
				)
			);
			wp_enqueue_script( 'dr-widgets-blocks-admin' );
			wp_enqueue_style( 'dr-widgets-blocks-admin', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/dashboard.css', array( 'wp-components' ) );

			wp_set_script_translations( 'dr-widgets-blocks-admin', 'dr-widgets-blocks' );
		}
	}

	/**
	 * Add block categories.
	 *
	 * @param array $categories
	 * @param array $post
	 * @return array
	 */
	public function block_categories( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'dr-widgets-blocks',
					'title' => __( 'Delisho', 'dr-widgets-blocks' ),
				),
			)
		);
	}

	/**
	 * Add Menu Page
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		$dr_admin_icon = base64_encode(
			'<svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.8421 2H8.21053C5.88512 2 4 3.88512 4 6.21053V13.4652C4 14.7104 4.96386 15.7719 6.19789 15.9385C7.889 16.1669 10.0323 16.4563 11.8876 16.4583C11.8996 16.4583 11.9108 16.4523 11.9178 16.4426C11.9218 16.4371 11.9257 16.4315 11.9289 16.4268C12.033 16.2757 12.1781 16.1843 12.3389 16.1843C12.3484 16.1843 12.3579 16.1851 12.3673 16.1859C12.3768 16.1867 12.3863 16.1875 12.3957 16.1875C12.4212 16.1875 12.4469 16.1812 12.4642 16.1625C12.5808 16.0361 12.7246 15.9565 12.8814 15.9461C12.9045 15.9446 12.9278 15.9466 12.9509 15.9481C12.9627 15.9489 12.9746 15.9497 12.9856 15.9513C12.9951 15.9529 13.0038 15.9544 13.0124 15.956C13.0282 15.9589 13.0438 15.9506 13.0506 15.936C13.2987 15.4043 13.7325 14.9281 14.2979 14.5404C15.2126 13.9137 16.4807 13.5263 17.8782 13.5263C19.2743 13.5263 20.5413 13.9129 21.4559 14.5386C21.4575 14.5397 21.4585 14.5416 21.4585 14.5435C21.4585 14.5455 21.4595 14.5474 21.4611 14.5485C22.3806 15.175 22.9474 16.0435 22.9474 17.0031C22.9474 17.9637 22.3764 18.8329 21.4585 19.4596C20.5437 20.0863 19.2756 20.4737 17.8782 20.4737C16.4807 20.4737 15.2158 20.0863 14.2979 19.4596C13.7282 19.0721 13.2921 18.5917 13.045 18.052C13.0416 18.0446 13.0335 18.0405 13.0256 18.0424C12.9993 18.0487 12.9722 18.0511 12.9452 18.0517C12.9359 18.0519 12.9263 18.0519 12.9162 18.0519C12.7451 18.0519 12.5878 17.9692 12.4623 17.8323C12.4463 17.8148 12.4197 17.8067 12.3961 17.8093C12.3943 17.8095 12.3948 17.8096 12.3931 17.8099C12.3779 17.8125 12.36 17.8125 12.3421 17.8125C12.1898 17.8125 12.0516 17.7278 11.949 17.5932C11.9374 17.578 11.9252 17.5632 11.9152 17.5469C11.912 17.5416 11.9063 17.5385 11.9001 17.5385C10.0211 17.5396 7.91267 17.8522 6.2384 18.1003C4.98184 18.2866 4 19.3746 4 20.6449V27.7895C4 30.1149 5.88512 32 8.21053 32H15.8421C24.1264 32 30.8421 25.2843 30.8421 17C30.8421 8.71573 24.1264 2 15.8421 2Z" fill="#A7AAAD"/>
		</svg>'
		);
		add_menu_page(
			esc_html__( 'Delisho', 'dr-widgets-blocks' ),
			'Delisho',
			'manage_options',
			'dr-widgets-blocks',
			array( $this, 'render_settings_page' ),
			'data:image/svg+xml;base64,' . $dr_admin_icon,
			30.2
		);
	}

	/**
	 * Render dashboard page
	 *
	 * @return void
	 */
	public function render_settings_page() {
		echo '<div id="drWidgetsBlocksAdminPageRoot" class="drWidgetsBlocksAdminPageRoot"></div>';
	}

	/**
	 * Add a link to the settings page to the plugins list
	 *
	 * @param array $links array of links for the plugins, adapted when the current plugin is found.
	 *
	 * @return array $links
	 */
	public function add_action_links( $links ) {

		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=dr-widgets-blocks' ) ) . '">' . esc_html__( 'Settings', 'dr-widgets-blocks' ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

}
