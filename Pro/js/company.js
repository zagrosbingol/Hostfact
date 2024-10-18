$(function(){

    $(document).on('click', '#form_save_company_data_btn', function()
	{
		if($('input[name="currentEmailAddress"]').val() != $('input[name="EmailAddress"]').val() || $('input[name="currentCompanyName"]').val() != $('input[name="CompanyName"]').val())
		{
			// Replace some vars
			companySyncDescription = __LANG_COMPANY_SYNC_DESCRIPTION.replace('{newSenderEmail}', ' &lt;' + $('input[name="EmailAddress"]').val() + '&gt;').replace('{newCompanyName}', $('input[name="CompanyName"]').val());
			$('#companySyncDescription').html(companySyncDescription);
				
			$('#sync_company_email').dialog({modal: true, resizable: false, width: 450, height: 'auto'});
		}
		else
		{
			document.form_create.submit();
		}
	});
	
	$('#sync_company_email_btn').click(function()
	{
		if($('input[name="sync_company_address"]:checked').val() != undefined)
		{
			$('input[name="SynchronizeEmail"]').val($('input[name="sync_company_address"]:checked').val());
			$('#sync_company_email').dialog('close');
			document.form_create.submit();
		}
	});
		
	$('#dashboard_list').sortable({ 
		placeholder: 'dashboarddragplaceholder', 
		forcePlaceholderSize: true, 
		axis: 'y',
		cursor: 'move',
		update: function(){
			$.post('XMLRequest.php', { action: 'order_dashboard', employee: $('#employee').val(), order: $(this).sortable('serialize') } );	
		}
	}).disableSelection();
	
	$('#dashboard_checkbox_table tr td input[type=checkbox]').change(function(){
		if($(this).prop('checked')){
			$('#dashboard_list ul[data-pref-order=' + $(this).data('pref-checkbox') + ']').show();
		}else{
			$('#dashboard_list ul[data-pref-order=' + $(this).data('pref-checkbox') + ']').hide();
		}
	});
	
	// International version only
	if(IS_INTERNATIONAL == 'true')
	{
		$('select[name="Country"]').change(function(){
			getCountryStates($(this).val(), '', '');	
		});
	}
	
	if($('#delete_debtor')){
		$('#delete_employee').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});		
		
		$('input[name="imsure"]').click(function(){
			if($('input[name="imsure"]:checked').val() != null)
			{
				$('#delete_employee_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_employee_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_employee_btn').click(function(){
			if($('input[name="imsure"]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
    
    if($('#deactivate_two_factor_auth'))
    {
		$('#deactivate_two_factor_auth').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
        
        $('#deactivate_two_factor_auth input[name="imsure"]').click(function()
        {
			if($(this).is(':checked'))
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
            $('form#deactivateTwoFactor').submit();	
		});
	}
    
    if($('#two_factor_authentication'))
    {
		$('#two_factor_authentication').dialog({modal: true, autoOpen: false, resizable: false, width: 700, height: 'auto'});		
		
		$('#activate_twofactor_btn').click(function()
        {
            if($(this).hasClass('button1'))
            {
                $('form#activateTwoFactor').submit();
            }	
		});
	}
	
	$('input[name^="Rights["]').change(function(){
		
		var RightName = $(this).attr('name').replace('Rights[','').replace(']','');
		
		if(RightName.indexOf('SHOW') >= 0 && $(this).prop('checked') == false){
			$('input[name="Rights[' + RightName.replace('_SHOW','_EDIT') + ']"]').prop('checked',false);
			$('input[name="Rights[' + RightName.replace('_SHOW','_DELETE') + ']"]').prop('checked',false);
		}else if(RightName.indexOf('SHOW') == -1 && $(this).prop('checked') == true){
			$('input[name="Rights[' + RightName.replace('_EDIT','_SHOW').replace('_DELETE','_SHOW') + ']"]').prop('checked',true);
		}
	});
	
});

function checkColumn(form,column){

	var columnChecked = true;
	
	$('input[name^="Rights["][name$="_'+column+']"]').each(function(index, element){
		if($(element).val() && !$(element).prop('checked')){
			columnChecked = false;
	   	}
	});

	$('input[name^="Rights["][name$="_'+column+']"]').each(function(index, element){
		if(columnChecked == false){
			$(element).prop('checked',true).change();
		}else{
			$(element).prop('checked',false).change();
		}
	});	
}
function checkRow(form,row){
	
	var rowChecked = true;
	
	$('input[name^="Rights['+row+'"]').each(function(index, element){
		if($(element).val() && !$(element).prop('checked')){
			rowChecked = false;
	   	}
	});

	$('input[name^="Rights['+row+'"]').each(function(index, element){
		if(rowChecked == false){
			$(element).prop('checked',true).change();
		}else{
			$(element).prop('checked',false).change();
		}
	});	
}