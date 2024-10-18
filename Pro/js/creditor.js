$(function(){
	/**
	 * Creditor pages
 	 */
	if($('#CreditorForm').html() != null){

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
			}
		}
		
		$('input[name=CompanyName]').keyup(function(){ toggleFormPart('CompanyName'); }).change(function(){ toggleFormPart('CompanyName'); });
		
		$('#CreditorCode_text').click(function(){
			$(this).hide();
			$('input[name=CreditorCode]').show().focus();
			$('input[name=CreditorCode]').blur(function(){
				$(this).hide();
				$('#CreditorCode_text').html(htmlspecialchars($(this).val()) + " " + STATUS_CHANGE_ICON);
				$('#CreditorCode_text').show();			
			});
		});
		
		// International version only
		if(IS_INTERNATIONAL == 'true')
		{
			$('select[name="Country"]').change(function(){
				
				getCountryStates($(this).val(), '', '');	
			});
		}
		
		
		// Show only
		if($('#delete_creditor')){
			$('#delete_creditor').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			
			$('#delete_creditor.autoopen').dialog('open');		
			
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_creditor_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_creditor_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_creditor_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
	}
	
});