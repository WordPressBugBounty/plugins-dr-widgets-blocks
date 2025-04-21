<?php 

namespace DR_Widgets_Blocks;

use DR_Widgets_Blocks\Widget;

if(!$all_taxonomy){
    if ( $taxonomy && $terms ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'terms'    => $terms,
                'field'    => 'term_id',
            ),
        );
    } elseif ( $taxonomy ) {
        $args['taxonomy'] = $taxonomy;
    }
} else {
    // Build tax_query args for filtering recipes by multiple taxonomy terms
    // Loops through selected term IDs, parses taxonomy and term from each,
    // and creates an array of tax queries with OR relation between them
    if ( !empty($all_term_id) && is_array($all_term_id) ) {
        $tax_array = array();
        foreach($all_term_id as $item) {
            $tax_array[] = array(
                'taxonomy' => dr_widgets_blocks_parse_term_id($item, true),
                'terms'    => dr_widgets_blocks_parse_term_id($item, false),
                'field'    => 'term_id',
            );
        }

        // Merge the tax_query args with OR relation between them
        $args['tax_query'] = array_merge(array(
            'relation' => 'OR',
        ), $tax_array);
    }
}

$recipes_query = new \WP_Query( $args );

if ( 0 === $recipes_query->post_count ) {
    ?>
        <p>
            <?php esc_html_e( 'Please check the widget settings and make sure you have selected a valid query.', 'dr-widgets-blocks' ); ?>
        </p>
    <?php
    return;
}

$wrapper_classes = array(
    'dr-widgetBlock_row',
	'dr-post-list-3',
    esc_attr( $layout_data ),
);

if (isset($this) && method_exists($this, 'add_render_attribute')) {
    // When called from Elementor widget
    $this->add_render_attribute('wrapper-classes', 'class', $wrapper_classes);
    $wrapper_attr = $this->get_render_attribute_string('wrapper-classes');
} else {
    // When called from Ajax
    $wrapper_attr = 'class="' . implode(' ', $wrapper_classes) . '"';
}
?>
    <div class="dr-widget">
			<div <?php echo $wrapper_attr; ?>>
				<?php
				$i = 0;
				if ( $recipes_query->have_posts() ) {
					while ( $recipes_query->have_posts() ) {
						$recipes_query->the_post();
						$recipe       = get_post( get_the_ID() );
						$recipe_metas = delicious_recipes_get_recipe( $recipe );
						$data         = array(
							'settings'     => array(
								'image_size'          => $image_size,
								'hero_image_size'     => $hero_image_size,
								'title_tag'           => $title_tag,
								'show_total_time'     => $show_total_time,
								'show_difficulty'     => $show_difficulty,
								'show_recipe_keys'    => $show_recipe_keys,
								'show_category'       => $show_category,
								'separator'           => $separator,
								'layout'			  => $layout_data,
								'counter'			  => $counter,
								'show_wishlist'       => $show_wishlist,
							),
							'recipe_metas' => $recipe_metas,
							'count' => $i,
						);
						dr_widgets_blocks_get_template( 'recipe-post-list-three.php', $data );
						$i++;
					}
					wp_reset_postdata();
				}
				?>
			</div>
		</div>
<?php
$total_pages = $recipes_query->max_num_pages;
if( $show_pagination ) {
    if ( $total_pages > 1 ) {
        if( 'number' === $pagination_type ) {?>
            <div class="dr-widget-pagination">
                <?php
                    echo paginate_links( array(
                        'total'   => $total_pages,
                        'current' => $paged,
                        'format'  => '?paged=%#%',
                        'prev_text' => esc_html($prev_text),
                        'next_text' => esc_html($next_text),
                    ) ); ?>
            </div>
        <?php
        }
    }
}
?>