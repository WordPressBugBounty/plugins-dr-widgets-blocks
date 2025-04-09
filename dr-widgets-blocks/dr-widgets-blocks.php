<?php
/**
 * Plugin Name:     	Delisho
 * Plugin URI:      	https://wpdelicious.com/delisho/
 * Description:     	An Elementor Widget and Gutenberg Blocks plugin for WP Delicious that include 10+ widgets and 4 Gutenberg blocks to create beautiful and interactive recipe blogs with a quick drag-and-drop.
 * Author:          	WP Delicious
 * Author URI:      	https://wpdelicious.com
 * License:         	GPLv3 or later
 * License URI:     	https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:     	dr-widgets-blocks
 * Domain Path:     	/languages
 * Version:         	1.1.1
 * Requires at least: 	5.5
 * Tested up to: 		6.7
 * Requires PHP: 		7.4
 *
 * @package         DR_Widgets_Blocks
 */

use DR_Widgets_Blocks\DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'DR_WIDGETS_BLOCKS_PLUGIN_FILE' ) ) {
	define( 'DR_WIDGETS_BLOCKS_PLUGIN_FILE', __FILE__ );
}

// include the autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Main instance of dr_widgets_blocks_init.
 *
 * Returns the main instance of dr_widgets_blocks_init.
 *
 * @since  1.0.0
 * @return dr_widgets_blocks_init
 */
function dr_widgets_blocks_init() {
	return DR_Widgets_Blocks::instance();
}

// Run the show.
dr_widgets_blocks_init();
