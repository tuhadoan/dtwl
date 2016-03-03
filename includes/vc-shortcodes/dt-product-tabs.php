 <?php
class dtwoo_tabs{
	
	public function __construct(){
		$this->init();
	}
	
	public function init(){
		add_shortcode('dtwoo_tabs', array(&$this, 'dtwoo_tabs_sc'));
	}
	
	public function dtwoo_tabs_sc($atts, $content){
		$rt = rand().time();
		$html = '';
		
		extract( shortcode_atts(array(
		'heading'			=> '',
		'heading_color'		=> '#363230',
		'heading_font_size'	=> '20px',
		'display_type'		=> '',
		'tabs_left_banner'	=> '',
		'banner_url'		=> '',
		'template'			=> 'grid',
		'query_types'		=> 'category',
		'categories'		=> '', // id
		'tags'				=> '', // id
		'tabs_orderby'		=> 'recent,featured_product,top_rate,best_selling,on_sale',
		'orderby'			=> 'recent',
		'row'				=> 2,
		'col'				=> 4,
		'number_load'		=> 4,
		'number_display'	=> 4,
		'number_limit'		=> 8,
		'speed'				=> 300,
		'dots'				=> 'false',
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
		'loadmore_text'		=> 'Load more',
		'loaded_text'		=> 'All products loaded',
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
				wp_enqueue_style('dtwl-woo-slick');
				wp_enqueue_style('dtwl-woo-slick-theme');
				wp_enqueue_script('dtwl-woo-slick');
				break;
			case 'masonry':
				wp_enqueue_script('dtwl-woo-imagesloaded');
				wp_enqueue_script('dtwl-woo-isotope');
				break;
			default:
				break;
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
		#'.$id.' .dtwl-woo-loadmore-wrap .dtwl-woo-loadmore .dtwl-navloading .dtwl-navloader{
			border-left-color:' . $main_color . ';
		}
		#'.$id.' .dtwl-woo-filter-ajax-loading .dtwl-woo-fade-loading i{
			background: none repeat scroll 0 0 '. $main_color .';
		}
		';
		
		if( $template == 'carousel' ){
			$inline_style .= 
			'#'.$id.' .slick-dots li button:before{color: ' . $main_color . ';}
			
			';
		}
		// Hide star-rating
		if( $show_rating == '0' )
			$inline_style .= '#'.$id.'.dtwl-woo .star-rating{display:none;}';
		
		$html .= '<style type="text/css">'.$inline_style.'</style>';
		
		// Class
		$class = 'dtwl-woo dtwl-woo-product-tabs woocommerce dtwl-woo-template-'.esc_attr($template);
		if( $template == 'carousel' ) $class .= ' dtwl-pre-load';
		$class .= esc_attr($el_class);
		
		$tab_nav_class = '';
		$tab_content_class = '';
		switch ($display_type){
			case 'tabs_left':
				$tab_nav_class = 'dtwl-woo-col-sm-3';
				$tab_content_class = 'dtwl-woo-col-sm-9';
				$class .= ' dtwl-woo-row-fluid';
				break;
			default: break;
		}
			
		// Array tabs title
		$tab_titles = dtwl_get_list_tab_title($query_types, $categories, $tags, $tabs_orderby);
		
		if(empty($tab_titles)){ return;}
		
		// Get products
		if ($template == 'grid' && $display_type == '') :
			$number_query = $row*$col;
		elseif($display_type == 'tabs_left'):
			$number_query = $number_limit; // carousel, masonry limit
		else:
			$number_query = $number_limit; // carousel, masonry limit
		endif;
		
		if ($query_types == 'category'){
			if( !empty($categories) ){
				$tabs = explode(',', $categories);
			}else{
				$tabs = dtwl_get_cats();
			}
		}else if($query_types == 'tags'){
			if( !empty($tags) ){
				$tabs = explode(',', $tags);
			}else{
				$tabs = dtwl_get_tags();
			}
		}else{ // Tab type orderby
			$orderby = '';
			$tabs = explode(',', $tabs_orderby);
		}
		
		ob_start();
		?>
		<div id="<?php echo $id;?>" class="<?php echo $class . ' ' .$display_type;?>">
			<div class="dtwl-nav-tabs-wapper <?php echo $tab_nav_class; ?>">
				<?php if( ! empty( $heading ) ):?>
				<h2 class="dtwl-heading"><?php echo esc_html($heading);?></h2>
				<?php endif; ?>
				<div class="dtwl-woo-filters dtwl-woo-clearfix">
					
					<div class="dtwl-woo-filters-menu">
						<a href="#dtwl-woo-nav-tabs" class="dtwl-inevent-flters"><?php esc_html_e('Categories', DT_WOO_LAYOUTS);?></a>
					</div>
					<ul id="dtwl-woo-nav-tabs" class="dtwl-woo-nav-tabs gfont" data-option-key="filter">
						<?php
						if( $template == 'masonry' ){
								?>
								<li class="dtwl-nav-item first"><a href="#" class="dtwl-woo-masonry-filter" data-option-value="*"><span><?php echo esc_html__('All', DT_WOO_LAYOUTS); ?></span></a></li>
								<?php
							foreach ($tab_titles as $tab) {
								?>
								<li class="dtwl-nav-item"><span>/</span><a href="#" class="dtwl-woo-masonry-filter" data-option-value=".<?php echo esc_attr($tab['name']); ?>"><span><?php echo esc_html($tab['short_title']); ?></span></a></li>
								<?php
								}
								
						}else{ // templage carousel - grid
							$data_tab = array();
							if($query_types !== 'orderby'){
								foreach ($tab_titles as $tab) {
									array_push($data_tab, $tab['name']);
								}
							}
							if(!empty($data_tab)){
								$data_tab = implode(',', $data_tab);
							}else{
								$data_tab = '';
							}
							
							?>
							<li class="dtwl-nav-item first">
								<a href="#" class="tab-intent" title="<?php echo esc_html__('All', DT_WOO_LAYOUTS); ?>"
									data-display_type	= "<?php echo esc_attr($display_type) ?>"
									data-query_types	= "<?php echo esc_attr($query_types) ?>"
									data-tab			= "<?php echo esc_attr($data_tab) ?>"
									data-orderby		= "<?php echo esc_attr($orderby) ?>"
									data-number_query	= "<?php echo esc_attr($number_query) ?>"
									data-number_load	= "<?php echo esc_attr($number_load) ?>"
									data-number_display = "<?php echo esc_attr($number_display) ?>"
									data-template		= "<?php echo esc_attr($template) ?>"
									data-speed			= "<?php echo esc_attr($speed) ?>"
									data-dots			= "<?php echo esc_attr($dots) ?>"
									data-col			= "<?php echo esc_attr($col) ?>"
									data-loadmore_text	= "<?php echo esc_html($loadmore_text) ?>"
									data-loaded_text	= "<?php echo esc_html($loaded_text) ?>"
									data-hover_thumbnail	= "<?php echo esc_attr($hover_thumbnail) ?>"
									><span><?php echo esc_html__('All', DT_WOO_LAYOUTS); ?></span>
								</a>
							</li>
							<?php
							
							foreach ($tab_titles as $tab) {
								?>
								<li class="dtwl-nav-item"><span>/</span>
									<a href="#" class="tab-intent" title="<?php echo esc_attr($tab['title']); ?>"
										data-display_type	= "<?php echo esc_attr($display_type) ?>"
										data-query_types	= "<?php echo esc_attr($query_types) ?>"
										data-tab			= "<?php echo esc_attr($tab['name']) ?>"
										data-orderby		= "<?php echo esc_attr($orderby) ?>"
										data-number_query	= "<?php echo esc_attr($number_query) ?>"
										data-number_load	= "<?php echo esc_attr($number_load) ?>"
										data-number_display = "<?php echo esc_attr($number_display) ?>"
										data-template		= "<?php echo esc_attr($template) ?>"
										data-speed			= "<?php echo esc_attr($speed) ?>"
										data-dots			= "<?php echo esc_attr($dots) ?>"
										data-col			= "<?php echo esc_attr($col) ?>"
										data-loadmore_text	= "<?php echo esc_html($loadmore_text) ?>"
										data-loaded_text	= "<?php echo esc_html($loaded_text) ?>"
										data-hover_thumbnail	= "<?php echo esc_attr($hover_thumbnail) ?>"
										><span><?php echo esc_html($tab['short_title']); ?></span>
									</a>
								</li>
								
								<?php
								}
						}
						
						?>
					</ul>
				</div>
				<?php if($display_type == 'tabs_left' && $tabs_left_banner):?>
				<div class="dtwl-woo-tabs-banner">
					<?php 
					$baner_html = '';
					if( !empty($banner_url) ){
						$baner_html .= '<a href="'.esc_url($banner_url).'" target="blank">';
						$baner_html .= wp_get_attachment_image($tabs_left_banner, 'shop_catalog');
						$baner_html .= '</a>';
					}else{
						$baner_html .= wp_get_attachment_image($tabs_left_banner, 'shop_catalog');
					}
						
					
					echo $baner_html;
					?>
				</div>
				<?php endif; ?>
				
				<?php if($display_type == 'tabs_left'):?>
				<div class="dtwl-next-prev-wrap" data-offset-def="<?php echo esc_attr($number_query) ?>">
					<a href="#" class="dtwl-ajax-prev-page ajax-page-disabled" data-offset="0" data-current-page="1"><i class="fa fa-chevron-left"></i></a>
					<a href="#" class="dtwl-ajax-next-page" data-offset="<?php echo esc_attr($number_query) ?>" data-current-page="1"><i class="fa fa-chevron-right"></i></a>
				</div>
				<?php endif; ?>
			</div> <!-- /.dtwl-nav-tabs-wapper -->
			
			<?php
			
			/*
			 * Product tabs template: grid - carousel
			 */
			if( $template == 'grid' || $template == 'carousel' ):
				?>
				<div class="dtwl-woo-tab-content <?php echo $tab_content_class; ?>">
					<div class="dtwl-woo-filter-ajax-loading">
					<div class="dtwl-woo-fade-loading"><i></i><i></i><i></i><i></i></div>
					</div>
					<div class="dhwl-template-tab-content">
					<?php
						$tab_args = array(
							'display_type'	=> $display_type,
							'query_types'	=> $query_types,
							'tab'			=> $data_tab,
							'orderby'		=> $orderby,
							'number_query'	=> $number_query,
							'number_load'	=> $number_load,
							'number_display'=> $number_display,
							'template'		=> $template,
							'speed'			=> $speed,
							'dots'			=> $dots,
							'col'			=> $col,
							'loadmore_text'	=> esc_html($loadmore_text),
							'loaded_text'	=> esc_html($loaded_text),
							'hover_thumbnail'=> $hover_thumbnail,
						);
						wc_get_template( 'tpl-tab.php', array('tab_args' => $tab_args), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
						
						?>
					</div><!-- /.dhwl-template-tab-content -->
				</div><!-- /.dtwl-woo-tab-content -->
				<script>
					jQuery(document).ready(function($){
						// Only handle preload for template is carousel
						$('#<?php echo $id;?>.dtwl-woo-template-carousel').removeClass('dtwl-pre-load');
						// Tab
						$('#<?php echo $id;?> .dtwl-woo-nav-tabs').find("li").first().addClass("active");
						
						// Handle click
						$('#<?php echo $id;?> .dtwl-woo-nav-tabs > li').click(function(e){
							e.preventDefault();
							if( !$(this).hasClass('active') ){
								id = $(this).find('a').attr('href');
								// Tab
								$('#<?php echo $id;?> .dtwl-woo-nav-tabs li').removeClass('active');
								$(this).addClass('active');
								return false;
							}
						});
				   	});
				</script>
			<?php elseif ($template == 'masonry'): ?>
					<div class="dtwl-woo-mansory-list dtwl-woo-row-fluid dtwl-woo-products dtwl-woo-product-list">
						<?php 
						foreach ($tabs as $tab):
							$loop = dhwl_woo_tabs_query($query_types, $tab, $orderby, $number_query);
							$this->masonry_template($loop, $col, $tab, $hover_thumbnail);
						endforeach;
						?>
					</div>
			<?php endif; ?>
			</div>
			
			<?php
			$html .= ob_get_clean();
// 			wp_reset_postdata();
			
			return $html;
			
	} 
	
	/*
	 *  ================================================
	 *  =============== End Shortcode ==================
	 *  ================================================
	 */
	
	
	public function masonry_template($loop, $col, $tab, $hover_thumbnail){
		while ( $loop->have_posts() ) : $loop->the_post();
			$class = 'dtwl-woo-mansory-item dtwl-woo-item product ';
			
			if ( isset($col) && $col > 0) :
				$column = ($col == 5) ? '15' : absint(12/$col);
				$column2 = absint(12/($col-1));
				$column3 = absint(12/($col-2));
				$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
			endif;
			
			$class .= ' '. $tab;
			?>
			<div class="<?php echo $class; ?>">
				<?php
					wc_get_template( 'item-grid.php', array('hover_thumbnail' => $hover_thumbnail), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
				?>
			</div>
			<?php
    	endwhile;
	}
}
new dtwoo_tabs();