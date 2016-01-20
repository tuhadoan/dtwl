<?php 

class DT_WooCommerce_Layouts{
	
	public function __construct(){
		$this->init();
	}
	
	public function init(){
		
		if( defined('WPB_VC_VERSION') && function_exists('vc_add_param') ){
			$params_script = DT_WOO_LAYOUTS_URL.'assets/js/params.js';
			vc_add_shortcode_param ( 'dtwl_woo_field_id', 'dhwl_woo_setting_field_id');
			
			// Categories
			vc_add_shortcode_param ( 'dtwl_woo_field_categories', 'dtwl_woo_setting_field_categories', $params_script);
			// Tags
			vc_add_shortcode_param ( 'dtwl_woo_field_tags', 'dtwl_woo_setting_field_tags');
			
			// Tags
			vc_add_shortcode_param ( 'dtwl_woo_field_orderby', 'dtwl_woo_setting_field_orderby');
			
			// Custom heading
			vc_add_shortcode_param ( 'dtwl_woo_field_heading', 'dtwl_woo_setting_field_heading');
			
			require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-map.php';
		}
		
		add_shortcode('dt_woolayouts', array( &$this, 'dt_woolayouts_sc' ));
	}
	
	public function dt_woolayouts_sc($atts, $content){
		$rt = rand().time();
		$html = '';
		
		extract( shortcode_atts(array(
		'heading'			=> '',
		'heading_color'		=> '#363230',
		'heading_font_size'	=> '20px',
		'display_type'		=> 'product_tabs',
		'template'			=> 'grid',
		'query_types'		=> 'category',
		'categories'		=> '', // id
		'tags'				=> '', // id
		'list_orderby'		=> 'recent,featured_product,top_rate,best_selling,on_sale',
		'orderby'			=> 'recent',
		'row'				=> 2,
		'col'				=> 4,
		'number_load'		=> 4,
		'number_load'		=> 4,
		'effect_load'		=> 'zoomOut',
		'number_display'	=> 4,
		'number_limit'		=> 10,
		// product list params
		'plist_orderby'		=> 'recent',
		'plist_border'		=> 'no',
		'plist_padding'		=> '0',
		'plist_desc'		=> 'hide',
		'list_limit'		=> 4,
		// product slider params
		'pslides_template'	=> 'def',
		'pslides_centerpadding'	=> '100px',
		'pslides_autoplay'	=> 'true',
		'pslides_speed'		=> 300,
		'pslides_margin'	=> 10,
		'pslides_toshow'	=> 3,
		'pslides_toscroll'	=> 3,
		'pslider_limit'		=> 10,
		'pslides_dots'		=> 'false',
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
		'loadmore_text'		=> 'Load more',
		'loaded_text'		=> 'All ready',
		'loadmore_border_style' => 'solid',
		'loadmore_border_color' => '#eaeaea',
		'loadmore_border_width' => '3px',
		'loadmore_border_radius' => '0px',
		'el_class'	=> '',
		), $atts) );
		
		/*
		 * enqueue library
		 */
		switch ($template){
			case 'carousel':
				wp_enqueue_style('dtwl-woo-owlcarousel');
				wp_enqueue_script('dtwl-woo-owlcarousel');
				break;
			case 'masonry':
				wp_enqueue_script('dtwl-woo-imagesloaded');
				wp_enqueue_script('dtwl-woo-isotope');
				break;
			default:
				break;
		}
		if($display_type == 'product_slider'){
			wp_enqueue_style('dtwl-woo-slick');
			wp_enqueue_style('dtwl-woo-slick-theme');
			wp_enqueue_script('dtwl-woo-slick');
		}
		
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
			
		#'.$id.' .dtwl-woo-loadmore-wrap .dtwl-woo-loadmore{
			border-style:' . $loadmore_border_style . ';
			border-width:' . $loadmore_border_width . ';
			border-color:' . $loadmore_border_color . ';
			border-radius:' . $loadmore_border_radius . ';
			-webkit-border-radius:' . $loadmore_border_radius . ';
		}
				
		#'.$id.' .dtwl-woo-loadmore-wrap .dtwl-woo-loadmore.loaded{
			border-color:' . $main_color . ';
		}
		
		#'.$id.' .dtwl-woo-loadmore-wrap .dtwl-woo-loadmore .dtwl-navloading .dtwl-navloader{
			border-left-color:' . $main_color . ';
		}
		';
		
		// custom product list style
		if( $display_type == 'product_list'){
			if($plist_border == 'yes')
			$inline_style .= '#'.$id.'.template-list .dtwl-woo-list-content{
				border: 1px solid #e8e4e3;
			}';
			$inline_style .= '#'.$id.'.template-list .dtwl-woo-list-content{
				padding: '. $plist_padding .';
			}';
		}
		
		// custom product slider style
		if( $display_type == 'product_slider' && $pslides_template !== 'single_mode' ){
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
		
		if ( $display_type == 'product_tabs' ){
			/* =========================================================================================
			 * Display type Product Tabs
			 * =========================================================================================
			 */
			
			// Class
			$class = 'dtwl-woo dtwl-woo-product-tabs woocommerce dtwl-woo-template-'.esc_attr($template);
			if( $template == 'carousel' ) $class .= ' dtwl-pre-load';
			$class .= esc_attr($el_class);
			
			// Array tabs title
			$tab_titles = dtwl_get_list_tab_title($query_types, $categories, $tags, $list_orderby);
			
			ob_start();
			?>
			<div id="<?php echo $id;?>" class="<?php echo $class;?>">
				<div class="dtwl-nav-tabs-wapper">
					<?php if( ! empty( $heading ) ):?>
					<h2 class="dtwl-heading"><?php echo esc_html($heading);?></h2>
					<?php endif; ?>
					<div class="dtwl-woo-filters dtwl-woo-clearfix">
						<ul class="dtwl-woo-nav-tabs gfont" data-option-key="filter">
							<?php
							if( $template == 'masonry' ){
									?>
									<li class="dtwl-nav-item first"><a href="#" class="dtwl-woo-masonry-filter" data-option-value="*"><span><?php echo esc_html__('All', DT_WOO_LAYOUTS); ?></span></a></li>
									<?php
								foreach ($tab_titles as $tab) {
									?>
									<li class="dtwl-nav-item"><a href="#" class="dtwl-woo-masonry-filter" data-option-value=".<?php echo esc_attr($tab['name']); ?>"><span><?php echo esc_html($tab['short_title']); ?></span></a></li>
									<?php
									}
									
							}else{ // templage carousel - grid - list
								$i = 0;
								foreach ($tab_titles as $tab) {
									$i++;
									if ( $i == 1) $class = 'dtwl-nav-item first';
									else $class = 'dtwl-nav-item';
									?>
									<li class="<?php echo $class; ?>"><a href="#dtwl_pdtabs_<?php echo esc_attr($tab['name']); ?>" title="<?php echo esc_attr($tab['title']); ?>"><span><?php echo esc_html($tab['short_title']); ?></span></a></li>
									
									<?php
									}
							}
							
							?>
						</ul>
					</div>
				</div> <!-- /.dtwl-nav-tabs-wapper -->
				
				<?php
				if ($template == 'grid') :
				$number_query = $row*$col;
				else:
				$number_query = $number_limit; // carousel, masonry limit
				endif;
				/*
				 * Product tabs template: grid - carousel
				 */
				if( $template == 'grid' || $template == 'carousel' ):
					?>
					<div class="dtwl-woo-tab-content">
						<?php
						if ($query_types == 'category') :
							$tag = '';
							if( !empty($categories) ){
								$cats = explode(',', $categories);
							}else{
								$cats = dtwl_get_cats();
							}
							
							foreach ($cats as $cat): 
								if( !empty($categories) ){
									$cat_term = get_term($cat,'product_cat');
									$cat = $cat_term->slug;
								}
								
								$loop = dhwl_woo_query($orderby, $number_query, $cat);
								
							?>
								<div id="dtwl_pdtabs_<?php echo esc_attr($cat); ?>" class="tab-pan fade">
								<?php
									/*
									 * Product tabs grid template
									 */
									if ($template == 'grid') :
										?>
										<div class="dtwl-woo-row-fluid dtwl-woo-products dtwl-woo-product-list grid <?php echo esc_attr($effect_load); ?>">
										<?php
										while ( $loop->have_posts() ) : $loop->the_post();
											$class = 'dtwl-woo-item product';
											
											if ( isset($col) && $col > 0) :
												$column = ($col == 5) ? '15' : absint(12/$col);
												$column2 = absint(12/($col-1));
												$column3 = absint(12/($col-2));
												$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
											endif;
											if ( isset($animate) && $animate) :
											$class .= ' item-animate';
											endif;
											
											?>
											<div class="<?php echo $class; ?>">
												<?php
													wc_get_template( 'item-grid.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
												?>
											</div>
											<?php
								    	endwhile;
								    	?>
								    	</div>
								    	<div class="dtwl-woo-loadmore-wrap">
								    		<?php 
								    		$btn_id = $cat.'_'.$rt;
								    		$btn_type = 'cat';
								    		$content_div = '#dtwl_pdtabs_'.$cat. ' .dtwl-woo-product-list';
								    		$this->load_more_button($btn_id, $content_div, $template_part, $number_load, $number_query, $cat, $tag, $col, $btn_type, $loadmore_text, $loaded_text); ?>
								    	</div>
	    							<?php
							    	/*
							    	 * Product tabs carousel template
							    	 */
								    elseif($template == 'carousel'):
								    ?>
								   		<div class="dtwl-woo-navslider" style="display:none"><span class="prev"><i class="fa fa-chevron-left"></i></span><span class="next"><i class="fa fa-chevron-right"></i></span></div>
								    
								    <?php 
								    	// Carousel template
								    	$this->carousel_template($loop, 'dtwl_pdtabs_'.$cat, $number_display);
								    
								    endif;
							    ?>
								</div>
						    <?php
							endforeach;
						?>
						<?php 
						elseif( $query_types == 'tags' ):
								$cat = '';
								if( !empty($tags)){
									$tags_r = explode(',', $tags);
								}else{
									$tags_r = dtwl_get_tags();
								}
								
								foreach ($tags_r as $tag):
								if($tags){ // has include tags_ids
									$tag_term = get_term($tag,'product_tag');
									$tag = $tag_term->slug;
								}
								
								?>
								<div id="dtwl_pdtabs_<?php echo esc_attr($tag); ?>" class="tab-pan fade">
								<?php
									
									$loop = dhwl_woo_query($query_types, $number_query, $cat, $tag);
									
									if ($template == 'grid') :
										?>
										<div class="dtwl-woo-row-fluid dtwl-woo-products dtwl-woo-product-list grid <?php echo esc_attr($effect_load); ?>">
										<?php
										while ( $loop->have_posts() ) : $loop->the_post();
											$class = 'dtwl-woo-item product';
											
											if ( isset($col) && $col > 0) :
												$column = ($col == 5) ? '15' : absint(12/$col);
												$column2 = absint(12/($col-1));
												$column3 = absint(12/($col-2));
												$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
											endif;
											if ( isset($animate) && $animate) :
											$class .= ' item-animate';
											endif;
											
											?>
											<div class="<?php echo $class; ?>">
												<?php
												wc_get_template( 'item-grid.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
												?>
											</div>
											<?php
								    	endwhile;
								    	?>
								    	</div>
								    	<div class="dtwl-woo-loadmore-wrap">
								    		<?php 
								    		$btn_id = $tag.'_'.$rt;
								    		$btn_type = 'tag';
								    		$content_div = '#dtwl_pdtabs_'.$tag. ' .dtwl-woo-product-list';
								    		$this->load_more_button($btn_id, $content_div, $template_part, $number_load, $number_query, $cat, $tag, $col, $btn_type, $loadmore_text, $loaded_text); ?>
								    	</div>
								    	<?php
								    else:
								    ?>
								   		<div class="dtwl-woo-navslider" style="display:none"><span class="prev"><i class="fa fa-chevron-left"></i></span><span class="next"><i class="fa fa-chevron-right"></i></span></div>
								    <?php
								    	// Carousel template
								    	$this->carousel_template($loop, 'dtwl_pdtabs_'.$tag, $number_display);
								    endif;
							    ?>
								</div>
						    <?php
							endforeach;
						?>
						<?php 
						else: // Tabs type list orderby
							$orderbys = explode(',', $list_orderby); 
							$tag	= '';
							foreach ($orderbys as $orderby): ?>
									<div id="dtwl_pdtabs_<?php echo esc_attr($orderby); ?>" class="tab-pan fade">
								<?php  
									$loop = dhwl_woo_query($orderby, $number_query, $categories);
									
									if ($template == 'grid') :
										?>
										<div class="dtwl-woo-row-fluid dtwl-woo-products dtwl-woo-product-list grid <?php echo esc_attr($effect_load); ?>">
										<?php
										while ( $loop->have_posts() ) : $loop->the_post();
											$class = 'dtwl-woo-item product';
											
											if ( isset($col) && $col > 0) :
												$column = ($col == 5) ? '15' : absint(12/$col);
												$column2 = absint(12/($col-1));
												$column3 = absint(12/($col-2));
												$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
											endif;
											if ( isset($animate) && $animate) :
											$class .= ' item-animate';
											endif;
											
											?>
											<div class="<?php echo $class; ?>">
												<?php
												wc_get_template( 'item-grid.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
												?>
											</div>
											<?php
								    	endwhile;
								    	?>
								    	</div>
								    	<div class="dtwl-woo-loadmore-wrap">
								    		<?php 
								    		$btn_id = $orderby.'_'.$rt;
								    		$btn_type = 'order';
								    		$content_div = '#dtwl_pdtabs_'.$orderby. ' .dtwl-woo-product-list';
								    		$cat = '';
								    		$this->load_more_button($btn_id, $content_div, $template_part, $number_load, $number_query, $cat, $tag, $col, $btn_type, $loadmore_text, $loaded_text); ?>
								    	</div>
								    	<?php
								    else:
								    ?>
								   		<div class="dtwl-woo-navslider" style="display:none"><span class="prev"><i class="fa fa-chevron-left"></i></span><span class="next"><i class="fa fa-chevron-right"></i></span></div>
								    <?php
								    	// Carousel template
								    	$this->carousel_template($loop, 'dtwl_pdtabs_'.$orderby, $number_display);
								    endif;
							    ?>
								</div>
							<?php
							endforeach;
						endif;
						?>
					</div><!-- /.dtwl-woo-tab-content -->
					<script>
						jQuery(document).ready(function($){
							// Only handle preload for template is carousel
							$('#<?php echo $id;?>.dtwl-woo-template-carousel').removeClass('dtwl-pre-load');
							// Tab
							$('#<?php echo $id;?> .dtwl-woo-nav-tabs').find("li").first().addClass("active");
							// Tab content
							$('#<?php echo $id;?> .dtwl-woo-tab-content .tab-pan').css({'overflow':'hidden', 'height':'0'});
							$('#<?php echo $id;?> .dtwl-woo-tab-content').find(".tab-pan").first().addClass("active in").css({'overflow':'', 'height':''});
							// Handle click
							$('#<?php echo $id;?> .dtwl-woo-nav-tabs > li').click(function(e){
								e.preventDefault();
								if( !$(this).hasClass('active') ){
									id = $(this).find('a').attr('href');
									// Tab
									$('#<?php echo $id;?> .dtwl-woo-nav-tabs li').removeClass('active');
									$(this).addClass('active');
									if( id.indexOf('drop_') == 1){
										id = id.replace('drop_', '');
										$('#<?php echo $id;?> .dtwl-woo-nav-tabs li').each(function(){
											if ( $(this).find('a').attr('href') == id ) $(this).addClass('active');
										})	
									}
									
									// Tab content
									$('#<?php echo $id;?> .tab-pan').removeClass('active').removeClass('in').css({'overflow':'hidden', 'height':'0'});
									$('#<?php echo $id;?>').find(id).addClass('active').addClass('in').css({'overflow':'', 'height':''});
									
									// Reset effect
					            	dtwl_effect.resetAnimate($(this));
									return false;
								}
							});
					   	});
					</script>
			<?php elseif ($template == 'masonry'): ?>
					<div class="dtwl-woo-mansory-list dtwl-woo-row-fluid dtwl-woo-products dtwl-woo-product-list grid">
						<?php 
						if ($query_types == 'category') :
							$tag = '';
							if( !empty($categories) ){
								$cats = explode(',', $categories);
							}else{
								$cats = dtwl_get_cats();
							}
							
							foreach ($cats as $cat): 
								if( !empty($categories) ){
									$cat_term = get_term($cat,'product_cat');
									$cat = $cat_term->slug;
								}
								
								$loop = dhwl_woo_query($orderby, $number_query, $cat);
								
								$this->masonry_template($loop, $col, $cat);
							endforeach;
						?>
						<?php  
						elseif( $query_types == 'tags' ): 
							$cat = '';
							if( !empty($tags)){
								$tags_r = explode(',', $tags);
							}else{
								$tags_r = dtwl_get_tags();
							}
							
							foreach ($tags_r as $tag):
								if($tags){ // has include tags_ids
									$tag_term = get_term($tag,'product_tag');
									$tag = $tag_term->slug;
								}
									$loop = dhwl_woo_query($query_types, $number_query, $cat, $tag);
									
									$this->masonry_template($loop, $col, $tag);
							endforeach;
						?>
						<?php 
						else: // Tabs type list orderby
							$orderbys = explode(',', $list_orderby); 
							$tag	= '';
							foreach ($orderbys as $orderby): 
									$loop = dhwl_woo_query($orderby, $number_query, $categories);
							
									$this->masonry_template($loop, $col, $orderby);
							endforeach;
						endif;
						?>
					</div>
			<?php endif; ?>
			</div>
			
			<?php
			$html .= ob_get_clean();
			//wp_reset_postdata();
			
			return $html;
			
		}elseif ($display_type == 'product_list'){
			/* =========================================================================================
			 * Display type Product List
			 * =========================================================================================
			 */
			global $plist_desc_show; 
			$plist_desc_show = $plist_desc;
			
			// Class
			$class = 'dtwl-woo dtwl-woo-product-list woocommerce dtwl-woo-template-list';
			$class .= esc_attr($el_class);
			$loop = dhwl_woo_query($plist_orderby, $list_limit);
			ob_start();
			?>
			<div id="<?php echo $id;?>" class="<?php echo $class;?>">
				<?php if( ! empty( $heading ) ):?>
				<h2 class="dtwl-heading"><span><?php echo esc_html($heading);?></span></h2>
				<?php endif; ?>
				<div class="dtwl-woo-list-content">
					<?php 
					while ( $loop->have_posts() ) : $loop->the_post();
						wc_get_template( 'item-list.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
					endwhile;
					?>
				</div>
			</div>
			<?php
			$html .= ob_get_clean();
			return $html;
			
		}elseif ($display_type == 'product_slider'){
			/* =========================================================================================
			 * Display type Product Slider
			 * =========================================================================================
			 */
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
					if ($query_types == 'category') :
						$tag = '';
						if( !empty($categories) ){
							$cats = explode(',', $categories);
						}else{
							$cats = dtwl_get_cats();
						}
						foreach ($cats as $cat): 
							if( !empty($categories) ){
								$cat_term = get_term($cat,'product_cat');
								$cat = $cat_term->slug;
							}
							
							$loop = dhwl_woo_query($orderby, $number_query, $cat);
							$this->slider_template($loop, $pslides_template);
							
						endforeach;
					?>
					<?php
					// 
					elseif( $query_types == 'tags' ):
								$cat = '';
								if( !empty($tags)){
									$tags_r = explode(',', $tags);
								}else{
									$tags_r = dtwl_get_tags();
								}
								
								foreach ($tags_r as $tag):
									if($tags){ // has include tags_ids
										$tag_term = get_term($tag,'product_tag');
										$tag = $tag_term->slug;
									}
										$loop = dhwl_woo_query($query_types, $number_query, $cat, $tag);
										$this->slider_template($loop, $pslides_template);
										
								endforeach;
					?>
					<?php 
					else: // Tabs type list orderby
						$orderbys = explode(',', $list_orderby); 
						$tag	= '';
						foreach ($orderbys as $orderby): ?>
							<?php  
								$loop = dhwl_woo_query($orderby, $number_query, $categories);
								$this->slider_template($loop, $pslides_template);
						endforeach;
					endif;
					?>
					</div><!-- /.dtwl-woo-slider-content -->
					
					<?php
					$pslides_autoplay 	= ($pslides_autoplay == 'true') ? 'true' : 'false';
					$pslides_toshow		= ((int)$pslides_toshow) ? (int)$pslides_toshow : 3;
					$pslides_toscroll	= ((int)$pslides_toscroll) ? (int)$pslides_toscroll : 3;
					$centerMode			= ($pslides_template == 'center_mode') ? 'true' : 'false';
					$pslides_dots	 	= ($pslides_dots == 'true') ? 'true' : 'false';
					
					switch ($pslides_template){
						case 'slider_syncing':?>
							<script>
							jQuery(document).ready(function(){
								var $this = jQuery('#<?php echo $id ?>.dtwl-woo-product-slider'),
								$container = $this.find('.dtwl-woo-slider-content');
								
								jQuery('.dtwl-woo-slider-for').slick({
								  slidesToShow: 1,
								  slidesToScroll: 1,
								  arrows: false,
								  fade: true,
								  asNavFor: '.dtwl-woo-slider-nav'
								});
								jQuery('.dtwl-woo-slider-nav').slick({
									autoplay: <?php echo esc_attr($pslides_autoplay);?>,
    								slidesToShow: <?php echo intval($pslides_toshow);?>,
    								slidesToScroll: <?php echo intval($pslides_toscroll);?>,
								  	asNavFor: '.dtwl-woo-slider-for',
								  	dots: <?php echo esc_attr($pslides_dots)?>,
								  	centerMode: true,
								  	centerPadding: '100px',
								  	focusOnSelect: true,
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
								  cssEase: 'linear'
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
    									slidesToScroll: <?php echo intval($pslides_toscroll);?>,
    									responsive: [
    									             {
    									               breakpoint: 1024,
    									               settings: {
    									                 slidesToShow: <?php echo intval($pslides_toshow)-1;?>,
    									                 slidesToScroll: <?php echo intval($pslides_toshow)-1;?>,
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
    									                 slidesToScroll: 2,
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
				return $html;
		}
		
	} 
	
	/*
	 *  ================================================
	 *  =============== End Shortcode ==================
	 *  ================================================
	 */
	
	public function load_more_button($id, $content_div, $template_part = 'item-grid', $number_load, $number_query, $cat, $tag, $col, $type = 'cat', $loadmore_text, $loaded_text){
		?>
		<div id="dtwl_woo_loadmore_<?php echo esc_attr($id); ?>" class="dtwl-woo-loadmore"
				data-target="<?php echo esc_attr($content_div); ?>"
				data-template="<?php echo esc_attr($template_part); ?>"
    			data-numberquery="<?php echo esc_attr($number_load);?>"
    			data-start="<?php echo esc_attr($number_query); ?>"
    			data-order="<?php echo esc_attr($cat); ?>"
    			data-cat="<?php echo esc_attr($cat); ?>"
    			data-tag="<?php echo esc_attr($tag); ?>"
    			data-col="<?php echo esc_attr($col); ?>"
    			data-type="<?php echo esc_attr($type); ?>"
    			data-loadtext="<?php echo esc_html($loadmore_text); ?>"
    			data-loadedtext="<?php echo esc_html__($loaded_text); ?>">
    			<span><?php echo esc_html($loadmore_text); ?></span>
    			<div class="dtwl-navloading"><div class="dtwl-navloader"></div></div>
    	</div>
		<?php
	}
	
	public function carousel_template($loop, $id, $number_display){
		?>
    	<ul class="dtwl-woo-products dtwl-woo-product-list grid zoomOut">
    			<?php
			while ( $loop->have_posts() ) : $loop->the_post();
				$class = 'dtwl-woo-item product';
				
				?>
				<li class="<?php echo $class; ?>">
					<?php
					wc_get_template( 'item-carousel.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
					?>
				</li>
				<?php
	    	endwhile;
	    	?>
    	</ul>
    	<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#<?php echo $id; ?> ul').owlCarousel({
					items: <?php echo intval($number_display) ?>,
					responsive : {
					    0 : { items: 1 },
					    480 : { items: 2 },
					    768 : { items: <?php echo intval($number_display)-1 ?> },
					    992 : { items: <?php echo intval($number_display) ?> },
					    1200 : { items: <?php echo intval($number_display) ?> }
					},
					loop:true,
			        dots: false,
				    autoplay: false,
			        onInitialized: callback,
			        slideSpeed : 800
				});
				function callback(event) {
					if(this._items.length > this.options.items){
				        jQuery('#<?php echo $id; ?> .dtwl-woo-navslider').show();
				    }else{
				        jQuery('#<?php echo $id; ?> .dtwl-woo-navslider').hide();
				    }
				}
				jQuery('#<?php echo $id; ?> .dtwl-woo-navslider .prev').on('click', function(e){
					e.preventDefault();
					jQuery('#<?php echo $id; ?>  ul').trigger('prev.owl.carousel');
				});
				jQuery('#<?php echo $id; ?> .dtwl-woo-navslider .next').on('click', function(e){
					e.preventDefault();
					jQuery('#<?php echo $id; ?>  ul').trigger('next.owl.carousel');
				});
			});
		</script>
    	<?php
		}
		
		public function masonry_template($loop, $col, $cat){
			while ( $loop->have_posts() ) : $loop->the_post();
				$class = 'dtwl-woo-mansory-item dtwl-woo-item product ';
				
				if ( isset($col) && $col > 0) :
					$column = ($col == 5) ? '15' : absint(12/$col);
					$column2 = absint(12/($col-1));
					$column3 = absint(12/($col-2));
					$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
				endif;
				
				$class .= ' '. $cat;
				?>
				<div class="<?php echo $class; ?>">
					<?php
						wc_get_template( 'item-grid.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
					?>
				</div>
				<?php
	    	endwhile;
		}
		
		public function slider_template($loop, $pslides_template = 'def'){
			switch ($pslides_template){
				case 'slider_syncing':?>
					<div class="dtwl-woo-slider-for">
					<?php
					while ( $loop->have_posts() ) : $loop->the_post();
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
					while ( $loop->have_posts() ) : $loop->the_post();
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
					while ( $loop->have_posts() ) : $loop->the_post();
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
					while ( $loop->have_posts() ) : $loop->the_post();
					?>
						<div class="dtwl-woo-item product">
							<?php
								wc_get_template( 'item-grid.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</div>
						<?php
			    	endwhile;
					break;
				default:
					break;
			}
		}
//
}

new DT_WooCommerce_Layouts();