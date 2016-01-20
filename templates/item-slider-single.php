<?php
global $product;
?>
<div class="dtwl-woo-slide-single">
	<div class="dtwl-woo-slide-item-wrapper clearfix">
			<div class="dtwl-woo-slide-item-thumb">
				<a class="dtwl-woo-product-image" href="<?php the_permalink(); ?>">
					<?php
						add_action('dtwl_woo_product_thumbnail', 'woocommerce_template_loop_product_thumbnail', 10);
						do_action( 'dtwl_woo_product_thumbnail' );
					?>
				</a>
				<h3 class="dtwl-woo-title"><a class="product-name" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			</div>
	</div>
</div>