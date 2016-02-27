<?php
/**
 * The template for displaying product content within loops of DT Products Shortcode
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop, $yith_woocompare;


// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Extra post classes
$classes = array();
$classes[] = 'dtwl-woo-item';
$col = 12/$grid_columns;
$col2 = 12/($grid_columns-1);
$classes[] = 'dtwl-woo-col-sm-'.$col.' dtwl-woo-col-xs-'.$col2.' dtwl-woo-col-phone-12';

?>
<li <?php post_class( $classes ); ?>>
	<div class="dtwl-woo-grid-view">
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
	</div>
	<?php if ( !is_product() && !is_cart() ) : ?>
	<div class="dtwl-woo-list-view">
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
										do_action( 'woocommerce_after_shop_loop_item' ); 
									?>
								</div>
							</div>
						</div>
				</div>
			</div>
	</div>
	<?php endif; ?>
</li>