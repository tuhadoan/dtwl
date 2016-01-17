<?php
global $product, $woocommerce_loop, $yith_woocompare;
?>
<div class="dtwl-woo-item-wrapper">
	<div class="dtwl-woo-inner">
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
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
							do_action('dtwl_template_loop_item_action');
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