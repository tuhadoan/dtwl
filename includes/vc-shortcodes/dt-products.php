<?php
class dtwoo_products{
	
	public function __construct(){
		$this->init();
	}
	
	public function init(){
		add_shortcode('dtwoo_products', array(&$this, 'dtwoo_products_sc'));
		
		add_action('wp_ajax_dtwl_woosetmodeview', array(&$this, 'dtwl_woosetmodeview'));
		add_action('wp_ajax_nopriv_dtwl_woosetmodeview', array(&$this, 'dtwl_woosetmodeview'));
	}
	
	public function dtwoo_products_sc($atts, $content){
		$rt = rand().time();
		$html = '';
		
		extract( shortcode_atts(array(
		'heading'			=> '',
		'heading_color'		=> '#363230',
		'heading_font_size'	=> '20px',
		'template'			=> 'grid',
		'switch_template'	=> 'yes',
		'col'				=> 4,
		'categories'		=> '', // id
		'tags'				=> '', // id
		'orderby'			=> 'recent',
		'order'				=> 'DESC',
		'posts_per_page'	=> 10,
		'page_navigation'	=> 'woo_pagination',
		// Custom options
		'main_color'		=> '#ff4800',
		'thumbnail_background_color' => '#ffffff',
		'thumbnail_border_style' => 'none',
		'thumbnail_border_color' => '#e1e1e1',
		'thumbnail_border_width' => '1px',
		'thumbnail_border_radius' => '0px',
		'thumbnail_padding' => '',
		'thumbnail_margin' => '',
		'show_rating' => '1',
		'hover_thumbnail' => '0',
		'el_class'	=> '',
		), $atts) );
		
		$id	= dtwl_woo_get_id();
		$template_part = '';
		$woo_products_pagination = ($page_navigation) ? $page_navigation : 'woo_pagination';
		
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
		
		
		// Hide star-rating
		if( $show_rating == '0' )
			$inline_style .= '#'.$id.'.dtwl-woo .star-rating{display:none;}';
		
		$html .= '<style type="text/css">'.$inline_style.'</style>';
		
		global $woocommerce, $product;
		
		$orderby    		 	= sanitize_title( $orderby );
		$order       			= sanitize_title( $order );
		
		if( is_front_page() ) {
			$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
		} else {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 ;
		}
		
		$query_args = array(
			'paged' 			=> $paged,
			'posts_per_page' 	=> absint($posts_per_page),
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
		$query = new WP_Query( $query_args );
		
		// Class
		$class = 'dtwl-woo dtwl-woo-products woocommerce';
		$class .= esc_attr($el_class);
		ob_start();
		?>
		<div id="<?php echo $id;?>" class="<?php echo $class;?>">
			<?php if( ! empty( $heading ) ):?>
			<h2 class="dtwl-heading"><span><?php echo esc_html($heading);?></span></h2>
			<?php endif; ?>
			<?php 
			if($query->have_posts()):
			?>
			<?php 
			if($switch_template == 'yes'):
				$mode_view = $template;
				if( isset($_COOKIE['dtwl_woo_list_modeview']) ){
					if( $_COOKIE['dtwl_woo_list_modeview'] == 'dtwl-woo-list' ){
						$mode_view = 'list';
					}elseif( $_COOKIE['dtwl_woo_list_modeview'] == 'dtwl-woo-grid' ){
						$mode_view = 'grid';
					}
				}
			?>
			<div class="dtwl-woo-switch_template">
				<a href="#" class="dtwl-woo-mode-view dtwl-woo-grid <?php echo ($mode_view == 'grid') ? 'active' : '';?>" data-mode-view="dtwl-woo-grid" title="<?php esc_attr_e('Grid', DT_WOO_LAYOUTS);?>">
					<i class="fa fa-th"></i>
					<span><?php esc_html_e('Grid', DT_WOO_LAYOUTS);?></span>
				</a>
				<a href="#" class="dtwl-woo-mode-view dtwl-woo-list <?php echo ($mode_view == 'list') ? 'active' : '';?>" data-mode-view="dtwl-woo-list" title="<?php esc_attr_e('List', DT_WOO_LAYOUTS);?>">
					<i class="fa fa-th-list"></i>
					<span><?php esc_html_e('List', DT_WOO_LAYOUTS);?></span>
				</a>
			</div>
			<?php endif; ?>
			<?php $dt_ul_product_class = '';
			if($woo_products_pagination == 'ajax'){
				$dt_ul_product_class = 'loadmore-wrap';
			}elseif($woo_products_pagination == 'infinite_scroll'){
				$dt_ul_product_class = 'infinite-scroll-wrap';
			}
			?>
			<div class="dtwl-woo-prdlist-content">
				<div class="dtwl-woo-content" data-msgtext="<?php esc_attr_e('Loading products', DT_WOO_LAYOUTS);?>" data-finished="<?php esc_attr_e('All products loaded','woow')?>" data-contentselector=".dtwl-woo-content ul.dtwl-woo-products" data-paginate="<?php echo esc_attr($woo_products_pagination) ?>"  data-itemselector=".dtwl-woo-content li.product">
					<div class="dtwl-woo-shop-loop-wrap <?php echo $dt_ul_product_class; ?>">
						<ul id="dtwl-woo-prdlist" class="dtwl-woo-products dtwl-woo-product-list dtwl-woo-<?php echo $mode_view; ?> dtwl-woo-row-fluid" data-maxpage="<?php echo absint($query->max_num_pages);?>">
							<?php
							while ( $query->have_posts() ) : $query->the_post();
								wc_get_template( 'item-content-product.php', array('grid_columns' => absint($col), 'hover_thumbnail' => $hover_thumbnail), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							endwhile;
							?>
						</ul>
						<?php
							/*
							 * Page navigation
							 */
							
							$max_num_pages = $GLOBALS['wp_query']->max_num_pages;
							
							if (!empty($query) ) {
								$max_num_pages = $query->max_num_pages;
							}
							
							// Don't print empty markup if there's only one page.
							if ( $max_num_pages < 2 ) {
								return;
							}else{
								
								switch ($woo_products_pagination){
									case 'ajax':
											?>
											<nav class="dtwl-navigation-ajax" role="navigation">
												<a href="javascript:void(0)"
												data-cat	= "<?php echo esc_attr($categories) ?>"
												data-tags	= "<?php echo esc_attr($tags) ?>"
												data-orderby	= "<?php echo esc_attr($orderby) ?>"
												data-order	= "<?php echo esc_attr($order) ?>"
												data-target ="#<?php echo $id;?> #dtwl-woo-prdlist"
												data-grid-col="<?php echo absint($col); ?>"
												data-hover-thumbnail = "<?php echo esc_attr($hover_thumbnail) ?>"
												data-posts-per-page = "<?php echo absint($posts_per_page) ?>"
												data-offset = "<?php echo absint($posts_per_page) ?>"
												id="dtwl-navigation-ajax" class="dtwl-load-more">
													<span class="dtwl-loadmore-title"><?php echo esc_html__('Load more', DT_WOO_LAYOUTS);?></span>
													<div class="dtwl-navloading"><div class="dtwl-navloader"></div></div>
												</a>
											</nav>
											<?php
										break;
									case 'infinite_scroll':
										wp_enqueue_script('dtwl-woo-vendor-infinitescroll');
										?>
										<nav class="woocommerce-pagination">
											<div class="paginate">
											<?php
												echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
													'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
													'format'       => '',
													'add_args'     => '',
													'current'      => max( 1, get_query_var( 'paged' ) ),
													'total'        => $query->max_num_pages,
													'prev_text'    => '&larr;',
													'next_text'    => '&rarr;',
													'type'         => 'list',
													'end_size'     => 3,
													'mid_size'     => 3
												) ) );
											?>
											</div>
										</nav>
										<?php
										break;
									default:
										?>
										<nav class="dtwl-woocommerce-pagination">
											<?php
												echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
													'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
													'format'       => '',
													'add_args'     => '',
													'current'      => max( 1, get_query_var( 'paged' ) ),
													'total'        => $query->max_num_pages,
													'prev_text'    => '&larr;',
													'next_text'    => '&rarr;',
													'type'         => 'list',
													'end_size'     => 3,
													'mid_size'     => 3
												) ) );
											?>
										</nav>
										<?php
										break;
								}
								
							}// print navigation
						?>
					</div>
				</div>
			</div>
			<?php 
			else:
				wc_get_template( 'loop/no-products-found.php' );
			endif;
			
			?>
		</div>
		<?php
		$html .= ob_get_clean();
		
		wp_reset_postdata(); //wp_reset_query ();
		return $html;
	}
	
	public function dtwl_woosetmodeview(){
		setcookie('dtwl_woo_list_modeview', $_POST['mode'], time()+3600*24*100, '/');
	}
}
new dtwoo_products();