$(function(){
	/**
	 * Domain pages
 	 */
	
	// Show only
	if($('input[type="hidden"][name="domain_id"]').val() > 0)
    {		
		// Get domain details
		var domain_id   = $('input[type="hidden"][name="domain_id"]').val();
        var enable_auto_sync = $('input[type="hidden"][name="enable_auto_sync"]').val();

        if(enable_auto_sync == 'yes')
        {
            // check for sync
    		$.post("XMLRequest.php", { action: "domain_sync", id: domain_id},
    		function(data)
            {
                if(data && data.success)
                {
                    for(var i=0; i < data.success.length;i++)
                    {
                        $('#domain_success ul').append('<li>' + data.success[i] + '</li>');
                    }
                    $('#domain_success').show();    
                }
                if(data && data.error)
                {
                    $('#domain_error ul').append('<li>' + data.error + '</li>');
                    $('#domain_error').show();    
                }
                
                $('#domain_sync_text span').html(data.synced_text);
				$('#domain_sync_text').show();
                $('#domain_sync_loader').hide();
    
    		}, "json");
        }

		// Dialogs
		$('#dialog_nameservers').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});
		$('#dialog_cancel_domain_at_registrar').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});
		if($('#dialog_cancel_domain_at_registrar')){
			$('#dialog_cancel_domain_at_registrar input[name=imsure]').click(function(){
				if($('#dialog_cancel_domain_at_registrar input[name=imsure]:checked').val() != null)
				{
					$('#dialog_cancel_domain_at_registrar_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#dialog_cancel_domain_at_registrar_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#dialog_cancel_domain_at_registrar_btn').click(function(){
				if($('#dialog_cancel_domain_at_registrar input[name=imsure]:checked').val() != null)
				{
					document.cancel_domain.submit();
				}	
			});
		}
		$('#dialog_update_whois').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('#dialog_update_whois_btn').click(function(){
				document.update_whois.submit();	
			});
		
		// Get IP from host
		$('input[name="ns1"], input[name="ns2"], input[name="ns3"]').change(function(){
			var DNS_field_name = $(this).attr('name');
			$.get("XMLRequest.php", { action: "getIPFromHost", hostname: $(this).val()},
				function(data){
		            if(data){
	
		            	$('input[name="'+DNS_field_name.replace('ns','ip')+'"]').val(data);
		            }else{
		            	$('input[name="'+DNS_field_name.replace('ns','ip')+'"]').val('');
		            }
			}, "html");
		});
		
		// Delete dialog
		if($('#delete_domain')){
				$('#delete_domain').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});
				$('#delete_domain.autoopen').dialog('open');	
				
				$('#delete_domain input[name=imsure]').click(function(){
					if($('#delete_domain input[name=imsure]:checked').val() != null)
					{
						$('#delete_domain_btn').removeClass('button2').addClass('button1');
					}
					else
					{
						$('#delete_domain_btn').removeClass('button1').addClass('button2');
					}
				});
				$('#delete_domain_btn').click(function(){
					if($('#delete_domain input[name=imsure]:checked').val() != null)
					{
						document.form_delete.submit();
					}	
				});
		}
	}
	// UpdateWHOIS only
	if($('#UpdateWHOISForm').html() != null){

		$('.whois_contact_changer').click(function(){
			var handle_type = $(this).attr('name');
			handle_type = handle_type.substring(0,handle_type.length-1);
			if($(this).val() == 'handle'){
				$('#div_'+$(this).attr('name')+'_handle').slideDown();
				
				$('.'+handle_type+'_existinghandle').slideDown();
				$('.'+handle_type+'_newhandle').slideUp();
				
				// Show costs ownerchange?
				if(handle_type == "owner" && $('input[name="cost_owner_current_handle"]').val() != $('select[name="ownerHandle"]').val()){
					$('#cost_owner_change_div').slideDown();
				}else if(handle_type == "owner"){
					$('#cost_owner_change_div').slideUp();
				}
			}else if($(this).val() == 'new'){
				$('#div_'+$(this).attr('name')+'_handle').slideUp();
				
				$('.'+handle_type+'_existinghandle').slideUp();
				$('.'+handle_type+'_newhandle').slideDown();
				
				if(handle_type == "owner"){
					$('#cost_owner_change_div').slideDown();
				}
			}else{
				$('#div_'+$(this).attr('name')+'_handle').slideUp();
				
				$('.'+handle_type+'_existinghandle').slideDown();
				$('.'+handle_type+'_newhandle').slideUp();
			}
		});
		// For each tab
		$('input[name$="CompanyName"]').keyup(function(){ if($(this).val()){ $(this).parent().find('.CompanyName_extra').show(); }else{ $(this).parent().find('.CompanyName_extra').hide();  }}).change(function(){ if($(this).val()){ $(this).parent().find('.CompanyName_extra').show(); }else{ $(this).parent().find('.CompanyName_extra').hide(); }});
		
		// Check VAT numbers
		$('input[name$="TaxNumber"]').blur(function(){
			var handle_type = $(this).attr('name').replace('TaxNumber','');
			$('#vat_status_' + handle_type).html(vat_checker($(this).val(), $('select[name="' + handle_type + 'Country"]').val(), $('#vat_status_' + handle_type)));
		});
		
		$('.whois_contact_changer_select').change(function(){ 
			var handle_type = $(this).attr('name').replace('Handle','');
			if($(this).val() > 0){
				// Copy data
				$.post("XMLRequest.php", { action: "handle_information", id: $(this).val()},
					function(data){
						if(data.errorSet != undefined){
							alert(data.errorSet[0].Message);
						}else{
							// Show costs ownerchange?
							if(handle_type == "owner" && $('input[name="cost_owner_current_handle"]').val() != data.resultSet[0].Identifier){
								$('#cost_owner_change_div').slideDown();
							}else if(handle_type == "owner"){
								$('#cost_owner_change_div').slideUp();
							}
							
							// Copy handle data to fields
							$('#UpdateWHOISForm input[name="'+handle_type+'CompanyName"]').val(data.resultSet[0].CompanyName).change();
							$('#UpdateWHOISForm input[name="'+handle_type+'CompanyNumber"]').val(data.resultSet[0].CompanyNumber);
							$('#UpdateWHOISForm input[name="'+handle_type+'TaxNumber"]').val(data.resultSet[0].TaxNumber);
							$('#UpdateWHOISForm select[name="'+handle_type+'LegalForm"]').val(data.resultSet[0].LegalForm);
							
							$('#UpdateWHOISForm select[name="'+handle_type+'Sex"]').val(data.resultSet[0].Sex);
							$('#UpdateWHOISForm input[name="'+handle_type+'Initials"]').val(data.resultSet[0].Initials);
							$('#UpdateWHOISForm input[name="'+handle_type+'SurName"]').val(data.resultSet[0].SurName);
							$('#UpdateWHOISForm input[name="'+handle_type+'Address"]').val(data.resultSet[0].Address);
							$('#UpdateWHOISForm input[name="'+handle_type+'ZipCode"]').val(data.resultSet[0].ZipCode);
							$('#UpdateWHOISForm input[name="'+handle_type+'City"]').val(data.resultSet[0].City);
							$('#UpdateWHOISForm select[name="'+handle_type+'Country"]').val(data.resultSet[0].Country);
							
							$('#UpdateWHOISForm input[name="'+handle_type+'PhoneNumber"]').val(data.resultSet[0].PhoneNumber);
							$('#UpdateWHOISForm input[name="'+handle_type+'FaxNumber"]').val(data.resultSet[0].FaxNumber);
							$('#UpdateWHOISForm input[name="'+handle_type+'EmailAddress"]').val(data.resultSet[0].EmailAddress);
							
							// Copy handle data to labels
							$('#link_'+handle_type+'_handle').attr('href','handles.php?page=show&id=' + data.resultSet[0].Identifier).html(data.resultSet[0].Handle);
							
							$('#label_'+handle_type+'_registrarhandle').html((data.resultSet[0].RegistrarHandle) ? data.resultSet[0].RegistrarHandle : '-');
							if(data.resultSet[0].RegistrarHandle && data.resultSet[0].RegistrarHandle != data.resultSet[0].Handle){
								$('#div_'+handle_type+'_registrarhandle').show();
							}else{
								$('#div_'+handle_type+'_registrarhandle').hide();
							}
							
							$('#label_'+handle_type+'_companyname').html((data.resultSet[0].CompanyName) ? data.resultSet[0].CompanyName : '-');
							$('#label_'+handle_type+'_company_number').html((data.resultSet[0].CompanyNumber) ? data.resultSet[0].CompanyNumber : '-');
							$('#label_'+handle_type+'_taxnumber').html((data.resultSet[0].TaxNumber) ? data.resultSet[0].TaxNumber : '-');
							$('#label_'+handle_type+'_legal_form').html((data.resultSet[0].LegalFormLong) ? data.resultSet[0].LegalFormLong : '-');
							if(data.resultSet[0].CompanyName){
								$('#div_'+handle_type+'_company').show();
							}else{
								$('#div_'+handle_type+'_company').hide();
							}
							
							$('#label_'+handle_type+'_contact_person').html((data.resultSet[0].Initials + data.resultSet[0].SurName) ? data.resultSet[0].Initials + ' ' + data.resultSet[0].SurName : '-');
							$('#label_'+handle_type+'_address').html((data.resultSet[0].Address) ? data.resultSet[0].Address : '-');
							$('#label_'+handle_type+'_zipcode_city').html((data.resultSet[0].ZipCode + data.resultSet[0].City) ? data.resultSet[0].ZipCode + ' ' + data.resultSet[0].City : '-');
							$('#label_'+handle_type+'_country').html((data.resultSet[0].CountryLong) ? data.resultSet[0].CountryLong : '-');
							
							$('#label_'+handle_type+'_phonenumber').html((data.resultSet[0].PhoneNumber) ? data.resultSet[0].PhoneNumber : '-');
							$('#label_'+handle_type+'_faxnumber').html((data.resultSet[0].FaxNumber) ? data.resultSet[0].FaxNumber : '-');
							$('#label_'+handle_type+'_emailaddress').html((data.resultSet[0].EmailAddress) ? data.resultSet[0].EmailAddress : '-');
							
							// Handle custom client fields?
							loadCustomClientFields(data.resultSet[0].customfields_list, data.resultSet[0], true, 'UpdateWHOISForm', handle_type);
	
						}
					}
				, "json");
			}else{
				// Do nothing
			}
		});
	}

});