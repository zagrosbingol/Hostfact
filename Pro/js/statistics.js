$(function(){
	
	var mouse_is_inside = false;

    $(document).on('mouseenter', '#chooseperiod, #ui-datepicker-div', function () {
        mouse_is_inside = true;
    });
    $(document).on('mouseleave', '#chooseperiod, #ui-datepicker-div', function () {
        mouse_is_inside = false;
    });
	
	$('body').mouseup(function(){

		if($('#chooseperiod').html() != null){
			if(!mouse_is_inside){
	        	$('#chooseperiod').hide();
			}
		}
    });
	
	var dates = $( "#start_date, #end_date" ).datepicker({
		dateFormat: DATEPICKER_DATEFORMAT,
		dayNamesMin: DATEPICKER_DAYNAMESMIN,
		monthNames: DATEPICKER_MONTHNAMES,
		firstDay: 1,
		changeMonth: true,
		onSelect: function( selectedDate ) {
			var option = this.id == "start_date" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
    $(document).on('change', "#start_date, #end_date", function () { $(this).datepicker("option", "dateFormat", DATEPICKER_DATEFORMAT);});
	
	$('#view_period').click(function(){
		save('statistics.overview','period','d_'+$('#start_date').val()+'_'+$('#end_date').val(), $('#current_url').val());
	});
	
	$('#Status_text').click(function(){
		if($('#chooseperiod:visible').length){
			$('#chooseperiod').fadeOut();	
		}else{
			$('#chooseperiod').fadeIn();	
		}
	});
	
	$('#chooseperiod a:not(#view_period)').click(function(){
		save('statistics.overview','period',$(this).attr('id'), $('#current_url').val());
	});
	
	$('.plot').click(function(){
		switch($(this).attr('id').replace('tab_', '')){
			case '1': plotGraph1($(this).attr('id').replace('tab_', '')); break;
			case '2': plotGraph1($(this).attr('id').replace('tab_', '')); break;
			case '3': plotGraph1($(this).attr('id').replace('tab_', '')); break;
		}
	});
	
});