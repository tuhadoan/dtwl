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
	function dtwl_woo_init() {
		// Click loadmore from shortcode SNS Product Tabs
		jQuery('.dtwl-woo-loadmore').each(function() {
			jQuery(this).click(function(){
				var $this_ = jQuery(this);
				if(!$this_.hasClass('loaded')){
					var btnid, numberquery, start, order, col, cat, tag, loadtext, loadedtext, type, wrapid, eclass;
					btnid       = $this_.attr('id');
					numberquery = $this_.attr('data-numberquery');
					start       = $this_.attr('data-start');
	            	order       = $this_.attr('data-order');
	            	col         = $this_.attr('data-col');
	            	cat         = $this_.attr('data-cat');
	            	tag         = $this_.attr('data-tag');
	            	loadtext    = $this_.attr('data-loadtext');
	            	loadedtext  = $this_.attr('data-loadedtext');
	            	type        = $this_.attr('data-type');
	            	content_div = $this_.attr('data-target');
	            	item_template = $this_.attr('data-template');

	            	wrapid = jQuery('#'+btnid).parents('.dtwl-woo').attr('id');

	            	eclass = 'animate-'+Math.floor((Math.random() * 1000000000));

	            	jQuery('#'+btnid).addClass('loading');

	            	jQuery.ajax({
		                url: dtwl_ajaxurl,
		                data:{
		                	action 		: 'dtwl_wooloadmore',
		                	template 	: item_template,
		                	numberquery : numberquery,
		                	start       : start,
		                	order       : order,
		                	col         : col,
		                	cat         : cat,
		                	tag			: tag,
		                	eclass      : eclass,
		                },
		                type: 'POST',
		                success: function(data){
		                	if( data!='' ){
		                		jQuery('#'+wrapid+' '+content_div).append(data);
			                	if(type == 'order'){
			                		//jQuery('#'+wrapid+' #dtwl_pdtabs_'+order+' .dtwl-woo-product-list').append(data);
			                		dtwl_effect.setAnimate( '#'+wrapid+' #dtwl_pdtabs_'+order, eclass );
			                	}else if(type == 'cat'){
			                		//jQuery('#'+wrapid+' #dtwl_pdtabs_'+cat+' .dtwl-woo-product-list').append(data);
			                		dtwl_effect.setAnimate('#'+wrapid+' #dtwl_pdtabs_'+cat, eclass );
			                	}else if(type == 'tag'){
			                		//jQuery('#'+wrapid+' #dtwl_pdtabs_'+tag+' .dtwl-woo-product-list').append(data);
			                		dtwl_effect.setAnimate('#'+wrapid+' #dtwl_pdtabs_'+tag, eclass );
			                	}
			                	
			                	jQuery('#'+btnid).removeClass('loading');
			                	if( (parseInt(start) + parseInt(numberquery)) > jQuery('.dtwl-woo-product-tabs #dtwl_pdtabs_'+order+' .dtwl-woo-product-list .dtwl-woo-item').size() ){
			                		jQuery('#'+btnid + ' > span').html(loadedtext);
			                		jQuery('#'+btnid).addClass('loaded');
			                	}else{
			                		jQuery('#'+btnid + ' > span').html(loadtext);
			                	}
			                	jQuery('#'+btnid).attr('data-start', parseInt(start) + parseInt(numberquery));
			                	// Callback quickview, wishlist
			                	//jQuery.fn.yith_quick_view();
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
			$slidesToShow = jQuery($container).attr('data-slidestoshow'),
			$slidesToScroll = jQuery($container).attr('data-slidestoscroll')
			;
			console.log($slidesSpeed);
			console.log($slidesToShow);
			console.log($slidesToScroll);
			
			window.setTimeout(function(){
				jQuery($container).slick({
					dots: true,
					infinite: true,
					speed: 300,
					adaptiveHeight: true,
					slidesToShow: 3,
					slidesToScroll: $slidesToScroll,
				});
				// remove pre load
				$this.removeClass('dtwl-pre-load');
			},2000);
			
		})
	};
	
	
	jQuery(document).ready(function($) {
		dtwl_woo_init();
		dtwl_woo_isotope();
		DTWL_ProductTabsListHeight();
	});
})(jQuery);

