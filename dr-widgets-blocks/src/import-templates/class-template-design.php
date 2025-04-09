<?php
namespace DR_Widgets_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Templates_Design setup
 *
 * @since 1.0
 */
class Templates_Design {

    /**
	 * The single instance of the class.
	 *
	 * @var Templates_Design|null
	 * @since 1.0.0
	 */
	protected static $_instance = null;

    /**
	 * Main Templates_Design Instance.
	 *
	 * Ensures only one instance of Templates_Design is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Templates_Design - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_render_templates_designs', [ $this, 'render_templates_designs' ] );
		add_action( 'wp_ajax_process_data_for_import', [ $this, 'process_data_for_import' ] );
	}

    /*
    * Get the server list
    *
    * @return array
    */
    public function get_server_list(){
        return [
            [
                'slug' => 'travel-monster',
                'name' => esc_html__('Default', 'dr-widgets-blocks'),
            ],
        ];
    }

    /**
     * Get the templates array from the API
     *
     * @param string $server
     * @return array
     */
    public function get_templates_array(){
        $apiData =  wp_remote_get("https://demo.wpdelicious.com/widgets-blocks-demo/wp-json/delisho-elementor-templates/v1/patterns/");
       
        if( ! is_wp_error( $apiData ) && wp_remote_retrieve_response_code( $apiData ) == 200){
            $body = wp_remote_retrieve_body( $apiData );
            $data = json_decode( $body );
            $apiArray = [];
            if ( is_array($data)) {
                foreach ( $data as $demoAPI ){ 
                    array_push($apiArray, $demoAPI );
                } 
            }
            return (array) $apiArray;
        }
        return [];
    }

    /**
     * Get the category list from the templates array
     * with the count of each category
     *
     * @param array $data
     * @return array
     */
    protected function get_category_list($data){
        $get_cat_data = [];
        foreach( $data as $data_content){
            if( isset($data_content->cat)){
                foreach($data_content->cat as $key => $item){
                    $get_cat_data[$item->slug]['name'] = $item->name;
                    if (isset($get_cat_data[$item->slug]['count'])) {
                        $get_cat_data[$item->slug]['count']++;
                    } else {
                        $get_cat_data[$item->slug]['count'] = 1;
                    }
                }
            }
        }
        return $get_cat_data;
    }

    /**
     * Get the total count for the category
     *
     * @param array $cat_list
     * @return string
     */
    public function get_total_count_for_category($cat_list){
        $totalCount = 0;
        foreach($cat_list as $catData) {
            if(isset($catData['count'])) {
                $totalCount += $catData['count'];
            }
        }
        return absint($totalCount);
    }

    /**
     * Render the templates designs as per the requested server
     *
     * @return void
     */
	public function render_templates_designs() {
        $data     = $this->get_templates_array();
        $cat_list = $this->get_category_list($data);
		?>
        <div class="cw-pattern-library__content">
            <aside class="cw-pattern-library__sidebar">
                <div class='cw-pattern-library__sidebar-sticky'>
                    <div class="cw-search-icon">
                        <label for="cw-search-control" class="visually-hidden">
                            <?php esc_html_e('Search', 'dr-widgets-blocks'); ?>
                        </label>
                        <input type="search" id="cw-search-control" placeholder="<?php esc_attr_e('Search', 'dr-widgets-blocks'); ?>">
                        <i class="eicon-search"></i>
                    </div>
                    <div class="cw-pattern-library__category">
                        <ul class="cw-pattern-library__category-list">
                            <li>
                                <span class="cw-cat-item tab-active transform-scale" data-filter="all">
                                    <?php esc_html_e('All', 'dr-widgets-blocks'); ?>
                                    <span><?php echo $this->get_total_count_for_category($cat_list); ?></span>
                                </span>
                            </li>
                            <?php foreach( $cat_list as $slug => $cat_data){ 
                                $count = isset($cat_data['count']) ? $cat_data['count'] : '';
                                ?>
                                <li>
                                    <span class="cw-cat-item transform-scale" data-filter="<?php echo esc_attr($slug); ?>">
                                        <?php echo esc_html($cat_data['name']); ?>
                                        <span><?php echo esc_html($count) ; ?></span>
                                    </span>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </aside>
            <div class="cw-pattern-library__main">
                <div class="cw-pattern-library__topbar">
                    <div class="demo-list">
                        <select class="demo-list_dropdown">
                            <option value="all"><?php esc_html_e('All', 'dr-widgets-blocks'); ?></option>
                            <option value="free"><?php esc_html_e('Free', 'dr-widgets-blocks'); ?></option>
                            <option value="pro"><?php esc_html_e('Pro', 'dr-widgets-blocks'); ?></option>
                        </select>
                    </div>
                    <div class='cw-pattern-library__btn-group'>
                        <button class="cw-info-btn cw-pattern-library__btn" aria-label="<?php esc_attr_e('Info Icon', 'dr-widgets-blocks'); ?>" data-text="<?php esc_attr_e("Imported templates will inherit the theme's settings.", 'dr-widgets-blocks'); ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5903 10.6919C12.5636 10.5444 12.4826 10.4122 12.3633 10.3214C12.244 10.2306 12.095 10.1877 11.9457 10.2013C11.7964 10.2148 11.6575 10.2838 11.5565 10.3946C11.4555 10.5054 11.3996 10.65 11.3999 10.7999V16.2023L11.4095 16.3103C11.4362 16.4578 11.5172 16.59 11.6365 16.6808C11.7558 16.7717 11.9048 16.8145 12.0541 16.801C12.2034 16.7874 12.3423 16.7184 12.4433 16.6076C12.5443 16.4968 12.6002 16.3522 12.5999 16.2023V10.7999L12.5903 10.6919ZM12.9587 8.0999C12.9587 7.86121 12.8639 7.63229 12.6951 7.46351C12.5263 7.29472 12.2974 7.1999 12.0587 7.1999C11.82 7.1999 11.5911 7.29472 11.4223 7.46351C11.2535 7.63229 11.1587 7.86121 11.1587 8.0999C11.1587 8.3386 11.2535 8.56752 11.4223 8.7363C11.5911 8.90508 11.82 8.9999 12.0587 8.9999C12.2974 8.9999 12.5263 8.90508 12.6951 8.7363C12.8639 8.56752 12.9587 8.3386 12.9587 8.0999ZM21.5999 11.9999C21.5999 9.45382 20.5885 7.01203 18.7881 5.21168C16.9878 3.41133 14.546 2.3999 11.9999 2.3999C9.45382 2.3999 7.01203 3.41133 5.21168 5.21168C3.41133 7.01203 2.3999 9.45382 2.3999 11.9999C2.3999 14.546 3.41133 16.9878 5.21168 18.7881C7.01203 20.5885 9.45382 21.5999 11.9999 21.5999C14.546 21.5999 16.9878 20.5885 18.7881 18.7881C20.5885 16.9878 21.5999 14.546 21.5999 11.9999ZM3.5999 11.9999C3.5999 10.8968 3.81717 9.8045 4.23931 8.78536C4.66145 7.76623 5.28019 6.84022 6.06021 6.06021C6.84022 5.28019 7.76623 4.66145 8.78536 4.23931C9.8045 3.81717 10.8968 3.5999 11.9999 3.5999C13.103 3.5999 14.1953 3.81717 15.2144 4.23931C16.2336 4.66145 17.1596 5.28019 17.9396 6.06021C18.7196 6.84022 19.3384 7.76623 19.7605 8.78536C20.1826 9.8045 20.3999 10.8968 20.3999 11.9999C20.3999 14.2277 19.5149 16.3643 17.9396 17.9396C16.3643 19.5149 14.2277 20.3999 11.9999 20.3999C9.77208 20.3999 7.63551 19.5149 6.06021 17.9396C4.4849 16.3643 3.5999 14.2277 3.5999 11.9999Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <span class="divider"></span>
                        <button class="cw-layout-three transform-scale active cw-pattern-library__btn" aria-label="<?php esc_attr_e('Change pattern\'s layout into 3 columns', 'dr-widgets-blocks'); ?>" >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20.1819 9.54552H16.9091C16.091 9.54552 16.091 9.54552 16.091 10.3636V13.6364C16.091 14.4545 16.091 14.4545 16.9091 14.4545H20.1819C21 14.4545 21 14.4545 21 13.6364V10.3636C21 9.54552 21 9.54552 20.1819 9.54552ZM13.6364 3H10.3636C9.54552 3 9.54552 3 9.54552 3.8181V7.09086C9.54552 7.90896 9.54552 7.90896 10.3636 7.90896H13.6364C14.4545 7.90896 14.4545 7.90896 14.4545 7.09086V3.8181C14.4545 3 14.4545 3 13.6364 3ZM20.1819 16.0909H16.9091C16.091 16.0909 16.091 16.0909 16.091 16.909V20.1817C16.091 20.9998 16.091 20.9998 16.9091 20.9998H20.1819C21 21 21 21 21 20.1819V16.9091C21 16.0909 21 16.0909 20.1819 16.0909ZM13.6364 9.54552H10.3636C9.54552 9.54552 9.54552 9.54552 9.54552 10.3636V13.6364C9.54552 14.4545 9.54552 14.4545 10.3636 14.4545H13.6364C14.4545 14.4545 14.4545 14.4545 14.4545 13.6364V10.3636C14.4545 9.54552 14.4545 9.54552 13.6364 9.54552ZM7.09086 3H3.8181C3 3 3 3 3 3.8181V7.09086C3 7.90896 3 7.90896 3.8181 7.90896H7.09086C7.90896 7.90896 7.90896 7.90896 7.90896 7.09086V3.8181C7.90914 3 7.90914 3 7.09086 3ZM13.6364 16.0909H10.3636C9.54552 16.0909 9.54552 16.0909 9.54552 16.909V20.1817C9.54552 20.9998 9.54552 20.9998 10.3636 20.9998H13.6364C14.4545 20.9998 14.4545 20.9998 14.4545 20.1817V16.9091C14.4545 16.0909 14.4545 16.0909 13.6364 16.0909ZM7.09086 9.54552H3.8181C3 9.54552 3 9.54552 3 10.3636V13.6364C3 14.4545 3 14.4545 3.8181 14.4545H7.09086C7.90896 14.4545 7.90896 14.4545 7.90896 13.6364V10.3636C7.90914 9.54552 7.90914 9.54552 7.09086 9.54552ZM7.09086 16.0909H3.8181C3 16.0909 3 16.0909 3 16.9091V20.1819C3 21 3 21 3.8181 21H7.09086C7.90896 21 7.90896 21 7.90896 20.1819V16.9091C7.90914 16.0909 7.90914 16.0909 7.09086 16.0909ZM20.1819 3H16.9091C16.091 3 16.091 3 16.091 3.8181V7.09086C16.091 7.90896 16.091 7.90896 16.9091 7.90896H20.1819C21 7.90896 21 7.90896 21 7.09086V3.8181C21 3 21 3 20.1819 3Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <button class="cw-layout-two transform-scale cw-pattern-library__btn" aria-label="<?php esc_attr_e('Change pattern\'s layout into 2 columns', 'dr-widgets-blocks'); ?>" >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4.44133C11 3.64533 10.3547 3 9.55867 3H4.44133C3.64533 3 3 3.64533 3 4.44133V9.55867C3 10.3547 3.64533 11 4.44133 11H9.55867C10.3547 11 11 10.3547 11 9.55867V4.44133Z" fill="currentColor"/>
                                <path d="M21 4.44133C21 3.64533 20.3547 3 19.5587 3H14.4413C13.6453 3 13 3.64533 13 4.44133V9.55867C13 10.3547 13.6453 11 14.4413 11H19.5587C20.3547 11 21 10.3547 21 9.55867V4.44133Z" fill="currentColor"/>
                                <path d="M11 14.4413C11 13.6453 10.3547 13 9.55867 13H4.44133C3.64533 13 3 13.6453 3 14.4413V19.5587C3 20.3547 3.64533 21 4.44133 21H9.55867C10.3547 21 11 20.3547 11 19.5587V14.4413Z" fill="currentColor"/>
                                <path d="M21 14.4413C21 13.6453 20.3547 13 19.5587 13H14.4413C13.6453 13 13 13.6453 13 14.4413V19.5587C13 20.3547 13.6453 21 14.4413 21H19.5587C20.3547 21 21 20.3547 21 19.5587V14.4413Z" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="cw-pattern-library__design">
                    <div class="cw-pattern-library__design-wrap">
                    <ul class="cw-pattern-library__design-list">
                        <?php foreach( $data as $item){ 
                            $categories_list = $this->filter_cat_slug($item);
                            $data_filter     = isset($item->meta->freevspro) ? $item->meta->freevspro : '';
                            ?>
                            <li class='cw-pattern-library__design-item' data-filter="<?php echo esc_attr($categories_list); ?>" data-filter-name="<?php echo esc_attr($item->title); ?>" data-filter-plan="<?php echo esc_attr($data_filter); ?>">
                                <?php if($data_filter === '1') { ?>
                                    <div class="cw-pattern-library-badge">
                                        <span>
                                            <?php esc_html_e('Pro', 'dr-widgets-blocks'); ?>
                                        </span>
                                    </div>
                                <?php } ?>
                                <?php if(isset($item->featured_media)){ ?>
                                    <div class="cw-pattern-library__design-item-img">
                                        <button class="cw-pattern-library__design-preview-btn" data-preview-url="<?php echo esc_url($item->permalink); ?>" data-slug-id="<?php echo esc_attr($item->id); ?>">
                                            <span>
                                                <i aria-hidden="true" class="fas fa-eye"></i>
                                            </span>
                                            <?php esc_html_e('Click to Preview', 'dr-widgets-blocks'); ?>
                                        </button>
                                        <img src="<?php echo esc_url($item->featured_media); ?>" height="<?php echo esc_attr($item->img_height); ?>" width="<?php echo esc_attr($item->img_width); ?>" loading="lazy"/>
                                    </div>
                                <?php } else { ?>
                                    <div class="cw-pattern-library__design-item-img no-featured-img">
                                        <button class="cw-pattern-library__design-preview-btn" data-preview-url="<?php echo esc_url($item->permalink); ?>" data-slug-id="<?php echo esc_attr($item->id); ?>">
                                            <span>
                                                <i aria-hidden="true" class="fas fa-eye"></i>
                                            </span>
                                            <?php esc_html_e('Click to Preview', 'dr-widgets-blocks'); ?>
                                        </button>
                                        <span>
                                            <i aria-hidden="true" class="fas fa-image"></i>
                                        </span>
                                    </div>
                                <?php } ?>
                                <div class="cw-pattern-library__design-item-info">
                                    <span><?php echo esc_html($item->title); ?></span>
                                    <button class="cw-insert-temp transform-scale" data-slug-id="<?php echo esc_attr($item->id); ?>"><?php esc_html_e('Import', 'dr-widgets-blocks'); ?></button>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                    </div>
                </div>
            </div>
        </div>

		<?php wp_die();
	}

    /**
     * Process the data for import
     *
     * @return void
     */
    public function process_data_for_import() {

        // Verify Nonce.
        \check_ajax_referer( 'elementor_import_site', 'nonce' );

        if ( ! \current_user_can( 'edit_posts' ) ) {
            \wp_send_json_error( __( 'You are not allowed to perform this action', 'dr-widgets-blocks' ) );
        }

        $apiURL = isset( $_POST['apiURL'] ) ? \esc_url_raw( $_POST['apiURL'] ) : '';
        $response = \wp_safe_remote_get( $apiURL );

        if ( \is_wp_error( $response ) ) {
            \wp_send_json_error( \wp_remote_retrieve_body( $response ) );
        }

        $body = \wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        $meta = json_decode( $data['content'], true);

        if ( empty( $meta ) ) {
            \wp_send_json_error( __( 'No Content Found', 'dr-widgets-blocks' ) );
        }

        $import      = new \DR_Widgets_Blocks\Import_Content();
        $import_data = $import->import( $meta['content'] );
        \wp_send_json_success( $import_data );
    }

    /**
     * Filter category slug
     * 
     * @param object $item
     * @return string
     */
    public function filter_cat_slug($item){
        $cat_slug = [];

        if( isset($item->cat)){
            foreach($item->cat as $key => $cat){
                $cat_slug[] = $cat->slug;
            }
        }
        
        return implode(' ', $cat_slug);
    }

    /**
     * Get the active plugins on current WordPress installation
     *
     * @return array
     */
    public function get_active_plugins() {
        $active_plugins = get_plugins();
        $plugins = array();
    
        foreach ($active_plugins as $key => $plugin) {
            if ( is_plugin_active( $key ) ) {
                $extract = explode( '/', $key );
                $path    = ABSPATH . 'wp-content/plugins/' . $key;
                $plugin_data = get_plugin_data($path);
                $plugins[] = array(
                    'name'    => $plugin_data['Name'],
                    'slug'    => $extract[0],
                    'version' => $plugin_data['Version'],
                );
            }
        }
    
        return $plugins;
    }
}