<?php
class DT_WooCommerce_Layouts{
	
	public function __construct(){
		$this->init();
	}
	
	public function init(){
		
		if( defined('WPB_VC_VERSION') && function_exists('vc_add_param') ){
			$params_script = DT_WOO_LAYOUTS_URL.'assets/js/params.js';
			vc_add_shortcode_param ( 'dtwl_woo_field_id', 'dhwl_woo_setting_field_id');
			
			// Categories
			vc_add_shortcode_param ( 'dtwl_woo_field_categories', 'dtwl_woo_setting_field_categories', $params_script);
			// Category
			vc_add_shortcode_param ( 'dtwl_woo_field_category', 'dtwl_woo_setting_field_category');

			// Tags
			vc_add_shortcode_param ( 'dtwl_woo_field_tags', 'dtwl_woo_setting_field_tags');
			
			// Tags
			vc_add_shortcode_param ( 'dtwl_woo_field_orderby', 'dtwl_woo_setting_field_orderby');
			
			// Custom heading
			vc_add_shortcode_param ( 'dtwl_woo_field_heading', 'dtwl_woo_setting_field_heading');
			
			//require shortcodes
			require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-map/dtwoo_tabs.php';
			require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-map/dtwoo_slider.php';
			require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-map/dtwoo_products.php';
			require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-map/dtwoo_product_category.php';
		}
	}

//
}

new DT_WooCommerce_Layouts();

// require shortcodes
require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-shortcodes/dt-product-tabs.php';
require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-shortcodes/dt-product-slider.php';
require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-shortcodes/dt-products.php';
require_once DT_WOO_LAYOUTS_DIR . '/includes/vc-shortcodes/dt-product-category.php';
