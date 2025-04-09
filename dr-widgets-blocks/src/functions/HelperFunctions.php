<?php
/**
 * DR_Widgets_Blocks Core Functions.
 *
 * General core functions avaiable on both the front-end and backend.
 *
 * @package DR_Widgets_Blocks\Functions
 *
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper for _doing_it_wrong().
 *
 * @since  1.0.0
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 */
function dr_widgets_blocks_doing_it_wrong( $function, $message, $version ) {
	// @codingStandardsIgnoreStart
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();
	
	_doing_it_wrong( $function, $message, $version );
	// @codingStandardsIgnoreEnd
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string Template path.
 */
function dr_widgets_blocks_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = DR_Widgets_Blocks_Init()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = DR_Widgets_Blocks_Init()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template.
	if ( ! $template || DR_WIDGETS_BLOCKS_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'dr_widgets_blocks_locate_template', $template, $template_name, $template_path );
}

/**
 * Get other templates (e.g. article attributes) passing attributes and including the file.
 *
 * @since 1.0.0
 *
 * @param string $template_name   Template name.
 * @param array  $args            Arguments. (default: array).
 * @param string $template_path   Template path. (default: '').
 * @param string $default_path    Default path. (default: '').
 */
function dr_widgets_blocks_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key( implode( '-', array( 'template', $template_name, $template_path, $default_path, DR_WIDGETS_BLOCKS_VERSION ) ) );
	$template  = (string) wp_cache_get( $cache_key, 'dr-widgets-blocks' );

	if ( ! $template ) {
		$template = dr_widgets_blocks_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, 'dr-widgets-blocks' );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'dr_widgets_blocks_get_template', $template, $template_name, $args, $template_path, $default_path );

	if ( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			/* translators: %s template */
			dr_widgets_blocks_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'dr-widgets-blocks' ), '<code>' . $template . '</code>' ), '1.0.0' );
			return;
		}
		$template = $filter_template;
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			dr_widgets_blocks_doing_it_wrong(
				__FUNCTION__,
				__( 'action_args should not be overwritten when calling dr_widgets_blocks_get_template.', 'dr-widgets-blocks' ),
				'1.0.0'
			);
			unset( $args['action_args'] );
		}
		extract( $args );
	}

	do_action( 'dr_widgets_blocks_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'dr_widgets_blocks_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Get template part.
 *
 * DR_WIDGETS_BLOCKS_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @param mixed  $slug Template slug.
 * @param string $name Template name (default: '').
 */
function dr_widgets_blocks_get_template_part( $slug, $name = '' ) {
	$cache_key = sanitize_key( implode( '-', array( 'template-part', $slug, $name, DR_WIDGETS_BLOCKS_VERSION ) ) );
	$template  = (string) wp_cache_get( $cache_key, 'dr-widgets-blocks' );

	if ( ! $template ) {
		if ( $name ) {
			$template = DR_WIDGETS_BLOCKS_TEMPLATE_DEBUG_MODE ? '' : locate_template(
				array(
					"{$slug}-{$name}.php",
					DR_Widgets_Blocks_Init()->template_path() . "{$slug}-{$name}.php",
				)
			);

			if ( ! $template ) {
				$fallback = DR_Widgets_Blocks_Init()->plugin_path() . "/templates/{$slug}-{$name}.php";
				$template = file_exists( $fallback ) ? $fallback : '';
			}
		}

		if ( ! $template ) {
			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/dr-widgets-blocks/slug.php.
			$template = DR_WIDGETS_BLOCKS_TEMPLATE_DEBUG_MODE ? '' : locate_template(
				array(
					"{$slug}.php",
					DR_Widgets_Blocks_Init()->template_path() . "{$slug}.php",
				)
			);
		}

		wp_cache_set( $cache_key, $template, 'dr-widgets-blocks' );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'dr_widgets_blocks_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Like dr_widgets_blocks_get_template, but return the HTML instaed of outputting.
 *
 * @see dr_widgets_blocks_get_template
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param array  $args           Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string.
 */
function dr_widgets_blocks_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	dr_widgets_blocks_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Get all Recipes Name list.
 *
 * @return array
 */
function dr_widgets_blocks_get_all_recipe_options() {
	$args    = array(
		'post_type'      => DELICIOUS_RECIPE_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);
	$recipes = get_posts( $args );

	$options = array();
	foreach ( $recipes as $recipe ) {
		$options[ $recipe->ID ] = $recipe->post_title;
	}

	return $options;
}

/**
 * Get all image size options
 *
 * @return array
 */
function dr_widgets_blocks_get_all_image_sizes() {
	global $_wp_additional_image_sizes;

	$default_image_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' );

	$image_sizes = array();

	foreach ( $default_image_sizes as $size ) {
		$image_sizes[ $size ] = array(
			'width'  => (int) get_option( $size . '_size_w' ),
			'height' => (int) get_option( $size . '_size_h' ),
			'crop'   => (bool) get_option( $size . '_crop' ),
		);
	}

	if ( $_wp_additional_image_sizes ) {
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	}

	return apply_filters( 'dr_widgets_blocks_image_sizes', $image_sizes );
}

/**
 * Get the image size options for the image size dropdown.
 *
 * @return array
 */
function dr_widgets_blocks_get_image_size_options( $block = false ) {
	$wp_image_sizes = dr_widgets_blocks_get_all_image_sizes();
	$image_sizes    = array();

	foreach ( $wp_image_sizes as $size_name => $size_attrs ) {
		$image_size_name = ucwords( str_replace( '_', ' ', $size_name ) );
		if ( is_array( $size_attrs ) ) {
			$image_size_name .= sprintf( ' - %d x %d', $size_attrs['width'], $size_attrs['height'] );
		}
		$image_sizes[ $size_name ] = $image_size_name;
	}

	// Add full and custom image sizes.
	$image_sizes['full'] = _x( 'Full', 'Image Size Control', 'dr-widgets-blocks' );

	if ( ! $block ) {
		$image_sizes['custom'] = _x( 'Custom', 'Image Size Control', 'dr-widgets-blocks' );
	}

	return $image_sizes;
}

/**
 * Get the image width and height values for custom image size.
 *
 * @param string $image_custom_size Image size array.
 *
 * @return array
 */
function dr_widgets_blocks_get_custom_image_size( $image_custom_size ) {
	$attachment_size = array(
		0 => null, // Width.
		1 => null, // Height.
	);

	if ( is_array( $image_custom_size ) ) {
		if ( ! empty( $image_custom_size['width'] ) ) {
			$attachment_size[0] = $image_custom_size['width'];
		}

		if ( ! empty( $image_custom_size['height'] ) ) {
			$attachment_size[1] = $image_custom_size['height'];
		}
	} else {
		$attachment_size = 'full';
	}

	return $attachment_size;
}

/**
 * Generate a random ID.
 */
if ( ! function_exists( 'dr_widgets_blocks_rand_md5' ) ) {
	function dr_widgets_blocks_rand_md5( $slug = null ) {
		if ( $slug ) {
			return md5( $slug );
		}
		return md5( time() . '-' . uniqid( wp_rand(), true ) . '-' . wp_rand() );
	}
}

/**
 * Delisho get block settings.
 */
function dr_widgets_blocks_get_block_settings() {
	$settings = get_option( 'drwb_block_settings', array() );

	$block_defaults = apply_filters(
		'drwb_block_settings_defaults',
		array(
			'recipe-posts'          => true,
			'recipe-categories'     => true,
			'recipe-posts-carousel' => true,
			'recipe-categories-tab' => true,
		)
	);

	$settings = wp_parse_args( $settings, $block_defaults );

	return $settings;
}

/**
 * Delisho get widget settings.
 */
function dr_widgets_blocks_get_widget_settings() {
	$settings = get_option( 'drwb_widget_settings', array() );

	$widget_defaults = apply_filters(
		'drwb_widget_settings_defaults',
		array(
			'recipe-posts'            		=> true,
			'recipe-post-list-one'    		=> true,
			'recipe-post-list-two'    		=> true,
			'recipe-post-list-three' 		=> true,
			'recipe-categories'      		=> true,
			'recipe-categories-two'  		=> true,
			'recipe-categories-three' 		=> true,
			'recipe-categories-tab'   		=> true,
			'recipe-categories-tab-two' 	=> true,
			'recipe-posts-carousel'  		=> true,
			'recipe-posts-carousel-two' 	=> true,
			'recipe-posts-carousel-three' 	=> true,
			'recipe-grid-one'         		=> true,
			'recipe-grid-two'        		=> true,
			'recipe-grid-module-one' 		=> true,
			'recipe-grid-module-two' 		=> true,
			'recipe-advanced-heading' 		=> true,
		)
	);

	$settings = wp_parse_args( $settings, $widget_defaults );

	return $settings;
}

/**
 * Delisho get disabled blocks.
 */
function dr_widgets_blocks_get_disabled_blocks() {
	$all_blocks = dr_widgets_blocks_get_block_settings();

	$disabled_blocks = array();
	foreach ( $all_blocks as $block_name => $block_enabled ) {
		if ( ! $block_enabled ) {
			$disabled_blocks[] = $block_name;
		}
	}

	return $disabled_blocks;
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function dr_widgets_blocks_clean_vars( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'dr_widgets_blocks_clean_vars', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

