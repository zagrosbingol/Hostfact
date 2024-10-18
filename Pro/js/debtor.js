$(function(){
	/**
	 * Debtor pages
 	 */
	if($('#DebtorForm').html() != null){
		
		function toggleFormPart(part){
			switch(part){
				case 'CompanyName':
					if($('input[name=CompanyName]').val() != "")
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
						// Prevent sending welcome mail
						$('select[name="WelcomeMail"]').val('');
					}
					break;
			}
		}
		
		$('select[name="payment_mail_when_helper"]').change(function(){
			if($('select[name="payment_mail_when_helper"]').val() == ''){
				$('#paymentmail_template').hide();
			}else{
				$('#paymentmail_template').show();
			}
			if($('select[name="payment_mail_when_helper"]').val() == 'custom'){
				$('#payment_mail_when').show();
			}else{
				$('#payment_mail_when').hide();
			}
		});
		
		$('input[name=CompanyName]').keyup(function(){ toggleFormPart('CompanyName'); }).change(function(){ toggleFormPart('CompanyName'); });
		$('input[name=AbnormalInvoiceData]').change(function(){ toggleFormPart('AbnormalInvoiceData'); });
		$('input[name=CustomerPanelAccess]').change(function(){ toggleFormPart('CustomerPanelAccess'); });

		$('select[name=Country]').change(function(){ if($('input[name=AbnormalInvoiceData]:checked').val() == null){ $('select[name=InvoiceCountry]').val($('select[name=Country]').val()).change(); } });
		
		$('#DebtorCode_text').click(function(){
			$(this).hide();
			var OldDebtorCode = $('input[name=DebtorCode]').val();
			$('input[name=DebtorCode]').show().focus();
			$('input[name=DebtorCode]').blur(function(){
				$(this).hide();
				$('#DebtorCode_text').html(htmlspecialchars($(this).val()) + " " + STATUS_CHANGE_ICON);
				$('#DebtorCode_text').show();	
				
				// Auto adjust username if username was same as old debtorcode
				if(OldDebtorCode == $('input[name="Username"]').val())
				{
					$('input[name="Username"]').val($('input[name=DebtorCode]').val());
				}	
				
				if($('input[name="InvoiceAuthorisation"]').prop('checked'))
				{
					// Get/update MandateID
					updateDebtorMandateID(OldDebtorCode);
				}	
			});
		});
		
		// Add only
		if($('input[name="id"]').val() == undefined && $('input[name="Username"]') && $('input[name="Username"]').val() == '' && $('input[name="Password"]') && $('input[name="Password"]').val() == ''){
			createLogin();
		}
		
		// International version only
		if(IS_INTERNATIONAL == 'true')
		{
			$('select[name="Country"], select[name="InvoiceCountry"]').change(function(){
				
				var NamePrefix = $(this).attr('name').replace('Country', '');
				getCountryStates($(this).val(), NamePrefix, '');	
			});
		}
		
		// Direct Debit
		$('input[name="InvoiceAuthorisation"]').click(function(){
			if($(this).prop('checked'))
			{
				$('#debtor_div_directdebit').show();
				
				// Get/update MandateID
				updateDebtorMandateID($('input[name=DebtorCode]').val());
			}
			else
			{
				$('#debtor_div_directdebit').hide();
			}
		});
		
		// Edit only
		if($('input[name=id]').val() != ""){
			
			$('input[name=EmailAddress], input[name=InvoiceEmailAddress]').change(function(){
				var ShowSyncDialog = false;
				if($('input[name=AbnormalInvoiceData]:checked').val() != null && $('input[name=InvoiceEmailAddress]').val())
				{
					if($(this).attr('name') == 'InvoiceEmailAddress')
					{
						ShowSyncDialog = true;	
					}
				}
				else
				{
					ShowSyncDialog = true;
				}
				
				if(ShowSyncDialog)
				{
					$('#sync_emailaddress').dialog({modal: true, resizable: false, width: 450, height: 'auto'});
					
					$('input[name=sync_email]').click(function(){
						$('input[name=SynchronizeEmail]').val($('input[name=sync_email]:checked').val());	
					});
				}
			});
			
			$('input[name=InvoiceAuthorisation]').click(function(){
				$('#sync_authorisation').dialog({modal: true, resizable: false, width: 450, height: 'auto'});
				
				$('input[name=sync_auth]').click(function(){
					$('input[name=SynchronizeAuth]').val($('input[name=sync_auth]:checked').val());	
				});
				
			});
			
			// Custom nameservers
			$('input[name="UseCustomNameservers"]').change(function(){
				if($(this).prop('checked')){
					$('#debtor_use_custom_nameservers').show();
				}else{
					$('#debtor_use_custom_nameservers').hide();
				}
			});

            // Custom reminder emailaddress
            $('input[name="UseCustomReminderEmailAddress"]').change(function(){
                if($(this).prop('checked')){
                    $('#debtor_use_custom_reminder_emailaddress').show();
                }else{
                    $('#debtor_use_custom_reminder_emailaddress').hide();
                }
            });

			// Custom term of payment
			$('input[name="CustomInvoiceTermCheckbox"]').change(function(){
				if($(this).prop('checked')){
					$('#CustomInvoiceTerm').show();
				}else{
					$('#CustomInvoiceTerm').hide();
				}
			});
			
			// Custom periodic invoice days
			$('input[name="CustomPeriodicInvoiceCheckbox"]').change(function(){
				if($(this).prop('checked')){
					$('#CustomPeriodicInvoice').show();
				}else{
					$('#CustomPeriodicInvoice').hide();
				}
			});
			
			// Factuurgegevens gewijzigd?
			var InvoiceAddressChanged = false;
			if($('input[name=AbnormalInvoiceData]:checked').val() != null){
				$('input[name=InvoiceCompanyName], select[name=InvoiceSex], input[name=InvoiceInitials], input[name=InvoiceSurName], input[name=InvoiceAddress], input[name=InvoiceAddress2], input[name=InvoiceZipCode], input[name=InvoiceCity], input[name=InvoiceState], select[name=InvoiceStateCode], select[name=InvoiceCountry]').change(function(){ InvoiceAddressChanged = true; });
			}else if($('input[name=AbnormalInvoiceData]:checked').val() == null){
				$('input[name=CompanyName], select[name=Sex], input[name=Initials], input[name=SurName], input[name=Address], input[name=Address2], input[name=ZipCode], input[name=City], input[name=State], select[name=StateCode], select[name=Country]').change(function(){ InvoiceAddressChanged = true; });
			}

			$('input[name=InvoiceDataForPriceQuote], input[name=AbnormalInvoiceData], input[name="TaxNumber"]').change(function(){ InvoiceAddressChanged = true; });
			
			// Contactgegevens gewijzigd?
			var ContactDataChanged = false;
			$('input[name=Initials], input[name=SurName], input[name=Address], input[name=Address2], input[name=ZipCode], input[name=City], input[name=State], select[name=StateCode], select[name=Country], input[name=EmailAddress],  input[name=PhoneNumber], input[name=FaxNumber], input[name=TaxNumber], input[name=CompanyName], input[name=CompanyNumber], select[name=LegalForm]').change(function(){ ContactDataChanged = true; });
			
			$('#form_debtor_edit_btn').click(function(){	
				
				if(InvoiceAddressChanged == true && $('#sync_address').html() != null){
					$('#sync_address').dialog({modal: true, resizable: false, width: 450, height: 'auto'});
					
					$('input[name=sync_address]').click(function(){
						$('input[name=SynchronizeAddress]').val($('input[name=sync_address]:checked').val());	
					});
				}else if(ContactDataChanged == true && $('#sync_handles').html() != null){
					$('#sync_handles').dialog({modal: true, resizable: false, width: 450, height: 'auto'});
					
					$('input[name=sync_handles]').click(function(){
						$('input[name=SynchronizeHandles]').val($('input[name=sync_handles]:checked').val());	
					});
				}else{
					document.form_create.submit();
				}
			});
			
			$('#sync_address_btn').click(function(){
				if(ContactDataChanged == true && $('#sync_handles').html() != null){  
					$('#sync_address').dialog('close');
					$('#sync_handles').dialog({modal: true, resizable: false, width: 450, height: 'auto'});
					
					$('input[name=sync_handles]').click(function(){
						$('input[name=SynchronizeHandles]').val($('input[name=sync_handles]:checked').val());	
					});
				}else{
					$('form[name=form_create]').submit(); 
				}
			});
			
			
		}
		
		// Show only
		if($('#delete_debtor')){
			$('#delete_debtor').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('#delete_debtor.autoopen').dialog('open');		
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_debtor_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_debtor_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_debtor_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
        if($('#anonimize_debtor')){
            $('#anonimize_debtor').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
            $('input[name=imsure]').click(function(){
                if($('input[name=imsure]:checked').val() != null)
                {
                    $('#anonimize_debtor_btn').removeClass('button2').addClass('button1');
                }
                else
                {
                    $('#anonimize_debtor_btn').removeClass('button1').addClass('button2');
                }
            });
            $('#anonimize_debtor_btn').click(function(){
                if($('input[name=imsure]:checked').val() != null)
                {
                    document.form_anonimize.submit();
                }
            });
        }
	}
	
	if($('#DebtorGeneratePDFDialog')){
		
		$('#DebtorGeneratePDFDialog').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});	
		$("#debtor_pdf_generation").click(function(){ $('#DebtorGeneratePDFDialog').dialog('open'); });
		
		
		$('#DebtorGeneratePDFDialog select[name="Template"]').change(function(){
			
			// Load domain or hosting question
			$.post("XMLRequest.php", { action: "get_pdf_variable_objects", template: $(this).val(), debtor: $('#DebtorGeneratePDFDialog input[name="id"]').val()},
				function(data){
			    if(data.domain){
					$('#DebtorGeneratePDFDialog select[name="Domain"]').html(data.domain);
					$('#debtor_generate_pdf_dialog_domain').show();
				}else{
					$('#DebtorGeneratePDFDialog select[name="Domain"]').html('');
					$('#debtor_generate_pdf_dialog_domain').hide();
				}
				if(data.hosting){
					$('#DebtorGeneratePDFDialog select[name="Hosting"]').html(data.hosting);
					$('#debtor_generate_pdf_dialog_hosting').show();
				}else{
					$('#DebtorGeneratePDFDialog select[name="Hosting"]').html('');
					$('#debtor_generate_pdf_dialog_hosting').hide();
				}
			}, "json");
			
			
			
			$('#generate_pdf_btn').removeClass('button2').addClass('button1');
			//$('#generate_pdf_btn').removeClass('button1').addClass('button2');
		});
		$('#generate_pdf_btn').click(function(){
			if($('#DebtorGeneratePDFDialog select[name="Template"]').val())
			{
				document.DebtorGeneratePDFForm.submit();
			}	
		});
	}

	$('#buttonbar_show_debtor #more_info').click(function(){
		$(this).hide();
		$('#buttonbar_show_debtor #less_info').show();
		//$('.buttonbar').show();
		$('.debtorinfo_inline').slideDown('slow');
	});
	
	$('#buttonbar_show_debtor #less_info').click(function(){
		$(this).hide();
		$('#buttonbar_show_debtor #more_info').show();
		//$('.buttonbar').hide();
		$('.debtorinfo_inline').slideUp('slow');
	});
	
	// Interactions
	$('#dialog_interactions').dialog({modal: true, resizable: false, width: 750, height: 530});
	$('#dialog_interactions.autoopen').dialog('open');

    $(document).on('click', '.view_more_interactions', function()
	{
		$('#all_interactions').show();
		$('#new_interaction').hide();
		$('#dialog_interactions').dialog('open');
	});
    
    // show full comment
    $(document).on('click', '#view_full_comment', function()
	{
        $('#buttonbar_show_debtor #more_info').hide();
		$('#buttonbar_show_debtor #less_info').show();
        $( "#tabs" ).tabs( "option", 'active', $('#tabs').find('a[href="#tab-note"]').parent().index() );
		$('.debtorinfo_inline').slideDown('slow');
	});
    
    // show attachments tab
    $(document).on('click', '#view_attachments', function()
	{
        $('#buttonbar_show_debtor #more_info').hide();
		$('#buttonbar_show_debtor #less_info').show();

        $( "#tabs" ).tabs( "option", 'active', $('#tabs').find('a[href="#tab-attachments"]').parent().index());
		$('.debtorinfo_inline').slideDown('slow');
	});

    $(document).on('click', '#new_interaction_back', function()
	{
        $('#all_interactions').show();
        $('#new_interaction').hide();

		// Clicked by large button on debtor.show
		if(NewInteractionBtn == 'new_interaction_btn')
		{
            NewInteractionBtn = false;
            $('#dialog_interactions').dialog('close');
		}
	});

	var NewInteractionBtn = false;
    $(document).on('click', '#new_interaction_btn, #new_interaction_btn_dialog', function()
	{
        NewInteractionBtn = $(this).attr('id');

		// reset form
		$('#new_interaction').find('textarea').val('');
		$('#new_interaction').find('select').find('option:first').prop('selected', true);
		// set active user as default
		$('#new_interaction').find('select[name="Author"]').val($('#new_interaction').find('select[name="Author"]').data('default-author'));
		$('#new_interaction').find('input[name="InteractionID"]').val(0);
		var today = new Date();
		var yyyy = today.getFullYear().toString();
		var mm = (today.getMonth()+1).toString(); // getMonth() is zero-based
		var dd  = today.getDate().toString();
		var h = today.getHours();
		var m = today.getMinutes();
		$('form[name="InteractionForm"] input[name="Date"]').val(rewrite_date_db2site(yyyy, mm, dd));
		$('form[name="InteractionForm"] input[name="Time"]').val((h<10?"0"+h:h) + ':' + (m<10?"0"+m:m));

		$('#interaction_error').hide().children('ul').html('');

		$('#all_interactions').hide();
		$('#new_interaction').show();
		$('#dialog_interactions').dialog('open');
		$('p#interaction_submit_btn a.add').show();
		$('p#interaction_submit_btn a.edit').hide();
	});

    $(document).on('click', '#all_interactions .edit_interaction_btn', function()
	{
        NewInteractionBtn = false;
		$('#interaction_error').hide().children('ul').html('');
		var interaction_id = $(this).data('id');
		// get interaction data and fill form fields
		$.post('XMLRequest.php', { action: 'get_interaction', id: interaction_id },
			function(data)
			{
				if(data.Category != undefined && data.Category != null)
				{
					$('#new_interaction select[name="Category"]').val(data.Category);
				}
				if(data.Type != undefined && data.Type != null)
				{
					$('#new_interaction select[name="Type"]').val(data.Type);
				}
				if(data.Date != undefined && data.Date != null)
				{
					$('#new_interaction input[name="Date"]').val(data.Date);
				}
				if(data.Time != undefined && data.Time != null)
				{
					$('#new_interaction input[name="Time"]').val(data.Time);
				}
				if(data.Author != undefined && data.Author != null)
				{
					$('#new_interaction select[name="Author"]').val(data.Author);
				}
				if(data.Message != undefined && data.Message != null)
				{
					$('#new_interaction textarea[name="Comment"]').val(data.Message);
				}

				$('#new_interaction input[name="InteractionID"]').val(interaction_id);

				$('#all_interactions').hide();
				$('#new_interaction').show();
				$('p#interaction_submit_btn a.add').hide();
				$('p#interaction_submit_btn a.edit').show();

			}, 'json'
		);
	});

    $(document).on('click', '#add_interaction_btn, #save_interaction_btn', function()
	{
		var debtor_id = $('form[name="InteractionForm"] input[name="Debtor"]').val();
		var form_fields = $('form[name="InteractionForm"]').serialize();
		$.post('XMLRequest.php', { action: 'save_interaction', form_fields: form_fields },
			function(data)
			{
				$('#interaction_error').hide().children('ul').html('');

				if(data.result == true)
				{
					// giving along a table_id: '' means all the debtor tables won't be loaded, which makes the request faster
					$.post('debtors.php?page=show&id=' + debtor_id, { table_id: '' }, function(data)
					{
						$('#debtorinfo_sidebar').html($(data).find('#debtorinfo_sidebar').html());
						$('#sidebar .interactioninfo_sidebar').html($(data).find('#sidebar .interactioninfo_sidebar').html());
						$('#dialog_interactions').html($(data).find('#dialog_interactions').html());
						// update dialog title, we cannot replace the #dialog_interactions div because of added CSS
						$('#ui-dialog-title-dialog_interactions').text($(data).find('#dialog_interactions').attr('title'));
					});
				}
				else if(data.errors != undefined)
				{
					for(var i=0; i < data.errors.length; i++)
					{
						$('#interaction_error ul').append('<li>' + data.errors[i] + '</li>');
						$('#interaction_error').show();
					}
				}
			}, 'json'
		);
	});

    $(document).on('click', '.delete_interaction_btn', function()
	{
		if(confirm($(this).data('confirm')))
		{
			var debtor_id = $('form[name="InteractionForm"] input[name="Debtor"]').val();

			$.post('XMLRequest.php', { action: 'delete_interaction', interaction_id: $(this).data('id') },
				function(data)
				{
					if(data.result == true)
					{
						// giving along a table_id: '' means all the debtor tables won't be loaded, which makes the request faster
						$.post('debtors.php?page=show&id=' + debtor_id, { table_id: '' }, function(data)
						{
							$('#debtorinfo_sidebar').html($(data).find('#debtorinfo_sidebar').html());
							$('#sidebar .interactioninfo_sidebar').html($(data).find('#sidebar .interactioninfo_sidebar').html());
							$('#dialog_interactions').html($(data).find('#dialog_interactions').html());
							// update dialog title, we cannot replace the #dialog_interactions div because of added CSS
							$('#ui-dialog-title-dialog_interactions').text($(data).find('#dialog_interactions').attr('title'));

							if($('#dialog_interactions .interaction_container').length == 0)
							{
								// Close dialog when no items left
                                $('#dialog_interactions').dialog('close');
							}
						});
					}
					else
					{

					}
				}, 'json'
			);
		}
	});
	
	// Toggle interactions in dialog
    $(document).on('click', '.interaction_container .arrowdown', function()
	{
		$(this).parents('.interaction_container').find('.extended_message').show();
		$(this).parents('.interaction_container').find('.message').hide();
		
		$(this).parents('.interaction_container').find('.arrowup').show();
		$(this).hide();
	});
    $(document).on('click', '.interaction_container .arrowup', function()
	{
		$(this).parents('.interaction_container').find('.extended_message').hide();
		$(this).parents('.interaction_container').find('.message').show();
		
		$(this).parents('.interaction_container').find('.arrowdown').show();
		$(this).hide();
	});
    
    // used by the changeDebtor dialog
    // we retrieve the debtor list with ajax because getting all debtors for each service type significantly slows down page speed for users with huge number of debtors
    $(function()
    {
        $(document).on('change', '.BatchSelect', function()
        {
            var action          = $(this).val();
            var service_type    = action.substr(19);

            if(action.substr(0, 19) == "dialog:changeDebtor")
            {
            	$('#batch_confirm').find('input[name="AutoCompleteSearch[]"]').data('filter', 'exclude_debtor.' +  $('input[name="id"]').val());
            	$('#batch_confirm').find('#current_debtor').html(htmlspecialchars($('input[name="id"]').data('fullname')));
            	
            	
               	$('#batch_confirm').dialog('option', 'width', '700');
               	$("#batch_confirm").dialog("option", "position", { my: "center", at: "center", of: window });
             }
        });
    });

	$('#deactivate_two_factor_auth').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

	$('#deactivate_two_factor_auth input[name=imsure]').change( function()
	{
		if($(this).is(":checked"))
		{
			$('#deactivate_twofactor_btn').removeClass('button2').addClass('button1');
		}
		else
		{
			$('#deactivate_twofactor_btn').removeClass('button1').addClass('button2');
		}
	});

	$('#deactivate_twofactor_btn').click(function()
	{
		if($(this).hasClass('button1'))
		{
			$('form#deactivateTwoFactor').submit();
		}
	});

});

// Load more interactions
function loadMoreInteractions(){

	if($('.interaction_block:hidden').length > 0){
				
		$('.interaction_block:hidden').each(function(){
			$(this).slideDown('slow');
			return false;
		});		
	}
	if($('.interaction_block:hidden').length == 0){
		$('#dialog_interaction_more').hide();
	}
}

// Copy Invoice data from general data in debtor add/edit form
function copyInvoiceData(){
    $('input[name=InvoiceCompanyName]').val($('input[name=CompanyName]').val());
    $('select[name=InvoiceSex]').val($('select[name=Sex]').val());
    $('input[name=InvoiceInitials]').val($('input[name=Initials]').val());
    $('input[name=InvoiceSurName]').val($('input[name=SurName]').val());
    $('input[name=InvoiceAddress]').val($('input[name=Address]').val());
    if(IS_INTERNATIONAL == 'true'){ $('input[name=InvoiceAddress2]').val($('input[name=Address2]').val()); }
    $('input[name=InvoiceZipCode]').val($('input[name=ZipCode]').val());
    $('input[name=InvoiceCity]').val($('input[name=City]').val());
    $('select[name=InvoiceCountry]').val($('select[name=Country]').val());
    if(IS_INTERNATIONAL == 'true'){
    	
		$('input[name=InvoiceState]').val($('input[name=State]').val());
		$('select[name=InvoiceStateCode]').val($('select[name=StateCode]').val());
		if($('input[name=State]').val()){
    		getCountryStates($('select[name=Country]').val(), 'Invoice', $('input[name=State]').val());
    	}else{
    		getCountryStates($('select[name=Country]').val(), 'Invoice', $('select[name=StateCode]').val());	
    	}
   	}
    
    
    
	$('input[name=InvoiceEmailAddress]').val($('input[name=EmailAddress]').val());   
}

// Create random password and check availability of username
function createLogin()
{
    var debtorcode  = $('input[name="DebtorCode"]').val();
    // add or edit
    var debtorid    = ($('input[name="id"]').length > 0) ? $('input[name="id"]').val() : 0;
    
    $.post("XMLRequest.php", { action: "debtor_username", username: debtorcode, debtor_id: debtorid},
		function(data){
	       if(data.resultSet[0].Number == 0){
	           $('input[name="Username"]').val(debtorcode);
	       }else{
	           alert(__LANG_DEBTORCODE_ALREADY_IN_USE_AS_USERNAME);
	       }
	}, "json");
    
    $.post('XMLRequest.php', { action: 'generate_hosting_password'}, function(data){
       $('input[name="Password"]').val(data);       
	},"html");
}

function updateDebtorMandateID(OldDebtorCode)
{
	var DebtorID 	= ($('input[name="id"]').val() == undefined) ? 0 : $('input[name="id"]').val();
	var DebtorCode 	= $('input[name="DebtorCode"]').val();
	var MandateID 	= $('input[name="MandateID"]').val();
	
	$.post("XMLRequest.php", { action: "debtor_update_mandateid", debtor: DebtorID, debtorcode: DebtorCode, olddebtorcode: OldDebtorCode, mandateid: MandateID},
		function(data){
			if(data.MandateID)
			{
	       		$('input[name="MandateID"]').val(data.MandateID);
 			}
	}, "json");
	
}