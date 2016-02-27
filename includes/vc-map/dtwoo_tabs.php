<?php
vc_map(array(
	"name" => esc_html__( "DT Product Tabs", DT_WOO_LAYOUTS ),
	"base" => "dtwoo_tabs",
	"category" => esc_html__( "DT WooCommerce", DT_WOO_LAYOUTS ),
	"icon" => "icon-dtwl-woo-tabs",
	"description" => esc_html__( "Show multiple products in tabs", DT_WOO_LAYOUTS ),
	"params" => array(
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Heading", DT_WOO_LAYOUTS ),
			'admin_label'=> true,
			"param_name" => "heading",
			"value" => esc_html__( "DT Product Tabs", DT_WOO_LAYOUTS )
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
			"description" => __ ( "Enter your custom size. Example: 20px.", DT_WOO_LAYOUTS ),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Display Type", DT_WOO_LAYOUTS),
			"param_name" => "display_type",
			'admin_label'=> true,
			"value" => array(
				esc_html__("Tabs Top", DT_WOO_LAYOUTS) => "",
				esc_html__("Tabs Left", DT_WOO_LAYOUTS) =>  "tabs_left",
			),
			"description" => ""
		),
		array(
			"type" => "attach_image",
			"class" => "",
			"heading" => esc_html__("Tabs Left Banner", DT_WOO_LAYOUTS),
			"param_name" => "tabs_left_banner",
			"description" => "",
			"dependency" => array (
				'element' => "display_type",
				'value' => array (
					'tabs_left',
				)
			)
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__( "Banner URL", DT_WOO_LAYOUTS ),
			"param_name" => "banner_url",
			"value" => "",
			"description" => esc_html__("Include http://", DT_WOO_LAYOUTS),
			"dependency" => array (
				'element' => "display_type",
				'value' => array (
					'tabs_left',
				)
			)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Template", DT_WOO_LAYOUTS),
			"param_name" => "template",
			'admin_label'=> true,
			"value" => array(
				esc_html__("Grid", DT_WOO_LAYOUTS) => "grid",
				esc_html__("Carousel", DT_WOO_LAYOUTS) =>  "carousel",
				esc_html__("Masonry", DT_WOO_LAYOUTS) =>  "masonry",
			),
			"description" => "",
			"dependency" => array (
				'element' => "display_type",
				'value' => array (
					'',
				)
			)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Tab types", DT_WOO_LAYOUTS),
			"param_name" => "query_types",
			"value" => array(
				"Categories" 	=> "category",
				"Tags" 			=>  "tags",
				"Order By" 		=>  "orderby"
			),
			"description" => ""
		),
		array (
			"type" => "dtwl_woo_field_categories",
			"class" => "",
			"heading" => __ ( "Categories", DT_WOO_LAYOUTS ),
			"param_name" => "categories",
			"dependency" => array (
				'element' => "query_types",
				'value' => array (
					'category',
				)
			)
		),
		array (
			"type" => "dtwl_woo_field_tags",
			"class" => "",
			"heading" => __ ( "Tags", DT_WOO_LAYOUTS ),
			"param_name" => "tags",
			"dependency" => array (
				'element' => "query_types",
				'value' => array (
					'tags',
				)
			)
		),
		array(
			"type" => "dtwl_woo_field_orderby",
			"class" => "",
			"heading" => esc_html__("Select Order By",DT_WOO_LAYOUTS),
			"param_name" => "tabs_orderby",
			"dependency" => array("element" => "query_types" , "value" => "orderby" ),
			"description" => ""
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
			"dependency" => array (
				'element' => "query_types",
				'value' => array (
					'category',
					'tags',
				)
			),
			"description" => ""
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Row", DT_WOO_LAYOUTS),
			"param_name" => "row",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
			"value" => "2"
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Column per Row", DT_WOO_LAYOUTS),
			"param_name" => "col",
			"value" => array(
				'4' => "4",
				'2' => "2",
				'3' => "3",
				'4' => "4",
				'5' => "5",
				'6' => "6",
			),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Number product with each click to Load more button", DT_WOO_LAYOUTS),
			"param_name" => "number_load",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
			"value" => array(
				'4' => "4",
				'2' => "2",
				'3' => "3",
				'4' => "4",
				'5' => "5",
				'6' => "6",
			),
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Number display", DT_WOO_LAYOUTS),
			"param_name" => "number_display",
			"dependency" => array("element" => "template" , "value" => array("carousel") ),
			"value" => "4"
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Product number limit", DT_WOO_LAYOUTS),
			"param_name" => "number_limit",
			"value" => "8",
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Speed. int(ms)", DT_WOO_LAYOUTS),
			"param_name" => "speed",
			"value" => "300",
			"dependency" => array("element" => "template" , "value" => "carousel"),
			"description" => "",
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Dots", DT_WOO_LAYOUTS),
			"param_name" => "dots",
			"value" => array(
				esc_html__('False',  DT_WOO_LAYOUTS) => "false",
				esc_html__('True',  DT_WOO_LAYOUTS) => "true",
			),
			"dependency" => array("element" => "template" , "value" => "carousel"),
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
		array (
			"type" => "checkbox",
			"class" => "",
			"heading" => __ ( "Hover Thumbnail Effects", DT_WOO_LAYOUTS ),
			"param_name" => "hover_thumbnail",
			"value" => array (
				esc_html__( 'Yes, please', DT_WOO_LAYOUTS ) => '1'
			)
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
		// Loadmore
		// Custom Loadmore
		array (
			"type" => "dtwl_woo_field_heading",
			"value" => "Custom Loadmore",
			"param_name" => "custom_options",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Load more text", DT_WOO_LAYOUTS),
			"param_name" => "loadmore_text",
			"value" => "Load more",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
			"description" => ""
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__("Loaded text", DT_WOO_LAYOUTS),
			"param_name" => "loaded_text",
			"value" => "All products loaded",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Loadmore Border Style", DT_WOO_LAYOUTS),
			"param_name" => "loadmore_border_style",
			"value" => array(
				esc_html__('Solid',  DT_WOO_LAYOUTS) => "solid",
				esc_html__('Dashed',  DT_WOO_LAYOUTS) => "dashed",
				esc_html__('Dotted',  DT_WOO_LAYOUTS) => "dotted",
				esc_html__('None',  DT_WOO_LAYOUTS) => "none",
			),
			"dependency" => array("element" => "template" , "value" => array("grid") ),
		),
		array(
			"type" => "colorpicker",
			"value" => "#eaeaea",
			"heading" => esc_html__("Loadmore Border Color", DT_WOO_LAYOUTS),
			"param_name" => "loadmore_border_color",
			"dependency" => array("element" => "loadmore_border_style" , "value" => array('solid','dashed','dotted') ),
		),
		array(
			"type" => "textfield",
			"value" => "3px",
			"heading" => esc_html__("Loamore Border Width", DT_WOO_LAYOUTS),
			"param_name" => "thumbnail_border_width",
			"dependency" => array("element" => "loadmore_border_style" , "value" => array('solid','dashed','dotted') ),
			"description" => esc_html__("Enter border width: ex: 3px.", DT_WOO_LAYOUTS )
		),
		array (
			"type" => "textfield",
			"class" => "",
			"heading" => __ ( "Loamore Border Radius", DT_WOO_LAYOUTS ),
			"param_name" => "loadmore_border_radius",
			"value" => "0px",
			"dependency" => array("element" => "template" , "value" => array("grid") ),
			"description" => __ ( "Enter your custom border radius . Example: 10px 10px 10px 10px.", DT_WOO_LAYOUTS )
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