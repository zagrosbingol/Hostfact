//Prevent conflict with other libraries
//jQuery.noConflict();

var whois_js_loaded = false;

jQuery(document).ready(function(){
	
	if(whois_js_loaded){
		return;
	}
	
	whois_js_loaded = true;
	
	jQuery('.w_form form').submit(function() {
		jQuery(this).find('.wf_form_submit').click();
		return false;	
	});
	
	jQuery('.w_form .wf_form_submit').click(function(){
		
		var whois_form_element = jQuery(this).parents('form');

		if(jQuery(whois_form_element).find('input[name="result"]').val() == 'extern'){
			document.location = jQuery(whois_form_element).find('input[name="newpage"]').val() + '?domain=' + encodeURIComponent(jQuery(whois_form_element).find('input[name="whois_domain"]').val());
		}else{

			jQuery.post(jQuery(whois_form_element).attr('action'), {whois_domain: jQuery(whois_form_element).find('input[name="whois_domain"]').val(), result_type: jQuery(whois_form_element).find('input[name="result"]').val() }, function(responseText, textStatus, XMLHttpRequest) {
	
				if(jQuery(whois_form_element).find('input[name="result"]').val() == 'inline'){
	
					jQuery('div.w_result').after(responseText).remove();
					
					// WordPress iframe wrapper
                    postIframeHeight();
				}else{
					document.location = jQuery(whois_form_element).find('input[name="newpage"]').val();
				}
				
			});		
		}
	});

    // On load, send information to parent frame
    window.parent.postMessage("iframe_reload", "*");
	
});

function w_check_next_domain(){
	if(jQuery('.w_result table tr.domain_tr td.domain_td_unchecked:visible').length == 0){
		return true;
	}
	
	var ToCheck = jQuery('.w_result table tr.domain_tr td.domain_td_unchecked:visible').first();

	jQuery.post(jQuery(ToCheck).parents('form').attr('action'), {check_domain: jQuery(ToCheck).parent().attr('id').replace('domain_tr_','').replace('_','.')}, function(response, textStatus, XMLHttpRequest) {
		
		// Remove class that domain/sld must be checked
		jQuery(ToCheck).removeClass('domain_td_unchecked');
		
		// Parse response
		switch(response.status){
			case 'available':
				jQuery(ToCheck).addClass('domain_td_checked_available');
				jQuery(ToCheck).parent().addClass('domain_td_checked_available');
				break;
			case 'unavailable':
				jQuery(ToCheck).addClass('domain_td_checked_unavailable');
				jQuery(ToCheck).parent().addClass('domain_td_checked_unavailable');
				break;
			case 'error':
				jQuery(ToCheck).addClass('domain_td_checked_error');
				jQuery(ToCheck).parent().addClass('domain_td_checked_unavailable');
				break;			
		}
		
		// Put result in table
		jQuery(ToCheck).html('<span>' + response.text + '</span>');
		
		// Place order link and bind event
		jQuery(ToCheck).parent().find('.domain_td_order').html(response.link);
		jQuery(ToCheck).parent().find('.domain_td_order').find('a.order_link').bind('click', function(event) { w_order_domain(jQuery(this)); });
		jQuery(ToCheck).parent().find('.domain_td_order').find('a.remove_link').bind('click', function(event) { w_remove_domain(jQuery(this)); });
		
		// Check if we need to WHOIS more domains
		if(jQuery('.w_result table tr.domain_tr td.domain_td_unchecked:visible').length > 0){
			w_check_next_domain();
		}
		
		// WordPress iframe wrapper
        postIframeHeight();
		
	}, 'json');
}

function w_show_other_tlds(obj){
	jQuery('.show_other_tlds').hide();
	jQuery('.w_result table tbody.domain_tbody_other').show();
	w_check_next_domain();
}

function w_order_domain(obj){
	var ToOrder = jQuery(obj).parents('tr.domain_tr').attr('id').replace('domain_tr_','').replace('_','.');
	
	jQuery.post(jQuery(obj).parents('form').attr('action'), {order_domain: ToOrder}, function(response, textStatus, XMLHttpRequest) {
		
		// Update table
		jQuery(obj).parents('tr.domain_tr').find('.domain_td_order').html(response.ordered).find('a.remove_link').bind('click', function(event) { w_remove_domain(jQuery(this)); });
		
		// Update cart counter
		w_update_cart_count(response.count);
		
	}, 'json');
}

function w_remove_domain(obj){
	var ToOrder = jQuery(obj).parents('tr.domain_tr').attr('id').replace('domain_tr_','').replace('_','.');
	
	jQuery.post(jQuery(obj).parents('form').attr('action'), {remove_domain: ToOrder}, function(response, textStatus, XMLHttpRequest) {
		
		// Update table
		jQuery(obj).parents('tr.domain_tr').find('.domain_td_order').html(response.link).find('a.order_link').bind('click', function(event) { w_order_domain(jQuery(this)); });
		
		// Update cart counter
		w_update_cart_count(response.count);
		
	}, 'json');
}

function w_update_cart_count(count){
	// Update next-div
	jQuery('.goto_orderform').find('span.cart_count').html(count);
	if(count > 0){
		jQuery('.goto_orderform').show();
	}else{
		jQuery('.goto_orderform').hide();			
	}
}

function postIframeHeight()
{
    var frameHeight = Math.max(
        document.documentElement.scrollHeight,
        document.documentElement.offsetHeight
    );
    window.parent.postMessage("iframe_click_" + frameHeight, "*");
}