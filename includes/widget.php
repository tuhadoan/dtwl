<?php
/**
 * DTWL_Widget_Products widget class
 */

class DTWL_Widget_Products extends WP_Widget {

	function __construct(){
		parent::__construct(
			'DTWL_Widget_Products',
			esc_html__( 'DT - Products', DT_WOO_LAYOUTS ),
			array( "description" => esc_html__("Display your products on your site.", DT_WOO_LAYOUTS) )
		);
	}
	
	public function get_products( $args, $instance  ){
		
		$number_display = ! empty( $instance['number_display'] ) ? absint( $instance['number_display'] ) : 4;
		$show 			= ! empty( $instance['show'] ) ? sanitize_title( $instance['show'] ) : 'recent';
		
		global $woocommerce;
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => $number_display,
			'post_status' => 'publish',
		);
		switch ($show) {
			case 'best_selling':
				$args['meta_key']='total_sales';
				$args['orderby']='meta_value_num';
				$args['ignore_sticky_posts']   = 1;
				$args['meta_query'] = array();
				$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
				$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
				break;
			case 'featured_product':
				$args['ignore_sticky_posts']=1;
				$args['meta_query'] = array();
				$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
				$args['meta_query'][] = array(
					'key' => '_featured',
					'value' => 'yes'
				);
				$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
				break;
			case 'top_rate':
				add_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
				$args['meta_query'] = array();
				$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
				$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
				break;
			case 'recent':
				$args['meta_query'] = array();
				$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
				break;
			case 'on_sale':
				$args['meta_query'] = array();
				$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
				$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
				$args['post__in'] = wc_get_product_ids_on_sale();
				break;
			case 'recent_review':
				if($post_per_page == -1) $_limit = 4;
				else $_limit = $post_per_page;
				global $wpdb;
				$query = $wpdb->prepare( "
					SELECT c.comment_post_ID
					FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c
					WHERE p.ID = c.comment_post_ID AND c.comment_approved > %d
					AND p.post_type = %s AND p.post_status = %s
					AND p.comment_count > %d ORDER BY c.comment_date ASC" ,
					0, 'product', 'publish', 0 );
					$results = $wpdb->get_results($query, OBJECT);
					$_pids = array();
					foreach ($results as $re) {
					if(!in_array($re->comment_post_ID, $_pids))
						$_pids[] = $re->comment_post_ID;
						if(count($_pids) == $_limit)
							break;
					}
		
				$args['meta_query'] = array();
				$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
				$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
				$args['post__in'] = $_pids;
				break;
		}
		
		return new WP_Query($args);
	}
	
	function widget( $args, $instance ) {
		$output = '';
		$uq = rand().time();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Latest posts',DT_WOO_LAYOUTS );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		
		$output .= $args['before_widget'];
		
		$output .= $args['before_title'] . esc_html($title) . $args['after_title'];

		if( class_exists('WooCommerce') ){
			global $woocommerce;
				$product = $this->get_products($args, $instance);
				if($product->have_posts()):
				ob_start();
				?>
					<div class="dtwl-woo-widget-products">
						<ul class="dtwl-woo-product_list_widget">
						<?php 
						while ( $product->have_posts() ) {
							$product->the_post();
							?>
							<li>
								<?php wc_get_template( 'item-widget.php', array('show_rating' => $instance['show_rating']), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' ); ?>
							</li>
							<?php
						}
						?>
						</ul>
					</div>
				<?php
				$output .= ob_get_clean();
				endif;
			
			wp_reset_postdata();
		}
		
		$output .= $args['after_widget'];

		echo $output;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= esc_attr($new_instance['title']);
		$instance['number_display'] =  absint($new_instance['number_display']) ? 4 : absint($new_instance['number_display']);
		$instance['show'] 			=  sanitize_title($new_instance['show']);
		$instance['show_rating'] 			=  sanitize_title($new_instance['show_rating']);
		
		return $instance;
	}

	function form( $instance ) {
		$title 	 		= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Latest Products';
		$number_display = isset( $instance['number_display'] ) ? esc_attr( $instance['number_display'] ) : '4';
		$show 			= isset( $instance['show'] ) ? esc_attr( $instance['show'] ) : 'recent';
		$show_rating 	= isset( $instance['show_rating'] ) ? esc_attr( $instance['show_rating'] ) : '';
		
?>
		<p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title', DT_WOO_LAYOUTS ); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_html($title); ?>" /></p>
		
		<p><label for="<?php echo esc_attr($this->get_field_id( 'number_display' )); ?>"><?php esc_html_e( 'Number of products to show:', DT_WOO_LAYOUTS ); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'number_display' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number_display' )); ?>" type="text" value="<?php echo esc_html($number_display); ?>" /></p>
			
		<p><label for="<?php echo esc_attr($this->get_field_id( 'show' )); ?>"><?php esc_html_e( 'Show', DT_WOO_LAYOUTS ); ?></label>
			<select  class="widefat" name="<?php echo esc_attr($this->get_field_name( 'show' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'show' )); ?>">
				<option value="recent" <?php selected($show, 'recent', true)?>><?php esc_html_e('Latest Products', DT_WOO_LAYOUTS) ?></option>
				<option value="best_selling" <?php selected($show, 'best_selling', true)?>><?php esc_html_e('BestSeller Products', DT_WOO_LAYOUTS) ?></option>
				<option value="top_rate" <?php selected($show, 'top_rate', true)?>><?php esc_html_e('Top Rated Products', DT_WOO_LAYOUTS) ?></option>
				<option value="on_sale" <?php selected($show, 'on_sale', true)?>><?php esc_html_e('On Sale Products', DT_WOO_LAYOUTS) ?></option>
				<option value="featured_product" <?php selected($show, 'featured_product', true)?>><?php esc_html_e('Featured Products', DT_WOO_LAYOUTS) ?></option>
				<option value="recent_review" <?php selected($show, 'recent_review', true)?>><?php esc_html_e('Recent Review', DT_WOO_LAYOUTS) ?></option>
			</select>
		</p>
		
		<p>
			<input class="checkbox" id="<?php echo esc_attr($this->get_field_id( 'show_rating' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_rating' )); ?>" <?php checked($show_rating, '1', true); ?> type="checkbox" value="1" />	
			<label for="<?php echo esc_attr($this->get_field_id( 'show_rating' )); ?>"><?php esc_html_e( 'Show Rating', DT_WOO_LAYOUTS ); ?></label>
		</p>
<?php
	}
}
/*
 * Register Wiget
*/
function DTWL_Widget_Products_Register(){
	register_widget('DTWL_Widget_Products');
}
add_action('widgets_init', 'DTWL_Widget_Products_Register');