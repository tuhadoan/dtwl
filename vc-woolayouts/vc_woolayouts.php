<?php
class VCPlusGalleryAddon {
	function __construct(){
		
		// We safely intergrate with VC with this hook
		add_action( 'init', array( $this, 'integrateWithVC' ) );
		
		// Use this when creating a shortcode addon
		add_shortcode( 'vc_plusgallery', array(&$this, 'renderVCPlusGallery' ));
		
		// Register CSS and JS
		if( is_admin() ){
			add_action( 'admin_enqueue_scripts', array(&$this, 'AdminLoadCssAndJs' ) );
		}else{
			add_action( 'wp_enqueue_scripts', array(&$this, 'FrontLoadCssAndJs' ) );
		}
		
	}
	
	public function integrateWithVC(){
		// Check if Visual Composer is installed
		if ( ! defined('WPB_VC_VERSION') ){
			// Display notice that Visual Composer is require
			//add_action( 'admin_notices', array( $this, 'showVcVersionNotice' ) );
			return;
		}
		
		/*
		 * Add VC Plus gallery logic
		 * Lets call vc_map function to "register" our custom shortcode within Visual Composer interface
		 */
		if(function_exists('vc_map')){
			vc_map(  array(
				"name" => __("Plus Gallery", "vc_plusgallery"),			
				"base" => "vc_plusgallery",
				"class" => "",
				"controls" => "full",
				"icon" => plugins_url('assets/img/vc-plusgallery-logo.png', __FILE__),
				"category" =>__('Plus Gallery', 'js_composer'),
				"description" => __("A responsive photo Wordpress gallery and Social gallery.", "vc_plusgallery"),
				"params" => array(
					array(
						"param_name" => "data_type",
						"type" => "dropdown",
						"admin_label" => true,				
						"heading" => __('Type', 'vc_plusgallery'),
						"value" => array(
							__( 'Plus WP Gallery','vc_plusgallery' ) => 'wp_gallery',
							__( 'Facebook', 'vc_plusgallery' ) => 'facebook',
							__( 'Instagram', 'vc_plusgallery' ) => 'instagram',
							__( 'Flickr', 'vc_plusgallery' ) => 'flickr',
							__( 'Google plus', 'vc_plusgallery' ) => 'google',
						),
						"description" => __('Determines which API endpoint (feed) to access.', 'vc_plusgallery')
					),
					array(
						"param_name" => "data_userid",
						"type" => "textfield",
						"admin_label" => false,
						"heading" => __('UserId', 'vc_plusgallery'),					
						"value" => __('', 'vc_plusgallery'),
						"description" => __('The unique User ID of your online Gallery. EX: naturephotobook or 333666999@Nxx.', 'vc_plusgallery'),
						"dependency" => array(
								"element" => "data_type",
								"value" => array( "facebook", "instagram", "flickr", "google" ),
							),
					),
					array(
						"param_name" => "album_mode",
						"type" => "dropdown",
						"heading" => __('Show Album Mode', 'vc_plusgallery'),
						"value" => array(
							__( 'Show All','vc_plusgallery' ) => 'show_all',
							__( 'Include Albums', 'vc_plusgallery' ) => 'include',
							__( 'Exclude Albums', 'vc_plusgallery' ) => 'exclude',
							__( 'Single Album', 'vc_plusgallery' ) => 'single_album',
						),
						"description" => __('Manage your gallery by including/excluding or single album mode.', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "data_type",
							"value" => array( "wp_gallery", "facebook", "flickr", "google" ),
						),
					),
					array(
						"param_name" => "data_albumid",
						"type" => "textfield",					
						"heading" => __('Album ID', 'vc_plusgallery'),						
						"value" => __('', 'vc_plusgallery'),
						"description" => __('Adding the album id puts plus gallery into <b>single album mode</b>. Where you cannot navigate between albums.', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "album_mode",
							"value" => array( "single_album" ),
						),
					),
					array(
						"param_name" => "data_include",
						"type" => "textarea",
						"heading" => __('Album IDs Include', 'vc_plusgallery'),
						"value" => __('', 'vc_plusgallery'),
						"description" => __('Example (Album a,Album b):<a>111111111,222222222</a> .There should be a comma to separate between IDs (no space). If leaving it blank, default is getting all your albums.', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "album_mode",
							"value" => array( "include" ),
						),
					),
					array(
						"param_name" => "data_exclude",
						"type" => "textarea",
						"heading" => __('Album IDs Exclude', 'vc_plusgallery'),
						"value" => __('', 'vc_plusgallery'),
						"description" => __('Example (Album a, Album b):<a>111111111,222222222</a> .There should be a comma to separate between IDs (no space) of excluded albums. If leaving it blank, default is getting all your albums.', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "album_mode",
							"value" => array( "exclude" ),
						),
					),
					array(
						"param_name" => "data_albumlimit",
						"type" => "textfield",
						"heading" => __('Album Limit', 'vc_plusgallery'),
						"value" => __('20', 'vc_plusgallery'),
						"description" => __('An integer to limit the number of albums intially loaded. defaults to 20.', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "data_type",
							"value" => array( "facebook", "flickr", "google" ),
						),
					),
					array(
						"param_name" => "data_limit",
						"type" => "textfield",
						"heading" => __('Limit', 'vc_plusgallery'),
						"value" => __('20', 'vc_plusgallery'),
						"description" => __('An integer to limit individual Gallery results by. Default to 20. As a best practice keep the size of the galleries around this size. Especially on mobile devices, as they don\'t have the power to load lots of large images in the zoom mode and you will just make them mad. (Flickr also makes you agree to not load more than 30 at a time).', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "data_type",
							"value" => array( "facebook", "instagram" , "flickr", "google" ),
						),
					),
					array(
						"param_name" => "box_color",
						"type" => "colorpicker",
						"heading" => __('Box Color', 'vc_plusgallery'),
						"value" => __('#38beea', 'vc_plusgallery'),
						"description" => __('Set color for box gallery thumbnails.', 'vc_plusgallery')
					),
					array(
						"param_name" => "show_caption",
						"type" => "dropdown",
						"heading" => __('Show Caption', 'vc_plusgallery'),
						"value" => array(
									__('Yes', 'vc_plusgallery')=>'yes',
									__('No', 'vc_plusgallery')=>'no',
								),
						"description" => __('Show / Hide the caption', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "data_type",
							"value" => array( "wp_gallery"),
						),
					),
					array(
						"param_name" => "show_desc",
						"type" => "dropdown",
						"heading" => __('Show Description', 'vc_plusgallery'),
						"value" => array(
									__('Yes', 'vc_plusgallery')=>'yes',
									__('No', 'vc_plusgallery')=>'no',
								),
						"description" => __('Show / Hide image\'s description', 'vc_plusgallery')
					),
					array(
						"param_name" => "desc_length",
						"type" => "textfield",
						"heading" => __('Description Length', 'vc_plusgallery'),
						"value" => __('500', 'vc_plusgallery'),
						"description" => __('Length of description displayed. Default is 500 character limit.', 'vc_plusgallery'),
						"dependency" => array(
							"element" => "show_desc",
							"value" => array( "yes"),
						),
					),
				)
			));
		}
	}
	
	/*
	 * Shortcode login how it should be rendered
	 */
	public function renderVCPlusGallery( $atts, $content = null){
		global $PG_Manager;
		$html = $PG_Manager->PG_Shortcode_Func($atts);
		return $html;
	}
	
	/*
	 * load plugin css and javascript files
	 */
	public function FrontLoadCssAndJs() {
		
	}
	
	public function AdminLoadCssAndJs() {
		
	}
	
	/*
	 * Show notice if this plugin is activated but Visual Composer is not
	 */
	public function showVcVersionNotice(){
		$plugin_data = get_plugin_data(__FILE__);
		echo '
		<div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> Compatible with <strong>Visual Composer</strong> plugin. So You can install <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be used into Visual Composer page builder. Or not, you still can use the shortcode in your post/page.', 'vc_plusgallery'), $plugin_data['Name']).'</p>
        </div>';
	}
}

// Finally intialize code
new VCPlusGalleryAddon();