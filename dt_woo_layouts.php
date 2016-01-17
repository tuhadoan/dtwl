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
	function dtwl_is_activeb(){
		$active_plugins = (array) get_option( 'active_plugins' , array() );
		
		if( is_multisite() )
			$active_plugins = array_merge($active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		
		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}
}

class DT_WL_Manager{
	
	public function __construct(){
		add_action('init', array(&$this, 'init'));
		add_action( 'after_setup_theme', array( &$this, 'include_template_functions' ), 11 );
		
		add_action('wp_head', array($this, 'dtwl_renderurlajax'), 15);
		// Ajax load more
		add_action('wp_ajax_dtwl_wooloadmore', array($this, 'dtwl_woo_loadmore'));
		add_action('wp_ajax_nopriv_dtwl_wooloadmore', array($this, 'dtwl_woo_loadmore'));
	}
	
	public function init(){
		load_plugin_textdomain( DT_WOO_LAYOUTS , false, basename(DT_WOO_LAYOUTS_DIR) . '/languages');
		
		// Register library
		wp_register_style('dtwl-woo-chosen', DT_WOO_LAYOUTS_URL .'assets/css/chosen.min.css');
		
		// require woocommerce
		if( !dtwl_is_activeb() ){
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
		wp_register_style('dtwl-woo-owlcarousel', DT_WOO_LAYOUTS_URL .'assets/css/owl.carousel.min.css');
		wp_register_style('dtwl-woo-slick', DT_WOO_LAYOUTS_URL .'assets/slick/slick.css');
		wp_register_style('dtwl-woo-slick-theme', DT_WOO_LAYOUTS_URL .'assets/slick/slick-theme.css');
		wp_enqueue_style('dtwl-woo', DT_WOO_LAYOUTS_URL .'assets/css/style.css');
	}
	
	public function enqueue_scripts(){
		// register scritps
		wp_register_script('dtwl-woo-owlcarousel', DT_WOO_LAYOUTS_URL .'assets/js/owl.carousel.min.js', array('jquery'), '', true);
		wp_register_script('dtwl-woo-imagesloaded', DT_WOO_LAYOUTS_URL .'assets/js/imagesloaded.pkgd.min.js', array('jquery'), '', false);
		wp_register_script('dtwl-woo-isotope', DT_WOO_LAYOUTS_URL .'assets/js/isotope.pkgd.min.js', array('jquery'), '', false);
		wp_register_script('dtwl-woo-slick', DT_WOO_LAYOUTS_URL .'assets/slick/slick.min.js', array('jquery'), '', false);
		
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
		$template_name	= isset($_POST['template']) ? $_POST['template'] : 'item-grid';
		$orderby        = isset($_POST['order']) ?$_POST['order'] : '';
		$number_query   = isset($_POST['numberquery']) ?$_POST['numberquery'] : '';
		$start          = isset($_POST['start']) ?$_POST['start'] : '';
		$cat       		= isset($_POST['cat']) ?$_POST['cat'] : '';
		$tag       		= isset($_POST['tag']) ?$_POST['tag'] : '';
		$col            = isset($_POST['col']) ?$_POST['col'] : '';
		$eclass         = isset($_POST['eclass']) ?$_POST['eclass'] : '';
		
		$loop = dhwl_woo_query($orderby, $number_query, $cat, $tag, $start);
		while ( $loop->have_posts() ) : $loop->the_post();
			$class = 'dtwl-woo-item product';
				
			if ( isset($col) && $col > 0) :
			$column = ($col == 5) ? '15' : absint(12/$col);
			$column2 = absint(12/($col-1));
			$column3 = absint(12/($col-2));
			$class .= ' dtwl-woo-col-md-'.$column.' dtwl-woo-col-sm-'.$column2.' dtwl-woo-col-xs-'.$column3.' dtwl-woo-col-phone-12';
			endif;
			if($template_name == 'item-list'):
				$class .= ' dtwl-woo-list-content';
			endif;	
			$class .= ' item-animate';
			if ( isset($eclass) && $eclass) :
			$class .= ' '.$eclass;
			endif;
			?>
			<div class="<?php echo $class; ?>">
			<?php
				if ($template_name == 'item-grid' || $template_name == ''):
				wc_get_template( 'item-grid.php', array(), DT_WOO_LAYOUTS_DIR . 'templates/', DT_WOO_LAYOUTS_DIR . 'templates/' );
				endif;
			?>
			</div>
			
			<?php
		endwhile;
		wp_die();
	}
	
}

$dt_wl = new DT_WL_Manager();

