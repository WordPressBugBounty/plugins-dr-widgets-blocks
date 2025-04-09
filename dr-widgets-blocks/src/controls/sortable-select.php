<?php
namespace DR_Widgets_Blocks;

use \Elementor\Base_Data_Control;

defined( 'ABSPATH' ) || exit;

/**
 * Class Elementor_Sortable_Select_Control
 *
 * @package DR_Widgets_Blocks
 * @since 1.0.0
 */
class Elementor_Sortable_Select_Control extends Base_Data_Control {
	/**
	 * Get control type.
	 */
	public function get_type() {
		return 'drwb-sortable-select2';
	}

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {
		$editor_js_deps                   = include plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/assets/build/editorJS.asset.php';
		$editor_js_deps['dependencies'][] = 'jquery-elementor-select2';
		wp_register_script( 'drwb-sortable-select2', plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'assets/build/editorJS.js', $editor_js_deps['dependencies'], $editor_js_deps['version'], true );
		wp_enqueue_script( 'drwb-sortable-select2' );
	}

	/**
	 * Get control default values.
	 */
	protected function get_default_settings() {
		return array(
			'options'     => array(),
			'multiple'    => false,
			'label_block' => true,
			'separator'   => 'none',
			'default'     => array(),
		);
	}

	/**
	 * Render control output in the editor.
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<# var controlUID = '<?php echo esc_html( $control_uid ); ?>'; #>
		<# var currentID = elementor.panel.currentView.currentPageView.model.attributes.settings.attributes[data.name]; #>

		<div class="elementor-control-field">
			<# if ( data.label ) { #>
				<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>

			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php echo esc_attr( $control_uid ); ?>" {{ multiple }} class="elementor-control-custom-content-input" data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_value, option_key ) { #>
						<option value="{{ option_key }}">{{{ option_value }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<#
			( function( $ ) {
			$( document.body ).trigger( 'drwb_select2_init',{currentID:data.controlValue,data:data,controlUID:controlUID,multiple:data.multiple} );
			}( jQuery ) );
		#>
		<?php
	}

}
