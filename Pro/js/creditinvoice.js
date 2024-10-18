var dontupdateonfirstload = true;

$(function(){
	/**
	 * Creditinvoice pages
	 */
     
	if($('#partialPayment').html() != ''){
		$('#partialPayment').click(function(){
			$('#partialPaymentDialog').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('#partialPaymentDialog').dialog('open');	
		});
		
		$('#partialPaymentDialog_btn').click(function(){
			document.form_partpayment.submit();
		});
	}

    $('#dialog_paid_confirm').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	
	if($('#CreditorInvoiceForm').html() != null){	
		
		$('#InvoiceElements').find('tr:last td:last img').hide();
		
		getCreditInvoiceTaxPercentage();

        $(document).on('change', 'input[name^="TaxPercentage"]', function () {
            if (parseInt(deformatAsMoney($(this).val())) < 0 || parseInt(deformatAsMoney($(this).val())) > 100) {
                $(this).val('');
                getCreditInvoiceTaxPercentage();
            }
        });
        $(document).on('keyup', 'input[name^="TaxPercentage"]', function () {
            getCreditInvoiceTaxPercentage();
        });

        $(document).on('keyup', 'input[name^="PriceExcl"], input[name^="Number"]', function () {
            calculateCreditInvoiceTotal();
        });
		
		$('#CreditInvoiceCode_text').click(function(){
			$(this).hide();
			$('input[name=CreditInvoiceCode]').show().focus();
			$('input[name=CreditInvoiceCode]').blur(function(){
				$(this).hide();
				$('#CreditInvoiceCode_text').html(htmlspecialchars($(this).val()) + " " + STATUS_CHANGE_ICON);
				$('#CreditInvoiceCode_text').show();			
			});
		});

        $('#creditor_new').click(function(){
            $('input[name="Creditor"]').val('new').change();
            $('input[name="AutoCompleteSearch[]"][data-inputfieldname="Creditor"]').val('');
        });
		
		$('input[name=Creditor]').change(function()
        {
            // remove the UBL fields if we change creditor
            if($('input[name="MyCustomerCode"]').length > 0)
            {
                $('input[name="MyCustomerCode"]').remove();
            }
            if($('input[name="AccountNumber"]').length > 0)
            {
                $('input[name="AccountNumber"]').remove();
            }

			if($(this).val() == ""){
				$('#enveloppe').hide();
				$('input[name="Term"]').val(0);
			}
            else if($(this).val() == "new")
            {
                $('#enveloppe_text').hide();
                $('#enveloppe_input').show();
                $('#enveloppe').show();
                $('.enveloppe_title_new').show();
                $('.enveloppe_title').hide();
                $('#creditor_new').hide();

                $('#inputJQ-CompanyName').val('');
                $('#inputJQ-Initials').val('');
                $('#inputJQ-SurName').val('');
                $('#inputJQ-Sex').val('');
                $('#inputJQ-Address').val('');

                $('#inputJQ-ZipCode').val('');
                $('#inputJQ-City').val('');

                $('input[name="Term"]').val('0');

                $('#inputJQ-Country').val(htmlspecialchars($('input[name="companyCountry"]').val())); // Set the default company country
            }
            else
            {
                $('#enveloppe_input').hide();
                $('#enveloppe_text').show();
                $('.enveloppe_title_new').hide();
                $('.enveloppe_title').show();
                $('#creditor_new').show();

				// Ophalen crediteurrgegevens en plaatsen veld...
				$.post("XMLRequest.php", { action: 'creditinvoice_get_creditor', creditor: $(this).val() }, function(data){
					$('#formJQ-Name').html((data.CompanyName) ? data.CompanyName : data.Initials + ' ' + data.SurName);
					$('#formJQ-Address').html(data.Address);
					$('#formJQ-ZipCode').html(data.ZipCode);
					$('#formJQ-City').html(data.City);
					$('#formJQ-Country').html(data.CountryLong);
					
					$('input[name="Term"]').val(data.Term);

                    if(data.Authorisation == 'yes')
                    {
                        $('input[name="Authorisation"]').prop('checked',true);
                        if($('select[name="Status"] option:selected').val() == '0' || $('select[name="Status"] option:selected').val() == '1' || $('select[name="Status"] option:selected').val() == '3')
                        {
                            $('#div_payment_term').hide();
                        }
                    }
                    else
                    {
                        $('input[name="Authorisation"]').prop('checked',false);
                        $('#div_payment_term').show();
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
					}
					
					$('#enveloppe').show();
				}, "json");
			}				
		});
		
		$('select[name=Status]').change(function()
        {
            $('#div_payment_term').show();

            if($('input[name="Authorisation"]').is(':checked'))
            {
                if($(this).val() == "0" || $(this).val() == "1" || $(this).val() == "3"){
                    $('#div_payment_term').hide();
                }
            }

			if($(this).val() == "2"){
				$('#div_partpayment').show();
			}else{
				$('#div_partpayment').hide();
			}
			
			if($(this).val() == "3"){
				$('#div_payment_date').show();
			}else{
				$('#div_payment_date').hide();
			}

            if($(this).val() == "0" || $(this).val() == "1" || $(this).val() == "3"){
                $('#div_authorisation').show();
            }else{
                $('#div_authorisation').hide();
            }
		});

        $(document).on('change', 'input[name="Authorisation"]', function()
        {
            if($(this).is(':checked'))
            {
                $('#div_payment_term').hide();
            }
            else
            {
                $('#div_payment_term').show();
            }
        });
		
		$('input[name="invoiceHasPrivatePart"]').click(function(){
			if($(this).prop('checked')){
				$('#invoice_private_part_div').show();
			}else{
				$('#invoice_private_part_div').hide();
			}
		});
		
		$('#add_new_element').click(function(){
			
			// Always make hidden element visible
			if($('#NewElement:visible').html() == null && $('#NewElement').find('input[name^="Number"]').val() == 1){
				
				$('#NewElement').show();
				return;
			}
			
			var NewElement = $('#NewElement').clone();
			
			// Remove default ID
			LineID = $(NewElement).find('input[name^=Number]').attr('name').replace('Number[','').replace(']','');
			$(NewElement).attr('id','');
			
			// Rename fields
			var Counter = (1 * $('input[name=NumberOfElements]').val()) + 1;
			$(NewElement).find('input[name^=Number]').attr('name','Number[' + Counter + ']');
			$(NewElement).find('input[name^=Description]').attr('name','Description[' + Counter + ']');
			$(NewElement).find('input[name^=PriceExcl]').attr('name','PriceExcl[' + Counter + ']');
			$(NewElement).find('input[name^=TaxPercentage]').attr('name','TaxPercentage[' + Counter + ']');
			
			
			// Default values
			$(NewElement).find('input[name^=Number]').val('1');
			$(NewElement).find('input[name^=Description]').val('');
			$(NewElement).find('input[name^=PriceExcl]').val('');
			$(NewElement).find('input[name^=TaxPercentage]').val(STANDARD_TAX*100);
			
			// Change id-actions
			HTML = $(NewElement).html();
			HTML = str_replace("removeCreditInvoiceElement('" + LineID+"'","removeCreditInvoiceElement('"+Counter+"'",HTML);
			$(NewElement).html(HTML);
			
			$(NewElement).show();
			$(NewElement).appendTo('#InvoiceElements');	
			
			// Update counter
			$('input[name=NumberOfElements]').val(Counter);

            if ($('#InvoiceElements tr:visible').length <= 1) {
                $('#InvoiceElements').find('tr img').hide();
            } else {
                $('#InvoiceElements').find('tr img').show();
            }
		});

        // When the user hits tab on the last select element of the invoice lines, trigger the action for a new invoice line
        $('#InvoiceElements').on('keydown', '.tr_invoiceelement input:visible:last', function(e)
        {
            if ((e.which == 9) || (e.keyCode == 9))
            {
                $('#add_new_element').trigger('click');
            }
        });
		
		if($('#delete_creditinvoice')){
			$('#delete_creditinvoice').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('#delete_creditinvoice.autoopen').dialog('open');	
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_creditinvoice_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_creditinvoice_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_creditinvoice_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
		
	}
	
});

function calculateCreditInvoiceTotal(){
	
	TotalPerTaxRate = new Object();
	
	TotalExcl = 0;
	TotalIncl = 0;
	
	$('.total-tax').hide();
	
	$('#InvoiceElements > tr').each(function(){
		
		if($(this).is(':visible')){
			// Number
			Number = deformatAsMoney($(this).find('input[name^="Number"]').val());
			
			// PriceExcl
			PriceExcl = deformatAsMoney($(this).find('input[name^="PriceExcl"]').val());
			
			// Vat
			TaxPercentage = deformatAsMoney($(this).find('input[name^="TaxPercentage"]').val())/100;
			var offset = ((Number * PriceExcl) >= 0) ? 0.000000001 : -0.000000001;
			
			TotalExcl += Math.round(((Number * PriceExcl)+offset)*100)/100;
			TotalIncl += Math.round(((Math.round(((Number * PriceExcl)+offset)*100)/100) * (1+1*TaxPercentage))*10000)/10000;
			
			// Show corresponding Tax label
			if(TaxPercentage > 0)
			{
				TaxPercentage = TaxPercentage.toString();
				
				$('#total-tax-' + (TaxPercentage.replace('.','_'))).parent().show();
				if((TotalPerTaxRate[TaxPercentage.replace('.','_')]) == undefined){
					TotalPerTaxRate[TaxPercentage.replace('.','_')] = 0;
				}
				
				TotalPerTaxRate[TaxPercentage.replace('.','_')] += Math.round(((Math.round((Number * PriceExcl)*100)/100) * 1*TaxPercentage)*10000)/10000;
				
				$('#total-tax-' + (TaxPercentage.replace('.','_'))).html(formatAsMoney(Math.round(TotalPerTaxRate[TaxPercentage.replace('.','_')] *100)/100));
			}
		}
		
	});
	
	$('#total-excl').text(formatAsMoney(TotalExcl));
	
	if(dontupdateonfirstload == false){
		$('#total-incl').val(formatAsMoney(TotalIncl));
	}
	dontupdateonfirstload = false;
}

function getCreditInvoiceTaxPercentage(){
	
	TaxRate = [];
	
	$('#creditInvoiceTax').html('');
	
	$('#InvoiceElements > tr').each(function() {
		if($(this).parent().is(':visible') && $(this).find('input[name^="Number"]').val() != 0){
			TaxRate.push({key: parseInt(deformatAsMoney($(this).find('input[name^="TaxPercentage"]').val())*1000), value: deformatAsMoney($(this).find('input[name^="TaxPercentage"]').val())/100 });	
		}
	});
	
	var sorted = TaxRate.slice(0).sort(function(a, b) {
		return a.value - b.value;
	});
	
	var keys = [];
	for (var i = 0, len = sorted.length; i < len; ++i) {
	    keys[i] = sorted[i].key;
	}
	
	$.each(sorted, function(key, object) {
		
		if(object.value > 0)
		{
			TaxPercentage = object.value.toString();
			$('#creditInvoiceTax').append('<tr class="total-tax hide"><td>&nbsp;</td><td class="align_right">' + vat(object.key/1000) + '% '+__LANG_VAT+' :</td><td>&nbsp;</td><td>'+ ((CURRENCY_SIGN_LEFT) ? CURRENCY_SIGN_LEFT + ' ' : '') +'</td><td class="align_right" id="total-tax-' + (TaxPercentage.replace('.','_')) + '"></td><td>'+ ((CURRENCY_SIGN_RIGHT) ? CURRENCY_SIGN_RIGHT + ' ' : '') +'</td></tr>');
		}
	});
	
	calculateCreditInvoiceTotal();
}


function removeCreditInvoiceElement(LineID){
	
	if($('#InvoiceElements tr:visible').length > 1){
		$('input[name="Number['+LineID+']"]').parent().parent().hide();
		$('input[name="Number['+LineID+']"]').val('0');

        if ($('#InvoiceElements tr:visible').length <= 1) {
            $('#InvoiceElements').find('tr img').hide();
        }

	}else{
		
		$('input[name="Number['+LineID+']"]').parent().parent().hide();
		$('input[name="Number['+LineID+']"]').val('0');

        $('#add_new_element').click();
	}
	
	getCreditInvoiceTaxPercentage();
}