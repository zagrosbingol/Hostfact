$(function(){
	
	$('#generate_new_api_key').click(function(){
		generate_new_api_key();
	});
	
	/**
	 *E-mail
	 */
    $(document).on('keyup', 'input[name="BCC_EMAILADDRESS"]',function(){
	 	if($('input[name="BCC_EMAILADDRESS"]').val() == ''){
	 		$('#send_ticket_bcc_box').slideUp();
	 	}else{
	 		$('#send_ticket_bcc_box').slideDown();
	 	}
	 });

    $('input[name="DKIM_DOMAINS[]"]').change(function(){
        if($(this).prop('checked'))
        {
            $('.DKIM_information[data-domain="'+$(this).val()+'"]').show();
        }
        else
        {
            $('.DKIM_information[data-domain="'+$(this).val()+'"]').hide();
        }
    });
	 
	/**
	 * Api response dialog
	 */
 	if($('#dialog_api_logfile').html() != null){
 		$('#dialog_api_logfile').dialog({modal: true, autoOpen: false, resizable: false, width: 1000, height: 'auto'});

        $(document).on('click', '.api_logfile',function(){
			$.post('XMLRequest.php', {action: 'show_api_logrow', id: $(this).find('input').val()}, function(data) {	
				
				var output = input = '';
				if(data.input != null){input = print_r(data.input);}
				if(data.output != null){output = print_r(data.output);}
				
				$('#api_log_input').html(input);
				$('#api_log_response').html(output);
				
				// Only show if we have a response type
				if(data.type == 'success'){
					$('#api_log_type').html(' ('+ __LANG_APILOG_SUCCESS +')');
				}else if(data.type == 'error'){
					$('#api_log_type').html(' ('+ __LANG_APILOG_ERROR +')');
				}else{
					$('#api_log_type').html('');
				}
				
				$('#dialog_api_logfile').dialog('open');		
			}, 'json');
		});
		
		$('input[name="apiLogSearch"]').keyup(function(event){
			if($(this).val() == ''){
				$('.apilogsearchlink').hide();
			}else{
				$('.apilogsearchlink').show();
			}
			
			ajaxSave('api.show.logfile','searchfor',$(this).val(),'apiLogfile', $('#current_url').val());
		});
	}
	
	/**
	 * Automation pages
 	 */
	if($('#SettingsForm').html() != null && $('#SettingsForm').hasClass('automation')){
		$('#tab-automation input[name$="_value"]').click(function(){
			if($(this).prop('checked')){
				// Show all selects
				$(this).parents('tr').find('select, .checkbox_exception').show();
				$(this).parents('tr').addClass('active');
			}else{
				// Only show first select
				$(this).parents('tr').find('select, .checkbox_exception').hide();
				$(this).parents('tr').removeClass('active');
			}
		});	
		
		$('#tab-automation input[name$="_value"]').each(function(){
			if($(this).prop('checked')){
				$(this).parents('tr').addClass('active');
			}
		});
	}
	
	/**
	 * Backup pages
	 */
 	if($('#dialog_restore_backup').html() != null){
 		$('#dialog_restore_backup').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
		 
		$('input[name="downloadaction"]').click(function(){
			if($(this).prop('checked') && $(this).val() == 'restore'){
				$('#dialog_restore_backup_restore').slideDown();
			}else{
				$('#dialog_restore_backup_restore').slideUp();
			}	
		});
		
		$('#download_restore_btn').click(function(){
			$('#dialog_restore_backup').dialog('close');
			document.download_restore_form.submit();
		});	
	}
	
	/**
	 * Clean testdata
	 */
	if($('#CleanForm').html() != null){
	
		var sent = false;
		$('form[name=form_clean] input[type=checkbox]').change(function(){
			$('#warning_clean_services').hide();
			if($(this).hasClass('check_sub') && $(this).prop('checked')){
				$(this).parents('li').find('ul input[type=checkbox]').prop('checked', true);
			}
			
			if($(this).hasClass('clean_sub') && !$(this).prop('checked')){
				$(this).parents('li').parents('li').find('input[class=check_sub]').prop('checked', false);
			}
			
			if($('form[name=form_clean] input[type=checkbox]:checked').length > 0){
				sent = true;
				$('#form_clean_btn').removeClass('button2').addClass('button1');
			}else{
				sent = false;
				$('#form_clean_btn').removeClass('button1').addClass('button2');
			}
			
			if($('form[name=form_clean] input[name=clean_handles]:checked').length == 1 && $('form[name=form_clean] input[name=clean_services]:checked').length == 0){
				sent = false;
				$('#warning_clean_services').show();
				$('#form_clean_btn').removeClass('button1').addClass('button2');
			}
		});
		
		$('#form_clean_btn').click(function(){
			if(sent){
				$(this).parents('form').submit();
			}
		});
	}

    /**
     * Delete accounting period
     */
    if($('#DeleteYearsForm').html() != null){

        var sent = false;
        $('form[name=form_deleteyears] input[type=checkbox][name^="Data["]').change(function(){

            if($('form[name=form_deleteyears] input[type=checkbox][name^="Data["]:checked').length > 0){
                sent = true;
                $('#form_delete_years_btn').removeClass('button2').addClass('button1').addClass('has_loading_button');
            }else{
                sent = false;
                $('#form_delete_years_btn').removeClass('button1').removeClass('has_loading_button').addClass('button2');
            }
        });

        $('#form_delete_years_btn').click(function(){
            if($(this).hasClass('button1')){
                $(this).parents('form').submit();
            }
        });
    }


    if($('#ClientareaSettings').html() != null)
    {
        $('input[name="CLIENTAREA_TERMS_URL"]').change(function(){
            if($(this).val()){ checkURL($(this).val(), 'customerpanel-terms-url-img'); }else{ $('#customerpanel-terms-url-img').html(''); }
        });
        $('input[name="CLIENTAREA_LOGO_URL"]').change(function(){
            if($(this).val()){ checkURL($(this).val(), 'customerpanel-logo-url-img'); }else{ $('#customerpanel-logo-url-img').html(''); $('#customerpanel_logo_helper_div').hide(); }
        });

        $('select.notification_toggle').change(function()
        {
            if($(this).val() == 'no')
            {
                $('#' + $(this).data('toggle') + '_div').slideUp();
            }
            else
            {
                $('#'+ $(this).data('toggle') +'_div').slideDown();
            }
        });

        $('select[name="clientarea_logout_url_helper"]').change(function(){
            if($(this).val() == 'page'){
                $('input[name="CLIENTAREA_LOGOUT_URL"]').val('http://');
                $('#clientarea_logout_url_helper_div').slideDown();
            }else{
                $('input[name="CLIENTAREA_LOGOUT_URL"]').val('');
                $('#clientarea_logout_url_helper_div').slideUp();
            }
        });

    }

 	/**
 	 * Order form
 	 */
    if($('#OrderForm').html() != null){
    	
    	if($('#delete_orderform')){
			$('#delete_orderform').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_orderform_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_orderform_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_orderform_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
    	
    	$('#addBillingCycle').click(function(){
			$('#addBillingCycle').before('<div>' + $('.addBillingCycleHTML').html().replace("PeriodDefaultPeriods","PeriodChoiceOptions_Periods[]").replace("PeriodDefaultPeriodic","PeriodChoiceOptions_Periodic[]") + '<span class="removeBillingCycle">&nbsp;</span></div>');
		});
        $(document).on('click', '.removeBillingCycle',function(){
			$(this).parent().remove();
		});
		
		$('#hosting_compare_overflow_container').css('width',$('.split2:visible').innerWidth() - 210 + 'px');
		$( window ).resize(function() {
			$('#hosting_compare_overflow_container').css('width',$('.split2:visible').innerWidth() - 210 + 'px');
		});
		
		$('#hosting_compare_table_add_record').click(function(){
			// Add label
			$('.hosting_compare_table_label_div').find('#hosting_compare_table_add_record').before('<div><span>&nbsp;</span><input type="text"name="hosting[CompareLabels][]" class="text1" value=""/></div>');
			
			// Add records
			$('.hosting_compare_table_product_div').each(function(){
				var ProdId = $(this).attr('id').replace('hosting_compare_','');
				
				$(this).append('<input type="text" class="text1" name="hosting[CompareValues]['+ProdId+'][]" value=""/>');
				
			});
			
		});

        $(document).on('click', '.hosting_compare_table_label_div div span',function(){
			
			var LabelIndex = $(this).parent().index() - 2; // -2 for captions
			
			// Remove label
			$(this).parent().remove();
			
			// Remove records
			$('.hosting_compare_table_product_div').each(function(){
				$(this).find('input').eq(LabelIndex - 1).remove();
			});
		});

        $(document).on('click', '.hosting_compare_table .hosting_compare_table_product_div span.arrowleft',function(){
			$(this).parent().after($(this).parent().prev());
		});
        $(document).on('click', '.hosting_compare_table .hosting_compare_table_product_div span.arrowright',function(){
			$(this).parent().before($(this).parent().next());
		});
		
		$('#Available_text').click(function(){ 
			$(this).hide(); 
			$('select[name=Available]').show().focus(); 
			
			$('select[name=Available]').change(function(){ 
				$(this).hide(); 
				$('#Available_text').html(htmlspecialchars($(this).find('option:selected').text()) + " " + STATUS_CHANGE_ICON); 
				$('#Available_text').show();
			});	
			$('select[name=Available]').blur(function(){ 
				$(this).hide(); 
				$('#Available_text').html(htmlspecialchars($(this).find('option:selected').text()) + " " + STATUS_CHANGE_ICON); 
				$('#Available_text').show();
			});
		});
		
		// Billing cycle
		$('input[name="PeriodChoice"]').click(function(){
			switch($(this).val()){
				case 'no':
					$('#BillingCycleDiv').hide();
					$('#BillingCycleChoicesDiv').hide();
					break;
				case 'default':
					$('#BillingCycleDiv').show();
					$('#BillingCycleChoicesDiv').hide();
					break;
				case 'yes':
					$('#BillingCycleDiv').show();
					$('#BillingCycleChoicesDiv').show();
					break;
			}
		});
		
		// Type
		$('select[name="Type"]').change(function(){
			// Hide/show tabs
			$('#tabs > .content').each(function(index){
				if(index > 0){
					$('#tabs').tabs("disable",index);
				}	
			});
	
			if($('#tab-' + $(this).val()).index() > 0){
				$('#tabs').tabs("enable",$('#tab-' + $(this).val()).index()-1);
			}
			
			// Extra tabs for domain or hosting
			switch($(this).val()){
				case 'domain':
					// Also show hosting tab
					$('#tabs').tabs("enable",$('#tab-hosting').index()-1);
					$('.hideForDomain').hide();
					$('.hideForHosting').show();
					
					$('input[name="domain[Available]"]').prop('checked',true).change();
					$('.isOtherEnabled').hide();
					break;
				case 'hosting':
					// Also show domain tab
					$('#tabs').tabs("enable",$('#tab-domain').index()-1);
					$('.hideForHosting').hide();
					$('.hideForDomain').show();
					
					$('input[name="hosting[Available]"]').prop('checked',true).change();
					$('.isOtherEnabled').hide();
					break;
				default:
					$('input[name="domain[Available]"]').prop('checked',false);
					$('input[name="hosting[Available]"]').prop('checked',false);
					
					if($(this).val() == 'custom' || $(this).val() == 'other'){
						$('.isOtherEnabled').show();
					}
					else
					{
						$('.isOtherEnabled').hide();
					}
					break;
			}
			
		});
		//On load
		$('select[name="Type"]').change();
		
		// Hosting theme
		$('select[name="hosting[Theme]"]').change(function(){
			switch($(this).val()){
				case 'simple':
					$('#ThemeCompareDiv').hide();
					$('#ThemePackagesDiv').hide();
					break;
				case 'packages':
					$('#ThemeCompareDiv').hide();
					$('#ThemePackagesDiv').show();
					break;
				case 'compare':
					$('#ThemeCompareDiv').show();
					$('#ThemePackagesDiv').hide();
					break;
			}
		});
		
		$('select[name="ProductGroups[hosting]"]').change(function(){
			
			// Empty current compare-matrix
			$('#hosting_compare_overflow_container .overflow_inner_wrapper').html('');
			$('.hosting_packages_table tbody').html('');
			$('.hosting_packages_table').hide();
			
			$('select[name="hosting[DefaultPackage]"]').html('<option value="">' + __LANG_PLEASE_CHOOSE + '</option>');
			
			if($(this).val() > 0){
				
				$.post('XMLRequest.php', {action: 'get_products_in_productgroup', group_id: $(this).val()}, function(data) {	
					
					// Fill objects in compare-matrix
					$.each(data, function(product_id, product_properties){  
				  		// Extend default package
				  		$('select[name="hosting[DefaultPackage]"]').append('<option value="'+product_id+'">' + product_properties.ProductName + '</option>');
				  		
				  		$('#hosting_compare_overflow_container .overflow_inner_wrapper').append('<div id="hosting_compare_' + product_id + '" class="hosting_compare_table_product_div"><span class="arrowleft">&nbsp;</span><span class="arrowright">&nbsp;</span><strong>' + product_properties.ProductName +'</strong><br />' + product_properties.PriceLine +'<br /></div>');
				  		
					});
					
					// Add records to compare-matrix
					$('.hosting_compare_table_label_div div').each(function(){
						$('.hosting_compare_table_product_div').each(function(){
							var ProdId = $(this).attr('id').replace('hosting_compare_','');
							$(this).append('<input type="text" class="text1" name="hosting[CompareValues]['+ProdId+'][]" value=""/>');
						});
					});
					
					// Fill objects in compare-matrix
					$.each(data, function(product_id, product_properties){  
						$('.hosting_packages_table tbody').append('<tr><td><span class="up_hosting_packages_table">&nbsp;</span><span class="down_hosting_packages_table">&nbsp;</span></td><td>' + product_properties.ProductName +'</td><td style="width: 5px;" class="currency_sign_left">' + ((CURRENCY_SIGN_LEFT) ? CURRENCY_SIGN_LEFT : '&nbsp;') + '</td><td style="width: 50px; padding-left: 0px; padding-right: 0px;" align="right" class="currency_sign_right">' + product_properties.PriceExcl + ((CURRENCY_SIGN_RIGHT) ? ' ' + CURRENCY_SIGN_RIGHT: '') + '</td><td style="width: 75px;">' + product_properties.PricePeriod +'</td><td><input type="text" name="hosting[PackagesDescription]['+product_id+']" class="text1 size11" value="" /></td></tr>');
						
						$(".hosting_packages_table").show();
					});
	
					
					$(".hosting_packages_table tbody tr").removeClass("tr1");
					$(".hosting_packages_table tbody tr:odd").addClass("tr1");	
				
				}, 'json');
			}
	
		});
		$('input[name="domain[Available]"]').change(function(){
			if($(this).prop('checked')){
				$('.isDomainEnabled').show();
			}else{
				$('.isDomainEnabled').hide();
			}
		});
		$('input[name="hosting[Available]"]').change(function(){
			if($(this).prop('checked')){
				$('.isHostingEnabled').show();
			}else{
				$('.isHostingEnabled').hide();
			}
		});
		
		// Sorting package table
		$(".hosting_packages_table tbody tr:odd").addClass("tr1");
        $(document).on('click', '.up_hosting_packages_table',function(){
			$(this).parents('tr').after($(this).parents('tr').prev());
			$(".hosting_packages_table tbody tr").removeClass("tr1");
			$(".hosting_packages_table tbody tr:odd").addClass("tr1");		
		});
        $(document).on('click', '.down_hosting_packages_table',function(){
			$(this).parents('tr').before($(this).parents('tr').next());	
			$(".hosting_packages_table tbody tr").removeClass("tr1");
			$(".hosting_packages_table tbody tr:odd").addClass("tr1");	
		});
		
		// Domain whois
        $(document).on('click', '#other_tld_list li',function(){
			
			if($(this).hasClass('popular')){
				// Remove element from popular list
				$('#' + $(this).attr('id').replace('other_tld_','popular_tld_')).remove();
				
				$(this).removeClass('popular');
				
				if($('#popular_tld_list li').length == 0){ $('#popular_tld_list span.none').show(); }
			}else{
				// Add element to popular list
				$('#popular_tld_list ul').append('<li id="'+$(this).attr('id').replace('other_tld_','popular_tld_')+'">'+$(this).html()+'<input type="hidden" name="domain[Popular][]" value="'+$(this).attr('id').replace('other_tld_','').replace('_','.')+'" /><a class="a1">'+__LANG_REMOVE_FROM_POPULAR_TLD_LIST+'</a></li>');
				
				$(this).addClass('popular');
				
				$('#popular_tld_list span.none').hide();
			}
		});

        $(document).on('click', '#popular_tld_list li a',function(){
			// Remove element from popular list
			$(this).parents('li').remove();			
			
			$('#' + $(this).parents('li').attr('id').replace('popular_tld_','other_tld_')).removeClass('popular');	
			
			if($('#popular_tld_list li').length == 0){ $('#popular_tld_list span.none').show(); }
		});
		
		$('#popular_tld_list ul').sortable({ 
			placeholder: 'dashboarddragplaceholder', 
			forcePlaceholderSize: true, 
			axis: 'y',
			cursor: 'move'
		}).disableSelection();
		
		$('select[name="ProductGroups[domain]"]').change(function(){
			if($(this).val()){
				// If productgroup is selected, load tlds
				$('#right').load('orderform.php?page=add #right', {group_id: $(this).val()}, function(response){ 
					// Re initialize sortable
					$('#popular_tld_list ul').sortable({ 
						placeholder: 'dashboarddragplaceholder', 
						forcePlaceholderSize: true, 
						axis: 'y',
						cursor: 'move'
					}).disableSelection();
				});	
			}else{
				// If no productgroup is selected
				$('#right').html('');
			}
		});
		
		$('input[name="domain[ResultURL]"]').change(function(){
			if($(this).val()){ checkURL($(this).val(), 'whoisform-result-url'); }else{ $('#whoisform-result-url').html(''); }
		});
		
		$('input[name="domain[OrderFormURL]"]').change(function(){
			if($(this).val()){ checkURL($(this).val(), 'orderform-url-img'); }else{ $('#orderform-url-img').html(''); }
		});
		
		$('input[name="iframe_integration"]').click(function(){
			if($(this).prop('checked')){
				$('#iframe_integration_div').show();
			}else{
				$('input[name="domain[ResultURL]"]').val('');
				$('input[name="domain[OrderFormURL]"]').val('');
				$('#iframe_integration_div').hide();
			}
		});
		
	    	
    }
 	
 	/**
	 * WHOIS FORM panel pages
	 */
 	if($('#WHOISForm').html() != null){
 		
		$('select[name="OrderForm"]').change(function(){
			location.href = '?id=' + $(this).val() + '#tab-wizard';
		});
		
		$('input[name="extern"]').click(function(){
			if($(this).prop('checked')){
				if($('#whois_wizard_standalone').html() != null){
					$('#whois_wizard_inlineresult').hide();
					$('#whois_wizard_standalone').show();
				}
				
				$('#whois_wizard_form_1').hide();
				$('#whois_wizard_form_2').hide();
				$('#whois_wizard_form_3').show();
				
				$('input[name="inlineresult"]').parent('label').css('visibility','hidden');
			}else{
				
				if($('input[name="inlineresult"]').prop('checked')){
					$('#whois_wizard_form_1').hide();
					$('#whois_wizard_form_2').show();
					
					if($('#whois_wizard_standalone').html() != null){
						$('#whois_wizard_inlineresult').show();
						$('#whois_wizard_standalone').hide();
					}
				}else{
					$('#whois_wizard_form_1').show();
					$('#whois_wizard_form_2').hide();
					
					if($('#whois_wizard_standalone').html() != null){
						$('#whois_wizard_inlineresult').hide();
						$('#whois_wizard_standalone').show();
					}
				}
				
				$('#whois_wizard_form_3').hide();
				
				$('input[name="inlineresult"]').parent('label').css('visibility','visible');
			}	
		});					
		
		$('input[name="inlineresult"]').click(function(){
			if($(this).prop('checked')){
				$('#whois_wizard_inlineresult').show();
				$('#whois_wizard_standalone').hide();
				
				$('#whois_wizard_form_1').hide();
				$('#whois_wizard_form_2').show();
				$('#whois_wizard_form_3').hide();
			}else{
				$('#whois_wizard_inlineresult').hide();
				$('#whois_wizard_standalone').show();
				
				$('#whois_wizard_form_1').show();
				$('#whois_wizard_form_2').hide();
				$('#whois_wizard_form_3').hide();
			}	
		});
 	}
 	
 	/**
	 * Customer panel pages
	 */
 	if($('#paymentmethod_list').html() != null){
 		$('#paymentmethod_list').sortable({ 
			placeholder: 'paymentmethodsdragplaceholder', 
			forcePlaceholderSize: true, 
			axis: 'y',
			cursor: 'move',
			update: function(){
				$.post('XMLRequest.php', { action: 'order_paymentmethods', order: $(this).sortable('serialize') } );	
			}
		}).disableSelection();
		
		if($('#delete_paymentmethod')){
			$('#delete_paymentmethod').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_paymentmethod_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_paymentmethod_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_paymentmethod_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
	}
	
	if($('#OrderForm').html() != null){
		$('input[name="COMPANY_AV_PDF"]').change(function(){
			if($(this).val()){ checkURL($(this).val(), 'orderform-terms-url-img'); }else{ $('#orderform-terms-url-img').html(''); }
		});
		
		$('select[name="ORDERFORM_ENABLED"]').change(function(){
			if($(this).val() == 'yes'){ 
				$('.orderform_enabled').slideDown();
				$('#tabs').tabs("enable",1);
			}else{ 
				$('.orderform_enabled').slideUp();
				$('#tabs').tabs("disable",1);
			}
		});
		
		$('select[name="ORDERMAIL_SENT"]').change(function(){
			if($(this).val() == 'yes'){ 
				$('#ordermail_sent_div').slideDown();
			}else{
				$('#ordermail_sent_div').slideUp();
			}
		});
	}
	
	if($('#PaymentMethodForm').html() != null){


        $('#PaymentMethodForm select[name="PaymentType"]').change(function(){

            // Hide forms & show hint
            $('.form_div_directdebit').hide();
            $('#form_div_specific').hide();


            if($(this).val() == 'wire')
            {

            }
            else if($(this).val() == 'auth')
            {
                $('.form_div_directdebit').show();
                $('#form_txt_Hint').parent().hide();
            }
            else if($(this).val() != '')
            {
                // On change directory, hide/show settings
                loadSettings($(this).val(), false);
            }

        });

        // Load different payment methods
        loadPaymentMethodList();

        // On change slideup/down transaction costs
		$('#PaymentMethodForm input[name="FeeType_helper"]').click(function(){
			if($(this).val() == "no"){
				$('#form_div_transactioncost').slideUp();
			}else{
				if($(this).val() == "discount"){
					$('#feetype_title_discount').show();
					$('#feetype_title_fee').hide();
				}else{
					$('#feetype_title_discount').hide();
					$('#feetype_title_fee').show();
				}
				
				$('#form_div_transactioncost').slideDown();
				
				$('#PaymentMethodForm input[name="FeeType"]:checked').click();
			}
		});
		
		$('#PaymentMethodForm input[name="FeeType"]').click(function(){
			if($(this).val() == "EUR"){
				$('#transaction_fee_percentage_sign').hide();
				$('#feetype_title_eur').show();
				$('#feetype_title_proc').hide();
				if($('#PaymentMethodForm input[name="FeeAmount"]').val() != 0)
				{
					$('.span_product_excl_incl').show();
					$('#PaymentMethodForm input[name="FeeAmount"]').keyup();
				}
			}else{
				$('#transaction_fee_percentage_sign').show();
				$('#feetype_title_eur').hide();
				$('#feetype_title_proc').show();
				$('.span_product_excl_incl').hide();
			}
		});
		
		$('#PaymentMethodForm input[name="FeeAmount"]').keyup(function(){
			
			if(!$(this).val() || $('#PaymentMethodForm input[name="FeeType"]:checked').val() != 'EUR'){
				$('.span_product_excl_incl').hide().find('span').html('');
			}else{
				if(!$(this).hasClass('price_incl')){
					// Excl
					$('.span_product_excl_incl').show().find('span').html(formatAsMoney(formatAsFloat(deformatAsMoney($(this).val()),2) * (1 + (1*STANDARD_TAX))));
				}else{
					// Incl
					$('.span_product_excl_incl').show().find('span').html(formatAsMoney(formatAsFloat(deformatAsMoney($(this).val()),2) / (1 + (1*STANDARD_TAX))));
				}
			}
		});

		// get all logo's
		$.post('XMLRequest.php', {action: 'payment_backoffice', logos: 'all'}, function(data) {
			if(data){
				$.each(data, function(val, text){  
			  			if($('#PaymentMethodForm input[name="ImageHidden"]').val() && text == $('#PaymentMethodForm input[name="ImageHidden"]').val()){
	        				option = $('<option></option>').val(text).html(text).prop('selected',true);
						}else{
	   						option = $('<option></option>').val(text).html(text);
	   					}
	   				$('select[name="Image"]').append(option);
	 			});
 			}
		}, 'json');
		
		
		// Direct debit options
        $('#sdd_add_day').click(function () {
            // Add extra batch to the list of batches
            var ExtraSelect = $('select[name="SDD_DAYS[]"]').first().parent('div').clone();
            // Set to default value 1
            $(ExtraSelect).find('select[name="SDD_DAYS[]"]').val('1');

            $('#sdd_days_div').append(ExtraSelect);
        });

        // Remove days from the list
        $(document).on('click', '.sdd_remove_day', function () {
            // If none left, add a new one
            if ($('select[name="SDD_DAYS[]"]').length == 1) {
                $('select[name="SDD_DAYS[]"]').val('1').change();
                return;
            }

            // Else remove
            $(this).parent('div').remove();
        });

        if($('#change_sdd_id'))
        {
            $('#change_sdd_id').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

            $(document).on('blur', 'input[name="SDD_ID"]', function()
            {
                // only show dialog if the value is new and the old value was set
                if($(this).data('current-sddid') != '' && $(this).data('current-sddid') != $(this).val())
                {
                    $('#change_sdd_id').dialog('open');
                }
            });

            $(document).on('click', 'a#change_sdd_id_confirm', function()
            {
                $('input[name="change_sepa_type_value"]').val($('input[name="change_sepa_type"]:checked').val());
                $('#change_sdd_id').dialog('close');
            });
        }
		
		$('input[name="SDD_iban_bic_helper"]').click(function(){
			if($(this).prop('checked')){
				$('#sdd_directdebit_iban_bic').show();
			}else{
				$('#sdd_directdebit_iban_bic').hide();
			}
		})
		
		$('input[name="SDD_limit_helper"]').click(function(){
			if($(this).prop('checked')){
				$('#sdd_directdebit_limits').show();
			}else{
				$('#sdd_directdebit_limits').hide();
			}
		})
		
		$('#sdd_default_notice_link').click(function(){
			$('input[name="SDD_NOTICE"]').val('14');
		});
		
	}
	
	if($('#payment_settings_form').html() != null){
		$('input[name="IDEAL_EMAIL"]').change(function(){
			if($(this).val()){ checkURL($(this).val(), 'ideal-email-url-img'); }else{ $('#ideal-email-url-img').html(''); }
		});
	}
	
	/**
	 * Services pages
	 */
	if($('#ServiceSettingsForm').html() != null){
		$('input[name="ACCOUNT_GENERATION"]').click(function(){
			if($(this).val() == '1'){
				$('#account_generation_auto_div').slideDown();
			}else{
				$('#account_generation_auto_div').slideUp();
			}
		});

		$('input[name="DOMAIN_HOME_WARNING"]').change(function(){
			if($('input[name="DOMAIN_HOME_WARNING"]:checked').val() == "off"){
				$('#domain_home_warning_div').slideUp();
			}else{
				$('#domain_home_warning_div').slideDown();
			}
		});
		
		
		$('input[name="ProductModules[]"]').click(function(){
			var ShowDeactivateWarning = false;
			$('input[name="ProductModules[]"]').each(function(){
				if($(this).hasClass('is_enabled') && !$(this).prop('checked'))
				{
					ShowDeactivateWarning = true;
				}
			});
			
			if(ShowDeactivateWarning === false)
			{
				$('input[name="iagreewithdeactivate"]').prop('checked', false);
				$('#add_product_module_deactivate').hide();
			}
			else
			{
				$('#add_product_module_deactivate').show();
			}	
		});
		
	}
	
	/**
	 * Export
	 */
	if($('#delete_exporttemplate')){
		$('#delete_exporttemplate').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});		
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_exporttemplate_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_exporttemplate_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_exporttemplate_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_exportdelete.submit();
			}	
		});
	}
 	
	$('#ExportData').change( function() {
		$('#download_export_btn').parent().show();
		$('#filters').show();
		
		if($(this).find('option:first').val() == '0'){
			$(this).find('option:first').remove();	
		}
		
		$('.exportfilter_div').hide();
		$('.exportfilter_div').find('input, select').attr('disabled',true);
		
		ExportData = $(this).val();
		
		if($('#filter_' + ExportData).find('select#status_' + ExportData).length != 0 && $('#filter_' + ExportData).find('select#status_' + ExportData).find('option').length == 1){
			$.post("/ajax_get.php", {get: 'get_translated_values', ExportData: ExportData}, function(data){
				$.each(data, function(key, value){
					$('#filter_' + ExportData).find('select#status_' + ExportData).append($('<option value="' + key + '">' + value + '</option>'));
				});
			}, 'json');
		}
		
		$('#export_edit').attr('href','export.php?page=edit&id=' + $(this).val()).show();
		$('#filter_' + $(this).val()).show();
		$('#filter_' + $(this).val()).find('input, select').attr('disabled',false);
	});
	
	$('#ExportDataNew').change( function() {
		$('#fragment-s-1 li').remove();
		$('#columns_div table input[type=checkbox]').removeAttr('checked');
		
		$('#columns_div').load('export.php?page=edit&id=0&exportdata=' + $(this).val() + ' table', function(){
			$('#columns_box').show();
		});
	});
	
	$('#download_export_btn').click(function(){
		$('#ExportTemplateForm').submit();
	});

	$('#form_export_edit_btn').click(function() {
		document.form_exporttemplates.submit();
	});

    $(document).on('change', '#columns_box .addcolumn', function(){
		if($(this).prop('checked')){
			if($(this).hasClass('subtable')){
				label = $(this).parent().find('label').text() + ' <span class="normalfont">(' + $(this).parent().find('label').data('table') + ')</span>';
			}else{
				label = $(this).parent().find('label').text();	
			}
			
			$('#fragment-s-1').append('<li id="' + $(this).attr('id') + '_clone"><input type="hidden" value="' + $(this).attr('id') + '" name="selected_columns[]" /><a class="a1 c1 ico inline ico_sortable floatl">&nbsp;</a><strong>' + label + '</strong></li>');
		}else{
			item = document.getElementById($(this).attr('id') + '_clone');
			$(item).remove();
		}
	});
	
	$('#exportsortable_list ul').sortable({ 
		placeholder: 'dashboarddragplaceholder', 
		forcePlaceholderSize: true, 
		axis: 'y',
		cursor: 'move'
	}).disableSelection();
	
	$('#export_externfilter').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});	
	
	$('.extern_actionbtn').click(function(){
		$('#export_externfilter').dialog('open');
		$('#export_externfilter').load('export.php?id=' + $(this).parent().find('input[name=id]').val() + '&dataset=' + $(this).parent().find('input[name=dataset]').val() + '&template_type=extern #form_download_extern',function(){
			$('#export_externfilter').find('input, select').attr('disabled',false);
		});
	});

    $(document).on('click', '#download_extern_btn',function(){
		document.form_download_extern.submit();
	});
	
	/**
	 * Contract renewal
	 */
	if($('#contract_renew_div').html() != null){
		
		$('input[name="contract_renew_for_helper"]').click(function(){
			if($(this).val() == "no"){
				$('#contract_renew_div').slideUp();
			}else{
				$('#contract_renew_div').slideDown();
			}
		});
		
		$('input[name="contract_renew_confirm_mail_helper"]').click(function(){
			if($(this).val() == "no"){
				$('#contract_renew_confirm_mail_div').slideUp();
			}else{
				$('#contract_renew_confirm_mail_div').slideDown();
			}
		});
		
		
	}

    $(document).on('click', '#form_settings_btn', function() {
	
		$('#tax_change_default_dialog_line, #tax_change_default_dialog_total').hide();
	
		if($('#tax_change_default_dialog_line').html() != null && STANDARD_TAX != $('select[name="DefaultTaxRate1"]').val()){			
			$('#tax_change_default_dialog span.newtax').html($('select[name="DefaultTaxRate1"]').find('option:selected').text().replace('%',''));
			$('#tax_change_default_dialog_line').show();
		}
		if($('#tax_change_default_dialog_total').html() != null && STANDARD_TOTAL_TAX != $('select[name="DefaultTaxRate2"]').val()){			
			$('#tax_change_default_dialog span.newtax2').html($('select[name="DefaultTaxRate2"]').find('option:selected').text().replace('%',''));
			$('#tax_change_default_dialog_total').show();
		}

		if($('#tax_change_default_dialog').html() && (($('#tax_change_default_dialog_line').html() != null && STANDARD_TAX != $('select[name="DefaultTaxRate1"]').val() && $('select[name="DefaultTaxRate1"]').val() != null) || ($('#tax_change_default_dialog_total').html() != null && STANDARD_TOTAL_TAX != $('select[name="DefaultTaxRate2"]').val() && $('select[name="DefaultTaxRate2"]').val() != null))){
			$('#tax_change_default_dialog').dialog('open');
		}else{
			document.form_create.submit();
		} 
	});
	
	/**
	 * Which new codes?
	 */
	$('select[name="DATE_FORMAT"]').change(function(){ 
		// Store current dateformat
		var tempFormat = DATEFORMAT;
		
		// Translate date
		DATEFORMAT = $(this).val();
		var today = new Date();
		$('#date_format_example').html(rewrite_date_db2site(today.getFullYear(),today.getMonth()+1,today.getDate()));
		
		// Fix changed dateformat
		DATEFORMAT = tempFormat;
	});
	 
	if($('#number_examples').html() != null){
		$.post('XMLRequest.php', {action: 'get_new_codes_examples', get: 'all'}, function(data){
			$('#debtorcode_td').html(data.debtorcode);
			$('#creditorcode_td').html(data.creditorcode);
			$('#productcode_td').html(data.productcode);
			$('#ordercode_td').html(data.ordercode);
			$('#pricequotecode_td').html(data.pricequotecode);
			$('#invoicecode_td').html(data.invoicecode);
			$('#creditinvoicecode_td').html(data.creditinvoicecode);
		}, 'json');
		
		$('input[name$="_PREFIX"], input[name$="_NUMBER"]').change(function(){		
			var codenumber = $(this).attr('name').replace('_PREFIX','').replace('_NUMBER','');
			$.post('XMLRequest.php', {action: 'get_new_codes_examples', get: codenumber.toLowerCase(), prefix: $('input[name="'+codenumber+'_PREFIX"]').val(), number: $('input[name="'+codenumber+'_NUMBER"]').val()}, function(data){
				$('#'+codenumber.toLowerCase()+'_td').html(data[codenumber.toLowerCase()]);
			}, 'json');
				
		});
		$('input[name$="_PREFIX"], input[name$="_NUMBER"]').keyup(function(){ var tmp_number = $(this); number_delay(function(){ $(tmp_number).change(); },800)});		

		var number_delay = (function(){
		  var number_timer = 0;
		  return function(callback, ms){
		    clearTimeout (number_timer);
		    number_timer = setTimeout(callback, ms);
		  };
		})();
	}
	
	$('input[name$="_NUMBER"]').keydown(function(event) {
        // Allow: backspace, delete, tab and escape
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
	});
	
	// Default tax percentage
	$('#tax_change_default_dialog').dialog({modal: true, autoOpen: false, resizable: false, width: 500, height: 'auto'});

	$('#tax_change_submit_btn').click(function(){
		
		if($('input[name="TaxChanger_Products"]').prop('checked')){
			$('input[name="TaxChanger[Products]"]').val('yes');
		}else{
			$('input[name="TaxChanger[Products]"]').val('');
		}
		
		if($('input[name="TaxChanger_Subscriptions"]').prop('checked')){
			$('input[name="TaxChanger[Subscriptions]"]').val('yes');
		}else{
			$('input[name="TaxChanger[Subscriptions]"]').val('');
		}
		
		if($('input[name="TaxChanger_InvoiceElements"]').prop('checked')){
			$('input[name="TaxChanger[InvoiceElements]"]').val('yes');
		}else{
			$('input[name="TaxChanger[InvoiceElements]"]').val('');
		}
		
		if($('input[name="TaxChanger_PriceQuoteElements"]').prop('checked')){
			$('input[name="TaxChanger[PriceQuoteElements]"]').val('yes');
		}else{
			$('input[name="TaxChanger[PriceQuoteElements]"]').val('');
		}
		
		if($('input[name="TaxChanger_TemplateElements"]').prop('checked')){
			$('input[name="TaxChanger[TemplateElements]"]').val('yes');
		}else{
			$('input[name="TaxChanger[TemplateElements]"]').val('');
		}
		
		$('#tax_change_default_dialog').dialog('close');
		
		$('#SettingsForm').submit();	
	});
	
	$('input[name="BACKOFFICE_URL"]').change(function(){
		if($(this).val()){ checkURL($(this).val(), 'backoffice-url-img'); }else{ $('backoffice-url-img').html(''); }
	});
    $('input[name="CLIENTAREA_URL"]').change(function(){
        if($(this).val()){ checkURL($(this).val(), 'clientarea-url-img'); }else{ $('clientarea-url-img').html(''); }
    });
	$('input[name="IDEAL_EMAIL"]').change(function(){
		if($(this).val()){ checkURL($(this).val(), 'ideal-email-url-img'); }else{ $('ideal-email-url-img').html(''); }
	});
	$('input[name="ORDERFORM_URL"]').change(function(){
		if($(this).val()){ checkURL($(this).val(), 'orderform-url-img'); }else{ $('orderform-url-img').html(''); }
	});
	
    /*
    *  Invoices & pricequotes
    */
    $(document).on('change', 'select[name="PERIODIC_REMINDER_SENT_FOR"]',function(){
		if($(this).val() == "private"){ 
            $('select[name="CONTRACT_RENEW_FOR"]').val('private');
            $('select[name="CONTRACT_RENEW_FOR"]').prop('disabled', true);
        }else{ 
            $('select[name="CONTRACT_RENEW_FOR"]').prop('disabled', false); 
        }
    
	});

    /*
    *  Ticket system pop3 auth
    */
    $(document).on('change', 'select[name="TICKET_POP3_AUTH_TYPE"]',function(){
        if($(this).val() == "OAUTH2_MS"){
            $('div.auth_type_basic_auth').hide();
            $('div.auth_type_oauth2_ms').show();
        }else{
            $('div.auth_type_oauth2_ms').hide();
            $('div.auth_type_basic_auth').show();
        }
    });

});

function get_oauth_device_code(){
    $.post('XMLRequest.php', {action: 'get_oauth2_ms_device_code' }, function(data) {
        if (data && data.result === true) {
            $('div.auth_type_oauth2_ms p:not(.instructions)').remove();
            $('div.auth_type_oauth2_ms p.instructions span').text(data.code);
            $('div.auth_type_oauth2_ms p.instructions').removeClass('hide');
        }
    }, 'json');
}

function deletePaymentMethod(payment_id){
	$('#delete_paymentmethod').find('input[name="id"]').val(payment_id);
	$('#delete_paymentmethod').dialog('open');
}

function loadPaymentMethodList(){
    $.post('XMLRequest.php', {action: 'payment_backoffice', edit: $('#PaymentMethodForm input[name="id"]').val()}, function(data) {

        $('select[name="PaymentType"]').find('option[value!="wire"][value!="auth"][value!=""]').remove();

        if($(data).length > 0)
        {

            $.each(data, function(val, text){

                if($('#PaymentMethodForm input[name="DirectoryHidden"]').val() && text == $('#PaymentMethodForm input[name="DirectoryHidden"]').val()){
                    option = $('<option></option>').val(text).html(text).prop('selected', true);
                }else{
                    option = $('<option></option>').val(text).html(text);
                }
                $('#PaymentMethodForm select[name="PaymentType"] optgroup:last-child').append(option);
            });

        }
        else {
            // Put in optgroup
            $('#PaymentMethodForm select[name="PaymentType"] optgroup:last-child').remove();
        }

        // When filled, get settings from selected method
        if($('#PaymentMethodForm input[name="DirectoryHidden"]').val() && $('#PaymentMethodForm input[name="DirectoryHidden"]').val() != 'payment.auth'){
            loadSettings($('#PaymentMethodForm input[name="DirectoryHidden"]').val(),true);
        }
    }, 'json');
}
function loadSettings(directory, edit){
	$.post('XMLRequest.php', {action: 'payment_backoffice', directory: directory}, function(data) {
			$('#form_div_specific').show();    			
			
			if(data.InternalName != undefined){
				$('input[name="InternalName"]').val(data.InternalName);
			}else{
				$('input[name="InternalName"]').val('');	
			}
			
			// MerchantID				
			if(data.MerchantID != undefined){
				$('#form_div_MerchantID').show();
				$('#form_txt_MerchantID').html(data.MerchantID.Title);
				if(edit == false){ $('input[name="MerchantID"]').val(data.MerchantID.Value); }
			}else{
				$('#form_div_MerchantID').hide();
			}
			
			// Password				
			if(data.Password != undefined){
				$('#form_div_Password').show();
				$('#form_txt_Password').html(data.Password.Title);
			if(edit == false){ 	$('input[name="Password"]').val(data.Password.Value); }
			}else{
				$('#form_div_Password').hide();
			}
			
			// Hint				
			if(data.Hint != undefined && data.Hint){
				$('#form_txt_Hint').parent().show();
				$('#form_txt_Hint').html(data.Hint);
			}else{
				$('#form_txt_Hint').parent().hide();
			}

			// Advanced	
			if(edit == false){ 		
				if(data.Advanced.Title != undefined){ $('input[name="Title"]').val(data.Advanced.Title); }
				if(data.Advanced.Image != undefined){ $('select[name="Image"]').val(data.Advanced.Image); }
				if(data.Advanced.Description != undefined){ $('textarea[name="Description"]').val(data.Advanced.Description); }
				
				if(data.Advanced.FeeType != undefined){ $('select[name="FeeType"]').val(data.Advanced.FeeType); if(data.Advanced.FeeType != ''){ $('#tr_feeamount').show(); }else{ $('#tr_feeamount').hide(); } }
				if(data.Advanced.FeeAmount != undefined){ $('input[name="FeeAmount"]').val(data.Advanced.FeeAmount); }
				if(data.Advanced.FeeDesc != undefined){ $('input[name="FeeDesc"]').val(data.Advanced.FeeDesc); }
				
				if(data.Advanced.Extra != undefined){ $('input[name="Extra"]').val(data.Advanced.Extra); }
			}

            // Show/hide Extra
            if(data.Advanced.Extra != undefined)
            {
                $('input[name="Extra"]').parent().show();
            }
            else
            {
                $('input[name="Extra"]').parent().hide();
            }
			
		}, 'json');
}

function deleteOrderForm(orderform_id){
	$('#delete_orderform').find('input[name="id"]').val(orderform_id);
	$('#delete_orderform').dialog('open');
}

function generate_new_api_key(){
	$.post('XMLRequest.php', {action: 'generate_new_api_key' }, function(data) {
			$('input[name="API_KEY"]').val(data);
		
		$('#dialog_apikey').dialog('close');
	});
}