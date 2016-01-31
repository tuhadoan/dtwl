<?php
global $product, $woocommerce_loop, $yith_woocompare;
?>
<div class="dtwl-woo-item-wrapper">
	<div class="dtwl-woo-inner">
		<div class="dtwl-woo-images clearfix">
			<div class="dtwl-woo-img-info">
				<a class="dtwl-woo-product-image" href="<?php the_permalink(); ?>">
					<?php
						/**
						 * woocommerce_before_shop_loop_item_title hook
						 *
						 * @hooked woocommerce_show_product_loop_sale_flash - 10
						 * @hooked woocommerce_template_loop_product_thumbnail - 10
						 */
						if(isset($hover_thumbnail) && $hover_thumbnail === '1'){
							remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
							add_action('woocommerce_before_shop_loop_item_title', 'dtwl_woo_template_loop_product_thumbnail', 10);
						}else{
							remove_action('woocommerce_before_shop_loop_item_title', 'dtwl_woo_template_loop_product_thumbnail', 10);
							add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
						}
						do_action( 'woocommerce_before_shop_loop_item_title' );
					?>
				</a>
				<div class="dtwl-woo-item-box-add">
					<?php 
					$add_to_cart_fa = 'add_to_cart_fa';
					if ( class_exists('YITH_WCQV_Frontend') || isset($yith_woocompare) || class_exists( 'YITH_WCWL' )){
						$add_to_cart_fa = '';
					}
					?>
					<div class="dtwl-woo-add-action <?php echo $add_to_cart_fa; ?>">
						<?php
							/**
							 * woocommerce_after_shop_loop_item hook
							 *
							 * @hooked woocommerce_template_loop_add_to_cart - 10
							 */
							add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
							$wishlist = 2;
							
							if ( class_exists('YITH_WCQV_Frontend') ) {
								add_action('woocommerce_after_shop_loop_item',  array( YITH_WCQV_Frontend::get_instance(), 'yith_add_quick_view_button'), 15);
								$wishlist = $wishlist + 1;
							}
							
							if ( isset($yith_woocompare) ) {
							    remove_action( 'woocommerce_after_shop_loop_item', array( $yith_woocompare->obj, 'add_compare_link' ), 20 );
							    $wishlist = $wishlist + 1;
							    $add_to_cart = '';
							}
							do_action( 'woocommerce_after_shop_loop_item' );
							
							if( class_exists( 'YITH_Woocompare' ) ):
							$action_add = 'yith-woocompare-add-product';
							$url_args = array(
								'action' => $action_add,
								'id' => $product->id
							);
							?>
    			                <a rel="nofollow" data-product_id="<?php echo esc_attr( $product->id ); ?>" class="compare button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( $url_args ), $action_add ) ); ?>"><?php esc_html_e('Compare', DT_WOO_LAYOUTS);?></a>
						    
							<?php
							endif;
							
							if( class_exists( 'YITH_WCWL' ) ):
								
							?>
							<div class="dtwl-woo-add-to-wishlist" style="width: <?php echo 100 / (int)$wishlist; ?>%;">
							<?php 
							echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
							?>
							</div>
							<?php
							endif;
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="dtwl-woo-info">
			<div class="dtwl-woo-info-inner">
				<h3 class="dtwl-woo-title"><a class="product-name" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<div class="dtwl-woo-content dtwl-woo-clearfix">
					<?php
						/**
						 * woocommerce_after_shop_loop_item_title hook
						 *
						 * @hooked woocommerce_template_loop_rating - 5
						 * @hooked woocommerce_template_loop_price - 10
						 */
						do_action( 'woocommerce_after_shop_loop_item_title' );
					?>
				</div>
			</div>
		</div>
	</div>
</div>