<?php
namespace DR_Widgets_Blocks;
/**
 * DR_Widgets_Blocks Block Helper.
 *
 * @package DR_Widgets_Blocks
 */
if ( ! class_exists( 'Block_Helpers' ) ) {

	/**
	 * Class Block_Helpers.
	 */
	class Block_Helpers {

		public static function get_block_fonts( $blockname, $attr ) {
			$block_fonts   = array();

			$blockname = sanitize_title( $blockname );
			$blockname = str_replace( '-', '_', $blockname );
			
			$function_name  = "{$blockname}_fonts";
			$block_fonts = self::$function_name( $attr );

			return $block_fonts;
		}

		public static function recipe_posts_fonts( $attr ) {
			return array(
				dr_widgets_blocks_rand_md5() => isset( $attr['titleTypography'] ) ? $attr['titleTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['excerptTypography'] ) ? $attr['excerptTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['metaTypography'] ) ? $attr['metaTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['categoryTypography'] ) ? $attr['categoryTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['readMoreButtonTypography'] ) ? $attr['readMoreButtonTypography'] : array(),
			);
		}

		public static function recipe_categories_fonts( $attr ) {
			return array(
				dr_widgets_blocks_rand_md5() => isset( $attr['titleTypography'] ) ? $attr['titleTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['countTypography'] ) ? $attr['countTypography'] : array(),
			);
		}

		public static function recipe_categories_tab_fonts( $attr ) {
			return array(
				dr_widgets_blocks_rand_md5() => isset( $attr['titleTypography'] ) ? $attr['titleTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['metaTypography'] ) ? $attr['metaTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['headingTitleTypography'] ) ? $attr['headingTitleTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['tabsTypography'] ) ? $attr['tabsTypography'] : array(),
			);
		}

		public static function recipe_posts_carousel_fonts( $attr ) {
			return array(
				dr_widgets_blocks_rand_md5() => isset( $attr['titleTypography'] ) ? $attr['titleTypography'] : array(),
				dr_widgets_blocks_rand_md5() => isset( $attr['metaTypography'] ) ? $attr['metaTypography'] : array(),
			);
		}

	}
}
