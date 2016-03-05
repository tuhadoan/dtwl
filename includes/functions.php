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
		$output .= '<option value="' . esc_attr( $cat->slug ) . '"' . selected( in_array( $cat->slug, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
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
			$output .= '<option value="' . esc_attr($tag->slug) . '" '. selected( in_array($tag->slug, $tags_ids), true, false) .'>' . esc_html( $tag->name ) . '</option>';
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

function dhwl_woo_tabs_query($query_types, $tab, $orderby, $post_per_page=-1, $offset=0, $paged=1){
	global $woocommerce;
	$query_args = array(
		'post_type' => 'product',
		'posts_per_page' => $post_per_page,
		'post_status' => 'publish',
		'offset'            => $offset,
		'paged' => $paged
	);
	
	if ($query_types == 'category'){
		if($tab!=''){
			$category_array = array_filter(explode(',', $tab));
			
			if(!empty($category_array)){
				$query_args['tax_query'][] =
				array(
					'taxonomy'			=> 'product_cat',
					'field'				=> 'slug',
					'terms'				=> $category_array,
					'operator'			=> 'IN'
				);
			}
		}
	}elseif ($query_types == 'tags'){
		if($tab!=''){
		$tags_array = array_filter(explode(',', $tab));
				
			if( !empty($tags_array) ){
				$query_args['tax_query'][] =
				array(
					'taxonomy'			=> 'product_tag',
					'field'				=> 'slug',
					'terms'				=> $tags_array,
					'operator'			=> 'IN'
				);
			}
		}
	}else{ // List orderby
		$tab = $orderby;
	}
	
	
	switch ($tab) {
		case 'best_selling':
			$query_args['meta_key']='total_sales';
			$query_args['orderby']='meta_value_num';
			$query_args['ignore_sticky_posts']   = 1;
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'featured_product':
			$query_args['ignore_sticky_posts']=1;
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = array(
				'key' => '_featured',
				'value' => 'yes'
			);
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'top_rate':
			add_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'recent':
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			break;
		case 'on_sale':
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			$query_args['post__in'] = wc_get_product_ids_on_sale();
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

				$query_args['meta_query'] = array();
				$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
					$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
					$query_args['post__in'] = $_pids;
				break;
		case 'price':
			$query_args['meta_key'] = '_price';
			$query_args['orderby'] = 'meta_value_num';
			$query_args['order'] = $order;
			break;
		case 'rand':
			$query_args['orderby']  = 'rand';
			break;
	}
	
	wp_reset_postdata();
	return new WP_Query($query_args);
}


function dhwl_woo_query_orderby($orderby = 'recent'){
	global $woocommerce;
	$query_args = array();
	switch ($orderby){
		case 'recent':
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			break;
		case 'best_selling':
			$query_args['meta_key']='total_sales';
			$query_args['orderby']='meta_value_num';
			$query_args['ignore_sticky_posts']   = 1;
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'featured_product':
			$query_args['ignore_sticky_posts']=1;
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = array(
				'key' => '_featured',
				'value' => 'yes'
			);
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'top_rate':
			add_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			break;
		case 'on_sale':
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			$query_args['post__in'] = wc_get_product_ids_on_sale();
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
	
				$query_args['meta_query'] = array();
					$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
					$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
					$query_args['post__in'] = $_pids;
				break;
					case 'price':
		$query_args['meta_key'] = '_price';
				$query_args['orderby'] = 'meta_value_num';
					$query_args['order'] = $order;
					break;
					case 'rand':
					$query_args['orderby']  = 'rand';
				break;
			default:
					$ordering_args = $woocommerce->query->get_catalog_ordering_args($orderby, $order);
					$query_args['orderby'] = $ordering_args['orderby'];
					break;
		}
		
		return $query_args;
}


function dtwl_get_list_tab_title($query_types, $categories, $tags, $tabs_orderby){
	
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
		$array_tab 	= explode(',', $tabs_orderby);
	}
	
	foreach ($array_tab as $tab) {
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
				return array('name'=>$tab,'title'=>esc_html__('Special Products', DT_WOO_LAYOUTS),'short_title'=>esc_html__('Sale', DT_WOO_LAYOUTS));
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
