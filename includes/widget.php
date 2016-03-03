<?php
class DT_Widget extends WP_Widget {
	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;
	public $cached = true;
	/**
	 * Constructor
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );
		if($this->cached){
			add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
			add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
			add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		}
	}

	/**
	 * get_cached_widget function.
	 */
	function get_cached_widget( $args ) {

		$cache = wp_cache_get( apply_filters( 'dt_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 * @param string $content
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( apply_filters( 'dt_cached_widget_id', $this->widget_id ), $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'dt_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * Output the html at the start of a widget
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the html at the end of a widget
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_end( $args ) {
		echo $args['after_widget'];
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if ( ! $this->settings ) {
			return $instance;
		}

		foreach ( $this->settings as $key => $setting ) {
				
			if(isset($setting['multiple'])):
			$instance[ $key ] = implode ( ',', $new_instance [$key] );
			else:
			if ( isset( $new_instance[ $key ] ) ) {
				$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
			} elseif ( 'checkbox' === $setting['type'] ) {
				$instance[ $key ] = 0;
			}
			endif;
		}
		if($this->cached){
			$this->flush_widget_cache();
		}

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	public function form( $instance ) {

		if ( ! $this->settings ) {
			return;
		}
		foreach ( $this->settings as $key => $setting ) {
			$value   = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];
			switch ( $setting['type'] ) {
				case "text" :
					?>
				<p>
					<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
				</p>
				<?php
			break;

			case "number" :
				?>
				<p>
					<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
				</p>
				<?php
			break;
			case "select" :
				if(isset($setting['multiple'])):
				$value = explode(',', $value);
				endif;
				?>
				<p>
					<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" <?php if(isset($setting['multiple'])):?> multiple="multiple"<?php endif;?> name="<?php echo $this->get_field_name( $key ); ?><?php if(isset($setting['multiple'])):?>[]<?php endif;?>">
						<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
							<option value="<?php echo esc_attr( $option_key ); ?>" <?php if(isset($setting['multiple'])): selected( in_array ( $option_key, $value ) , true ); else: selected( $option_key, $value ); endif; ?>><?php echo esc_html( $option_value ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<?php
			break;

			case "checkbox" :
				?>
				<p>
					<input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
					<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
				</p>
				<?php
			break;
			}
		}
	}
}

class DT_Woocommerce_Price_Filter extends DT_Widget{
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_layered_nav widget_price_filter dt_woocommerce_price_filter';
		$this->widget_description = __( 'Shows a price filter list in a widget which lets you narrow down the list of shown products when viewing product categories.', DT_WOO_LAYOUTS );
		$this->widget_id          = 'dt_woocommerce_price_filter';
		$this->widget_name        = __( 'DT WooCommerce Price Filter', DT_WOO_LAYOUTS );

		parent::__construct();
	}

	public function update( $new_instance, $old_instance ) {
		$this->init_settings();

		return parent::update( $new_instance, $old_instance );
	}

	public function form( $instance ) {
		$this->init_settings();

		parent::form( $instance );
	}

	public function init_settings() {
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Filter by', 'sitesao' ),
				'label' => __( 'Title', 'sitesao' )
			),'price_range_size' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 50,
				'label' => __( 'Price range size', 'sitesao' )
			),
			'max_price_ranges' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Max price ranges', 'sitesao' )
			),
			'hide_empty_ranges' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Hide empty price ranges', 'sitesao' )
			)
		);
	}

	public function widget( $args, $instance ) {

		global $_chosen_attributes, $wpdb, $wp;

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}
	
		if ( sizeof( WC()->query->unfiltered_product_ids ) == 0 ) {
			return; // None shown - return
		}

		$range_size = absint( $instance['price_range_size'] );
		$max_ranges = ( absint( $instance['max_price_ranges'] ) - 1 );

		$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

		// 		if ( get_option( 'permalink_structure' ) == '' ) {
		// 			$link = remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		// 		} else {
		// 			$link = preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) );
		// 		}

		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( woocommerce_get_page_id('shop') ) ) {
			$link = get_post_type_archive_link( 'product' );
		} else {
			$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
		}

		if ( get_search_query() ) {
			$link = add_query_arg( 's', get_search_query(), $link );
		}

		if ( ! empty( $_GET['post_type'] ) ) {
			//$fields .= '<input type="hidden" name="post_type" value="' . esc_attr( $_GET['post_type'] ) . '" />';
			$link = add_query_arg( 'post_type', esc_attr( $_GET['post_type'] ), $link );
		}

		if ( ! empty ( $_GET['product_cat'] ) ) {
			//$fields .= '<input type="hidden" name="product_cat" value="' . esc_attr( $_GET['product_cat'] ) . '" />';
			$link = add_query_arg( 'product_cat', esc_attr( $_GET['product_cat'] ), $link );
		}

		if ( ! empty ( $_GET['filter_product_brand'] ) ) {
			//$fields .= '<input type="hidden" name="filter_product_brand" value="' . esc_attr( $_GET['filter_product_brand'] ) . '" />';
			$link = add_query_arg( 'filter_product_brand', esc_attr( $_GET['filter_product_brand'] ), $link );
		}

		if ( ! empty( $_GET['product_tag'] ) ) {
			//$fields .= '<input type="hidden" name="product_tag" value="' . esc_attr( $_GET['product_tag'] ) . '" />';
			$link = add_query_arg( 'product_tag', esc_attr( $_GET['product_tag'] ), $link );
		}

		if ( ! empty( $_GET['orderby'] ) ) {
			//$fields .= '<input type="hidden" name="orderby" value="' . esc_attr( $_GET['orderby'] ) . '" />';
			$link = add_query_arg( 'orderby', esc_attr( $_GET['orderby'] ), $link );
		}

		if ( $_chosen_attributes ) {
			foreach ( $_chosen_attributes as $attribute => $data ) {
				if ( $attribute !== 'product_brand' ) {
					$taxonomy_filter = 'filter_' . str_replace( 'pa_', '', $attribute );
						
					//$fields .= '<input type="hidden" name="' . esc_attr( $taxonomy_filter ) . '" value="' . esc_attr( implode( ',', $data['terms'] ) ) . '" />';
					$link = add_query_arg( esc_attr( $taxonomy_filter ), esc_attr( implode( ',', $data['terms'] ) ), $link );
						
					if ( 'or' == $data['query_type'] ) {
						//$fields .= '<input type="hidden" name="' . esc_attr( str_replace( 'pa_', 'query_type_', $attribute ) ) . '" value="or" />';
						$link = add_query_arg( esc_attr( str_replace( 'pa_', 'query_type_', $attribute ) ), 'or', $link );
					}
				}
			}
		}

		if ( 0 === sizeof( WC()->query->layered_nav_product_ids ) ) {
			$min = floor( $wpdb->get_var( "
				SELECT min(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price', '_min_variation_price' ) ) ) ) . "')
				AND meta_value != ''
			" ) );
			$max = ceil( $wpdb->get_var( "
				SELECT max(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
			" ) );
		} else {
			$min = floor( $wpdb->get_var( "
				SELECT min(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price', '_min_variation_price' ) ) ) ) . "')
				AND meta_value != ''
				AND (
					posts.ID IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
					OR (
						posts.post_parent IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
						AND posts.post_parent != 0
					)
				)
			" ) );
			$max = ceil( $wpdb->get_var( "
				SELECT max(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
				AND (
					posts.ID IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
					OR (
						posts.post_parent IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
						AND posts.post_parent != 0
					)
				)
			" ) );
		}

		if ( $min == $max ) {
			return;
		}

		$this->widget_start( $args, $instance );
		echo '<ul>';
		if(apply_filters('dt_woocommerce_price_filter_show_all', false)){
			if ( strlen( $min_price ) > 0 ) {
				echo '<li><a href="' . esc_url( $link ) . '">' . esc_html__( 'All', 'sitesao' ) . '</a></li>';
			} else {
				$url = remove_query_arg( array( 'min_price','max_price'), $link );
				echo '<li class="chosen"><a href="' . esc_url( $link ) . '">' . esc_html__( 'All', 'sitesao' ) . '</a></li>';
			}
		}
		$count = 0;
		for ( $range_min = 0; $range_min < ( $max + $range_size ); $range_min += $range_size ) {
			$range_max = $range_min + $range_size;

			if ( intval( $instance['hide_empty_ranges'] ) ) {
				if ( $min > $range_max || ( $max + $range_size ) < $range_max ) {
					continue;
				}
			}

			$count++;

			$min_price_output = wc_price( $range_min );

			if ( $count == $max_ranges ) {
				$price_output = $min_price_output . '+';

				if ( $range_min != $min_price ) {
					$url = add_query_arg( array( 'min_price' => $range_min, 'max_price' => $max ), $link );
					echo '<li><a href="' . esc_url( $url ) . '">' . $price_output . '</a></li>';
				} else {
					$url = remove_query_arg( array( 'min_price','max_price'), $link );
					echo '<li class="chosen"><a href="' . esc_url( $url ) . '">' . $price_output . '</a></li>';
				}

				break;
			} else {
				$price_output = $min_price_output . ' - ' . wc_price( $range_min + $range_size );

				if ( $range_min != $min_price || $range_max != $max_price ) {
					$url = add_query_arg( array( 'min_price' => $range_min, 'max_price' => $range_max ), $link );
					echo '<li><a href="' . esc_url( $url ) . '">' . $price_output . '</a></li>';
				} else {
					$url = remove_query_arg( array( 'min_price','max_price'), $link );
					echo '<li class="chosen"><a href="' . esc_url( $url ) . '">' . $price_output . '</a></li>';
				}
			}
		}
		echo '</ul>';
		$this->widget_end( $args );

	}
}

class DT_Woocommerce_Product_Sorting extends DT_Widget{
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_layered_nav widget_product_sorting dt_woocommerce_product_sorting';
		$this->widget_description = __( 'Display WooCommerce Product sorting list.', 'sitesao' );
		$this->widget_id          = 'dt_woocommerce_product_sorting';
		$this->widget_name        = __( 'DT WooCommerce Product Sorting', 'sitesao' );

		parent::__construct();
	}

	public function update( $new_instance, $old_instance ) {
		$this->init_settings();

		return parent::update( $new_instance, $old_instance );
	}

	public function form( $instance ) {
		$this->init_settings();

		parent::form( $instance );
	}

	public function init_settings() {
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Filter by', 'sitesao' ),
				'label' => __( 'Title', 'sitesao' )
			),
		);
	}

	public function widget( $args, $instance ) {
		global $_chosen_attributes,$wp_query,$wp;

		if ( 1 == $wp_query->found_posts || ! woocommerce_products_will_display() ) {
			return;
		}


		$this->widget_start( $args, $instance );

		if ( 1 != $wp_query->found_posts || woocommerce_products_will_display() ) {
			echo '<ul>';
			$orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
			$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
			$catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
				'menu_order' => __( 'Default sorting', 'sitesao' ),
				'popularity' => __( 'Sort by popularity', 'sitesao' ),
				'rating'     => __( 'Sort by average rating', 'sitesao' ),
				'date'       => __( 'Sort by newness', 'sitesao' ),
				'price'      => __( 'Sort by price: low to high', 'sitesao' ),
				'price-desc' => __( 'Sort by price: high to low', 'sitesao' )
			) );
				
			if ( !$show_default_orderby ) {
				unset( $catalog_orderby_options['menu_order'] );
			}
				
			if(!apply_filters('dh_woocommerce_product_sorting_show_default', false)){
				unset( $catalog_orderby_options['menu_order'] );
			}
				
			if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
				unset( $catalog_orderby_options['rating'] );
			}
				
				
			foreach ( $catalog_orderby_options as $id => $name ) {
				if ( $orderby == $id ) {
					$link = remove_query_arg( 'orderby' );
					echo '<li class="chosen"><a href="' . esc_url( $link ) . '">' . esc_attr( $name ) . '</a></li>';
				} else {
					$link = add_query_arg( 'orderby', $id );
					echo '<li><a href="' . esc_url( $link ) . '">' . esc_attr( $name ) . '</a></li>';
				}
			}
				
			echo '</ul>';
		}

		$this->widget_end( $args );

	}
}

/**
 * DT_Widget_Products widget class
 */
class DT_Widget_Products extends WP_Widget {

	function __construct(){
		parent::__construct(
			'DT_Widget_Products',
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
function DT_Widget_Register(){
	register_widget('DT_Woocommerce_Price_Filter');
	register_widget('DT_Woocommerce_Product_Sorting');
	register_widget('DT_Widget_Products');
}
add_action('widgets_init', 'DT_Widget_Register');