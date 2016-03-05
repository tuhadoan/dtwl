<?php
/*
Plugin Name: DT WooCommerce Product Layouts
Plugin URI: http://dawnthemes.com/
Description: Display Product with multi layout for WooCommerce.
Version: 1.0
Author: DawnThemes 
Author URI: http://dawnthemes.com/
Copyright @2016 by DawnThemes
License: License GNU General Public License version 2 or later
Text-domain: dt_woo_layouts
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Current DT WooCommerce Product Layouts version
 */
if ( ! defined( 'DTWL_VERSION' ) ) {
	/**
	 *
	 */
	define( 'DTWL_VERSION', '1.0' );
}

if ( ! defined( 'DT_WOO_LAYOUTSL' ) )
	define( 'DT_WOO_LAYOUTS' , 'dt_woo_layouts');

if ( ! defined( 'DT_WOO_LAYOUTS_URL' ) )
	define( 'DT_WOO_LAYOUTS_URL' , plugin_dir_url(__FILE__));

if ( ! defined( 'DT_WOO_LAYOUTS_DIR' ) )
	define( 'DT_WOO_LAYOUTS_DIR' , plugin_dir_path(__FILE__));

require_once DT_WOO_LAYOUTS_DIR . 'includes/functions.php';

/*
 * Check is active require plugin
 */
if( ! function_exists('dtwl_is_active') ){
	function dtwl_is_active(){
		$active_plugins = (array) get_option( 'active_plugins' , array() );
		
		if( is_multisite() )
			$active_plugins = array_merge($active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		
		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}
}

// DT Widget
if( dtwl_is_active() ){
	include_once plugin_dir_path(__FILE__). '/includes/widget.php';
}

class DT_WL_Manager{
	
	public function __construct(){
		add_action('init', array(&$this, 'init'));
		add_action( 'after_setup_theme', array( &$this, 'include_template_functions' ), 11 );
		
		add_action('wp_head', array($this, 'dtwl_renderurlajax'), 15);
		/*
		 * Ajax Product Tabs shortcode
		 */
		// Ajax load more
		add_action('wp_ajax_dtwl_wooloadmore', array($this, 'dtwl_woo_loadmore'));
		add_action('wp_ajax_nopriv_dtwl_wooloadmore', array($this, 'dtwl_woo_loadmore'));
		// Ajax tab load more
		add_action('wp_ajax_dtwl_wootabloadproducts', array($this, 'dtwl_woo_tab_load_products'));
		add_action('wp_ajax_nopriv_dtwl_wootabloadproducts', array($this, 'dtwl_woo_tab_load_products'));
		// Ajax tab load more
		add_action('wp_ajax_dtwl_wootabnextprevpage', array($this, 'dtwl_woo_tab_next_prev_page'));
		add_action('wp_ajax_nopriv_dtwl_wootabnextprevpage', array($this, 'dtwl_woo_tab_next_prev_page'));
		
		/*
		 * Ajax Products shortcode
		 */
		// Ajax products navigation load more
		add_action('wp_ajax_dtwl_wooproductsloadmore', array($this, 'dtwl_wooproducts_loadmore'));
		add_action('wp_ajax_nopriv_dtwl_wooproductsloadmore', array($this, 'dtwl_wooproducts_loadmore'));
		
		// Product category shortcode
		add_action('wp_ajax_dtwl_category_nav', array($this, 'dtwl_category_nav'));
		add_action('wp_ajax_nopriv_dtwl_category_nav', array($this, 'dtwl_category_nav'));
		
	}
	
	public function init(){
		load_plugin_textdomain( DT_WOO_LAYOUTS , false, basename(DT_WOO_LAYOUTS_DIR) . '/languages');
		
		// Register library
		wp_register_style('dtwl-woo-chosen', DT_WOO_LAYOUTS_URL .'assets/css/chosen.min.css');
		
		// require woocommerce
		if( !dtwl_is_active() ){
			add_action('admin_notices', array(&$this, 'woocommerce_notice'));
		}
		
		if(is_admin()){
			include_once DT_WOO_LAYOUTS_DIR . 'includes/admin.php';
		}else{
			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
			add_action('wp_enqueue_scripts',array(&$this,'enqueue_scripts'));
		}
		
		// require vc
		if(!defined('WPB_VC_VERSION')){
			add_action('admin_notices', array(&$this, 'showVcVersionNotice'));
		}else{
			require_once DT_WOO_LAYOUTS_DIR .'includes/vc-class.php';
		}
	}
	
	public function include_template_functions(){
		include_once( 'includes/dt-template-functions.php' );
		include_once( 'includes/dt-template-hooks.php' );
	}
	
	public function woocommerce_notice(){
		$plugin  = get_plugin_data(__FILE__);
		echo '
		  <div class="updated">
		    <p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a></strong> plugin to be installed and activated on your site.', DT_WOO_LAYOUTS), $plugin['Name']) . '</p>
		  </div>';
	}
	
	public function showVcVersionNotice(){
		$plugin_data = get_plugin_data(__FILE__);
		echo '
		<div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> Compatible with <strong>Visual Composer</strong> plugin. So You can install <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be used into Visual Composer page builder.', DT_WOO_LAYOUTS), $plugin_data['Name']).'</p>
        </div>';
	}
	
	public function enqueue_styles(){
		wp_enqueue_style('dtwl-woo-font-awesome', DT_WOO_LAYOUTS_URL .'assets/fonts/awesome/css/font-awesome.min.css');
		// register styles
		wp_register_style('dtwl-woo-slick', DT_WOO_LAYOUTS_URL .'assets/vendor/slick/slick.css');
		wp_register_style('dtwl-woo-slick-theme', DT_WOO_LAYOUTS_URL .'assets/vendor/slick/slick-theme.css');
		wp_enqueue_style('dtwl-woo', DT_WOO_LAYOUTS_URL .'assets/css/style.css');
	}
	
	public function enqueue_scripts(){
		// register scritps
		wp_register_script('dtwl-woo-slick', DT_WOO_LAYOUTS_URL .'assets/vendor/slick/slick.min.js', array('jquery'), '', false);
		wp_register_script('dtwl-woo-isotope', DT_WOO_LAYOUTS_URL .'assets/vendor/isotope.pkgd.min.js', array('jquery'), '', false);
		wp_register_script('dtwl-woo-vendor-infinitescroll', DT_WOO_LAYOUTS_URL . 'assets/vendor/jquery.infinitescroll.min.js', array('jquery'), '2.1.0', true);
		
		wp_enqueue_script('dtwl-woo',DT_WOO_LAYOUTS_URL.'assets/js/script.js',array('jquery'),DTWL_VERSION,true);
		
	}
	
	public function dtwl_renderurlajax() {
		?>
		<script type="text/javascript">
			var dtwl_ajaxurl = '<?php echo esc_js( admin_url('admin-ajax.php') ); ?>';
		</script>
		<?php
	}
	public function dtwl_woo_loadmore(){
		$query_types	= isset($_POST['query_types']) ? $_POST['query_types'] : 'category';
		$tab			= isset($_POST['tab']) ? $_POST['tab'] : '';
		$orderby        = isset($_POST['orderby']) ?$_POST['orderby'] : 'recent';
		$number_query   = isset($_POST['number_load']) ?$_POST['number_load'] : '';
		$start          = isset($_POST['start']) ?$_POST['start'] : '';
		$col            = isset($_POST['col']) ?$_POST['col'] : '';
		
		$loop = dhwl_woo_tabs_query($query_types, $tab, $orderby, $number_query, $start);
		
		while ( $loop->have_posts() ) : $loop->the_post();
			$class = 'dtwl-woo-item product';
				
			if ( isset($col) && $col > 0) :
			$column = ($col == 5) ? '15' : absint(12/$col);
			$column2 = absint(12/($col-1));
			$column3 = absint(12/($col-2));
			$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
			endif;
			
			?>
			<div class="<?php echo $class; ?>">
			<?php
				wc_get_template( 'item-grid.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
			?>
			</div>
			
			<?php
		endwhile;
		
		wp_die();
	}
	
	public function dtwl_woo_tab_load_products(){
			
		$tab_args = array(
			'wrap_id'			=> $_POST['wrap_id'],
			'display_type'		=> $_POST['display_type'],
			'query_types'		=> $_POST['query_types'],
			'tab'				=> $_POST['tab'],
			'orderby'			=> $_POST['orderby'],
			'number_query'		=> $_POST['number_query'],
			'number_load'		=> $_POST['number_load'],
			'number_display'	=> $_POST['number_display'],
			'template'			=> $_POST['template'],
			'speed'				=> $_POST['speed'],
			'dots'				=> $_POST['dots'],
			'col'				=> $_POST['col'],
			'loadmore_text'		=> $_POST['loadmore_text'],
			'loaded_text'		=> $_POST['loaded_text'],
			'hover_thumbnail'	=> $_POST['hover_thumbnail'],
		);
		
		wc_get_template( 'tpl-tab.php', array('tab_args' => $tab_args), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
		wp_die();
	}
	
	public function dtwl_woo_tab_next_prev_page(){
		$tab_args = array(
			'display_type'		=> $_POST['display_type'],
			'query_types'		=> $_POST['query_types'],
			'tab'				=> $_POST['tab'],
			'orderby'			=> $_POST['orderby'],
			'number_query'		=> $_POST['number_query'],
			'template'			=> $_POST['template'],
			'col'				=> $_POST['col'],
			'hover_thumbnail'	=> $_POST['hover_thumbnail'],
			'offset'			=> $_POST['offset'],
			'paged'				=> $_POST['current_page'],
		);
		
		wc_get_template( 'tpl-tab.php', array('tab_args' => $tab_args), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
		wp_reset_postdata();
		wp_die();
	}
	
	
	public function dtwl_wooproducts_loadmore(){
		$cat				= isset($_POST['cat']) ? $_POST['cat'] : '';
		$tags				= isset($_POST['tags']) ? $_POST['tags'] : '';
		$orderby        	= isset($_POST['orderby']) ?$_POST['orderby'] : 'recent';
		$order   			= isset($_POST['order']) ?$_POST['order'] : '';
		$col            	= isset($_POST['col']) ?$_POST['col'] : '';
		$hover_thumbnail	= isset($_POST['hover_thumbnail']) ?$_POST['hover_thumbnail'] : '';
		$posts_per_page     = isset($_POST['posts_per_page']) ?$_POST['posts_per_page'] : '';
		$offset          	= isset($_POST['offset']) ?$_POST['offset'] : '';
		
		global $woocommerce, $product;
		
		$paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
		$query_args = array(
			'post_type' => 'product',
			'posts_per_page' => absint($posts_per_page),
			'order'          	=> $order == 'asc' ? 'ASC' : 'DESC',
			'post_status' => 'publish',
			'offset'            => $offset,
			'paged' => $paged
		);
		
		$category_array = array();
		if( !empty($cat) ){
			$category_array = array_filter(explode(',', $cat));
				
			if(!empty($category_array)){
				$query_args['tax_query'][] =
				array(
					'taxonomy'			=> 'product_cat',
					'field'				=> 'slug',
					'terms'				=> $category_array,
					'operator'			=> 'IN'
				);
			}
		}
		
		$tags_array = array();
		if( !empty($tags) ){
			$tags_array = array_filter(explode(',', $tags));
				
			if( !empty($tags_array) ){
				$query_args['tax_query'][] =
				array(
					'taxonomy'			=> 'product_tag',
					'field'				=> 'slug',
					'terms'				=> $tags_array,
					'operator'			=> 'IN'
				);
			}
		}
		
		dhwl_woo_query_orderby($orderby);
		
		$query = new WP_Query( $query_args );
		$idx = 0;
		if($query->have_posts()){
			while ( $query->have_posts() ) : $query->the_post();
				$idx = $idx + 1;
				if($idx < $posts_per_page + 1)
					wc_get_template( 'item-content-product.php', array('grid_columns' => absint($col), 'hover_thumbnail' => $hover_thumbnail), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
			endwhile;
			
			if($idx < $posts_per_page){
				// there are no more product
				// print a flag to detect
				echo '<div id="dtwl-ajax-no-products" class=""><!-- --></div>';
			}
		}else{
			// no products found
		}
		
		wp_reset_postdata();
		wp_die();
	}
	
	public function dtwl_category_nav(){
		$category			= $_POST['cat'];
		$orderby			= $_POST['orderby'];
		$order				= $_POST['order'];
		$hover_thumbnail	= $_POST['hover_thumbnail'];
		$offset				= $_POST['offset'];
		$paged				= $_POST['current_page'];
		$posts_per_page		= $_POST['posts_per_page'];
	
		$orderby    		 	= sanitize_title( $orderby );
		$order       			= sanitize_title( $order );
		
		$query_args = array(
			'posts_per_page' 	=> $posts_per_page,
			'post_status' 	 	=> 'publish',
			'post_type' 	 	=> 'product',
			'offset'            => $offset,
			'order'          	=> $order == 'asc' ? 'ASC' : 'DESC',
			'paged'				=> $paged,
			);
		
		$query_args['tax_query'][] =
		array(
			'taxonomy'			=> 'product_cat',
			'field'				=> 'slug',
			'terms'				=> $category,
			'operator'			=> 'IN'
		);
		
		dhwl_woo_query_orderby($orderby);
		
		$p = new WP_Query( $query_args  );
	
		while ( $p->have_posts() ) : $p->the_post(); $limit = $p->found_posts;
			$class = 'dtwl-woo-item product';
			$column = ($posts_per_page == 5) ? '15' : absint(12/$posts_per_page);
			$column2 = absint(12/($posts_per_page-1));
			$column3 = absint(12/($posts_per_page-2));
			$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
			
			?>
			<div class="<?php echo $class; ?>">
				<?php
					wc_get_template( 'item-grid.php', array('hover_thumbnail' => $hover_thumbnail), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
				?>
			</div>
			<?php
			if($offset + $posts_per_page >= $limit){
	    		// there are no more product
	    		// print a flag to detect
	    		echo '<div id="dtwl-ajax-no-products" class=""><!-- --></div>';
	    	}
    	endwhile;
    	
		wp_reset_postdata();
		wp_die();
	}
	
}
$dt_wl = new DT_WL_Manager();