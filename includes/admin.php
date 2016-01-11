<?php
class DTWL_Admin{
	
	public function __construct(){

		add_action ('admin_init', array(&$this,'init'));
		
	}
	
	public function init(){
		wp_register_script( 'dtwl-woo-admin',DT_WOO_LAYOUTS_URL. 'assets/js/admin.js', array('jquery'),DTWL_VERSION,false);
		wp_enqueue_script( 'dtwl-woo-admin' );
		wp_enqueue_style('dtwl-woo-chosen');
		
	}
	// End Class
}
new DTWL_Admin();