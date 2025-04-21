<?php
/**
 * Ajax of DR_Widgets_Blocks.
 *
 * @package DR_Widgets_Blocks
 */

namespace DR_Widgets_Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Ajax class
 *
 * @package DR_Widgets_Blocks
 */
class DR_Widgets_Blocks_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {
		// Initialize hooks.
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_dr_widgets_blocks_get_image_sizes', array( $this, 'get_image_sizes' ) );
		add_action( 'wp_ajax_dr_widgets_blocks_get_recipe_posts', array( $this, 'get_recipe_posts' ) );

		add_action( 'wp_ajax_dr_widgets_blocks_get_block_settings', array( $this, 'get_block_settings' ) );
		add_action( 'wp_ajax_dr_widgets_blocks_save_block_settings', array( $this, 'save_block_settings' ) );

		add_action( 'wp_ajax_dr_widgets_blocks_get_widget_settings', array( $this, 'get_widget_settings' ) );
		add_action( 'wp_ajax_dr_widgets_blocks_save_widget_settings', array( $this, 'save_widget_settings' ) );

		add_action( 'wp_ajax_dr_widgets_blocks_get_latest_changelog', array( $this, 'get_latest_changelog' ) );

		//Ajax Pagination
		add_action( 'wp_ajax_dr_widgets_blocks_recipe_pagination', array( $this,'recipe_pagination') );
		add_action( 'wp_ajax_nopriv_dr_widgets_blocks_recipe_pagination', array( $this,'recipe_pagination' ) );

		//Ajax Pagination for Recipe Grid Two
		add_action( 'wp_ajax_dr_widgets_blocks_recipe_pagination_two', array( $this,'recipe_pagination_two') );
		add_action( 'wp_ajax_nopriv_dr_widgets_blocks_recipe_pagination_two', array( $this,'recipe_pagination_two' ) );
		
		//Ajax Pagination for Recipe Post List One
		add_action( 'wp_ajax_dr_widgets_blocks_recipe_post_list_one', array( $this,'recipe_post_list_one') );
		add_action( 'wp_ajax_nopriv_dr_widgets_blocks_recipe_post_list_one', array( $this,'recipe_post_list_one' ) );

		//Ajax Pagination for Recipe Post List Two
		add_action( 'wp_ajax_dr_widgets_blocks_recipe_post_list_two', array( $this,'recipe_post_list_two') );
		add_action( 'wp_ajax_nopriv_dr_widgets_blocks_recipe_post_list_two', array( $this,'recipe_post_list_two' ) );
		
		//Ajax Pagination for Recipe Post List Three
		add_action( 'wp_ajax_dr_widgets_blocks_recipe_post_list_three', array( $this,'recipe_post_list_three') );
		add_action( 'wp_ajax_nopriv_dr_widgets_blocks_recipe_post_list_three', array( $this,'recipe_post_list_three' ) );
	}

	/**
	 * Recipe pagination.
	 *
	 * @since 1.1.2
	 * @access public
	 *
	 * @return void
	 */
	public function recipe_pagination() {

		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'recipe_grid_one_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'dr-widgets-blocks' ) );
			die;
		}

		ob_start();
		$paged = isset( $_POST['paged'] ) ? $_POST['paged'] : 1;
		$layout = isset( $_POST['layout'] ) ? esc_attr( $_POST['layout'] ) : 'layout-1';
		$per_page   = isset( $_POST['postsPerPage'] ) ? $_POST['postsPerPage'] : 3;
		$recipe_ids = isset( $_POST['exclude'] ) ? $_POST['exclude'] : false;
		$order_by   = isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'date';
		$order      = isset( $_POST['order'] ) ? $_POST['order'] : 'DESC';
		$offset     = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;

		if ( $paged > 1 ) {
			$final_offset = ( ( $paged - 1 ) * intval( $per_page ) ) + intval( $offset );
		} else {
			$final_offset = intval( $offset );
		}

		if(isset( $_POST['filterBy'] )){
			$orderby = $_POST['filterBy'] === 'rand' ? 'rand' : ($_POST['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'paged'            => $paged,
			'offset'           => $final_offset,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( isset($_POST['filterBy']) && $_POST['filterBy'] === 'popular' || 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$taxonomy           = isset( $_POST['taxonomy'] ) && '' !== $_POST['taxonomy'] ? $_POST['taxonomy'] : 'recipe-course';
		$terms              = $taxonomy ? ( isset( $_POST[ "terms" ] ) ? $_POST[ "terms" ] : '' ) : '';
		$all_taxonomy       = isset( $_POST['all_taxonomy'] ) && 'yes' === $_POST['all_taxonomy'] ? true : false;
		$all_term_id        = isset( $_POST['all_term_id'] ) ? $_POST['all_term_id'] : [];
		$per_row        	= isset( $_POST['recipesPerRow'] ) ? absint( $_POST['recipesPerRow'] ) : 3;
		$per_row_tablet 	= isset( $_POST['recipesPerRow_tablet'] ) ? absint( $_POST['recipesPerRow_tablet'] ) : 2;
		$per_row_mobile 	= isset( $_POST['recipesPerRow_mobile'] ) ? absint( $_POST['recipesPerRow_mobile'] ) : 1;
		$layout         	= isset( $_POST['layout'] ) ? $_POST['layout'] : 'layout-1';
		$show_pagination    = isset( $_POST['showPagination'] ) && 'yes'   === $_POST['showPagination'] ? true : false;
		$pagination_type    = isset( $_POST['paginationType'] ) ? $_POST['paginationType'] : 'number';
		$show_feature_image = isset( $_POST['showFeatureImage'] ) && 'yes' === $_POST['showFeatureImage'] ? true : false;
		$image_size         = isset( $_POST['imageSize'] ) ? $_POST['imageSize'] : 'delrecpe-structured-data-4_3';
		$image_size_l2      = isset( $_POST['imageSizel2'] ) ? $_POST['imageSizel2'] : 'delrecpe-structured-data-1_1';
		$image_custom_size  = isset( $_POST['imageCustomSize'] ) ? $_POST['imageCustomSize'] : false;
		$image_size         = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$show_title         = isset( $_POST['showTitle'] ) && 'yes' === $_POST['showTitle'] ? true : false;
		$title_tag          = isset( $_POST['headingTag'] ) ? $_POST['headingTag'] : 'h3';
		$show_total_time    = isset( $_POST['showTotalTime'] ) && 'yes' === $_POST['showTotalTime'] ? true : false;
		$show_difficulty    = isset( $_POST['showDifficulty'] ) && 'yes' === $_POST['showDifficulty'] ? true : false;
		$show_recipe_keys   = isset( $_POST['showRecipeKeys'] ) && 'yes' === $_POST['showRecipeKeys'] ? true : false;
		$show_excerpt       = isset( $_POST['showExcerpt'] ) && 'yes' === $_POST['showExcerpt'] ? true : false;
		$show_author        = isset( $_POST['showAuthor'] ) && 'yes' === $_POST['showAuthor'] ? true : false;
		$show_publish_date  = isset( $_POST['showPublishDate'] ) && 'yes' === $_POST['showPublishDate'] ? true : false;
		$show_rating        = isset( $_POST['showRating'] ) && 'yes' === $_POST['showRating'] ? true : false;
		$show_comment       = isset( $_POST['showComment'] ) && 'yes' === $_POST['showComment'] ? true : false;
		$show_category      = isset( $_POST['showCategory'] ) && 'yes' === $_POST['showCategory'] ? true : false;
		$excerpt_length     = isset( $_POST['excerptLength'] ) ? $_POST['excerptLength'] : 20;
		$image_alignment    = isset( $_POST['imageAlignment'] ) ? $_POST['imageAlignment'] : 'left';
		$separator          = isset( $_POST['separator'] ) ? $_POST['separator'] : 'dot';
		$show_wishlist      = isset( $_POST['showBookmark'] ) && 'yes' === $_POST['showBookmark'] ? true : false;
		$prev_text          = isset( $_POST['prevText'] ) ? $_POST['prevText'] : esc_html__( 'Previous', 'dr-widgets-blocks' );
		$next_text          = isset( $_POST['nextText'] ) ? $_POST['nextText'] : esc_html__( 'Next', 'dr-widgets-blocks' );
		$load_text          = isset( $_POST['loadText'] ) ? $_POST['loadText'] : __( 'Load More', 'dr-widgets-blocks' );


		$widget_data = array(
			'layout'  			  => $layout,
			'show_feature_image'  => $show_feature_image,
			'image_size'          => $image_size,
			'image_size_l2'       => $image_size_l2,
			'show_title'          => $show_title,
			'title_tag'           => $title_tag,
			'show_total_time'     => $show_total_time,
			'show_difficulty'     => $show_difficulty,
			'show_recipe_keys'    => $show_recipe_keys,
			'show_excerpt'        => $show_excerpt,
			'show_author'         => $show_author,
			'show_publish_date'   => $show_publish_date,
			'show_rating'         => $show_rating,
			'show_comment'        => $show_comment,
			'show_category'       => $show_category,
			'excerpt_length'      => $excerpt_length,
			'image_alignment'     => $image_alignment,
			'separator'           => $separator,
			'show_wishlist'       => $show_wishlist,
			'pagination_type'     => $pagination_type,
			'current_page'        => $paged,
			'pagination_type'     => $pagination_type,
			'show_pagination'     => $show_pagination,
			'per_row'			  => $per_row,
			'per_row_tablet'	  => $per_row_tablet,
			'per_row_mobile'	  => $per_row_mobile,
			'taxonomy'            => $taxonomy,
			'terms'               => $terms,
			'all_taxonomy'        => $all_taxonomy,
			'all_term_id'         => $all_term_id,
			'args' 				  => $args,
			'paged' 			  => $paged,
			'prev_text' 		  => $prev_text,
			'next_text' 		  => $next_text,
			'load_text' 		  => $load_text
		);

		extract( $widget_data );
		include DR_WIDGETS_BLOCKS_PLUGIN_PATH .'/src/widgets/recipe-grid-one/render.php';
		
		wp_reset_postdata(); 
		$output = ob_get_clean();
		echo $output;
		wp_die();
	}

	/**
	 * Recipe pagination for Grid Two.
	 *
	 * @since 1.1.2
	 * @access public
	 *
	 * @return void
	 */
	public function recipe_pagination_two() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'recipe_grid_two_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'dr-widgets-blocks' ) );
			die;
		}

		ob_start();
		
		// Get all the pagination parameters
		$paged = isset( $_POST['paged'] ) ? $_POST['paged'] : 1;
		$layout = isset( $_POST['layout'] ) ? esc_attr( $_POST['layout'] ) : 'layout-1';
		$per_page = isset( $_POST['postsPerPage'] ) ? $_POST['postsPerPage'] : 3;
		$recipe_ids = isset( $_POST['exclude'] ) ? $_POST['exclude'] : false;
		$order_by = isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'date';
		$order = isset( $_POST['order'] ) ? $_POST['order'] : 'DESC';
		$offset = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;

		if ( $paged > 1 ) {
			$final_offset = ( ( $paged - 1 ) * intval( $per_page ) ) + intval( $offset );
		} else {
			$final_offset = intval( $offset );
		}

		// Handle filtering
		if(isset( $_POST['filterBy'] )){
			$orderby = $_POST['filterBy'] === 'rand' ? 'rand' : ($_POST['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		// Build query args
		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'paged'            => $paged,
			'offset'           => $final_offset,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( isset($_POST['filterBy']) && $_POST['filterBy'] === 'popular' || 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$taxonomy           = isset( $_POST['taxonomy'] ) && '' !== $_POST['taxonomy'] ? $_POST['taxonomy'] : 'recipe-course';
		$terms              = $taxonomy ? ( isset( $_POST[ "terms" ] ) ? $_POST[ "terms" ] : '' ) : '';
		$all_taxonomy       = isset( $_POST['all_taxonomy'] ) && 'yes' === $_POST['all_taxonomy'] ? true : false;
		$all_term_id        = isset( $_POST['all_term_id'] ) ? $_POST['all_term_id'] : [];
		$per_row        	= isset( $_POST['recipesPerRow'] ) ? absint( $_POST['recipesPerRow'] ) : 3;
		$per_row_tablet 	= isset( $_POST['recipesPerRow_tablet'] ) ? absint( $_POST['recipesPerRow_tablet'] ) : 2;
		$per_row_mobile 	= isset( $_POST['recipesPerRow_mobile'] ) ? absint( $_POST['recipesPerRow_mobile'] ) : 1;
		$layout         	= isset( $_POST['layout'] ) ? $_POST['layout'] : 'layout-1';
		$show_pagination    = isset( $_POST['showPagination'] ) && 'yes'   === $_POST['showPagination'] ? true : false;
		$pagination_type    = isset( $_POST['paginationType'] ) ? $_POST['paginationType'] : 'number';
		$show_feature_image = isset( $_POST['showFeatureImage'] ) && 'yes' === $_POST['showFeatureImage'] ? true : false;
		$image_size         = isset( $_POST['imageSize'] ) ? $_POST['imageSize'] : 'delrecpe-structured-data-4_3';
		$image_size_l2      = isset( $_POST['imageSizel2'] ) ? $_POST['imageSizel2'] : 'delrecpe-structured-data-1_1';
		$image_custom_size  = isset( $_POST['imageCustomSize'] ) ? $_POST['imageCustomSize'] : false;
		$image_size         = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$show_title         = isset( $_POST['showTitle'] ) && 'yes' === $_POST['showTitle'] ? true : false;
		$title_tag          = isset( $_POST['headingTag'] ) ? $_POST['headingTag'] : 'h3';
		$show_total_time    = isset( $_POST['showTotalTime'] ) && 'yes' === $_POST['showTotalTime'] ? true : false;
		$show_difficulty    = isset( $_POST['showDifficulty'] ) && 'yes' === $_POST['showDifficulty'] ? true : false;
		$show_recipe_keys   = isset( $_POST['showRecipeKeys'] ) && 'yes' === $_POST['showRecipeKeys'] ? true : false;
		$show_excerpt       = isset( $_POST['showExcerpt'] ) && 'yes' === $_POST['showExcerpt'] ? true : false;
		$show_author        = isset( $_POST['showAuthor'] ) && 'yes' === $_POST['showAuthor'] ? true : false;
		$show_publish_date  = isset( $_POST['showPublishDate'] ) && 'yes' === $_POST['showPublishDate'] ? true : false;
		$show_rating        = isset( $_POST['showRating'] ) && 'yes' === $_POST['showRating'] ? true : false;
		$show_comment       = isset( $_POST['showComment'] ) && 'yes' === $_POST['showComment'] ? true : false;
		$show_category      = isset( $_POST['showCategory'] ) && 'yes' === $_POST['showCategory'] ? true : false;
		$excerpt_length     = isset( $_POST['excerptLength'] ) ? $_POST['excerptLength'] : 20;
		$image_alignment    = isset( $_POST['imageAlignment'] ) ? $_POST['imageAlignment'] : 'left';
		$separator          = isset( $_POST['separator'] ) ? $_POST['separator'] : 'dot';
		$show_wishlist      = isset( $_POST['showBookmark'] ) && 'yes' === $_POST['showBookmark'] ? true : false;
		$prev_text          = isset( $_POST['prevText'] ) ? $_POST['prevText'] : esc_html__( 'Previous', 'dr-widgets-blocks' );
		$next_text          = isset( $_POST['nextText'] ) ? $_POST['nextText'] : esc_html__( 'Next', 'dr-widgets-blocks' );
		$load_text          = isset( $_POST['loadText'] ) ? $_POST['loadText'] : __( 'Load More', 'dr-widgets-blocks' );

		// Get all the display settings
		$widget_data = array(
			'layout'             => $layout,
			'show_feature_image'  => $show_feature_image,
			'image_size'          => $image_size,
			'image_size_l2'       => $image_size_l2,
			'show_title'          => $show_title,
			'title_tag'           => $title_tag,
			'show_total_time'     => $show_total_time,
			'show_difficulty'     => $show_difficulty,
			'show_recipe_keys'    => $show_recipe_keys,
			'show_excerpt'        => $show_excerpt,
			'show_author'         => $show_author,
			'show_publish_date'   => $show_publish_date,
			'show_rating'         => $show_rating,
			'show_comment'        => $show_comment,
			'show_category'       => $show_category,
			'excerpt_length'      => $excerpt_length,
			'image_alignment'     => $image_alignment,
			'separator'           => $separator,
			'show_wishlist'       => $show_wishlist,
			'current_page'        => $paged,
			'pagination_type'     => $pagination_type,
			'show_pagination'     => $show_pagination,
			'per_row'			  => $per_row,
			'per_row_tablet'	  => $per_row_tablet,
			'per_row_mobile'	  => $per_row_mobile,
			'taxonomy'            => $taxonomy,
			'terms'               => $terms,
			'all_taxonomy'        => $all_taxonomy,
			'all_term_id'         => $all_term_id,
			'args' 				  => $args,
			'paged' 			  => $paged,
			'prev_text' 		  => $prev_text,
			'next_text' 		  => $next_text,
			'load_text' 		  => $load_text
		);

		extract( $widget_data );
		include DR_WIDGETS_BLOCKS_PLUGIN_PATH .'/src/widgets/recipe-grid-two/render.php';
		
		wp_reset_postdata(); 
		$output = ob_get_clean();
		echo $output;
		wp_die();
	}

	/**
	 * Recipe pagination for Post List One.
	 *
	 * @since 1.1.2
	 * @access public
	 *
	 * @return void
	 */
	public function recipe_post_list_one() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'recipe_post_list_one_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'dr-widgets-blocks' ) );
			die;
		}

		ob_start();
		
		// Get all the pagination parameters
		$paged = isset( $_POST['paged'] ) ? $_POST['paged'] : 1;
		$per_page = isset( $_POST['postsPerPage'] ) ? $_POST['postsPerPage'] : 3;
		$recipe_ids = isset( $_POST['exclude'] ) ? $_POST['exclude'] : false;
		$order_by = isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'date';
		$order = isset( $_POST['order'] ) ? $_POST['order'] : 'DESC';
		$offset = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;

		if ( $paged > 1 ) {
			$final_offset = ( ( $paged - 1 ) * intval( $per_page ) ) + intval( $offset );
		} else {
			$final_offset = intval( $offset );
		}

		// Handle filtering
		if(isset( $_POST['filterBy'] )){
			$orderby = $_POST['filterBy'] === 'rand' ? 'rand' : ($_POST['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		// Build query args
		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'paged'            => $paged,
			'offset'           => $final_offset,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( isset($_POST['filterBy']) && $_POST['filterBy'] === 'popular' || 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$taxonomy           = isset( $_POST['taxonomy'] ) && '' !== $_POST['taxonomy'] ? $_POST['taxonomy'] : 'recipe-course';
		$terms              = $taxonomy ? ( isset( $_POST[ "{$taxonomy}_term_id" ] ) ? $_POST[ "{$taxonomy}_term_id" ] : '' ) : '';
		$all_taxonomy       = isset( $_POST['all_taxonomy'] ) && 'yes' === $_POST['all_taxonomy'] ? true : false;
		$all_term_id        = isset( $_POST['all_term_id'] ) ? $_POST['all_term_id'] : [];
		$col_lay_1        = isset( $_POST['numOfCol'] ) ? absint( $_POST['numOfCol'] ) : 1;
		$col_lay_1_tablet = isset( $_POST['numOfCol_tablet'] ) ? absint( $_POST['numOfCol_tablet'] ) : 1;
		$col_lay_1_mobile = isset( $_POST['numOfCol_mobile'] ) ? absint( $_POST['numOfCol_mobile'] ) : 1;
		$col_lay_2        = isset( $_POST['numOfCol2'] ) ? absint( $_POST['numOfCol2'] ) : 1;
		$col_lay_2_tablet = isset( $_POST['numOfCol2_tablet'] ) ? absint( $_POST['numOfCol2_tablet'] ) : 1;
		$col_lay_2_mobile = isset( $_POST['numOfCol2_mobile'] ) ? absint( $_POST['numOfCol2_mobile'] ) : 1;
		$layout_data      = isset( $_POST['layout'] ) ? $_POST['layout'] : 'layout-1';

		if( $layout_data === 'layout-2' ){
			$per_row        = $col_lay_2;
			$per_row_tablet = $col_lay_2_tablet;
			$per_row_mobile = $col_lay_2_mobile;
		} else {
			$per_row        = $col_lay_1;
			$per_row_tablet = $col_lay_1_tablet;
			$per_row_mobile = $col_lay_1_mobile;
		}

		$image_size          = isset( $_POST['imageSize'] ) ? $_POST['imageSize'] : 'thumbnail';
		$image_custom_size   = isset( $_POST['imageCustomSize'] ) ? $_POST['imageCustomSize'] : false;
		$image_size          = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$title_tag           = isset( $_POST['headingTag'] ) ? $_POST['headingTag'] : 'h3';
		$show_total_time     = isset( $_POST['showTotalTime'] ) && 'yes' === $_POST['showTotalTime'] ? true : false;
		$show_difficulty     = isset( $_POST['showDifficulty'] ) && 'yes' === $_POST['showDifficulty'] ? true : false;
		$show_recipe_keys    = isset( $_POST['showRecipeKeys'] ) && 'yes' === $_POST['showRecipeKeys'] ? true : false;
		$show_excerpt        = isset( $_POST['showExcerpt'] ) && 'yes' === $_POST['showExcerpt'] ? true : false;
		$show_category       = isset( $_POST['showCategory'] ) && 'yes' === $_POST['showCategory'] ? true : false;
		$show_rating         = isset( $_POST['showRating'] ) && 'yes' === $_POST['showRating'] ? true : false;
		$excerpt_length      = isset( $_POST['excerptLength'] ) ? $_POST['excerptLength'] : 20;
		$image_alignment     = isset( $_POST['imageAlignment'] ) ? $_POST['imageAlignment'] : 'left';
		$separator           = isset( $_POST['separator'] ) ? $_POST['separator'] : 'dot';
		$show_wishlist       = isset( $_POST['showBookmark'] ) && 'yes' === $_POST['showBookmark'] ? true : false;
		
		// Add pagination settings
		$show_pagination    = isset( $_POST['showPagination'] ) && 'yes' === $_POST['showPagination'] ? true : false;
		$pagination_type    = isset( $_POST['paginationType'] ) ? $_POST['paginationType'] : 'number';
		$prev_text         = isset( $_POST['prevText'] ) ? $_POST['prevText'] : __( 'Previous', 'dr-widgets-blocks' );
		$next_text         = isset( $_POST['nextText'] ) ? $_POST['nextText'] : __( 'Next', 'dr-widgets-blocks' );
		$load_text         = isset( $_POST['loadText'] ) ? $_POST['loadText'] : __( 'Load More', 'dr-widgets-blocks' );


		// Get all the display settings
		$widget_data = array(
			'image_size'       => $image_size,
			'layout'           => $layout_data,
			'per_row'          => $per_row,
			'per_row_tablet'   => $per_row_tablet,
			'per_row_mobile'   => $per_row_mobile,
			'title_tag'        => $title_tag,
			'show_total_time'  => $show_total_time,
			'show_difficulty'  => $show_difficulty,
			'show_recipe_keys' => $show_recipe_keys,
			'show_excerpt'     => $show_excerpt,
			'show_category'    => $show_category,
			'show_rating'      => $show_rating,
			'excerpt_length'   => $excerpt_length,
			'image_alignment'  => $image_alignment,
			'separator'        => $separator,
			'show_wishlist'    => $show_wishlist,
			'taxonomy'         => $taxonomy,
			'terms'            => $terms,
			'all_taxonomy'     => $all_taxonomy,
			'all_term_id'      => $all_term_id,
			'show_pagination'  => $show_pagination,
			'current_page'        => $paged,
			'args'             => $args,
			'paged'            => $paged,
			'pagination_type'  => $pagination_type,
			'prev_text'        => $prev_text,
			'next_text'        => $next_text,
			'load_text'        => $load_text
		);

		extract( $widget_data );
		include DR_WIDGETS_BLOCKS_PLUGIN_PATH .'/src/widgets/recipe-post-list-one/render.php';
		
		wp_reset_postdata(); 
		$output = ob_get_clean();
		echo $output;
		wp_die();
	}

	/**
	 * Recipe pagination for Post List Two.
	 *
	 * @since 1.1.2
	 * @access public
	 *
	 * @return void
	 */
	public function recipe_post_list_two() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'recipe_post_list_two_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'dr-widgets-blocks' ) );
			die;
		}

		ob_start();
		
		// Get all the pagination parameters
		$paged = isset( $_POST['paged'] ) ? $_POST['paged'] : 1;
		$per_page = isset( $_POST['postsPerPage'] ) ? $_POST['postsPerPage'] : 3;
		$recipe_ids = isset( $_POST['exclude'] ) ? $_POST['exclude'] : false;
		$order_by = isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'date';
		$order = isset( $_POST['order'] ) ? $_POST['order'] : 'DESC';
		$offset = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;

		if ( $paged > 1 ) {
			$final_offset = ( ( $paged - 1 ) * intval( $per_page ) ) + intval( $offset );
		} else {
			$final_offset = intval( $offset );
		}

		// Handle filtering
		if(isset( $_POST['filterBy'] )){
			$orderby = $_POST['filterBy'] === 'rand' ? 'rand' : ($_POST['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		// Build query args
		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'paged'            => $paged,
			'offset'           => $final_offset,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( isset($_POST['filterBy']) && $_POST['filterBy'] === 'popular' || 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$taxonomy           = isset( $_POST['taxonomy'] ) && '' !== $_POST['taxonomy'] ? $_POST['taxonomy'] : 'recipe-course';
		$terms              = $taxonomy ? ( isset( $_POST[ "{$taxonomy}_term_id" ] ) ? $_POST[ "{$taxonomy}_term_id" ] : '' ) : '';
		$all_taxonomy       = isset( $_POST['all_taxonomy'] ) && 'yes' === $_POST['all_taxonomy'] ? true : false;
		$all_term_id        = isset( $_POST['all_term_id'] ) ? $_POST['all_term_id'] : [];
		$layout_data      = isset( $_POST['layout'] ) ? $_POST['layout'] : 'layout-1';
		$image_size          = isset( $_POST['imageSize'] ) ? $_POST['imageSize'] : 'delrecpe-structured-data-4_3';
		$image_custom_size   = isset( $_POST['imageCustomSize'] ) ? $_POST['imageCustomSize'] : false;
		$image_size          = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$title_tag           = isset( $_POST['headingTag'] ) ? $_POST['headingTag'] : 'h3';
		$show_total_time     = isset( $_POST['showTotalTime'] ) && 'yes' === $_POST['showTotalTime'] ? true : false;
		$show_difficulty     = isset( $_POST['showDifficulty'] ) && 'yes' === $_POST['showDifficulty'] ? true : false;
		$show_recipe_keys    = isset( $_POST['showRecipeKeys'] ) && 'yes' === $_POST['showRecipeKeys'] ? true : false;
		$show_excerpt        = isset( $_POST['showExcerpt'] ) && 'yes' === $_POST['showExcerpt'] ? true : false;
		$show_category       = isset( $_POST['showCategory'] ) && 'yes' === $_POST['showCategory'] ? true : false;
		$show_rating         = isset( $_POST['showRating'] ) && 'yes' === $_POST['showRating'] ? true : false;
		$excerpt_length      = isset( $_POST['excerptLength'] ) ? $_POST['excerptLength'] : 20;
		$image_alignment     = isset( $_POST['imageAlignment'] ) ? $_POST['imageAlignment'] : 'left';
		$separator           = isset( $_POST['separator'] ) ? $_POST['separator'] : 'dot';
		$show_wishlist       = isset( $_POST['showBookmark'] ) && 'yes' === $_POST['showBookmark'] ? true : false;
		
		// Add pagination settings
		$show_pagination    = isset( $_POST['showPagination'] ) && 'yes' === $_POST['showPagination'] ? true : false;
		$pagination_type    = isset( $_POST['paginationType'] ) ? $_POST['paginationType'] : 'number';
		$prev_text         = isset( $_POST['prevText'] ) ? $_POST['prevText'] : __( 'Previous', 'dr-widgets-blocks' );
		$next_text         = isset( $_POST['nextText'] ) ? $_POST['nextText'] : __( 'Next', 'dr-widgets-blocks' );
		$load_text         = isset( $_POST['loadText'] ) ? $_POST['loadText'] : __( 'Load More', 'dr-widgets-blocks' );


		// Get all the display settings
		$widget_data = array(
			'image_size'       => $image_size,
			'layout'           => $layout_data,
			'title_tag'        => $title_tag,
			'show_total_time'  => $show_total_time,
			'show_difficulty'  => $show_difficulty,
			'show_recipe_keys' => $show_recipe_keys,
			'show_excerpt'     => $show_excerpt,
			'show_category'    => $show_category,
			'show_rating'      => $show_rating,
			'excerpt_length'   => $excerpt_length,
			'image_alignment'  => $image_alignment,
			'separator'        => $separator,
			'show_wishlist'    => $show_wishlist,
			'taxonomy'         => $taxonomy,
			'terms'            => $terms,
			'all_taxonomy'     => $all_taxonomy,
			'all_term_id'      => $all_term_id,
			'show_pagination'  => $show_pagination,
			'current_page'        => $paged,
			'args'             => $args,
			'paged'            => $paged,
			'pagination_type'  => $pagination_type,
			'prev_text'        => $prev_text,
			'next_text'        => $next_text,
			'load_text'        => $load_text
		);

		extract( $widget_data );
		include DR_WIDGETS_BLOCKS_PLUGIN_PATH .'/src/widgets/recipe-post-list-two/render.php';
		
		wp_reset_postdata(); 
		$output = ob_get_clean();
		echo $output;
		wp_die();
	}

	/**
	 * Recipe pagination for Post List Three.
	 *
	 * @since 1.1.2
	 * @access public
	 *
	 * @return void
	 */
	public function recipe_post_list_three() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'recipe_post_list_three_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'dr-widgets-blocks' ) );
			die;
		}

		ob_start();
		
		// Get all the pagination parameters
		$paged = isset( $_POST['paged'] ) ? $_POST['paged'] : 1;
		$per_page = isset( $_POST['postsPerPage'] ) ? $_POST['postsPerPage'] : 3;
		$recipe_ids = isset( $_POST['exclude'] ) ? $_POST['exclude'] : false;
		$order_by = isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'date';
		$order = isset( $_POST['order'] ) ? $_POST['order'] : 'DESC';
		$offset = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;

		if ( $paged > 1 ) {
			$final_offset = ( ( $paged - 1 ) * intval( $per_page ) ) + intval( $offset );
		} else {
			$final_offset = intval( $offset );
		}

		// Handle filtering
		if(isset( $_POST['filterBy'] )){
			$orderby = $_POST['filterBy'] === 'rand' ? 'rand' : ($_POST['filterBy'] === 'popular' ? 'meta_value_num' : $order_by);
		} else {
			$orderby = $order_by;
		}

		// Build query args
		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'paged'            => $paged,
			'offset'           => $final_offset,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( isset($_POST['filterBy']) && $_POST['filterBy'] === 'popular' || 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$taxonomy           = isset( $_POST['taxonomy'] ) && '' !== $_POST['taxonomy'] ? $_POST['taxonomy'] : 'recipe-course';
		$terms              = $taxonomy ? ( isset( $_POST[ "{$taxonomy}_term_id" ] ) ? $_POST[ "{$taxonomy}_term_id" ] : '' ) : '';
		$all_taxonomy       = isset( $_POST['all_taxonomy'] ) && 'yes' === $_POST['all_taxonomy'] ? true : false;
		$all_term_id        = isset( $_POST['all_term_id'] ) ? $_POST['all_term_id'] : [];
		$layout_data       = isset( $_POST['layout'] ) ? $_POST['layout'] : 'layout-1';
		$counter           = isset( $_POST['counter_style'] ) ? $_POST['counter_style'] : 'style-1';
		$image_size        = isset( $_POST['imageSize'] ) ? $_POST['imageSize'] : 'thumbnail';
		$image_custom_size = isset( $_POST['imageCustomSize'] ) ? $_POST['imageCustomSize'] : false;
		$image_size        = 'custom' === $image_size && $image_custom_size ? dr_widgets_blocks_get_custom_image_size( $image_custom_size ) : $image_size;
		$hero_image_size   = isset( $_POST['hero_imageSize'] ) ? $_POST['hero_imageSize'] : 'medium';
		$hero_custom_size  = isset( $_POST['imageCustomSize'] ) ? $_POST['imageCustomSize'] : false;
		$hero_image_size   = 'custom' === $hero_image_size && $hero_custom_size ? dr_widgets_blocks_get_custom_image_size( $hero_custom_size ) : $hero_image_size;
		$title_tag         = isset( $_POST['headingTag'] ) ? $_POST['headingTag'] : 'h3';
		$show_total_time   = isset( $_POST['showTotalTime'] ) && 'yes' === $_POST['showTotalTime'] ? true : false;
		$show_difficulty   = isset( $_POST['showDifficulty'] ) && 'yes' === $_POST['showDifficulty'] ? true : false;
		$show_recipe_keys  = isset( $_POST['showRecipeKeys'] ) && 'yes' === $_POST['showRecipeKeys'] ? true : false;
		$show_category     = isset( $_POST['showCategory'] ) && 'yes' === $_POST['showCategory'] ? true : false;
		$separator         = isset( $_POST['separator'] ) ? $_POST['separator'] : 'dot';
		$show_wishlist     = isset( $_POST['showBookmark'] ) && 'yes' === $_POST['showBookmark'] ? true : false;
		
		// Add pagination settings
		$show_pagination    = isset( $_POST['showPagination'] ) && 'yes' === $_POST['showPagination'] ? true : false;
		$pagination_type    = isset( $_POST['paginationType'] ) ? $_POST['paginationType'] : 'number';
		$prev_text         = isset( $_POST['prevText'] ) ? $_POST['prevText'] : __( 'Previous', 'dr-widgets-blocks' );
		$next_text         = isset( $_POST['nextText'] ) ? $_POST['nextText'] : __( 'Next', 'dr-widgets-blocks' );
		$load_text         = isset( $_POST['loadText'] ) ? $_POST['loadText'] : __( 'Load More', 'dr-widgets-blocks' );

		// Get all the display settings
		$widget_data = array(
			'image_size'       => $image_size,
			'hero_image_size'  => $hero_image_size,
			'layout'           => $layout_data,
			'counter'          => $counter,
			'title_tag'        => $title_tag,
			'show_total_time'  => $show_total_time,
			'show_difficulty'  => $show_difficulty,
			'show_recipe_keys' => $show_recipe_keys,
			'show_category'    => $show_category,
			'separator'        => $separator,
			'show_wishlist'    => $show_wishlist,
			'taxonomy'         => $taxonomy,
			'terms'            => $terms,
			'all_taxonomy'     => $all_taxonomy,
			'all_term_id'      => $all_term_id,
			'show_pagination'  => $show_pagination,
			'current_page'     => $paged,
			'args'             => $args,
			'paged'            => $paged,
			'pagination_type'  => $pagination_type,
			'prev_text'        => $prev_text,
			'next_text'        => $next_text,
			'load_text'        => $load_text
		);

		extract( $widget_data );
		include DR_WIDGETS_BLOCKS_PLUGIN_PATH .'/src/widgets/recipe-post-list-three/render.php';
		
		wp_reset_postdata(); 
		$output = ob_get_clean();
		echo $output;
		wp_die();
	}

	/**
	 * Get image sizes.
	 */
	public function get_image_sizes() {
		$block = true;
		$sizes = dr_widgets_blocks_get_image_size_options( $block );

		wp_send_json_success( $sizes );
	}

	/**
	 * Get recipe posts.
	 */
	public function get_recipe_posts() {
		$attributes = isset( $_POST['attributes'] ) ? dr_widgets_blocks_clean_vars( json_decode( wp_unslash( $_POST['attributes'] ), true ) ) : array();

		if ( ! isset( $attributes ) ) {
			return;
		}
		$per_page   = isset( $attributes['postsPerPage'] ) ? $attributes['postsPerPage'] : 3;
		$recipe_ids = isset( $attributes['exclude'] ) ? $attributes['exclude'] : false;
		$order_by   = isset( $attributes['orderby'] ) ? $attributes['orderby'] : 'date';
		$order      = isset( $attributes['order'] ) ? $attributes['order'] : 'DESC';
		$offset     = isset( $attributes['offset'] ) ? $attributes['offset'] : 0;

		$args = array(
			'posts_per_page'   => $per_page,
			'post__not_in'     => $recipe_ids,
			'offset'           => $offset,
			'orderby'          => $order_by,
			'order'            => $order,
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( 'meta_value_num' === $order_by ) {
			$args['meta_key'] = '_delicious_recipes_view_count';
		}

		$term_ids = array();
		$taxonomy = isset( $attributes['taxonomy'] ) && '' !== $attributes['taxonomy'] ? $attributes['taxonomy'] : false;
		$terms    = $taxonomy ? ( isset( $attributes['terms'] ) ? $attributes['terms'] : false ) : false;

		if ( $taxonomy && $terms ) {
			foreach ( $terms as $term ) {
				$term_id = get_term_by( 'term_id', $term, $taxonomy )->term_id;
				if ( $term_id ) {
					$term_ids[] = $term_id;
				}
			}
		}

		if ( $term_ids ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'terms'    => $term_ids,
					'field'    => 'term_id',
				),
			);
		} elseif ( $taxonomy ) {
			$args['taxonomy'] = $taxonomy;
		}

		$recipes_query = new \WP_Query( $args );

		$image_size = isset( $attributes['imageSize'] ) ? $attributes['imageSize'] : 'recipe-archive-grid';
		$recipes    = array();

		if ( $recipes_query->have_posts() ) {
			while ( $recipes_query->have_posts() ) {
				$recipes_query->the_post();
				$recipe       = get_post( get_the_ID() );
				$recipe_metas = delicious_recipes_get_recipe( $recipe );

				$thumbnail_id = has_post_thumbnail( $recipe_metas->ID ) ? get_post_thumbnail_id( $recipe_metas->ID ) : '';
				$thumbnail    = $thumbnail_id ? get_the_post_thumbnail( $recipe_metas->ID, $image_size ) : '';
				$fallback_svg = delicious_recipes_get_fallback_svg( $image_size, true );

				$recipe_keys = array();

				if ( ! empty( $recipe_metas->recipe_keys ) ) {
					foreach ( $recipe_metas->recipe_keys as $recipe_key ) {
						$key           = get_term_by( 'name', $recipe_key, 'recipe-key' );
						if ( $key ) {
							$link          = get_term_link( $key, 'recipe-key' );
							$icon          = delicious_recipes_get_tax_icon( $key, true );
							$recipe_keys[] = array(
								'key'  => $recipe_key,
								'link' => $link,
								'icon' => $icon,
							);
						} else {
							error_log( "Term not found for recipe key: " . $recipe_key );
						}
					}
				}

				$recipes[] = array(
					'recipe_id'        => $recipe_metas->ID,
					'title'            => $recipe_metas->name,
					'permalink'        => $recipe_metas->permalink,
					'thumbnail_id'     => $recipe_metas->thumbnail_id,
					'thumbnail_url'    => $recipe_metas->thumbnail,
					'thumbnail'        => $thumbnail,
					'fallback_svg'     => $fallback_svg,
					'recipe_keys'      => $recipe_keys,
					'total_time'       => $recipe_metas->total_time,
					'difficulty_level' => $recipe_metas->difficulty_level,
				);
			}
			wp_reset_postdata();
		}
		wp_send_json_success( $recipes );
	}

	/**
	 * Get Block Settings values.
	 */
	public function get_block_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = dr_widgets_blocks_get_block_settings();
		wp_send_json_success( $data );
	}

	/**
	 * Save Block Settings values.
	 */
	public function save_block_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = isset( $_POST['blocks'] ) ? dr_widgets_blocks_clean_vars( json_decode( wp_unslash( $_POST['blocks'] ), true ) ) : array();
		update_option( 'drwb_block_settings', $data );

		wp_send_json_success( __( 'Saved successfully.', 'dr-widgets-blocks' ) );
	}

	/**
	 * Get Widget Settings values.
	 */
	public function get_widget_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = dr_widgets_blocks_get_widget_settings();
		wp_send_json_success( $data );
	}

	/**
	 * Save Widget Settings values.
	 */
	public function save_widget_settings() {
		check_ajax_referer( 'dr_widgets_blocks_ajax_nonce', 'security' );

		$data = isset( $_POST['widgets'] ) ? dr_widgets_blocks_clean_vars( json_decode( wp_unslash( $_POST['widgets'] ), true ) ) : array();
		update_option( 'drwb_widget_settings', $data );

		wp_send_json_success( __( 'Saved successfully.', 'dr-widgets-blocks' ) );
	}

	/**
	 * Get Latest Changelog
	 *
	 * @return void
	 */
	public function get_latest_changelog() {
		$changelog     = null;
		$pro_changelog = null;
		$access_type   = get_filesystem_method();

		if ( 'direct' === $access_type ) {
			$creds = request_filesystem_credentials(
				site_url() . '/wp-admin/',
				'',
				false,
				false,
				array()
			);

			if ( WP_Filesystem( $creds ) ) {
				global $wp_filesystem;

				$changelog = $wp_filesystem->get_contents(
					plugin_dir_path( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/changelog.txt'
				);
			}
		}

		wp_send_json_success(
			array(
				'changelog' => apply_filters(
					'drwb_changelogs_list',
					array(
						array(
							'title'     => __( 'Free', 'dr-widgets-blocks' ),
							'changelog' => $changelog,
						),
						array(
							'title'     => __( 'Pro', 'dr-widgets-blocks' ),
							'changelog' => $pro_changelog,
						),
					)
				),
			)
		);
	}


}

new DR_Widgets_Blocks_Ajax();
