$(function(){
	/**
	 * Server pages
 	 */
	// Initial call
	if($('select[name="Panel"]').val() != undefined){
		getControlPanelAPI($('select[name="Panel"]').val());
	}else if($('input[name="Panel"]').val() != undefined){
		getControlPanelAPI($('input[name="Panel"]').val());
	}
	 
	if($('#ServerForm').html() != null){

		// Toggle form parts
		$('select[name=Panel]').change(function(){
			getControlPanelAPI($(this).val());
		});

		// Split on portnumber
		$('input[name="Location"]').change(function(){
			var tmp_location = $(this).val().replace('https://','').replace('http://','');
			if(tmp_location.indexOf(':') >= 0){
				var tmp_port = tmp_location.substring(tmp_location.indexOf(':')+1);
				$('input[name="Port"]').val(tmp_port.replace(/[^0-9]/g, '')); // Only accept digits
				$(this).val($(this).val().replace(':'+tmp_port, ""));
			}
		});
				
		// Loading
		$('#form_create_btn').click(function(){
			$(this).removeClass('button1').addClass('button2').hide();
			$('#loader_saving').show();
		});

		// Show only
		if($('#delete_server')){
			$('#delete_server').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('#delete_server.autoopen').dialog('open');	
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_server_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_server_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_server_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
			
			
			// Check login data
			if($('#login_check').html() != null){
				$.post("XMLRequest.php", { action: "server_logincheck", id: $('input[type="hidden"][name="id"]').val()},
					function(data){
						if(data.resultSet == undefined && data.errorSet != undefined){
							$('#login_check').html(data.errorSet[0].Message);

                            if(data.errorSet[0].ServerError != undefined)
                            {
                                $('#login_check_error ul li').html(data.errorSet[0].ServerError);
                                $('#login_check_error').show();
                            }
						}else if(data.errorSet == undefined){
			                $('#login_check').html(data.resultSet[0].Message);
						}
				}, "json");
				
			}
		}
		$('#delete_pleskdialog_btn').click(function(){
			document.form_plesk_clientid.submit();
		});
		
		// Import accounts
		$('#import_server').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});
		$('#import_server.autoopen').dialog('open');
		$('#import_server_btn').click(function(){ document.form_import.submit();});
		
		$('select[name=Package]').change(function(){
			if($(this).val() != ""){
				// Get package information
				getPackageInformation($(this).val());
			}	
		});
		
		$('input[name="Product"]').change(function(){
			if($(this).val() != ""){
				// Get package information
				var Debtor = 0;
			    if($('input[name="Debtor"]').val() > 0){
			        Debtor = $('input[name="Debtor"]').val();
			    }
			    var ProductID = $(this).val();
				$.post("XMLRequest.php", { action: "get_product", product: $(this).val(), TaxDebtor: Debtor},
					function(data){
					  
			            if(data.ProductCode){

			            	if(data.HasCustomPrice == 'period')
							{
								CustomPriceObject[ProductID] = data.CustomPrices;
							}
							
							// Save product period price info
							ProductPeriodPriceObject[ProductID] = new Array();
							ProductPeriodPriceObject[ProductID]['PricePeriod'] 	= data.PricePeriod;
							ProductPeriodPriceObject[ProductID]['PriceIncl'] 	= data.PriceIncl;
							ProductPeriodPriceObject[ProductID]['PriceExcl'] 	= data.PriceExcl;
			            	
							$('textarea[name="Description"]').val(data.Description).autoGrow();
							$('input[name="Periods"]').val(1);
							$('select[name="Periodic"]').val(data.PricePeriod).change();
							$('input[name="PriceExcl"]').val(data.PriceExcl);
							
							// Show hide Custom Period div
							showCustomPeriodPrice('hostingimport');
						}
				}, "json");
			}	
		});

        $(document).on('focus', 'select[name="Periodic"]', function () {
            // Set prev if not new or undefined
            $(this).attr('prev', $(this).val());
        });
		
		$('input[name=StartPeriod], input[name=Periods], select[name="Periodic"]').change(function(){
			
			if($(this).attr('name') == 'Periods')
			{
				// Check if we have a custom price, if not calc price period
				checkCustomPeriodPrice('hostingimport', 'Periods');
			}else if($(this).attr('name') == 'Periodic')
			{
				$('.span_periodic').html($('select[name="Periodic"] option:selected').text());
				
				// Check if we have a custom price, if not calc price period
				checkCustomPeriodPrice('hostingimport', 'Periods');
			}
			
			result = changePeriodCalc($('select[name=Periodic]').val(), $('input[name=Periods]').val(), $('input[name=StartPeriod]').val());
			$('input[name=StartPeriod]').val(result[0]);
			$('input[name=EndPeriod]').val(result[1]);
			$('#EndPeriodText').html(__LANG_TILL + ' ' + result[1]);
		});
		
		$('input[name=Subscription]').click(function(){
			if($(this).prop('checked')){
				$('#account_periodic').show();
			}else{
				$('#account_periodic').hide();
			}
		});
		$('input[name=Subscription]:checked').each(function(){
			$('#account_periodic').show();
		});
	}
	
	/**
	 * Package pages
 	 */
	if($('#PackageForm').html() != null){
		// Get hosting product info
		$('select[name=Server]').change(function(){
			$('#properties_hosting_package_none').show();
			$('#properties_hosting_package_custom').hide();
			$('#properties_hosting_package').hide();
			
			getServerPackages($(this).val(), $('select[name=PackageType]').val());
		});
		$('select[name=PackageType]').change(function(){
			$('#properties_hosting_package_none').show();
			$('#properties_hosting_package_custom').hide();
			$('#properties_hosting_package').hide();
						
			getServerPackages($('select[name=Server]').val(), $(this).val());
		});
		// Onload
		getServerPackages($('select[name=Server]').val(), $('select[name=PackageType]').val());
		
		
		// Get package stats
		$('select[name="TemplateName"]').change(function(){

			$.post('XMLRequest.php', { action: 'package_information_from_server', server_id: $('select[name=Server]').val(), package_type: $('select[name=PackageType]').val(), template_name: $(this).val() }, function(data){
				if(data.resultSet != undefined && data.resultSet[0].BandWidth != undefined){
										
					$('#hosting_discspace').html((data.resultSet[0].uDiscSpace) ? __LANG_UNLIMITED : data.resultSet[0].DiscSpace);
					$('#hosting_traffic').html((data.resultSet[0].uBandWidth) ? __LANG_UNLIMITED : data.resultSet[0].BandWidth);
					$('#hosting_domains').html((data.resultSet[0].uDomains) ? __LANG_UNLIMITED : data.resultSet[0].Domains);
					$('#hosting_databases').html((data.resultSet[0].uMySQLDatabases) ? __LANG_UNLIMITED : data.resultSet[0].MySQLDatabases);
					$('#hosting_emailaccounts').html((data.resultSet[0].uEmailAccounts) ? __LANG_UNLIMITED : data.resultSet[0].EmailAccounts);
					
					$('#properties_hosting_package_none').hide();
					$('#properties_hosting_package_custom').hide();
					$('#properties_hosting_package').show();
					
				}else{
					$('#properties_hosting_package_none').show();
					$('#properties_hosting_package_custom').hide();
					$('#properties_hosting_package').hide();
					
					$('#hosting_discspace').html(0 + " MB");
					$('#hosting_traffic').html(0 + " MB");
					$('#hosting_domains').html(0);
					$('#hosting_databases').html(0);
					$('#hosting_emailaccounts').html(0);
				}
			},"json");
			
		});
		
		if($('#delete_package')){
			$('#delete_package').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('#delete_package.autoopen').dialog('open');	
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_package_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_package_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_package_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
		
		$('#package_create_btn').click(function(){			
			if($('input[name="Product"]').val() == "" || $('input[name="Product"]').val() == "0"){
				$('input[name="ProductUpdate"]').val('yes');
				document.form_create.submit();
			}else{
				
				var CurrentPackageID = ($('input[name="id"]').val() > 0 ) ? $('input[name="id"]').val() : 0;
				
				$.post('XMLRequest.php', { action: 'get_product_packageid', id: $('input[name="Product"]').val() }, function(data){
					if(data.ProductType != undefined){
						if(data.ProductType == "hosting" && data.PackageID > 0 && data.PackageID != CurrentPackageID){
							
							$('#package_product_connect_dialog span.productname').html($('input[name="AutoCompleteSearch[]"][data-inputfieldname="Product"]').val());
							$('#package_product_connect_dialog span.packagename').html($('input[name="PackageName"]').val());
							
							$('#package_product_connect_dialog').dialog({modal: true, autoOpen: true, resizable: false, width: 450, height: 'auto'}); 
							
							$('input[type="radio"][name="ProductUpdateDialog"]').click(function(){
								$('#package_product_connect_dialog_btn').removeClass('button2').addClass('button1');
							});
							
							$('#package_product_connect_dialog_btn').click(function(){
								$('input[name="ProductUpdate"]').val($('input[type="radio"][name="ProductUpdateDialog"]:checked').val());	
								document.form_create.submit();
							});
							
							return false;
						}else if(data.ProductType == "hosting" && data.PackageID != CurrentPackageID){
							$('input[name="ProductUpdate"]').val('yes');
						}else{
							$('input[name="ProductUpdate"]').val('yes');
						}
						document.form_create.submit();
					}
				},"json");
			}
				
			
		});
		
	}
});

function getPackageInformation(package_id){
	$.post("XMLRequest.php", { action: "package_information", id: package_id},
		function(data){
			if(data.resultSet == undefined && data.errorSet != undefined){
			
			}else if(data.errorSet == undefined){
                $('input[name="Product"]').val(data.resultSet[0].Product).change();
			}
	}, "json");
}

function importAccount(account_id,username,domain,package_name, account_type){
	
	// First reset the fields
	$('select[name="Package"]').val('');
	$('input[name="Debtor"]').val('');
	
	$('input[name="Product"]').val('');
	if(!package_name){
		$('input[name="Product"]').change();	
	}
	
	$('textarea[name="Description"]').val('');
	$('input[name="PriceExcl"]').val('');
	$('input[name="PriceIncl"]').val('');
	$('select[name="Periodic"]').val('j').change()
	$('input[name="Periods"]').val('1');
	$('input[name="StartPeriod"]').val('');
	$('#EndPeriodText').text('');
	$('.custom_period_price_div').remove();
	
	// Set the new data
	$('input[name=account_id]').val(account_id);
	$('#account_username').html(username);
	$('#account_domain').html(domain);
	$('select[name=Package]').val(package_name).change();
	$('#account_type').html(account_type);
	$('#import_server').dialog('open');
}

function getControlPanelAPI(controlpanel_id)
{
	if(controlpanel_id == "")
	{
		$('#panel_div').hide();
		$('#server_div_error').hide();
		return;
	}
    $.post("XMLRequest.php", { action: "controlpanel_api", id: controlpanel_id, edit_or_show: ($('#panel_div_show_additional_settings').html() != undefined ? 'show' : 'edit'), server_id: $('input[name="id"]').val()},
		function(data){
			$('#panel_div').show();
															
			if(data.errorSet != undefined) {
				$('#server_div_error').html('<br />' + data.errorSet[0].Message).show();				
				$('#panel_div').hide();
			}else if(data.errorSet == undefined){
				$('#panel_div').show();
				$('#server_div_error').hide();			
				
				// Toggle default domain type
				var showServerdomaintype = true;
				
				$('#serverdomaintype').css('display', 'none');
				$('#holderOneDomainType').css('display', 'none');
				
				$('#serverdomaintype input[name="DomainType"]').parent().css('display', 'none');
				
				$.each(data.DomainType, function(index, value) {
					if(value && $('#serverdomaintype input[name="DomainType"][value="'+ index +'"]').val()){
						$('#serverdomaintype input[name="DomainType"][value="'+ index +'"]').parent().css('display', 'block');
						onlyOne = index;
					}else{
						if($('input[name="page"]') == 'add'){
							$('#serverdomaintype input[name="DomainType"][value="'+ index +'"]').prop('checked', false).parent().css('display', 'none');
						}
					}
				});
				
				// Count visible options
				var count = 0 
				$('#serverdomaintype input[name="DomainType"]').each(function(valx){
					if($(this).parent().css('display') == 'block'){
						count++;
					}
				});
				
				// No results hide all
				if(count < 2){
					$('#serverdomaintype input[name="DomainType"]').prop('checked', false);
					$('#holderOneDomainType').css('display', 'block');
					
					if(count == 1){
						$('#serverdomaintype input[value="' + onlyOne + '"]').prop('checked', true);
					}
					showServerdomaintype = false

				// If there is no option checked
				} else if(!$('#serverdomaintype input[name="DomainType"]').is(':checked')){
					// If the option additional is visible check this option
					if($('#serverdomaintype input[name="DomainType"][value="additional"]').parent().css('display') == 'block'){
						$('#serverdomaintype input[name="DomainType"][value="additional"]').prop('checked', true);
					}else{
						// Check the first visible option
						$('#serverdomaintype input[name="DomainType"]').each(function(index){
							if(!$('#serverdomaintype input[name="DomainType"]').is(':checked') && $(this).parent().css('display') == 'block'){
								$(this).prop('checked', true);
								return;
							}
						})
					}
				// Is the selected option visible?
				}else if($('#serverdomaintype input[name="DomainType"]:checked').parent().css('display') == 'none'){
					// If the option is not visible uncheck the option
					$('#serverdomaintype input[name="DomainType"]:checked').prop('checked', false);
					
					// Check the first visible option
					if($('#serverdomaintype input[name="DomainType"][value="additional"]').parent().css('display') == 'block'){
						$('#serverdomaintype input[name="DomainType"][value="additional"]').prop('checked', true);
					}else{
						$('#serverdomaintype input[name="DomainType"]:first').prop('checked', true);
					}
				}
				
				// Show the serverdomaintype		
				if(showServerdomaintype){
					$('#serverdomaintype').css('display', 'block');
				}
				
				// Toggle password
				switch(data.password_type){
					case 'textarea':
						$('#panel_div_password').hide();
						$('#panel_div_remotekey').show();
						break;
					default:
						$('#panel_div_password').show();
						if(data.password_label){
                            $('#panel_div_password strong').html(data.password_label);
                        }
						$('#panel_div_remotekey').hide();
						break;
				}

				if(data.hasAdditionalSettings)
				{
                    $('#panel_div_show_additional_settings').html(data.hasAdditionalSettings); // For show page
					$('#panel_div_has_additional_settings').html(data.hasAdditionalSettings); // For add/edit page
				}
				else
				{
					$('#panel_div_has_additional_settings').html('');
				}
				
				// Toggle additional IP
				if(data.additional_server_ip)
				{
					$('#panel_div_additional_ip').show();
				}
				else
				{
					$('#panel_div_additional_ip').hide();
				}
								
				var html = '';
				if(data.dev_logo){ html += '<img src="' + data.dev_logo + '" alt="" class="controlpanel_logo"/>'; }
				if(data.dev_author){ html += data.dev_author + '<br />'; }
				if(data.dev_website){ html += '<a href="' + data.dev_website + '" class="a1 c1">' + data.dev_website + '</a><br />'; }
				if(data.dev_email){ html += '<a href="mailto:' + data.dev_email + '" class="a1 c1">' + data.dev_email + '</a><br />'; }
				if(data.dev_phone){ html += data.dev_phone + '<br />'; }
				
				if(html == ''){ 
					$('#panel_div .setting_help_box').hide(); 
				}else{					
					$('#dev_info').html('<br />' + html);
					$('#panel_div .setting_help_box').show();
				}
			}
	   }, "json");
}