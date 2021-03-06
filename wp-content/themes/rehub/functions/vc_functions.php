<?php
//////////////////////////////////////////////////////////////////
// Visual Composer functions
//////////////////////////////////////////////////////////////////

function wpse_rehub_make_searchable( $post_type, $args ) {
    // Make sure we're only editing the post type we want
    if ( 'thirstylink' != $post_type )
        return;

    // Make thirstyaffiliate searchable
    $args->exclude_from_search = false;

    // Modify post type object
    $wp_post_types[$post_type] = $args;
}
add_action( 'registered_post_type', 'wpse_rehub_make_searchable', 10, 2 );


//REMOVE SOME DEFAULT ELEMENTS
vc_remove_element( 'vc_images_carousel' );
vc_remove_element( 'vc_teaser_grid' );
vc_remove_element( 'vc_posts_grid' );
vc_remove_element( 'vc_carousel' );
vc_remove_element( 'vc_posts_slider' );
vc_remove_element( 'vc_wp_recentcomments' );
vc_remove_element( 'vc_wp_calendar' );
vc_remove_element( 'vc_wp_tagcloud' );
vc_remove_element( 'vc_wp_text' );
vc_remove_element( 'vc_wp_links' );
vc_remove_element( 'vc_wp_archives' );
vc_remove_element( 'vc_cta_button' );
vc_remove_element( 'vc_basic_grid' );
vc_remove_element( 'vc_media_grid' );
vc_remove_element( 'vc_masonry_grid' );
vc_remove_element( 'vc_masonry_media_grid' );
function rehub_vc_remove_woocommerce() {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        vc_remove_element( 'woocommerce_cart' );
        vc_remove_element( 'woocommerce_checkout' );
        vc_remove_element( 'woocommerce_order_tracking' );
        vc_remove_element( 'woocommerce_my_account' );
        vc_remove_element( 'recent_products' );
        vc_remove_element( 'featured_products' );
        vc_remove_element( 'product' );
        vc_remove_element( 'products' );
        vc_remove_element( 'add_to_cart' );
        vc_remove_element( 'add_to_cart_url' );
        vc_remove_element( 'product_page' );
        vc_remove_element( 'product_category' );
        vc_remove_element( 'product_categories' );
        vc_remove_element( 'sale_products' );
        vc_remove_element( 'best_selling_products' );
        vc_remove_element( 'top_rated_products' );
        vc_remove_element( 'product_attribute' );
    }
}
add_action( 'vc_build_admin_page', 'rehub_vc_remove_woocommerce', 11 );
add_action( 'vc_load_shortcode', 'rehub_vc_remove_woocommerce', 11 );



//Disable frontend
vc_disable_frontend();

//Set default post types
vc_set_default_editor_post_types( array('page') );

$dir_for_vc = get_template_directory() . '/functions/vc_templates';
vc_set_shortcodes_templates_dir( $dir_for_vc );

//WIDGET BLOCK
vc_remove_param("vc_widget_sidebar", "title");

//ROW BLOCK
add_action( 'vc_after_init_base', 'add_more_rehub_layouts' );
function add_more_rehub_layouts() {
    global $vc_row_layouts;
    array_push( $vc_row_layouts, array(
        'cells' => '34_14',
        'mask' => '212',
        'title' => '3/4 + 1/4',
        'icon_class' => 'l_34_14')
    );    
}

vc_remove_param("vc_row", "full_width");
vc_add_param("vc_row", array(
	"type" => "dropdown",
	"class" => "",
	"heading" => __("Type of row width", "rehub_framework"),
	"param_name" => "bg_width_type",
	"admin_label" => true,
	"value" => array(
		__( 'Inside content container', 'rehub_framework' ) => "simple",
		__( 'Full width of content container', 'rehub_framework' ) => "container_width",
		__( 'Full width of browser window', 'rehub_framework' ) => "window_width",
	),
));

vc_add_param("vc_row", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => __("Container with sidebar?", "rehub_framework"),
	"value" => array(__("Yes", "rehub_framework") => "true" ),
	"param_name" => "rehub_container",
	"dependency" => array("element" => "bg_width_type", "value" => array("simple")),
	"description" => __("Is this container with sidebar? Enable this option and use 2/3 + 1/3 layout for better compatibility if you want to add sidebar widget area.", "rehub_framework")
));

vc_add_param("vc_row", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => __("Make inner container center alignment?", "rehub_framework"),
	"value" => array(__("Yes", "rehub_framework") => "true" ),
	"param_name" => "centered_container",
	"description" => __("This option is good for full width pages. It leaves background of row as full widht, but makes inner column centered in window with max-width 1170px.", "rehub_framework")
));

$setting_row = array (
  'show_settings_on_create' => true,
);
vc_map_update( 'vc_row', $setting_row ); 

add_action( 'vc_before_init', 'rehub_integrateWithVC' );
function rehub_integrateWithVC() {

vc_set_as_theme();	

//GET CATEGORY FUNCTION
if( !function_exists('rehub_category_chooser') ) {
    function rehub_category_chooser($type = 'all') {
        $blog_types = get_categories();
        if ($type == 'all') {
           $rehub_category_chooser = array("All" => "all"); 
        }
        else {
            $rehub_category_chooser = array();
        }       
        foreach ($blog_types as $type) {
        	$rehub_category_chooser[$type->name] = $type->cat_ID;
        }
        return $rehub_category_chooser;
    }
}


//Where to open window
$target_arr = array(__("Same window", "js_composer") => "_self", __("New window", "js_composer") => "_blank");

//Post format chooser
$post_formats = array(   
	__('all', 'rehub_framework') => 'all',
 	__('regular', 'rehub_framework') => 'regular',
 	__('video', 'rehub_framework') => 'video',
 	__('gallery', 'rehub_framework') => 'gallery',
 	__('review', 'rehub_framework') => 'review',
 	__('music', 'rehub_framework') => 'music',              
);

//Category Chooser
if( !function_exists('rehub_catchooser') ) {
    function rehub_catchooser() {
        $postcats = get_terms( 'category'  );
        $catchooser = array();
        foreach ( $postcats as $t ) {
        	$catchooser[] = array(
        		'label' => $t->name,
        		'value' => $t->term_id,
        	);
        }
        return $catchooser;
    }
}

//Tag Chooser
if( !function_exists('rehub_tagchooser') ) {
    function rehub_tagchooser() {
        $posttags = get_terms( 'post_tag'  );
        $tagchooser = array();
        foreach ( $posttags as $t ) {
            $tagchooser[] = array(
                'label' => $t->name,
                'value' => $t->term_id,
            );
        }
        return $tagchooser;
    }
}

//CPT chooser
if( !function_exists('rehub_post_type_vc') ) {
    function rehub_post_type_vc() {
        $post_types = get_post_types( array('public'   => true) );
        $post_types_list = array();
        foreach ( $post_types as $post_type ) {
            if ( $post_type !== 'revision' && $post_type !== 'nav_menu_item' && $post_type !== 'attachment') {
                $label = ucfirst( $post_type );
                $post_types_list[] = array( $post_type, __( $label, 'js_composer' ) );
            }
        }
        return $post_types_list;
    }
}


//Post chooser
add_filter( 'vc_autocomplete_small_thumb_loop_ids_callback',
	'rehub_post_search', 10, 1 );
add_filter( 'vc_autocomplete_small_thumb_loop_ids_render',
	'rehub_post_render', 10, 1 );
add_filter( 'vc_autocomplete_regular_blog_loop_ids_callback',
	'rehub_post_search', 10, 1 );
add_filter( 'vc_autocomplete_regular_blog_loop_ids_render',
	'rehub_post_render', 10, 1 );
add_filter( 'vc_autocomplete_grid_loop_mod_ids_callback',
	'rehub_post_search', 10, 1 );
add_filter( 'vc_autocomplete_grid_loop_mod_ids_render',
	'rehub_post_render', 10, 1 );
add_filter( 'vc_autocomplete_columngrid_loop_ids_callback',
    'rehub_post_search', 10, 1 );
add_filter( 'vc_autocomplete_columngrid_loop_ids_render',
    'rehub_post_render', 10, 1 );
add_filter( 'vc_autocomplete_compactgrid_loop_mod_ids_callback',
    'rehub_post_search', 10, 1 );
add_filter( 'vc_autocomplete_compactgrid_loop_mod_ids_render',
    'rehub_post_render', 10, 1 );

function rehub_post_search( $search_string ) {
	$query = $search_string;
	$data = array();
	$args = array( 's' => $query, 'post_type' => 'post' );
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = - 1;
	if ( strlen( $args['s'] ) == 0 ) {
		unset( $args['s'] );
	}
	add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		$data[] = array(
			'value' => $post->ID,
			'label' => $post->post_title,
		);
	}
	return $data;
}

function rehub_post_render( $value ) {
	$post = get_post( $value['value'] );

	return is_null( $post ) ? false : array(
		'label' => $post->post_title,
		'value' => $post->ID,
	);
}


//HOME FEATURED SECTION
vc_map( array(
    "name" => __('Featured section', 'rehub_framework'),
    "base" => "wpsm_featured",
    "icon" => "icon-featured",
    "category" => __('For homepage', 'rehub_framework'),
    'description' => __('Use only in full width row', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "heading" => __('Base', 'rehub_framework'),
            "param_name" => "base",
            "value" => array(
				__('Show last featured posts', 'rehub_framework') => "1",
				__('Based on tag', 'rehub_framework') => "2",	
			),
			'description' => __('Choose base for displaying this block', 'rehub_framework'),
        ),
        array(
            "type" => "textfield",
            "heading" => __('Slug of tag', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "tag",
			'description' => __('Set slug of tag', 'rehub_framework'),
			"dependency" => Array('element' => "base", 'value' => array('2'))
        ),        
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "fetch",
            "value" => '4',
			'description' => __('How much posts you\'d like to display in slider?', 'rehub_framework'),
        ),
        array(
            "type" => "checkbox",
            "heading" => __('Disable exerpt?', 'rehub_child'),
            "value" => array(__("Yes", "rehub_child") => true ),
            "param_name" => "dis_exerpt",
        ),                         

    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Wpsm_Featured extends WPBakeryShortCode {
    }
}

//HOME CAROUSEL SECTION
vc_map( array(
    "name" => __('Full width carousel', 'rehub_framework'),
    "base" => "full_carousel",
    "icon" => "icon-f-carousel",
    "category" => __('For homepage', 'rehub_framework'),
    'description' => __('Use only in full width row', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "heading" => __('Base', 'rehub_framework'),
            "param_name" => "base",
            "value" => array(
				__('Show last Editor\'s choice posts', 'rehub_framework') => "1",
				__('Based on tag', 'rehub_framework') => "2",	
			),
			'description' => __('Choose base for displaying this block. You need to have minimum 5 posts for correct work of carousel section. Editor\'s choice label you can set in options of each post on right section.', 'rehub_framework'),
        ),
        array(
            "type" => "textfield",
            "heading" => __('Slug of tag', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "tag",
			'description' => __('Set slug of tag', 'rehub_framework'),
			"dependency" => Array('element' => "base", 'value' => array('2')),
        ),        
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "fetch",
            "value" => '10',
			'description' => __('How much posts you\'d like to display in slider?', 'rehub_framework'),
        ), 
		array(
			"type" => "checkbox",
			"heading" => __('Show badge?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "badge",
		),  
        array(
            "type" => "dropdown",
            "heading" => __('Choose color of badge', 'rehub_framework'),
            "param_name" => "badge_color",
            "value" => array(
				__('Orange', 'rehub_framework') => "orange",
				__('Blue', 'rehub_framework') => "blue",	
				__('Green', 'rehub_framework') => "green",
				__('Violet', 'rehub_framework') => "violet",				
			),
			"dependency" => Array('element' => "badge", 'not_empty' => true),
        ),
        array(
            "type" => "textarea_raw_html",
            "heading" => __('Set text on label', 'rehub_framework'),
            "param_name" => "badge_text",
            "value" => base64_encode( 'Editor\'s <span>choice</span>'),
			"dependency" => Array('element' => "badge", 'not_empty' => true),
        ),         		                       

    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Full_Carousel extends WPBakeryShortCode {
    }
}

//NEWS WITH THUMBNAILS BLOCK
vc_map( array(
    "name" => __("News with thumbnails", "rehub_framework"),
    "base" => "news_with_thumbs_mod",
    "category" => __('For row with sidebar', 'rehub_framework'), 
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "icon" => "icon-n-w-thumbs",
    "params" => array(
        // add params same as with any other content element
        array(
            "type" => "dropdown",
            "heading" => __("Category", "rehub_framework"),
            "param_name" => "module_cats",
            "admin_label" => true,
            "value" => rehub_category_chooser(),
            'description' => __('Choose the category that you\'d like to include to block', 'rehub_framework')
        )
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_News_With_Thumbs_Mod extends WPBakeryShortCode {
    }
}

//NEWS WITHOUT THUMBNAILS BLOCK
vc_map( array(
    "name" => __("News without thumbnails", "rehub_framework"),
    "base" => "news_no_thumbs_mod",
    "category" => __('For row with sidebar', 'rehub_framework'), 
    'description' => __('For row with sidebar', 'rehub_framework'),
    "icon" => "icon-n-n-thumbs",    
    "params" => array(
        // add params same as with any other content element
        array(
            "type" => "dropdown",
            "heading" => __("Category", "rehub_framework"),
            "admin_label" => true,
            "param_name" => "module_cats",
            "value" => rehub_category_chooser(),
            'description' => __('Choose the category that you\'d like to include to block', 'rehub_framework')
        )
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_News_No_Thumbs_Mod extends WPBakeryShortCode {
    }
}

//IMAGE CAROUSEL BLOCK
vc_map( array(
    "name" => __("Image carousel", "rehub_framework"),
    "base" => "gal_carousel",
    "icon" => "icon-gal-carousel",
    "category" => __('Content', 'js_composer'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "attach_images",
            "heading" => __("Images", "rehub_framework"),
            "param_name" => "images",
            "value" => "",
            "description" => __("Select images from media library.", "rehub_framework")
        ),
        array(
            "type" => "dropdown",
            "heading" => __("On click", "rehub_framework"),
            "param_name" => "onclick",
            "value" => array(__("Open prettyPhoto", "rehub_framework") => "link_image", __("Do nothing", "rehub_framework") => "link_no", __("Open custom link", "rehub_framework") => "custom_link"),
            "description" => __("What to do when slide is clicked?", "rehub_framework")
        ),
        array(
            "type" => "exploded_textarea",
            "heading" => __("Custom links", "rehub_framework"),
            "param_name" => "custom_links",
            "description" => __('Enter links for each slide here. Divide links with linebreaks (Enter).', 'rehub_framework'),
            "dependency" => Array('element' => "onclick", 'value' => array('custom_link'))
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Custom link target", "rehub_framework"),
            "param_name" => "custom_links_target",
            "description" => __('Select where to open  custom links.', 'rehub_framework'),
            "dependency" => Array('element' => "onclick", 'value' => array('custom_link')),
            'value' => $target_arr
        ),
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", "rehub_framework"),
            "param_name" => "el_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "rehub_framework")
        )
    )
) );
require_once vc_path_dir('SHORTCODES_DIR', 'vc-gallery.php');
if ( class_exists( 'WPBakeryShortCode_VC_gallery' ) ) {
    class WPBakeryShortCode_Gal_Carousel extends WPBakeryShortCode_VC_gallery {

    }
}

//TWO COLUMN NEWS BLOCK
vc_map( array(
    "name" => __('Two column news block', 'rehub_framework'),
    "base" => "two_col_news",
    "icon" => "icon-t-c-news",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "heading" => __('Category for 1 column', 'rehub_framework'),
            "param_name" => "module_cats_first",
            "admin_label" => true,
            "value" => rehub_category_chooser('noall'),
			'description' => __('Choose the category that you\'d like to include to first column', 'rehub_framework'),
        ),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats for 1 column', 'rehub_framework'),
            "param_name" => "module_formats_first",
            "value" => $post_formats,
			'description' => __('Choose post formats to display in first column or leave blank to display all', 'rehub_framework'),
        ),        
        array(
            "type" => "dropdown",
            "heading" => __('Category for 2 column', 'rehub_framework'),
            "param_name" => "module_cats_second",
            "admin_label" => true,
            "value" => rehub_category_chooser('noall'),
			'description' => __('Choose the category that you\'d like to include to second column', 'rehub_framework'),
        ),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats for 2 column', 'rehub_framework'),
            "param_name" => "module_formats_second",
            "value" => $post_formats,
			'description' => __('Choose post formats to display in second column or leave blank to display all', 'rehub_framework'),
        ), 
        array(
            "type" => "textfield",
            "heading" => __('Offset for 2 column', 'rehub_framework'),
            "param_name" => "module_offset_second",
			'description' => __('Number of posts to offset for second column  or leave blank', 'rehub_framework'),
        ), 
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "module_fetch",
            "value" => '4',
			'description' => __('How much posts you\'d like to display?', 'rehub_framework'),
        ),                 

    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Two_Col_News extends WPBakeryShortCode {
    }
}

//VIDEO NEWS BLOCK
vc_map( array(
    "name" => __('Video news block', 'rehub_framework'),
    "base" => "video_mod",
    "icon" => "icon-v-n-block",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "textfield",
            "heading" => __('Set slug of tag for block', 'rehub_framework'),
            "param_name" => "loop_tags",
			'description' => __('Set tag slug from which to display content in block or leave blank', 'rehub_framework'),
        ),                  
    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Video_Mod extends WPBakeryShortCode {
    }
}


//1-4 tabed block
vc_map( array(
    "name" => __('Tabbed block', 'rehub_framework'),
    "base" => "tab_mod",
    "icon" => "icon-tab-block",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "heading" => __('Category for 1 tab', 'rehub_framework'),
            "param_name" => "module_cats_first",
            "admin_label" => true,
            "value" => rehub_category_chooser('noall'),
			'description' => __('Choose the category that you\'d like to include to first tab', 'rehub_framework'),
        ),
        array(
            "type" => "textfield",
            "heading" => __('Choose name for 1 tab', 'rehub_framework'),
            "param_name" => "module_name_first",
			'description' => __('Note, name must be maximum 15 symbols', 'rehub_framework'),
        ), 
        array(
            "type" => "dropdown",
            "heading" => __('Category for 2 tab', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "module_cats_second",
            "value" => rehub_category_chooser('noall'),
			'description' => __('Choose the category that you\'d like to include to second tab', 'rehub_framework'),
        ),
        array(
            "type" => "textfield",
            "heading" => __('Choose name for 2 tab', 'rehub_framework'),
            "param_name" => "module_name_second",
			'description' => __('Note, name must be maximum 15 symbols', 'rehub_framework'),
        ),
        array(
            "type" => "dropdown",
            "heading" => __('Category for 3 tab', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "module_cats_third",
            "value" => rehub_category_chooser('noall'),
			'description' => __('Choose the category that you\'d like to include to third tab', 'rehub_framework'),
        ),
        array(
            "type" => "textfield",
            "heading" => __('Choose name for 3 tab', 'rehub_framework'),
            "param_name" => "module_name_third",
			'description' => __('Note, name must be maximum 15 symbols', 'rehub_framework'),
        ),
        array(
            "type" => "dropdown",
            "heading" => __('Category for 4 tab', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "module_cats_fourth",
            "value" => rehub_category_chooser('noall'),
			'description' => __('Choose the category that you\'d like to include to fourth tab', 'rehub_framework'),
        ),
        array(
            "type" => "textfield",
            "heading" => __('Choose name for 4 tab', 'rehub_framework'),
            "param_name" => "module_name_fourth",
			'description' => __('Note, name must be maximum 15 symbols', 'rehub_framework'),
        ),          
    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Tab_Mod extends WPBakeryShortCode {
    }
}


//POSTS LOOP WITH LEFT SMALL THUMBNAILS
vc_map( array(
    "name" => __('Posts with small thumbs', 'rehub_framework'),
    "base" => "small_thumb_loop",
    "icon" => "icon-s-t-loop",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Data source', 'rehub_framework'),
			"param_name" => "data_source",
			"value" => array(
				__('Category or tag', 'rehub_framework') => "cat",
				__('Manual select and order', 'rehub_framework') => "ids",
                __('Custom post type and taxonomy', 'rehub_framework') => "cpt",					
			), 
		),
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Category', 'rehub_framework' ),
			'param_name' => 'cat',
			'settings' => array(
				'multiple' => true,
				'min_length' => 2,
				'display_inline' => true,
				'values' => rehub_catchooser(),
			),
			'description' => __( 'Enter names of categories. Or leave blank to show all', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
		),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category exclude', 'rehub_framework' ),
            'param_name' => 'cat_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories to exclude', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),        
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags exclude', 'rehub_framework' ),
            'param_name' => 'tag_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags to exclude.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),              	
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Post names', 'rehub_framework' ),
			'param_name' => 'ids',
			'settings' => array(
				'multiple' => true,
				'sortable' => true,
				'groups' => false,
			),
			'description' => __( 'Or enter names of posts.', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'ids' ),
			),							
		),	
        array(
            'type' => 'dropdown',
            'heading' => __( 'Choose post type', 'rehub_framework' ),
            'param_name' => 'post_type',
            'value' => rehub_post_type_vc(),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),            
        ),   
        array(
            'type' => 'textfield',
            'heading' => __( 'Taxonomy name slug', 'rehub_framework' ),
            'param_name' => 'tax_name',
            'description' => __( 'Enter slug of your taxonomy. Example, taxonomy for posts - is category. Or leave blank', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ), 
        array(
            'type' => 'textfield',
            'heading' => __( 'Show posts by taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug',
            'description' => __( 'Enter slug of your taxonomy name (from field above) if you want to show only posts from certain taxonomy (from field above)', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),  
        array(
            'type' => 'textfield',
            'heading' => __( 'Exclude posts with taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug_exclude',
            'description' => __( 'Enter slug of your taxonomy name (from field above) to exclude posts', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),                            
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Number of comments', 'js_composer' ) => 'comment_count',				
				__( 'Meta value', 'js_composer' ) => 'meta_value',
				__( 'Meta value number', 'js_composer' ) => 'meta_value_num',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'description' => __( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Meta key', 'js_composer' ),
			'param_name' => 'meta_key',
			'description' => __( 'Input meta key for grid ordering.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'orderby',
				'value' => array( 'meta_value', 'meta_value_num' ),
			),
		),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats', 'rehub_framework'),
            "param_name" => "post_formats",
            "value" => $post_formats,
			'description' => __('Choose post formats to display or leave blank to display all', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),			
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
        ),	
        array(
            "type" => "textfield",
            "heading" => __('Offset', 'rehub_framework'),
            "param_name" => "offset",
            "value" => '',
			'description' => __('Number of products to offset', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),        	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '10',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),						
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __('Enable pagination?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "enable_pagination",
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
		),				                  
    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Small_Thumb_Loop extends WPBakeryShortCode {
    }
}
		

//BLOG STYLE LOOP
vc_map( array(
    "name" => __('Regular blog posts', 'rehub_framework'),
    "base" => "regular_blog_loop",
    "icon" => "icon-r-b-loop",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Data source', 'rehub_framework'),
            "param_name" => "data_source",
            "value" => array(
                __('Category or tag', 'rehub_framework') => "cat",
                __('Manual select and order', 'rehub_framework') => "ids",
                __('Custom post type and taxonomy', 'rehub_framework') => "cpt",                    
            ), 
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category', 'rehub_framework' ),
            'param_name' => 'cat',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category exclude', 'rehub_framework' ),
            'param_name' => 'cat_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories to exclude', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),        
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags exclude', 'rehub_framework' ),
            'param_name' => 'tag_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags to exclude.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),                  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Post names', 'rehub_framework' ),
            'param_name' => 'ids',
            'settings' => array(
                'multiple' => true,
                'sortable' => true,
                'groups' => false,
            ),
            'description' => __( 'Or enter names of posts.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'ids' ),
            ),                          
        ),  
        array(
            'type' => 'dropdown',
            'heading' => __( 'Choose post type', 'rehub_framework' ),
            'param_name' => 'post_type',
            'value' => rehub_post_type_vc(),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),            
        ),   
        array(
            'type' => 'textfield',
            'heading' => __( 'Taxonomy name slug', 'rehub_framework' ),
            'param_name' => 'tax_name',
            'description' => __( 'Enter slug of your taxonomy. Example, taxonomy for posts - is category. Or leave blank', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ), 
        array(
            'type' => 'textfield',
            'heading' => __( 'Show posts by taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug',
            'description' => __( 'Enter slug of your taxonomy name (from field above) if you want to show only posts from certain taxonomy (from field above)', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),  
        array(
            'type' => 'textfield',
            'heading' => __( 'Exclude posts with taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug_exclude',
            'description' => __( 'Enter slug of your taxonomy name (from field above) to exclude posts', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),                            
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Number of comments', 'js_composer' ) => 'comment_count',				
				__( 'Meta value', 'js_composer' ) => 'meta_value',
				__( 'Meta value number', 'js_composer' ) => 'meta_value_num',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'description' => __( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Meta key', 'js_composer' ),
			'param_name' => 'meta_key',
			'description' => __( 'Input meta key for grid ordering.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'orderby',
				'value' => array( 'meta_value', 'meta_value_num' ),
			),
		),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats', 'rehub_framework'),
            "param_name" => "post_formats",
            "value" => $post_formats,
			'description' => __('Choose post formats to display or leave blank to display all', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),			
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
        ),	
        array(
            "type" => "textfield",
            "heading" => __('Offset', 'rehub_framework'),
            "param_name" => "offset",
            "value" => '',
			'description' => __('Number of products to offset', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),        	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '10',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),						
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __('Enable pagination?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "enable_pagination",
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
		),				                  
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Regular_Blog_Loop extends WPBakeryShortCode {
    }
}				

//GRID STYLE LOOP
vc_map( array(
    "name" => __('Grid style posts block', 'rehub_framework'),
    "base" => "grid_loop_mod",
    "icon" => "icon-g-l-loop",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "checkbox",
            "heading" => __('Enable compact view?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "compact",    
        ),         
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Data source', 'rehub_framework'),
            "param_name" => "data_source",
            "value" => array(
                __('Category or tag', 'rehub_framework') => "cat",
                __('Manual select and order', 'rehub_framework') => "ids",
                __('Custom post type and taxonomy', 'rehub_framework') => "cpt",                    
            ), 
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category', 'rehub_framework' ),
            'param_name' => 'cat',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category exclude', 'rehub_framework' ),
            'param_name' => 'cat_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories to exclude', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),        
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags exclude', 'rehub_framework' ),
            'param_name' => 'tag_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags to exclude.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),                  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Post names', 'rehub_framework' ),
            'param_name' => 'ids',
            'settings' => array(
                'multiple' => true,
                'sortable' => true,
                'groups' => false,
            ),
            'description' => __( 'Or enter names of posts.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'ids' ),
            ),                          
        ),  
        array(
            'type' => 'dropdown',
            'heading' => __( 'Choose post type', 'rehub_framework' ),
            'param_name' => 'post_type',
            'value' => rehub_post_type_vc(),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),            
        ),   
        array(
            'type' => 'textfield',
            'heading' => __( 'Taxonomy name slug', 'rehub_framework' ),
            'param_name' => 'tax_name',
            'description' => __( 'Enter slug of your taxonomy. Example, taxonomy for posts - is category. Or leave blank', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ), 
        array(
            'type' => 'textfield',
            'heading' => __( 'Show posts by taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug',
            'description' => __( 'Enter slug of your taxonomy name (from field above) if you want to show only posts from certain taxonomy (from field above)', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),  
        array(
            'type' => 'textfield',
            'heading' => __( 'Exclude posts with taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug_exclude',
            'description' => __( 'Enter slug of your taxonomy name (from field above) to exclude posts', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),                            
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Set columns', 'rehub_framework'),
			"param_name" => "columns",
			"value" => array(
				__('2 columns', 'rehub_framework') => "2_col",
				__('3 columns', 'rehub_framework') => "3_col",
				__('4 columns', 'rehub_framework') => "4_col",					
			),
			'description' => __('4 columns is good only for full width row', 'rehub_framework'), 
		),		
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Number of comments', 'js_composer' ) => 'comment_count',				
				__( 'Meta value', 'js_composer' ) => 'meta_value',
				__( 'Meta value number', 'js_composer' ) => 'meta_value_num',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'description' => __( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Meta key', 'js_composer' ),
			'param_name' => 'meta_key',
			'description' => __( 'Input meta key for grid ordering.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'orderby',
				'value' => array( 'meta_value', 'meta_value_num' ),
			),
		),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats', 'rehub_framework'),
            "param_name" => "post_formats",
            "value" => $post_formats,
			'description' => __('Choose post formats to display or leave blank to display all', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),			
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
        ),	
        array(
            "type" => "textfield",
            "heading" => __('Offset', 'rehub_framework'),
            "param_name" => "offset",
            "value" => '',
			'description' => __('Number of products to offset', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),        	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '10',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),	
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Pagination type', 'rehub_framework'),
			"param_name" => "module_pagination",
			"value" => array(
				__('Simple pagination', 'rehub_framework') => "1",
				__('New item will be added by click', 'rehub_framework') => "2",
				__('Infinite scroll', 'rehub_framework') => "3",	
				__('No pagination', 'rehub_framework') => "no"
			),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
		)         									                  
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Grid_Loop_Mod extends WPBakeryShortCode {
    }
}	


//COMPACT GRID STYLE LOOP
vc_map( array(
    "name" => __('Deals grid block', 'rehub_framework'),
    "base" => "compactgrid_loop_mod",
    "icon" => "icon-cg-l-loop",
    "category" => __('Affiliate and offers', 'rehub_framework'),
    'description' => __('Compact grid', 'rehub_framework'), 
    "params" => array(        
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Data source', 'rehub_framework'),
            "param_name" => "data_source",
            "value" => array(
                __('Category or tag', 'rehub_framework') => "cat",
                __('Manual select and order', 'rehub_framework') => "ids",
                __('Custom post type and taxonomy', 'rehub_framework') => "cpt",                    
            ), 
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category', 'rehub_framework' ),
            'param_name' => 'cat',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category exclude', 'rehub_framework' ),
            'param_name' => 'cat_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories to exclude', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),        
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags exclude', 'rehub_framework' ),
            'param_name' => 'tag_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags to exclude.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),                  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Post names', 'rehub_framework' ),
            'param_name' => 'ids',
            'settings' => array(
                'multiple' => true,
                'sortable' => true,
                'groups' => false,
            ),
            'description' => __( 'Or enter names of posts.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'ids' ),
            ),                          
        ),  
        array(
            'type' => 'dropdown',
            'heading' => __( 'Choose post type', 'rehub_framework' ),
            'param_name' => 'post_type',
            'value' => rehub_post_type_vc(),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),            
        ),   
        array(
            'type' => 'textfield',
            'heading' => __( 'Taxonomy name slug', 'rehub_framework' ),
            'param_name' => 'tax_name',
            'description' => __( 'Enter slug of your taxonomy. Example, taxonomy for posts - is category. Or leave blank', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ), 
        array(
            'type' => 'textfield',
            'heading' => __( 'Show posts by taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug',
            'description' => __( 'Enter slug of your taxonomy name (from field above) if you want to show only posts from certain taxonomy (from field above)', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),  
        array(
            'type' => 'textfield',
            'heading' => __( 'Exclude posts with taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug_exclude',
            'description' => __( 'Enter slug of your taxonomy name (from field above) to exclude posts', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),                            
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Set columns', 'rehub_framework'),
            "param_name" => "columns",
            "value" => array(
                __('3 columns', 'rehub_framework') => "3_col",
                __('4 columns', 'rehub_framework') => "4_col",
                __('5 columns', 'rehub_framework') => "5_col", 
                __('6 columns', 'rehub_framework') => "6_col",                                 
            ),
            'description' => __('4 columns is good only for full width row', 'rehub_framework'), 
        ),
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Filter by coupon date', 'rehub_framework'),
            "param_name" => "show_coupons_only",
            "value" => array(
                __('Show all', 'rehub_framework') => "all",
                __('Only with actual coupons', 'rehub_framework') => "1",                  
                __('With actual coupons and without coupons', 'rehub_framework') => "2", 
                __('Only with expired coupons', 'rehub_framework') => "3",                               
            ), 
        ),               
        array(
            'type' => 'dropdown',
            'heading' => __( 'Order by', 'js_composer' ),
            'param_name' => 'orderby',
            'value' => array(
                __( 'Date', 'js_composer' ) => 'date',
                __( 'Order by post ID', 'js_composer' ) => 'ID',
                __( 'Title', 'js_composer' ) => 'title',
                __( 'Last modified date', 'js_composer' ) => 'modified',
                __( 'Number of comments', 'js_composer' ) => 'comment_count',               
                __( 'Meta value', 'js_composer' ) => 'meta_value',
                __( 'Meta value number', 'js_composer' ) => 'meta_value_num',
                __( 'Random order', 'js_composer' ) => 'rand',
            ),
            'description' => __( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'js_composer' ),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids'),
            ),
        ),
        array(
            'type' => 'dropdown',
            'heading' => __( 'Sorting', 'js_composer' ),
            'param_name' => 'order',
            'group' => __( 'Data settings', 'js_composer' ),
            'value' => array(
                __( 'Descending', 'js_composer' ) => 'DESC',
                __( 'Ascending', 'js_composer' ) => 'ASC',
            ),
            'description' => __( 'Select sorting order.', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),
        ),
        array(
            'type' => 'textfield',
            'heading' => __( 'Meta key', 'js_composer' ),
            'param_name' => 'meta_key',
            'description' => __( 'Input meta key for grid ordering.', 'js_composer' ),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'orderby',
                'value' => array( 'meta_value', 'meta_value_num' ),
            ),
        ),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats', 'rehub_framework'),
            "param_name" => "post_formats",
            "value" => $post_formats,
            'description' => __('Choose post formats to display or leave blank to display all', 'rehub_framework'),
            'group' => __( 'Data settings', 'js_composer' ),            
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),  
        array(
            "type" => "textfield",
            "heading" => __('Offset', 'rehub_framework'),
            "param_name" => "offset",
            "value" => '',
            'description' => __('Number of products to offset', 'rehub_framework'),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),          
        ),          
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '10',
            'description' => __('Number of products to display', 'rehub_framework'),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),          
        ),  
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Pagination type', 'rehub_framework'),
            "param_name" => "enable_pagination",
            "value" => array(
                __('No pagination', 'rehub_framework') => "0",
                __('Simple pagination', 'rehub_framework') => "1",
                __('Infinite scroll', 'rehub_framework') => "2",    
            ),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),          
        ),                                                            
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Compactgrid_Loop_Mod extends WPBakeryShortCode {
    }
}   

//POST CAROUSEL BLOCK
vc_map( array(
    "name" => __('Posts carousel block', 'rehub_framework'),
    "base" => "post_carousel_mod",
    "icon" => "icon-p-c-mod",
    "category" => __('Content', 'js_composer'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "heading" => __('Choose Category', 'rehub_framework'),
            "param_name" => "module_cat",
            "value" => rehub_category_chooser('noall'),
			'description' => __('Choose the category that you\'d like to include to block', 'rehub_framework'),
        ),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats for carousel', 'rehub_framework'),
            "param_name" => "module_formats",
            "value" => $post_formats,
			'description' => __('Choose post formats to display in carousel or leave blank to display all', 'rehub_framework'),
        ),         
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "module_fetch",
            "value" => '6',
			'description' => __('How much posts you\'d like to display in carousel?', 'rehub_framework'),
        ),                 

    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Post_Carousel_Mod extends WPBakeryShortCode {
    }
}

//POST SLIDER BLOCK
vc_map( array(
    "name" => __('Full width Posts slider', 'rehub_framework'),
    "base" => "post_slider_mod",
    "icon" => "icon-p-s-mod",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "heading" => __('Category', 'rehub_framework'),
            "param_name" => "module_cat",
            "admin_label" => true,
            "value" => rehub_category_chooser(),
			'description' => __('Choose the category that you\'d like to include to block', 'rehub_framework'),
        ),
        array(
            "type" => "textfield",
            "heading" => __('Slug of tag', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "module_tag",
			'description' => __('Set tag of posts or leave blank to use only category', 'rehub_framework'),
        ), 
        array(
            'type' => 'textfield',
            'heading' => __( 'Symbols in exerpt', 'js_composer' ),
            'param_name' => 'exerpt_count',
            'value' => '',
            'description' => __('Leave blank to disable exerpt.', 'rehub_framework'),
        ),
        array(
            'type' => 'textfield',
            'heading' => __( 'Height of slider', 'js_composer' ),
            'param_name' => 'height',
            'value' => '',
            'description' => __('Default, 490px', 'rehub_framework'),
        ),                       
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "module_fetch",
            "value" => '4',
			'description' => __('How much posts you\'d like to display in slider?', 'rehub_framework'),
        ),                 

    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Post_Slider_Mod extends WPBakeryShortCode {
    }
}

//TITLE FOR CUSTOM BLOCK
vc_map( array(
    "name" => __('Title for custom block', 'rehub_framework'),
    "base" => "title_mod",
    "icon" => "icon-title-mod",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "textfield",
            "heading" => __('Title', 'rehub_framework'),
            "param_name" => "title_name",
            "admin_label" => true,
        ),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Title position', 'rehub_framework'),
			"param_name" => "title_position",
			"value" => array(
				__('above line', 'rehub_framework') => "top_title",
				__('left position inside line', 'rehub_framework') => "left_title",	
				__('center position inside line', 'rehub_framework') => "center_title"
			)
		),
        array(
            "type" => "vc_link",
            "heading" => __('Custom URL:', 'rehub_framework'),
            "param_name" => "title_url",
			'description' => __('Set url near title or leave blank', 'rehub_framework'),
			"admin_label" => true,
        ),        		                        
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Title_Mod extends WPBakeryShortCode {
    }
}

//Column grid
vc_map( array(
    "name" => __('Posts grid in columns', 'rehub_framework'),
    "base" => "columngrid_loop",
    "icon" => "icon-columngrid",
    'description' => __('Columned grid', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Data source', 'rehub_framework'),
            "param_name" => "data_source",
            "value" => array(
                __('Category or tag', 'rehub_framework') => "cat",
                __('Manual select and order', 'rehub_framework') => "ids",
                __('Custom post type and taxonomy', 'rehub_framework') => "cpt",                    
            ), 
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category', 'rehub_framework' ),
            'param_name' => 'cat',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Category exclude', 'rehub_framework' ),
            'param_name' => 'cat_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_catchooser(),
            ),
            'description' => __( 'Enter names of categories to exclude', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),        
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags. Or leave blank to show all', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tags exclude', 'rehub_framework' ),
            'param_name' => 'tag_exclude',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_tagchooser(),
            ),
            'description' => __( 'Enter names of tags to exclude.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),                  
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Post names', 'rehub_framework' ),
            'param_name' => 'ids',
            'settings' => array(
                'multiple' => true,
                'sortable' => true,
                'groups' => false,
            ),
            'description' => __( 'Or enter names of posts.', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'ids' ),
            ),                          
        ),  
        array(
            'type' => 'dropdown',
            'heading' => __( 'Choose post type', 'rehub_framework' ),
            'param_name' => 'post_type',
            'value' => rehub_post_type_vc(),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),            
        ),   
        array(
            'type' => 'textfield',
            'heading' => __( 'Taxonomy name slug', 'rehub_framework' ),
            'param_name' => 'tax_name',
            'description' => __( 'Enter slug of your taxonomy. Example, taxonomy for posts - is category. Or leave blank', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ), 
        array(
            'type' => 'textfield',
            'heading' => __( 'Show posts by taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug',
            'description' => __( 'Enter slug of your taxonomy name (from field above) if you want to show only posts from certain taxonomy (from field above)', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),  
        array(
            'type' => 'textfield',
            'heading' => __( 'Exclude posts with taxonomy slug', 'rehub_framework' ),
            'param_name' => 'tax_slug_exclude',
            'description' => __( 'Enter slug of your taxonomy name (from field above) to exclude posts', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cpt'),
            ),
        ),                            
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Set columns', 'rehub_framework'),
            "param_name" => "columns",
            "value" => array(
                __('3 columns', 'rehub_framework') => "3_col",
                __('4 columns', 'rehub_framework') => "4_col",                  
            ),
            'description' => __('4 columns is good only for full width row', 'rehub_framework'), 
        ),        
        array(
            'type' => 'dropdown',
            'heading' => __( 'Order by', 'js_composer' ),
            'param_name' => 'orderby',
            'value' => array(
                __( 'Date', 'js_composer' ) => 'date',
                __( 'Order by post ID', 'js_composer' ) => 'ID',
                __( 'Title', 'js_composer' ) => 'title',
                __( 'Last modified date', 'js_composer' ) => 'modified',
                __( 'Number of comments', 'js_composer' ) => 'comment_count',               
                __( 'Meta value', 'js_composer' ) => 'meta_value',
                __( 'Meta value number', 'js_composer' ) => 'meta_value_num',
                __( 'Random order', 'js_composer' ) => 'rand',
            ),
            'description' => __( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'js_composer' ),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids'),
            ),
        ),
        array(
            'type' => 'dropdown',
            'heading' => __( 'Sorting', 'js_composer' ),
            'param_name' => 'order',
            'group' => __( 'Data settings', 'js_composer' ),
            'value' => array(
                __( 'Descending', 'js_composer' ) => 'DESC',
                __( 'Ascending', 'js_composer' ) => 'ASC',
            ),
            'description' => __( 'Select sorting order.', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),
        ),
        array(
            'type' => 'textfield',
            'heading' => __( 'Meta key', 'js_composer' ),
            'param_name' => 'meta_key',
            'description' => __( 'Input meta key for grid ordering.', 'js_composer' ),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'orderby',
                'value' => array( 'meta_value', 'meta_value_num' ),
            ),
        ),
        array(
            "type" => "dropdown",
            "heading" => __('Choose post formats', 'rehub_framework'),
            "param_name" => "post_formats",
            "value" => $post_formats,
            'description' => __('Choose post formats to display or leave blank to display all', 'rehub_framework'),
            'group' => __( 'Data settings', 'js_composer' ),            
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'cat' ),
            ),          
        ),  
        array(
            "type" => "textfield",
            "heading" => __('Offset', 'rehub_framework'),
            "param_name" => "offset",
            "value" => '',
            'description' => __('Number of products to offset', 'rehub_framework'),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),          
        ),          
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '10',
            'description' => __('Number of products to display', 'rehub_framework'),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),          
        ),
        array(
            'type' => 'textfield',
            'heading' => __( 'Symbols in exerpt', 'js_composer' ),
            'param_name' => 'exerpt_count',
            'value' => '120',
            'description' => __('Set 0 to disable exerpt', 'rehub_framework'),
        ),        
        array(
            "type" => "checkbox",
            "class" => "",
            "heading" => __('Enable button?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "enable_btn",         
        ),                              
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Pagination type', 'rehub_framework'),
            "param_name" => "enable_pagination",
            "value" => array(
                __('No pagination', 'rehub_framework') => "0",
                __('Simple pagination', 'rehub_framework') => "1",
                __('Infinite scroll', 'rehub_framework') => "2",    
            ),
            'group' => __( 'Data settings', 'js_composer' ),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'ids' ),
            ),          
        ),                                
    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Columngrid_Loop extends WPBakeryShortCode {
    }
}

//CUSTOM TEXT BLOCK
vc_add_param("vc_column_text", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => __("Add border to block?", "rehub_framework"),
	"value" => array(__("Yes", "rehub_framework") => true ),
	"param_name" => "bordered",
));


//OFFER BOX
vc_map( array(
    "name" => __('Offer Box', 'rehub_framework'),
    "base" => "wpsm_offerbox",
    "icon" => "icon-offer-box",
    "category" => __('Affiliate and offers', 'rehub_framework'),
    'description' => __('Offer box', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "textfield",
            "heading" => __('Offer sale price', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "price",
        ),	
        array(
            "type" => "textfield",
            "heading" => __('Offer old price', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "price_old",
        ),        
        array(
            "type" => "textfield",
            "heading" => __('Offer url', 'rehub_framework'),
            "param_name" => "button_link",
        ), 
        array(
            "type" => "textfield",
            "heading" => __('Button text', 'rehub_framework'),
            "param_name" => "button_text",
        ), 
        array(
            "type" => "textfield",
            "heading" => __('Name of product', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "title",
        ),
        array(
            "type" => "textfield",
            "heading" => __('Short description of product', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "description",
        ),   
		array(
			'type' => 'attach_image',
			'heading' => __('Upload thumbnail', 'rehub_framework'),
			'param_name' => 'image_id',
			'value' => '',
		), 
        array(
            "type" => "textfield",
            "heading" => __('Set coupon code', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "offer_coupon",
        ),
        array(
            "type" => "checkbox",
            "class" => "",
            "heading" => __('Mask coupon code?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "offer_coupon_mask",
        ),
        array(
            "type" => "textfield",
            "heading" => __('Set text on mask', 'rehub_framework'),
            "admin_label" => false,
            "param_name" => "offer_coupon_mask_text",
            'dependency' => array(
                'element' => 'offer_coupon_mask',
                'not_empty' => true,
            ),            
        ), 
        array(
            "type" => "textfield",
            "heading" => __('Coupon End Date', 'rehub_framework'),
            "description" => __( 'Format date-month-year. Example, 20-12-2015', 'js_composer' ),
            "admin_label" => false,
            "param_name" => "offer_coupon_date",
        ), 
        array(
            'type' => 'attach_image',
            'heading' => __('Brand logo', 'rehub_framework'),
            'param_name' => 'logo_image_id',
            'value' => '',
        ),                                                                                  	                        
    )
) );



//AFFILIATE LIST

if(function_exists('thirstyInit')) {

//Thirstylink Category Chooser
$thirstycats = get_terms( 'thirstylink-category'  );
$thirstycatchooser = array();
foreach ( $thirstycats as $t ) {
	$thirstycatchooser[] = array(
		'label' => $t->name,
		'value' => $t->term_id,
	);
}

vc_map( array(
    "name" => __('List of offers', 'rehub_framework'),
    "base" => "wpsm_offerlist",
    "icon" => "icon-afflist",
    "category" => __('Affiliate and offers', 'rehub_framework'),
    'description' => __('Works only with ThirstyAffiliate plugin', 'rehub_framework'), 
    "params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Data source', 'rehub_framework'),
			"param_name" => "data_source",
			"value" => array(
				__('Category', 'rehub_framework') => "cat",
				__('Manual select and order', 'rehub_framework') => "ids",					
			), 
		),    	
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Category', 'rehub_framework' ),
			'param_name' => 'cat',
			'settings' => array(
				'multiple' => true,
				'min_length' => 2,
				'display_inline' => true,
				'values' => $thirstycatchooser,
			),
			'description' => __( 'Enter names of thirstylink categories', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
		), 
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Offer names', 'rehub_framework' ),
			'param_name' => 'ids',
			'settings' => array(
				'multiple' => true,
				'sortable' => true,
				'groups' => false,
			),
			'description' => __( 'Or enter names of offers.', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'ids' ),
			),							
		), 
		// Data settings
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Meta value', 'js_composer' ) => 'meta_value',
				__( 'Meta value number', 'js_composer' ) => 'meta_value_num',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'description' => __( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Meta key', 'js_composer' ),
			'param_name' => 'meta_key',
			'description' => __( 'Input meta key for grid ordering.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'orderby',
				'value' => array( 'meta_value', 'meta_value_num' ),
			),
		),	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '9',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),							    	   
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __('Enable pagination?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "enable_pagination",
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
		),	
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __('Disable link cloaking?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "no_cloaking",
		),   		                            
    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Wpsm_Offerlist extends WPBakeryShortCode {
    }
}


//AFFILIATE GRID
vc_map( array(
    "name" => __('Grid of offers', 'rehub_framework'),
    "base" => "wpsm_affgrid",
    "icon" => "icon-affgrid",
    "category" => __('Affiliate and offers', 'rehub_framework'),
    'description' => __('Works only with ThirstyAffiliate plugin', 'rehub_framework'), 
    "params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Data source', 'rehub_framework'),
			"param_name" => "data_source",
			"value" => array(
				__('Category', 'rehub_framework') => "cat",
				__('Manual select and order', 'rehub_framework') => "ids",					
			), 
		),    	
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Category', 'rehub_framework' ),
			'param_name' => 'cat',
			'settings' => array(
				'multiple' => true,
				'min_length' => 2,
				'display_inline' => true,
				'values' => $thirstycatchooser,
			),
			'description' => __( 'Enter names of thirstylink categories', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
		), 
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Offer names', 'rehub_framework' ),
			'param_name' => 'ids',
			'settings' => array(
				'multiple' => true,
				'sortable' => true,
				'groups' => false,
			),
			'description' => __( 'Or enter names of offers.', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'ids' ),
			),							
		), 
		// Data settings
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Meta value', 'js_composer' ) => 'meta_value',
				__( 'Meta value number', 'js_composer' ) => 'meta_value_num',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'description' => __( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Meta key', 'js_composer' ),
			'param_name' => 'meta_key',
			'description' => __( 'Input meta key for grid ordering.', 'js_composer' ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'orderby',
				'value' => array( 'meta_value', 'meta_value_num' ),
			),
		),	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '9',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),							    	   
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __('Enable pagination?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "enable_pagination",
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Set columns', 'rehub_framework'),
			"param_name" => "columns",
			"value" => array(
				__('3 columns', 'rehub_framework') => "3_col",
				__('4 columns', 'rehub_framework') => "4_col",					
			),
			'description' => __('4 columns is good only for full width row', 'rehub_framework'), 
		), 	
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __('Disable link cloaking?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "no_cloaking",
		),   				                                           
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Wpsm_Affgrid extends WPBakeryShortCode {
    }
}

add_filter( 'vc_autocomplete_wpsm_affgrid_ids_callback',
	'rehub_thirsty_search', 10, 1 ); 
add_filter( 'vc_autocomplete_wpsm_affgrid_ids_render',
	'rehub_thirsty_render', 10, 1 ); 
add_filter( 'vc_autocomplete_wpsm_offerlist_ids_callback',
	'rehub_thirsty_search', 10, 1 ); 
add_filter( 'vc_autocomplete_wpsm_offerlist_ids_render',
	'rehub_thirsty_render', 10, 1 );

function rehub_thirsty_search( $search_string ) {
	$query = $search_string;
	$data = array();
	$args = array( 's' => $query, 'post_type' => 'thirstylink' );
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = - 1;
	if ( strlen( $args['s'] ) == 0 ) {
		unset( $args['s'] );
	}
	add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		$data[] = array(
			'value' => $post->ID,
			'label' => $post->post_title,
		);
	}
	return $data;
}

function rehub_thirsty_render( $value ) {
	$post = get_post( $value['value'] );

	return is_null( $post ) ? false : array(
		'label' => $post->post_title,
		'value' => $post->ID,
	);
}


}

//PROS BLOCK
vc_map( array(
    "name" => __('Pros block', 'rehub_framework'),
    "base" => "wpsm_pros",
    "icon" => "icon-pros",
    "category" => __('Affiliate and offers', 'rehub_framework'),
    'description' => __('List of positives', 'rehub_framework'), 
    "params" => array(	
		array(
            "type" => "textfield",
            "heading" => __('Pros title', 'rehub_framework'),
            "param_name" => "title",
            "value" => __('PROS:', 'rehub_framework'),           
        ),
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			'param_name' => 'content',
			"admin_label" => true,
			'value' => __( '<ul><li>Positive 1</li><li>Positive 2</li><li>Positive 3</li><li>Positive 4</li></ul>', 'rehub_framework' ),
		),                                  	                        
    )
) );

//CONS BLOCK
vc_map( array(
    "name" => __('Cons block', 'rehub_framework'),
    "base" => "wpsm_cons",
    "icon" => "icon-cons",
    "category" => __('Affiliate and offers', 'rehub_framework'),
    'description' => __('List of negatives', 'rehub_framework'), 
    "params" => array(	
		array(
            "type" => "textfield",
            "heading" => __('Cons title', 'rehub_framework'),
            "param_name" => "title",
            "value" => __('CONS:', 'rehub_framework'),           
        ),
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			'param_name' => 'content',
			"admin_label" => true,
			'value' => __( '<ul><li>Negative 1</li><li>Negative 2</li><li>Negative 3</li><li>Negative 4</li></ul>', 'rehub_framework' ),
		),                                  	                        
    )
) );


//TEXT SEPARATOR
vc_add_param("vc_text_separator", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => __("Make title uppercase?", "rehub_framework"),
	"value" => array(__("Yes", "rehub_framework") => true ),
	"param_name" => "uppercase",
));
vc_add_param("vc_text_separator", array(
	"type" => "textfield",
	"class" => "",
	"heading" => __("Set font size in px (by default, 21px)", "rehub_framework"),
	"param_name" => "font_size",
));


//LINE SEPARATOR
vc_remove_param("vc_separator", "el_width");
vc_remove_param("vc_separator", "el_class");
vc_remove_param("vc_separator", "align");
vc_add_param("vc_separator", array(
	"type" => "dropdown",
	"heading" => __('Align', 'rehub_framework'),
	"param_name" => "sep_align",
	"value" => array(
		__('Left', 'rehub_framework') => "left",
		__('Right', 'rehub_framework') => "right",	
		__('Center', 'rehub_framework') => "center",
	)
));
vc_add_param("vc_separator", array(
	"type" => "textfield",
	"class" => "",
	"heading" => __("Set width in px or %", "rehub_framework"),
	"param_name" => "sep_width",
	"value" => "60px",
));
vc_add_param("vc_separator", array(
	"type" => "textfield",
	"class" => "",
	"heading" => __("Margin from top in px", "rehub_framework"),
	"param_name" => "m_top",
	"value" => "10px",
));
vc_add_param("vc_separator", array(
	"type" => "textfield",
	"class" => "",
	"heading" => __("Margin from bottom in px", "rehub_framework"),
	"param_name" => "m_bottom",
	"value" => "10px",
));



//IMAGE SLIDER
add_action( 'init', 're_remove_slider_type' ); 
function re_remove_slider_type() {
	$param = WPBMap::getParam( 'vc_gallery', 'type' );
	unset($param['value'][__( 'Flex slider fade', 'js_composer' )]);
	vc_update_shortcode_param( 'vc_gallery', $param );
}
vc_remove_param("vc_gallery", "interval");


if(class_exists( 'WooCommerce' )) {//WOOBLOCKS

//Woo Category Chooser
if( !function_exists('rehub_woocatchooser') ) {
    function rehub_woocatchooser() {
        $woocats = get_terms( 'product_cat'  );
        $woocatchooser = array();
        foreach ( $woocats as $t ) {
            $woocatchooser[] = array(
                'label' => $t->name,
                'value' => $t->term_id,
            );
        }
        return $woocatchooser;
    }
}

//Woo Tag Chooser
if( !function_exists('rehub_wootagchooser') ) {
    function rehub_wootagchooser() {
        $wootags = get_terms( 'product_tag'  );
        $wootagchooser = array();
        foreach ( $wootags as $t ) {
            $wootagchooser[] = array(
                'label' => $t->name,
                'value' => $t->term_id,
            );
        }
        return $wootagchooser;
    }
}

//WOO chooser

add_filter( 'vc_autocomplete_wpsm_woobox_id_callback',
	'rehub_woopost_search', 10, 1 );
add_filter( 'vc_autocomplete_wpsm_woobox_id_render',
	'rehub_woopost_render', 10, 1 );
add_filter( 'vc_autocomplete_wpsm_woolist_ids_callback',
	'rehub_woopost_search', 10, 1 );
add_filter( 'vc_autocomplete_wpsm_woolist_ids_render',
	'rehub_woopost_render', 10, 1 );
add_filter( 'vc_autocomplete_wpsm_woogrid_ids_callback',
	'rehub_woopost_search', 10, 1 );
add_filter( 'vc_autocomplete_wpsm_woogrid_ids_render',
	'rehub_woopost_render', 10, 1 );
add_filter( 'vc_autocomplete_wpsm_woocolumns_ids_callback',
	'rehub_woopost_search', 10, 1 );
add_filter( 'vc_autocomplete_wpsm_woocolumns_ids_render',
	'rehub_woopost_render', 10, 1 );

function rehub_woopost_search( $search_string ) {
	$query = $search_string;
	$data = array();
	$args = array( 's' => $query, 'post_type' => 'product' );
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = - 1;
	if ( strlen( $args['s'] ) == 0 ) {
		unset( $args['s'] );
	}
	add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		$data[] = array(
			'value' => $post->ID,
			'label' => $post->post_title,
		);
	}
	return $data;
}

function rehub_woopost_render( $value ) {
	$post = get_post( $value['value'] );

	return is_null( $post ) ? false : array(
		'label' => $post->post_title,
		'value' => $post->ID,
	);
}


//WOO CAROUSEL
vc_map( array(
    "name" => __('Woo commerce product carousel', 'rehub_framework'),
    "base" => "woo_mod",
    "icon" => "icon-woo-mod",
    "category" => __('For row with sidebar', 'rehub_framework'),
    'description' => __('For row with sidebar', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "module_fetch",
            "value" => '4',
			'description' => __('Number of products to display', 'rehub_framework'),
        ),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Type of products to display in block', 'rehub_framework'),
			"param_name" => "module_type",
			"value" => array(
				__('Latest products', 'rehub_framework') => "latest",
				__('Featured products', 'rehub_framework') => "featured",	
				__('Best sellers', 'rehub_framework') => "best"
			)
		),
        array(
            "type" => "textfield",
            "heading" => __('Set category', 'rehub_framework'),
            "param_name" => "product_cat",
			'description' => __('Set slug of product category or leave blank', 'rehub_framework'),
        ),		                        
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Woo_Mod extends WPBakeryShortCode {
    }
}

//WOO OFFER BOX
vc_map( array(
    "name" => __('Woo Box', 'rehub_framework'),
    "base" => "wpsm_woobox",
    "icon" => "icon-woo-offer-box",
    "category" => __('Woocommerce', 'rehub_framework'),
    'description' => __('Woocommerce product box', 'rehub_framework'), 
    "params" => array(
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Set Product name', 'rehub_framework' ),
			'param_name' => 'id',
			'settings' => array(
				'multiple' => false,
				'sortable' => false,
				'groups' => false,
			),
			'description' => __( 'Type name of product', 'rehub_framework' ),							
		),	                                	                        
    )
) );



//WOO LIST
vc_map( array(
    "name" => __('List of woo products', 'rehub_framework'),
    "base" => "wpsm_woolist",
    "icon" => "icon-woolist",
    "category" => __('Woocommerce', 'rehub_framework'),
    'description' => __('Works only with Woocommerce', 'rehub_framework'), 
    "params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Data source', 'rehub_framework'),
			"param_name" => "data_source",
			"value" => array(
				__('Category', 'rehub_framework') => "cat",
                __('Tag', 'rehub_framework') => "tag",
				__('Manual select and order', 'rehub_framework') => "ids",	
				__('Type of products', 'rehub_framework') => "type",				
			), 
		),    	
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Category', 'rehub_framework' ),
			'param_name' => 'cat',
			'settings' => array(
				'multiple' => true,
				'min_length' => 2,
				'display_inline' => true,
				'values' => rehub_woocatchooser(),
			),
			'description' => __( 'Enter names of categories', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
		),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tag', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_wootagchooser(),
            ),
            'description' => __( 'Enter names of tags', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'tag' ),
            ),          
        ),         
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Product names', 'rehub_framework' ),
			'param_name' => 'ids',
			'settings' => array(
				'multiple' => true,
				'sortable' => true,
				'groups' => false,
			),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'ids' ),
			),							
		), 
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Type of product', 'rehub_framework'),
			"param_name" => "type",
			"value" => array(
				__('Recent products', 'rehub_framework') => "recent",
				__('Featured products', 'rehub_framework') => "featured",	
				__('Sale products', 'rehub_framework') => "sale",
				__('Best selling products', 'rehub_framework') => "best_sale"								
			), 
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'type' ),
			),			
		),
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Filter by coupon date', 'rehub_framework'),
            "param_name" => "show_coupons_only",
            "value" => array(
                __('Show all', 'rehub_framework') => "all",
                __('Only with actual coupons', 'rehub_framework') => "1",                  
                __('With actual coupons and without coupons', 'rehub_framework') => "2", 
                __('Only with expired coupons', 'rehub_framework') => "3",                               
            ), 
        ),           		
		// Data settings
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Number of comments', 'js_composer' ) => 'comment_count',
				__( 'Menu order/Page Order', 'js_composer' ) => 'menu_order',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '9',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),							    	   	                               
    )
) );


//WOO GRID
vc_map( array(
    "name" => __('Grid of woocommerce products', 'rehub_framework'),
    "base" => "wpsm_woogrid",
    "icon" => "icon-woogrid",
    "category" => __('Woocommerce', 'rehub_framework'),
    'description' => __('Works only with Woocommerce', 'rehub_framework'), 
    "params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Data source', 'rehub_framework'),
			"param_name" => "data_source",
			"value" => array(
				__('Category', 'rehub_framework') => "cat",
                __('Tag', 'rehub_framework') => "tag",                
				__('Manual select and order', 'rehub_framework') => "ids",	
				__('Type of products', 'rehub_framework') => "type",				
			), 
		),    	
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Category', 'rehub_framework' ),
			'param_name' => 'cat',
			'settings' => array(
				'multiple' => true,
				'min_length' => 2,
				'display_inline' => true,
				'values' => rehub_woocatchooser(),
			),
			'description' => __( 'Enter names of categories', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
		),
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tag', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_wootagchooser(),
            ),
            'description' => __( 'Enter names of tags', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'tag' ),
            ),          
        ),         
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Product names', 'rehub_framework' ),
			'param_name' => 'ids',
			'settings' => array(
				'multiple' => true,
				'sortable' => true,
				'groups' => false,
			),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'ids' ),
			),							
		), 
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Type of product', 'rehub_framework'),
			"param_name" => "type",
			"value" => array(
				__('Recent products', 'rehub_framework') => "recent",
				__('Featured products', 'rehub_framework') => "featured",	
				__('Sale products', 'rehub_framework') => "sale",
				__('Best selling products', 'rehub_framework') => "best_sale"								
			), 
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'type' ),
			),			
		), 
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Set columns', 'rehub_framework'),
			"param_name" => "columns",
			"value" => array(
				__('3 columns', 'rehub_framework') => "3_col",
				__('4 columns', 'rehub_framework') => "4_col",
                __('5 columns', 'rehub_framework') => "5_col",
                __('6 columns', 'rehub_framework') => "6_col",					
			), 
		),	
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Show link from title and image on', 'rehub_framework'),
            "param_name" => "woolinktype",
            "value" => array(
                __('Affiliate link', 'rehub_framework') => "aff",
                __('Product page', 'rehub_framework') => "product",                              
            ), 
        ),   
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Filter by coupon date', 'rehub_framework'),
            "param_name" => "show_coupons_only",
            "value" => array(
                __('Show all', 'rehub_framework') => "all",
                __('Only with actual coupons', 'rehub_framework') => "1",                  
                __('With actual coupons and without coupons', 'rehub_framework') => "2", 
                __('Only with expired coupons', 'rehub_framework') => "3",                               
            ), 
        ),              
        array(
            "type" => "checkbox",
            "heading" => __('Enable compact view?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "compact",    
        ),               			
		// Data settings
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Number of comments', 'js_composer' ) => 'comment_count',
				__( 'Menu order/Page Order', 'js_composer' ) => 'menu_order',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '9',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),							    	   
		array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Pagination type', 'rehub_framework'),
            "param_name" => "enable_pagination",
            "value" => array(
                __('No pagination', 'rehub_framework') => "0",
                __('Simple pagination', 'rehub_framework') => "1",
                __('Infinite scroll', 'rehub_framework') => "2",    
            ),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
		),	 		  			                                           
    )
) );

//WOO COLUMNS
vc_map( array(
    "name" => __('Columns of woocommerce products', 'rehub_framework'),
    "base" => "wpsm_woocolumns",
    "icon" => "icon-woocolumns",
    "category" => __('Woocommerce', 'rehub_framework'),
    'description' => __('Works only with Woocommerce', 'rehub_framework'), 
    "params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Data source', 'rehub_framework'),
			"param_name" => "data_source",
			"value" => array(
				__('Category', 'rehub_framework') => "cat",
				__('Manual select and order', 'rehub_framework') => "ids",	
				__('Type of products', 'rehub_framework') => "type",
                __('Tag', 'rehub_framework') => "tag",                				
			), 
		),    	
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Category', 'rehub_framework' ),
			'param_name' => 'cat',
			'settings' => array(
				'multiple' => true,
				'min_length' => 2,
				'display_inline' => true,
				'values' => rehub_woocatchooser(),
			),
			'description' => __( 'Enter names of categories', 'rehub_framework' ),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'cat' ),
			),			
		), 
        array(
            'type' => 'autocomplete',
            'heading' => __( 'Tag', 'rehub_framework' ),
            'param_name' => 'tag',
            'settings' => array(
                'multiple' => true,
                'min_length' => 2,
                'display_inline' => true,
                'values' => rehub_wootagchooser(),
            ),
            'description' => __( 'Enter names of tags', 'rehub_framework' ),
            'dependency' => array(
                'element' => 'data_source',
                'value' => array( 'tag' ),
            ),          
        ),        
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Product names', 'rehub_framework' ),
			'param_name' => 'ids',
			'settings' => array(
				'multiple' => true,
				'sortable' => true,
				'groups' => false,
			),
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'ids' ),
			),							
		), 
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Type of product', 'rehub_framework'),
			"param_name" => "type",
			"value" => array(
				__('Recent products', 'rehub_framework') => "recent",
				__('Featured products', 'rehub_framework') => "featured",	
				__('Sale products', 'rehub_framework') => "sale",
				__('Best selling products', 'rehub_framework') => "best_sale"								
			), 
			'dependency' => array(
				'element' => 'data_source',
				'value' => array( 'type' ),
			),			
		), 
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Set columns', 'rehub_framework'),
			"param_name" => "columns",
			"value" => array(
				__('3 columns', 'rehub_framework') => "3_col",
				__('4 columns', 'rehub_framework') => "4_col",					
			),
			'description' => __('4 columns is good only for full width row', 'rehub_framework'), 
		),	
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Filter by coupon date', 'rehub_framework'),
            "param_name" => "show_coupons_only",
            "value" => array(
                __('Show all', 'rehub_framework') => "all",
                __('Only with actual coupons', 'rehub_framework') => "1",                  
                __('With actual coupons and without coupons', 'rehub_framework') => "2", 
                __('Only with expired coupons', 'rehub_framework') => "3",                               
            ), 
        ),          			
		// Data settings
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Date', 'js_composer' ) => 'date',
				__( 'Order by post ID', 'js_composer' ) => 'ID',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Last modified date', 'js_composer' ) => 'modified',
				__( 'Number of comments', 'js_composer' ) => 'comment_count',
				__( 'Menu order/Page Order', 'js_composer' ) => 'menu_order',
				__( 'Random order', 'js_composer' ) => 'rand',
			),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids'),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sorting', 'js_composer' ),
			'param_name' => 'order',
			'group' => __( 'Data settings', 'js_composer' ),
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => __( 'Select sorting order.', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),
		),	
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '9',
			'description' => __('Number of products to display', 'rehub_framework'),
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
        ),							    	   
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __('Enable pagination?', 'rehub_framework'),
			"value" => array(__("Yes", "rehub_framework") => true ),
			"param_name" => "enable_pagination",
			'group' => __( 'Data settings', 'js_composer' ),
			'dependency' => array(
				'element' => 'data_source',
				'value_not_equal_to' => array( 'ids' ),
			),			
		),	   			                                           
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Wpsm_Woocolumns extends WPBakeryShortCode {
    }
}

}

//SEARCHBOX
vc_map( array(
    "name" => __('Search box', 'rehub_framework'),
    "base" => "wpsm_searchbox",
    "icon" => "icon-searchbox",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Searchbox', 'rehub_framework'), 
    "params" => array(	
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Where to search', 'rehub_framework'),
			"param_name" => "by",
			"value" => array(
				__('Posts', 'rehub_framework') => "post",
				__('Pages', 'rehub_framework') => "page",
				__('Products', 'rehub_framework') => "product",					
			)
		), 
		array(
            "type" => "textfield",
            "heading" => __('Placeholder', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "placeholder",          
        ), 
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Color of button', 'rehub_framework'),
			"param_name" => "color",
			"value" => array(
				__('orange', 'rehub_framework') => "orange",
				__('gold', 'rehub_framework') => "gold",
				__('black', 'rehub_framework') => "black",	
				__('blue', 'rehub_framework') => "blue",
				__('red', 'rehub_framework') => "red",
				__('green', 'rehub_framework') => "green",	
				__('rosy', 'rehub_framework') => "rosy",
				__('brown', 'rehub_framework') => "brown",
				__('pink', 'rehub_framework') => "pink",
				__('purple', 'rehub_framework') => "purple",
				__('teal', 'rehub_framework') => "teal",				
			)
		),                                               	                        
    )
) );

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Wpsm_Searchbox extends WPBakeryShortCode {
    }
}


//TESTIMONIAL
vc_map( array(
    "name" => __('Testimonial', 'rehub_framework'),
    "base" => "wpsm_testimonial",
    "icon" => "icon-testimonial",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Testimonial box', 'rehub_framework'), 
    "params" => array(	
		array(
            "type" => "textfield",
            "heading" => __('Author', 'rehub_framework'),
            "param_name" => "by",
            'description' => __('Add author or leave blank.', 'rehub_framework'),            
        ),
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			"admin_label" => true,
			'param_name' => 'content',
			'value' => __( 'Content goes here, click edit button to change this text.', 'rehub_framework' ),
		),                                  	                        
    )
) );

//LIST
vc_map( array(
    "name" => __('Styled list', 'rehub_framework'),
    "base" => "wpsm_list",
    "icon" => "icon-s-list",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Styled simple list', 'rehub_framework'), 
    "params" => array(	
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Type of list', 'rehub_framework'),
			"param_name" => "type",
			"value" => array(
				__('Arrow', 'rehub_framework') => "arrow",
				__('Check', 'rehub_framework') => "check",	
				__('Star', 'rehub_framework') => "star",
				__('Bullet', 'rehub_framework') => "bullet"
			)
		),
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			"admin_label" => true,
			'param_name' => 'content',
			'value' => __( '<ul><li>Item 1</li><li>Item 2</li><li>Item 3</li><li>Item 4</li></ul>', 'rehub_framework' ),
		),                                  	                        
    )
) );

//NUMBERED HEADING
vc_map( array(
    "name" => __('Numbered Headings', 'rehub_framework'),
    "base" => "wpsm_numhead",
    "icon" => "icon-numhead",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Numbered Headings', 'rehub_framework'), 
    "params" => array(	
		array(
            "type" => "textfield",
            "heading" => __('Number', 'rehub_framework'),
            "param_name" => "num",
            "value" => '1',           
        ),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Style of number', 'rehub_framework'),
			"param_name" => "style",
			"admin_label" => true,
			"value" => array(
				__('Orange', 'rehub_framework') => "3",
				__('Black', 'rehub_framework') => "2",	
				__('Grey', 'rehub_framework') => "1",
				__('Blue', 'rehub_framework') => "4"
			)
		), 
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Heading', 'rehub_framework'),
			"param_name" => "heading",
			"value" => array(
				"H2" => "2",	
				"H1" => "1",
				"H3" => "3",
				"H4" => "4",
			)
		), 		       
		array(
            "type" => "textarea",
            "heading" => __('Text', 'rehub_framework'),
            "param_name" => "content",
            "admin_label" => true,
            "value" => 'Lorem ipsum dolor sit amet',           
        ),                                  	                        
    )
) );


//NUMBERED BOX
vc_map( array(
    "name" => __('Box with number', 'rehub_framework'),
    "base" => "wpsm_numbox",
    "icon" => "icon-numbox",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Box with number', 'rehub_framework'), 
    "params" => array(	
		array(
            "type" => "textfield",
            "heading" => __('Number', 'rehub_framework'),
            "param_name" => "num",
            "value" => '1',           
        ),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Style of number', 'rehub_framework'),
			"param_name" => "style",
			"admin_label" => true,
			"value" => array(
				__('Orange', 'rehub_framework') => "3",
				__('Black', 'rehub_framework') => "2",	
				__('Grey', 'rehub_framework') => "1",
				__('Blue', 'rehub_framework') => "4"
			)
		), 
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			'param_name' => 'content',
			"admin_label" => true,
			'value' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim',
		),		 		       
                                  	                        
    )
) );

//CART BOX
vc_map( array(
    "name" => __('Cart box', 'rehub_framework'),
    "base" => "wpsm_cartbox",
    "icon" => "icon-cartbox",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Box with image', 'rehub_framework'), 
    "params" => array(  
        array(
            "type" => "textfield",
            "heading" => __('Title', 'rehub_framework'),
            "param_name" => "title",
            "value" => '', 
            "admin_label" => true,                      
        ),
        array(
            "type" => "textfield",
            "heading" => __('Description', 'rehub_framework'),
            "param_name" => "description",
            "value" => '',           
        ), 
        array(
            'type' => 'attach_image',
            'heading' => __('Image', 'rehub_framework'),
            'param_name' => 'image',
            'value' => '',
        ),
        array(
            "type" => "checkbox",
            "class" => "",
            "heading" => __('Make background image contain?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "bg_contain",         
        ),  
        array(
            "type" => "checkbox",
            "class" => "",
            "heading" => __('Show image first?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "revert_image",         
        ),               
        array(
            "type" => "textfield",
            "heading" => __('URL:', 'rehub_framework'),
            "param_name" => "link",
            'description' => __('Will be used on image and title', 'rehub_framework'),
        ),                                              
                                                            
    )
) );

//TITLED BOX
vc_map( array(
    "name" => __('Titled box', 'rehub_framework'),
    "base" => "wpsm_titlebox",
    "icon" => "icon-titlebox",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Box with border and title', 'rehub_framework'), 
    "params" => array(	
		array(
            "type" => "textfield",
            "heading" => __('Title', 'rehub_framework'),
            "param_name" => "title",
            "value" => __('Title of box', 'rehub_framework'),           
        ),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Style', 'rehub_framework'),
			"param_name" => "style",
			"admin_label" => true,
			"value" => array(
				__('Grey', 'rehub_framework') => "1",
				__('Black', 'rehub_framework') => "2",	
				__('Orange', 'rehub_framework') => "3",
				__('Double dotted', 'rehub_framework') => "4"
			)
		), 
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			'param_name' => 'content',
			"admin_label" => true,
			'value' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim',
		),		 		       
                                  	                        
    )
) );

//COLORED TABLE
vc_map( array(
    "name" => __('Colored Table', 'rehub_framework'),
    "base" => "wpsm_colortable",
    "icon" => "icon-colortable",
    'category' => __( 'Content', 'js_composer' ),
    'description' => __('Table with color header', 'rehub_framework'), 
    "params" => array(	
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Color of heading table :', 'rehub_framework'),
			"param_name" => "color",
			"admin_label" => true,
			"value" => array(
				__('grey', 'rehub_framework') => "grey",
				__('black', 'rehub_framework') => "black",	
				__('yellow', 'rehub_framework') => "yellow",
				__('blue', 'rehub_framework') => "blue",
				__('red', 'rehub_framework') => "red",
				__('green', 'rehub_framework') => "green",	
				__('orange', 'rehub_framework') => "orange",
				__('purple', 'rehub_framework') => "purple",				
			)
		), 
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			'param_name' => 'content',
			'value' => '<table>
							<thead>
								<tr>
									<th style="width: 25%;">Heading 1</th>
									<th style="width: 25%;">Heading 2</th>
									<th style="width: 25%;">Heading 3</th>
									<th style="width: 25%;">Heading 4</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
								</tr>
								<tr class="odd">
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
								</tr>
								<tr>
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
								</tr>
								<tr class="odd">
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
									<td>Value</td>
								</tr>
							</tbody>
						</table>',
		),		 		                                       	                        
    )
) );


//PRICE TABLES

vc_map( array(
    "name" => __("Price table", "rehub_framework"),
    "base" => "wpsm_price_table",
    "icon" => "icon-pricetable",    
    "as_parent" => array('only' => 'wpsm_price_column'),
    "content_element" => true,
    "show_settings_on_create" => false,
    "params" => array(
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", "rehub_framework"),
            "param_name" => "el_class",
        )
    ),
    "js_view" => 'VcColumnView'
) );
vc_map( array(
    "name" => __("Price table column", "rehub_framework"),
    "base" => "wpsm_price_column",
    "icon" => "icon-pricetable", 
    "content_element" => true,
    "as_child" => array('only' => 'wpsm_price_table'), 
    "params" => array(
		array(
			"type" => "dropdown",
			"heading" => __('Column size', 'rehub_framework'),
			"param_name" => "size",
			"value" => array(
				'1/3' => "3",
				"1/4" => "4",	
				"1/5" => "5",
				"1/2" => "2"
			)
		),
		array(
			"type" => "dropdown",
			"heading" => __('Featured', 'rehub_framework'),
			"param_name" => "featured",
			"value" => array(
				__('No', 'rehub_framework') => "no",
				__('Yes', 'rehub_framework') => "yes",	
			)
		),
		array(
            "type" => "textfield",
            "heading" => __('Title', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "name",
            "value" => __('Title of box', 'rehub_framework'),           
        ),
		array(
            "type" => "textfield",
            "heading" => __('Price', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "price",
            'edit_field_class' => 'vc_col-md-6 vc_column',
            "value" => __('$99.99', 'rehub_framework'),           
        ),  
		array(
            "type" => "textfield",
            "heading" => __('Per', 'rehub_framework'),
            "param_name" => "per",
            'edit_field_class' => 'vc_col-md-6 vc_column',
            "value" => __('month', 'rehub_framework'),           
        ),  
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __('Color', 'rehub_framework'),
			"param_name" => "color",
			"value" => array(
				__('orange', 'rehub_framework') => "orange",
				__('gold', 'rehub_framework') => "gold",
				__('black', 'rehub_framework') => "black",	
				__('blue', 'rehub_framework') => "blue",
				__('red', 'rehub_framework') => "red",
				__('green', 'rehub_framework') => "green",	
				__('rosy', 'rehub_framework') => "rosy",
				__('brown', 'rehub_framework') => "brown",
				__('pink', 'rehub_framework') => "pink",
				__('purple', 'rehub_framework') => "purple",
				__('teal', 'rehub_framework') => "teal",				
			)
		), 
		array(
            "type" => "textfield",
            "heading" => __('Button URL', 'rehub_framework'),
            "param_name" => "button_url",       
        ),
		array(
            "type" => "textfield",
            "heading" => __('Button text', 'rehub_framework'),
            "param_name" => "button_text",
            "value" => "Buy this",       
        ), 
		array(
			'type' => 'textarea_html',
			'heading' => __( 'List of items', 'rehub_framework' ),
			'param_name' => 'content',
			'value' => __( '<ul><li>Item 1</li><li>Item 2</li><li>Item 3</li><li>Item 4</li></ul>', 'rehub_framework' ),
		),                		                    				
    )
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_Wpsm_Price_Table extends WPBakeryShortCodesContainer {
    }
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Wpsm_Price_Column extends WPBakeryShortCode {
    }
}

//MEMBER BLOCK CONTENT
vc_map( array(
    "name" => __('Text for members block', 'rehub_framework'),
    "base" => "wpsm_member",
    "icon" => "icon-memberbox",
    "category" => __('Content', 'js_composer'),
    'description' => __('Hide from guests', 'rehub_framework'), 
    "params" => array(	
		array(
            "type" => "textfield",
            "heading" => __('Text for guests', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "guest_text",
            "value" => __('Please, login or register to view this content', 'rehub_framework'),           
        ),
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Content', 'rehub_framework' ),
			'param_name' => 'content',
			"admin_label" => true,
			'value' => __( 'Text for members', 'rehub_framework' ),
		),                                  	                        
    )
) );

//POPUP BUTTON
vc_map( array(
    "name" => __('Button with popup', 'rehub_framework'),
    "base" => "wpsm_button_popup",
    "icon" => "icon-button_popup",
    "category" => __('Content', 'js_composer'),
    'description' => __('Popup on button click', 'rehub_framework'), 
    "params" => array( 
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Color of button', 'rehub_framework'),
            "param_name" => "color",
            "value" => array(
                __('orange', 'rehub_framework') => "orange",
                __('gold', 'rehub_framework') => "gold",
                __('black', 'rehub_framework') => "black",  
                __('blue', 'rehub_framework') => "blue",
                __('red', 'rehub_framework') => "red",
                __('green', 'rehub_framework') => "green",  
                __('rosy', 'rehub_framework') => "rosy",
                __('brown', 'rehub_framework') => "brown",
                __('pink', 'rehub_framework') => "pink",
                __('purple', 'rehub_framework') => "purple",
                __('teal', 'rehub_framework') => "teal",                
            )
        ),
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Button Size', 'rehub_framework'),
            "param_name" => "size",
            "value" => array(
                __('Medium', 'rehub_framework') => "medium",                
                __('Small', 'rehub_framework') => "small",
                __('Big', 'rehub_framework') => "big",                  
            )
        ),
        array(
            "type" => "checkbox",
            "class" => "",
            "heading" => __('Enable icon in button?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "enable_icon",         
        ),        
        array(
            'type' => 'iconpicker',
            'heading' => __( 'Icon', 'rehub_framework' ),
            'param_name' => 'icon',
            'value' => '',
            'settings' => array(
                'emptyIcon' => true,
                'iconsPerPage' => 100,
            ),
            "dependency" => Array('element' => "enable_icon", 'not_empty' => true),
        ),                     
        array(
            "type" => "textfield",
            "heading" => __('Button text', 'rehub_framework'),
            "admin_label" => true,
            "param_name" => "btn_text",         
        ),
        array(
            "type" => "textfield",
            "heading" => __('Max width of popup', 'rehub_framework'),
            "param_name" => "max_width",
            "value" => 500         
        ),        
        array(
            'type' => 'textarea_html',
            'heading' => __( 'Content', 'rehub_framework' ),
            'param_name' => 'content',
            "admin_label" => true,
            'value' => __( 'Content of popup. You can use also shortcode', 'rehub_framework' ),
        ),                                                              
    )
) );


//CTA BLOCK
vc_add_param("vc_cta_button2", array(
	'type' => 'colorpicker',
	'heading' => __( 'Text Color', 'rehub_framework' ),
	'param_name' => 'text_color',
));

//TABS BLOCK

vc_add_param("vc_tabs", array(
	"type" => "checkbox",
	"heading" => __('Enable design of tabs without border?', 'rehub_framework'),
	"value" => array(__("Yes", "rehub_framework") => true ),
	"param_name" => "style_sec",
));

if (defined('WPB_VC_VERSION') && version_compare(WPB_VC_VERSION, '4.6.0', '>=')) {
    vc_remove_param("vc_tta_tabs", "title");
    vc_add_param("vc_tta_tabs", array(
        "type" => "checkbox",
        "heading" => __('Overwrite design of tabs with theme settings?', 'rehub_framework'),
        "value" => array(__("Yes", "rehub_framework") => true ),
        "weight" => 100,
        "param_name" => "style_rehub",
    ));
    vc_add_param("vc_tta_tabs", array(
        "type" => "checkbox",
        "heading" => __('Enable design of tabs without border?', 'rehub_framework'),
        "value" => array(__("Yes", "rehub_framework") => true ),
        "weight" => 99,
        "param_name" => "style_sec",
    ));
        
}


//MDTF

vc_map( array(
    "name" => __('MDTF shortcode', 'rehub_framework'),
    "base" => "mdtf_shortcode",
    "icon" => "icon-mdtf",
    'description' => __('Works only with MDTF', 'rehub_framework'), 
    "params" => array(
        array(
            "type" => "dropdown",
            "class" => "",
            "heading" => __('Output template', 'rehub_framework'),
            "param_name" => "data_source",
            "value" => array(
                __('Column grid loop', 'rehub_framework') => "template/column",
                __('Masonry grid loop', 'rehub_framework') => "template/grid",  
                __('List loop', 'rehub_framework') => "template/list",
                __('Offer grid - use only with thirstyaffiliate enabled', 'rehub_framework') => "template/offer",
                __('Woocommerce grid - use only with woocommerce enabled', 'rehub_framework') => "woocommerce",
                __('Woocommerce list - use only with woocommerce enabled', 'rehub_framework') => "template/woolist",                
                __('Review list - use only for posts', 'rehub_framework') => "template/reviewlist",                
            ), 
        ),
        array(
            'type' => 'dropdown',
            'heading' => __( 'Choose post type', 'rehub_framework' ),
            'param_name' => 'post_type',
            'value' => rehub_post_type_vc(),
            'dependency' => array(
                'element' => 'data_source',
                'value_not_equal_to' => array( 'woocommerce'),
            ),            
        ),              
            
        array(
            'type' => 'dropdown',
            'heading' => __( 'Order by', 'js_composer' ),
            'param_name' => 'orderby',
            'value' => array(
                __( 'Date', 'js_composer' ) => 'date',
                __( 'Order by post ID', 'js_composer' ) => 'ID',
                __( 'Title', 'js_composer' ) => 'title',
                __( 'Last modified date', 'js_composer' ) => 'modified',
                __( 'Number of comments', 'js_composer' ) => 'comment_count',
                __( 'Menu order/Page Order', 'js_composer' ) => 'menu_order',
                __( 'Random order', 'js_composer' ) => 'rand',
            ),
        ),
        array(
            'type' => 'dropdown',
            'heading' => __( 'Sorting', 'js_composer' ),
            'param_name' => 'order',
            'value' => array(
                __( 'Descending', 'js_composer' ) => 'DESC',
                __( 'Ascending', 'js_composer' ) => 'ASC',
            ),
            'description' => __( 'Select sorting order.', 'js_composer' ),
        ),  
        array(
            "type" => "textfield",
            "heading" => __('Fetch Count', 'rehub_framework'),
            "param_name" => "show",
            "value" => '9',
            'description' => __('Number of products to display', 'rehub_framework'),         
        ),                                       
        array(
            'type' => 'dropdown',
            'heading' => __( 'Pagination position', 'rehub_framework' ),
            'param_name' => 'pag_pos',
            'value' => array(
                __( 'Top and bottom', 'rehub_framework' ) => 'tb',
                __( 'Top', 'rehub_framework' ) => 't',
                __( 'Bottom', 'rehub_framework' ) => 'b',
            ),            
        ), 
        array(
            "type" => "textfield",
            "heading" => __('Taxonomies', 'rehub_framework'),
            "param_name" => "tax",
            "value" => '',
            'description' => __('if you want to show posts of any custom taxonomies. Example of setting this field: taxonomies=product_cat+77,96,12', 'rehub_framework'),         
        ), 
        array(
            "type" => "textfield",
            "heading" => __('ID of sort panel', 'rehub_framework'),
            "param_name" => "sortid",
            "value" => '',
            'description' => __('if you want to sort panel before posts, write id of panel', 'rehub_framework'),         
        ),                        
        array(
            "type" => "checkbox",
            "heading" => __('Enable ajax?', 'rehub_framework'),
            "value" => array(__("Yes", "rehub_framework") => true ),
            "param_name" => "ajax",    

        ),                                                             
    )
) );
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Mdtf_Shortcode extends WPBakeryShortCode {
    }
}


}




?>