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

if ( ! function_exists( 'dtwl_woo_template_loop_product_thumbnail' ) ) {

	/**
	 * Get the product thumbnail for the loop.
	 *
	 * @subpackage	Loop
	 */
	function dtwl_woo_template_loop_product_thumbnail() {
		echo dtwl_woo_get_product_thumbnail();
		echo dtwl_woo_get_product_first_thumbnail();
	}
}

if ( ! function_exists( 'dtwl_woo_get_product_thumbnail' ) ) {

	/**
	 * Get the product thumbnail, or the placeholder if not set.
	 *
	 * @subpackage	Loop
	 * @param string $size (default: 'shop_catalog')
	 * @param int $deprecated1 Deprecated since WooCommerce 2.0 (default: 0)
	 * @param int $deprecated2 Deprecated since WooCommerce 2.0 (default: 0)
	 * @return string
	 */
	function dtwl_woo_get_product_thumbnail( $size = 'shop_catalog', $deprecated1 = 0, $deprecated2 = 0 ) {
		global $post;
		$html = '<div class="dtwl-woo-product-thumbnail dtwl-woo-product-front-thumbnail">';
		if ( has_post_thumbnail() ) {
			$html .=  get_the_post_thumbnail( $post->ID, $size );
		} elseif ( wc_placeholder_img_src() ) {
			$html .=  wc_placeholder_img( $size );
		}
		$html .= '</div>';
		return $html;
	}
}
if ( ! function_exists( 'dtwl_woo_get_product_first_thumbnail' ) ) {
	function dtwl_woo_get_product_first_thumbnail( $size = 'shop_catalog', $deprecated1 = 0, $deprecated2 = 0 ) {
		global $post, $product;
		$html = '';
		$attachment_ids = $product->get_gallery_attachment_ids();
		$i = 0;
		
		if($attachment_ids){
			$i++;
			foreach ( $attachment_ids as $attachment_id ) {
				$image_link = wp_get_attachment_url( $attachment_id );
				
				if ( ! $image_link )
					continue;
				
				$image_title 	= esc_attr( get_the_title( $attachment_id ) );
				
				$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', $size ), 0, $attr = array(
					'title'	=> $image_title,
					'alt'	=> $image_title
				) );
				
				$html = '<div class="dtwl-woo-product-thumbnail dtwl-woo-product-back-thumbnail">';
				$html .= $image;
				$html .= '</div>';
				
				if($i == 1)
					break;
			}
		}
		return $html;
	}
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
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
		add_action('woocommerce_after_shop_loop_item', 'dtwl_template_loop_add_to_cart', 10);
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

if( ! function_exists('dtwl_product_thumbnail') ){
	function dtwl_product_thumbnail(){
	    global $post;
	    $size = 'shop_thumbnail';
	    if ( has_post_thumbnail() ) {
	        echo get_the_post_thumbnail( $post->ID, $size, array('class' => "attachment-$size") );
	    } elseif ( wc_placeholder_img_src() ) {
	        echo wc_placeholder_img( $size );
	    }
	}
}


if( ! function_exists('dtwl_template_loop_add_to_cart') ){
	function dtwl_template_loop_add_to_cart(){
		global $product;
		
		$add_class = 'button ';
		if( $product->is_type( 'simple' ) ){
		  // a simple product
			$add_class .= 'product_type_simple add_to_cart_button ajax_add_to_cart ';
		} elseif( $product->is_type( 'variable' ) ){
		  // a variable product
			$add_class .= 'product_type_variable add_to_cart_button ';
		}
		
		echo apply_filters( 'woocommerce_loop_add_to_cart_link',
			sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $class ) ? $class : $add_class ),
				esc_html( $product->add_to_cart_text() )
			),
		$product );
	}
}

if( ! function_exists('dt_is_ajax') ){
	function dt_is_ajax(){
		if ( defined( 'DOING_AJAX' ) ) {
			return true;
		}
	
		return ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) ? true : false;
	
	}
}

if( ! function_exists('dtwl_template_toolbar') ){
	function dtwl_template_toolbar(){
	?>
	<div class="dtwl-toolbar">
		<?php
		if(is_dynamic_sidebar('dt-filter-sidebar')):
			$sidebar_widgets = wp_get_sidebars_widgets();
			$count = count( (array) $sidebar_widgets['dt-filter-sidebar'] );
			$count = absint($count);
			?>
			<div class="dt-filter-toogle-button">
				<a href="#" class="dt-filter" title="<?php esc_html_e('Filter', DT_WOO_LAYOUTS)?>">
					<i class="fa fa-filter"></i><?php esc_html_e('Filter', DT_WOO_LAYOUTS)?>
				</a>
			</div>
			<div class="dtwl-woo-sidebar-shop-filter" data-toggle="dtwl-shop-filter-ajax">
				<div class="dtwl-woo-sidebar-shop-filter-wrap dtwl-woo-filter-sidebar-<?php echo esc_attr($count);?>">
					<?php dynamic_sidebar('dt-filter-sidebar');?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
	}
}