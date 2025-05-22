<?php
namespace DR_Widgets_Blocks;

/**
 * Fonts Manager Class
 *
 * @package DR_Widgets_Blocks
 */
class Fonts_Manager {

	/**
	 * Page Blocks Variable
	 *
	 * @since 1.0.0
	 * @var instance
	 */
	public static $page_blocks;

	/**
	 * Stylesheet
	 *
	 * @since 1.0.0
	 * @var stylesheet
	 */
	public static $stylesheet;

	/**
	 * Script
	 *
	 * @since 1.0.0
	 * @var script
	 */
	public static $script;

	public function get_all_fonts() {
		return apply_filters(
			'dr_widgets_blocks_typography_font_sources',
			array(
				'system' => array(
					'type'     => 'system',
					'families' => $this->get_system_fonts(),
				),

				'google' => array(
					'type'     => 'google',
					'families' => $this->get_googgle_fonts(),
				),
			)
		);
	}

	public function get_static_fonts_ids() {
		$font_ids = array();
		if ( is_single() || is_page() || is_404() ) {
			global $post;
			$this_post = $post;
			if ( isset( $this_post->ID ) && has_blocks( $this_post->ID ) && isset( $this_post->post_content ) ) {

				$blocks = $this->parse( $this_post->post_content );

				if ( ! is_array( $blocks ) || empty( $blocks ) ) {
					return array();
				}

				$font_ids = $this->get_blocks_fonts( $blocks );

			}
			if ( ! is_object( $post ) ) {
				return array();
			}
		}

		// For Widgets
		$wgdet_font_ids = array();
		$has_block      = false;
		$widget_blocks  = array();
		global $wp_registered_sidebars, $sidebars_widgets;
		foreach ( $wp_registered_sidebars as $key => $value ) {
			if ( is_active_sidebar( $key ) ) {
				foreach ( $sidebars_widgets[ $key ] as $val ) {
					if ( strpos( $val, 'block-' ) !== false ) {
						if ( empty( $widget_blocks ) ) {
							$widget_blocks = get_option( 'widget_block' );
						}
						foreach ( (array) $widget_blocks as $block ) {
							if ( ! is_array( $block ) || ! isset( $block['content'] ) ) {
								continue;
							}
							$blocks = $this->parse( $block['content'] );

							if ( ! is_array( $blocks ) || empty( $blocks ) ) {
								continue 2;
							}
							$has_block      = true;
							$wgdet_font_ids = $this->get_blocks_fonts( $blocks );
						}
						if ( $has_block ) {
							break;
						}
					}
				}
			}
		}

		return array_merge( $font_ids, $wgdet_font_ids );
	}

	/**
	 * Generates stylesheet and appends in head tag.
	 *
	 * @since 1.0.0
	 */
	public function generate_assets() {

		$this_post = array();

		if ( is_single() || is_page() || is_404() ) {

			global $post;
			$this_post = $post;

			if ( ! is_object( $this_post ) ) {
				return;
			}

			/**
			 * Filters the post to build stylesheet for.
			 *
			 * @param \WP_Post $this_post The global post.
			 */
			$this_post = apply_filters( 'dr_widgets_blocks_post_for_stylesheet', $this_post );

			$this->get_generated_stylesheet( $this_post );

		} elseif ( is_archive() || is_home() || is_search() ) {

			global $wp_query;
			$cached_wp_query = $wp_query->posts;

			foreach ( $cached_wp_query as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$this->get_generated_stylesheet( $post );
			}
		}
	}

	/**
	 * Generates stylesheet in loop.
	 *
	 * @param object $this_post Current Post Object.
	 * @since 1.0.0
	 */
	public function get_generated_stylesheet( $this_post ) {

		if ( ! is_object( $this_post ) ) {
			return;
		}

		if ( ! isset( $this_post->ID ) ) {
			return;
		}

		if ( has_blocks( $this_post->ID ) && isset( $this_post->post_content ) ) {

			$blocks            = $this->parse( $this_post->post_content );
			self::$page_blocks = $blocks;

			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return;
			}

			self::$stylesheet .= get_post_meta( $this_post->ID, '_drwidgetsblocks_css', true );
			self::$script     .= '';
		}
	}

	/**
	 * Generates stylesheet for reusable blocks.
	 *
	 * @param array $blocks Blocks array.
	 * @since 1.0.0
	 */
	public function get_blocks_fonts( $blocks ) {

		$blocks_fonts = array();

		foreach ( $blocks as $i => $block ) {

			if ( is_array( $block ) ) {

				if ( '' === $block['blockName'] ) {
					continue;
				}
				if ( 'core/block' === $block['blockName'] ) {
					$id = ( isset( $block['attrs']['ref'] ) ) ? $block['attrs']['ref'] : 0;

					if ( $id ) {
						$content = get_post_field( 'post_content', $id );

						$reusable_blocks = $this->parse( $content );

						$assets[ $i ] = $this->get_blocks_fonts( $reusable_blocks );

					}
				} else {
					$_block_fonts = $this->get_block_fonts( $block );
					if ( is_array( $_block_fonts ) ) {
						foreach ( $_block_fonts as $_block_font ) {
							$blocks_fonts = array_merge( $blocks_fonts, $_block_font );
						}
					}
				}
			}
		}

		return $blocks_fonts;
	}

	function flatten( array $array ) {
		$return = array();
		array_walk_recursive(
			$array,
			function( $a ) use ( &$return ) {
				$return[] = $a;
			}
		);
		return $return;
	}

	/**
	 * Parse Guten Block.
	 *
	 * @param string $content the content string.
	 * @since 1.0.0
	 */
	public function parse( $content ) {

		global $wp_version;

		return ( version_compare( $wp_version, '5', '>=' ) ) ? parse_blocks( $content ) : gutenberg_parse_blocks( $content );
	}

	/**
	 * Get Blocks Fonts.
	 *
	 * @param object $block The block object.
	 * @since 1.0.0
	 */
	public function get_block_fonts( $block ) {

		$block = (array) $block;

		$block_fonts = array();
		$name        = $block['blockName'];
		$block_id    = '';

		if ( ! isset( $name ) ) {
			return array();
		}

		if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
			$blockattr = $block['attrs'];
			if ( isset( $blockattr['block_id'] ) ) {
				$block_id = $blockattr['block_id'];
			}
		}

		switch ( $name ) {

			case 'dr-widgets-blocks/recipe-posts':
				$posts_fonts = Block_Helpers::get_block_fonts( 'Recipe Posts', $blockattr );
				array_push( $block_fonts, $posts_fonts );
				break;

			case 'dr-widgets-blocks/recipe-categories':
				$category_fonts = Block_Helpers::get_block_fonts( 'Recipe Categories', $blockattr );
				array_push( $block_fonts, $category_fonts );
				break;

			case 'dr-widgets-blocks/recipe-categories-tab':
				$category_tabs_fonts = Block_Helpers::get_block_fonts( 'Recipe Categories Tab', $blockattr );
				array_push( $block_fonts, $category_tabs_fonts );
				break;

			case 'dr-widgets-blocks/recipe-posts-carousel':
				$posts_carousel_fonts = Block_Helpers::get_block_fonts( 'Recipe Posts Carousel', $blockattr );
				array_push( $block_fonts, $posts_carousel_fonts );
				break;

			default:
				// Nothing to do here.
				break;
		}

		$block_fonts = apply_filters( 'dr_widgets_blocks_get_block_fonts', $block_fonts, $name, $blockattr );
		$block_fonts = is_array( $block_fonts ) ? array_unique( $block_fonts, SORT_REGULAR ) : array();

		return $block_fonts;
	}

	public function load_dynamic_google_fonts() {
		$has_dynamic_google_fonts = apply_filters(
			'dr-widgets-blocks:typography:google:use-remote',
			true
		);

		if ( ! $has_dynamic_google_fonts ) {
			return;
		}

		$static = $this->get_static_fonts_ids();

		$url = $this->get_google_fonts_url(
			array_merge(
				$static,
				array()
			)
		);

		if ( ! empty( $url ) ) {
			wp_register_style( 'drwb-fonts-source-google', $url, array(), null );
			wp_enqueue_style( 'drwb-fonts-source-google' );
		}
	}

	private function get_google_fonts_url( $fonts_ids = array() ) {
		$all_fonts = $this->get_system_fonts();

		$system_fonts_families = array();

		foreach ( $all_fonts as $single_google_font ) {
			$system_fonts_families[] = $single_google_font['family'];
		}

		$to_enqueue = array();

		$default_family = get_theme_mod(
			'rootTypography',
			dr_widgets_blocks_typography_default_values(
				array(
					'fontFamily'     => 'System Default',
					'variation'      => 'n4',
					'size'           => '17px',
					'lineHeight'     => '1.65',
					'letterSpacing'  => '0em',
					'textTransform'  => 'normal',
					'textDecoration' => 'none',
				)
			)
		);

		$default_variation = isset($default_family['variation']) ? $default_family['variation'] : 'n4';
		$default_family    = isset($default_family['fontFamily']) ? $default_family['fontFamily'] : 'System Default';

		$all_google_fonts = $this->get_googgle_fonts( true );

		foreach ( $fonts_ids as $font_id ) {
			if ( is_array( $font_id ) ) {
				$value = $font_id;
			} else {
				$value = get_theme_mod( $font_id, null );
			}

			if ( $value && isset( $value['fontFamily'] ) && $value['fontFamily'] === 'Default' ) {
				$value['fontFamily'] = $default_family;
			}

			if ( $value && isset( $value['variation'] ) && $value['variation'] === 'Default' ) {
				$value['variation'] = $default_variation;
			}

			if (
				! $value
				||
				! isset( $value['fontFamily'] )
				||
				in_array( $value['fontFamily'], $system_fonts_families )
				||
				$value['fontFamily'] === 'Default'
				||
				! isset( $all_google_fonts[ $value['fontFamily'] ] )
			) {
				continue;
			}

			// Ensure 'variation' key exists before using it
			$variation = isset($value['variation']) ? $value['variation'] : $default_variation;

			if ( ! isset( $to_enqueue[ $value['fontFamily'] ] ) ) {
				$to_enqueue[ $value['fontFamily'] ] = array( $variation );
			} else {
				$to_enqueue[ $value['fontFamily'] ][] = $variation;
			}

			$to_enqueue[ $value['fontFamily'] ] = array_unique(
				$to_enqueue[ $value['fontFamily'] ]
			);
		}

		$url = 'https://fonts.googleapis.com/css2?';

		$families = array();

		foreach ( $to_enqueue as $family => $variations ) {
			$to_push = 'family=' . $family . ':';

			$ital_vars = array();
			$wght_vars = array();

			foreach ( $variations as $variation ) {
				$var_to_push  = intval( $variation[1] ) * 100;
				$var_to_push .= $variation[0] === 'i' ? 'i' : '';

				if ( $variation[0] === 'i' ) {
					$ital_vars[] = intval( $variation[1] ) * 100;
				} else {
					$wght_vars[] = intval( $variation[1] ) * 100;
				}
			}

			sort( $ital_vars );
			sort( $wght_vars );

			$axis_tag_list = array();

			if ( count( $ital_vars ) > 0 ) {
				$axis_tag_list[] = 'ital';
			}

			if ( count( $wght_vars ) > 0 ) {
				$axis_tag_list[] = 'wght';
			}

			$to_push .= implode( ',', $axis_tag_list );
			$to_push .= '@';

			$all_vars = array();

			foreach ( $ital_vars as $ital_var ) {
				$all_vars[] = '0,' . $ital_var;
			}

			foreach ( $wght_vars as $wght_var ) {
				if ( count( $axis_tag_list ) > 1 ) {
					$all_vars[] = '1,' . $wght_var;
				} else {
					$all_vars[] = $wght_var;
				}
			}

			$to_push .= implode( ';', $all_vars );

			$families[] = $to_push;
		}

		$families = implode( '&', $families );

		if ( ! empty( $families ) ) {
			$url .= $families;
			$url .= '&display=swap';

			return $url;
		}

		return false;
	}

	public function get_system_fonts() {
		$system = array(
			'System Default',
			'Arial',
			'Verdana',
			'Trebuchet',
			'Georgia',
			'Times New Roman',
			'Palatino',
			'Helvetica',
			'Myriad Pro',
			'Lucida',
			'Gill Sans',
			'Impact',
			'Serif',
			'monospace',
		);

		$result = array();

		foreach ( $system as $font ) {
			$result[] = array(
				'source'         => 'system',
				'family'         => $font,
				'variations'     => array(),
				'all_variations' => $this->get_standard_variations_descriptors(),
			);
		}

		return $result;
	}

	public function get_standard_variations_descriptors() {
		return array(
			'n1',
			'i1',
			'n2',
			'i2',
			'n3',
			'i3',
			'n4',
			'i4',
			'n5',
			'i5',
			'n6',
			'i6',
			'n7',
			'i7',
			'n8',
			'i8',
			'n9',
			'i9',
		);
	}

	public function all_google_fonts() {
		$saved_data = get_option( 'dr_widgets_blocks_google_fonts', false );
		$ttl        = 7 * DAY_IN_SECONDS;

		if (
			false === $saved_data
			||
			( ( $saved_data['last_update'] + $ttl ) < time() )
			||
			! is_array( $saved_data )
			||
			! isset( $saved_data['fonts'] )
			||
			empty( $saved_data['fonts'] )
		) {
			$response = wp_remote_get(
				plugin_dir_url( DR_WIDGETS_BLOCKS_PLUGIN_FILE ) . '/src/google-fonts/google-fonts.json'
			);

			$body = wp_remote_retrieve_body( $response );

			if (
				200 === wp_remote_retrieve_response_code( $response )
				&&
				! is_wp_error( $body ) && ! empty( $body )
			) {
				update_option(
					'dr_widgets_blocks_google_fonts',
					array(
						'last_update' => time(),
						'fonts'       => $body,
					),
					false
				);

				return $body;
			} else {
				if ( empty( $saved_data['fonts'] ) ) {
					$saved_data['fonts'] = wp_json_encode( array( 'items' => array() ) );
				}

				update_option(
					'dr_widgets_blocks_google_fonts',
					array(
						'last_update' => time() - $ttl + MINUTE_IN_SECONDS,
						'fonts'       => $saved_data['fonts'],
					),
					false
				);
			}
		}

		return $saved_data['fonts'];
	}

	public function get_googgle_fonts( $as_keys = false ) {
		$maybe_custom_source = apply_filters(
			'dr-widgets-blocks-typography-google-fonts-source',
			null
		);

		if ( $maybe_custom_source ) {
			return $maybe_custom_source;
		}

		$response = $this->all_google_fonts();
		$response = json_decode( $response, true );

		if ( ! isset( $response['items'] ) ) {
			return false;
		}

		if ( ! is_array( $response['items'] ) || ! count( $response['items'] ) ) {
			return false;
		}

		foreach ( $response['items'] as $key => $row ) {
			$response['items'][ $key ] = $this->prepare_font_data( $row );
		}

		if ( ! $as_keys ) {
			return $response['items'];
		}

		$result = array();

		foreach ( $response['items'] as $single_item ) {
			$result[ $single_item['family'] ] = true;
		}

		return $result;
	}

	private function prepare_font_data( $font ) {
		$font['source'] = 'google';

		$font['variations'] = array();

		if ( isset( $font['variants'] ) ) {
			$font['all_variations'] = $this->change_variations_structure( $font['variants'] );
		}

		unset( $font['variants'] );
		return $font;
	}

	private function change_variations_structure( $structure ) {
		$result = array();

		foreach ( $structure as $weight ) {
			$result[] = $this->get_weight_and_style_key( $weight );
		}

		return $result;
	}

	private function get_weight_and_style_key( $code ) {
		$prefix = 'n'; // Font style: italic = `i`, regular = n.
		$sufix  = '4';  // Font weight: 1 -> 9.

		$value = strtolower( trim( $code ) );
		$value = str_replace( ' ', '', $value );

		// Only number.
		if ( is_numeric( $value ) && isset( $value[0] ) ) {
			$sufix  = $value[0];
			$prefix = 'n';
		}

		// Italic.
		if ( preg_match( '#italic#', $value ) ) {
			if ( 'italic' === $value ) {
				$sufix  = 4;
				$prefix = 'i';
			} else {
				$value = trim( str_replace( 'italic', '', $value ) );
				if ( is_numeric( $value ) && isset( $value[0] ) ) {
					$sufix  = $value[0];
					$prefix = 'i';
				}
			}
		}

		// Regular.
		if ( preg_match( '#regular|normal#', $value ) ) {
			if ( 'regular' === $value ) {
				$sufix  = 4;
				$prefix = 'n';
			} else {
				$value = trim( str_replace( array( 'regular', 'normal' ), '', $value ) );
				if ( is_numeric( $value ) && isset( $value[0] ) ) {
					$sufix  = $value[0];
					$prefix = 'n';
				}
			}
		}

		return "{$prefix}{$sufix}";
	}
}

if ( ! function_exists( 'dr_widgets_blocks_typography_default_values' ) ) {
	function dr_widgets_blocks_typography_default_values( $values = array() ) {
		return array_merge(
			array(
				'fontFamily'     => 'Default',
				'variation'      => 'Default',

				'size'           => '17px',
				'lineHeight'     => '1.65',
				'letterSpacing'  => '0em',
				'textTransform'  => 'normal',
				'textDecoration' => 'none',

				'size'           => 'CT_CSS_SKIP_RULE',
				'lineHeight'     => 'CT_CSS_SKIP_RULE',
				'letterSpacing'  => 'CT_CSS_SKIP_RULE',
				'textTransform'  => 'CT_CSS_SKIP_RULE',
				'textDecoration' => 'CT_CSS_SKIP_RULE',
			),
			$values
		);
	}
}

add_action(
	'wp_ajax_dr_widgets_blocks_get_fonts_list',
	function () {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error();
		}

		$fontsmanager = new Fonts_Manager();

		wp_send_json_success(
			array(
				'fonts' => $fontsmanager->get_all_fonts(),
			)
		);
	}
);
