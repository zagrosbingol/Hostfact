$(function(){
	/**
	 * Product pages
 	 */
	if($('#ProductForm').html() != null){
		
		$('#ProductCode_text').click(function(){
			$(this).hide();
			$('input[name=ProductCode]').show().focus();
			$('input[name=ProductCode]').blur(function(){
				$(this).hide();
				$('#ProductCode_text').html(htmlspecialchars($(this).val()) + " " + STATUS_CHANGE_ICON);
				$('#ProductCode_text').show();			
			});
		});
		
		// Edit only
		if($('input[name=id]').val() != ""){
			if($('input[name=ProductType]:checked').val() == "hosting"){
				getServerPackages($('select[name=Server]').val(), $('select[name=PackageType]').val(), true);
			}
			
		}
		
		$('input[name=ProductType]').click(function(){
			if($(this).val() == 'domain'){ $('#domain_div').slideDown(); }else{$('#domain_div').slideUp(); }
			if($(this).val() == 'hosting'){ $('#hosting_div').slideDown(); }else{$('#hosting_div').slideUp(); }
			
			if($(this).val() == 'domain' || $(this).val() == 'hosting'){ $('select[name=PricePeriod]').val('j').change(); }else{ $('select[name=PricePeriod]').val(''); }
			
			// Load div for other product types
			if($(this).val() != 'other' && $(this).val() != 'domain' && $(this).val() != 'hosting')
			{
				var ModuleActionName = ($('input[name=id]').val() != "") ? 'product_form_add' : 'product_form_edit';
				$.post('modules.php', { module: $(this).val(), page: 'ajax', action: 'product_form_add', product_id: $('input[name=id]').val()}, function(data){
					$('#producttype_module').html(data).show();
				}, 'html');
			}
			else
			{
				$('#producttype_module').html('').hide();
			}
		});
		
		// Get domain product info
		$('input[name=ProductTld]').keyup(function(){
			if($(this).val().length < 2){
				$('#domain_div_new').hide();
				$('#domain_div_existing').hide();
				return;
			}
			$.post('XMLRequest.php', { action: 'get_product_by_tld', tld: $(this).val() }, function(data){
				
				$('input[name="WhoisServer"]').val('');
				$('input[name="WhoisNoMatch"]').val('');
				
				if(data.Tld){
					$('#domain_div_existing_registrar').html(data.RegistrarName);
					if(data.OwnerChangeCostProductCode){
						$('#domain_div_existing_costownerchange').html(data.OwnerChangeCostProductCode + ' - ' + data.OwnerChangeCostProductName + ' ('+ data.OwnerChangeCostPriceExclF + ')');
					}else{
						$('#domain_div_existing_costownerchange').html(__LANG_NO_OWNERCHANGE_COSTS);
					}
					
					$('#domain_div_new').hide();
					$('#domain_div_existing').show();
				}else{
					$('#domain_div_new').show();
					$('#domain_div_existing').hide();
					
					$('#public_whois_status').html('');
		
					$.post("XMLRequest.php", { action: "get_public_whois_for_tld", tld: $('input[name="ProductTld"]').val()},
					function(data){
						if(data.errorSet != undefined) {
							$('#public_whois_status').html('<span class="loading_red">'+data.errorSet[0].Message+'</span>');
							
						}else if(data.errorSet == undefined){
							$('input[name="WhoisServer"]').val(data.WhoisServer);
							$('input[name="WhoisNoMatch"]').val(data.WhoisNoMatch);						
						}
			   		}, "json");	
			
					
				}
			},"json");
			
		});
		$('input[name=ProductTld]').keyup();
		
		if($('#div_for_tldsearch')){
		
			$('#div_for_tldsearch').load('XMLRequest.php?action=searchtld', function(){ });
			
			$('#tld_search_icon').click(function(){
				
					
					$('#tld_search').dialog({modal: true, autoOpen: true, resizable: false, width: 750, height: 'auto'});

                    $(document).on('click', '.dialog_select_hover', function() {
						SelectedTld = $(this).children().first().html();
						$('input[name=ProductTld]').val(SelectedTld).keyup();
						
						$('#tld_search').dialog('close');
	
						ajaxSave('search.tld','searchfor','');
					});
	
			});
		}
		
		// Get hosting product info
		$('select[name=Server]').change(function(){
			getServerPackages($(this).val(), $('select[name=PackageType]').val(), true);
		});
		$('select[name=PackageType]').change(function(){
			getServerPackages($('select[name=Server]').val(), $(this).val(), true);
		});

		$('select[name=TemplateName]').change(function()
		{
			if($(this).val() && $(this).val().substr(0,2) == 'ex')
			{
				$('.box3.hostingTemplates').addClass('hide');
			}
			else
			{
				$('.box3.hostingTemplates').removeClass('hide');
			}
		});

		$('select[name="EmailTemplate"]').change(function(){
			if($(this).val() > 0){
				$('#hosting_pdf_email_div').slideDown();
			}else{
				$('#hosting_pdf_email_div').slideUp();
			}
		});

		// Show only
		if($('#delete_product')){
			$('#delete_product').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('#delete_product.autoopen').dialog('open');	
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_product_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_product_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_product_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
	}
	
	// Auto calculate
	if($('select[name=TaxPercentage]').val() != ""){
		
		
		$('input[name="PriceExcl"], input[name="PriceIncl"]').keyup(function(){
			calculate_span_product_excl_incl();
		});
		$('input[name="PriceExcl"], input[name="PriceIncl"]').change(function(){
			calculate_span_product_excl_incl();
		});
		$('select[name="TaxPercentage"]').change(function(){
			calculate_span_product_excl_incl();
		});
	}	
 
	$('input[name=Cost]').change(function(){ 
		$(this).val(formatAsMoney(deformatAsMoney($(this).val()))); 
	});
	
	$('select[name="PricePeriod"]').change(function(){
		if($(this).val())
		{
			$('#CustomPricesDiv').show();
			$('select[name="CustomPrices[period][Periodic][helper]"]').val($(this).val()).change();
		}
		else
		{
			$('#CustomPricesDiv').hide();
		}						
	});
	
	$('input[name="HasCustomPrice"]').change(function(){
		if($('input[name="HasCustomPrice"]:checked').val() == 'period')
		{
			$("#CustomPricesPeriodDiv").show();
			
			if($('.removeBillingCycle').length <= 1)
			{
				$('#addBillingCycle').click();
			}
		}
		else
		{
			$('input[name="CustomPrices[period][Periods][]"]').each(function(){
				$(this).parent().remove();
			});
			$("#CustomPricesPeriodDiv").hide();
		}						
	});

    $(document).on('change', 'select[name^="CustomPrices[period][Periodic]"]',function(){
		$(this).parent().find('span span').html($(this).find('option:selected').text());	
	});
	
	$('#addBillingCycle').click(function(){
		$('#addBillingCycle').before('<div>' + $('#CustomPricesPeriodDiv_example').html().replace(/\[helper\]/g,"[]"));
		$('select[name^="CustomPrices[period][Periodic]"]').last().val($('select[name="PricePeriod"]').val());
		
	});
    $(document).on('click', '.removeBillingCycle',function(){
		$(this).parent().remove();
	});
	
	
});

function calculate_span_product_excl_incl(){

	var PriceInput = ($('input[name="PriceExcl"]').val() == undefined) ? $('input[name="PriceIncl"]') : $('input[name="PriceExcl"]');

	if(!$(PriceInput).val()){
		$('.span_product_excl_incl').hide().find('span').html('');
	}else{
		if($(PriceInput).attr('name') == 'PriceExcl'){
			// Excl
			$('.span_product_excl_incl').show().find('span').html(formatAsMoney(formatAsFloat(deformatAsMoney($(PriceInput).val()),2) * (1 + ($('select[name=TaxPercentage]').val() * 1))));
		}else{
			// Incl
			$('.span_product_excl_incl').show().find('span').html(formatAsMoney(formatAsFloat(deformatAsMoney($(PriceInput).val()),2) / (1 + ($('select[name=TaxPercentage]').val() * 1))));
		}
	}
}