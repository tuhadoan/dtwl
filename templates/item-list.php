<?php
global $product, $plist_desc_show, $woocommerce_loop, $yith_woocompare;
?>
<div class="dtwl-woo-list-item">
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	<div class="dtwl-woo-list-item-wrapper clearfix">
			<div class="dtwl-woo-list-item-thumb">
				<a class="product-image" href="<?php the_permalink(); ?>">
					<?php
						/**
						 * woocommerce_before_shop_loop_item_title hook
						 *
						 * @hooked woocommerce_show_product_loop_sale_flash - 10
						 * @hooked woocommerce_template_loop_product_thumbnail - 10
						 */
						do_action( 'woocommerce_before_shop_loop_item_title' );
					?>
				</a>
			</div>
			
			<div class="dtwl-woo-list-item-info">
				<div class="info-inner">
					<h3 class="item-title"><a class="product-name" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<div class="item-content">
						<?php
							/**
							 * woocommerce_after_shop_loop_item_title hook
							 *
							 * @hooked woocommerce_template_loop_rating - 5
							 * @hooked woocommerce_template_loop_price - 10
							 */
							remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
							remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
							// Re-order
							add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 5);
							add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 10);
							do_action( 'woocommerce_after_shop_loop_item_title' );
						?>
					</div>
				</div>
				<?php if( $plist_desc_show == 'show' ):?>
				<div class="dtwl-woo-desc">
				<?php woocommerce_template_single_excerpt(); ?>
				</div>
				<div class="dtwl-woo-add">
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
				<?php endif; ?>
			</div>
	</div>
</div>