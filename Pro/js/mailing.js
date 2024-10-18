$(function(){

	$('#add_recipients').dialog({modal: true, autoOpen: false, resizable: false, width: 700, height: 'auto'});
	
	$('#add_recipient_link').click(function(){
		$('#add_recipients').dialog('open');
	});
	
	$('#removeDebtors').change(function(){
		if($(this).val() == 'removeDebtors'){
			$(this).val('first');
			removefromRecipientTables();
		}
	});

    $(document).on('click', '.removeSingleDebtor', function(){
		removefromRecipientTables($(this).attr('rel'));
	});
	
	$('#selectDebtors').click(function(){
		updateRecipientTables();
	});
	

					
	$('input[name="ProductGroup"]').change(function(){
		selectProduct($(this).val());
		$(this).val('');
		$('input[name="AutoCompleteSearch[]"][data-inputfieldname="ProductGroup"]').val('');	
	});


});


function GetTemplateInformation(tid) {
	
	$('#attachmentbox').load($('#current_url').val() + '&template=' + tid + ' #attachment_files',function(){
		$.post("XMLRequest.php", { action: "template_information", templateid: tid},
			function(data){
	            if(data.resultSet != undefined && data.errorSet == undefined) {
	                var template = data.resultSet[0];
	                //$('.div_attachment').hide();
	                
	                $("input[name=Subject]").val(template.Subject);
	                $("input[name=SenderName]").val(template.SenderName);
	                $("input[name=SenderEmail]").val(template.SenderEmail);
	                $("input[name=CarbonCopy]").val(template.CarbonCopy);
	                $("input[name=BlindCarbonCopy]").val(template.BlindCarbonCopy);
	                $("input[name=Attachment]").val(template.Attachment);
	                /*
					var arr_attach = template.Attachment;
	                if(arr_attach.length == 0 || (arr_attach.length == 1 && arr_attach[0] == "")){
	                    $('#attachmentbox').hide();
	                }else{
	                    $('#attachmentbox').show();
						for(i =0; i < arr_attach.length; i++){
	                        $('#' + arr_attach[i]).show();
	                    }
	                }
	                */
	                
					CKEDITOR.instances['Message'].setData(template.Message);    
	          } else {
	            alert( data.errorSet[0].Message );             
	          }
		}, "json");
    });
    
    
   
}

function removefromRecipientTables(recipientSingleId){ 
	// Remove single recipient
	if(recipientSingleId >= 1){
		DebtorList = recipientSingleId;
	}else{
		DebtorList = '';
		
		$('input[name="id[]"]:checked').each(function(){
			DebtorList += $(this).val() + "|";
		});
	}
	
	$('#SubTable_Recipients').load($('#current_url').val() + '&remove_debtors=' + DebtorList + ' #MainTable_Recipients', function(){
		$('#SubTable_DebtorSearch').load($('#current_url').val() + ' #MainTable_DebtorSearch');
		$('#recipient_total').load($('#current_url').val() + ' #recipient_count');
	});
}

function updateRecipientTables(){
    DebtorList = '';
	
	$('input[name="debtorid[]"]:checked').each(function(){
		if($(this).css('visibility') != 'hidden'){
			DebtorList += $(this).val() + "|";	
		}
	});

	$('#SubTable_Recipients').load($('#current_url').val() + '&add_debtors=' + DebtorList + ' #MainTable_Recipients', function(){
		$('#SubTable_DebtorSearch').load($('#current_url').val() + ' #MainTable_DebtorSearch');
		$('#recipient_total').load($('#current_url').val() + ' #recipient_count');
	});
	
	$('#add_recipients').dialog('close');
}

function selectDebtorGroup(groupID){
    if(groupID == "all"){        
		$('#SubTable_Recipients').load($('#current_url').val() + '&add_debtors=all #MainTable_Recipients', function(){
			$('#SubTable_DebtorSearch').load($('#current_url').val() + ' #MainTable_DebtorSearch');
			$('#recipient_total').load($('#current_url').val() + ' #recipient_count');
		});
    }else{
  		$('#SubTable_Recipients').load($('#current_url').val() + '&add_group=' + groupID + ' #MainTable_Recipients', function(){
  			$('#SubTable_DebtorSearch').load($('#current_url').val() + ' #MainTable_DebtorSearch');
  			$('#recipient_total').load($('#current_url').val() + ' #recipient_count');
  		});        
    }

    $('#add_recipients').dialog('close');
}

function selectProduct(productID){
    $('#SubTable_Recipients').load($('#current_url').val() + '&add_product=' + productID + ' #MainTable_Recipients', function(){
    	$('#SubTable_DebtorSearch').load($('#current_url').val() + ' #MainTable_DebtorSearch');
    	$('#recipient_total').load($('#current_url').val() + ' #recipient_count');
    });

	$('#add_recipients').dialog('close');
}

function selectServer(serverID){
    $('#SubTable_Recipients').load($('#current_url').val() + '&add_server=' + serverID + ' #MainTable_Recipients', function(){
    	$('#SubTable_DebtorSearch').load($('#current_url').val() + ' #MainTable_DebtorSearch');
    	$('#recipient_total').load($('#current_url').val() + ' #recipient_count');
    });
    
	$('#add_recipients').dialog('close'); 
}