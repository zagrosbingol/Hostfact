$(function(){					
	$("button").button({icons: {primary: 'ui-icon-check'}});
	
	// Toggle all
	$("input[name=\"check_all\"]").click(function(){
		if($(this).prop('checked')){
			$('.checkbox').prop('checked', true);
		}else{
			$('.checkbox').prop('checked', false);
		}
		// Update button
		$('#submit_btn span.counter').html("(" + $('.checkbox:checked').length + ")");
	});
	
	// Update counter
	$('.checkbox').click(function(){ $('#submit_btn span.counter').html("(" + $('.checkbox:checked').length + ")"); });
	
	// Initial check for browser compatibility
	$('#submit_btn span.counter').html("(" + $('.checkbox:checked').length + ")");
	
	// Submit button
	$('#submit_btn').click(function(){				
		// Check for agree checkbox and name/function
		if($('input[name="agree"]:checked').length == 0 || $('input[name="Name"]').val() == ""  || $('input[name="Role"]').val() == ""){
			$('#error').show();
			return false;
		}else{
			$('#error').hide();
			$('form[name="subscription_form"]').submit();
		}
	});
});