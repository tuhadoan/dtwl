<?php
function dtwl_woo_get_id(){
	$chars = '0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	$token = '';
	$id = session_id();
	for ($i = 0; $i < 32; ++$i)
	{
		$token .= $chars[(rand(0, $max))];
	}
	return 'dtwl_woo_'.substr(md5($token.$id),0,10);
}

function dhwl_woo_setting_field_id($settings, $value, $dependency=''){
	if(empty($value)){
		$value = dtwl_woo_get_id();
	}
	return '<input name="'.$settings['param_name'].'" class="wpb_vc_param_value dtwl-woo-param-value wpb-textinput" type="hidden" value="'.$value.'"/>';
}

function dtwl_woo_setting_field_categories($settings, $value, $dependency=''){
	$category_ids = explode(',',$value);
	$args = array(
		'orderby' => 'name',
		'hide_empty' => 0,
	);
	$categories = get_terms( 'product_cat', $args );
	$output = '<select '.$dependency.' id= "'.$settings['param_name'].'" multiple="multiple" class="dtwl-woo-select chosen_select_nostd">';
	$output .= '<option value="">' . esc_html__('-- Select --',DT_WOO_LAYOUTS) . '</option>';
	if( ! empty($categories)){
		foreach ($categories as $cat):
		$output .= '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
		endforeach;
	}
	$output .= '</select>';
	$output .='<input id= "'.$settings['param_name'].'" type="hidden" class="wpb_vc_param_value wpb-textinput" name="'.$settings['param_name'].'" value="'.$value.'" />';
	return $output;
}

function dtwl_woo_setting_field_tags($settings, $value, $dependency = ''){
	$tags_ids = explode(',', $value);
	
	$args = array(
		'order_by'		=> 'name',
		'hide_empty'	=> 0
	);
	
	$tags = get_terms('product_tag', $args);
	$output = '<select '.$dependency.' id="'.$settings['param_name'].'" multiple="multiple" class="dtwl-woo-select chosen_select_nostd">';
	if( ! empty($tags) ){
		foreach ($tags as $tag){
			$output .= '<option value="' . esc_attr($tag->term_id) . '" '. selected( in_array($tag->term_id, $tags_ids), true, false) .'>' . esc_html( $tag->name ) . '</option>';
		}
	}
	$output .= '</select>';
	$output .= '<input id= "'.$settings['param_name'].'" type="hidden" class="wpb_vc_param_value wpb-textinput" name="'.$settings['param_name'].'" value="'.$value.'" />';
	return $output;
}

function dtwl_woo_setting_field_heading( $settings, $value, $dependency='' ){
	return '<div '.$dependency.' style="background: none repeat scroll 0 0 #ffcc00;font-size: 14px; color: #ffffff; font-weight: bold;padding: 5px;">'.$value.'</div>';
}

function dtwl_woo_setting_field_orderby($settings, $value, $dependency = ''){
	$orderby_values = explode(',', $value);
	
	$list_orderbys = array(
		'recent'	=> esc_html__('Latest Products', DT_WOO_LAYOUTS),
		'best_selling'	=> esc_html__('BestSeller Products', DT_WOO_LAYOUTS),
		'top_rate'	=> esc_html__('Top Rated Products', DT_WOO_LAYOUTS),
		'on_sale'	=> esc_html__('Special Products', DT_WOO_LAYOUTS),
		'featured_product'	=> esc_html__('Featured Products', DT_WOO_LAYOUTS),
		'recent_review'	=> esc_html__('Recent Review', DT_WOO_LAYOUTS),
	);
	
	$output = '<select '.$dependency.' id= "'.$settings['param_name'].'" multiple="multiple" class="dtwl-woo-select chosen_select_nostd">';
	$output .= '<option value="">' . esc_html__('-- Select --',DT_WOO_LAYOUTS) . '</option>';
	
	foreach ($list_orderbys as $key => $orderby):
	$output .= '<option value="' . esc_attr( $key ) . '"' . selected( in_array($key, $orderby_values), true, false ) . '>' . esc_html( $orderby ) . '</option>';
	endforeach;
	
	$output .= '</select>';
	$output .='<input id= "'.$settings['param_name'].'" type="hidden" class="wpb_vc_param_value wpb-textinput" name="'.$settings['param_name'].'" value="'.$value.'" />';
	return $output;
}

function dhwl_woo_query($type, $post_per_page=-1, $cat='', $tag = '', $offset=0, $paged=1){
	global $woocommerce;
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => $post_per_page,
		'post_status' => 'publish',
		'offset'            => $offset,
		'paged' => $paged
	);
	switch ($type) {
		case 'best_selling':
			$args['meta_key']='total_sales';
			$args['orderby']='meta_value_num';
			$args['ignore_sticky_posts']   = 1;
			$args['meta_query'] = array();
			$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'featured_product':
			$args['ignore_sticky_posts']=1;
			$args['meta_query'] = array();
			$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$args['meta_query'][] = array(
				'key' => '_featured',
				'value' => 'yes'
			);
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'top_rate':
			add_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
			$args['meta_query'] = array();
			$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'recent':
			$args['meta_query'] = array();
			$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			break;
		case 'on_sale':
			$args['meta_query'] = array();
			$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			$args['post__in'] = wc_get_product_ids_on_sale();
			break;
		case 'recent_review':
			if($post_per_page == -1) $_limit = 4;
			else $_limit = $post_per_page;
			global $wpdb;
			$query = $wpdb->prepare( "
				SELECT c.comment_post_ID
				FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c
				WHERE p.ID = c.comment_post_ID AND c.comment_approved > %d
				AND p.post_type = %s AND p.post_status = %s
				AND p.comment_count > %d ORDER BY c.comment_date ASC" ,
				0, 'product', 'publish', 0 );
				$results = $wpdb->get_results($query, OBJECT);
				$_pids = array();
					foreach ($results as $re) {
			if(!in_array($re->comment_post_ID, $_pids))
				$_pids[] = $re->comment_post_ID;
				if(count($_pids) == $_limit)
					break;
				}

			$args['meta_query'] = array();
			$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			$args['post__in'] = $_pids;
			break;
	}

	if($cat!=''){
		$args['product_cat']= $cat;
	}
	
	if($tag!=''){
		$args['product_tag']= $tag;
	}
	wp_reset_postdata();
	return new WP_Query($args);
}


function dhwl_woo_params(){
	return array(
// 		array (
// 			"type" => "dtwl_woo_field_id",
// 			"param_name" => "id"
// 		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Heading", DT_WOO_LAYOUTS ),
			'admin_label'=> true,
			"param_name" => "heading",
			"value" => ""
		),
		array (
			"type" => "colorpicker",
			"class" => "",
			"heading" => esc_html__( "Heading Color", DT_WOO_LAYOUTS ),
			"param_name" => "heading_color",
			"value" => "#363230"
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Heading font size", DT_WOO_LAYOUTS ),
			"param_name" => "heading_font_size",
			"value" => "20px",
			"description" => __ ( "Enter your custom size. Example: 20px.", DT_WOO_LAYOUTS ),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Display Type", DT_WOO_LAYOUTS),
			"param_name" => "display_type",
			"admin_label" => true,
			"value" => array(
				"Product Tabs" => "product_tabs",
				"Product Slider" =>  "product_slider",
				"Product List" =>  "product_list",
			),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Template", DT_WOO_LAYOUTS),
			"param_name" => "template",
			"value" => array(
				esc_html__("Grid", DT_WOO_LAYOUTS) => "grid",
				esc_html__("Carousel", DT_WOO_LAYOUTS) =>  "carousel",
				esc_html__("Masonry", DT_WOO_LAYOUTS) =>  "masonry",
			),
			"dependency" => array (
				'element' => "display_type",
				'value' => array (
					'product_tabs',
				)
			),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Query types", DT_WOO_LAYOUTS),
			"param_name" => "query_types",
			"value" => array(
				"Categories" 	=> "category",
				"Tags" 			=>  "tags",
				"Order By" 		=>  "orderby"
			),
			"dependency" => array (
				'element' => "display_type",
				'value' => array (
					'product_tabs',
					'product_slider',
				)
			),
			"description" => ""
		),
		array (
			"type" => "dtwl_woo_field_categories",
			"class" => "",
			"heading" => __ ( "Categories", DT_WOO_LAYOUTS ),
			"param_name" => "categories",
			"dependency" => array (
				'element' => "query_types",
				'value' => array (
					'category',
				)
			)
		),
		array (
			"type" => "dtwl_woo_field_tags",
			"class" => "",
			"heading" => __ ( "Tags", DT_WOO_LAYOUTS ),
			"param_name" => "tags",
			"dependency" => array (
				'element' => "query_types",
				'value' => array (
					'tags',
				)
			)
		),
		array(
			"type" => "dtwl_woo_field_orderby",
			"class" => "",
			"heading" => esc_html__("Select Order By",DT_WOO_LAYOUTS),
			"param_name" => "list_orderby",
			"dependency" => array("element" => "query_types" , "value" => "orderby" ),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Order By", DT_WOO_LAYOUTS),
			"param_name" => "orderby",
			"value" => array(
				esc_html__('Latest Products',  DT_WOO_LAYOUTS) => "recent",
				esc_html__('BestSeller Products',  DT_WOO_LAYOUTS) => "best_selling",
				esc_html__('Top Rated Products',  DT_WOO_LAYOUTS) => "top_rate",
				esc_html__('Special Products',  DT_WOO_LAYOUTS) => "on_sale",
				esc_html__('Featured Products',  DT_WOO_LAYOUTS) => "featured_product",
				esc_html__('Recent Review',  DT_WOO_LAYOUTS) => "recent_review",
			),
			"dependency" => array (
				'element' => "query_types",
				'value' => array (
					'category',
					'tags',
				)
			),
			"description" => ""
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Row", DT_WOO_LAYOUTS),
			"param_name" => "row",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
			"value" => "2"
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Column per Row", DT_WOO_LAYOUTS),
			"param_name" => "col",
			"dependency" => array("element" => "template" , "value" => array("grid","masonry") ),
			"value" => array(
				'2' => "2",
				'3' => "3",
				'4' => "4",
				'5' => "5",
				'6' => "6",
			),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Number product with each click to Load more button", DT_WOO_LAYOUTS),
			"param_name" => "number_load",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
			"value" => array(
				'2' => "2",
				'3' => "3",
				'4' => "4",
				'5' => "5",
				'6' => "6",
			),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Select Effect", DT_WOO_LAYOUTS),
			"param_name" => "effect_load",
			"value" => array(
				esc_html__('zoomOut',  DT_WOO_LAYOUTS) => "zoomOut",
				esc_html__('zoomIn',  DT_WOO_LAYOUTS) => "zoomIn",
				esc_html__('pageRight',  DT_WOO_LAYOUTS) => "pageRight",
				esc_html__('pageLeft',  DT_WOO_LAYOUTS) => "pageLeft",
				esc_html__('pageTop',  DT_WOO_LAYOUTS) => "pageTop",
				esc_html__('pageBottom',  DT_WOO_LAYOUTS) => "pageBottom",
				esc_html__('starwars',  DT_WOO_LAYOUTS) => "starwars",
				esc_html__('slideBottom',  DT_WOO_LAYOUTS) => "slideBottom",
				esc_html__('slideLeft',  DT_WOO_LAYOUTS) => "slideLeft",
				esc_html__('slideRight',  DT_WOO_LAYOUTS) => "slideRight",
				esc_html__('bounceIn',  DT_WOO_LAYOUTS) => "bounceIn",
			),
			"dependency" => array("element" => "template" , "value" => array("grid") ),
			"description" => ""
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Number display", DT_WOO_LAYOUTS),
			"param_name" => "number_display",
			"dependency" => array("element" => "template" , "value" => array("carousel") ),
			"value" => "4"
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Product number limit", DT_WOO_LAYOUTS),
			"param_name" => "number_limit",
			"dependency" => array("element" => "template" , "value" => array("carousel","masonry") ),
			"value" => "10",
			"description" => esc_html__('the number limit to query.', DT_WOO_LAYOUTS),
		),
		
		
		// Product list params
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Order By", DT_WOO_LAYOUTS),
			"param_name" => "plist_orderby",
			"value" => array(
				esc_html__('Latest Products',  DT_WOO_LAYOUTS) => "recent",
				esc_html__('BestSeller Products',  DT_WOO_LAYOUTS) => "best_selling",
				esc_html__('Top Rated Products',  DT_WOO_LAYOUTS) => "top_rate",
				esc_html__('Special Products',  DT_WOO_LAYOUTS) => "on_sale",
				esc_html__('Featured Products',  DT_WOO_LAYOUTS) => "featured_product",
				esc_html__('Recent Review',  DT_WOO_LAYOUTS) => "recent_review",
			),
			"dependency" => array("element" => "display_type" , "value" => "product_list" ),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Short Description", DT_WOO_LAYOUTS),
			"param_name" => "plist_desc",
			"value" => array(
				esc_html__('Hide',  DT_WOO_LAYOUTS) => "hide",
				esc_html__('Show',  DT_WOO_LAYOUTS) => "show",
			),
			"dependency" => array("element" => "display_type" , "value" => "product_list" ),
			"description" => esc_html__("Show / Hide Product short description. if show, the button add to cart will be shown.", DT_WOO_LAYOUTS )
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Number display", DT_WOO_LAYOUTS),
			"param_name" => "list_limit",
			"dependency" => array("element" => "display_type" , "value" => "product_list" ),
			"value" => "4"
		),
		
		// Product Slider params
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Speed. int(ms)", DT_WOO_LAYOUTS),
			"param_name" => "pslides_speed",
			"dependency" => array("element" => "display_type" , "value" => 'product_slider' ),
			"value" => "300",
			"description" => "",
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Width Between Each Slide. int(px)", DT_WOO_LAYOUTS),
			"param_name" => "pslides_margin",
			"dependency" => array("element" => "display_type" , "value" => 'product_slider' ),
			"value" => "10px",
			"description" => esc_html__('', DT_WOO_LAYOUTS),
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Slides To Show.", DT_WOO_LAYOUTS),
			"param_name" => "pslides_toshow",
			"dependency" => array("element" => "display_type" , "value" => 'product_slider' ),
			"value" => "3",
			"description" => "",
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Slides To Scroll", DT_WOO_LAYOUTS),
			"param_name" => "pslides_toscroll",
			"dependency" => array("element" => "display_type" , "value" => 'product_slider' ),
			"value" => "3",
			"description" => "",
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Product number limit", DT_WOO_LAYOUTS),
			"param_name" => "pslider_limit",
			"dependency" => array("element" => "display_type" , "value" => 'product_slider' ),
			"value" => "10",
			"description" => esc_html__('The number limit to query.', DT_WOO_LAYOUTS),
		),
		
		// Custom Options
		array (
			"type" => "dtwl_woo_field_heading",
			"value" => "Custom Options",
			"param_name" => "custom_options"
		),
		array(
			"type" => "colorpicker",
			"value" => "#ff4800",
			"heading" => esc_html__("Main Color", DT_WOO_LAYOUTS),
			"param_name" => "main_color"
		),
		// for procut list
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("List Border Wrapper", DT_WOO_LAYOUTS),
			"param_name" => "plist_border",
			"value" => array(
				esc_html__('no',  DT_WOO_LAYOUTS) => "no",
				esc_html__('Yes',  DT_WOO_LAYOUTS) => "yes",
			),
			"dependency" => array("element" => "display_type" , "value" => array('product_list') ),
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("List Padding", DT_WOO_LAYOUTS),
			"param_name" => "plist_padding",
			"dependency" => array("element" => "display_type" , "value" => "product_list" ),
			"value" => "0px",
			"description" => __ ( "Enter your custom padding of product list wrapper. Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		
		array(
			"type" => "colorpicker",
			"value" => "#ffffff",
			"heading" => esc_html__("Thumbnail Backgroud Color", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_background_color"
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Thumbnail Border Style", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_style",
			"value" => array(
				esc_html__('None',  DT_WOO_LAYOUTS) => "none",
				esc_html__('Solid',  DT_WOO_LAYOUTS) => "solid",
				esc_html__('Dashed',  DT_WOO_LAYOUTS) => "dashed",
				esc_html__('Dotted',  DT_WOO_LAYOUTS) => "dotted",
				esc_html__('Double',  DT_WOO_LAYOUTS) => "double",
				esc_html__('Groove',  DT_WOO_LAYOUTS) => "groove",
				esc_html__('Ridge',  DT_WOO_LAYOUTS) => "ridge",
				esc_html__('Inset',  DT_WOO_LAYOUTS) => "inset",
				esc_html__('Outset',  DT_WOO_LAYOUTS) => "outset",
				esc_html__('Mix',  DT_WOO_LAYOUTS) => "mix",
			),
		),
		array(
			"type" => "colorpicker",
			"value" => "#e1e1e1",
			"heading" => esc_html__("Thumbnail Border Color", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_color",
			"dependency" => array("element" => "thumbnail_border_style" , "value" => array('solid','dashed','dotted','double','groove','ridge','inset','outset','mix') ),
		),
		array(
			"type" => "textfield",
			"value" => "1px",
			"heading" => esc_html__("Thumbnail Border Width", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_width",
			"dependency" => array("element" => "thumbnail_border_style" , "value" => array('solid','dashed','dotted','double','groove','ridge','inset','outset','mix') ),
			"description" => esc_html__("Enter border width: ex: 1px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Thumbnail Border Radius", DT_WOO_LAYOUTS ),
			"param_name" => "thumbnail_border_radius",
			"value" => "0px",
			"description" => __ ( "Enter your custom border radius . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Thumbnail Padding", DT_WOO_LAYOUTS ),
			"param_name" => "thumbnail_padding",
			"value" => '0',
			"description" => __ ( "Enter your custom padding . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Thumbnail Margin", DT_WOO_LAYOUTS ),
			"param_name" => "thumbnail_margin",
			"value" => '0',
			"description" => __ ( "Enter your custom margin . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		// Product title
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Product title font size", DT_WOO_LAYOUTS ),
			"param_name" => "product_font_size",
			"value" => "20px",
			"description" => __ ( "Enter your custom size. Example: 20px.", DT_WOO_LAYOUTS ),
		),
		array (
			"type" => "checkbox",
			"class" => "",
			"heading" => __ ( "Hide Rating", DT_WOO_LAYOUTS ),
			"param_name" => "show_rating",
			"value" => array (
				__ ( 'Yes, please', DT_WOO_LAYOUTS ) => '0'
			)
		),
		// Loadmore
		// Custom Loadmore
// 		array (
// 			"type" => "dtwl_woo_field_heading",
// 			"value" => "Custom Loadmore",
// 			"param_name" => "custom_options",
// 			"dependency" => array("element" => "display_type" , "value" => 'product_tabs' ),
// 		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Load more text", DT_WOO_LAYOUTS),
			"param_name" => "loadmore_text",
			"value" => "Load more",
			"dependency" => array("element" => "display_type" , "value" => 'product_tabs' ),
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Loaded text", DT_WOO_LAYOUTS),
			"param_name" => "loaded_text",
			"value" => "All ready",
			"dependency" => array("element" => "display_type" , "value" => 'product_tabs' ),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Loadmore Border Style", DT_WOO_LAYOUTS),
			"param_name" => "loadmore_border_style",
			"value" => array(
				esc_html__('Solid',  DT_WOO_LAYOUTS) => "solid",
				esc_html__('Dashed',  DT_WOO_LAYOUTS) => "dashed",
				esc_html__('Dotted',  DT_WOO_LAYOUTS) => "dotted",
				esc_html__('None',  DT_WOO_LAYOUTS) => "none",
			),
			"dependency" => array("element" => "display_type" , "value" => 'product_tabs' ),
		),
		array(
			"type" => "colorpicker",
			"value" => "#eaeaea",
			"heading" => esc_html__("Loadmore Border Color", DT_WOO_LAYOUTS),
			"param_name" => "loadmore_border_color",
			"dependency" => array("element" => "loadmore_border_style" , "value" => array('solid','dashed','dotted') ),
		),
		array(
			"type" => "textfield",
			"value" => "3px",
			"heading" => esc_html__("Loamore Border Width", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_width",
			"dependency" => array("element" => "loadmore_border_style" , "value" => array('solid','dashed','dotted') ),
			"description" => esc_html__("Enter border width: ex: 3px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Loamore Border Radius", DT_WOO_LAYOUTS ),
			"param_name" => "loadmore_border_radius",
			"value" => "0px",
			"dependency" => array("element" => "display_type" , "value" => 'product_tabs' ),
			"description" => __ ( "Enter your custom border radius . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		////
		array (
			"type" => "textfield",
			"heading" => esc_html__( "Extra class name", DT_WOO_LAYOUTS ),
			"param_name" => "el_class",
			"description" => esc_html__( "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", DT_WOO_LAYOUTS )
		),
	);
}


function dtwl_get_list_tab_title($query_types, $categories, $tags, $list_orderby){
	
	$array_tab 	= array();
	$list_tab 	= array();
	
	if($query_types == 'category'){
		if(empty($categories)){
			$array_tab 	= dtwl_get_cats();
		}else{
			$array_tab 	= explode(',', $categories);
		}
	}elseif($query_types == 'tags'){
		if(empty($tags)){
			$array_tab 	= dtwl_get_tags();
		}else{
			$array_tab 	= explode(',', $tags);
		}
	}else{ // list_orderby
		$array_tab 	= explode(',', $list_orderby);
	}
	
	foreach ($array_tab as $tab) {
		if($query_types == 'category' && $categories){
			$cat_term 	= get_term($tab,'product_cat');
			$tab 		= $cat_term->slug;
		}elseif($query_types == 'tags' && $tags){
			$tag_term 	= get_term($tab,'product_tag');
			$tab 		= $tag_term->slug;
		}
		
		$list_tab[$tab] = dtwl_tab_title($tab, $query_types);
	}
	
	return $list_tab;
}

function dtwl_tab_title($tab, $query_types){
	if( $query_types == 'category' ){
		$cat = get_term_by('slug', $tab, 'product_cat');
			
		return array('name'=>str_replace(' ', '_', $tab),'title'=>$cat->name,'short_title'=>$cat->name);
		
	}elseif($query_types == 'tags' ){ // Tab title is tags
		$tag = get_term_by('slug', $tab, 'product_tag');
			
		return array('name'=>str_replace(' ', '_', $tab),'title'=>$tag->name,'short_title'=>$tag->name);
		
	}else{
		switch ($tab) {
			case 'recent':
				return array('name'=>$tab,'title'=>esc_html__('Latest Products', DT_WOO_LAYOUTS),'short_title'=>esc_html__('Latest', DT_WOO_LAYOUTS));
			case 'featured_product':
				return array('name'=>$tab,'title'=>esc_html__('Featured Products', DT_WOO_LAYOUTS),'short_title'=>esc_html__('Featured', DT_WOO_LAYOUTS));
			case 'top_rate':
				return array('name'=>$tab,'title'=> esc_html__('Top Rated Products', DT_WOO_LAYOUTS),'short_title'=>esc_html__('Top Rated',  DT_WOO_LAYOUTS));
			case 'best_selling':
				return array('name'=>$tab,'title'=>esc_html__('BestSeller Products', DT_WOO_LAYOUTS),'short_title'=>esc_html__('Best Seller', DT_WOO_LAYOUTS));
			case 'on_sale':
				return array('name'=>$tab,'title'=>esc_html__('Special Products', DT_WOO_LAYOUTS),'short_title'=>esc_html__('Special', DT_WOO_LAYOUTS));
		}
	}
}

function dtwl_get_cats(){
	$cats = get_terms('product_cat');
	$arr = array();
	foreach ($cats as $cat) {
		$arr[] = $cat->slug;
	}
	return $arr;
}

function dtwl_get_tags(){
	$tags = get_terms('product_tag');
	$arr = array();
	foreach ($tags as $tag) {
		$arr[] = $tag->slug;
	}
	return $arr;
}

