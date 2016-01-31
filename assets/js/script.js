function DTWL_ProductTabsListHeight(){
	var product_list_h = 0;
	jQuery('.dtwl-woo-product-tabs .dtwl-woo-product-list .dtwl-woo-item').each(function(){

		var _this_height = jQuery(this).height();

		if( product_list_h > 0 && _this_height < product_list_h){
			jQuery(this).css( 'min-height', product_list_h);
		}else{
			product_list_h = _this_height;
		}
	});
};


var dtwl_effect= {
	setAnimate: function (el, eclass){
		jQuery(el).find('.dtwl-woo-loadmore').fadeOut('fast');
		morec = '';
		if( jQuery(el+' .dtwl-woo-product-list').hasClass('owl-carousel') ){
		 	morec = '.owl-item.active ';
		}
		jQuery(el+' .dtwl-woo-product-list '+morec+'.'+eclass).each(function(i){
			jQuery(this).attr("style", "-webkit-animation-delay:" + i * 300 + "ms;"
	                + "-moz-animation-delay:" + i * 300 + "ms;"
	                + "-o-animation-delay:" + i * 300 + "ms;"
	                + "animation-delay:" + i * 300 + "ms;");
			if (i == jQuery(el+' .dtwl-woo-product-list '+morec+'.'+eclass).size() -1) {
	            jQuery(el+' .dtwl-woo-product-list').addClass("play");
	            jQuery(el).find('.dtwl-woo-loadmore').fadeIn(i*0.3);
	            if( morec!='' ){
	            	setTimeout(function(){
	            		dtwl_effect.delAnimate(el);
	            	}, i*300+700);
	            }
	        }
		});

	},
	resetAnimate: function (el){
		var wrapid, eclass, contentid;
		wrapid = el.parents('.dtwl-woo').attr('id');
    	eclass = 'animate-'+Math.floor((Math.random() * 1000000000));
    	contentid = el.find('a').attr('href');
    	//
    	jQuery('#'+wrapid+' .dtwl-woo-product-list').removeClass('play');
    	jQuery('#'+wrapid+' .dtwl-woo-product-list .dtwl-woo-item').removeClass('item-animate');
    	jQuery('#'+wrapid+' .dtwl-woo-product-list .dtwl-woo-item').attr('style', '');
    	
    	// Remove class with prefix animate-
    	var classNames = [];
		jQuery('#'+wrapid+' .dtwl-woo-product-list div[class*="animate-"]').each(function(i, el){
		    var name = (el.className.match(/(^|\s)(animate\-[^\s]*)/) || [,,''])[2];
		    if(name){
		        classNames.push(name);
		        jQuery(el).removeClass(name);
		    }
		});
    	//
    	jQuery('#'+wrapid+' '+contentid+' .dtwl-woo-product-list .dtwl-woo-item').addClass('item-animate').addClass(eclass);
    	// Set effect
	    dtwl_effect.setAnimate('#'+wrapid+' '+contentid, eclass );

	    DTWL_ProductTabsListHeight();
	},
	delAnimate: function(el){
		if( jQuery(el+' .dtwl-woo-product-list .dtwl-woo-item').hasClass('item-animate') ) jQuery(el+' .dtwl-woo-product-list .dtwl-woo-item').removeClass('item-animate');
	}
};

;(function(jQuery){
	function dtwl_woo_init(){
		dtwl_woo_loadmore();
		dtwl_woo_tab_loadmore();
		dtwl_woo_tab_filters_menu();
		dtwl_woo_isotope();

		jQuery(window).resize(function(){
			dtwl_woo_tab_filters_menu();
		});
	};

	function dtwl_woo_loadmore(){
		// Click loadmore from shortcode Product Tabs
		jQuery('.dtwl-woo-loadmore').each(function() {
			jQuery(this).click(function(){
				var $this_ = jQuery(this);
				if(!$this_.hasClass('loaded')){
					var btnid, query_types, tab, orderby, number_load, start, loadtext, loadedtext, wrapid, eclass;
					btnid       = $this_.attr('id');
					query_types = $this_.attr('data-query-types');
					tab         = $this_.attr('data-tab');
	            	orderby     = $this_.attr('data-orderby');
	            	number_load = $this_.attr('data-number-load');
	            	start       = $this_.attr('data-start');
	            	col         = $this_.attr('data-col');
	            	loadtext    = $this_.attr('data-loadtext');
	            	loadedtext  = $this_.attr('data-loadedtext');

	            	wrapid = jQuery('#'+btnid).parents('.dtwl-woo').attr('id');

	            	eclass = 'animate-'+Math.floor((Math.random() * 1000000000));

	            	jQuery('#'+btnid).addClass('loading');

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
		                	eclass      : eclass,
		                },
		                type: 'POST',
		                success: function(data){
		                	if( jQuery.trim(data)!='' ){

		                		jQuery('#'+wrapid+' .dtwl-woo-tab-content  #dtwl_pdtabs_'+tab + ' .dtwl-woo-product-list').append(data);
			                	dtwl_effect.setAnimate('#'+wrapid+' #dtwl_pdtabs_'+tab, eclass );
			                	
			                	jQuery('#'+btnid).removeClass('loading');
			                	if( (parseInt(start) + parseInt(number_load)) > jQuery('.dtwl-woo-product-tabs #dtwl_pdtabs_'+tab+' .dtwl-woo-product-list .dtwl-woo-item').size() ){
			                		jQuery('#'+btnid + ' > span').html(loadedtext);
			                		jQuery('#'+btnid).addClass('loaded');
			                	}else{
			                		jQuery('#'+btnid + ' > span').html(loadtext);
			                	}
			                	jQuery('#'+btnid).attr('data-start', parseInt(start) + parseInt(number_load));
			                	
			                }else{
			                	jQuery('#'+btnid + ' > span').html(loadedtext);
			                	jQuery('#'+btnid).removeClass('loading');
			                	jQuery('#'+btnid).addClass('loaded');
			                }
		                	
		                }
		            });
	            	DTWL_ProductTabsListHeight();
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
				var $wapp_height = jQuery(this).find('.dtwl-woo-tab-content').height();
				jQuery(this).find('.dtwl-woo-tab-content').css('min-height', $wapp_height);

				jQuery(this).find('.dtwl-woo-filters .dtwl-woo-nav-tabs li a.tab-intent').one('click', function(e){
					var $this = jQuery(this);
					if( ! jQuery(this).hasClass('tab-loaded') ){
						//console.log(jQuery(this).attr('data-tab'));
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
							effect_load 	= jQuery(this).attr('data-effect_load'),
							col 			= jQuery(this).attr('data-col'),
							loadmore_text 	= jQuery(this).attr('data-loadmore_text'),
							loaded_text 	= jQuery(this).attr('data-loaded_text'),
							hover_thumbnail = jQuery(this).attr('data-hover_thumbnail');

						var eclass = 'animate-'+Math.floor((Math.random() * 1000000000));

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
									effect_load 	: effect_load,
									col 			: col,
									loadmore_text 	: loadmore_text,
									loaded_text 	: loaded_text,
									hover_thumbnail : hover_thumbnail,
									eclass			: eclass,
								},
								type: 'POST',
								success: function(data){
									if(data != ''){
										setTimeout(function(){
											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content').removeClass('dtwl-woo-tab-loading');

											jQuery('#'+$wapp_id+' .dtwl-woo-tab-content').append(data);
						                	jQuery('#'+$wapp_id+' .dtwl-woo-tab-content').find('#dtwl_pdtabs_'+tab).addClass('active in');
						                	dtwl_effect.setAnimate('#'+$wapp_id+' .dtwl-woo-tab-content #dtwl_pdtabs_'+tab, eclass );

						                	$this.addClass('tab-loaded');
						                	dtwl_woo_loadmore();
										},3000);

									}else{

									}
								}
						});
					}
				});
			});
		}
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
	
	function dtwl_woo_slider(){
		jQuery('.dtwl-woo-product-slider').each(function(){
			var $this = jQuery(this),
			$container = $this.find('.dtwl-woo-slider-content'),
			$slidesSpeed = jQuery($container).attr('data-slidesspeed'),
			$slidesToShow = jQuery($container).attr('data-slidestoshow')
			;
			console.log($slidesSpeed);
			console.log($slidesToShow);
			
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
		DTWL_ProductTabsListHeight();
	});
})(jQuery);

