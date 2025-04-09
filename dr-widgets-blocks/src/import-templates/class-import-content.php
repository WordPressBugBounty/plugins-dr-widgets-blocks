<?php
/**
 * Elementor Importer
 *
 * @package DR_Widgets_Blocks
 */
namespace DR_Widgets_Blocks;

use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

/**
 * Elementor template library local source.
 *
 * Elementor template library local source handler class is responsible for
 * handling local Elementor templates saved by the user locally on their site.
 *
 */
class Import_Content extends Source_Local {

	/**
	 * Update post meta.
	 *
	 * @param  array   $data Elementor Data.
	 * @return array   $data Elementor Imported Data.
	 */
	public function import( $data = array()) {

		if ( ! empty( $data ) ) {

			// Set the Request's state as an Elementor upload request, in order to support unfiltered file uploads.
			Plugin::$instance->uploads_manager->set_elementor_upload_state( true );

			Plugin::$instance->uploads_manager->enable_unfiltered_files_upload();

			// Import the data.
			$data = $this->process_export_import_content( $data, 'on_import' );

			// Clear the cache after images are imported
			Plugin::$instance->posts_css_manager->clear_cache();

			Plugin::$instance->uploads_manager->set_elementor_upload_state( false );

			return $data;
		}

		return array();
	}
}
