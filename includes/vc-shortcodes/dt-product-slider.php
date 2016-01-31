<?php
class dtwoo_slider{
	
	public function __construct(){
		$this->init();
	}
	
	public function init(){
		add_shortcode('dtwoo_slider', array(&$this, 'dtwoo_slider_sc'));
	}
	
	public function dtwoo_slider_sc($atts, $content){
		wp_enqueue_style('dtwl-woo-slick');
		wp_enqueue_style('dtwl-woo-slick-theme');
		wp_enqueue_script('dtwl-woo-slick');
		
		$rt = rand().time();
		$html = '';
		
		extract( shortcode_atts(array(
		'heading'			=> '',
		'heading_color'		=> '#363230',
		'heading_font_size'	=> '20px',
		'categories'		=> '', // id
		'tags'				=> '', // id
		'orderby'			=> 'recent',
		'order' 			=> 'ASC',
		// product slider params
		'pslides_template'	=> 'def',
		'pslides_centerpadding'	=> '100px',
		'pslides_autoplay'	=> 'true',
		'pslides_speed'		=> 300,
		'pslides_margin'	=> 10,
		'pslides_toshow'	=> 5,
		'pslider_limit'		=> 15,
		'pslides_dots'		=> 'false',
		// Custom options
		'main_color'		=> '#ff4800',
		'hover_thumbnail'	=> '0',
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
		
		// custom product slider style
		if( $pslides_template !== 'single_mode' ){
			$pslides_margin = ((int)$pslides_margin) ? (int)$pslides_margin : 10;
			$inline_style .= '#'.$id.'.dtwl-woo-product-slider .slick-slider{
				margin-left: -'. $pslides_margin .'px;
				margin-right: -'. $pslides_margin .'px;
			}';
			$inline_style .= '#'.$id.'.dtwl-woo-product-slider .slick-slider .slick-slide{
				margin-left: '. $pslides_margin .'px;
				margin-right: '. $pslides_margin .'px;
			}';
		}
		
		
		// Hide star-rating
		if( $show_rating == '0' )
			$inline_style .= '#'.$id.'.dtwl-woo .star-rating{display:none;}';
		
		$html .= '<style type="text/css">'.$inline_style.'</style>';
		
		$number_query = $pslider_limit;
		// Class
		$class = 'dtwl-woo dtwl-woo-product-slider woocommerce template-slider dtwl-woo-' .$pslides_template;
		$class .= ' dtwl-pre-load';
		$class .= esc_attr($el_class);
		ob_start();
		?>
		<div id="<?php echo $id;?>" class="<?php echo $class;?>">
			<?php if( ! empty( $heading ) ):?>
			<h2 class="dtwl-heading"><?php echo esc_html($heading);?></h2>
			<?php endif; ?>
			
			<div class="dtwl-woo-slider-content dtwl-woo-products">
				<?php
				global $woocommerce,$product,$wp_the_query;
				$posts_per_page      	= $pslider_limit ;
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
				
				$this->slider_template($p, $pslides_template, $hover_thumbnail);
				
				?>
				</div><!-- /.dtwl-woo-slider-content -->
				
				<?php
				$pslides_autoplay 	= ($pslides_autoplay == 'true') ? 'true' : 'false';
				$pslides_toshow		= ((int)$pslides_toshow) ? (int)$pslides_toshow : 3;
				$centerMode			= ($pslides_template == 'center_mode') ? 'true' : 'false';
				$pslides_dots	 	= ($pslides_dots == 'true') ? 'true' : 'false';
				
				switch ($pslides_template){
					case 'slider_syncing':?>
						<script>
						jQuery(document).ready(function(){
							var $this = jQuery('#<?php echo $id ?>.dtwl-woo-product-slider');
							
							jQuery( '#<?php echo $id ?> .dtwl-woo-slider-for').slick({
							  slidesToShow: 1,
							  slidesToScroll: 1,
							  arrows: false,
							  fade: true,
							  asNavFor: '#<?php echo $id ?>  .dtwl-woo-slider-nav'
							});
							jQuery('#<?php echo $id ?> .dtwl-woo-slider-nav').slick({
								autoplay: <?php echo esc_attr($pslides_autoplay);?>,
    							slidesToShow: <?php echo intval($pslides_toshow);?>,
    							slidesToScroll: 1,
							  	asNavFor: '#<?php echo $id ?> .dtwl-woo-slider-for',
							  	dots: <?php echo esc_attr($pslides_dots)?>,
							  	centerMode: true,
							  	centerPadding: '100px',
							  	focusOnSelect: true,
							  	nextArrow: '<div class="dtwl-woo-navslider"><span class="next"><i class="fa fa-chevron-right"></i></span></div>',
								prevArrow: '<div class="dtwl-woo-navslider"><span class="prev"><i class="fa fa-chevron-left"></i></span></div>',
							  	responsive: [
											{
											    breakpoint: 480,
											    settings: {
											      slidesToShow: 3,
											      slidesToScroll: 1,
											      centerMode: false,
											      arrows: false,
											      infinite: false,
											      dots: false
											    }
											  }
											]
							});
							
							jQuery($this).removeClass('dtwl-pre-load');
						});
						</script>
						<?php
						break;
					case 'single_mode':
						?>
						<script>
						jQuery(document).ready(function(){
							var $this = jQuery('#<?php echo $id?>.dtwl-woo-product-slider'),
							$container = $this.find('.dtwl-woo-slider-content');
							
							jQuery($container).slick({
							  dots: <?php echo esc_attr($pslides_dots)?>,
							  infinite: true,
							  autoplay: <?php echo esc_attr($pslides_autoplay);?>,
							  speed: <?php echo intval($pslides_speed);?>,
							  fade: true,
							  cssEase: 'linear',
							  nextArrow: '<div class="dtwl-woo-navslider"><span class="next"><i class="fa fa-chevron-right"></i></span></div>',
							  prevArrow: '<div class="dtwl-woo-navslider"><span class="prev"><i class="fa fa-chevron-left"></i></span></div>',
							});

							jQuery($this).removeClass('dtwl-pre-load');
						});
						</script>
						<?php
						break;
					case 'def' || 'center_mode':
						?>
	    				<script>
    							jQuery(document).ready(function(){
    								var $this = jQuery('#<?php echo $id?>.dtwl-woo-product-slider'),
    								$container = $this.find('.dtwl-woo-slider-content');
    								
    								jQuery($container).slick({
    									dots: <?php echo esc_attr($pslides_dots)?>,
    									infinite: true,
    									speed: <?php echo intval($pslides_speed);?>,
    									adaptiveHeight: true,
    									centerMode: <?php echo esc_attr($centerMode);?>,
    									centerPadding: '<?php echo esc_attr($pslides_centerpadding)?>',
    									autoplay: <?php echo esc_attr($pslides_autoplay);?>,
    									slidesToShow: <?php echo intval($pslides_toshow);?>,
    									slidesToScroll: 1,
    									nextArrow: '<div class="dtwl-woo-navslider"><span class="next"><i class="fa fa-chevron-right"></i></span></div>',
    									prevArrow: '<div class="dtwl-woo-navslider"><span class="prev"><i class="fa fa-chevron-left"></i></span></div>',
    									responsive: [
    									             {
    									               breakpoint: 1024,
    									               settings: {
    									                 slidesToShow: <?php echo intval($pslides_toshow)-1;?>,
    									                 slidesToScroll: 1,
    									                 centerMode: <?php echo esc_attr($centerMode);?>,
    								    				 centerPadding: '40px',
    									                 infinite: true,
    									                 dots: <?php echo esc_attr($pslides_dots)?>
    									               }
    									             },
    									             {
    									               breakpoint: 600,
    									               settings: {
    									                 slidesToShow: 2,
    									                 slidesToScroll: 1,
    									                 centerMode: <?php echo esc_attr($centerMode);?>,
    	    								    		 centerPadding: '40px',
    									                 infinite: false,
    									                 dots: false
    									               }
    									             },
    									             {
    									               breakpoint: 480,
    									               settings: {
    									                 slidesToShow: 1,
    									                 slidesToScroll: 1,
    									                 centerMode: <?php echo esc_attr($centerMode);?>,
    	    								    		 centerPadding: '40px',
    									                 infinite: false,
    									                 dots: false
    									               }
    									             }
    									           ]
    								});
    	
    								jQuery($this).removeClass('dtwl-pre-load');
    							});
		    				</script>
						<?php
						break;
					default:
						break;
				}
				?>
			<?php
			$html .= ob_get_clean();
			wp_reset_postdata ();
			return $html;
		}
		
		public function slider_template($product, $pslides_template = 'def', $hover_thumbnail){
			switch ($pslides_template){
				case 'slider_syncing':?>
					<div class="dtwl-woo-slider-for">
					<?php
					while ( $product->have_posts() ) : $product->the_post();
					?>	
						<div class="dtwl-woo-item product">
							<?php
								wc_get_template( 'item-slider-syncing.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</div>
						<?php
			    	endwhile;
			    	?>
			    	</div>
					<div class="dtwl-woo-slider-nav">
					<?php
					while ( $product->have_posts() ) : $product->the_post();
					?>
						<div>
							<?php
								wc_get_template( 'item-slider-syncing-nav.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</div>
						<?php
			    	endwhile;
			    	?>
			    	</div>
			    	<?php
					break;
				case 'single_mode':
					while ( $product->have_posts() ) : $product->the_post();
					?>
						<div class="dtwl-woo-item product">
							<?php
								wc_get_template( 'item-slider-single.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</div>
						<?php
			    	endwhile;
					break;
				case 'def' || 'center_mode':
					while ( $product->have_posts() ) : $product->the_post();
					?>
						<div class="dtwl-woo-item product">
							<?php
								wc_get_template( 'item-grid.php', array('hover_thumbnail' => $hover_thumbnail), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</div>
						<?php
			    	endwhile;
					break;
				default:
					break;
			}
		}
}
new dtwoo_slider();