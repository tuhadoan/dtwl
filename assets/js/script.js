function DTWL_ProductGridMinHeight(){
	var dtwl_woo_product_list_h = 0;
	jQuery('.dtwl-woo-grid .dtwl-woo-item').each(function(){

		var _this_height = jQuery(this).height();

		if( dtwl_woo_product_list_h > 0 && _this_height < dtwl_woo_product_list_h){
			jQuery(this).css( 'min-height', dtwl_woo_product_list_h);
		}else{
			dtwl_woo_product_list_h = _this_height;
		}
	});
};

;(function(jQuery){
	function dtwl_woo_init(){
		dtwl_woo_loadmore();
		dtwl_woo_tab_loadmore();
		dtwl_woo_products_ajax_loadmore();
		dtwl_woo_tab_filters_menu();
		dtwl_woo_isotope();
		dtwl_woo_products_switch_template();

		jQuery(window).resize(function(){
			//DTWL_ProductGridMinHeight();
			dtwl_woo_tab_filters_menu();
		});
	};

	function dtwl_woo_loadmore(){
		// Click loadmore from shortcode Product Tabs
		jQuery('.dtwl-woo-loadmore').each(function() {
			jQuery(this).click(function(){
				var $this_ = jQuery(this);
				if(!$this_.hasClass('loaded')){
					var query_types, tab, orderby, number_load, start, loadtext, loadedtext, wrapid, eclass;
					
					query_types = $this_.attr('data-query-types');
					tab         = $this_.attr('data-tab');
	            	orderby     = $this_.attr('data-orderby');
	            	number_load = $this_.attr('data-number-load');
	            	start       = $this_.attr('data-start');
	            	col         = $this_.attr('data-col');
	            	loadtext    = $this_.attr('data-loadtext');
	            	loadedtext  = $this_.attr('data-loadedtext');

	            	wrapid = jQuery(this).parents('.dtwl-woo').attr('id');

	            	jQuery(this).addClass('loading');
	            	
	            	jQuery.ajax({
		                url: dtwl_ajaxurl,
		                data:{
		                	action 		: 'dtwl_wooloadmore',
		                	query_types : query_types,
		                	tab 		: tab,
		                	orderby     : orderby,
		                	number_load : number_load,
		                	start       : start,
		                	col         : col,
		                },
		                type: 'POST',
		                success: function(data){
		                	if( jQuery.trim(data)!='' ){

		                		jQuery('#'+wrapid+' .dtwl-woo-tab-content .dtwl-woo-product-list').append(data);
			                
			                	jQuery($this_).removeClass('loading');
			                	if( (parseInt(start) + parseInt(number_load)) > jQuery('#'+wrapid+' .dtwl-woo-tab-content .dtwl-woo-product-list .dtwl-woo-item').size() ){
			                		jQuery($this_).find(' > span').html(loadedtext);
			                		jQuery($this_).addClass('loaded');
			                	}else{
			                		jQuery($this_).find(' > span').html(loadtext);
			                	}
			                	jQuery($this_).attr('data-start', parseInt(start) + parseInt(number_load));
			                	
			                }else{
			                	jQuery($this_).find(' > span').html(loadedtext);
			                	jQuery($this_).removeClass('loading');
			                	jQuery($this_).addClass('loaded');
			                }
		                	//DTWL_ProductGridMinHeight();
		                }
		            });
		        }else{
		         	return false;
		        }
		    });
		});
	};

	function dtwl_woo_tab_loadmore(){
		if(jQuery('.dtwl-woo-product-tabs').length > 0){
			jQuery('.dtwl-woo-product-tabs').each(function(){
				var $wapp_id = jQuery(this).attr('id');
				//var $wapp_height = jQuery(this).find('.dtwl-woo-tab-content').height();
				//jQuery(this).find('.dtwl-woo-tab-content').css('min-height', $wapp_height);

				// Click load tab
				jQuery(this).find('.dtwl-woo-filters .dtwl-woo-nav-tabs li a.tab-intent').on('click', function(e){
					var $this = jQuery(this);
					//if( ! jQuery(this).hasClass('tab-loaded') ){
						
						jQuery('#'+$wapp_id+' .dtwl-woo-tab-content').addClass('dtwl-woo-tab-loading');

						var display_type 	= jQuery(this).attr('data-display_type'),
							query_types 	= jQuery(this).attr('data-query_types'),
							tab 			= jQuery(this).attr('data-tab'),
							orderby 		= jQuery(this).attr('data-orderby'),
							number_query 	= jQuery(this).attr('data-number_query'),
							number_load 	= jQuery(this).attr('data-number_load'),
							number_display 	= jQuery(this).attr('data-number_display'),
							template 		= jQuery(this).attr('data-template'),
							speed 			= jQuery(this).attr('data-speed'),
							dots 			= jQuery(this).attr('data-dots'),
							col 			= jQuery(this).attr('data-col'),
							loadmore_text 	= jQuery(this).attr('data-loadmore_text'),
							loaded_text 	= jQuery(this).attr('data-loaded_text'),
							hover_thumbnail = jQuery(this).attr('data-hover_thumbnail');


						jQuery.ajax({
								url : dtwl_ajaxurl,
								data:{
									action			: 'dtwl_wootabloadproducts',
									display_type 	: display_type,
									query_types 	: query_types,
									tab 			: tab,
									orderby 		: orderby,
									number_query 	: number_query,
									number_load 	: number_load,
									number_display 	: number_display,
									template 		: template,
									speed 			: speed,
									dots 			: dots,
									col 			: col,
									loadmore_text 	: loadmore_text,
									loaded_text 	: loaded_text,
									hover_thumbnail : hover_thumbnail,
								},
								type: 'POST',
								success: function(data){
									if(data != ''){
										setTimeout(function(){
											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content').removeClass('dtwl-woo-tab-loading');
											
											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content .dhwl-template-tab-content').html(data).hide();
											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content .dhwl-template-tab-content').fadeIn('slow');
						                	
						                	if( jQuery('#'+$wapp_id+' #dtwl-ajax-no-products').length > 0 ){
						                		jQuery('#'+$wapp_id).find('.dtwl-next-prev-wrap a.dtwl-ajax-next-page').removeClass('ajax-page-disabled').addClass('ajax-page-disabled');
						                	}else{
						                		jQuery('#'+$wapp_id).find('.dtwl-next-prev-wrap a.dtwl-ajax-next-page').removeClass('ajax-page-disabled');
						                		jQuery('#'+$wapp_id).find('.dtwl-next-prev-wrap a.dtwl-ajax-prev-page').addClass('ajax-page-disabled');
						                	}

						                	dtwl_woo_loadmore();
						                	//DTWL_ProductGridMinHeight();
										},500);
										
									}else{

									}
								}
						});
					//}
				});

				// Click ajax next-prev page
				jQuery(this).find('.dtwl-next-prev-wrap a').on('click', function(e){
					e.preventDefault();
					var $_this = jQuery(this);
					if( ! jQuery(this).hasClass('ajax-page-disabled') ){
						var tab_intent = jQuery('#'+$wapp_id).find('.dtwl-woo-filters .dtwl-woo-nav-tabs li.active a');
						
						jQuery('#'+$wapp_id+' .dtwl-woo-tab-content').addClass('dtwl-woo-tab-loading');

						var display_type 	= jQuery(tab_intent).attr('data-display_type'),
							query_types 	= jQuery(tab_intent).attr('data-query_types'),
							tab 			= jQuery(tab_intent).attr('data-tab'),
							orderby 		= jQuery(tab_intent).attr('data-orderby'),
							number_query 	= jQuery(tab_intent).attr('data-number_query'),
							template 		= jQuery(tab_intent).attr('data-template'),
							col 			= jQuery(tab_intent).attr('data-col'),
							hover_thumbnail = jQuery(tab_intent).attr('data-hover_thumbnail');
							
						var offset			= jQuery($_this).attr('data-offset');
						var current_page	= jQuery($_this).attr('data-current-page');

						jQuery.ajax({
								url : dtwl_ajaxurl,
								data:{
									action			: 'dtwl_wootabnextprevpage',
									display_type 	: display_type,
									query_types 	: query_types,
									tab 			: tab,
									orderby 		: orderby,
									number_query 	: number_query,
									template 		: template,
									col 			: col,
									hover_thumbnail : hover_thumbnail,
									offset 			: offset,
									current_page	: current_page,
								},
								type: 'POST',
								success: function(data){
									if(data != ''){
										setTimeout(function(){
											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content').removeClass('dtwl-woo-tab-loading');
											
											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content .dhwl-template-tab-content').html(data).hide();
											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content .dhwl-template-tab-content').fadeIn('slow');
						                	
						                	// uddate current page - offset
						                	var current_page	= parseInt( jQuery($_this).attr('data-current-page') );
						                	var current_offset	= parseInt( jQuery($_this).attr('data-offset') );

						                	if( jQuery($_this).hasClass('dtwl-ajax-next-page') ) {
						                		jQuery('#'+$wapp_id+' .dtwl-next-prev-wrap .dtwl-ajax-next-page').attr('data-current-page', current_page + 1);
						                		var prev_page = parseInt( jQuery($_this).attr('data-current-page') - 1 );
						                		jQuery('#'+$wapp_id+' .dtwl-next-prev-wrap .dtwl-ajax-prev-page').attr('data-current-page', prev_page);

						                		jQuery($_this).attr('data-offset', parseInt(offset) + parseInt(number_query));

						                		jQuery('#'+$wapp_id+' .dtwl-ajax-prev-page').removeClass('ajax-page-disabled');
						                		jQuery('#'+$wapp_id+' .dtwl-ajax-prev-page').attr('data-offset', parseInt(offset) - parseInt(number_query));

						                	}else if( jQuery($_this).hasClass('dtwl-ajax-prev-page') ){
						                		jQuery('#'+$wapp_id+' .dtwl-next-prev-wrap .dtwl-ajax-prev-page').attr('data-current-page', current_page - 1);
						                		jQuery('#'+$wapp_id+' .dtwl-next-prev-wrap .dtwl-ajax-next-page').attr('data-current-page', current_page);

						                		if(current_offset <= 0){
						                			jQuery($_this).addClass('ajax-page-disabled');
						                			jQuery($_this).attr('data-offset', 0);
						                			jQuery('#'+$wapp_id+' .dtwl-ajax-next-page').attr('data-offset', parseInt(number_query));
						                			jQuery('#'+$wapp_id+' .dtwl-next-prev-wrap .dtwl-ajax-next-page').attr('data-current-page', 1);

						                		}else{
						                			jQuery($_this).attr('data-offset', parseInt(current_offset) - parseInt(number_query));

						                			jQuery('#'+$wapp_id+' .dtwl-ajax-next-page').attr('data-offset', parseInt(current_offset) + parseInt(number_query));
						                		}
						                		
						                		jQuery('#'+$wapp_id+' .dtwl-ajax-next-page').removeClass('ajax-page-disabled');
						                		
											}

						                	// hidden action
						                	if( jQuery('#'+$wapp_id+' #dtwl-ajax-no-products').length > 0 ){
						                		$_this.addClass('ajax-page-disabled');
						                	}

						                	dtwl_woo_loadmore();
						                	//DTWL_ProductGridMinHeight();
										},500);
										
									}else{

									}
								}
						});
					}

				});
				
			});
		}
	};

	function dtwl_woo_products_ajax_loadmore(){
		// Click loadmore from shortcode Products layout
		jQuery('#dtwl-navigation-ajax').each(function() {
			jQuery(this).click(function(){
				var $this_ = jQuery(this);
				if(!$this_.hasClass('loaded')){
					var cat, tags, orderby, order, target, col, hover_thumbnail, posts_per_page, offset;

					cat 			= $this_.attr('data-cat');
					tags         	= $this_.attr('data-tags');
	            	orderby     	= $this_.attr('data-orderby');
	            	order 			= $this_.attr('data-order');
	            	target       	= $this_.attr('data-target');
	            	col         	= $this_.attr('data-grid-col');
	            	hover_thumbnail = $this_.attr('data-hover-thumbnail');
	            	posts_per_page  = $this_.attr('data-posts-per-page');
	            	offset			= $this_.attr('data-offset');


	            	jQuery($this_).addClass('loading');

	            	jQuery.ajax({
		                url: dtwl_ajaxurl,
		                data:{
		                	action 		: 'dtwl_wooproductsloadmore',
		                	cat 		: cat,
		                	tags 		: tags,
		                	orderby     : orderby,
		                	order 		: order,
		                	col         : col,
		                	hover_thumbnail      : hover_thumbnail,
		                	posts_per_page : posts_per_page,
		                	offset		: offset,
		                },
		                type: 'POST',
		                success: function(data){
		                	if( jQuery.trim(data)!='' ){

		                		jQuery(target).append(data);
			                	
			                	jQuery($this_).removeClass('loading');
			                	jQuery($this_).attr('data-offset', parseInt(offset) + parseInt(posts_per_page));
			                	
			                	// 
			                	if( jQuery('#dtwl-ajax-no-products').length > 0 ){
			                		jQuery($this_).hide();
			                	}
			                }else{
			                	jQuery($this_).removeClass('loading');
			                	jQuery($this_).hide();
			                }
		                	
		                }
		            });
	            	//DTWL_ProductGridMinHeight();
		        }else{
		         	return false;
		        }
		    });
		});
	};
	
	function dtwl_woo_tab_filters_menu(){
		if(jQuery('.dtwl-woo-filters').length > 0){
			jQuery('.dtwl-woo-filters').each(function(){
				var wrapid = jQuery(this).parents('.dtwl-woo').attr('id');
				if( jQuery( window ).width() < 992){
					jQuery('#'+wrapid).find('#dtwl-woo-nav-tabs').slideUp();

					jQuery(this).find('a.dtwl-inevent-flters').on('click',function(e){
						e.preventDefault();

						if(jQuery(this).hasClass('active')){
							jQuery('#'+wrapid).find('#dtwl-woo-nav-tabs').slideUp();
							jQuery(this).removeClass('active')
						}else{
							jQuery(this).addClass('active');
							jQuery('#'+wrapid).find('#dtwl-woo-nav-tabs').slideDown();
						}

						return false;
					});
				}
			});
		}
	};

	function dtwl_woo_isotope(){
		jQuery('.dtwl-woo-mansory-list').each(function(){
			var $this = jQuery(this);
			
			$this.isotope({
				itemSelector : '.dtwl-woo-mansory-item',
				transitionDuration : '0.3s',
				masonry : {
				}
			});
			
			window.setTimeout(function(){
				$this.isotope('layout');
			},2000);
		});
		
		jQuery(document).on('click','.dtwl-woo-masonry-filter',function(e) {

			e.stopPropagation();
			e.preventDefault();

			var $this = jQuery(this), $container = $this.closest('.dtwl-woo').find('.dtwl-woo-mansory-list');
			// don't proceed if already selected
			if ($this.hasClass('selected')) {
				return false;
			}
			var filters = $this.closest('ul');
			filters.find('.selected').removeClass('selected');
			$this.addClass('selected');

			var options = {
				layoutMode : 'masonry',
				transitionDuration : '0.6s',
			}, 
			key = filters.attr('data-option-key'), 
			value = $this.attr('data-option-value');
			console.log(value);
			value = value === 'false' ? false : value;
			options[key] = value;

			$container.isotope(options);
		});
	};
	
	function dtwl_woo_products_switch_template(){
		if( jQuery('.dtwl-woo-switch_template').length > 0 ){
			jQuery('.dtwl-woo-switch_template').each(function(){
				var $wrapid = jQuery(this).parents('.dtwl-woo').attr('id');

				jQuery(this).find('a.dtwl-woo-mode-view').click(function(e){
					e.preventDefault();
					var $mode_view = jQuery(this).attr('data-mode-view');
					if(!jQuery(this).hasClass('active')){
			            jQuery.ajax({
			                url: dtwl_ajaxurl,
			                data:{
			                	action : 'dtwl_woosetmodeview',
			                	mode : $mode_view
			                },
			                type: 'POST'
			            });
			        }else{
			        	return false;
			        }
			        jQuery('#'+$wrapid+' .dtwl-woo-switch_template a').removeClass('active');
		            jQuery('#'+$wrapid+' .dtwl-woo-switch_template a').each(function(){ 
		            	if ( jQuery(this).hasClass($mode_view) ) jQuery(this).addClass('active');
		            });
		            
		            //
		            if( jQuery('#'+$wrapid+' #dtwl-woo-prdlist.dtwl-woo-product-list').hasClass('dtwl-woo-grid') ){
		            	jQuery('#'+$wrapid+' #dtwl-woo-prdlist.dtwl-woo-product-list').removeClass('dtwl-woo-grid');
		            	jQuery('#'+$wrapid+' #dtwl-woo-prdlist.dtwl-woo-product-list').addClass('dtwl-woo-list');
		            }else if( jQuery('#'+$wrapid+' #dtwl-woo-prdlist.dtwl-woo-product-list').hasClass('dtwl-woo-list') ){
		            	jQuery('#'+$wrapid+' #dtwl-woo-prdlist.dtwl-woo-product-list').removeClass('dtwl-woo-list');
		            	jQuery('#'+$wrapid+' #dtwl-woo-prdlist.dtwl-woo-product-list').addClass('dtwl-woo-grid');
		            }
		            return false;
				});

			});
		}
	};
	function dtwl_woo_slider(){
		jQuery('.dtwl-woo-product-slider').each(function(){
			var $this = jQuery(this),
			$container = $this.find('.dtwl-woo-slider-content'),
			$slidesSpeed = jQuery($container).attr('data-slidesspeed'),
			$slidesToShow = jQuery($container).attr('data-slidestoshow')
			;
			
			window.setTimeout(function(){
				jQuery($container).slick({
					dots: true,
					infinite: true,
					speed: 300,
					adaptiveHeight: true,
					slidesToShow: 3,
					slidesToScroll: 1,
				});
				// remove pre load
				$this.removeClass('dtwl-pre-load');
			},2000);
			
		})
	};
	
	
	jQuery(document).ready(function($) {
		dtwl_woo_init();
		//DTWL_ProductGridMinHeight();
	});
})(jQuery);

