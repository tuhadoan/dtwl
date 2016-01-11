function dtwl_woo_init() {
	// Click loadmore from shortcode SNS Product Tabs
	jQuery('.dtwl-woo-loadmore').each(function() {
		jQuery(this).click(function(){
			var $this_ = jQuery(this);
			if(!$this_.hasClass('loaded')){
				var btnid, numberquery, start, order, col, cat, tag, loadtext, loadingtext, loadedtext, type, wrapid, eclass;
				btnid       = $this_.attr('id');
				numberquery = $this_.attr('data-numberquery');
				start       = $this_.attr('data-start');
            	order       = $this_.attr('data-order');
            	col         = $this_.attr('data-col');
            	cat         = $this_.attr('data-cat');
            	tag         = $this_.attr('data-tag');
            	loadtext    = $this_.attr('data-loadtext');
            	loadingtext = $this_.attr('data-loadingtext');
            	loadedtext  = $this_.attr('data-loadedtext');
            	type        = $this_.attr('data-type');

            	wrapid = jQuery('#'+btnid).parents('.dtwl-woo-product-tabs').attr('id');

            	eclass = 'animate-'+Math.floor((Math.random() * 1000000000));

            	jQuery('#'+btnid + ' > span').html(loadingtext); jQuery('#'+btnid).addClass('loading');

            	jQuery.ajax({
	                url: dtwl_ajaxurl,
	                data:{
	                	action 		: 'dtwl_wooloadmore',
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
	                		
		                	if(type == 'order'){
		                		jQuery('#'+wrapid+' #dtwl_pdtabs_'+order+' .dtwl-woo-product-list').append(data);
		                		dtwl_effect.setAnimate( '#'+wrapid+' #dtwl_pdtabs_'+order, eclass );
		                	}else if(type == 'cat'){
		                		jQuery('#'+wrapid+' #dtwl_pdtabs_'+cat+' .dtwl-woo-product-list').append(data);
		                		dtwl_effect.setAnimate('#'+wrapid+' #dtwl_pdtabs_'+cat, eclass );
		                	}else if(type == 'tag'){
		                		jQuery('#'+wrapid+' #dtwl_pdtabs_'+tag+' .dtwl-woo-product-list').append(data);
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

}

// Set min-height for product tabs list
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
}


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

		DTWL_ProductTabsListHeight();
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
	},
	delAnimate: function(el){
		if( jQuery(el+' .dtwl-woo-product-list .dtwl-woo-item').hasClass('item-animate') ) jQuery(el+' .dtwl-woo-product-list .dtwl-woo-item').removeClass('item-animate');
	}
};

jQuery(document).ready(function($) {
	dtwl_woo_init();
	DTWL_ProductTabsListHeight();
});

