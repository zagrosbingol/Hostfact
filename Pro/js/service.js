// Keep track of nameservers
var NameserverFromHosting = false;
var NameserverFromDebtor = false;


$(function(){
	
	var mouse_is_inside = false;
	var mouse_is_select = false;
	
	$('body').mouseup(function(){
        if(mouse_is_select == true){
            mouse_is_select = false;
        }
	});

	/**
	 * Service pages
	 */
	if($('#ServiceForm').html() != null){
		
		$('input[name="Debtor"]').change(function(){
			
			if($(this).val() == ""){
				$('#enveloppe').hide();
				$('#service_subscription').slideUp();
				
				if($('#servicetype_' + $('input[name="PeriodicType"]:checked').val()).html() != null){
					$('#servicetype_' + $('input[name="PeriodicType"]:checked').val()).slideUp();
				}
				
				$('#servicetype_module').slideUp();
				
				$('.form_create_btn_p').hide();
				
				// Keep track of nameservers
				NameserverFromDebtor = false;
			}else{
				// SlideDown subscription data
				if($('input[name="subscription_invoice"]').prop('checked') && $('#servicetype_' + $('input[name="PeriodicType"]:checked').val()).find('.setting_help_box').html() == null){
					$('#service_subscription').slideDown();
					$('.form_create_btn_p').show();
				}
				
				
				// get debtor data
				$.post("XMLRequest.php", { action: 'invoice_get_debtor', debtor: $(this).val(), return_nameservers: 'true' }, function(data){
					$('#formJQ-CompanyName').html(htmlspecialchars(data.CompanyName));
					$('#formJQ-Name').html(htmlspecialchars(data.Initials + ' ' + data.SurName));
					$('#formJQ-Address').html(htmlspecialchars(data.Address));
					$('#formJQ-ZipCodeCity').html(htmlspecialchars(data.ZipCode + ' ' + data.City));
					$('#formJQ-Country').html(htmlspecialchars(data.CountryLong));
					
					if(!$('#invoiceText').attr('class','hide')){
						$('#EditLink').hide();
					}
					
					var PeriodicInvoiceDays;
					if(data.PeriodicInvoiceDays != -1){
						PeriodicInvoiceDays  = data.PeriodicInvoiceDays;
					}else{
						PeriodicInvoiceDays = __DEFAULT_PERIODIC_INVOICE_DAYS
					}
					
					if($('input[name=page]').val() == 'edit'){
						$('#EditLink').hide();
						$('#NextDate_text').hide();
						$('#invoiceText').hide();
						$('#invoiceInput').show();
						
						$('#datePicker').show();
						
						$('input[name=emptyNextdate]').val('false');
					}else if(PeriodicInvoiceDays == 0){
						$('#EditLink').show();
						$('#NextDate_text').show();
						$('#invoiceText').hide();
						$('#invoiceInput').show();
						
						$('#datePicker').hide();
						
						$('input[name=emptyNextdate]').val('false');
					}else{
						$('#EditLink').hide();
						$('#NextDate_text').hide();
						$('#datePicker').hide();
						$('#invoiceText').show();
						
						$('input[name=emptyNextdate]').val('true');
					}
					
					$('#invoiceDays').html(PeriodicInvoiceDays);
					$('input[name="InvoiceCollectionDays"]').val((data.InvoiceCollectionDays != -1) ? data.InvoiceCollectionDays : __DEFAULT_INVOICE_COLLECTION_DAYS);

					if(IS_INTERNATIONAL == 'true')
					{
						if(data.Address2){
							$('#formJQ-Address2').html(htmlspecialchars(data.Address2)).removeClass('hide');
						}else{
							$('#formJQ-Address2').html('').addClass('hide');
						}
						if(data.StateName){
							$('#formJQ-State').html(htmlspecialchars(data.StateName)).removeClass('hide');
						}else{
							$('#formJQ-State').html('').addClass('hide');
						}
					}
				
					$('#enveloppe').show();
					
					
					$('input[name="Taxable"]').val(data.Taxable);
					$('input[name="TaxRate1"]').val('');
					if(data.Taxable == "true"){	
						$('select[name="subscription[TaxPercentage]"]').show();
						$('#TaxPercentage_label').hide();
					}else if(data.TaxRate1 != '' || parseInt(data.TaxRate1) == data.TaxRate1){	
						$('input[name="TaxRate1"]').val(data.TaxRate1);
						$('select[name="subscription[TaxPercentage]"]').hide();
						$('#TaxPercentage_label').html(vat(formatAsFloatVat(data.TaxRate1 * 100, (data.TaxRate1.toString().length - data.TaxRate1.toString().indexOf('.') - 3))) + '%').show();
					}else{
						$('select[name="subscription[TaxPercentage]"]').hide();
						$('#TaxPercentage_label').html('0%').show();
					}
					
					// Only select a preferred server if there is nothing selected
					if($('input[name=page]').val() == 'add'){
						if(data.Server > 0){
							$('select[name="hosting[Server]"]').val(data.Server);
						}else{
							$('select[name="hosting[Server]"] > option:eq(0)').prop('selected', true);
						}
					}
					
					// If the field PeriodicType is undefined we are in add mode 
					if($('input[type="hidden"][name="PeriodicType"]').val() == undefined){
						$('input[name="PeriodicType"]:checked').click();
					}else if($('input[type="hidden"][name="PeriodicType"]').val() == "domain"){
						// Get hosting accounts for domain
						get_hosting_accounts($('input[name="Debtor"]').val(),false);
						getHandleSelect($('input[name="Debtor"]').val(), $('select[name="domain[Registrar]"]').val(), 'domain');
					}
					
					// Get other data
					if($('select[name="SubscriptionType"]').val() == 'existing'){
						// get existing subscriptions
						get_periodics(null,$('input[name="Debtor"]').val());
					}else if($('select[name="SubscriptionType"]').val() == 'new' || $('select[name="SubscriptionType"]').val() == 'current'){
						// Update prices
						calculate_span_subscription_excl_incl(true);
					}
					
					// Keep track of nameservers
					if(data.DNS1 == undefined){
						NameserverFromDebtor = false;
					}else{
						NameserverFromDebtor = true;
						$('input[name="domain[DNS1]"]').val(data.DNS1);
						$('input[name="domain[DNS2]"]').val(data.DNS2);
						$('input[name="domain[DNS3]"]').val(data.DNS3);
						$('#domain_nameservers_text').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val()).change();
						toggleDomainNameserverForm('hide');
					}
								
				}, "json");
			}				
		});
		
		$('input[name="PeriodicType"]').click(function(){
			
			$('.servicetype_div').slideUp();
			$('.form_create_btn_p').hide();
			$('#subscription_div_period').show();
				
			toggleSubscriptionAutoRenew('yes');
			
			if(	$('input[name="PeriodicType"]:checked').val() != 'other' && 
				$('input[name="PeriodicType"]:checked').val() != 'domain' && 
				$('input[name="PeriodicType"]:checked').val() != 'hosting' && 
				$('input[name="Debtor"]').val())
			{
				$.post('modules.php', { module: $('input[name="PeriodicType"]:checked').val(), page: 'ajax', action: 'service_form_add' }, function(data){
					$('#servicetype_module').html(data).slideDown();
			
					// SlideDown subscription data
					if($('input[name="subscription_invoice"]').prop('checked') && $('#servicetype_module').find('.setting_help_box').html() == null){
						$('#service_subscription').slideDown();	
						$('.form_create_btn_p').show();
					}else if($('#servicetype_module').find('.setting_help_box').html() != null)
					{
						$('#service_subscription').slideUp();
						$('.form_create_btn_p').hide();	
					}else{
						$('#service_subscription').slideUp();
						if(data)
						{
							$('.form_create_btn_p').show();
						}	
					}

				}, 'html');
			}
			else if($('#servicetype_' + $('input[name="PeriodicType"]:checked').val()).html() != null && $('input[name="Debtor"]').val())
			{
				$('#servicetype_' + $('input[name="PeriodicType"]:checked').val()).slideDown();
				
				// What if there is no setting box
				if($('#servicetype_' + $('input[name="PeriodicType"]:checked').val()).find('.setting_help_box').html() == null){
						
					if($('input[name="subscription_invoice"]').prop('checked')){
						$('#service_subscription').slideDown();			
					}else{
						$('#service_subscription').slideUp();
					}
					
					$('.form_create_btn_p').show();
				// What if there is a setting box
				}else{
					$('#service_subscription').slideUp();
					$('.form_create_btn_p').hide();
				}

				// Number visible for subscription?
				if($('input[name="PeriodicType"]:checked').val() == "domain" || $('input[name="PeriodicType"]:checked').val() == "hosting"){
					$('#service_subscription_number').hide();			
				}else{
					$('#service_subscription_number').show();
				}
				
				// Execute extra jquery code?
				if($('input[name="PeriodicType"]:checked').val() == "domain" && $('input[name="Debtor"]').val()){
					// Get hosting accounts for domain
					get_hosting_accounts($('input[name="Debtor"]').val(),true);
					
					// Get product for tld
					$('select[name="domain[Tld]"]').change();
				}else if($('input[name="PeriodicType"]:checked').val() == "hosting" && $('input[name="Debtor"]').val()){
					// Get server data
					$('select[name="hosting[Server]"]').change();
				}
				
				// Show existing periodic
				$('#subscription_toggler').show();	
				
			}else{
				// Number visible for subscription
				$('#service_subscription_number').show();
				
				// Hide existing periodic
				if($('#subscription_toggler:visible').html() != null){
					$('select[name="SubscriptionType"]').val('new').change().blur();
					$('#subscription_toggler').hide();
				}
				
				if($('input[name="Debtor"]').val() && $('input[name="subscription_invoice"]').prop('checked')){
                    // Also show subscription part
                    $('#service_subscription').slideDown();

                    $('.form_create_btn_p').show();
				}
			}
			
			
		});
		
		$('input[name="subscription_invoice"]').click(function(){
			if($(this).prop('checked') && $('input[name="Debtor"]').val()){
				
				if($('input[name="subscription[Identifier]"]').val() == undefined){
					// Service hosting: If there is a hosting package selected get the associated product information
					if($('select[name="hosting[Package]"]').val() > 0 && !$('input[name="subscription[Product]"]').val()){
						$.post('XMLRequest.php', { action: 'package_information', id: $('select[name="hosting[Package]"]').val() }, function(data){
							if(data.resultSet != undefined && data.resultSet[0].PackageName){
								$('input[name="subscription[Product]"]').val(data.resultSet[0].Product).change();
							}
						},"json");
					}
					
					// Service domain: If there is a domain selected get the associated product information
					if($('select[name="domain[Tld]"]').val() != undefined){
						$.post("XMLRequest.php", { action: "get_product_by_tld", tld: $('select[name="domain[Tld]"]').val()}, function(data){
							if(data.errorSet == undefined && data.ProductID > 0){
								$('input[name="subscription[Product]"]').val(data.ProductID).change();
							}
						}, "json");					
					}
				}
				
				$('#service_subscription').slideDown();
				$('.form_create_btn_p').show();
			}else{
				$('#service_subscription').slideUp();
				if($('input[name="PeriodicType"]:checked').val() == "other"){
					$('.form_create_btn_p').hide();
				}
				// If we have a current subscription, ask what we should do
				ask_disconnect_subscription();
			}
		});
		
		$('input[name="hosting[AddDomain]"]').change(function(){
			if($(this).prop('checked') && $('input[name="Debtor"]').val()){
				$('#adddomain_redirected').show();
				$('.create_another_label').hide();
				$('input[name="CreateAnother"]').prop('checked',false);
			}else{
				$('#adddomain_redirected').hide();
				$('.create_another_label').show();
			}
		});
		
		// On load, check for
			// 1. is there a setting box
			if($('#servicetype_' + $('input[name="PeriodicType"]:checked').val()).find('.setting_help_box').html() != null){
				// Hide subscription
				$('#service_subscription').slideUp();
				// Hide button
				$('.form_create_btn_p').hide();
			}
			else if(!$('input[name="Debtor"]').val())
			{
				// Hide button
				$('.form_create_btn_p').hide();
			}
			
			// Should we adjust value of NameserverFromDebtor
			if($('input[name="NameserverFromDebtor"]').val() == "true"){
				NameserverFromDebtor = true;
				$('#domain_nameservers_text').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val()).change();
				toggleDomainNameserverForm('hide');
			}

		/********************************************************************
		 * BEGIN: Subscription
		 *******************************************************************/		
		$('input[name="subscription[Product]"]').change(function(){
						
			if($(this).val() == '')
			{
				$('#subscription_price_period').show().find('span').html($('select[name="subscription[Periodic]"] option:selected').text());
				return false;
			}

			var ProductID = $(this).val();
			$.post("XMLRequest.php", { action: "get_product", product: $(this).val()},
				function(data){
				  
		            if(data.ProductCode){
		            	
		            	if(data.HasCustomPrice == 'period')
						{
							CustomPriceObject[ProductID] = data.CustomPrices;
						}
						
						// Save product period price info
						ProductPeriodPriceObject[ProductID] = new Array();
						ProductPeriodPriceObject[ProductID]['PricePeriod'] = data.PricePeriod;
						ProductPeriodPriceObject[ProductID]['PriceIncl'] = data.PriceIncl;
						ProductPeriodPriceObject[ProductID]['PriceExcl'] = data.PriceExcl;
		            	
						$('textarea[name="subscription[Description]"]').val(data.Description).autoGrow();

                        var Number = extractNumberAndSuffix($('input[name="subscription[Number]"]').val());
                        var Suffix = $('input[name="subscription[Number]"]').val().substring(Number.length);

                        if(data.NumberSuffix == '')
                        {
                            $('input[name="subscription[Number]"]').val(Number);
                        }
                        else
                        {
                            $('input[name="subscription[Number]"]').val(Number + ' ' + data.NumberSuffix);
                        }
						
							// If domain, check product description
			            	if($('input[name="PeriodicType"]:checked').val() == "domain"){

                                // Prevent race conditions, if domain was entered before product was loaded.
                                LastDomainEntered = $('input[name="domain[Domain]"]').val() + '.' + $('select[name="domain[Tld]"]').val();
			            		
			            		// correct subscription description
								if($('textarea[name="subscription[Description]"]').val().lastIndexOf(' .' + $('select[name="domain[Tld]"]').val()) >= 0){
									var lastIndex = $('textarea[name="subscription[Description]"]').val().lastIndexOf(' .' + $('select[name="domain[Tld]"]').val());	
									$('textarea[name="subscription[Description]"]').val($('textarea[name="subscription[Description]"]').val().substring(0,lastIndex) + $('textarea[name="subscription[Description]"]').val().substring(lastIndex).replace(' .' + $('select[name="domain[Tld]"]').val(),' '+$('input[name="domain[Domain]"]').val()+'.' + $('select[name="domain[Tld]"]').val())).autoGrow();
								}else{
									var lastIndex = $('textarea[name="subscription[Description]"]').val().lastIndexOf('-');	
									if(lastIndex > 0){ 
										$('textarea[name="subscription[Description]"]').val($('textarea[name="subscription[Description]"]').val().substring(0,lastIndex) + '- ' + $('input[name="domain[Domain]"]').val() + '.' + $('select[name="domain[Tld]"]').val()).autoGrow();
									}else{
										$('textarea[name="subscription[Description]"]').val($('textarea[name="subscription[Description]"]').val() + ' - ' + $('input[name="domain[Domain]"]').val() + '.' + $('select[name="domain[Tld]"]').val()).autoGrow();
									}
								}
							}

						
						$('input[name="subscription[PriceExcl]"]').val(data.PriceExcl).change();
						$('input[name="subscription[PriceIncl]"]').val(data.PriceIncl).change();
						if($('#subscription_div_period:visible').html() != null)
						{
							$('input[name="subscription[Periods]"]').val(1);
							if(data.PricePeriod != ''){
								$('select[name="subscription[Periodic]"]').val(data.PricePeriod);
							}else{
								$('select[name="subscription[Periodic]"]').val('m');
							}
						}
						if(data.TaxPercentage == '0.00'){ data.TaxPercentage = 0; }
						$('select[name="subscription[TaxPercentage]"]').val(data.TaxPercentage).change();
						
						if(data.PricePeriod)
						{
							$('select[name="subscription[Periodic]"]').attr('prev', data.PricePeriod);							
							$('select[name="subscription[Periodic]"]').val(data.PricePeriod).change();

							$('#subscription_price_period').show().find('span').html(data.PricePeriodLong);
						}
						else
						{
							$('#subscription_price_period').show().find('span').html($('select[name="subscription[Periodic]"] option:selected').text());
						}
						
						var TempFunctionName = 'subscription_product_changed_for_type_' + $('input[name="PeriodicType"]:checked').val();
							
						if(typeof window[TempFunctionName] == 'function')
						{
							window[TempFunctionName]();
						}
					}
					// Show hide Custom Period div
					showCustomPeriodPrice('subscription');
			}, "json");
		});

        $(document).on('focus', 'select[name="subscription[Periodic]"]', function () {
            // Set prev if not new or undefined
            $(this).attr('prev', $(this).val());
        });
		
		$('input[name="subscription[StartPeriod]"], input[name="subscription[Periods]"], select[name="subscription[Periodic]"]').change(function(){	
			// If no product, update price incl
			//if($('select[name="subscription[Product]"]').val() == ""){
				$('#subscription_price_period').show().find('span').html($('select[name="subscription[Periodic]"] option:selected').text());	
			//}
			
			// Check if we have a custom price, if not calc price period
			if($(this).attr('name') == 'subscription[Periodic]')
			{
				if($(this).attr('prev') != undefined)
				{
					checkCustomPeriodPrice('subscription', 'Periodic');
				}
			}
			else if($(this).attr('name') == 'subscription[Periods]')
			{
				// Check if we have a custom price, if not calc price period
				checkCustomPeriodPrice('subscription', 'Periods');
			}
			
			result = changePeriodCalc($('select[name="subscription[Periodic]"]').val(), $('input[name="subscription[Periods]"]').val(), $('input[name="subscription[StartPeriod]"]').val());
			$('input[name="subscription[StartPeriod]"]').val(result[0]);
			$('input[name="subscription[EndPeriod]"]').val(result[1]);
			$('#EndPeriod_text').html(result[1]);
		});
		
		// Recalculate NextDate
		$('input[name="subscription[StartPeriod]"]').change(function(){
			
			// First check for # invoice days before start period
			if($('#invoiceDays').html() == 0)
			{
				var nextdate_candidate = $(this).val();
			}
			else
			{
				result = changePeriodCalc('d', -1 * $('#invoiceDays').html(), $(this).val());
				var nextdate_candidate = result[1];
			}
			
			nextdate_candidate = rewrite_date_site2db(nextdate_candidate); 
			
			// Check for invoice collection
			switch($('input[name="InvoiceCollectionDays"]').val())
			{
				case '1': 
					nextdate_candidate = nextdate_candidate.substr(0,6) + '01';
					break;
				case '2': 
					nextdate_candidate = (nextdate_candidate.substr(6,2) >= 15) ? nextdate_candidate.substr(0,6) + '15' : nextdate_candidate.substr(0,6) + '01';
					break;
				default: break;
			}
			
			// If date in the past, make it today
			var Today = new Date();
			day = (Today.getDate()) + "";
			month = (Today.getMonth()+1) + "";
			year = (Today.getFullYear());
			while(month.length < 2){month = "0" + month;}
	        while(day.length < 2){day = "0" + day;}	
	        Today = year + "" + month + "" + day;
			if(nextdate_candidate < Today)
			{
				nextdate_candidate = Today;
			}
			
			// Push to html
			nextdate_candidate = rewrite_date_db2site(nextdate_candidate.substr(0,4), nextdate_candidate.substr(4,2), nextdate_candidate.substr(6,2));
			$('#NextDate_value').html(nextdate_candidate); 
			$('input[name="subscription[NextDate]"]').val(nextdate_candidate);
		});
		
		$('#SubscriptionType_text').click(function(){
			$(this).hide();
			$('select[name=SubscriptionType]').show().focus();
			$('select[name=SubscriptionType]').blur(function(){
				$(this).hide();
				$('#SubscriptionType_text').html(htmlspecialchars($('select[name=SubscriptionType] option:selected').text()) + " " + STATUS_CHANGE_ICON);
				$('#SubscriptionType_text').show();			
			});
		});
		
		$('select[name="SubscriptionType"]').change(function(){
			if($(this).val() == "new"){
				$('#subscription_new').show();
				$('#subscription_existing').hide();
			}else if($(this).val() == "current"){
				$('#subscription_new').show();
				$('#subscription_existing').hide();
			}else{
				$('#subscription_new').hide();
				$('#subscription_existing').show();
				// get existing subscriptions
				get_periodics(null,$('input[name="Debtor"]').val());
			}
			
			// If we have a current subscription, ask what we should do
			ask_disconnect_subscription();
		});
		
		$('select[name="subscription[Existing]"]').change(function(){
			$.post("XMLRequest.php", { action: 'get_periodic', subscription_id: $(this).val(), format: 'true' },function(data){
				if(data.errorSet != undefined){
					$('#subscription_existing_please_select').html(data.errorSet[0].Message).show();
					$('#subscription_existing_details').hide();		
				}else if(data.errorSet == undefined){
					
					$('#subscription_description').html(data.Description);
					$('#subscription_price').html(data.Price_text);
					$('#subscription_period').html(data.Period_text);
					
					if(data.NextDate_text){
						$('#subscription_nextdate').html(data.NextDate_text).show().prev().show();
					}else{
						$('#subscription_nextdate').hide().prev().hide();
					}
					
					if(data.Termation_text){
						$('#subscription_terminated').html(data.Termation_text).show().prev().show();
					}else{
						$('#subscription_terminated').hide().prev().hide();
					}
					
					$('#subscription_existing_please_select').hide();
					$('#subscription_existing_details').show();
				}		
			}, "json");
		});
		
		// Auto calculate price incl and format price excl
		if($('select[name="subscription[TaxPercentage]"]').val() != ""){
			
			
			$('input[name="subscription[PriceExcl]"], input[name="subscription[PriceIncl]"]').keyup(function(){
				calculate_span_subscription_excl_incl();
			});
			$('input[name="subscription[PriceExcl]"], input[name="subscription[PriceIncl]"]').change(function(){
				calculate_span_subscription_excl_incl();
			});
			$('select[name="subscription[TaxPercentage]"]').change(function(){
				calculate_span_subscription_excl_incl();
			});
			
			function calculate_span_subscription_excl_incl(DebtorChanged){

				var InclExclBase = ($('input[name="subscription[PriceIncl]"]:visible').html() != null) ? 'PriceIncl' : 'PriceExcl';
				
				// Select input field
				var PriceInput = $('input[name="subscription['+InclExclBase+']"]');

				// If input field is empty, hide label
				if(!$('input[name="subscription['+InclExclBase+']"]').val())
				{
					if(InclExclBase == 'PriceIncl')
					{
						// Also empty price excl
						$('input[name="subscription[PriceExcl]"]').val('');
					}
					$('.span_subscription_excl_incl').hide().find('span').first().html('');
					return true;
				}
				
				if($('input[name="TaxRate1"]').val() != '')
		    	{
		    		// Hardcoded taxrate (0 or something else)	
		    		currentVAT = (1 + $('input[name="TaxRate1"]').val() * 1);
		    		
		    		// If base was incl
		    		if(currentVAT != 1)
					{
						// incl
						InclExclBase = ($('input[name="subscription[PriceIncl]"]').html() != null) ? 'PriceIncl' : 'PriceExcl';;
					}
					else
					{
						// now excl
						InclExclBase = 'PriceExcl';
					}
	    		}
	    		else
	    		{
	    			// Calc price for other option
	    			currentVAT = (1 + $('select[name="subscription[TaxPercentage]"]').val() * 1);
	    			
					if(currentVAT > 1 && InclExclBase == 'PriceExcl' && $('input[name="subscription[PriceIncl]"]').html() != null)
	    			{
	    				InclExclBase = 'PriceIncl';
	    			}
	    			
	    		}
	    		
	    		
	    		//DebtorChanged
	    		if(DebtorChanged === true)
	    		{
	    			$('input[name="subscription[PriceIncl]"]').val(formatAsMoney(deformatAsMoney($('input[name="subscription[PriceExcl]"]').val()) * currentVAT));
	    		}
	    		else if(InclExclBase == 'PriceIncl')
    			{
    				$('input[name="subscription[PriceExcl]"]').val(formatAsMoney(deformatAsMoney($('input[name="subscription[PriceIncl]"]').val()) / currentVAT));
    			}
    			else
    			{
    				$('input[name="subscription[PriceIncl]"]').val(formatAsMoney(deformatAsMoney($('input[name="subscription[PriceExcl]"]').val()) * currentVAT));
    			}
	    		
	    		if(InclExclBase == 'PriceIncl')
	    		{
	    			$('input[name="subscription[PriceIncl]"]').show();
					$('input[name="subscription[PriceExcl]"]').hide();
					$('.span_vat_incl').show();
					$('.span_vat_excl').hide();
	    		}
	    		else
	    		{
	    			$('input[name="subscription[PriceIncl]"]').hide();
					$('input[name="subscription[PriceExcl]"]').show();
					$('.span_vat_incl').hide();
					$('.span_vat_excl').show();
	    		}
				
				if(currentVAT == 1)
				{
					$('.span_subscription_excl_incl').hide().find('span').first().html('');
				}
				else
				{
					if(InclExclBase == 'PriceIncl')
	    			{
	    				// Excl
						$('.span_subscription_excl_incl').show().find('span').first().html(formatAsMoney(formatAsFloat(deformatAsMoney($('input[name="subscription['+InclExclBase+']"]').val()),2) / currentVAT));
    				}
    				else
    				{
    					// Incl
						$('.span_subscription_excl_incl').show().find('span').first().html(formatAsMoney(formatAsFloat(deformatAsMoney($('input[name="subscription['+InclExclBase+']"]').val()),2) * currentVAT));
    				}
				}
				return true;
			}
		}
		
		// contract data tab
		$('input[name="ContractPeriod"]').change(function(){
			if($(this).val() == 'custom'){
				$('#custom_contract_period').show();
				$('#div_contract_period').show();
				$('select[name="subscription[ContractPeriodic]"]').change();
			}else{
				$('#custom_contract_period').hide();
				
				// Also adjust contract period, so we can change end contract
				$('input[name="subscription[ContractPeriods]"]').val($('input[name="subscription[Periods]"]').val());
				$('select[name="subscription[ContractPeriodic]"]').val($('select[name="subscription[Periodic]"]').val()).change();
				
				// Empty values if we are on add
				if($('input[name="subscription[Identifier]"]').val() == undefined){
					$('#div_contract_period').hide();
					$('input[name="subscription[StartContract]"]').val('');
					$('input[name="subscription[EndContract]"]').val('');
				}
			}
		});
		
		// If contract period is changed, change end contract
		$('input[name="subscription[ContractPeriods]"], select[name="subscription[ContractPeriodic]"]').change(function(){
			// Does ContractRenewalDate exist?
			if($('input[name="subscription[ContractRenewalDate]"]').val() != undefined){
				result = changePeriodCalc($('select[name="subscription[ContractPeriodic]"]').val(), $('input[name="subscription[ContractPeriods]"]').val(), $('input[name="subscription[ContractRenewalDate]"]').val());
				$('input[name="subscription[ContractRenewalDate]"]').val(result[0]);
				$('input[name="subscription[EndContract]"]').val(result[1]);
			}else{
				result = changePeriodCalc($('select[name="subscription[ContractPeriodic]"]').val(), $('input[name="subscription[ContractPeriods]"]').val(), $('input[name="subscription[StartContract]"]').val());
				$('input[name="subscription[StartContract]"]').val(result[0]);
				$('input[name="subscription[EndContract]"]').val(result[1]);
			}		
		});
		// Initiate above call if possible start changes
		$('input[name="subscription[StartContract]"], input[name="subscription[ContractRenewalDate]"]').change(function(){ $('input[name="subscription[ContractPeriods]"]').change(); });
				
		// Initial call
		$('select[name="subscription[Periodic]"]').change();
		if($('input[name="subscription[Product]"]').val())
		{
			showCustomPeriodPrice('subscription');
		}
		/********************************************************************
		 * END: Subscription
		 *******************************************************************/		
		
		/********************************************************************
		 * BEGIN: Domain
		 *******************************************************************/
		
		if($('#div_for_hostingsearch')){

			$('#hosting_search_icon').click(function(){
				
					$('#div_for_hostingsearch').load('XMLRequest.php?action=searchhosting&id=' + $('input[name="Debtor"]').val(), function(){ $('#hosting_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto', close: function(event,ui){ $('#hosting_search').dialog('destroy').remove(); }}); });

                    $(document).off('click', '#hosting_search .dialog_select_hover');
                    $(document).on('click', '#hosting_search .dialog_select_hover', function() {
						SelectedHosting = $(this).attr('id').replace('hostingID_','');
						$('select[name="domain[HostingID]"]').val(SelectedHosting).change();
						
						$('#hosting_search').dialog('close');
	
						ajaxSave('search.hosting','searchfor','');
					});
	
			});
		}
		
		if($('#div_for_handlesearch')){

			$('.handle_search_icon').click(function(){
					var HandleSearchFrom = $(this).attr('id').replace('handle_search_icon_','');
					$('#div_for_handlesearch').load('XMLRequest.php?action=searchhandle&debtor_id=' + $('input[name="Debtor"]').val() + '&registrar_id=' + $('select[name="domain[Registrar]"]').val() , function(){ $('#handle_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto', close: function(event,ui){ $('#handle_search').dialog('destroy').remove(); }}); });

                    $(document).off('click', '#handle_search .dialog_select_hover');
                    $(document).on('click', '#handle_search .dialog_select_hover', function() {
						SelectedHandle = $(this).attr('id').replace('tr_','');
						$('select[name="domain['+HandleSearchFrom+'Handle]"]').val(SelectedHandle);
						$('#handle_search').dialog('close');
	
						ajaxSave('search.handle','searchfor','');
					});
	
			});
		}
		
		
		// Change hosting ID
		$('select[name="domain[HostingID]"]').change(function(){
			
			$('#domain_hosting_create').addClass('hide');
			// Get nameservers from hosting or registrar
			if($(this).val() > 0){
				$.post("XMLRequest.php", { action: "server_information_by_account_id", id: $(this).val()},
					function(data){
						if(data.resultSet == undefined && data.errorSet != undefined){
								
						}else if(data.errorSet == undefined){
							
							$('input[name="HostingAccountDomain"]').val(data.resultSet[0].HostingAccountDomain);
							$('input[name="ServerPanel"]').val(data.resultSet[0].Panel);
							
							var newDomain = $('input[name="domain[Domain]"]').val().toLowerCase()+'.'+$('select[name="domain[Tld]"]').val();
							
							if($('#servicedomaintype') != null && data.resultSet[0].HostingAccountDomain.toLowerCase() != newDomain && data.resultSet[0].Panel != ''){
								$('#servicedomaintype').text(data.resultSet[0].DomainTypeTranslated);
								$('#domain_hosting_create').removeClass('hide');
							}
							
							if(data.resultSet[0].DNS1 && NameserverFromDebtor == false){ 
								
								// Check if we are on add or edit, in case of edit, ask what we need to do if nameservers are different
								if($('input[name="domain_id"]').val() > 0 && (data.resultSet[0].DNS1 != $('input[name="domain[DNS1]"]').val() || data.resultSet[0].DNS2 != $('input[name="domain[DNS2]"]').val() || data.resultSet[0].DNS3 != $('input[name="domain[DNS3]"]').val())){
									NameserverFromHosting = true;
									
									// Open dialog
									ask_nameserver_change(data.resultSet[0].DNS1, data.resultSet[0].DNS2, data.resultSet[0].DNS3, __LANG_SERVER);
									
								}else if($('input[name="domain_id"]').val() > 0){
									// We are on edit page, but nameservers are the same, so do nothing
									NameserverFromHosting = false;
								}else{
									// We are on add page
									NameserverFromHosting = true;
									
									// The change link has been used, so perhaps custom nameservers are entered
									if($('#domain_nameservers_input:visible').html() != null){
										// Open dialog
										ask_nameserver_change(data.resultSet[0].DNS1, data.resultSet[0].DNS2, data.resultSet[0].DNS3, __LANG_REGISTRAR);
									}else{
										// No custom nameservers, so change them
				                		$('input[name="domain[DNS1]"]').val(data.resultSet[0].DNS1);
				                		$('input[name="domain[DNS2]"]').val(data.resultSet[0].DNS2);
				                		$('input[name="domain[DNS3]"]').val(data.resultSet[0].DNS3);
				                		$('#domain_nameservers_text').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val()).change();
        							}
								}		                		
		                	}else if($('input[name="domain_id"]').val() == 0 && NameserverFromDebtor == false){
		                		// Get registrar nameservers, if on add page
		                		NameserverFromHosting = false;
		                		getRegistrarInformation($('select[name="domain[Registrar]"]').val());
		                	}
						}
				}, "json");
			}else if($('input[name="domain_id"]').val() == 0){
				// Get registrar nameservers
  				getRegistrarInformation($('select[name="domain[Registrar]"]').val());
				NameserverFromHosting = false;		
			}else{
				NameserverFromHosting = false;
			}
		})
						
		$('select[name="domain[Status]"]').change(function(){
			if($(this).val() == 4){
				$('#domain_registration_dates').show();
			}else{
				$('#domain_registration_dates').hide();
			}
			
			if($(this).val() >= 4){
				$('#domain_domain_create').hide();
			}else{
				$('#domain_domain_create').show();
			}
		});
        
        $('input[name="domain[AuthKey]"]').change(function()
        {
            if($(this).val() == '')
            {
                $('#direct_create_transfer').hide();
                $('#direct_create_register').show();
            }
            else
            {
                $('#direct_create_register').hide();
                $('#direct_create_transfer').show();
            }    
        });
        
		$('input[name="domain[RegistrationDate]"]').change(function(){
			
			if($('input[name="domain_id"]').val() == 0 && $('select[name="domain[Status]"]').val() == 4){
				$('input[name="subscription[StartPeriod]"]').val($(this).val()).change();
				
				// Change the NextInvoice to RegistrationDate
				$('#NextDate_value').html($(this).val()); 
				$('input[name="subscription[NextDate]"]').val($(this).val()); 
				
			}
			
			$('input[name="domain[ExpirationDate]"]').val(EndPeriod($('input[name="domain[RegistrationDate]"]').val(), 'j', '1'));
			
			
		});
		$('#domain_change_registrar').click(function(){
			$(this).hide(); $(this).parent().next().hide(); $('select[name="domain[Registrar]"]').show();
		});
		$('select[name="domain[Registrar]"]').change(function(){
			$('#domain_registrar_text').html($('select[name="domain[Registrar]"] option:selected').text());
			getRegistrarInformation($('select[name="domain[Registrar]"] option:selected').val());
		});
		
		// Nameservers
		$("#domain_change_nameservers").click(function(){
			toggleDomainNameserverForm('show');
		});
		
		// Check for IP's
		$('input[name="domain[DNS1]"], input[name="domain[DNS2]"], input[name="domain[DNS3]"]').change(function(){
			$('#domain_nameservers_text').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val()).change();
			
			var DNS_field_name = $(this).attr('name');
			$.get("XMLRequest.php", { action: "getIPFromHost", hostname: $(this).val()},
				function(data){
		            if(data){
	
		            	$('input[name="'+DNS_field_name.replace(']','IP]')+'"]').val(data);
		            }else{
		            	$('input[name="'+DNS_field_name.replace(']','IP]')+'"]').val('');
		            }
			}, "html");
		});
		
		// Handles
		$('input[name="domain[ownerc]"]').click(function(){
			switch($('input[name="domain[ownerc]"]:checked').val()){
				case 'debtor': $('#domain_contact_owner').hide(); break;
				case 'custom': $('#domain_contact_owner').show(); break;
			}
		});
		$('input[name="domain[adminc]"]').click(function(){
			switch($('input[name="domain[adminc]"]:checked').val()){
				case 'owner': $('#domain_contact_admin').hide(); break;
				case 'handle': $('#domain_contact_admin').hide(); break;
				case 'custom': $('#domain_contact_admin').show(); break;
			}
		});
		$('input[name="domain[techc]"]').click(function(){
			switch($('input[name="domain[techc]"]:checked').val()){
				case 'owner': $('#domain_contact_tech').hide(); break;
				case 'handle': $('#domain_contact_tech').hide(); if($('input[name="domain[techc_id]"]').val()){ $('select[name="domain[techHandle]"]').val($('input[name="domain[techc_id]"]').val());  } break;
				case 'custom': $('#domain_contact_tech').show(); break;
			}
		});

		$('#domain_contact_new_owner, #domain_contact_new_owner_2').click(function(){ open_handle_dialog('owner','domain'); });
		$('#domain_contact_new_admin').click(function(){ open_handle_dialog('admin','domain'); });
		$('#domain_contact_new_tech').click(function(){ open_handle_dialog('tech','domain'); });
		
		// What if TLD changes
		$('select[name="domain[Tld]"]').change(function(){
			
			$.post("XMLRequest.php", { action: "get_product_by_tld", tld: $(this).val()}, function(data){
				
				// Get product for TLD (only if new subscription)
				if($('input[name="subscription_invoice"]').prop('checked') && $('select[name="SubscriptionType"]').val() == 'new'){
					if(data.errorSet == undefined && data.ProductID > 0){
						$('input[name="subscription[Product]"]').val(data.ProductID).change();
					}else{
						$('input[name="subscription[Product]"]').val('').change();
					}
				}
				
				if(data.errorSet == undefined && data.Registrar > 0){
					$('select[name="domain[Registrar]"]').val(data.Registrar).change();
				}
				
			}, "json");
		});
		
		// On change domain/tld, check availability
		$('input[name="domain[Domain]"]').keyup(function(){
			if($('input[name="domain[Domain]"]').val())
			{
				$('#domain_status a').show();
			}else{
				$('#domain_status a').hide();	
			}
			$('#domain_status span').hide();
			
			var hostingAccountDomainName = $('input[name="domain[Domain]"]').val().toLowerCase()+'.'+$('select[name="domain[Tld]"]').val().toLowerCase();
			if(hostingAccountDomainName == $('input[name="HostingAccountDomain"]').val().toLowerCase() || $('select[name="domain[HostingID]"]').val() == 0 || $('input[name="ServerPanel"]').val() == ''){
				if($('#domain_hosting_create').hasClass('hide') == false){
					$('#domain_hosting_create').addClass('hide');
				}
			}else{
				if($('#domain_hosting_create').hasClass('hide') == true){
					$('#domain_hosting_create').removeClass('hide');
				}
			}
		});
		$('input[name="domain[Domain]"], select[name="domain[Tld]"]').change(function(){
			if($('input[name="domain[Domain]"]').val())
			{
				$('#domain_status a').show();
			}else{
				$('#domain_status a').hide();	
			}
			
			var hostingAccountDomainName = $('input[name="domain[Domain]"]').val().toLowerCase()+'.'+$('select[name="domain[Tld]"]').val().toLowerCase();
			if(hostingAccountDomainName == $('input[name="HostingAccountDomain"]').val().toLowerCase() || $('select[name="domain[HostingID]"]').val() == 0 || $('input[name="ServerPanel"]').val() == ''){
				if($('#domain_hosting_create').hasClass('hide') == false){
					$('#domain_hosting_create').addClass('hide');
				}
			}else{
				if($('#domain_hosting_create').hasClass('hide') == true){
					$('#domain_hosting_create').removeClass('hide');
				}
			}

			$('#domain_status span').hide();
		});
		
		$('#domain_status a').click(function(){
			$(this).hide();
			$('#domain_status span').removeClass().html('<img src="images/icon_circle_loader_grey.gif" alt="" class="ico inline" />').show();
			
			$.post("XMLRequest.php", { action: "check_domain_availability", sld: $('input[name="domain[Domain]"]').val(), tld: $('select[name="domain[Tld]"]').val(), registrar: $('select[name="domain[Registrar]"]').val()}, function(data){
			
	            if(data.result == 'unknown' || data.result == 'invalid')
	            {
	            	$('#domain_status span').addClass('loading_orange').html(data.msg).show();
	            }
	            else if(data.result == 'register')
	            {
	            	$('#domain_status span').addClass('loading_green').html(data.msg).show();
	            }
	            else if(data.result == 'transfer')
	            {
	            	$('#domain_status span').addClass('loading_red').html(data.msg).show();
	            }
			}, "json");
		});
		
		
		// On change domain name, update subscription info
		var LastDomainEntered = ($('input[name="domain_id"]').val() == 'error' && $('input[name="domain[Domain]"]').val()) ? $('input[name="domain[Domain]"]').val() + '.' + $('select[name="domain[Tld]"]').val() : '';
        var availableTLDs     = '';
		
		$('input[name="domain[Domain]"]').change(function(){
            
            // get all the available tld's from the select and build string for regexp
            if(availableTLDs == '')
            {
                $('select[name="domain[Tld]"]').children('option').each( function()
                    {
                        availableTLDs += (availableTLDs == '') ? $(this).val() : '|' + $(this).val();
                    }
                );
            }
            
            // if the domain string ends with one of the tld's from the select, set the matching tld and remove the tld from the domain
            var re = new RegExp('\\.+(' + availableTLDs + ')$');
            var result = re.exec($('input[name="domain[Domain]"]').val());
            if(result && result != null)
            {
                $('select[name="domain[Tld]"]').val(result[1]).change();
                $('input[name="domain[Domain]"]').val($('input[name="domain[Domain]"]').val().replace(result[0], ''));
            }
            
			// Do we have a new subscription?
			if($('input[name="subscription_invoice"]').prop('checked') && $('select[name="SubscriptionType"]').val() == 'new'){
				// correct subscription description
				
				// Is domain changed after initial creation? If yes, replace!
				if(LastDomainEntered){
					$('textarea[name="subscription[Description]"]').val($('textarea[name="subscription[Description]"]').val().replace(LastDomainEntered, '.' + $('select[name="domain[Tld]"]').val())).autoGrow();
				}
				if($('textarea[name="subscription[Description]"]').val().lastIndexOf(' .' + $('select[name="domain[Tld]"]').val()) >= 0){
					var lastIndex = $('textarea[name="subscription[Description]"]').val().lastIndexOf(' .' + $('select[name="domain[Tld]"]').val());	
					$('textarea[name="subscription[Description]"]').val($('textarea[name="subscription[Description]"]').val().substring(0,lastIndex) + $('textarea[name="subscription[Description]"]').val().substring(lastIndex).replace(' .' + $('select[name="domain[Tld]"]').val(),' '+$('input[name="domain[Domain]"]').val()+'.' + $('select[name="domain[Tld]"]').val())).autoGrow();
				}else{
					var lastIndex = $('textarea[name="subscription[Description]"]').val().lastIndexOf('-');	
					if(lastIndex > 0){ 
						$('textarea[name="subscription[Description]"]').val($('textarea[name="subscription[Description]"]').val().substring(0,lastIndex) + '- ' + $('input[name="domain[Domain]"]').val() + '.' + $('select[name="domain[Tld]"]').val()).autoGrow();
					}else{
						$('textarea[name="subscription[Description]"]').val($('textarea[name="subscription[Description]"]').val() + ' - ' + $('input[name="domain[Domain]"]').val() + '.' + $('select[name="domain[Tld]"]').val()).autoGrow();
					}
				}
				
				LastDomainEntered = $('input[name="domain[Domain]"]').val() + '.' + $('select[name="domain[Tld]"]').val();
			}			
		});
		
		// Load if periodictype is already selected
		if($('input[name="PeriodicType"]:checked').val() == "domain" && $('input[name="Debtor"]').val() > 0){
			$('#domain_registrar_text').html($('select[name="domain[Registrar]"] option:selected').text());
			$('#domain_nameservers_text').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val()).change();
			
			// If add-page, load tld information, registrar info, handles, nameservers, product info
			if($('input[name="domain_id"]').val() == 0){
				$('select[name="domain[Tld]"]').change();
			// If edit-page, only load handles
			}else{
				$('#domain_registrar_text').html($('select[name="domain[Registrar]"] option:selected').text());
				getRegistrarInformation($('select[name="domain[Registrar]"] option:selected').val(), true);
			}
		}
		
		/********************************************************************
		 * END: Domain
		 *******************************************************************/	
		
		/********************************************************************
		 * BEGIN: Hosting
		 *******************************************************************/
		if($('#div_for_domainsearch')){

			$('#domain_search_icon').click(function(){
				
					$('#div_for_domainsearch').load('XMLRequest.php?action=searchdomain&id=' + $('input[name="Debtor"]').val(), function(){ $('#domain_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto', close: function(event,ui){ $('#domain_search').dialog('destroy').remove(); }});});

                    $(document).off('click', '#domain_search .dialog_select_hover');
                    $(document).on('click', '#domain_search .dialog_select_hover', function() {
						SelectedDomain = $(this).find('td').first().html();
						$('input[name="hosting[Domain]"]').val(SelectedDomain);
						// Also set accountname
						setAccountName();
						$('#hosting_domain_add_in_software').hide();
						
						$('#domain_search').dialog('close');
	
						ajaxSave('search.domain','searchfor','');
					});
	
			});
		}
		
		// When to check for changing account name
		$('input[name="hosting[Domain]"], input[name="Debtor"]').change(function(){ setAccountName();});
		$('input[name="hosting[Domain]"]').change(function(){ 
			// Check domain in software
			is_domain_in_software($(this).val());
		});
		
		$('input[name="PeriodicType"]').click(function(){ setAccountName(); });
		
		$('#hosting_password').click(function(){
			$.post('XMLRequest.php', { action: 'generate_hosting_password'}, function(data){
				$('input[name="hosting[Password]"]').val(data);
			},"html");
		});

		$('select[name="hosting[Package]"]').change(function(){

			$.post('XMLRequest.php', { action: 'package_information', id: $(this).val() }, function(data){
				if(data.resultSet != undefined && data.resultSet[0].PackageName)
				{
                    // Get product for package (only if new subscription)
                    if($('input[name="subscription_invoice"]').prop('checked') && $('select[name="SubscriptionType"]').val() == 'new')
                    {
                        if(data.resultSet[0].Product != undefined && data.resultSet[0].Product > 0)
                        {
                            $('input[name="subscription[Product]"]').val(data.resultSet[0].Product).change();
                        }
                        else
                        {
                            // if we select a package which has no product
                            $('input[name="subscription[Product]"]').val('').change();
                        }
                    }

					// Select the associated server
					if(data.resultSet[0].Server > 0){
						$('select[name="hosting[Server]"]').val(data.resultSet[0].Server);
					}
					
					if(data.resultSet[0].uDiscSpace == 1){ var DiscSpace = __LANG_UNLIMITED; }else{ var DiscSpace = data.resultSet[0].DiscSpace; }
					if(data.resultSet[0].uBandWidth == 1){ var BandWidth = __LANG_UNLIMITED; }else{ var BandWidth = data.resultSet[0].BandWidth; }
					
					if(data.resultSet[0].uDomains == 1){ var Domains = __LANG_UNLIMITED; }else{ var Domains = data.resultSet[0].Domains }
					if(data.resultSet[0].uMySQLDatabases == 1){ var MySQLDatabases = __LANG_UNLIMITED; }else{ var MySQLDatabases = data.resultSet[0].MySQLDatabases }
					if(data.resultSet[0].uEmailAccounts == 1){ var EmailAccounts = __LANG_UNLIMITED; }else{ var EmailAccounts = data.resultSet[0].EmailAccounts }
					
					
					$('#hosting_discspace').html(DiscSpace);
					$('#hosting_traffic').html(BandWidth);
					$('#hosting_domains').html(Domains);
					$('#hosting_databases').html(MySQLDatabases);
					$('#hosting_emailaccounts').html(EmailAccounts);
					
					$('#account_usage_no_package').hide();
					$('#account_usage_for_package').show();

				}else{
					$('#hosting_discspace').html(0 + " MB");
					$('#hosting_traffic').html(0 + " MB");
					$('#hosting_domains').html(0);
					$('#hosting_databases').html(0);
					$('#hosting_emailaccounts').html(0);
					$('#account_usage_no_package').show();
					$('#account_usage_for_package').hide();
				}
			},"json");
            
            // only show checkbox when we change to another package on the same server
            if($('input[name="hosting[changePackageOnServer]"]').length > 0)
            {
                if($(this).val() != $('input[name="hosting[oldPackage]"]').val() && 
                   $(this).val() != '' && 
                   $('select[name="hosting[Server]"]').val() == $('input[name="hosting[oldServer]"]').val())
                {
                    $('#changePackageOnServer').show();
                }
                else
                {
                    $('#changePackageOnServer').hide();
                }    
            }
			
		});
		
		$('select[name="hosting[Server]"]').change(function(){
			var package_id = 0;
			$.post('XMLRequest.php', { action: 'package_options_for_server', id: $(this).val(), package_id: package_id  }, function(data){
				$('select[name="hosting[Package]"]').html(data).change();
			},"html");
            
            // when we switch to another server, disable the checkbox to change the package on the server
            if($('input[name="hosting[changePackageOnServer]"]').length > 0)
            {
                if($(this).val() == $('input[name="hosting[oldServer]"]').val())
                {
                    $('#changePackageOnServer').show();
                }
                else
                {
                    $('#changePackageOnServer').hide();
                }    
            }
		});
		
		$('select[name="hosting[Status]"]').change(function(){
			if($(this).val() >= 4 || $(this).val() < 0){
				$('input[name="hosting[DirectCreation]"]').prop('checked',false);
				$('#hosting_directcreation_div').hide();
			}else{
				$('#hosting_directcreation_div').show();
			}	
		});
		
		$('#hosting_change_server_and_package').click(function(){
			$('#hosting_server_div_text').hide();
			$('#hosting_server_div_modify').show();
		});
		
		if($('select[name="hosting[Server]"]').val() != $('input[name="hosting[oldServer]"]').val() || $('select[name="hosting[Package]"]').val() != $('input[name="hosting[oldPackage]"]').val()){
			$('#hosting_server_div_text').hide();
			$('#hosting_server_div_modify').show();	
		}
		
		// Initial

		// Load if periodictype is already selected
		if($('input[name="PeriodicType"]:checked').val() == "hosting"){

            if($('select[name="hosting[Package]"]').val() > 0)
            {
                $('select[name="hosting[Package]"]').change();
            }

			// Only for new pages
			if($('input[name="hosting_id"]').val() == ""){
				$('select[name="hosting[Server]"]').change();
			}else if($('input[name="hosting_id"]').val() > 0){
				// Check domain in software
                is_domain_in_software($('input[name="hosting[Domain]"]').val());
			}
		}
		
		/********************************************************************
		 * END: Hosting
		 *******************************************************************/
	}
	
	// Show page
	/*
	if($('#terminate_subscription').html() != null){
		$('#terminate_subscription').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

		$('#terminate_subscription_btn').click(function(){
			document.form_terminate.submit();	
		});
	}
	*/
	
	if($('#delete_subscription').html() != null){
		$('#delete_subscription').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
		$('#delete_subscription.autoopen').dialog('open');	
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_subscription_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_subscription_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_subscription_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
});

function is_domain_in_software(domain){
	// Check domain in software
	$.get('XMLRequest.php', { action: 'is_domain_in_software', domain: domain }, function(data){
		if(data == 'yes'){
			// Domain is known
			$('#hosting_domain_add_in_software').hide();
		}else{
			// Domain is unknown
			$('#hosting_domain_add_in_software').show();
		}
	},"html");
}

function setAccountName()
{
	var number_option = $('input[name="account_generation"]').val();
	var account_status = $('select[name="hosting[Status]"]').val();
	
	// If auto numbering or account is already active or blocked
	if(number_option == 1 || account_status >= 4 || $('input[name="PeriodicType"]:checked').val() != 'hosting')
	{
		return true;
	}
	else
	{
		$.post("XMLRequest.php", { action: "generate_accountname", method: number_option, debtor: $('input[name="Debtor"]').val(), domain: $('input[name="hosting[Domain]"]').val()},
			function(data){
				if(data.resultSet == undefined && data.errorSet != undefined)
					$('input[name="hosting[Username]"]').val(data.errorSet[0].Message);				
				else if(data.errorSet == undefined)
					$('input[name="hosting[Username]"]').val(data.resultSet[0].Name);
		}, "json");
	}
}

function get_periodics(periodic_id,debtor_id){
	$.post("XMLRequest.php", { action: 'get_periodics', periodic_id: periodic_id, debtor_id: debtor_id },function(data){
		$('select[name="subscription[Existing]"]').html(data);
		
		if($('input[name="subscription_existing_hidden"]').val() > 0){
			$('select[name="subscription[Existing]"]').val($('input[name="subscription_existing_hidden"]').val()).change();
		}
	}, "html");
}

function get_hosting_accounts(debtor_id,first_account_selected){
	$.post("XMLRequest.php", { action: "debtor_hosting_select", debtor_id: debtor_id, first_account_selected: first_account_selected},function(data){
		$('select[name="domain[HostingID]"]').html(data);
		if(first_account_selected === true){
			$('select[name="domain[HostingID]"]').change();
		}
	});
}

function getRegistrarInformation(registrar_id, loading){
	if(registrar_id > 0){
		$.post("XMLRequest.php", { action: "registrar_information", id: registrar_id},
			function(data){
				if(data.resultSet == undefined && data.errorSet != undefined){
									
				}else if(data.errorSet == undefined){
					// Only look for nameservers if no hosting nameservers are selected and registrar has nameservers
					if(NameserverFromDebtor == false && NameserverFromHosting == false && data.resultSet[0].DNS1){
	                	// In case of loading on edit page, don't do nameserver-changes
	                	if(loading == true && $('input[name="domain_id"]').val() != ''){
	                		
	                	}else{

							// Check if we are on add or edit, in case of edit, ask what we need to do if nameservers are different
							if($('input[name="domain_id"]').val() > 0 && (data.resultSet[0].DNS1 != $('input[name="domain[DNS1]"]').val() || data.resultSet[0].DNS2 != $('input[name="domain[DNS2]"]').val() || data.resultSet[0].DNS3 != $('input[name="domain[DNS3]"]').val())){
							
								// Open dialog
								ask_nameserver_change(data.resultSet[0].DNS1, data.resultSet[0].DNS2, data.resultSet[0].DNS3, __LANG_REGISTRAR);
								
							}else if($('input[name="domain_id"]').val() > 0){
								// We are on edit page, but nameservers are the same, so do nothing
							}else if(data.resultSet[0].DNS1){
								// We are on add page
								
								// The change link has been used, so perhaps custom nameservers are entered
								if($('#domain_nameservers_input:visible').html() != null){
									// Open dialog
									ask_nameserver_change(data.resultSet[0].DNS1, data.resultSet[0].DNS2, data.resultSet[0].DNS3, __LANG_REGISTRAR);
								}else{
									// No custom nameservers, so change them								
			                		$('input[name="domain[DNS1]"]').val(data.resultSet[0].DNS1);
			                		$('input[name="domain[DNS2]"]').val(data.resultSet[0].DNS2);
			                		$('input[name="domain[DNS3]"]').val(data.resultSet[0].DNS3);
			                		$('#domain_nameservers_text').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val()).change();
			                		toggleDomainNameserverForm('hide');
      							}
		                	}else{
		                		$('input[name="domain[DNS1]"]').val('');
		                		$('input[name="domain[DNS2]"]').val('');
		                		$('input[name="domain[DNS3]"]').val('');
		                		$('#domain_nameservers_text').html('').change();
		                		toggleDomainNameserverForm('show');
		                	}
       					}
                	}
					
					// Get extra fields for this registrar
					getExtraFields(registrar_id, $('select[name="domain[Tld]"] option:selected').val());
					
					// Handle options
					var current_handle_owner = ($('select[name="domain[ownerHandle]"]').val() > 0 && $('input[name="domain[ownerc]"]:checked').val() == "custom") ? $('select[name="domain[ownerHandle]"]').val() : $('input[name="domain[ownerHandle_hidden]"]').val();
					var current_handle_admin = ($('select[name="domain[adminHandle]"]').val() > 0 && $('input[name="domain[adminc]"]:checked').val() == "custom") ? $('select[name="domain[adminHandle]"]').val() : $('input[name="domain[adminHandle_hidden]"]').val();
					var current_handle_tech = ($('select[name="domain[techHandle]"]').val() > 0 && $('input[name="domain[techc]"]:checked').val() == "custom") ? $('select[name="domain[techHandle]"]').val() : $('input[name="domain[techHandle_hidden]"]').val();
					
					// Admin handle
					if(current_handle_admin > 0){
						// If a handle exists, check what to do
						if(data.resultSet[0].domain_admin_customer == 1){
							$('input[name="domain[adminc]"][value="owner"]').parent('span').show();
							$('input[name="domain[adminc]"][value="handle"]').parent('span').hide();
							$('input[name="domain[adminc_id]"]').val('');
							
							// If selected handle is equal to default handle, select this option
							if(current_handle_admin == current_handle_owner){
								$('input[name="domain[adminc]"][value="owner"]').prop('checked',true).click();
							}else{
								$('input[name="domain[adminc]"][value="custom"]').prop('checked',true).click();
							}
						}else{
							//$('input[name="domain[adminc]"][value="owner"]').parent('span').hide();
							$('input[name="domain[adminc]"][value="handle"]').parent('span').show();
							$('input[name="domain[adminc_id]"]').val(data.resultSet[0].domain_admin_handle);
							
							
							// If selected handle is equal to default handle, select this option
							if(data.resultSet[0].domain_admin_handle == current_handle_admin){
								$('input[name="domain[adminc]"][value="handle"]').prop('checked',true).click();
							}else if(current_handle_admin == current_handle_owner){
								$('input[name="domain[adminc]"][value="owner"]').prop('checked',true).click();
							}else{
								$('input[name="domain[adminc]"][value="custom"]').prop('checked',true).click();
							}
						}
					}else{
						// If no handle is selected, so adding a domain
						if(data.resultSet[0].domain_admin_customer == 1){
							$('input[name="domain[adminc]"][value="owner"]').click().parent('span').show();
							$('input[name="domain[adminc]"][value="handle"]').parent('span').hide();
							$('input[name="domain[adminc_id]"]').val('');
						}else{
							//$('input[name="domain[adminc]"][value="owner"]').parent('span').hide();
							$('input[name="domain[adminc]"][value="handle"]').click().parent('span').show();
							$('input[name="domain[adminc_id]"]').val(data.resultSet[0].domain_admin_handle);
						}
						
						// If form was already posted, fix
						if($('input[name="posted_adminc"]').val() == 'owner'){
							$('input[name="domain[adminc]"][value="owner"]').click();
						}
					}
					
					// Tech handle
					if(current_handle_tech > 0){
						// If a handle exists, check what to do
						if(data.resultSet[0].domain_tech_customer == 1){
							$('input[name="domain[techc]"][value="owner"]').parent('span').show();
							$('input[name="domain[techc]"][value="handle"]').parent('span').hide();
							$('input[name="domain[techc_id]"]').val('');
							
							// If selected handle is equal to default handle, select this option
							if(current_handle_tech == current_handle_owner){
								$('input[name="domain[techc]"][value="owner"]').prop('checked',true).click();
							}else{
								$('input[name="domain[techc]"][value="custom"]').prop('checked',true).click();
							}
						}else{
							//$('input[name="domain[techc]"][value="owner"]').parent('span').hide();
							$('input[name="domain[techc]"][value="handle"]').parent('span').show();
							$('input[name="domain[techc_id]"]').val(data.resultSet[0].domain_tech_handle);
						
							// If selected handle is equal to default handle, select this option
							if(data.resultSet[0].domain_tech_handle == current_handle_tech){
								$('input[name="domain[techc]"][value="handle"]').prop('checked',true).click();
							}else if(current_handle_tech == current_handle_owner){
								$('input[name="domain[techc]"][value="owner"]').prop('checked',true).click();
							}else{
								$('input[name="domain[techc]"][value="custom"]').prop('checked',true).click();
							}
						}
					}else{
						// If no handle is selected, so adding a domain
						if(data.resultSet[0].domain_tech_customer == 1){
							$('input[name="domain[techc]"][value="owner"]').click().parent('span').show();
							$('input[name="domain[techc]"][value="handle"]').parent('span').hide();
							$('input[name="domain[techc_id]"]').val('');
						}else{
							//$('input[name="domain[techc]"][value="owner"]').parent('span').hide();
							$('input[name="domain[techc]"][value="handle"]').click().parent('span').show();
							$('input[name="domain[techc_id]"]').val(data.resultSet[0].domain_tech_handle);
						}
						
						// If form was already posted, fix
						if($('input[name="posted_techc"]').val() == 'owner'){
							$('input[name="domain[techc]"][value="owner"]').click();
						}
					}
			
					// Get select form elements
					getHandleSelect($('input[name="Debtor"]').val(), $('select[name="domain[Registrar]"]').val(), 'domain');
					
				}
		}, "json");
	}
}

function getHandleSelect(debtor_id, registrar_id, field_prefix, new_handle_type, new_handle_id){
	
	var stripped_field_prefix = field_prefix.replace('module[','').replace(']','');
	
	$.post("XMLRequest.php", { action: "get_select_handles", debtor_id: debtor_id, registrar_id: registrar_id},
			function(data){
				// Do we need to get the current values?
				var current_handle_owner = ($('select[name="'+field_prefix+'[ownerHandle]"]').val() > 0 && $('input[name="'+field_prefix+'[ownerc]"]:checked').val() == "custom") ? $('select[name="'+field_prefix+'[ownerHandle]"]').val() : (($('input[name="'+field_prefix+'[ownerHandle_hidden]"]').val() > 0) ? $('input[name="'+field_prefix+'[ownerHandle_hidden]"]').val() : '');
				var current_handle_admin = ($('select[name="'+field_prefix+'[adminHandle]"]').val() > 0 && $('input[name="'+field_prefix+'[adminc]"]:checked').val() == "custom") ? $('select[name="'+field_prefix+'[adminHandle]"]').val() : (($('input[name="'+field_prefix+'[adminHandle_hidden]"]').val() > 0) ? $('input[name="'+field_prefix+'[adminHandle_hidden]"]').val() : '');
				var current_handle_tech = ($('select[name="'+field_prefix+'[techHandle]"]').val() > 0 && $('input[name="'+field_prefix+'[techc]"]:checked').val() == "custom") ? $('select[name="'+field_prefix+'[techHandle]"]').val() : (($('input[name="'+field_prefix+'[techHandle_hidden]"]').val() > 0) ? $('input[name="'+field_prefix+'[techHandle_hidden]"]').val() : '');
			
				// Update option list
				$('select[name="'+field_prefix+'[ownerHandle]"]').html(data);
				$('select[name="'+field_prefix+'[adminHandle]"]').html(data);
				$('select[name="'+field_prefix+'[techHandle]"]').html(data);	

				// Select old selected values again if possible
				var clone_handle_owner = clone_handle_admin = clone_handle_tech =  false;
				if($('select[name="'+field_prefix+'[ownerHandle]"] option[value="'+current_handle_owner+'"]').val() == null){
					clone_handle_owner = true;
				}else{
					$('select[name="'+field_prefix+'[ownerHandle]"]').val(current_handle_owner);
				}
			
				if($('select[name="'+field_prefix+'[adminHandle]"] option[value="'+current_handle_admin+'"]').val() == null){
					clone_handle_admin = true;
				}else{
					$('select[name="'+field_prefix+'[adminHandle]"]').val(current_handle_admin);
				}
				
				if($('select[name="'+field_prefix+'[techHandle]"] option[value="'+current_handle_tech+'"]').val() == null){
					clone_handle_tech = true;
				}else{
					$('select[name="'+field_prefix+'[techHandle]"]').val(current_handle_tech);
				}
				
				// Do we need to open dialog for clone handles?
				if(clone_handle_owner || clone_handle_admin || clone_handle_tech){
					$('#'+stripped_field_prefix+'_contact_registrar_changed').show();
				}
				
				// New handle to select?
				if(new_handle_type != undefined){
					$('select[name="'+field_prefix+'['+new_handle_type+'Handle]"]').val(new_handle_id);
					
					// Switch owner?
					if(new_handle_type == "owner" && new_handle_id > 0){
						$('input[name="'+field_prefix+'[ownerc]"][value="custom"]').prop('checked',true).click();
					}
				}else{
					// Registrar or debtor has been changed
					if($('select[name="'+field_prefix+'[ownerHandle]"] optgroup').length > 2 || current_handle_owner > 0){ // We need to use 2, because open and close tag both counts
						// Debtor has own handles
						$('input[name="'+field_prefix+'[ownerc]"][value="custom"]').prop('checked',true).click();
						$('#'+stripped_field_prefix+'_contact_owner_new').hide();
						$('#'+stripped_field_prefix+'_contact_owner br:first').remove();
						$('#'+stripped_field_prefix+'_contact_owner strong.title span:last').show();
						$('#'+stripped_field_prefix+'_contact_owner strong.title span:first').hide();
					}else{
						// Debtor doesn't have handles yet, or no global handles available
						$('input[name="'+field_prefix+'[ownerc]"][value="debtor"]').prop('checked',true).click();
						$('#'+stripped_field_prefix+'_contact_owner_new').show();
						$('#'+stripped_field_prefix+'_contact_owner br:first').remove(); $('#'+stripped_field_prefix+'_contact_owner').prepend('<br />');
						$('#'+stripped_field_prefix+'_contact_owner strong.title span:last').hide();
						$('#'+stripped_field_prefix+'_contact_owner strong.title span:first').show();
					}
				}
				
				// Text adjustment for default handles
				if($('input[name="'+field_prefix+'[adminc_id]"]').val()){
					if($('input[name="'+field_prefix+'[adminc]"]:checked').val() == "handle"){ $('select[name="'+field_prefix+'[adminHandle]"]').val($('input[name="'+field_prefix+'[adminc_id]"]').val()); }
					$('input[name="'+field_prefix+'[adminc]"][value="handle"]').next('label').find('span').html($('select[name="'+field_prefix+'[adminHandle]"] optgroup option[value='+$('input[name="'+field_prefix+'[adminc_id]"]').val()+']').text());
				}
				if($('input[name="'+field_prefix+'[techc_id]"]').val()){
					if($('input[name="'+field_prefix+'[techc]"]:checked').val() == "handle"){ $('select[name="'+field_prefix+'[techHandle]"]').val($('input[name="'+field_prefix+'[techc_id]"]').val()); }
					$('input[name="'+field_prefix+'[techc]"][value="handle"]').next('label').find('span').html($('select[name="'+field_prefix+'[techHandle]"] optgroup option[value='+$('input[name="'+field_prefix+'[techc_id]"]').val()+']').text());
				}			
		}, "html");
}

function getExtraFields(registrar_id, tld){
	$.post("XMLRequest.php", { action: "domain_extra_fields", registrar: registrar_id, tld: tld, domain: $('input[name="domain_id"]').val() },
		function(data){
			if(data == "")
			{
				$('#tab-domain-extra').html('');
				$('#tab-domain').parents('.tabs').tabs("disable",2);				
			}else{
                
				$('#tab-domain-extra').html(data);
				$('#tab-domain').parents('.tabs').tabs("enable",2);
			}
	}, "html");
}

function open_handle_dialog(contact_type, field_prefix){
	
	var handle_dialog_div = (field_prefix == 'domain') ? 'div_for_handleadd' : 'div_for_handleadd_' + field_prefix;

	if($('#' + handle_dialog_div).parents('form') != undefined)
    {
        // Move outside form.
        var FormParent = $('#' + handle_dialog_div).parents('form');
        $(FormParent).after($('#' + handle_dialog_div));
    }

	$('#' + handle_dialog_div).load('XMLRequest.php?action=addhandle&debtor_id=' + $('input[name="Debtor"]').val(), function(){ 
		$('#handle_add').dialog({modal: true, autoOpen: true, resizable: false, width: 950, height: 'auto', close: function(event,ui){ $('#handle_add').dialog('destroy').remove(); }});
		
		// International version only
		if(IS_INTERNATIONAL == 'true')
		{
			$('select[name="Country"]').change(function(){
				
				getCountryStates($(this).val(), '', '');	
			});
		}
		
		// Toggle select field for select input field
		$('#dialog_handle_use_other_handle_btn').click(function(){
			$(this).hide();
			$('#dialog_handle_use_other_handle').slideDown();
			
			// Fill select field for other handles
			$.post("XMLRequest.php", { action: "get_select_handles", debtor_id: $('input[name="Debtor"]').val(), registrar_id: 0, show: 'all'},
				function(data){
					// Update option list
					$('select[name="UseOtherHandle"]').html(data);
					
					// What if select value is changed
					$('select[name="UseOtherHandle"]').change(function(){
						if($(this).val() > 0){
							// Copy data
							$.post("XMLRequest.php", { action: "handle_information", id: $(this).val()},
								function(data){
									if(data.errorSet != undefined){
										alert(data.errorSet[0].Message);
									}else{
										// Copy handle data to fields
										$('#handle_add input[name="CompanyName"]').val(data.resultSet[0].CompanyName).change();
										$('#handle_add input[name="CompanyNumber"]').val(data.resultSet[0].CompanyNumber);
										$('#handle_add input[name="TaxNumber"]').val(data.resultSet[0].TaxNumber);
										$('#handle_add select[name="LegalForm"]').val(data.resultSet[0].LegalForm);
										
										$('#handle_add select[name="Sex"]').val(data.resultSet[0].Sex);
										$('#handle_add input[name="Initials"]').val(data.resultSet[0].Initials);
										$('#handle_add input[name="SurName"]').val(data.resultSet[0].SurName);
										$('#handle_add input[name="Address"]').val(data.resultSet[0].Address);
										$('#handle_add input[name="ZipCode"]').val(data.resultSet[0].ZipCode);
										$('#handle_add input[name="City"]').val(data.resultSet[0].City);
										$('#handle_add select[name="Country"]').val(data.resultSet[0].Country);
										
										$('#handle_add input[name="PhoneNumber"]').val(data.resultSet[0].PhoneNumber);
										$('#handle_add input[name="FaxNumber"]').val(data.resultSet[0].FaxNumber);
										$('#handle_add input[name="EmailAddress"]').val(data.resultSet[0].EmailAddress);
										
										if(IS_INTERNATIONAL == 'true')
										{							
											$('#handle_add  input[name="Address2"]').val(data.resultSet[0].Address2);
											getCountryStates(data.resultSet[0].Country, '', data.resultSet[0].State);
										}
										
										// Handle custom client fields?
										loadCustomClientFields(data.resultSet[0].customfields_list, data.resultSet[0], false, 'handle_add', '');
									}
								}
							, "json");
						}else{
							// Do nothing
						}
					});
					
					
				}
			, "html");
			
			// Make search button work
			if($('#' + handle_dialog_div)){
	
				$('#handle_search_use_other').click(function(){
				
						$('#' + handle_dialog_div).load('XMLRequest.php?action=searchhandle&debtor_id=0&registrar_id=' , function(){ $('#handle_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto', close: function(event,ui){ $('#handle_search').dialog('destroy').remove(); }}); });
						
						// use selected handle for field
                        $(document).off('click', '#handle_search .dialog_select_hover');
                        $(document).on('click', '#handle_search .dialog_select_hover', function() {
							SelectedHandle = $(this).attr('id').replace('tr_','');
							
							$('select[name="UseOtherHandle"]').val(SelectedHandle).change();
							$('#handle_search').dialog('close');
		
							ajaxSave('search.handle','searchfor','');
						});
		
				});
			}
			
		});
		$('#dialog_handle_use_other_handle_btn2').click(function(){
			$('#dialog_handle_use_other_handle').slideUp();
			$('#dialog_handle_use_other_handle_btn').show();
		});
		
		$('#HandleForm select[name="Registrar"]').change(function(){
			if($(this).val()){
				$('#dialog_handle_registrarhandle').show();
			}else{
				$('#HandleForm input[name="RegistrarHandle"]').val('');
				$('#dialog_handle_registrarhandle').hide();
			}
		});
		
		$('#HandleForm input[name="RegistrarHandleType"]').click(function(){
			if($('#HandleForm input[name="RegistrarHandleType"]:checked').val() == "new"){
				$('#HandleForm input[name="RegistrarHandle"]').val('');
				$('#dialog_handle_registrarhandle_type').hide();
			}else{
				$('#dialog_handle_registrarhandle_type').show();
			}
		});
		
		$('#HandleForm input[name="CompanyName"]').keyup(function(){ if($(this).val()){ $('#CompanyName_extra').show(); }else{ $('#CompanyName_extra').hide();  }}).change(function(){ if($(this).val()){ $('#CompanyName_extra').show(); }else{ $('#CompanyName_extra').hide(); }});
		
		$('#handle_add_btn').click(function(){
			if($(this).hasClass('button1')){
				$(this).removeClass('button1').addClass('button2');
				
				if($('#HandleForm select[name="Registrar"]').val() && $('#HandleForm input[name="RegistrarHandleType"]:checked').val() == "new"){
					$('#handle_add_loader').show().find('span.loading_grey span').html($('#HandleForm select[name="Registrar"] option:selected').text());
				}
				
				$.post('XMLRequest.php', { action: 'add_handle', debtor_id: $(this).val(), values: $('#HandleForm').serialize() }, function(data){
					$('#handle_add_loader').hide();
					
					if(data.errorSet != undefined){
						$('#handle_add_btn').removeClass('button2').addClass('button1');
						$('#handle_result_box').html(data.errorSet[0].Message);
					}else{
						// Reload handle-selects at background, then select data.handle_id
						if(field_prefix == 'domain')
						{
							getHandleSelect($('input[name="Debtor"]').val(), $('select[name="domain[Registrar]"]').val(), 'domain', contact_type, data.handle_id);
						}						
						else
						{
							getHandleSelect($('input[name="Debtor"]').val(), $('input[name="module['+field_prefix+'][Registrar]"]').val(), 'module[' + field_prefix + ']', contact_type, data.handle_id);
						}
						$('#handle_add').dialog('close');
					}
				},"json");
			}
		});
		
	});
}

function ask_disconnect_subscription(){
	// If we have a current subscription, change keep_current
	$('input[name="subscription[keep_current]"]').val('remove');
}

function ask_nameserver_change(DNS1, DNS2, DNS3, from){
	
	$('#dialog_nameservers_current').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val());
	$('#dialog_nameservers_new').html(DNS1 + '<br />' + DNS2 + '<br />' + DNS3);
	$('#dialog_nameservers_from').html(from);
	$('#dialog_confirm_modify').dialog({modal: true, autoOpen: true, resizable: false, width: 550, height: 'auto'});
	
	$('#dialog_confirm_modify_button').click(function(){
	
		// Which option has been chosen?
		if($('input[name="dialog_nameservers_radio"]:checked').val() == "current"){
			// We don't need to change
		}else{
			$('input[name="domain[DNS1]"]').val(DNS1);
    		$('input[name="domain[DNS2]"]').val(DNS2);
    		$('input[name="domain[DNS3]"]').val(DNS3);
    		$('#domain_nameservers_text').html($('input[name="domain[DNS1]"]').val() + '<br />' + $('input[name="domain[DNS2]"]').val() + '<br />' + $('input[name="domain[DNS3]"]').val()).change();
    		toggleDomainNameserverForm('hide');
		}
	
		$('#dialog_confirm_modify').dialog('close');
	});
	
}


function toggleDomainNameserverForm(mode){
	if(mode == 'show')
	{
		$('.nshide').hide(); $('#domain_nameservers_input').show();
		$('#domain_change_nameservers').hide().parent().hide().next().hide();	
	}
	else
	{
		$('.nshide').hide(); $('#domain_nameservers_input').hide();
		$('#domain_change_nameservers').show().parent().show().next().show();	
	}	
	
}

function toggleSubscriptionAutoRenew(new_value)
{
	//$('#subscription_div_autorenew').hide();
	$('input[name="subscription[AutoRenew]"][value="'+new_value+'"]').prop('checked', true);
	
	if(new_value == 'once' || new_value == 'no')
	{
		$('#subscription_remindermail_settings').hide();
		$('#subscription_remindermail_no_settings').show();
		$('#subscription_reminder_').click();
	}
}