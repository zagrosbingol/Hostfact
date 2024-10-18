$(function(){
	
	// Define dialogs
	$('#concept_date_change').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#credit_invoice_modal').dialog({modal: true, autoOpen: false, resizable: false, width: 575, height: 'auto'});
	
	$('#add_existing_invoice').click(function(){ 
			$('#concept_date_change').dialog('open');

            $(document).on('change', 'select[name="ConceptDateChangeStatus"]', function(){
				if($('select[name="ConceptDateChangeStatus"]').val() != '')
				{
					$('#concept_date_change_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#concept_date_change_btn').removeClass('button1').addClass('button2');
				}
			});
			
			$('#concept_date_change_btn').click(function(){
				
				if($('select[name="ConceptDateChangeStatus"]').val() == ''){
					return false;
				}
				
				if($('select[name="ConceptDateChangeStatus"] option:selected').val() > 0){
					$('select[name=Status]').val($('select[name="ConceptDateChangeStatus"] option:selected').val());
					$('#Status_text').html(htmlspecialchars($('select[name=Status]').find('option:selected').text()) + " " + STATUS_CHANGE_ICON);
					$('input[name=InvoiceCode]').val($('input[name="ConceptDateChangeInvoiceCode"]').val());
					$('#InvoiceCode_text').html($('input[name="ConceptDateChangeInvoiceCode"]').val());
					$('input[name=InvoiceDate]').val($('input[name="ConceptDateChangeInvoiceDate"]').val());
					$('#InvoiceDate_text').html($('input[name="ConceptDateChangeInvoiceDate"]').val());
					
					$('#add_existing_invoice').hide();
					$('#edit_extra_enveloppe_data').show();
				}else{
					$('#add_existing_invoice').show();
					$('#edit_extra_enveloppe_data').hide();
				}
								
				$('#concept_date_change').dialog('close');
			});
		});
	
	var productsearch_delay = (function(){
		var number_timer = 0;
		return function(callback, ms){
		clearTimeout (number_timer);
		number_timer = setTimeout(callback, ms);
	};
	})();
	
	var mouse_is_inside = false;
	var mouse_is_inside_enveloppe = false;
	var mouse_is_inside_extra = false;
	var mouse_is_select = false;
	
	// Show only
	$('#invoicedialog_partpayment').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#invoicedialog_clone').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#clone_btn').click(function(){
		document.form_clone.submit();	
	});
	$('#invoicedialog_invoicemethod').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#invoicedialog_history').dialog({modal: true, autoOpen: false, resizable: false, width: 700, height: 'auto'});
	
	$('#dialog_invoice_summation').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#dialog_invoice_summation.autoopen').dialog('open');
	$('#summation_btn').click(function(){ 
		$(this).parents('form').submit();
	});
	
	$('#dialog_paymentprocess').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#dialog_paymentprocess.autoopen').dialog('open');
	$('input[name="paymentprocess_imsure"]').click(function(){
		if($('input[name="paymentprocess_imsure"]:checked').val() != null)
		{
			$('#paymentprocess_btn').removeClass('button2').addClass('button1');
		}
		else
		{
			$('#paymentprocess_btn').removeClass('button1').addClass('button2');
		}
	});
	$('#paymentprocess_btn').click(function(){
		if($('#paymentprocess_imsure').html() == null ||  $('input[name="paymentprocess_imsure"]:checked').val() != null)
		{
			document.form_paymentprocess.submit();
		}	
	});
	
	$('#dialog_invoice_reminder').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#dialog_invoice_reminder.autoopen').dialog('open');
	$('#reminder_btn').click(function(){ 
		$(this).parents('form').submit();
	});

    $(document).on('click', '#credit_invoice_btn', function() {
		document.form_credit.submit();
	});

	// Partial crediting
	$('#credit_invoice').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

    if($('#credit_invoice')) {
        $('input[name=imsure]').click(function () {
            if ($('input[name=imsure]:checked').val() != null) {
                $('#credit_invoice_btn').removeClass('button2').addClass('button1');
            }
            else {
                $('#credit_invoice_btn').removeClass('button1').addClass('button2');
            }
        });
    }

	// Change credit button text and functionality in modal based on choice
    $(document).on('change', 'input[name="credit"]', function() {
		if ($(this).val() =='credit_whole') {
			$('.credit_btn').attr('id', 'credit_invoice_btn');
			$('.credit_button_text').removeClass('hide');
			$('.credit_partial_button_text').addClass('hide');

		} else if ($(this).val() =='credit_partially') {
			$('.credit_btn').attr('id','credit_choose_rows');
			$('.credit_button_text').addClass('hide');
			$('.credit_partial_button_text').removeClass('hide');
		}
	});

	// Credit partially UI handler function
    $(document).on('click', '#credit_choose_rows', function(){
		$('html, body').animate({
			scrollTop: $('.table1').offset().top - 60
		}, 'slow');

		// Reset credit input to avoid issues with modal re-opening
		$('input:radio[name="credit"][value="credit_whole"]').click();

		$('#credit_invoice').dialog('close');
		// Show elements necessary
		$('.white_overlay, .h4_choose_rows, .credit_checkbox_head, .credit_checkbox_row, .credit_checkbox_row_discount, .credit_arrow, .partial_credit_buttons, .credit_checkbox_number_input').removeClass('hide');
		// add relative to content
		$('#content').css('position','relative');
		// Hide elements
		$('.credit_checkbox_number_span').addClass('hide');
		// Style table
		$('.table-th-date, .table-tr-date').addClass('padding-left-0');
		$('form[name="form_lines"] table.table1').addClass('credit_table');

		// Add action to form
		$('form[name="form_lines"]').attr('action', $(this).parents('form').attr('action'));
	});

	// Post partly credit
    $(document).on('click', '#credit_partial_btn', function () {
		$('form[name="form_lines"]').submit();
	});

	// Remove or restore UI elements used for partial crediting
    $(document).on('click', '#credit_partial_cancel_btn', function () {
		// Reset table styles
		$('.table-th-date').addClass('reset-th');
		$('.table-th-date, .table-tr-date').removeClass('padding-left-0');
		// Hide partial credit UI elements
		$('.credit_checkbox_head, .credit_checkbox_row, .white_overlay, .credit_checkbox_row_discount, .credit_arrow, h4.h4_choose_rows, .partial_credit_buttons, .credit_partial_button_text, .credit_checkbox_number_input').addClass('hide');
		// Show elements
		$('.credit_checkbox_number_span').removeClass('hide');
		$('form[name="form_lines"] table.table1').removeClass('credit_table');
		// Restore button in credit modal
		$('.credit_btn').attr('id','submit_button');
		$('.credit_button_text').removeClass('hide');
	});

	// Only enable partial crediting button when at least 1 row is selected
	$('input[name="credit_checkbox"], .credit_checkbox').change(function () {

		if($(this).attr('name') == 'credit_checkbox')
		{
			// Loop all input fields
			$('.credit_checkbox').each(function(index, element){
				// One checkbox, toggle the input field
				if($(element).prop('checked') === true)
				{
					$(element).parents('tr').find('input[name^="Number["]').prop('disabled', false);
				}
				else
				{
					$(element).parents('tr').find('input[name^="Number["]').prop('disabled', true);
				}
			});
		}
		else
		{
			// One checkbox, toggle the input field
			if($(this).prop('checked') === true)
			{
				$(this).parents('tr').find('input[name^="Number["]').prop('disabled', false);
			}
			else
			{
				$(this).parents('tr').find('input[name^="Number["]').prop('disabled', true);
			}
		}

		if($('.credit_checkbox:checked').length > 0){
			$('#credit_partial_btn').removeClass('disabled');
		} else{
			$('#credit_partial_btn').addClass('disabled');
		}
	});
	
	$('#dialog_directdebit_failed').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#dialog_directdebit_failed_btn').click(function(){ 
		$(this).parents('form').submit();
		$('#dialog_directdebit_failed').dialog('close');
	});
	
	$('#dialog_block').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
    $('#dialog_block.autoopen').dialog('open');

    $('#dialog_edit_scheduled').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
    $('#dialog_schedule').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
    $('#dialog_schedule.autoopen').dialog('open');
	
	$('#block_btn').click(function()
	{
		document.form_block.submit();
	});
    $('#schedule_btn').click(function()
    {
        document.form_schedule.submit();
    });
	
	$('#dialog_paid_confirm').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	
	$('#confirm_paid_btn').click( function()
	{
		$('#dialog_paid_confirm form').submit();
	});
	
	if($('#InvoiceForm').html() != null){	
		
		var NormalInvoiceTemplate = false;
        var DirectDebitStatus = false;
		
		$('#Status_text').click(function(){ 
			$(this).hide(); 
			$('select[name=Status]').show().focus(); 
			
			$('select[name=Status]').change(function(){
				$(this).hide(); 
				$('#Status_text').html(htmlspecialchars($(this).find('option:selected').text()) + " " + STATUS_CHANGE_ICON); 
				$('#Status_text').show();
				
				// Factuurnummer aanpassen
				if($(this).val() > 0){
					$('input[name=InvoiceCode]').val($('input[name=NewNumber]').val());
					$('#InvoiceCode_text').html($('input[name=NewNumber]').val());
					$('input[name=InvoiceDate]').val($('input[name=NewDate]').val());
					$('#InvoiceDate_text').html($('input[name=NewDate]').val());
					
					$('#add_existing_invoice').hide();
					if(!$('#InvoiceCode').is(':visible')){
						$('#edit_extra_enveloppe_data').show();
					}
				}else{ 
					$('input[name="InvoiceCode"]').val($('input[name="ConceptCode"]').val()).hide();
					$('#InvoiceCode_text').html($('input[name="ConceptCode"]').val()).show();
					
					$('#InvoiceDate_text').show();
					$('#InvoiceDate').hide();
					
					// default text for concept-date
					$('#InvoiceDate_text').html('<i>&lt;&lt; '+__LANG_INVOICE_DATE_OF_DISPATCH+' &gt;&gt;</i>');
					
					$('#add_existing_invoice').show();
					$('#edit_extra_enveloppe_data').hide();
				}
				
				// If paid, show paydate
				if($(this).val() == 4){ $('#formJQ-PayDate').show(); }else{ $('#formJQ-PayDate').val('').hide(); }			
				
				// If (possible) sent, show sentdate
				if($(this).val() < 2){ $('#formJQ-SentDate').val('').hide(); }else{ $('#formJQ-SentDate').show(); }	
				
				// If credit invoice, change invoicetemplate	
				if($(this).val() == 8 && CREDIT_INVOICE_TEMPLATE > 0){
					if(NormalInvoiceTemplate === false){ 
						NormalInvoiceTemplate = $('select[name="Template"] option:selected').val();
					}
					$('select[name="Template"]').val(CREDIT_INVOICE_TEMPLATE);
				}else if(CREDIT_INVOICE_TEMPLATE > 0 && NormalInvoiceTemplate > 0){
					$('select[name="Template"]').val(NormalInvoiceTemplate);
				}

                // If credit invoice, disable direct debit, otherwise set to previous value
                if($(this).val() == 8)
                {
                    DirectDebitStatus = $('select[name="Authorisation"] option:selected').val();
                    $('select[name="Authorisation"]').val('no');

                    // Hide input field
                    $('#div-authorisation').addClass('hide');
                }
                else
                {
                    // Use previous value again
                    if(DirectDebitStatus !== false)
                    {
                        $('select[name="Authorisation"]').val(DirectDebitStatus);
                    }
                    // Show the input field
                    $('#div-authorisation').removeClass('hide');
                }
						
			});	
			$('select[name=Status]').blur(function(){ 
				$(this).hide(); 
				$('#Status_text').html(htmlspecialchars($(this).find('option:selected').text()) + " " + STATUS_CHANGE_ICON); 
				$('#Status_text').show();
			});
		});
		
		$('input[name="Debtor"]').change(function(){
		
			if($(this).val() == ""){
				$('#enveloppe').hide();
				$('#formJQ-DebtorCode').html('<i>&lt;&lt; '+__LANG_DEBTOR_CODE+' &gt;&gt;</i>');
				$('#formJQ-DebtorCode_copy').html('<i>&lt;&lt; '+__LANG_DEBTOR_CODE+' &gt;&gt;</i>');
			}else{
				// Ophalen debiteurgegevens en plaatsen veld...
				$.post("XMLRequest.php", { action: 'invoice_get_debtor', debtor: $(this).val() }, function(data){
                    $('.containerTaxNumber').hide();

                    $('#formJQ-DebtorCode').html(data.DebtorCode);
					$('#formJQ-DebtorCode_copy').html(data.DebtorCode);
					
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
                    $('#inputJQ-TaxNumber').val(data.TaxNumber);

                    if(data.CompanyName.length > 0 || data.TaxNumber.length)
                    {
                        $('.containerTaxNumber').show();
                    }
				
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
					
					if(data.TaxNumber){
						$('#formJQ-TaxNumber').html(__LANG_VAT_NUMBER + ': ' + htmlspecialchars(data.TaxNumber));
					}else{
						$('#formJQ-TaxNumber').html('');
					}

                    // If credit invoice, disable direct debit, otherwise use client data
                    if($('select[name=Status]').val() == 8)
                    {
                        DirectDebitStatus = data.InvoiceAuthorisation;
                        $('select[name="Authorisation"]').val('no');
                    }
                    else
                    {
                        $('select[name="Authorisation"]').val(data.InvoiceAuthorisation);
                    }

					if(data.InvoiceTemplate > 0){
						if($('select[name=Status]').val() != 8){ $('#formJQ-Template').val(data.InvoiceTemplate); }
						NormalInvoiceTemplate = data.InvoiceTemplate;
					}
					$('#formJQ-InvoiceMethod').val(data.InvoiceMethod);
					$('input[name="Term"]').val(data.Term);
					
					$('#enveloppe').show();

                    // Custom fields
                    if(data.CustomFields != undefined)
                    {
                        changeCustomFields(data.CustomFields, data.Custom);
                    }

				}, "json");
			}
		});

        $(document).on('keyup', 'input[name="CompanyName"]', function()
        {
            if($('input[name="CompanyName"]').val() != '')
            {
                // Show the Taxnumber container
                $('.showTaxNumberInput').show();
                $('.containerTaxNumber').show();
            }
        });
		
		// After changing date, statuschange shouldnt affect the date anymore
		$('input[name="InvoiceDate"]').change(function(){
			$('input[name="NewDate"]').val($('input[name="InvoiceDate"]').val());
		});
		
	}

    $(document).on('click', '#edit_enveloppe_data',function(){
		$(this).hide();
		$('#enveloppe #enveloppe_text').hide();
		$('#enveloppe #enveloppe_input').show();

        $('input[name="CompanyName"]').trigger('keyup');
		
	});

    $(document).on('click', '#edit_extra_enveloppe_data',function(event){
		$('#edit_extra_enveloppe_data').hide();
		// Invoice
		if($('#InvoiceCode').val()){
			var InvoiceCodeIndexOf = $('#InvoiceCode').val().indexOf('[concept]');
		}
		
		if($('select[name=Status]').val() > 0 || InvoiceCodeIndexOf != 0){
			$('#extra_invoicedata_input_text #InvoiceCode_text').hide();
			$('#extra_invoicedata_input_text #InvoiceCode').show();
			
			$('#extra_invoicedata_input_text #InvoiceDate_text').hide();
			$('#extra_invoicedata_input_text #InvoiceDate').show();
		}
		
		// Show Reference Input
		$('#extra_invoicedata_input_text #ReferenceNumber_text').hide();
		$('#extra_invoicedata_input_text #ReferenceNumber').show();
		
		if($('select[name=Status]').val() == 0 && InvoiceCodeIndexOf == 0){
			$('#add_existing_invoice').show();
			$('#edit_extra_enveloppe_data').hide();
		}
	});
	
	$('#partpayment_btn').click(function(){
		document.form_partpayment.submit();	
	});


    if($('form[name="form_invoicemethod"]').length > 0)
    {
        // when changing the invoice method (in dialog)
        $(document).on('change', 'select[name="InvoiceMethod"]', function()
        {
            // if the invoice has no emailaddress, but the user wants to change the method which requires a email, show the email input
            if($('#NewMethodEmailAddress').length > 0)
            {
                if($(this).val() == '0' || $(this).val() == '3')
                {
                    $('#NewMethodEmailAddress').removeClass('hide');
                }
                else
                {
                    $('#NewMethodEmailAddress').addClass('hide');
                }
            }
        });
    }

	$('#invoicemethod_btn').click(function(){
		document.form_invoicemethod.submit();	
	});


    $(document).on('click', 'input[name^="PeriodicType"]', function(){
			LineID = $(this).attr('name').replace('PeriodicType[','').replace(']','');
			
			if($(this).val() == "once"){
				$('#Periodic-period-' + LineID).hide();
				$('#formJQ-Period-' + LineID).html(__LANG_ONCE);
				$('input[name="Periods['+LineID+']"]').val('1');
				$('#EndPeriod-' + LineID).html('');
				
				$('input[name="DiscountPercentageType['+LineID+']"][value="line"]').prop('checked', true).change();
				$(this).parents('tr').find('.discountpercentage_type').removeClass('pointer').find('div.arrowdown').addClass('hide');
				
				getLineTotal(LineID);
			}else{
				$('#Periodic-period-' + LineID).show();
				
				if($('select[name="Periodic['+LineID+']"]').val() == 'd'){
					$('select[name="Periodic['+LineID+']"]').val('m');
				}
				
				$(this).parents('tr').find('.discountpercentage_type').addClass('pointer').find('div.arrowdown').removeClass('hide');
				
				// Set prev to new, if no line identifier AND no product
				if(!$('input[name="ProductCode['+LineID+']"]').val() && !$('input[name="Item['+LineID+']"]').val())
				{
					$('select[name="Periodic['+LineID+']"]').attr('prev', 'new');
				}
				// If we don't have a period
				if($('input[name="StartPeriod['+LineID+']"]').html() == null){
					$('#formJQ-Period-' + LineID).html($('input[name="Periods['+LineID+']"]').val() + ' ' + $('select[name="Periodic['+LineID+']"] option:selected').text());
				}else{
				
					if($('input[name="StartPeriod['+LineID+']"]').attr('id') == ''){
						$('input[name="StartPeriod['+LineID+']"]').datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1});
					}
					
					calculatePeriod($('input[name="Periods['+LineID+']"]').val(), $('select[name="Periodic['+LineID+']"]').val(), $('input[name="StartPeriod['+LineID+']"]'), $('input[name="EndPeriod['+LineID+']"]'));
					$('#formJQ-Period-' + LineID).html($('input[name="StartPeriod['+LineID+']"]').val() + ' ' + __LANG_TILL + ' ' + $('input[name="EndPeriod['+LineID+']"]').val());
					$('#EndPeriod-' + LineID).html(__LANG_TILL + ' ' + $('input[name="EndPeriod['+LineID+']"]').val());
				}
			}

	});

    $(document).on('focus', 'select[name^="Periodic"]', function () {
        // Set prev if not new or undefined
        if ($(this).attr('prev') == undefined || $(this).attr('prev') != 'new') {
            $(this).attr('prev', $(this).val());
        }

    });
    $(document).on('click', 'select[name^="Periodic"]', function () {
        if (mouse_is_select == 'changed') {
            mouse_is_select = false;
        }
        else {
            mouse_is_select = true;
        }
    });

    $(document).on('change', 'select[name^="Periodic"]', function () {
        mouse_is_select = 'changed';

        LineID = $(this).attr('name').replace('Periodic[', '').replace(']', '');

        // Check if we have a custom price, if not calc price period
        checkCustomPeriodPrice(LineID, 'Periodic');

        // If we don't have a period
        if ($('input[name="StartPeriod[' + LineID + ']"]').html() == null) {
            $('#formJQ-Period-' + LineID).html($('input[name="Periods[' + LineID + ']"]').val() + ' ' + $('select[name="Periodic[' + LineID + ']"] option:selected').text());
        } else {
            calculatePeriod($('input[name="Periods[' + LineID + ']"]').val(), $('select[name="Periodic[' + LineID + ']"]').val(), $('input[name="StartPeriod[' + LineID + ']"]'), $('input[name="EndPeriod[' + LineID + ']"]'));
            $('#formJQ-Period-' + LineID).html($('input[name="StartPeriod[' + LineID + ']"]').val() + ' ' + __LANG_TILL + ' ' + $('input[name="EndPeriod[' + LineID + ']"]').val());
            $('#EndPeriod-' + LineID).html(__LANG_TILL + ' ' + $('input[name="EndPeriod[' + LineID + ']"]').val());
        }
    });
    $(document).on('keyup', 'input[name^="Periods"]', function () {
        LineID = $(this).attr('name').replace('Periods[', '').replace(']', '');

        // If we don't have a period
        if ($('input[name="StartPeriod[' + LineID + ']"]').html() == null) {
            $('#formJQ-Period-' + LineID).html($('input[name="Periods[' + LineID + ']"]').val() + ' ' + $('select[name="Periodic[' + LineID + ']"] option:selected').text());
        } else {
            calculatePeriod($('input[name="Periods[' + LineID + ']"]').val(), $('select[name="Periodic[' + LineID + ']"]').val(), $('input[name="StartPeriod[' + LineID + ']"]'), $('input[name="EndPeriod[' + LineID + ']"]'));
            $('#formJQ-Period-' + LineID).html($('input[name="StartPeriod[' + LineID + ']"]').val() + ' ' + __LANG_TILL + ' ' + $('input[name="EndPeriod[' + LineID + ']"]').val());
            $('#EndPeriod-' + LineID).html(__LANG_TILL + ' ' + $('input[name="EndPeriod[' + LineID + ']"]').val());
        }

        // Check if we have a custom price, if not calc price period
        checkCustomPeriodPrice(LineID, 'Periods');
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

    $(document).on('change', 'input[name^="StartPeriod"]', function(){
			LineID = $(this).attr('name').replace('StartPeriod[','').replace(']','');
			calculatePeriod($('input[name="Periods['+LineID+']"]').val(), $('select[name="Periodic['+LineID+']"]').val(), $('input[name="StartPeriod['+LineID+']"]'), $('input[name="EndPeriod['+LineID+']"]'));
			$('#formJQ-Period-' + LineID).html($(this).val() + ' ' + __LANG_TILL + ' ' + $('input[name="EndPeriod['+LineID+']"]').val());
			$('#EndPeriod-' + LineID).html(__LANG_TILL + ' ' + $('input[name="EndPeriod['+LineID+']"]').val());
	});

    $(document).on('click', '.periodClick', function(){
			if($(this).find('span')){
				LineID = $(this).find('span').attr('id').replace('formJQ-Period-','');
				
				// Show custom prices?
				showCustomPeriodPrice(LineID);
				
				$('#Periodic-'+ LineID).show();
			}
	});

    $(document).on('click', '.taxAdjusterClick', function(){
			if($(this).find('.taxadjuster:visible')){
				LineID = $(this).find('.taxadjuster:visible').attr('id').replace('taxadjuster-','');
				$('#TaxList-'+ LineID).show();
			}
	});

    $(document).on('change', 'input[name^="TaxRadio"]', function(){
			LineID = $(this).attr('name').replace('TaxRadio[','').replace(']','');
			$('input[name="TaxPercentage['+LineID+']"]').val($(this).val());
			$('input[name="TaxPercentage['+LineID+']"]').parent().find('span').first().html(vat(formatAsFloatVat($(this).val() * 100, ($(this).val().toString().length - $(this).val().toString().indexOf('.') - 3))) + '%');
			
			if($('input[name="PriceExcl['+LineID+']"]:visible').html() != null){
				// Excl
				$('input[name="PriceIncl['+LineID+']"]').val(formatAsMoney(deformatAsMoney($('input[name="PriceExcl['+LineID+']"]').val()) * (1 + ($('input[name="TaxPercentage['+LineID+']"]').val() * 1)),5));
			}else{
				// Incl
				$('input[name="PriceExcl['+LineID+']"]').val(formatAsMoney(deformatAsMoney($('input[name="PriceIncl['+LineID+']"]').val()) / (1 + ($('input[name="TaxPercentage['+LineID+']"]').val() * 1)),5));
			}
			
			checkInvoicelineForShiftTax();
			
			getLineTotal(LineID);		
			$('.taxlist-dialog').hide();
	});

    $(document).on('keyup', 'input[name^="Number"]', function () {
        LineID = $(this).attr('name').replace('Number[', '').replace(']', '');
        getLineTotal(LineID);
    });
    $(document).on('keyup', 'input[name^="PriceExcl"], input[name^="PriceIncl"]', function () {
        LineID = $(this).attr('name').replace('PriceExcl[', '').replace('PriceIncl[', '').replace(']', '');

        if ($(this).attr('name').indexOf('Excl') >= 0) {
            // Excl
            $('input[name="PriceIncl[' + LineID + ']"]').val(formatAsMoney(deformatAsMoney($(this).val()) * (1 + ($('input[name="TaxPercentage[' + LineID + ']"]').val() * 1)), 5));
        } else {
            // Incl
            $('input[name="PriceExcl[' + LineID + ']"]').val(formatAsMoney(deformatAsMoney($(this).val()) / (1 + ($('input[name="TaxPercentage[' + LineID + ']"]').val() * 1)), 5));
        }

        getLineTotal(LineID);
    });
    $(document).on('keyup', 'input[name^="DiscountPercentage"]', function () {
        LineID = $(this).attr('name').replace('DiscountPercentage[', '').replace(']', '');
        getLineTotal(LineID);
    });

    $(document).on('click', '.discountpercentage_type', function () {
        if ($(this).hasClass('pointer')) {
            $(this).find('.discounttype-dialog').show();
        }
    });
    $(document).on('change', 'input[name^="DiscountPercentageType"]', function () {

        if ($(this).attr('value') == 'line') {
            $(this).parents('.discountpercentage_type').find('.discountpercentage_type_line').show();
            $(this).parents('.discountpercentage_type').find('.discountpercentage_type_subscription').hide();
        }
        else {
            $(this).parents('.discountpercentage_type').find('.discountpercentage_type_line').hide();
            $(this).parents('.discountpercentage_type').find('.discountpercentage_type_subscription').show();
        }


    });

    $(document).on('mouseenter', '.periodic-dialog, .discounttype-dialog', function () {
        mouse_is_inside = true;
    });
    $(document).on('mouseleave', '.periodic-dialog, .discounttype-dialog', function () {
        mouse_is_inside = false;
    });

    $(document).on('mouseenter', '.ui-datepicker', function () {
        mouse_is_inside = true;
    });
    $(document).on('mouseleave', '.ui-datepicker', function () {
        mouse_is_inside = false;
    });

    $(document).on('mouseenter', '.taxlist-dialog', function () {
        mouse_is_inside = true;
    });
    $(document).on('mouseleave', '.taxlist-dialog', function () {
        mouse_is_inside = false;
    });

    $(document).on('change', 'input[name="TotalTaxRadio"]', function(){
			$('input[name="TaxRate"]').val($(this).val());
			$('input[name="TaxRate"]').parent().find('span').first().html($(this).next('span').html());
			getLineTotal(0);		
			$('.taxlist-dialog').hide()
			
			checkInvoicelineForShiftTax();
	});
	
	$('body').mouseup(function(){ 
		if(mouse_is_select == true){
			mouse_is_select = false;
		}else if(! mouse_is_inside){
			$('.periodic-dialog').hide(); $('.discounttype-dialog').hide(); $('.taxlist-dialog').hide();
			$('select[name^="Periodic["]').attr('prev', '');
		}
	});
	
	$('select[name="VatCalcMethodHelper"]').change(function(){
		$('input[name="VatCalcMethod"]').val($(this).val());
		
		InvoiceLines = parseInt($('input[name=NumberOfElements]').val());
		for(x = 0; x <= InvoiceLines; x++){
			getLineTotal(x);
		}
	});
	
	$('#add_new_element').click(function(){
		// Always make the hidden element visible
		if($('#NewElement:visible').html() == null && $('input[name="id"]').val() > 0 && $('#NewElement').find('input[name^="Number"]').val() == 1){
			$('#NewElement').show();
			checkInvoicelineForShiftTax();
			return;
		}
		
		var NewElement = $('#NewElement').clone();
		
		
		// Remove default ID
		LineID = $(NewElement).find('input[name^=Date]').attr('name').replace('Date[','').replace(']','');
		$(NewElement).attr('id','');
		
		// Rename fields
		Counter = (1 * $('input[name=NumberOfElements]').val()) + 1;
		$(NewElement).find('input[name^=Date]').attr('name','Date[' + Counter + ']');
		$(NewElement).find('input[name^=Number]').attr('name','Number[' + Counter + ']');
		$(NewElement).find('input[name^=ProductCode]').attr('name','ProductCode[' + Counter + ']');
		$(NewElement).find('textarea[name^=Description]').attr('name','Description[' + Counter + ']');
		$(NewElement).find('input[name^=PriceExcl]').attr('name','PriceExcl[' + Counter + ']');
		$(NewElement).find('input[name^=PriceIncl]').attr('name','PriceIncl[' + Counter + ']');
		$(NewElement).find('input[name^="DiscountPercentageType["]').attr('name','DiscountPercentageType[' + Counter + ']');
		$(NewElement).find('input[name^="DiscountPercentage["]').attr('name','DiscountPercentage[' + Counter + ']');
		$(NewElement).find('input[name^=TaxPercentage]').attr('name','TaxPercentage[' + Counter + ']');
		$(NewElement).find('input[name^=PeriodicType]').attr('name','PeriodicType[' + Counter + ']');
		$(NewElement).find('input[name^=Periods]').attr('name','Periods[' + Counter + ']');
		$(NewElement).find('select[name^=Periodic]').attr('name','Periodic[' + Counter + ']');
		$(NewElement).find('input[name^=StartPeriod]').attr('name','StartPeriod[' + Counter + ']');
		$(NewElement).find('input[name^=EndPeriod]').attr('name','EndPeriod[' + Counter + ']');
		$(NewElement).find('input[name^=TaxRadio]').attr('name','TaxRadio[' + Counter + ']');
		
		
		// Rename id's
		$(NewElement).find('span[id=formJQ-Period-' + LineID+']').attr('id','formJQ-Period-' + Counter);
		$(NewElement).find('div[id=Periodic-' + LineID+']').attr('id','Periodic-' + Counter);
		$(NewElement).find('input[id=PeriodicType-' + LineID + '-once]').attr('id','PeriodicType-' + Counter + '-once');
		$(NewElement).find('input[id=PeriodicType-' + LineID + '-period]').attr('id','PeriodicType-' + Counter + '-period');
		$(NewElement).find('div[id=Periodic-period-' + LineID+']').attr('id','Periodic-period-' + Counter);
		$(NewElement).find('span[id=formJQ-TaxPercentageText-' + LineID+']').attr('id','formJQ-TaxPercentageText-' + Counter);
		$(NewElement).find('input[id=formJQ-TaxPercentage-' + LineID+']').attr('id','formJQ-TaxPercentage-' + Counter);
		$(NewElement).find('span[id=formJQ-LineTotal-' + LineID+']').attr('id','formJQ-LineTotal-' + Counter);
		$(NewElement).find('div[id=taxadjuster-' + LineID+']').attr('id','taxadjuster-' + Counter);
		$(NewElement).find('div[id=TaxList-' + LineID+']').attr('id','TaxList-' + Counter);
		
		
		// Change id-actions
		HTML = $(NewElement).html();
		HTML = str_replace("getProductData('"+LineID+"'","getProductData('"+Counter+"'",HTML);
		HTML = str_replace("data-inputfieldname=\"ProductCode["+LineID+"]\"","data-inputfieldname=\"ProductCode["+Counter+"]\"",HTML);
		HTML = str_replace('#Periodic-'+LineID,'#Periodic-'+Counter,HTML);
		HTML = str_replace('#TaxList-'+LineID,'#TaxList-'+Counter,HTML);
		HTML = str_replace('hasDatepicker','',HTML);
		HTML = str_replace("add_discount_rule('"+LineID+"'","add_discount_rule('"+Counter+"'",HTML);
		HTML = str_replace("remove_discount_rule('"+LineID+"'","remove_discount_rule('"+Counter+"'",HTML);
		HTML = str_replace("removeElement('"+LineID+"'","removeElement('"+Counter+"'",HTML);
		$(NewElement).html(HTML);

		// Hide discount rule
		$(NewElement).find('.discount').hide();
		$(NewElement).find('.discount_helper').hide();	
		$(NewElement).find('.discount_icon').show();
		$(NewElement).find('.discount_remove_icon').hide();
		$(NewElement).find('.discountpercentage_type_line').show();
		$(NewElement).find('.discountpercentage_type_subscription').hide();
		
		// Default values
		$(NewElement).find('input[name^=Number]').val('1');
		$(NewElement).find('textarea[name^=Description]').val('');
		$(NewElement).find('input[name^=PriceExcl]').val('');
		$(NewElement).find('input[name^=PriceIncl]').val('');
		$(NewElement).find('input[name^="DiscountPercentage["]').val('');
		$(NewElement).find('input[name^=ProductCode]').val('');
		$(NewElement).find('input[name^=StartPeriod]').val('');
		$(NewElement).find('input[name^=EndPeriod]').val('');
				
		$(NewElement).appendTo('#InvoiceElements').show();	
		
		$(NewElement).find('input[name^="DiscountPercentageType"][value="line"]').prop('checked', true);
		$(NewElement).find('.discountpercentage_type').removeClass('pointer').find('div.arrowdown').addClass('hide');
		
		// Datepicker fixes
		$(NewElement).find('img.ui-datepicker-trigger').remove();
		$(NewElement).find(".input_date .datepicker_icon").removeClass('hasDatepicker').attr('id', '');
		$(NewElement).find(".input_date .datepicker_icon").datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1, showOn: 'both', buttonImage: 'images/ico_calendar.png', buttonImageOnly: true, onSelect: function(dateText, inst) { $(this).parent().find('img.ui-datepicker-trigger').attr('title',dateText); }});
			
		$('input[name="StartPeriod['+Counter+']"]').attr('id','');
		
		$('#Periodic-period-'+Counter).hide();
		$('#PeriodicType-'+Counter+'-once').prop('checked', true);
		$('#formJQ-Period-' + Counter).html(__LANG_ONCE);

		// Reset for counter getLineTotal()
        var line_total_span = document.querySelector('#formJQ-LineTotal-' + Counter);
        line_total_span.innerHTML = formatAsMoney(0);
        delete line_total_span.dataset.TotalExcl;
        delete line_total_span.dataset.TotalIncl;
        delete line_total_span.dataset.TaxPercentage;
        delete line_total_span.dataset.TaxableAmount;

		if($('#formJQ-Taxable').val() == 'true'){
			$('#formJQ-TaxPercentageText-' + Counter).html(vat($('#formJQ-TaxPercentage-' + Counter).val()*100) + '%');
			$('input[name="TaxRadio['+Counter+']"][value="'+$('#formJQ-TaxPercentage-' + Counter).val()+'"]').prop('checked',true);
		}else{
			if($('input[name="TaxRate1"]').val() != ""){			
				$('#formJQ-TaxPercentageText-' + Counter).html(vat(formatAsFloatVat($('input[name="TaxRate1"]').val() * 100, ($('input[name="TaxRate1"]').val().toString().length - $('input[name="TaxRate1"]').val().toString().indexOf('.') - 3))) + '%');
				$('input[name="TaxRadio['+Counter+']"][value="'+$('#formJQ-TaxPercentage-' + Counter).val()+'"]').prop('checked',true);
			}else{
				$('#formJQ-TaxPercentageText-' + Counter).html(0 + '%');
			}
		}
		if($('#formJQ-Taxable').val() == 'true'){ $('#taxadjuster-'+Counter).removeClass('hide'); }
		
		// Update counter
		$('input[name=NumberOfElements]').val(Counter);
		
		// Start AutoGrow
		$('textarea[name="Description['+Counter+']"]').autoGrow();
		
		checkInvoicelineForShiftTax();
		
	});

    // When the user hits tab on the last select element of the invoice lines, trigger the action for a new invoice line
    $('.invoicetable').on('keydown', '.tr_invoiceelement input:visible:last', function(e)
    {
        if ((e.which == 9) || (e.keyCode == 9))
        {
            $('#add_new_element').trigger('click');
        }
    });
	
	// If form has been posted at least once, calculate totals
	if($('input[name=NumberOfElements]').val() > 0){
		getLineTotal(0);
	}

    $(document).on('mouseenter', '.tr_invoiceelement', function () {

        if ($('#InvoiceElements tr:visible').length == 1) {
            $(this).find('.remove_icon').css('opacity', '1');
            $(this).find('.discount_icon').css('opacity', '1');
            $(this).find('.discount_remove_icon').css('opacity', '1');
            $(this).find('.sort_icon').css('visibility', 'hidden');
        } else {
            $(this).find('.invoicetrhide').css('opacity', '1');
        }
    });
    $(document).on('mouseleave', '.tr_invoiceelement', function () {
        $(this).find('.invoicetrhide').css('opacity', '0');
        $(this).find('.sort_icon').css('visibility', 'visible');
    });
	
	// Sortable
	$( "#InvoiceElements" ).sortable({
		placeholder: "InvoiceElementContainer",
		axis: "y",
		forceHelperSize: true,
		forcePlaceholderSize: true,
		containment: 'parent',
		tolerance: 'pointer',
		handle: '.sortablehandle'
	});
    // Prevent smaller columns while sorting
    $(document).on('mousedown', '#InvoiceElements .sortablehandle', function(){
        var width = $(this).parents('tr').find('textarea').parent().width();
        $(this).parents('tr').find('textarea').parent().css('width', width + 'px');
    });
    $(document).on('mouseup', '#InvoiceElements .sortablehandle', function(){
        $(this).parents('tr').find('textarea').parent().css('width', 'auto');
    });
	
	// Discount
	$('#discount_link').click(function(){
		$(this).hide();
		$('#discount_dialog').show();
	});
	$('#discount_btn').click(function(){
		$('#discount_dialog').hide();
		$('#discount_link').show();
		
		var tmp_discount_value = deformatAsMoney($('input[name="Discount"]').val())*1;
		// Max 2 decimals
		tmp_discount_value = Math.round(tmp_discount_value * 100) / 100;
		
		if(tmp_discount_value > 0 && tmp_discount_value <= 100){
			$('#discount_txt').html(vat(tmp_discount_value));
			$('input[name="Discount"]').val(vat(tmp_discount_value));
			$('#discount_tr').show();
			$('#discount_link').html(__LANG_DISCOUNT_EDIT + '?');
			$(this).find('span').html(__LANG_DISCOUNT_EDIT);
		}else{
			$('input[name="Discount"]').val('');
			$('#discount_tr').hide();
			$('#discount_link').html(__LANG_DISCOUNT_ADD + '?');
			$(this).find('span').html(__LANG_DISCOUNT_ADD);
		}
		
		getLineTotal(0);
	});
	//Line discount
    $(document).on('change', 'input[name^="DiscountPercentage["]', function(){
			var tmp_discount_value = deformatAsMoney($(this).val())*1;
			// Max 2 decimals
			tmp_discount_value = Math.round(tmp_discount_value * 100) / 100;
			$(this).val(tmp_discount_value);
	});

    // Correct alignment of discount line
    $('textarea[name^="Description["]').each(function(index,element)
    {
        var LineName = $(element).attr('name').match(/\[(\d+)\]/);
        if(LineName != null)
        {
            LineID = LineName[1];
            position_discount_input(LineID);
        }
    });
    $(document).on('change', 'textarea[name^="Description["]', function(){
        // Also update discount lines
        var LineName = $(this).attr('name').match(/\[(\d+)\]/);
        if(LineName != null)
        {
            LineID = LineName[1];
            position_discount_input(LineID);
        }
    });
    $(document).on('keyup', 'textarea[name^="Description["]', function(){
        $(this).change();
    });

	$('#form_create_invoice').click(function(){

		// First check for creditinvoices with positive amounts, if so, warn
		if($('select[name="Status"] option:selected').val() == '8' && deformatAsMoney($('#total-excl').html()) > 0)
		{
			return $('#credit_invoice_modal').dialog('open');
		}

		// Prevent double click
		if($(this).data('already_clicked') == true)
		{
			return true;
		}
		$(this).data('already_clicked', true);
		document.form_create.submit();
	});

	$('#credit_invoice_toggle_btn').click(function(){

		if($('input[name="CreditInvoiceAction"]:checked').val() == 'toggle')
		{
			// Toggle amounts
			$('input[name^="PriceExcl["]:visible, input[name^="PriceIncl["]:visible').each(function(){
				if($(this).val())
				{
					$(this).val(-1 * deformatAsMoney($(this).val())).keyup();
				}
			});
		}

		// Prevent double click
		if($(this).data('already_clicked') == true)
		{
			return true;
		}
		$(this).data('already_clicked', true);
		document.form_create.submit();
	});
});

function add_discount_rule(lineID, icon){
	$('textarea[name="Description[' + lineID + ']"]').next('.discount').show();
	$('input[name="PriceExcl[' + lineID + ']"], input[name="PriceIncl[' + lineID + ']"]').next('.discount').show();

    position_discount_input(lineID);

	$(icon).hide();
	$(icon).parent().find('.discount_remove_icon').show();
	$(icon).parent().parent().find('.discount_helper').show();
}

function position_discount_input(lineID)
{
    var input_description = document.querySelector('textarea[name="Description[' + lineID + ']"]');
    var td_description = input_description.closest('td');
    var tr_description = input_description.closest('tr');

    var input_price_excl = tr_description.querySelector('input[name="PriceExcl[' + lineID + ']"]');
    var input_price_incl = tr_description.querySelector('input[name="PriceIncl[' + lineID + ']"]');
    // Only 1 is visible, take the correct one.
    var price_offset = Math.max(input_price_excl.offsetHeight,input_price_incl.offsetHeight);
    var td_price = input_price_excl.closest('td');

    var OffSetLabel = td_description.querySelector('.discount').offsetTop - input_description.offsetTop - price_offset;

    td_price.querySelector('.discount').style.marginTop = OffSetLabel + 'px';
    tr_description.querySelectorAll('.discount_helper').forEach(function (element) {
        element.style.marginTop = OffSetLabel + 'px';
    });
    tr_description.querySelector('.discount_remove_icon').style.marginTop = (OffSetLabel + 18) + 'px';
}

function remove_discount_rule(lineID, icon){
	$('textarea[name="Description[' + lineID + ']"]').next('.discount').hide();
	$('input[name="PriceExcl[' + lineID + ']"], input[name="PriceIncl[' + lineID + ']"]').next('.discount').hide();
	$('input[name="DiscountPercentage[' + lineID + ']"]').val('');
	$(icon).hide();
	$(icon).parent().parent().find('.discount_helper').hide();
	$(icon).parent().find('.discount_icon').show();
	getLineTotal(lineID);
}