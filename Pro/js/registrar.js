
$(function(){
	
	$('select[name="Class"]').change(function(){
		getRegistrarAPI($(this).val());
	});

    $(document).on('click', 'input[name="DNSTemplate"]', function(){
		if($('#helpText_DNSTemplate').text() != ''){
			$('#helpText_DNSTemplate_div').show();
		}
	});
	
	// Delete dialog
	if($('#delete_registrar')){
		$('#delete_registrar').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
		$('#delete_registrar.autoopen').dialog('open');	
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_registrar_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_registrar_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_registrar_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
	
	if($('#RegistrarForm').html() != null){
		$('input[name="AdminCustomer"]').click(function(){
			if($(this).val() == 'yes'){ $('#registrar_contact_admin').slideUp(); }else{ $('#registrar_contact_admin').slideDown(); }
		});
		$('input[name="TechCustomer"]').click(function(){
			if($(this).val() == 'yes'){ $('#registrar_contact_tech').slideUp(); }else{ $('#registrar_contact_tech').slideDown(); }
		});
		
		if($('#div_for_handlesearch')){
	
			$('.default_handle_search_icon').click(function(){

					var HandleSearchFrom = $(this).attr('id').replace('default_handle_search_icon_','');
					$('#div_for_handlesearch').load('XMLRequest.php?action=searchhandle&debtor_id=0&registrar_id=' + $('input[name="id"]').val() , function(){ $('#handle_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto', close: function(event,ui){ $('#handle_search').dialog('destroy').remove(); }}); });

                    $(document).on('click', '#handle_search .dialog_select_hover', function() {
						SelectedHandle = $(this).attr('id').replace('tr_','');
						$('select[name="'+HandleSearchFrom+'"]').val(SelectedHandle);
						$('#handle_search').dialog('close');
	
						ajaxSave('search.handle','searchfor','');
					});
	
			});

		}
		
		// Toggle domain enabled div's
		$('input[name="DomainEnabled"]').click(function(){
			if($(this).prop('checked'))
			{
				$('.domain_enabled_div').removeClass('hide');
			}
			else
			{
				$('.domain_enabled_div').addClass('hide');
			}
		});
		
	}
	
	if($('#delete_tld')){
		$('#delete_tld').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
		$('#delete_tld.autoopen').dialog('open');	
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_tld_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_tld_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_tld_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
	
	$('#dialog_sync_public_whois').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#dialog_sync_public_whois_btn').click(function(){ $('form[name="sync_public_whois"]').submit(); });

	if($('#dialog_cleanup_handles'))
	{
		$('#dialog_cleanup_handles').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#dialog_cleanup_handles_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#dialog_cleanup_handles_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#dialog_cleanup_handles_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
                $(this).hide();
                $(this).next('span.loader').show();
                $('form[name="cleanup_handles"]').submit();
			}
		});
	}
	
	if($('#TldForm').html() != null && $('input[name="id"]').val() == undefined){
		$('input[name="Tld"]').change(function(){
			$('#sync_public_whois_server').click();
			$('#sync_idn_support').click();
		});
	}
	
	$('#sync_public_whois_server').click(function(){
		$('#public_whois_status').html('');
		$('input[name="WhoisServer"]').val('');
		$('input[name="WhoisNoMatch"]').val('');
		
		$.post("XMLRequest.php", { action: "get_public_whois_for_tld", tld: $('input[name="Tld"]').val()},
				function(data){
					if(data.errorSet != undefined) {
						$('#public_whois_status').html('<span class="loading_red">'+data.errorSet[0].Message+'</span>');
						
					}else if(data.errorSet == undefined){
						$('input[name="WhoisServer"]').val(data.WhoisServer);
						$('input[name="WhoisNoMatch"]').val(data.WhoisNoMatch);						
					}
		   		}, "json");	
	});
	
	$('#sync_idn_support').click(function(){
		$('#sync_idn_status').html('<img src="images/icon_circle_loader_grey.gif" style="margin:6px 0 6px 6px;" />');
		$('input[name="AllowedIDNCharacters"]').val('');
		
		$.post("XMLRequest.php", { action: "get_idn_support_for_tld", tld: $('input[name="Tld"]').val()},
			function(data){
				$('#sync_idn_status').html('');
				if(data.substring(0,2) == 'OK' && data.length > 2)
				{
					$('input[name="AllowedIDNCharacters"]').val(data.substring(2));
					$('input[name="IDNsupport_helper"]').prop('checked',true);
					$('#IDNSupportDiv').show();
					
					$('#sync_idn_status').html('<span class="loading_green">'+__LANG_IDN_SYNC_SUCCESS+'</span>');
				}
				else
				{
					$('input[name="IDNsupport_helper"]').prop('checked',false);
					$('#IDNSupportDiv').hide();
					
					$('#sync_idn_status').html('<span class="loading_orange">'+__LANG_IDN_SYNC_UNKNOWN+'</span>');
				}
	   		}, "html");	
	});
	
	$('input[name="AllowedIDNCharacters"]').keyup(function(){
		$('#sync_idn_status').html('');
	});
	
	$('input[name="IDNsupport_helper"]').click(function(){
		$('#sync_idn_status').html('');
		if($(this).prop('checked'))
		{
			$('#IDNSupportDiv').show();
		}
		else
		{
			$('#IDNSupportDiv').hide();
		}
	});
	
	// Update selected domains
    $(document).on('click', '.GroupBatch, .BatchCheck',function(){
		countSelectedDomains();
	});
	 
	// Dialog import
    $(document).on('click', '#import_domains',function(){
		if($(this).hasClass('button2')){
			return false;
		}
		$('#domain_periodic table tbody').html('');
		$('#import_registrar').dialog({modal: true, autoOpen: false, resizable: false, width: 'auto', height: 'auto'});
		
		// Set values
		$('#dialog_numberofdomains').html($('.GroupBatch:checked').length);
		$('#dialog_debtor').html(htmlspecialchars($('input[name="AutoCompleteSearch[]"][data-inputfieldname="Debtor"]').val()));
		$('input[name=DialogDebtor]').val($('input[name="Debtor"]').val());
		
		// Loop domains
		$('.GroupBatch:checked').each(function(){
			var NewElement = $('#domain_periodic table tfoot tr').clone();
			var DomainID = $(this).val();
			var DomainName = $(this).parent().find('span').html();
			

			// Rename form elements
			$(NewElement).find('select').each(function(){
				$(this).attr('name',$(this).attr('name') + '[' + DomainID + ']');	
			});
			$(NewElement).find('input, textarea').each(function(){				
				// Skip fields already having [] search
				if($(this).attr('name').indexOf('[') == -1)
				{
					$(this).attr('name',$(this).attr('name') + '[' + DomainID + ']');
				}				
			});
			
			// Update product search
			if($(NewElement).find('input[name="AutoCompleteSearch[]"]').data('inputfieldname') != 'undefined')
			{
				$(NewElement).html(str_replace("data-inputfieldname=\"ProductCode\"","data-inputfieldname=\"ProductCode["+DomainID+"]\"",$(NewElement).html()));
			}
			
			$(NewElement).find('#EndPeriodText').attr('id','EndPeriodText_' + DomainID);
						
			// Append
			$(NewElement).appendTo('#domain_periodic table tbody');
			$(NewElement).find('input[name="StartPeriod[' + DomainID + ']"]').datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1});
			$(NewElement).find('td').first().data('domainname', DomainName);
			var tld = DomainName.split('.');
			if(tld.length == 2){ tld = tld[1]; }else{ tld = DomainName.replace(tld[0] + '.', ''); }

			var tmp_date = rewrite_date_site2db($('#expires_'+DomainID).html());
			tmp_date = rewrite_date_db2site(tmp_date.substr(0,4),tmp_date.substr(4,2),tmp_date.substr(6,2));
			
			if(tmp_date == $('#expires_'+DomainID).html()){
				$(NewElement).find('input[name="StartPeriod[' + DomainID + ']"]').val($('#expires_'+DomainID).html());
			}
			
			// Retrieve product, description etc.
			$.post("XMLRequest.php", { action: "get_product_by_tld", tld: tld, DomainID: DomainID},
				function(data){
					if(data.errorSet == undefined && data.ProductCode != undefined) 
                    {
						$('#domain_periodic input[name="ProductCode['+DomainID+']"]').val(data.ProductCode);											
						$('#domain_periodic input[name="AutoCompleteSearch[]"][data-inputfieldname="ProductCode['+DomainID+']"]').val(data.ProductCode + ' ' + data.Name);
						$('#domain_periodic input[name="AutoCompleteSearch[]"][data-inputfieldname="ProductCode['+DomainID+']"]').data('label', data.ProductCode + ' ' + data.Name);
						if(data.Description.lastIndexOf(' .' + tld) >= 0)
                        {
							var lastIndex = data.Description.lastIndexOf(' .' + tld);
							$('textarea[name="Description['+DomainID+']"]').val(data.Description.substring(0,lastIndex) + data.Description.substring(lastIndex).replace(' .' + tld,' '+DomainName)).autoGrow();
						}
                        else
                        {
							var lastIndex = data.Description.lastIndexOf('-');	
							if(lastIndex > 0)
                            { 
								$('#domain_periodic textarea[name="Description['+DomainID+']"]').val(data.Description.substring(0,lastIndex) + '- ' + DomainName).autoGrow();
							}
                            else
                            {
								$('#domain_periodic textarea[name="Description['+DomainID+']"]').val(data.Description + ' - ' + DomainName).autoGrow();
							}
						}
                    
    					$('#domain_periodic input[name="PriceExcl['+DomainID+']"]').val(formatAsMoney(data.PriceExcl));
    					$('#domain_periodic select[name="Periodic['+DomainID+']"]').val(data.PricePeriod).change();
					}
                    else
                    {
						$('#domain_periodic textarea[name="Description['+DomainID+']"]').val(DomainName).autoGrow();
                        
                        result = changePeriodCalc($('select[name="Periodic[' + DomainID + ']"]').val(), $('input[name="Periods[' + DomainID + ']"]').val(), $('input[name="StartPeriod[' + DomainID + ']"]').val());
        				$('#EndPeriodText_' + DomainID).html(__LANG_TILL + ' ' + result[1]);
					}
		   		}, "json");		
		});
		
		// Onchange
		$('input[name^="ProductCode"]').change(function(){
			var DomainID = $(this).parents('tr').find('input[type=hidden]').first().attr('name');
			DomainID = DomainID.replace('DomainID[','').replace(']','');
			var DomainName = $(this).parents('tr').find('td').first().data('domainname');
			
			$.post("XMLRequest.php", { action: "get_product", productcode: $(this).val()},
				function(data){
					if(data.ProductCode){
		            	var tld = DomainName.split('.',2);
						if(tld.length == 2){ tld = tld[1]; }else{ tld = tld[0]; }
						
						if(data.Description.lastIndexOf(' .' + tld) >= 0){
							var lastIndex = data.Description.lastIndexOf(' .' + tld);
							$('textarea[name="Description['+DomainID+']"]').val(data.Description.substring(0,lastIndex) + data.Description.substring(lastIndex).replace(' .' + tld,' '+DomainName)).autoGrow();
						}else{
							$('textarea[name="Description['+DomainID+']"]').val(data.Description + ' ' + DomainName).autoGrow();
						}
						
						$('input[name="PriceExcl['+DomainID+']"]').val(data.PriceExcl);
						// Also set 'prev'-attribute, so changing from period because of product won't result in wrong prices
						$('select[name="Periodic['+DomainID+']"]').attr('prev', data.PricePeriod).val(data.PricePeriod).change();
					}
			}, "json");
		});

        $(document).on('focus', 'select[name^="Periodic"]', function () {
            $(this).attr('prev', $(this).val());
        });

        $(document).on('change', 'select[name^="Periodic"]', function () {

            LineID = $(this).attr('name').replace('Periodic[', '').replace(']', '');

            $(this).parents('tr').find('.span_periodic').html($('select[name="Periodic[' + LineID + ']"] option:selected').text());

            // Check if we have a custom price, if not calc price period
            checkCustomPeriodPrice(LineID, 'Periodic');
        });
        $(document).on('change', 'input[name^="Periods"]', function () {
            // If period is empty, make 1
            if ($(this).val() == "" || $(this).val() == "0" || $(this).val() != $(this).val() * 1) {
                $(this).val("1").keyup();
                return;
            }

            LineID = $(this).attr('name').replace('Periods[', '').replace(']', '');

            // Check if we have a custom price, if not calc price period
            checkCustomPeriodPrice(LineID, 'Periods');
        });
		
		$('input[name^="StartPeriod"], input[name^="Periods"], select[name^="Periodic"]').change(function(){
			var element_name = $(this).attr("name");
			element_name = element_name.split('[',2); element_name = element_name[1];
			element_name = element_name.substring(0,element_name.length-1);
						
			DomainID = element_name;
			
			if($('input[name="StartPeriod[' + DomainID + ']"]').val()){			
			
				result = changePeriodCalc($('select[name="Periodic[' + DomainID + ']"]').val(), $('input[name="Periods[' + DomainID + ']"]').val(), $('input[name="StartPeriod[' + DomainID + ']"]').val());
				$('input[name="StartPeriod[' + DomainID + ']"]').val(result[0]);
				$('input[name="EndPeriod[' + DomainID + ']"]').val(result[1]);
				$('#EndPeriodText_' + DomainID).html(__LANG_TILL + ' ' + result[1]);
			}
		});
		
		
		$('#import_registrar').dialog('open');

		$('#import_registrar_btn').click(function(){ 
			$('input[name=domain_data]').val($(this).parents('form').find('table tbody').find('input, select, textarea').serialize());
			document.form_import.submit();

		});
		
		$('#subscription_checkbox_yes').click(function(){ 
			if($(this).prop('checked')){
				$('#domain_periodic').show(); 
				$('#import_registrar').dialog("option", "width", 'auto');
			}else{
				$('#import_registrar').dialog("option", "width", $('#domain_periodic').width() + 26);
				$('#domain_periodic').hide(); 
			}
		});
		
	});
	
	// Initial load
	if($('select[name="Class"]').val() != undefined){
		getRegistrarAPI($('select[name="Class"]').val());
	}else if($('input[name="Class"]').val() != undefined){
		getRegistrarAPI($('input[name="Class"]').val());
	}
	
	

	if($('#delete_handle')){
		$('#delete_handle').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
		$('#delete_handle.autoopen').dialog('open');	
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_handle_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_handle_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_handle_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
	
	if($('#HandleForm').html() != null){
		
		$('#Handle_text').click(function(){
			$(this).hide();
			$('input[name=Handle]').show().focus();
			$('input[name=Handle]').blur(function(){
				$(this).hide();
				$('#Handle_text').html(htmlspecialchars($(this).val()) + " " + STATUS_CHANGE_ICON);
				$('#Handle_text').show();			
			});
		});
		
		$('select[name="Registrar"]').change(function(){
			if($(this).val()){
				$('#handle_registrarhandle').show();
			}else{
				$('input[name="RegistrarHandle"]').val('');
				$('#handle_registrarhandle').hide();
			}
		});
		
		$('input[name="RegistrarHandleType"]').click(function(){
			if($('input[name="RegistrarHandleType"]:checked').val() == "new"){
				$('input[name="RegistrarHandle"]').val('');
				$('#handle_registrarhandle_type').hide();
			}else{
				$('#handle_registrarhandle_type').show();
			}
		});
		
		$('input[name="debtor_helper"]').click(function(){
			if($(this).val() == 'yes'){
				$('#debtor_helper_div').show();
			}else{
				$('input[name="Debtor"]').val('');
				$('#debtor_helper_div').hide();
			}
		});
		
		$('input[name="Debtor"]').change(function(){
			if($(this).val() > 0 && $('input[name="id"]').val() == undefined){
				
				// Get debtor data
				$.post("XMLRequest.php", { action: "debtor_information", id: $(this).val(), emailaddress: 'single'},
					function(data){
						if(data.errorSet != undefined){
							
						}else{
							// Copy handle data to fields
							$('#HandleForm input[name="CompanyName"]').val(data.resultSet[0].CompanyName).change();
							$('#HandleForm input[name="CompanyNumber"]').val(data.resultSet[0].CompanyNumber);
							$('#HandleForm input[name="TaxNumber"]').val(data.resultSet[0].TaxNumber);
							$('#HandleForm select[name="LegalForm"]').val(data.resultSet[0].LegalForm);
							
							$('#HandleForm select[name="Sex"]').val(data.resultSet[0].Sex);
							$('#HandleForm input[name="Initials"]').val(data.resultSet[0].Initials);
							$('#HandleForm input[name="SurName"]').val(data.resultSet[0].SurName);
							$('#HandleForm input[name="Address"]').val(data.resultSet[0].Address);
							$('#HandleForm input[name="ZipCode"]').val(data.resultSet[0].ZipCode);
							$('#HandleForm input[name="City"]').val(data.resultSet[0].City);
							$('#HandleForm select[name="Country"]').val(data.resultSet[0].Country);
							
							$('#HandleForm input[name="PhoneNumber"]').val(data.resultSet[0].PhoneNumber);
							$('#HandleForm input[name="FaxNumber"]').val(data.resultSet[0].FaxNumber);
							$('#HandleForm input[name="EmailAddress"]').val(data.resultSet[0].EmailAddress);
							
							if(IS_INTERNATIONAL == 'true')
							{							
								$('#HandleForm input[name="Address2"]').val(data.resultSet[0].Address2);
								getCountryStates(data.resultSet[0].Country, '', data.resultSet[0].State);
							}
						}
					}
				, "json");
			}	
		});
		
		$('input[name="CompanyName"]').keyup(function(){ if($(this).val()){ $('#CompanyName_extra').show(); }else{ $('#CompanyName_extra').hide();  }}).change(function(){ if($(this).val()){ $('#CompanyName_extra').show(); }else{ $('#CompanyName_extra').hide(); }});
		
		// Toggle select field for select input field
		$('#handle_use_other_handle_btn').click(function(){
			$(this).hide();
			$('#handle_use_other_handle').slideDown();
			
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
										$('#HandleForm input[name="CompanyName"]').val(data.resultSet[0].CompanyName).change();
										$('#HandleForm input[name="CompanyNumber"]').val(data.resultSet[0].CompanyNumber);
										$('#HandleForm input[name="TaxNumber"]').val(data.resultSet[0].TaxNumber);
										$('#HandleForm select[name="LegalForm"]').val(data.resultSet[0].LegalForm);
										
										$('#HandleForm select[name="Sex"]').val(data.resultSet[0].Sex);
										$('#HandleForm input[name="Initials"]').val(data.resultSet[0].Initials);
										$('#HandleForm input[name="SurName"]').val(data.resultSet[0].SurName);
										$('#HandleForm input[name="Address"]').val(data.resultSet[0].Address);
										$('#HandleForm input[name="ZipCode"]').val(data.resultSet[0].ZipCode);
										$('#HandleForm input[name="City"]').val(data.resultSet[0].City);
										$('#HandleForm select[name="Country"]').val(data.resultSet[0].Country);
										
										$('#HandleForm input[name="PhoneNumber"]').val(data.resultSet[0].PhoneNumber);
										$('#HandleForm input[name="FaxNumber"]').val(data.resultSet[0].FaxNumber);
										$('#HandleForm input[name="EmailAddress"]').val(data.resultSet[0].EmailAddress);
										
										if(IS_INTERNATIONAL == 'true')
										{							
											$('#HandleForm input[name="Address2"]').val(data.resultSet[0].Address2);
											getCountryStates(data.resultSet[0].Country, '', data.resultSet[0].State);
										}
										
										// Handle custom client fields?
										loadCustomClientFields(data.resultSet[0].customfields_list, data.resultSet[0], false, 'HandleForm', '');
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
			if($('#div_for_handlesearch')){
	
				$('#handle_search_use_other').click(function(){
				
						$('#div_for_handlesearch').load('XMLRequest.php?action=searchhandle&debtor_id=0&registrar_id=' , function(){ $('#handle_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto', close: function(event,ui){ $('#handle_search').dialog('destroy').remove(); }}); });
						
						// use selected handle for field
                        $(document).on('click', '#handle_search .dialog_select_hover', function() {
							SelectedHandle = $(this).attr('id').replace('tr_','');
							
							$('select[name="UseOtherHandle"]').val(SelectedHandle).change();
							$('#handle_search').dialog('close');
		
							ajaxSave('search.handle','searchfor','');
						});
		
				});

			}
			
		});
		
		$('#handle_use_other_handle_btn2').click(function(){
			$('#handle_use_other_handle').slideUp();
			$('#handle_use_other_handle_btn').show();
		});
		
		// International version only
		if(IS_INTERNATIONAL == 'true')
		{
			$('select[name="Country"]').change(function(){
				
				getCountryStates($(this).val(), '', '');	
			});
		}
		
	}
});

function getRegistrarAPI(registrar_id)
{
	if(registrar_id == "")
	{
		$('#registrar_div').hide();
		$('#registrar_div_error').hide();
		$('#registrar_none_info').show();
		
		return;
	}
	$('#registrar_none_info').hide();
	    
	$.post("XMLRequest.php", { action: "registrar_api", id: registrar_id},
		function(data){
			if(data.errorSet != undefined) {
				$('#registrar_div_error').html('<br />' + data.errorSet[0].Message).show();
				$('#registrar_div').hide();
			}else if(data.errorSet == undefined){
				$('#registrar_div').show();
				$('#registrar_div_error').hide();
				var html = '';
				if(data.dev_logo){ html += '<img src="' + data.dev_logo + '" alt="" class="registrar_logo"/>'; }
				if(data.dev_author){ html += data.dev_author + '<br />'; }
				if(data.dev_website){ html += '<a href="' + data.dev_website + '" class="a1 c1">' + data.dev_website + '</a><br />'; }
				if(data.dev_email){ html += '<a href="mailto:' + data.dev_email + '" class="a1 c1">' + data.dev_email + '</a><br />'; }
				if(data.dev_phone){ html += data.dev_phone + '<br />'; }
				
				if(html == ''){ 
					$('#registrar_div .setting_help_box').hide(); 
				}else{
					if(data.dev_author.toLowerCase().indexOf('hostfact') == -1)
					{
						html += '<br /><strong>' + __LANG_API_DATA + '</strong><br />' + __LANG_API_IMPL_VERSION + ' ' + data.integration_version + '<br />' + __LANG_API_SUP_VERSION + ' ' + data.registrar_version;
					}
					
					$('#dev_info').html('<br />' + html);
					$('#registrar_div .setting_help_box').show();
				}
				
				// Enabled fields
				if($('input[name="id"]').val() == undefined)
				{
					if($('input[name="DomainEnabled"][type="checkbox"]').val() != undefined)
					{
						$('input[name="DomainEnabled"]').prop('checked', data.domain_support);
						$('input[name="SSLEnabled"]').prop('checked', data.ssl_support);
						
					}
					else
					{
						$('input[name="DomainEnabled"]').val((data.domain_support) ? 'yes' : 'no');
						$('input[name="SSLEnabled"]').val((data.ssl_support) ? 'yes' : 'no');
					}
					
					if(data.domain_support)
					{
						$('.domain_enabled_div').removeClass('hide');
					}
					else
					{
						$('.domain_enabled_div').addClass('hide');	
					}
				}
				
				// API Settings
				if(data.settings.user.show){ $('#registrar_settings_user').show(); }else{ $('#registrar_settings_user').hide(); }
				if(data.settings.password.show){ $('#registrar_settings_password').show(); }else{ $('#registrar_settings_password').hide(); }
				if(data.settings.registrar_ip.show){ $('#registrar_ip').show(); }else{ $('#registrar_ip').hide(); }
				
				settingsArray = ['Setting1','Setting2','Setting3','DNSTemplate'];
				
				$.each(settingsArray, function( key, settingName ) {
					if(data.settings[settingName.toLowerCase()].show){
						
						// Are we on add/edit or view?
						var PageRegistrar = ($('#registrar_settings_'+settingName+' strong').hasClass('title')) ? 'add' : 'view';
						
						if(settingName == 'DNSTemplate' && $('#registrar_settings_'+settingName+' strong').hasClass('title')){
							
							$('#DNSTemplate_infoPopup').html(data.settings[settingName.toLowerCase()].label);
							
							if(data.settings[settingName.toLowerCase()].helpText == '' && $('input[name="DNSTemplate"]').val() == ''){
								$('#helpText_DNSTemplate_div').hide();
							}else{
								$('#helpText_DNSTemplate_div').show();
							}
						}else{
							$('#registrar_settings_'+settingName+' strong').html(data.settings[settingName.toLowerCase()].label);
						}
						
						if(data.settings[settingName.toLowerCase()].helpText != ''){
							$('#helpText_'+settingName).html(data.settings[settingName.toLowerCase()].helpText + "<br />");
						}
						
						switch(data.settings[settingName.toLowerCase()].type){
							case 'input':
								if(PageRegistrar == 'view'){
									$('#registrar_settings_'+settingName+' span.title2_value').html($('#registrar_settings_'+settingName+' input[name="'+settingName+'_current"]').val());
								}else{
									var defaultValue = $('#registrar_settings_'+settingName+' input[name="'+settingName+'_current"]').val();
									if(!defaultValue){
										defaultValue = (data.settings[settingName.toLowerCase()].defaultValue == null) ? '' : data.settings[settingName.toLowerCase()].defaultValue ;
									}
									$('#registrar_settings_'+settingName+' span.input_span').html('<input type="text" value="' + defaultValue + '" name="'+settingName+'" class="text1 size1">');
								}
								break;
							case 'textarea':
								if(PageRegistrar == 'view'){
									$('#registrar_settings_'+settingName+' span.title2_value').html('<textarea name="'+settingName+'" style="height: 150px;" class="text1 size11" readonly="readonly">' + $('#registrar_settings_'+settingName+' input[name="'+settingName+'_current"]').val() + '</textarea>');
								}else{
									var defaultValue = $('#registrar_settings_'+settingName+' input[name="'+settingName+'_current"]').val();
									if(!defaultValue){
										defaultValue = (data.settings[settingName.toLowerCase()].defaultValue == null) ? '' : data.settings[settingName.toLowerCase()].defaultValue ;;
									}
									$('#registrar_settings_'+settingName+' span.input_span').html('<textarea name="'+settingName+'" style="height: 150px;" class="text1 size11">' + defaultValue + '</textarea>');
									
									
								}
								break;
							case 'select':
								if(PageRegistrar == 'view'){
									$('#registrar_settings_'+settingName+' span.title2_value').html('<select class="text1 size4" name="'+settingName+'">' + data.settings[settingName.toLowerCase()].options + '</select>');
									$('#registrar_settings_'+settingName+' select[name="'+settingName+'"]').val($('#registrar_settings_'+settingName+' input[name="'+settingName+'_current"]').val());
									
									$('#registrar_settings_'+settingName+' span.title2_value').html($('#registrar_settings_'+settingName+' select[name="'+settingName+'"] option:selected').text());
								}else{
									$('#registrar_settings_'+settingName+' span.input_span').html('<select class="text1 size4" name="'+settingName+'">' + data.settings[settingName.toLowerCase()].options + '</select>');
									var defaultValue = $('#registrar_settings_'+settingName+' input[name="'+settingName+'_current"]').val();
									if(!defaultValue){
										defaultValue = (data.settings[settingName.toLowerCase()].defaultValue == null) ? '' : data.settings[settingName.toLowerCase()].defaultValue ;;
									}
									
									$('#registrar_settings_'+settingName+' select[name="'+settingName+'"]').val(defaultValue);
								}
								break;
						}
						
						if(PageRegistrar == 'view' && settingName == 'DNSTemplate' && $('#registrar_settings_'+settingName+' input[name="'+settingName+'_current"]').val() == ''){
							$('#registrar_settings_'+settingName).hide();
						}else{
							$('#registrar_settings_'+settingName).show();
						}
						 
					}else{
						// Delete block
						if(settingName == 'DNSTemplate'){
							$('#helpText_DNSTemplate_div').hide();
							$('#DNSTemplate_infoPopup').html('');
						}else{
							$('#registrar_settings_'+settingName+' strong').html('');
						}
						
						$('#registrar_settings_'+settingName+' span.input_span').html('');
						$('#registrar_settings_'+settingName).hide(); 
					}
				});			
			}
	   }, "json");
}

function selectDomainOwners(ownerID){
    $('input[type="checkbox"][name="domains[]"]').prop('checked',false);
    $('.checkbox_' + ownerID).prop('checked',true);
    countSelectedDomains();
}

function countSelectedDomains(){
	var SelectedItems = $('.GroupBatch:checked').length;
	if(SelectedItems > 0 && $('input[name="Debtor"]').val() > 0){
		$('#import_domains').removeClass('button2').addClass('button1');
		$('#selectedNumber').show().children('strong').first().html(SelectedItems);
	}else{
		$('#import_domains').removeClass('button1').addClass('button2');
		$('#selectedNumber').hide().children('strong').first().html(0);
	}

	// Second tab (diff registrar).
    var SelectedItems = $('.GroupBatch2:checked').length;
    if(SelectedItems > 0){
        $('#import_domains_diff_registrar').removeClass('button2').addClass('button1');
    }else{
        $('#import_domains_diff_registrar').removeClass('button1').addClass('button2');
    }
}

function open_default_handle_dialog(contact_type, from_page){
	$('#div_for_handleadd').load('XMLRequest.php?action=add_default_handle', {registrar_id: $('input[name="id"]').val(), from_page: from_page}, function(){ 
		$('#handle_add').dialog({modal: true, autoOpen: true, resizable: false, width: 950, height: 'auto', close: function(event,ui){ $('#handle_add').dialog('destroy').remove(); }});
		
		// Fill registrar
		$('#handle_add input[name="Registrar"]').val($('input[name="id"]').val());
		
		$('input[name="DefaultHandleType"]').click(function(){
			if($(this).val() == 'new'){
				$('.new_default_handle').slideDown();
				$('.existing_default_handle').slideUp();
			}else{
				$('.new_default_handle').slideUp();
				$('.existing_default_handle').slideDown();
			}
		});
		
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
			$.post("XMLRequest.php", { action: "get_select_handles", debtor_id: 0, registrar_id: $('input[name="id"]').val(), show: 'all'},
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
			if($('#div_for_handlesearch')){
	
				$('#handle_search_use_other').click(function(){
				
						$('#div_for_handlesearch').load('XMLRequest.php?action=searchhandle&debtor_id=0&registrar_id=' , function(){ $('#handle_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto', close: function(event,ui){ $('#handle_search').dialog('destroy').remove(); }}); });
						
						// use selected handle for field
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
		
		$('#handle_add input[name="RegistrarHandleType"]').click(function(){
			if($('#HandleForm input[name="RegistrarHandleType"]:checked').val() == "new"){
				$('#HandleForm input[name="RegistrarHandle"]').val('');
				$('#dialog_handle_registrarhandle_type').hide();
			}else{
				$('#dialog_handle_registrarhandle_type').show();
			}
		});
		
		$('#handle_add input[name="CompanyName"]').keyup(function(){ if($(this).val()){ $('#CompanyName_extra').show(); }else{ $('#CompanyName_extra').hide();  }}).change(function(){ if($(this).val()){ $('#CompanyName_extra').show(); }else{ $('#CompanyName_extra').hide(); }});
		
		$('#handle_add_btn').click(function(){
			if($(this).hasClass('button1')){
				$(this).removeClass('button1').addClass('button2');
				
				if($('#handle_add input[name="Registrar"]').val() && $('#handle_add input[name="RegistrarHandleType"]:checked').val() == "new"){
					$('#handle_add_loader').show();
				}

				$.post('XMLRequest.php', { action: 'add_default_handle', values: $('#handle_add #HandleForm').serialize(), role: contact_type }, function(data){
					$('#handle_add_loader').hide();
					
					if(data.errorSet != undefined){
						$('#handle_add_btn').removeClass('button2').addClass('button1');
						$('#handle_result_box').html(data.errorSet[0].Message);
					}else{
						// Reload handle-selects at background, then select data.handle_id
						if(from_page == "add"){
							var new_handle_id = data.handle_id;
							// Reload selects
							$.post("XMLRequest.php", { action: "get_select_handles", debtor_id: 0, registrar_id: $('#handle_add input[name="Registrar"]').val()},
								function(data){ 
									// Do we need to get the current values?
									var current_handle_admin = ($('select[name="AdminHandle"]').val() > 0) ? $('select[name="AdminHandle"]').val() : '';
									var current_handle_tech = ($('select[name="TechHandle"]').val() > 0) ? $('select[name="TechHandle"]').val() : '';
									
									// Update option list
									$('select[name="AdminHandle"]').html(data);
									$('select[name="TechHandle"]').html(data);
									
									// Select options
									$('select[name="AdminHandle"]').val(current_handle_admin);
									$('select[name="TechHandle"]').val(current_handle_tech);
									
									// Select new handle
									if(contact_type == "admin"){
										$('select[name="AdminHandle"]').val(new_handle_id);
									}else if(contact_type == "tech"){
										$('select[name="TechHandle"]').val(new_handle_id);
									}
									
							}, "html");	
						}else{
							window.location.reload(true);
						}
						$('#handle_add').dialog('close');
					}
				},"json");
			}
		});
		
	});
}