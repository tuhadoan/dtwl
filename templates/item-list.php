<?php
global $product, $plist_desc_show, $woocommerce_loop, $yith_woocompare;
if(isset($desc_show) && $desc_show)
	$plist_desc_show = $desc_show;
?>
<div class="dtwl-woo-list-item">
	<div class="dtwl-woo-list-item-wrapper clearfix">
			<div class="dtwl-woo-list-item-thumb">
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
			</div>
			
			<div class="dtwl-woo-list-item-info">
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
				<?php if( $plist_desc_show == 'show' ):?>
				<div class="dtwl-woo-desc">
				<?php woocommerce_template_single_excerpt(); ?>
				</div>
				<div class="dtwl-woo-list-item-box-add">
					<div class="dtwl-woo-add-action">
						<?php
							/**
							 * woocommerce_after_shop_loop_item hook
							 *
							 * @hooked woocommerce_template_loop_add_to_cart - 10
							 */
							add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
							do_action( 'woocommerce_after_shop_loop_item' ); 
						?>
					</div>
				</div>
				<?php endif; ?>
			</div>
	</div>
</div>