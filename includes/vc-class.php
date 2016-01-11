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
			
			require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-map.php';
		}
		
		add_shortcode('dt_woolayouts', array( &$this, 'dt_woolayouts_sc' ));
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
						dtwl_get_template_part('item', 'carousel');
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
	
	public function dt_woolayouts_sc($atts, $content){
		$rt = rand().time();
		
		extract( shortcode_atts(array(
		'id'				=> 'dtwl_woo_'.$rt,
		'heading'			=> '',
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
		'plist_desc'		=> 'hide',
		'list_limit'		=> 4,
		'el_class'	=> '',
		), $atts) );
		
		
		if ( $display_type == 'product_tabs' ){
			/* =========================================================================================
			 * Display type Product Tabs
			 * =========================================================================================
			 */
			
			// Class
			$class = 'dtwl-woo dtwl-woo-product-tabs woocommerce template-'.esc_attr($template);
			if( $template == 'carousel' ) $class .= ' dtwl-pre-load';
			$class .= esc_attr($el_class);
			
			// Array tabs title
			$tab_titles = dtwl_get_list_tab_title($query_types, $categories, $tags, $list_orderby);
			
			$html = '';
			ob_start();
			?>
			<div id="<?php echo $id;?>" class="<?php echo $class;?>">
				<div class="dtwl-nav-tabs-wapper">
					<h2 class="dtwl-heading"><?php echo esc_html($heading);?></h2>
					
					<div class="dtwl-woo-filters dtwl-woo-clearfix">
						<ul class="dtwl-woo-nav-tabs gfont">
							<?php
							$i = 0;
							foreach ($tab_titles as $tab) {
								$i++;
								if ( $i == 1) $class = 'dtwl-nav-item first';
								else $class = 'dtwl-nav-item';
							?>
							<li class="<?php echo $class; ?>"><a href="#dtwl_pdtabs_<?php echo esc_attr($tab['name']); ?>" title="<?php echo esc_attr($tab['title']); ?>"><span><?php echo esc_html($tab['short_title']); ?></span></a></li>
							
							<?php
							}
							?>
						</ul>
					</div>
				</div> <!-- /.dtwl-nav-tabs-wapper -->
				
				<div class="dtwl-woo-tab-content">
					<?php
					if ($template == 'grid') :
						$number_query = $row*$col;
					else:
						$number_query = $number_limit;
					endif;
					
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
							
						?>
							<div id="dtwl_pdtabs_<?php echo esc_attr($cat); ?>" class="tab-pan fade">
							<?php
								
								$loop = dhwl_woo_query($orderby, $number_query, $cat);
								
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
											dtwl_get_template_part('item', 'grid');
											?>
										</div>
										<?php
							    	endwhile;
							    	?>
							    	</div>
							    	<div class="dtwl-woo-loadmore-wrap">
							    		<div id="dtwl_woo_loadmore_<?php echo $cat.'_'.$rt; ?>" class="dtwl-woo-loadmore"
							    			data-numberquery="<?php echo esc_attr($number_load);?>"
							    			data-start="<?php echo esc_attr($number_query); ?>"
							    			data-order="<?php echo esc_attr($cat); ?>"
							    			data-cat="<?php echo esc_attr($cat); ?>"
							    			data-tag="<?php echo esc_attr($tag); ?>"
							    			data-col="<?php echo esc_attr($col); ?>"
							    			data-type='cat'
							    			data-loadtext="<?php echo esc_html__('Load more', DT_WOO_LAYOUTS); ?>"
							    			data-loadingtext="<?php echo esc_html__('Loading...', DT_WOO_LAYOUTS); ?>"
							    			data-loadedtext="<?php echo esc_html__('All ready', DT_WOO_LAYOUTS); ?>">
							    			<span><?php echo esc_html__('Load more', DT_WOO_LAYOUTS); ?></span>
							    			<div class="dtwl-navloading"><div class="dtwl-navloader"></div></div>
							    		</div>
							    	</div>
							    	<?php
							    else:
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
											dtwl_get_template_part('item', 'grid');
											?>
										</div>
										<?php
							    	endwhile;
							    	?>
							    	</div>
							    	<div class="dtwl-woo-loadmore-wrap">
							    		<div id="dtwl_woo_loadmore_<?php echo $tag.'_'.$rt; ?>" class="dtwl-woo-loadmore"
							    			data-numberquery="<?php echo esc_attr($number_load);?>"
							    			data-start="<?php echo esc_attr($number_query); ?>"
							    			data-order="<?php echo esc_attr($tag); ?>"
							    			data-cat="<?php echo esc_attr($cat); ?>"
							    			data-tag="<?php echo esc_attr($tag); ?>"
							    			data-col="<?php echo esc_attr($col); ?>"
							    			data-type='tag'
							    			data-loadtext="<?php echo esc_html__('Load more', DT_WOO_LAYOUTS); ?>"
							    			data-loadingtext="<?php echo esc_html__('Loading...', DT_WOO_LAYOUTS); ?>"
							    			data-loadedtext="<?php echo esc_html__('All ready', DT_WOO_LAYOUTS); ?>">
							    			<span><?php echo esc_html__('Load more', DT_WOO_LAYOUTS); ?></span>
							    			<div class="dtwl-navloading"><div class="dtwl-navloader"></div></div>
							    		</div>
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
											dtwl_get_template_part('item', 'grid');
											?>
										</div>
										<?php
							    	endwhile;
							    	?>
							    	</div>
							    	<div class="dtwl-woo-loadmore-wrap">
							    		<div id="dtwl_woo_loadmore_<?php echo $orderby.'_'.$rt; ?>" class="dtwl-woo-loadmore"
							    			data-numberquery="<?php echo esc_attr($number_load);?>"
							    			data-start="<?php echo esc_attr($number_query); ?>"
							    			data-order="<?php echo esc_attr($orderby); ?>"
							    			data-cat="<?php echo esc_attr($categories); ?>"
							    			data-tag="<?php echo esc_attr($tag); ?>"
							    			data-col="<?php echo esc_attr($col); ?>"
							    			data-type='order'
							    			data-loadtext="<?php echo esc_html__('Load more', DT_WOO_LAYOUTS); ?>"
							    			data-loadingtext="<?php echo esc_html__('Loading...', DT_WOO_LAYOUTS); ?>"
							    			data-loadedtext="<?php echo esc_html__('All ready', DT_WOO_LAYOUTS); ?>">
							    			<span><?php echo esc_html__('Load more', DT_WOO_LAYOUTS); ?></span>
							    			<div class="dtwl-navloading"><div class="dtwl-navloader"></div></div>
							    		</div>
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
						$('#<?php echo $id;?>.template-carousel').removeClass('dtwl-pre-load');
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
			</div>
			
			<?php
			$html .= ob_get_clean();
			wp_reset_postdata();
			
			return $html;
			
		}elseif ($display_type == 'product_list'){
			/* =========================================================================================
			 * Display type Product List
			 * =========================================================================================
			 */
			global $plist_desc_show; 
			$plist_desc_show = $plist_desc;
			
			$html = '';
			// Class
			$class = 'dtwl-woo dtwl-woo-product-list woocommerce ';
			$class .= esc_attr($el_class);
			$loop = dhwl_woo_query($plist_orderby, $list_limit);
			ob_start();
			?>
			<div id="<?php echo $id;?>" class="<?php echo $class;?>">
				<h2 class="dtwl-heading"><span><?php echo esc_html($heading);?></span></h2>
				<div class="dtwl-woo-list-content">
					<?php 
					while ( $loop->have_posts() ) : $loop->the_post();
						dtwl_get_template_part('item', 'list');
					endwhile;
					?>
				</div>
			</div>
			<?php
			$html = ob_get_clean();
			return $html;
		}
		
	} // End Shortcode
//
}

new DT_WooCommerce_Layouts();