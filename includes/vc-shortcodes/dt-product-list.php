<?php
class dtwoo_list{
	
	public function __construct(){
		$this->init();
	}
	
	public function init(){
		add_shortcode('dtwoo_list', array(&$this, 'dtwoo_list_sc'));
	}
	
	public function dtwoo_list_sc($atts, $content){
		$rt = rand().time();
		$html = '';
		
		extract( shortcode_atts(array(
		'heading'			=> '',
		'heading_color'		=> '#363230',
		'heading_font_size'	=> '20px',
		'categories'		=> '', // id
		'tags'				=> '', // id
		'orderby'			=> 'recent',
		'order'				=> 'DESC',
		'number_limit'		=> 4,
		'show_desc'			=> 'hide',
		// Custom options
		'main_color'		=> '#ff4800',
		'list_border'		=> 'no',
		'list_padding'		=> '',
		'thumbnail_background_color' => '#ffffff',
		'thumbnail_border_style' => 'none',
		'thumbnail_border_color' => '#e1e1e1',
		'thumbnail_border_width' => '1px',
		'thumbnail_border_radius' => '0px',
		'thumbnail_padding' => '',
		'thumbnail_margin' => '',
		'show_rating' => '1',
		'el_class'	=> '',
		), $atts) );
		
		$id	= dtwl_woo_get_id();
		$template_part = '';
		
		// Inline style
		$inline_style= '
		#'.$id.' {
		}
		
		#'.$id.' .dtwl-heading {
			color:' . $heading_color . ';
			font-size:' . $heading_font_size . ';
		}
		
		#'.$id.'.dtwl-woo-product-tabs .dtwl-woo-filters .dtwl-woo-nav-tabs li:hover a span,
		#'.$id.'.dtwl-woo-product-tabs .dtwl-woo-filters .dtwl-woo-nav-tabs li.focus a span,
		#'.$id.'.dtwl-woo-product-tabs .dtwl-woo-filters .dtwl-woo-nav-tabs li.active a span{
		  border-color: ' . $main_color . ';
		}
		
		#'.$id.' .dtwl-woo-images{
			margin: '. $thumbnail_margin .';
		}
		#'.$id.' .dtwl-woo-product-image img{
			border-style:' . $thumbnail_border_style . ';
			border-width:' . $thumbnail_border_width . ';
			border-color:' . $thumbnail_border_color . ';
			border-radius:' . $thumbnail_border_radius . ';
			-webkit-border-radius:' . $thumbnail_border_radius . ';
			padding:' . $thumbnail_padding . ';
		}
		';
		
		if($list_border == 'yes'){
			$inline_style .= '#'.$id.'.template-list .dtwl-woo-list-content{
				border: 1px solid #e8e4e3;
			}';
			$inline_style .= '#'.$id.'.template-list .dtwl-woo-list-content{
				padding: '. $list_padding .';
			}';
		}
		
		
		// Hide star-rating
		if( $show_rating == '0' )
			$inline_style .= '#'.$id.'.dtwl-woo .star-rating{display:none;}';
		
		$html .= '<style type="text/css">'.$inline_style.'</style>';
		
		global $woocommerce,$product,$wp_the_query;
		$posts_per_page      	= $number_limit ;
		$orderby    		 	= sanitize_title( $orderby );
		$order       			= sanitize_title( $order );
		
		$query_args = array(
			'posts_per_page' 	=> $posts_per_page,
			'post_status' 	 	=> 'publish',
			'post_type' 	 	=> 'product',
			'order'          	=> $order == 'asc' ? 'ASC' : 'DESC'
		);
		
		$category_array = array();
		if( !empty($categories) ){
			$category_array = array_filter(explode(',', $categories));
			
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
		
		$tags_array = array();
		if( !empty($tags) ){
			$tags_array = array_filter(explode(',', $tags));
			
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
		
		$query_args = apply_filters('dtwl_woo_query_args', $query_args, $atts, $content);
		$p = new WP_Query( $query_args  );
		
		global $plist_desc_show;
		$plist_desc_show = $show_desc;
			
		// Class
		$class = 'dtwl-woo dtwl-woo-product-list woocommerce dtwl-woo-template-list';
		$class .= esc_attr($el_class);
		ob_start();
		?>
		<div id="<?php echo $id;?>" class="<?php echo $class;?>">
			<?php if( ! empty( $heading ) ):?>
			<h2 class="dtwl-heading"><span><?php echo esc_html($heading);?></span></h2>
			<?php endif; ?>
			<div class="dtwl-woo-list-content">
				<?php 
				while ( $p->have_posts() ) : $p->the_post();
					wc_get_template( 'item-list.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
				endwhile;
				?>
			</div>
		</div>
		<?php
		$html .= ob_get_clean();
		wp_reset_postdata ();
		return $html;
	}
		
}
new dtwoo_list();