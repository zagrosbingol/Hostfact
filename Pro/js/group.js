$(function(){
	if($('#GroupForm').html() != null){

		if($('#delete_group')){
			$('#delete_group').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('input[name=imsure]').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					$('#delete_group_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_group_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_group_btn').click(function(){
				if($('input[name=imsure]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}

        $(document).on('click', '.GroupsBatchCheck', function() {
			if($('input[name=Groups]').val() != null){
			
				if($(this).prop('checked')){
					$('.' + $(this).attr('name')).each(function(){
						
						$(this).prop('checked', true);
						
						$('input[name=Groups]').val($('input[name=Groups]').val().replace(new RegExp(',' + $(this).val() + ',',"g"),','));
						$('input[name=Groups]').val($('input[name=Groups]').val() + $(this).val() + ',');
					});
				}else{
					$('.' + $(this).attr('name')).each(function(){
						
						$(this).prop('checked', false);
						
						$('input[name=Groups]').val($('input[name=Groups]').val().replace(new RegExp(',' + $(this).val() + ',',"g"),','));
					});
				}
				SelectedItems = $('input[name=Groups]').val().substr(1,$('input[name=Groups]').val().length-2);
				if(SelectedItems != ""){
					$('#selectedNumber').show().children('strong').first().html(SelectedItems.split(',').length);
				}else{
					$('#selectedNumber').hide().children('strong').first().html(0);
				}
			}
		});
        $(document).on('click', '.GroupBatch', function() {
			
			if($(this).prop('checked')){
				$('input[name=Groups]').val($('input[name=Groups]').val().replace(new RegExp(',' + $(this).val() + ',',"g"),','));
				$('input[name=Groups]').val($('input[name=Groups]').val() + $(this).val() + ',');
			}
			else{			
				$('input[name=Groups]').val($('input[name=Groups]').val().replace(new RegExp(',' + $(this).val() + ',',"g"),','));
			}
			SelectedItems = $('input[name=Groups]').val().substr(1,$('input[name=Groups]').val().length-2);
			if(SelectedItems != ""){
				$('#selectedNumber').show().children('strong').first().html(SelectedItems.split(',').length);
			}else{
				$('#selectedNumber').hide().children('strong').first().html(0);
			}
		});
		
	}
});