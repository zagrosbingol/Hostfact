$(function(){
	$('#dialog_order_paid').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

	if($('#OrderForm').html() != null){	
		
		
		$('#order_paid_btn').click(function(){ 
			$(this).parents('form').submit();
		});
		
		function toggleFormPart(part){
			switch(part){
				case 'CompanyName':
					if($('input[name="CustomerCompanyName"]').val() != "")
					{
						$('#CompanyName_extra').slideDown('slow');
					}
					else
					{
						$('#CompanyName_extra').slideUp('slow');	
					}
					break;
				case 'AbnormalInvoiceData':
					if($('input[name=AbnormalInvoiceData]:checked').val() != null)
					{
						$('#AbnormalInvoiceData_extra').slideDown('slow');
						$('#AbnormalInvoiceData_copylink').fadeIn('slow');
					}
					else
					{
						$('#AbnormalInvoiceData_extra').slideUp('slow');
						$('#AbnormalInvoiceData_copylink').fadeOut('slow');	
					}
					break;	
				case 'CustomerPanelAccess':
					if($('input[name=CustomerPanelAccess]:checked').val() != null)
					{
						$('#CustomerPanelAccess_extra').slideDown('slow');
					}
					else
					{
						$('#CustomerPanelAccess_extra').slideUp('slow');
					}
					break;
			}
		}
		
		$('input[name="CustomerCompanyName"]').keyup(function(){ toggleFormPart('CompanyName'); }).change(function(){ toggleFormPart('CompanyName'); });
		$('input[name=AbnormalInvoiceData]').change(function(){ toggleFormPart('AbnormalInvoiceData'); });
		$('input[name="CustomerPanelAccess"]').change(function(){ toggleFormPart('CustomerPanelAccess'); });
		
		
		// International version only
		if(IS_INTERNATIONAL == 'true')
		{
			$('select[name="CustomerCountry"], select[name="CustomerInvoiceCountry"]').change(function(){
				
				var NamePrefix = $(this).attr('name').replace('Country', '');
				getCountryStates($(this).val(), NamePrefix, '');	
			});
		}
		
		$('input[name="CustomerTaxNumber"]').blur(function(){
			
			if($(this).val() != ''){
				$('#vat_status').html('<img src="images/icon_circle_loader_grey.gif" style="margin:6px 0 6px 6px;" />');
			
				$.post('XMLRequest.php', { action: 'vat_check', vat: $(this).val(), cc: $('select[name="CustomerCountry"]').val() }, function(data){
					if(data == 'OK'){
						$('#vat_status').html('<span class="loading_green">'+__LANG_VAT_NUMBER_IS_VALID+'</span>');
					}else if(data == 'BAD'){
						$('#vat_status').html('<span class="loading_red">'+__LANG_VAT_NUMBER_IS_NOT_VALID+'</span>');
					}else if(data == 'UNAVAILABLE'){
						$('#vat_status').html('<span class="loading_orange">'+__LANG_CHECK_TEMPORARILY_UNAVAILABLE+'</span>');
					}else{
						$('#vat_status').html('');
					}
				});
			}else{
				$('#vat_status').html('');
			}
			
		});
		
		// Show only
		if($('#delete_order')){
			$('#delete_order').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});	
			$('#delete_order.autoopen').dialog('open');	
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_order_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_order_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_order_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
		
		$('#Status_text').click(function(){ 
			$(this).hide(); 
			$('select[name=Status]').show().focus(); 
			
			$('select[name=Status]').blur(function(){ 
				$(this).hide(); 
				$('#Status_text').html(htmlspecialchars($(this).find('option:selected').text()) + " " + STATUS_CHANGE_ICON); 
				$('#Status_text').show();			
			});	
		});
		
		// Switch between new or existing customer
		$('input[name="CustomerType"]').click(function(){
			if($(this).val() == 'new')
			{
				$('#debtor_select').hide();
				$('input[name="Debtor"]').val('').change();
				
				$('[href="#tab-newdebtor"]').closest('li').show();
			}
			else
			{
				$('#debtor_select').show();
				$('[href="#tab-newdebtor"]').closest('li').hide();
			}
		});
		
		$('input[name="Debtor"]').change(function(){
			
			if($(this).val() == ""){
				$('#formJQ-DebtorCode').html('<i>&lt;&lt; '+__LANG_DEBTOR_CODE+' &gt;&gt;</i>');
			}else{
				debtorid = $(this).val();
				
				// Ophalen debiteurgegevens en plaatsen veld...
				$.post("XMLRequest.php", { action: 'invoice_get_debtor', debtor: debtorid }, function(data){
					$('#formJQ-DebtorCode').html(data.DebtorCode);
					
					$('#formJQ-CompanyName').html(htmlspecialchars(data.CompanyName));
					$('#formJQ-Name').html(htmlspecialchars(data.Initials + ' ' + data.SurName));
					$('#formJQ-Address').html(htmlspecialchars(data.Address));
					$('#formJQ-ZipCodeCity').html(htmlspecialchars(data.ZipCode + ' ' + data.City));
					$('#formJQ-Country').html(htmlspecialchars(data.CountryLong));
					$('#formJQ-EmailAddress').html('<span style="display: inline-block">' + htmlspecialchars(data.EmailAddress).replace(/&amp;/g,'&').replace(/;/g, ',&nbsp;&nbsp;</span><span style="display: inline-block">') + '</span>');
					
					// Toggle
					$('#enveloppe #enveloppe_text').show();
					$('#enveloppe #enveloppe_input').hide();
					
					$('#edit_enveloppe_data').show();
					
					$('#inputJQ-CompanyName').val(data.CompanyName);
					$('#inputJQ-Initials').val(data.Initials);
					$('#inputJQ-SurName').val(data.SurName);
					$('#inputJQ-Sex').val(data.Sex);
					$('#inputJQ-Address').val(data.Address);
					$('#inputJQ-ZipCode').val(data.ZipCode);
					$('#inputJQ-City').val(data.City);
					$('#inputJQ-Country').val(data.Country);
					$('#inputJQ-EmailAddress').val(data.EmailAddress.replace(/;/g, ', '));
				
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
						
						$('#inputJQ-Address2').val(data.Address2);
						getCountryStates(data.Country, '', data.State);
					}
				
					$('#formJQ-Taxable').val(data.Taxable);
					$('input[name="TaxRate1"]').val('');
					if(data.Taxable == "true"){	
						$('input[name^="TaxPercentage"]').each(function(){
						
							$(this).parent().find('span').html(vat(parseFloat($(this).val()*100)) + '%');
							
							// Show taxpercentage
							LineID = $(this).attr('name').replace('TaxPercentage[','').replace(']','');
							$('#taxadjuster-'+LineID).removeClass('hide');							
						});
						vatshifthelper = true;
						InvoiceLines = parseInt($('input[name=NumberOfElements]').val());
						for(x = 0; x <= InvoiceLines; x++){
							getLineTotal(x, true);
							if($('#formJQ-TaxPercentage-'+x).closest('tr').is(':visible') && parseFloat($('#formJQ-TaxPercentage-'+x).val()) > 0){
								vatshifthelper = false;
							}
						}
						if(vatshifthelper){
							$('#shiftvat_div').show();
							$('input[name="VatShift_helper"]').val('true');
							$('input[name="VatShift"]').prop('checked',true);
						}else{
							$('#shiftvat_div').hide();
							$('input[name="VatShift_helper"]').val('');
							$('input[name="VatShift"]').prop('checked',false);
						}
					}else if(data.TaxRate1 != '' || parseInt(data.TaxRate1) == data.TaxRate1){	
						$('input[name="TaxRate1"]').val(data.TaxRate1);
						$('input[name^="TaxPercentage"]').each(function(){
						
							$(this).parent().find('span').html(vat(formatAsFloatVat(data.TaxRate1 * 100, (data.TaxRate1.toString().length - data.TaxRate1.toString().indexOf('.') - 3))) + '%');	
							
							// Show taxpercentage
							LineID = $(this).attr('name').replace('TaxPercentage[','').replace(']','');
							$('#taxadjuster-'+LineID).addClass('hide');
						});
						
						if(data.TaxRate1 !== '' && data.TaxRate1 == 0){
							$('#shiftvat_div').show();
							$('input[name="VatShift_helper"]').val('true');
							$('input[name="VatShift"]').prop('checked',true);
						}else{
							$('#shiftvat_div').hide();
							$('input[name="VatShift_helper"]').val('');
							$('input[name="VatShift"]').prop('checked',false);
						}
						
						InvoiceLines = parseInt($('input[name=NumberOfElements]').val());
						for(x = 0; x <= InvoiceLines; x++){
							getLineTotal(x, true);
						}
					}else{
						$('input[name^="TaxPercentage"]').parent().find('span').html('0%');
						$('.taxadjuster').addClass('hide');
						
						$('#shiftvat_div').show();
						$('input[name="VatShift_helper"]').val('true');
						$('input[name="VatShift"]').prop('checked',true);
						
						InvoiceLines = parseInt($('input[name=NumberOfElements]').val());
						for(x = 0; x <= InvoiceLines; x++){
							getLineTotal(x, true);
						}
					}
					
					if(parseInt(data.TaxRate1) == 0)
					{
						$('#vatcalcmethod_helper_div').hide();
					}
					else
					{
						$('#vatcalcmethod_helper_div').show();
					}
					
					$('input[name="TaxRate2"]').val('');
					vatshifthelper = true;
					if(data.TaxRate2 != '' || parseInt(data.TaxRate2) == data.TaxRate2){	
						$('input[name="TaxRate2"]').val(data.TaxRate2);
						
						$('input[name="TaxRate"]').parent().find('span').first().html($('input[name="TotalTaxRadio"][value="'+data.TaxRate2+'"]').next('span').html());
						getLineTotal(0);		
								
						$('.taxadjusterTotal').addClass('hide');
						if(data.TaxRate2 > 0)
						{
							vatshifthelper = false;
						}
					}else{
						$('input[name="TotalTaxRadio"]:checked').change();
						$('.taxadjusterTotal').removeClass('hide');
						
						if($('input[name="TotalTaxRadio"]:checked').val() > 0){
							vatshifthelper = false;
						}
					}
					
					// Hide vatshift box when there is a total tax
					if(vatshifthelper === false){
						$('#shiftvat_div').hide();
						$('input[name="VatShift_helper"]').val('');
						$('input[name="VatShift"]').prop('checked',false);
					}
					
					$('#formJQ-TaxNumber').html(htmlspecialchars(data.TaxNumber));
			
					$('#formJQ-Authorisation').val(data.InvoiceAuthorisation);
					$('#formJQ-InvoiceMethod').val(data.InvoiceMethod);
					$('#edit_enveloppe_data').show();
										
					$('#enveloppe').show();
				}, "json");
			}				
		});
		
	}

    $(document).on('click', '#edit_enveloppe_data',function(){
		$(this).hide();
		$('#enveloppe #enveloppe_text').hide();
		$('#enveloppe #enveloppe_input').show();
		
	});
	
});

// Copy Invoice data from general data in debtor add/edit form
function copyOrderInvoiceData(){
    $('input[name=CustomerInvoiceCompanyName]').val($('input[name=CustomerCompanyName]').val());
    $('select[name=CustomerInvoiceSex]').val($('select[name=CustomerSex]').val());
	$('input[name=CustomerInvoiceInitials]').val($('input[name=CustomerInitials]').val());
    $('input[name=CustomerInvoiceSurName]').val($('input[name=CustomerSurName]').val());
    $('input[name=CustomerInvoiceAddress]').val($('input[name=CustomerAddress]').val());
    if(IS_INTERNATIONAL == 'true'){ $('input[name=CustomerInvoiceAddress2]').val($('input[name=CustomerAddress2]').val()); }
    $('input[name=CustomerInvoiceZipCode]').val($('input[name=CustomerZipCode]').val());
    $('input[name=CustomerInvoiceCity]').val($('input[name=CustomerCity]').val());
    if(IS_INTERNATIONAL == 'true'){ 
    	
		if($('input[name=CustomerState]').val()){
    		getCountryStates($('select[name=CustomerCountry]').val(), 'CustomerInvoice', $('input[name=CustomerState]').val());
    	}else{
    		getCountryStates($('select[name=CustomerCountry]').val(), 'CustomerInvoice', $('select[name=CustomerStateCode]').val());	
    	}
   	}
    $('select[name=CustomerInvoiceCountry]').val($('select[name=CustomerCountry]').val());
	$('input[name=CustomerInvoiceEmailAddress]').val($('input[name=CustomerEmailAddress]').val().replace(/;/g, ', '));   
}