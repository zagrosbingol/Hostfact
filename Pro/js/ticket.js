$(function(){
	if($('#TicketForm').html() != null){
		
		$('input[name="Debtor"]').change(function(){

			if($(this).val() == ""){
				$('#enveloppe').hide();
				$('input[name="Type"][value="email"]').prop('checked',true);
				if(!$('input[name="OriginalEmailAddress"]').val())
				{
					$('input[name="EmailAddress"]').val(''); 
				}
			}else{
				// get debtor data
				$.post("XMLRequest.php", { action: 'debtor_information', id: $(this).val()}, function(data){
					
					if(data.errorSet != undefined){
						$('#enveloppe').hide();
						$('input[name="Type"][value="email"]').prop('checked',true);
						if(!$('input[name="OriginalEmailAddress"]').val())
						{
							$('input[name="EmailAddress"]').val(''); 
						}
					}else{
					
						$('#formJQ-CompanyName').html(htmlspecialchars(data.resultSet[0].CompanyName));
						$('#formJQ-Name').html(htmlspecialchars(data.resultSet[0].Initials + ' ' + data.resultSet[0].SurName));
						$('#formJQ-Address').html(htmlspecialchars(data.resultSet[0].Address));
						$('#formJQ-ZipCodeCity').html(htmlspecialchars(data.resultSet[0].ZipCode + ' ' + data.resultSet[0].City));
						$('#formJQ-Country').html(htmlspecialchars(data.resultSet[0].CountryLong));
						$('#formJQ-EmailAddress').html('<span style="display: inline-block">' + htmlspecialchars(data.resultSet[0].EmailAddress).replace(/&amp;/g,'&').replace(/;/g, ',&nbsp;&nbsp;</span><span style="display: inline-block">') + '</span>');
					
						if(!$('input[name="OriginalEmailAddress"]').val())
						{
							$('input[name="EmailAddress"]').val(data.resultSet[0].EmailAddress.replace(/;/g, ', '));
						}
						$('#enveloppe').show();	
					}			
				}, "json");
			}				
		});
		
		$('#TicketID_text').click(function(){
			$(this).hide();
			$('input[name="TicketID"]').show().focus();
			$('input[name="TicketID"]').blur(function(){
				$(this).hide();
				$('#TicketID_text').html(htmlspecialchars($(this).val()) + " " + STATUS_CHANGE_ICON);
				$('#TicketID_text').show();			
			});
		});
		
		$('#TicketStatus_text').click(function(){
			$(this).hide();
			$('select[name="Status"]').show().focus();
			$('select[name="Status"]').blur(function(){
				$(this).hide();
				$('#TicketStatus_text').html(htmlspecialchars($('select[name="Status"] option:selected').text()) + " " + STATUS_CHANGE_ICON);
				$('#TicketStatus_text').show();	
				
				// Save new status
				$.post("XMLRequest.php", { action: 'change_ticket_status', id: $('input[name="id"]').val(), status: $('select[name="Status"]').val()});
			});
		});
		
		var CheckedTicketLockStatus = false;
		
		$('textarea.ckeditor_2').each( function() {
		    CKEDITOR.replace($(this).attr('id'), {height: '200px', fullPage : true});
		});
		
		// Lock ticket as soon as someone click 'reply'
		$('.reply_on_ticket_div').click(function(){
			
			$('#ticket_reply_show').slideDown(); 
			
			$(this).removeClass('a1 ico inline add');
			
			 // Check if not locked in the meantime...
			if(CheckedTicketLockStatus === true)
			{
				return;
			}
			else
			{
				$.post("XMLRequest.php", { action: 'ticket_lock_information', id: $('input[name="id"]').val()}, function(data){
					CheckedTicketLockStatus = true;
					
					if(data.locked == true)
					{
						$('#ticket_lock_status').html(data.message).show();
						$('#ticket_textarea_div').hide();
					}
					else
					{
						$('#ticket_lock_status').html(data.message).show();
					}

				}, 'json');	
			}
			
		});
		
		// When ticket is unlocked, release and hide reply field
        $(document).on('click', '#ticket_unlock_link, #ticket_unlock_self', function(){
				
				var lock_initiator = $(this).attr('id');
				
				$.post("XMLRequest.php", { action: 'ticket_unlock', id: $('input[name="id"]').val()}, function(data){
					if(data.locked == false)
					{
						CheckedTicketLockStatus = false;
						$('#ticket_lock_status').html('').hide();
						$('#ticket_textarea_div').show();
						$('.ticket_reply_div').show();
						$('#locked_ticket_warning').remove();

						if(lock_initiator == 'ticket_unlock_self')
						{
							$('.reply_on_ticket_div').addClass('a1 ico inline add');
							$('#ticket_reply_show').slideUp();
						} 
						
					}
				}, 'json');
		});
		
		$('#attachment_add').click(function(){
			if($('#attachment_div div').html() == ''){
				$('#attachment_add').before('<br />');
			}
			
			$('#attachment_div div').append('<input type="file" name="add_attachment[]" /><br />');
		});
		
	}
	
	if($('#delete_ticket')){
		$('#delete_ticket').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});		
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_ticket_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_ticket_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_ticket_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
});