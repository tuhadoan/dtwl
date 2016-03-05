 <?php
class dtwoo_product_category{
	
	public function __construct(){
		$this->init();
	}
	
	public function init(){
		add_shortcode('dtwoo_product_category', array(&$this, 'dtwoo_product_category_sc'));
	}
	
	public function dtwoo_product_category_sc($atts, $content){
		$rt = rand().time();
		$html = '';
		
		extract( shortcode_atts(array(
		'heading'			=> 'Category',
		'heading_color'		=> '#363230',
		'heading_font_size'	=> '14px',
		'template'			=> 'grid',
		'category'			=> '', // id
		'orderby'			=> 'recent',
		'order'				=> 'DESC',
		'items_to_show'		=> 4,
		'show_cat_thumbnail'=> '1',
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
		
		// Inline style
		$inline_style= '
		#'.$id.' {
			
		}
		
		#'.$id.' .dtwl-woo-heading {
			border-color: '. $main_color .';
		}
				
		#'.$id.' .dtwl-heading {
			color:' . $heading_color . ';
			font-size:' . $heading_font_size . ';
			background-color: '. $main_color .';
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
		#'.$id.' .dtwl-woo-category-thumbnail dtwl-woo-category-title{
			background: '. $main_color .';
		}
		#'.$id.' .dtwl-woo-filter-ajax-loading .dtwl-woo-fade-loading i{
			background: none repeat scroll 0 0 '. $main_color .';
		}
		';
		
		// Hide star-rating
		if( $show_rating == '0' )
			$inline_style .= '#'.$id.'.dtwl-woo .star-rating{display:none;}';
		
		$html .= '<style type="text/css">'.$inline_style.'</style>';
		
		// Class
		$class = 'dtwl-woo dtwl-woo-category woocommerce dtwl-woo-category-'.esc_attr($template);
		
		$class .= esc_attr($el_class);
		
		if( empty($category) ) return;
		
		global $woocommerce,$product,$wp_the_query;
		
		$posts_per_page      	= $items_to_show;
		$orderby    		 	= sanitize_title( $orderby );
		$order       			= sanitize_title( $order );
		
		$query_args = array(
			'posts_per_page' 	=> $posts_per_page,
			'post_status' 	 	=> 'publish',
			'post_type' 	 	=> 'product',
			'order'          	=> $order == 'asc' ? 'ASC' : 'DESC'
		);
		
		$query_args['tax_query'][] =
		array(
			'taxonomy'			=> 'product_cat',
			'field'				=> 'slug',
			'terms'				=> $category,
			'operator'			=> 'IN'
		);
		
		dhwl_woo_query_orderby($orderby);
		
		$query_args = apply_filters('dtwl_woo_query_args', $query_args, $atts, $content);
		$p = new WP_Query( $query_args  );
		
		// get category thumbnail
		$cat_thumbnail = '';
		if($show_cat_thumbnail == '1'){
			$cat = get_term_by('slug', $category, 'product_cat');
			$thumbnail_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
			$cat_thumb = wp_get_attachment_image_src($thumbnail_id, 'shop_catalog');
			if( isset($cat_thumb[0]) && $cat_thumb[0] != '') $cat_thumbnail = '1';
		}
		
		ob_start();
		?>
		<div id="<?php echo $id;?>" class="<?php echo $class;?>">
			<div class="dtwl-woo-heading">
				<h2 class="dtwl-heading"><?php echo esc_html($heading);?></h2>
				<div class="dtwl-next-prev-wrap" data-cat="<?php esc_attr_e($category)?>" data-orderby="<?php esc_attr_e($orderby)?>" data-order="<?php esc_attr_e($order)?>" data-posts-per-page="<?php echo absint($items_to_show);?>" data-hover-thumbnail="<?php echo esc_attr($hover_thumbnail);?>">
					<a href="#" class="dtwl-ajax-prev-page ajax-page-disabled" data-offset="0" data-current-page="1"><i class="fa fa-chevron-left"></i></a>
					<a href="#" class="dtwl-ajax-next-page" data-offset="<?php echo absint($items_to_show);?>" data-current-page="1"><i class="fa fa-chevron-right"></i></a>
				</div>
			</div>
			<?php if($cat_thumbnail == '1'): ?>
			<div class="dtwl-woo-category-thumbnail">
				<img src="<?php echo esc_url($cat_thumb[0])?>" alt="<?php esc_attr_e($heading);?>"/>
			</div>
			<?php endif; ?>
			<div class="dtwl-woo-content">
				<div class="dtwl-woo-filter-ajax-loading">
					<div class="dtwl-woo-fade-loading"><i></i><i></i><i></i><i></i></div>
				</div>
				<div class="dtwl-woo-row-fluid dtwl-woo-products dtwl-woo-product-list dtwl-woo-grid">
					<?php
					while ( $p->have_posts() ) : $p->the_post();
						$class = 'dtwl-woo-item product';
						$column = ($items_to_show == 5) ? '15' : absint(12/$items_to_show);
						$column2 = absint(12/($items_to_show-1));
						$column3 = absint(12/($items_to_show-2));
						$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
				
						?>
						<div class="<?php echo $class; ?>">
							<?php
								wc_get_template( 'item-grid.php', array('hover_thumbnail' => $hover_thumbnail), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</div>
						<?php
			    	endwhile;
			    	?>
			    </div>
		    </div>
		</div>
			
		<?php
		$html .= ob_get_clean();
		wp_reset_postdata();
		return $html;
			
	}
	/*
	 *  ================================================
	 *  =============== End Shortcode ==================
	 *  ================================================
	 */

}
new dtwoo_product_category();