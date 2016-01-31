<?php
global $product, $woocommerce_loop;
$showRating = '';
if( isset($show_rating) && $show_rating != '')
	$showRating = $show_rating;
?>
<div>
	<a class="dtwl-woo-product-image" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
		<?php
			dtwl_product_thumbnail();
		?>
		<span class="product-title"><?php the_title(); ?></span>
	</a>
	<?php
		/**
		 * woocommerce_after_shop_loop_item_title hook
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		
		if($showRating == '')
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		do_action( 'woocommerce_after_shop_loop_item_title' );
	?>
</div>
			