$(function(){
	/**
	 * PriceQuote pages
 	 */
	
	// Show only
	$('#pricequotedialog_history').dialog({modal: true, autoOpen: false, resizable: false, width: 700, height: 'auto'});
	
	if($('#PriceQuoteForm').html() != null){	
		
		$('#Status_text').click(function(){ 
			$(this).hide(); 
			$('select[name=Status]').show().focus(); 
			
			$('select[name=Status]').blur(function(){ 
				$(this).hide(); 
				$('#Status_text').html(htmlspecialchars($(this).find('option:selected').text()) + " " + STATUS_CHANGE_ICON); 
				$('#Status_text').show();			
			});	
		});
		
		$('input[name=Debtor]').change(function(){
			
			if($(this).val() == ""){
				$('#enveloppe').hide();
				$('#formJQ-DebtorCode').html('<i>&lt;&lt; '+__LANG_DEBTOR_CODE+' &gt;&gt;</i>');
			}else{
				// Ophalen debiteurgegevens en plaatsen veld...
				$.post("XMLRequest.php", { action: 'invoice_get_debtor', debtor: $(this).val(), type: 'pricequote' }, function(data){
					$('#formJQ-DebtorCode').html(data.DebtorCode);
					$('#formJQ-DebtorCode_copy').html(data.DebtorCode);
					
					$('#formJQ-CompanyName').html(htmlspecialchars(data.CompanyName));
					$('#formJQ-Name').html(htmlspecialchars(data.Initials + ' ' + data.SurName));
					$('#formJQ-Address').html(htmlspecialchars(data.Address));
					if(data.Address2){
						$('#formJQ-Address2').html(htmlspecialchars(data.Address2)).removeClass('hide');
					}else{
						$('#formJQ-Address2').html('').addClass('hide');
					}
					$('#formJQ-ZipCodeCity').html(htmlspecialchars(data.ZipCode + ' ' + data.City));
					if(data.StateName){
						$('#formJQ-State').html(htmlspecialchars(data.StateName)).removeClass('hide');
					}else{
						$('#formJQ-State').html('').addClass('hide');
					}
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
					$('#inputJQ-Address2').val(data.Address2);
					$('#inputJQ-ZipCode').val(data.ZipCode);
					$('#inputJQ-City').val(data.City);
					getCountryStates(data.Country, '', data.State);
					$('#inputJQ-Country').val(data.Country);
					$('#inputJQ-EmailAddress').val(data.EmailAddress.replace(/;/g, ', '));
				
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
					
					if(data.TaxNumber){
						$('#formJQ-TaxNumber').html('<br />' + __LANG_VAT_NUMBER + ': ' + htmlspecialchars(data.TaxNumber));
					}else{
						$('#formJQ-TaxNumber').html('');
					}
				
					$('#formJQ-Authorisation').val(data.InvoiceAuthorisation);
					if(data.PriceQuoteTemplate > 0){
						$('#formJQ-Template').val(data.PriceQuoteTemplate);
					}
					$('#formJQ-PriceQuoteMethod').val(data.InvoiceMethod);
					$('#edit_enveloppe_data').show();
					
					$('#enveloppe').show();

                    // Custom fields
                    if(data.CustomFields != undefined)
                    {
                        changeCustomFields(data.CustomFields, data.Custom);
                    }

				}, "json");
			}				
		});
		
	}

    $(document).on('click', '#edit_enveloppe_data',function(){
		$(this).hide();
		$('#enveloppe #enveloppe_text').hide();
		$('#enveloppe #enveloppe_input').show();
		
	});

    $(document).on('click', '#edit_extra_enveloppe_data',function(){
		$('#edit_extra_enveloppe_data').hide();
		
		// Pricequote
		$('#extra_invoicedata_input_text #PriceQuoteDate_text').hide();
		$('#extra_invoicedata_input_text #PriceQuoteDate').show();
		
		$('#extra_invoicedata_input_text #PriceQuoteCode_text').hide();
		$('#extra_invoicedata_input_text #PriceQuoteCode').show();
		
		// Show Reference Input
		$('#extra_invoicedata_input_text #ReferenceNumber_text').hide();
		$('#extra_invoicedata_input_text #ReferenceNumber').show();
		
		$('#extra_invoicedata_input_text #OrderCode_text').hide();
		$('#extra_invoicedata_input_text #OrderCode').show();
		
		if($('select[name=Status]').val() == 0){
			$('#add_existing_invoice').show();
			$('#edit_extra_enveloppe_data').hide();
		}
	});
	
	$('#dialog_accept_pricequote').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#accept_pricequote_btn').click(function(){
		document.form_accept_pricequote.submit();
		$('#templatelocation').dialog('close');
	});
	$('.acceptQuestion').click(function(event){
		event.preventDefault();
		$('#dialog_accept_pricequote').dialog('open');
		$('#dialog_accept_pricequote').find('form').attr('action', event.target);
	});
    
    if($('#dialog_makeinvoice')){
    	$('#dialog_makeinvoice').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
        $('#dialog_makeinvoice_btn').click(function(){
    		document.form_dialog_makeinvoice.submit();
    	});
    }
    
	$("input[name=createinvoice]").click(function(){
			if($(this).val() == 'yes'){
				$('.accept_pricequote_options').show();    
			}else{
				$('.accept_pricequote_options').hide();
			}
	});
	
	$('#dialog_decline_pricequote').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#decline_pricequote_btn').click(function(){
		document.form_decline_pricequote.submit();
	});
	$('.declineQuestion').click(function(event){
		event.preventDefault();
		$('#dialog_decline_pricequote').dialog('open');
		$('#dialog_decline_pricequote').find('form').attr('action', event.target);
	});
	
	// Show only
	if($('#delete_pricequote')){
		$('#delete_pricequote').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});		
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_pricequote_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_pricequote_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_pricequote_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
});