$(function(){
	/**
	 * Hosting pages
 	 */
	
	// Show only
	if($('input[type="hidden"][name="hosting_id"]').val() > 0 && $('#domain_placeholder').length > 0){
		
		// Get hosting details
		var hosting_id = $('input[type="hidden"][name="hosting_id"]').val();
		
		// Get statistics
		$.post("XMLRequest.php", { action: "get_hosting_details", id: hosting_id, type:'stats'},
			function(data)
            {
                if(data.result[0])
                {
    				$('#hosting_account_password_change').show();
                }
                else if(data.errorMessage[0] != '')
                {
                    $('#hosting_account_error ul li').html(data.errorMessage[0]);
                    $('#hosting_account_error').show();
                }
                
				$('#hosting_discspace').html(data.resultSet[0].disc);
				$('#hosting_traffic').html(data.resultSet[0].traffic);
				//	$('#hosting_domains').html(data.resultSet[0].Domains);
				//	$('#hosting_databases').html(data.resultSet[0].MySQLDatabases);
		}, "json");
		
		// Get domains
		$.post("XMLRequest.php", { action: "get_hosting_details", id: hosting_id, type:'domains'},
		function(data){
			$('#domain_placeholder').html(data);
			$('#domain_placeholder .subtabs').tabs();
		}, "html");
		
		// Dialogs
		$('#dialog_download_pdf').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
		$('#dialog_email_pdf').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
        $('#dialog_updowngrade_hosting').dialog({modal: true, autoOpen: false, resizable: false, width: 750, height: 'auto'});

		// Toggle change password div
		$('input[name="also_change_password"]').click(function(){
			if($(this).prop('checked'))
			{
				$(this).parents('form').find('.change_password_div').removeClass('hide');
			}
			else
			{
				$(this).parents('form').find('.change_password_div').addClass('hide');
			}
		});
	}
    
    // Change password dialog (on page show)
	if($('#HostingPasswordDialog'))
    {	
    	$('#HostingPasswordForm input[name="imsure"]').click(function(){
    		if($('#HostingPasswordForm input[name="imsure"]:checked').val() != null)
    		{
    			$('#change_hosting_password_btn').removeClass('button2').addClass('button1');
    		}
    		else
    		{
    			$('#change_hosting_password_btn').removeClass('button1').addClass('button2');
    		}
    	});
    	
    	$('#change_hosting_password_btn').click(function(){
    		if($(this).hasClass('button1'))
    		{
				$(this).hide(); 
				$('#loader_saving').show(); 
				$('form[name=HostingPasswordForm]').submit();
			}
    	});
	}
	
	// Delete dialog
	if($('#delete_hosting')){
			$('#delete_hosting').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});
			$('#delete_hosting.autoopen').dialog('open');	
			
			$('#delete_hosting input[name=imsure]').click(function(){
				if($('#delete_hosting input[name=imsure]:checked').val() != null)
				{
					$('#delete_hosting_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_hosting_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_hosting_btn').click(function(){
				if($('#delete_hosting input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
	}
	
	// Suspend dialog
	if($('#suspend_hosting')){
			$('#suspend_hosting').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});
			
			$('#suspend_hosting input[name=imsure]').click(function(){
				if($('#suspend_hosting input[name=imsure]:checked').val() != null)
				{
					$('#suspend_hosting_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#suspend_hosting_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#suspend_hosting_btn').click(function(){
				if($('#suspend_hosting input[name=imsure]:checked').val() != null)
				{
					document.form_suspend.submit();
				}	
			});
	}
		
});


function getHostingDetails(hosting_id)
{
	
	$.post("XMLRequest.php", { action: "get_hosting_details", id: hosting_id, type:'stats'},
		function(data){
			
	}, "json");
}