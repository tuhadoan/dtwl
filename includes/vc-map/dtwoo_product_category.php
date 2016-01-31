<?php
vc_map(array(
	"name" => esc_html__( "DT Product Category", DT_WOO_LAYOUTS ),
	"base" => "dtwoo_product_category",
	"category" => esc_html__( "DT WooCommerce", DT_WOO_LAYOUTS ),
	"icon" => "icon-dtwl-woo-slider",
	"description" => esc_html__( "Show multiple products in category", DT_WOO_LAYOUTS ),
	"params" => array(
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Heading", DT_WOO_LAYOUTS ),
			'admin_label'=> true,
			"param_name" => "heading",
			"value" => esc_html__( "DT Product Slider", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "colorpicker",
			"class" => "",
			"heading" => esc_html__( "Heading Color", DT_WOO_LAYOUTS ),
			"param_name" => "heading_color",
			"value" => "#363230"
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Heading font size", DT_WOO_LAYOUTS ),
			"param_name" => "heading_font_size",
			"value" => "20px",
			"description" => esc_html__( "Enter your custom size. Example: 20px.", DT_WOO_LAYOUTS ),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Template", DT_WOO_LAYOUTS),
			"param_name" => "pslides_template",
			'admin_label'=> true,
			"value" => array(
				esc_html__("Default", DT_WOO_LAYOUTS) => "def",
				esc_html__("Center Mode", DT_WOO_LAYOUTS) => "center_mode",
				esc_html__("Slider Syncing", DT_WOO_LAYOUTS) => "slider_syncing",
				esc_html__("Single Mode", DT_WOO_LAYOUTS) => "single_mode",
			),
			"description" => ""
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Center Padding", DT_WOO_LAYOUTS),
			"param_name" => "pslides_centerpadding",
			"dependency" => array("element" => "pslides_template" , "value" => "center_mode"),
			"value" => "100px",
			"description" => esc_html__("Side padding when in center mode (px). EX: 100px", DT_WOO_LAYOUTS)
		),
		
		array (
			"type" => "dtwl_woo_field_categories",
			"class" => "",
			"heading" => __ ( "Categories", DT_WOO_LAYOUTS ),
			"param_name" => "categories",
		),
		array (
			"type" => "dtwl_woo_field_tags",
			"class" => "",
			"heading" => __ ( "Tags", DT_WOO_LAYOUTS ),
			"param_name" => "tags",
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Order By", DT_WOO_LAYOUTS),
			"param_name" => "orderby",
			"value" => array(
				esc_html__('Latest Products',  DT_WOO_LAYOUTS) => "recent",
				esc_html__('BestSeller Products',  DT_WOO_LAYOUTS) => "best_selling",
				esc_html__('Top Rated Products',  DT_WOO_LAYOUTS) => "top_rate",
				esc_html__('Special Products',  DT_WOO_LAYOUTS) => "on_sale",
				esc_html__('Featured Products',  DT_WOO_LAYOUTS) => "featured_product",
				esc_html__('Recent Review',  DT_WOO_LAYOUTS) => "recent_review",
				esc_html__('Price',  DT_WOO_LAYOUTS) => "price",
				esc_html__('Random',  DT_WOO_LAYOUTS) => "rand",
			),
			"description" => ""
		),
		array (
			"type" => "dropdown",
			"class" => "",
			"heading" => __ ( "Order", DT_WOO_LAYOUTS ),
			"param_name" => "order",
			"value" => array (
				__ ( 'Ascending', DT_WOO_LAYOUTS ) => 'ASC',
				__ ( 'Descending', DT_WOO_LAYOUTS ) => 'DESC'
			),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Autoplay", DT_WOO_LAYOUTS),
			"param_name" => "pslides_autoplay",
			"value" => array(
				esc_html__('True',  DT_WOO_LAYOUTS) => "true",
				esc_html__('False',  DT_WOO_LAYOUTS) => "false",
			),
			"description" => ""
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Speed. int(ms)", DT_WOO_LAYOUTS),
			"param_name" => "pslides_speed",
			"value" => "300",
			"description" => "",
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Width Between Each Slide. int(px)", DT_WOO_LAYOUTS),
			"param_name" => "pslides_margin",
			"dependency" => array("element" => "pslides_template" , "value" => array('def', 'center_mode', 'slider_syncing') ),
			"value" => "10px",
			"description" => esc_html__('', DT_WOO_LAYOUTS),
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Slides To Show.", DT_WOO_LAYOUTS),
			"param_name" => "pslides_toshow",
			"dependency" => array("element" => "pslides_template" , "value" => array('def', 'center_mode', 'slider_syncing') ),
			"value" => "5",
			"description" => "",
		),
		// array(
		// 	"type" => "textfield",
		// 	"class" => "",
		// 	"heading" => esc_html__("Slides To Scroll", DT_WOO_LAYOUTS),
		// 	"param_name" => "pslides_toscroll",
		// 	"dependency" => array("element" => "pslides_template" , "value" => array('def', 'center_mode', 'slider_syncing') ),
		// 	"value" => "5",
		// 	"description" => "",
		// ),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Product number limit", DT_WOO_LAYOUTS),
			"param_name" => "pslider_limit",
			"value" => "15",
			"description" => "",
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Dots", DT_WOO_LAYOUTS),
			"param_name" => "pslides_dots",
			"value" => array(
				esc_html__('False',  DT_WOO_LAYOUTS) => "false",
				esc_html__('True',  DT_WOO_LAYOUTS) => "true",
			),
			"description" => esc_html__("Show dot indicators", DT_WOO_LAYOUTS),
		),
		
		// Custom Options
		array (
			"type" => "dtwl_woo_field_heading",
			"value" => "Custom Options",
			"param_name" => "custom_options"
		),
		array(
			"type" => "colorpicker",
			"value" => "#ff4800",
			"heading" => esc_html__("Main Color", DT_WOO_LAYOUTS),
			"param_name" => "main_color"
		),
		
		array(
			"type" => "colorpicker",
			"value" => "#ffffff",
			"heading" => esc_html__("Thumbnail Backgroud Color", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_background_color"
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Thumbnail Border Style", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_style",
			"value" => array(
				esc_html__('None',  DT_WOO_LAYOUTS) => "none",
				esc_html__('Solid',  DT_WOO_LAYOUTS) => "solid",
				esc_html__('Dashed',  DT_WOO_LAYOUTS) => "dashed",
				esc_html__('Dotted',  DT_WOO_LAYOUTS) => "dotted",
				esc_html__('Double',  DT_WOO_LAYOUTS) => "double",
				esc_html__('Groove',  DT_WOO_LAYOUTS) => "groove",
				esc_html__('Ridge',  DT_WOO_LAYOUTS) => "ridge",
				esc_html__('Inset',  DT_WOO_LAYOUTS) => "inset",
				esc_html__('Outset',  DT_WOO_LAYOUTS) => "outset",
				esc_html__('Mix',  DT_WOO_LAYOUTS) => "mix",
			),
		),
		array(
			"type" => "colorpicker",
			"value" => "#e1e1e1",
			"heading" => esc_html__("Thumbnail Border Color", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_color",
			"dependency" => array("element" => "thumbnail_border_style" , "value" => array('solid','dashed','dotted','double','groove','ridge','inset','outset','mix') ),
		),
		array(
			"type" => "textfield",
			"value" => "1px",
			"heading" => esc_html__("Thumbnail Border Width", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_width",
			"dependency" => array("element" => "thumbnail_border_style" , "value" => array('solid','dashed','dotted','double','groove','ridge','inset','outset','mix') ),
			"description" => esc_html__("Enter border width: ex: 1px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Thumbnail Border Radius", DT_WOO_LAYOUTS ),
			"param_name" => "thumbnail_border_radius",
			"value" => "0px",
			"description" => __ ( "Enter your custom border radius . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Thumbnail Padding", DT_WOO_LAYOUTS ),
			"param_name" => "thumbnail_padding",
			"value" => '0',
			"description" => __ ( "Enter your custom padding . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Thumbnail Margin", DT_WOO_LAYOUTS ),
			"param_name" => "thumbnail_margin",
			"value" => '0',
			"description" => __ ( "Enter your custom margin . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
		),
		// Product title
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Product title font size", DT_WOO_LAYOUTS ),
			"param_name" => "product_font_size",
			"value" => "20px",
			"description" => __ ( "Enter your custom size. Example: 20px.", DT_WOO_LAYOUTS ),
		),
		array (
			"type" => "checkbox",
			"class" => "",
			"heading" => __ ( "Hide Rating", DT_WOO_LAYOUTS ),
			"param_name" => "show_rating",
			"value" => array (
				__ ( 'Yes, please', DT_WOO_LAYOUTS ) => '0'
			)
		),
		
		////
		array (
			"type" => "textfield",
			"heading" => esc_html__( "Extra class name", DT_WOO_LAYOUTS ),
			"param_name" => "el_class",
			"description" => esc_html__( "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", DT_WOO_LAYOUTS )
		),
	)
));