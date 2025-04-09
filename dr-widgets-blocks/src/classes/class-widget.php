<?php
namespace DR_Widgets_Blocks;

use \Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Widgets
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */
class Widget extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct( $data = array(), $args = array() ) {
		parent::__construct( $data, $args );
	}

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return $this->widget_name;
	}

	/**
	 * Get style dependencies.
	 */
	public function get_style_depends() {
		return array();
	}

	/**
	 * Get script dependencies.
	 */
	public function get_script_depends() {
		return array();
	}

	/**
	 * Widget categories.
	 */
	public function get_categories() {
		return array( 'deliciousrecipes' );
	}

}
