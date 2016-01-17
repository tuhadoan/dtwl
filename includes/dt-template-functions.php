<?php
/**
 * DT WooCommerce Layouts Template functions
 *
 * Functions for the templating system.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'dtwl_template_loop_add_action' ) ) {

	/**
	 * Get the add action including add to cart, quick view, compare, wishlist for the loop.
	 *
	 * @subpackage	Loop
	 */
	function dtwl_template_loop_add_action() {
		global $product, $yith_woocompare;
		/**
		 * woocommerce_after_shop_loop_item hook
		 *
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 * @hooked woocommerce_after_shop_loop_item - 15
		 * @hooked dtwl_add_compare_link - 20
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
			add_action('woocommerce_after_shop_loop_item', 'dtwl_add_compare_link', 20);
		}
		
		do_action( 'woocommerce_after_shop_loop_item' );
		
		if( class_exists( 'YITH_WCWL' ) ):
		?>
		<div class="dtwl-woo-add-to-wishlist" style="width: <?php echo 100 / (int)$wishlist; ?>%;">
		<?php 
		echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		?>
		</div>
		<?php
		endif;
	}
}

if( ! function_exists('dtwl_add_compare_link') ){
	function dtwl_add_compare_link(){
		global $product, $yith_woocompare;
		
		if( class_exists( 'YITH_Woocompare' ) ):
		$action_add = 'yith-woocompare-add-product';
		$url_args = array(
			'action' => $action_add,
			'id' => $product->id
		);
		?>
    	<a data-product_id="<?php echo esc_attr( $product->id ); ?>" class="compare button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( $url_args ), $action_add ) ); ?>"><?php esc_html_e('Compare', DT_WOO_LAYOUTS);?></a>
	    
		<?php
		endif;
	}
}
