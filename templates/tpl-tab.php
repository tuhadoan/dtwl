<?php
/**
 * The template for displaying tab content of Product Tabs shortcode
 *
 */
?>
<?php 
extract($tab_args);
if(isset($hover_thumbnail) && $hover_thumbnail){
	$hover_thumbnail_r = $hover_thumbnail;
}else{
	$hover_thumbnail_r = '0';
}

$offset = (isset($offset) && $offset) ? $offset : 0;
$paged = (isset($paged) && $paged) ? $paged : 1;

?>
<div class="dhwl-template-tab">
<?php 
	$loop = dhwl_woo_tabs_query($query_types, $tab, $orderby, $number_query, $offset, $paged);
	$idx = 0;
    if( $loop->have_posts() ):
        	if ($template == 'grid') :
        		?>
        		<div class="dtwl-woo-row-fluid dtwl-woo-products dtwl-woo-product-list dtwl-woo-grid">
					<?php
					while ( $loop->have_posts() ) : $loop->the_post();
						$idx = $idx + 1;
						$class = 'dtwl-woo-item product';
						
						if ( isset($col) && $col > 0) :
							$column = ($col == 5) ? '15' : absint(12/$col);
							$column2 = absint(12/($col-1));
							$column3 = absint(12/($col-2));
							$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
						endif;
						if ( isset($eclass) && $eclass){
							$class .= ' '.$eclass;
						}
						
						?>
						<div class="<?php echo $class; ?>">
							<?php
								wc_get_template( 'item-grid.php', array('hover_thumbnail' => $hover_thumbnail_r), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</div>
						<?php
			    	endwhile;
			    	if($idx < $number_query){
			    		// there are no more product
			    		// print a flag to detect
			    		echo '<div id="dtwl-ajax-no-products" class=""><!-- --></div>';
			    	}
			    	?>
			    </div>
			    <?php if($display_type != 'tabs_left'):?>
            	<div class="dtwl-woo-loadmore-wrap">
            		<div class="dtwl-woo-loadmore"
							data-query-types ="<?php echo esc_attr($query_types);?>"
							data-tab ="<?php echo esc_attr($tab);?>"
							data-orderby ="<?php echo esc_attr($orderby);?>"
			    			data-number-load ="<?php echo esc_attr($number_load);?>"
			    			data-start ="<?php echo esc_attr($number_query); ?>"
			    			data-col ="<?php echo esc_attr($col); ?>"
			    			data-loadtext ="<?php echo esc_html($loadmore_text); ?>"
			    			data-loadedtext ="<?php echo esc_html__($loaded_text); ?>">
			    			<span><?php echo esc_html($loadmore_text); ?></span>
			    			<div class="dtwl-navloading"><div class="dtwl-navloader"></div></div>
			    	</div>
            	</div>
            	<?php endif; ?>
            	<?php
            else: // Carousel?>
        		<ul class="dtwl-woo-products dtwl-woo-product-list">
    			<?php
					while ( $loop->have_posts() ) : $loop->the_post();
						$class = 'dtwl-woo-item product';
						
						?>
						<li class="<?php echo $class; ?>">
							<?php
							wc_get_template( 'item-carousel.php', array('hover_thumbnail' => $hover_thumbnail_r), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
							?>
						</li>
						<?php
			    	endwhile;
			    	?>
		    	</ul>
		    	<script type="text/javascript">
			    	jQuery(document).ready(function(){
						var $container = jQuery('#dtwl_pdtabs_<?php echo $tab; ?> ul');
						
						jQuery($container).slick({
							dots: <?php echo esc_attr($dots)?>,
							infinite: true,
							speed: <?php echo esc_attr($speed)?>,
							adaptiveHeight: true,
							autoplay: false,
							centerMode: false,
							slidesToShow: <?php echo intval($number_display);?>,
							slidesToScroll: 1,
							nextArrow: '<div class="dtwl-woo-navslider"><span class="next"><i class="fa fa-chevron-right"></i></span></div>',
							prevArrow: '<div class="dtwl-woo-navslider"><span class="prev"><i class="fa fa-chevron-left"></i></span></div>',
							responsive: [
							             {
							               breakpoint: 1024,
							               settings: {
							                 slidesToShow: <?php echo intval(3)-1;?>,
							                 slidesToScroll: 1,
							                 infinite: true,
							                 dots: <?php echo esc_attr($dots)?>
							               }
							             },
							             {
							               breakpoint: 600,
							               settings: {
							                 slidesToShow: 2,
							                 slidesToScroll: 1,
							                 infinite: false,
							                 dots: false
							               }
							             },
							             {
							               breakpoint: 480,
							               settings: {
							                 slidesToShow: 1,
							                 slidesToScroll: 1,
							                 infinite: false,
							                 dots: false
							               }
							             }
							           ]
						});
	
					});
		    	</script>
        	<?php
        	endif;
    else:
        wc_get_template( 'loop/no-products-found.php' );
    endif;
    
    ?>
</div>