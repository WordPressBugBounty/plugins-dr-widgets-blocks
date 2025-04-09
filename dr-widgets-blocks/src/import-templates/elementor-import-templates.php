<?php

namespace DR_Widgets_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Elementor_Import_Templates{

    /**
	 * Elementor_Import_Templates verison.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

    /**
	 * The single instance of the class.
	 *
	 * @var Elementor_Import_Templates|null
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	public $templates;

    /**
	 * Main Elementor_Import_Templates Instance.
	 *
	 * Ensures only one instance of Elementor_Import_Templates is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Elementor_Import_Templates - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    public function __construct(){
        require_once plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/import-templates/class-import-content.php';
        require plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/import-templates/class-template-design.php';
        $this->templates = Templates_Design::instance();
		add_action( 'wp_enqueue_scripts', [ $this, 'import_style' ], 988 );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'import_assets' ], 988 );
    }

    /**
     *  Enqueue style for the modal in the backend editor.
     *
     * @return void
     */
    public function import_style(){
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
            wp_enqueue_style(
                'cw-elementor-modal-admin',
                plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/import-templates/css/templates' . $suffix . '.css',
                array(),
                filemtime( plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/import-templates/css/templates.min.css' )
            );
        }
		
    }

    /**
     * Enqueue script for the modal in the backend editor.
     *
     * @return void
     */
    public function import_assets(){
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        
        wp_enqueue_script(
            'cw-elementor-modal-admin',
            plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/import-templates/js/templates' . $suffix . '.js',
            array('jquery') ,
            filemtime( plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . 'src/import-templates/js/templates.min.js' ),
            true
        );

        // Check Delisho Pro license key status
        $license_data   = get_option( 'delisho_pro_license', [] );
        $license_status = isset( $license_data['license_status'] ) ? $license_data['license_status'] : '';

        wp_localize_script(
            'cw-elementor-modal-admin',
            'delishoAdmin', 
            [
                'url'      => get_site_url(),
                'ajaxURL'  => admin_url( 'admin-ajax.php'),
                'nonce'    => wp_create_nonce( 'elementor_import_site' ),
                'activePlugin'    => $this->templates->get_active_plugins(),
                'demoList'        => $this->templates->get_server_list(), //@todo: we do not need this, instead we will add Free/Pro/All dropdown list
                'isLicenseActive' => 'valid' === $license_status ? true : false,
                'templatesText' => [
                    'header'  => __('Templates', 'dr-widgets-blocks'),
                    'back'    => __('Back', 'dr-widgets-blocks'),
                    'import'  => __('Import', 'dr-widgets-blocks'),
                    'loading' => __('Loading', 'dr-widgets-blocks'),
                    'stay'    => __('Importing content - kindly stay on this page.', 'dr-widgets-blocks'),
                ],
                'btnIcon' => '<svg width="103" height="114" viewBox="0 0 103 114" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M45.5 0H16.5C7.66344 0 0.5 7.16345 0.5 16V43.5677C0.5 48.2995 4.16265 52.3331 8.85195 52.9663C15.2782 53.8342 23.4227 54.934 30.473 54.9416C30.5187 54.9416 30.5611 54.9189 30.5877 54.8818C30.6027 54.8609 30.6177 54.8399 30.6297 54.8219C31.0252 54.2475 31.5766 53.9005 32.188 53.9005C32.2239 53.9005 32.2599 53.9034 32.2959 53.9064C32.3318 53.9094 32.3678 53.9124 32.4037 53.9124C32.5004 53.9124 32.5984 53.8887 32.6639 53.8176C33.1069 53.3372 33.6535 53.0345 34.2492 52.9953C34.3372 52.9895 34.4255 52.997 34.5134 53.0029C34.5584 53.0059 34.6033 53.0089 34.6453 53.0149C34.6812 53.0208 34.7142 53.0268 34.7471 53.0328C34.8072 53.0437 34.8665 53.0121 34.8923 52.9568C35.835 50.9363 37.4835 49.1266 39.6319 47.6535C43.1081 45.272 47.9268 43.8 53.237 43.8C58.5422 43.8 63.3569 45.2692 66.8324 47.6468C66.8385 47.651 66.8422 47.658 66.8422 47.6654C66.8422 47.6729 66.8459 47.68 66.8521 47.6842C70.3465 50.0652 72.5 53.3654 72.5 57.012C72.5 60.662 70.3304 63.965 66.8422 66.3465C63.366 68.728 58.5472 70.2 53.237 70.2C47.9268 70.2 43.1201 68.728 39.6319 66.3465C37.4673 64.8741 35.8101 63.0483 34.8711 60.9976C34.8581 60.9693 34.8275 60.9538 34.7972 60.9611C34.6974 60.9851 34.5943 60.9941 34.4917 60.9963C34.4564 60.9971 34.4199 60.9971 34.3816 60.9971C33.7313 60.9971 33.1335 60.683 32.6568 60.1626C32.5958 60.0961 32.4947 60.0653 32.4051 60.0755C32.3983 60.0762 32.4004 60.0763 32.3937 60.0775C32.3359 60.0876 32.2679 60.0876 32.2 60.0876C31.6211 60.0876 31.096 59.7657 30.706 59.254C30.6621 59.1964 30.6156 59.1401 30.5778 59.0782C30.5655 59.0582 30.5438 59.0464 30.5203 59.0464C23.3802 59.0506 15.3682 60.2382 9.00592 61.1813C4.23099 61.889 0.5 66.0235 0.5 70.8505V98C0.5 106.837 7.66344 114 16.5 114H45.5C76.9802 114 102.5 88.4802 102.5 57C102.5 25.5198 76.9802 0 45.5 0Z" fill="#FFFFFF"/>
                </svg>',
            ]
        );
        
    }
}
Elementor_Import_Templates::instance();