$(function(){
	
	$('#decline_modification').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	
	// International version only
	if(IS_INTERNATIONAL == 'true')
	{
		$('select[name="Country"], select[name="InvoiceCountry"]').change(function(){
			
			var NamePrefix = $(this).attr('name').replace('Country', '');
			getCountryStates($(this).val(), NamePrefix, '');	
		});
	}
	
});