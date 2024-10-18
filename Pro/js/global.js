var STATUS_CHANGE_ICON = '<span class=\"ico actionblock arrowdown mar2 pointer\" style=\"margin: 4px 2px 2px 2px; z-index: 1;\">&nbsp;</span>';
var EventTarget = null;

// Autocomplete
var AutoCompleteData = new Array;
var CurrentAutoCompleteSelector = null;
var AutoCompleteIsLoading = false;
var CloseCurrentAutoCompleteSelector = false;
var AutoCompleteLastSearch = '';
var AutoCompleteMaxResults = 250;
var mouse_is_inside_autocomplete = false;

// Polyfills
// We need this polyfill to make forEach methods work in all browsers
// See: https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

// Polyfill: closest - https://developer.mozilla.org/en-US/docs/Web/API/Element/closest
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector ||
        Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var el = this;

        do {
            if (Element.prototype.matches.call(el, s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

/**
 * Return true if request has ajaxResponse in it.
 * @param data
 * @returns {boolean}
 */
function detectAjaxCsrfResponse(data)
{
    return data.indexOf('{"ajaxResponse":"logout"') >= 0 || data.indexOf('{"ajaxResponse":"csrf","csrfToken"') >= 0;
}

$(function(){
	
	// CSRF protection
	// Adjust all <form>-tags
	$('form').each(function(index){
		if($(this).find('input[name="CSRFtoken"]').length == 0)
		{
			$(this).prepend('<input type="hidden" name="CSRFtoken" value="' + CSRFtoken + '" />');
		}
	}); 
	
	// Adjust all AJAX calls
	$(document).bind('ajaxSend', function(elm, xhr, s){
		if(s.type == 'POST')
		{
			if(s.context)
			{
				s.context.data = 'CSRFtoken=' + CSRFtoken + '&' + s.context.data;
			}
			else
			{
				s.data = 'CSRFtoken=' + CSRFtoken + '&' + s.data;	
			}
		}
	});

    $(document).ajaxComplete(function(event, xhr, settings) {
        var responseJSON = xhr.responseJSON;
        if (responseJSON === undefined && xhr.responseText.indexOf('{"ajaxResponse":"') >= 0) {
            responseJSON = $.parseJSON(xhr.responseText);
        }

        // Force logout if needed.
        if (responseJSON !== undefined && responseJSON.ajaxResponse !== undefined && responseJSON.ajaxResponse === 'logout') {
            location.href = 'login.php';
        }
        // If CSRF token is outdated, update it.
        if (responseJSON !== undefined && responseJSON.ajaxResponse !== undefined && responseJSON.ajaxResponse === 'csrf') {
            CSRFtoken = responseJSON.csrfToken;
        }
    });
	
	var mouse_on_profile_submenu = false;
	
	if($("input[name=file_type]").val() && 'draggable' in document.createElement('span')){
		
		if($('#dragndrophere').hasClass('hide')){
			$('#dragndrophere').removeClass('hide');
		}
		
		var file_type = $("input[name=file_type]").val();

        $(document).on('dragenter', '#container', function (e) {
			
			// Only allow file to be moved
			if(containsFiles(e.originalEvent)){
				$('#dropzone').removeClass('hide');
			}else{
				e.stopPropagation();
			}
		});
		
		// Only allow to drop the file into the drop zone
        $(document).on('dragover', 'body', function (e) {
			e.preventDefault();
		});
        $(document).on('drop', 'body', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$('#dropzone').addClass('hide');
		});

        $(document).on('dragover', '#dropzone', function (e) {
			e.preventDefault();
			e.originalEvent.dataTransfer.dropEffect = 'move';
		});
        $(document).on('dragleave', '#dropzone', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$('#dropzone').addClass('hide');
		});
        $(document).on('dragend', '#dropzone', function (e) {
			e.preventDefault();
			$('#dropzone').addClass('hide');
		});
        $(document).on('drop', '#dropzone', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$('#dropzone').addClass('hide');
			
			if($("#tab-attachments"))
			{
				$('a[href="#tab-attachments"]').click();
			}
			
			// Always empty the fileUploadError
			$('#fileUploadError').html('');
			
			
			if (e.originalEvent.dataTransfer.files) {
				var fd = new FormData();
				for (i = 0; i < e.originalEvent.dataTransfer.files.length; i++) {
				
					// Work with arrays to manage multiple files
					file = e.originalEvent.dataTransfer.files[i];
					fd.append("fileToUpload["+i+"]", file);
				}
				
				var xhr = new XMLHttpRequest();
				$('#dropzone').removeClass('hide');
				xhr.addEventListener('progress', function(e) {
					var done = e.position || e.loaded, total = e.totalSize || e.total;
					$('#dropzone .dropfileshere').html(__LANG_PROGRESS + ': ' + (Math.floor((done/total*1000)/10)) + '%');
				}, false);
				if ( xhr.upload ) {
					xhr.upload.onprogress = function(e) {
						var done = e.position || e.loaded, total = e.totalSize || e.total;
						$('#dropzone .dropfileshere').html(__LANG_PROGRESS + ': ' + (Math.floor((done/total*1000)/10)) + '%');
					};
					
					xhr.open("POST", "filemanager.php?uploadtype=drag&true&type="+file_type);
					xhr.send(fd);
				}
				
				xhr.onreadystatechange=function(){
					if (xhr.readyState==4 && xhr.status==200){
						if(xhr.response == ''){
							$('#fileUploadError').html(__LANG_DEFAULT_UPLOAD_FAILED);
						}else if(xhr.response != 'OK'){
							$('#fileUploadError').html(xhr.response);
						}
						
						if(file_type == 'creditinvoice_files'){
							get_uploaded_creditinvoice_file();
						}else{
							get_uploaded_files(file_type);
						}
						
						$('#dropzone').addClass('hide');
						$('#dropzone .dropfileshere').html(__LANG_MOVE_YOUR_FILE_HERE);
					}
				}
			}
		});
	}else{
		// Detecting drag and drop support
		if('draggable' in document.createElement('span')) {
			// Disable drag and drop
			window.addEventListener("dragover",function(e){
			  e = e || event;
			  e.preventDefault();
			},false);
			window.addEventListener("drop",function(e){
			  e = e || event;
			  e.preventDefault();
			},false);
		}else{
			if(!$('#dragndrophere').hasClass('hide')){
				$('#dragndrophere').addClass('hide');
			}
		}
	}
	
	//$(".datepicker").datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1});

    $(document).on('init','.datepicker', function()
    {
		$(this).datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1, minDate: $(this).data('dp-mindate')});
		$(this).change(function(){ $(this).datepicker("option","dateFormat",DATEPICKER_DATEFORMAT); })
	});

    $('.datepicker').each(function(){
        $(this).trigger('init');
    });
    $(document).on('focus','.datepicker:not(.hasDatepicker)', function()
    {
        $(this).trigger('init');
    });

	// Prevent autocomplete on datepicker.
    $(document).on('click', '.datepicker', function() {
        $(this).attr("autocomplete", "off");
    });
    $('.datepicker').attr("autocomplete", "off");

    $(document).on('change', ".datepicker_icon", function () {
        $(this).datepicker("option", "dateFormat", DATEPICKER_DATEFORMAT);
    });
	
	$(".datepicker_icon").datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1, showOn: 'both', buttonImage: 'images/ico_calendar.png', buttonImageOnly: true, onSelect: function(dateText, inst) { $(this).parent().find('img.ui-datepicker-trigger').attr('title',dateText); }});
	var dates = $("#start_date, #end_date").datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1,
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
	
	$('.input_date').each(function(){
		if($(this).find('img').attr('title') == '...'){
			$(this).find('img').attr('title',$(this).find('input').val());
		}
	});
	
	// Notes field
	$(".user_note textarea").autoGrow();

	$('.user_fullname').click(function(event)
    {
        $('.profile_submenu').toggle();
	});

    $(document).on('click', '#save_note',function()
    {
		$.post('XMLRequest.php', { action: 'save_note', Notes: $('textarea[name=Notes]').val() },function(data){
			$('#notes_saved_message').show();
			setTimeout("$('#notes_saved_message').fadeOut();",700);
		});
	});

    $(document).on('mouseenter', '.profile_submenu', function () {
        mouse_on_profile_submenu = true;
    });
    $(document).on('mouseleave', '.profile_submenu', function () {
        mouse_on_profile_submenu = false;
    });
    
    $('body').mouseup(function(e)
    {
		if(! mouse_on_profile_submenu)
        {
            // avoid dual trigger with username button
            if(e.target.className != 'user_fullname')
            {
                $('.profile_submenu').hide();   
            }
		}
    });
    
	
	if($('#notification_sidebar').html() != null && $('#notification_sidebar').html() == '')
    {
        $('#notification_sidebar_hr').hide();
		$.post('XMLRequest.php', { action: 'get_stats_summary' },function(data){
			$('#notification_sidebar').html(data);
            if(data != undefined && data != '' && data != ' ')
            {
                $('#notification_sidebar_hr').show();
            }
		});
	}
	
	// Edit field
    $(document).on('mouseenter', '.back2',function(){
		$(this).stop(true, true).animate({'background-color': '#EEEEEE'},400);
		$(this).find('.edit_label').stop(true, true).animate({'color': '#666666'}, 400);
	});

    $(document).on('mouseleave', '.back2',function(){
		$(this).stop(true, true).animate({'background-color': '#FFFFFF'},400);
		$(this).find('.edit_label').stop(true, true).animate({'color': '#CCCCCC'}, 400);
	});
	
	// Tooltip
    $(document).on('mouseenter', '.infopopupright', function () {
		$('#tooltip_div').css('left',$(this).offset().left + 40);
		$('#tooltip_div').css('top',$(this).offset().top - 14);
		$('#tooltip_div').html($(this).find('span.popup').html() + "<b></b>");
		$('#tooltip_div').show();
	});
    $(document).on('mouseleave', '.infopopupright', function () {
		$('#tooltip_div').hide();
	});

    $(document).on('mouseenter', '.infopopuptop,.infopopupinvoicestatus', function () {
		
		$(this).find('span.popup').css('left',-5);
		$(this).find('span.popup').css('top',-18-$(this).find('span.popup').height());
		$(this).find('span.popup b').css('top',10+$(this).find('span.popup').height());
		
		if($(this).hasClass('delaypopup')){
			$(this).find('span.popup').delay(400).fadeIn(1);
		}else{
			$(this).find('span.popup').show();
		}
	});

    $(document).on('mouseleave', '.infopopuptop,.infopopupinvoicestatus', function () {
		$(this).find('span.popup').hide();
		
		if($(this).hasClass('delaypopup')){
			$(this).find('span.popup').stop(true,true);
		}
		
	});

    $(document).on('mouseenter', '.infopopupleftsmall', function () {
		$(this).find('span.popup').css('top',-5);
		$(this).find('span.popup b').css('top',8);
		$(this).find('span.popup').show();
	});
    $(document).on('mouseleave', '.infopopupleftsmall', function () {
		$(this).find('span.popup').hide();
	});
	
	// Content should be minimal the height of the sidebar minus 10 padding
	if($('.sidebar_right')){
		$('#content').css('min-height',$('.sidebar_right').height()-10);
	}
	
	// Datepicker
	//$(".datepicker").datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1});

	
	// Hide extra information in table overview
	$('.tr_extra_info').hide();
	
	// Toggle extra information in table overview
    $(document).on('click', '.hover_extra_info_span',function(){
		if($(this).parent().parent().next().css('display') == 'none'){
			$('.hover_extra_info').removeClass('mark');
			$('.tr_extra_info').hide();			
			$(this).parent().parent().addClass('mark');
			$(this).parent().parent().next().show();
		}else{	
			$('.hover_extra_info').removeClass('mark');
			$('.tr_extra_info').hide();	
		}		
	});

	// Sorting hover
    $(document).on('mouseenter', 'a.arrowhover', function(){ $(this).addClass('arrowdown'); });
    $(document).on('mouseleave', 'a.arrowhover', function(){ $(this).removeClass('arrowdown'); });
	// Slow down sorting click, first save() function must be loaded
	$('.trtitle a').click(function(event){ event.preventDefault(); EventTarget = event.target;});
		
	// Tabs
	$("#tabs").tabs();
	$(".tabs").tabs();
	$("#subtabs").tabs();
	
	// Forms
    $(document).on('click', '#form_create_btn', function() {
		// Prevent double click
		if($(this).data('already_clicked') == true)
		{
			return true;
		}
		$(this).data('already_clicked', true);
		document.form_create.submit(); 
		
	});
	$('#form_edit_btn').click(function(){ 
		// Prevent double click
		if($(this).data('already_clicked') == true)
		{
			return true;
		}
		$(this).data('already_clicked', true);
		document.form_edit.submit(); 
	});
	
	// Dialogs
	$('.actiondialog').dialog({ autoOpen: false, width: 490, modal: true, resizable: false });
	$('#dialog').dialog({ autoOpen: false, width: 490, modal: true, resizable: false });

	if($('#force_download').html() != null){ setTimeout(function(){location.href = $('#force_download').attr('href')},300); }
	
	$('#templatelocation').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
	$('#templatelocation_btn').click(function(){
		document.form_templatelocation.submit();
		$('#templatelocation').dialog('close');
	});
	$('.printQuestion').click(function(event){
		event.preventDefault();
		$('#templatelocation').dialog('open');
		$('#templatelocation').find('form').attr('action', event.target);
	});
	
	$('a.ico.set1:not(.no-disable)').click(function(event){
		// Prevent double click
		if($(this).data('already_clicked') == true)
		{
			event.preventDefault();
		}
		else
		{
			$(this).data('already_clicked', true);
		}	
	});
	
	// VAT checker
	$('input[name=TaxNumber]').blur(function(){
		
		if($(this).val() != ''){
			$('#vat_status').html('<img src="images/icon_circle_loader_grey.gif" style="margin:6px 0 6px 6px;" />');
		
			$.post('XMLRequest.php', { action: 'vat_check', vat: $(this).val(), cc: $('select[name=Country]').val() }, function(data){
				if(data == 'OK'){
					$('#vat_status').html('<span class="loading_green">'+__LANG_VAT_NUMBER_IS_VALID+'</span>');
				}else if(data == 'BAD'){
					$('#vat_status').html('<span class="loading_red">'+__LANG_VAT_NUMBER_IS_NOT_VALID+'</span>');
				}else if(data == 'UNAVAILABLE'){
					$('#vat_status').html('<span class="loading_orange">'+__LANG_CHECK_TEMPORARILY_UNAVAILABLE+'</span>');
				}else{
					$('#vat_status').html('');
				}
			});
		}else{
			$('#vat_status').html('');
		}
		
	});
	
	// Messages
	$('.mark .close').click(function(){
		if($(this).hasClass('alt1') || $(this).hasClass('alt2') || $(this).hasClass('alt3')){
			$(this).parent().slideUp();
		}else{
			$(this).parent().hide(0,function(){ 
				$(this).parent().find('span#discount_link').show(); 
				//$(this).parent().find('span#link_testmail').show(); 
			});	
		}	
	});
	
	// Batch actions
	$(document).on('click', '.BatchCheck', function() {
		var CheckboxName = $(this).attr('name');
		
		if($('input[name='+CheckboxName+']:checked').val() != null)
		{
			$('.'+CheckboxName).prop('checked', true);
		}
		else
		{
			$('.'+CheckboxName).prop('checked', false);
		}
	});
	
	var BatchAction = BatchForm = '';
    $(document).on('change', '.BatchSelect', function()
	{
		if($(this).val() != "")
		{
			// In case of sending invoices, check for post-method
			if($(this).val() == 'dialog:invoicesent' || $(this).val() == 'dialog:send_invoice_concept' || $(this).val() == 'dialog:send_invoice_queue'){
				$(this).parents('form').find('#dialog_send_invoice_print').hide();
				if($(this).parents('form').find('input[name="ids[]"]:checked').parents('td').find('a').hasClass('printmethod')){
					$(this).parents('form').find('#dialog_send_invoice_print').show();
				}
			}else if($(this).val() == 'dialog:sent'){
				$(this).parents('form').find('#dialog_send_pricequote_print').hide();
				if($(this).parents('form').find('input[name="ids[]"]:checked').parents('td').find('a').hasClass('printmethod')){
					$(this).parents('form').find('#dialog_send_pricequote_print').show();
				}
			}
			
			if($(this).val().substr(0,7) == "dialog:")
			{
				BatchAction = $(this).val().substr(7);
				BatchForm = $(this).parents('form').attr('name');
				
				if($('#dialog_' + BatchAction).find('.design_div').html() != null)
				{
					$('#dialog_' + BatchAction).find('input[name="radio_send_invoicemethod"][value="setting"]').removeClass('design_div_toggle');
					$('#dialog_' + BatchAction).find('.design_div').hide();
					
					if($(this).parents('form').find('input[name="ids[]"]:checked').parents('td').find('a').hasClass('printmethod'))
					{
						$('#dialog_' + BatchAction).find('input[name="radio_send_invoicemethod"][value="setting"]').addClass('design_div_toggle');
						$('#dialog_' + BatchAction).find('.design_div').show();
					}
				}
				else if(!$('#dialog_' + BatchAction).html())
				{
					return false;
				}
				
				// reset submit button to active
				$('#batch_confirm_submit').removeClass('button2');
				$('#batch_confirm_submit').removeClass('button1');
				$('#batch_confirm_submit').addClass('button1');
				
				$selectCounter = 0;
				if($(this).parents('form').find('td > input[type=checkbox]:checked').length > 0)
				{
					$selectCounter = $(this).parents('form').find('td > input[type=checkbox]:checked').length;
				}
				else if($(this).parents('form').find('td > span > input[type=checkbox]:checked').length > 0)
				{
					$selectCounter = $(this).parents('form').find('td > span > input[type=checkbox]:checked').length;
				}
				
				$('#batch_confirm_text').html($('#dialog_' + BatchAction).html().replace('%d',$selectCounter) + '<br /><br />');
				
				if($('#dialog_' + BatchAction).attr('title'))
				{
					$('#batch_confirm').dialog('option', 'title', $('#dialog_' + BatchAction).attr('title'));
				}
				else
				{
					$('#batch_confirm').dialog('option', 'title', __LANG_CONFIRM_ACTION);
				}

                // Look for datepickers
                $('#batch_confirm .initDatepicker').removeClass('initDatepicker').addClass('datepicker');

				// disable submit button if we need confirm from checkbox
				if($('#batch_confirm input[name="imsure"]').length > 0)
				{
					$('#batch_confirm_submit').removeClass('button1');
					$('#batch_confirm_submit').addClass('button2');
				}
				
				$( "#batch_confirm" ).dialog({ close: function( event, ui ) 
				{
                    $('form[name="'+BatchForm+'"]').find('.BatchSelect').val($('form[name="'+BatchForm+'"]').find('.BatchSelect option:eq(0)').val());
					BatchAction = BatchForm = '';    
				}});

                // Call callback function?
                if($(this).find('option:checked').data('before-open') != undefined)
                {
                    var callback_function = $(this).find('option:checked').data('before-open');
                    if(callback_function != undefined && typeof window[callback_function] == 'function'){ window[callback_function](); }
                }
				
				$('#batch_confirm').dialog('open');
			}
			else
			{
				$(this).parents('form').submit();
			}
		}
	});
	
	$('#batch_confirm_submit').click(function()
    {
    	
        if(!$(this).hasClass('button2'))
        {
    		$('#batch_confirm').find('input').each(function(){
    			switch($(this).attr('type')){
    				case 'radio':
    					if($(this).prop('checked')){
    						$('form[name="'+BatchForm+'"]').find('input[name="'+$(this).attr('name')+'"][value='+$(this).val()+']').prop('checked',true);
    					}
    					break;
    				case 'checkbox':
    					if($(this).prop('checked')){
    						$('form[name="'+BatchForm+'"]').find('input[name="'+$(this).attr('name')+'"][value='+$(this).val()+']').prop('checked',true);
    					}
    					break;
    				case 'text':
                    case 'password':
    					$('form[name="'+BatchForm+'"]').find('input[name="'+$(this).attr('name')+'"]').val($(this).val());
    					break;
    			}
    		});
    		
    		$('#batch_confirm').find('select').each(function(){
    			$('form[name="'+BatchForm+'"]').find('select[name="'+$(this).attr('name')+'"]').val($(this).val());
    		});
    		
    		$('form[name="'+BatchForm+'"]').submit();
        }
	});

    $(document).on('change', '#batch_confirm input[name="imsure"]', function()
    {
        if($(this).is(':checked'))
        {
            $('#batch_confirm_submit').removeClass('button2');
            $('#batch_confirm_submit').addClass('button1');
        }
        else
        {
            $('#batch_confirm_submit').removeClass('button1');
            $('#batch_confirm_submit').addClass('button2');
        }  
    });    
	
	$('#batch_confirm_cancel').click(function(){
		$('#batch_confirm').dialog('close');
	});
	
	// Fix labels for double ID's
    $(document).on('click', '#batch_confirm label', function() {
		$('#batch_confirm #' + $(this).attr('for')).click();
	});
	
	$('#batch_confirm').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto', beforeClose: function(event, ui){ $("#batch_confirm").dialog("option", "width", 450); $("#batch_confirm").dialog("option", "position", { my: "center", at: "center", of: window }); }});
		
	
	if($('#files_list').html() != null){
		calc_files_total();
	}
	
	$('.upload_file').click(function(){
		filetype = $(this).data('filetype');
		$('#filemanager').dialog('open');
	});
	
	// Filemanager
	if($('#filemanager').html() != null){
		$('#filemanager').dialog({modal: true, autoOpen: false, resizable: false, width: 600,
			beforeClose: function(event, ui){
				 if(filetype == 'creditinvoice_files'){
					get_uploaded_creditinvoice_file();
				}else{
					get_uploaded_files(filetype);
				}
				
				$(this).load(location.href + ' #filemanager');
			}
			,open: function(){
				$('#filemanager').html('<iframe src="filemanager.php?type=' + filetype + '" width="574" height="510" frameborder="0"></iframe>');
			}
		});
	}
	
	if($('.filemanager_dialog').html() != null){
        $(document).on('click', 'input[name="name[]"], input[name="FileBatch"]', function(){
			if($('input[name="name[]"]:checked').length > 0){
				$('#select_files_btn').removeClass('button2').addClass('button1');
			}else{
				$('#select_files_btn').removeClass('button1').addClass('button2');
			}
		});
		
		$(document).on('click', 'input[name="name[]"], input[name="FileGenBatch"]', function (){
			if($('input[name="name[]"].FileGenBatch:checked').length > 0){
				$('#select_generatedfiles_btn').removeClass('button2').addClass('button1');
			}else{
				$('#select_generatedfiles_btn').removeClass('button1').addClass('button2');
			}
		});

        $('#select_files_btn').click(function () {
            if ($('input[name="name[]"]:checked').length > 0) {
                $(this).parents('form').submit();
            } else {
                // If no items checked, change button.
                $(this).removeClass('button1').addClass('button2');
            }
        });

        $('#select_generatedfiles_btn').click(function () {
            if ($('input[name="name[]"].FileGenBatch:checked').length > 0) {
                $(this).parents('form').submit();
            } else {
                // If no items checked, change button.
                $(this).removeClass('button1').addClass('button2');
            }
        });
	}

    $(document).on('click', '#creditinvoice_file_link',function(){
		$('input[name="File[]"]').val('');
		$('#creditinvoice_file_link').hide();
		$('#creditinvoice_file').html(__LANG_NO_FILE);
	});

    $(document).on('click', '.file_delete',function(){
		$(this).parent('li').remove();
	
		if($('#files_list_ul li').length == 0){
			$('#files_list').hide();
			$('#files_none').show();
		}else{
			calc_files_total();
		}
		
	});
	
	// Terminate subscriptions
    $(document).on('click', 'input[name=type_terminate_subscription]', function(){
			if($(this).parent().find('input[name=type_terminate_subscription]:checked').val() == "date"){
				$(this).parent().find('#dialog_subscription_termination_date').show();
				
				$(this).parent().find('input[name=stop_date]').datepicker({ dateFormat: DATEPICKER_DATEFORMAT, dayNamesMin: DATEPICKER_DAYNAMESMIN, monthNames: DATEPICKER_MONTHNAMES, firstDay: 1});
			}else{
				$(this).parent().find('#dialog_subscription_termination_date').hide();
			}
	});

	// Waiting dialog
	$('.wait_dialog').click(function(event){ 
		//event.preventDefault(); 
		//location.href=event.target;
		$('#dialog').dialog( 'option', 'title', __LANG_DIALOG_WAIT_TITLE ).html($('#dialog').html() + '<br /><p>' + __LANG_DIALOG_WAIT_DESC + '</p>').dialog('open');
			
	});
	
	$('.contact_toggler').click(function(){
		$(this).parents('.contact_placeholder').find('.hide').slideToggle();	
	});
	
	// Auto input checkers
    $(document).on('change', '.timechecker',function(){
		$(this).val(timechecker($(this).val()));
	});
		
	// Knowledegebase
	$('#kb_open_btn').click(function(){ $(this).hide(); $('#kb_content').show().animate({right: '0'},500, function(){ $('#kb_scroll').css('height',$('#kb_content').height()-90);$('#kb_scroll2').css('height',$('#kb_content').height()-90); 
	$('#kb_scroll').tinyscrollbar();$('#kb_scroll2').tinyscrollbar();  if($('#kb_results').html() == ''){ show_kb_articles(); } }); });
	$('#kb_close_btn').click(function(){  $('#kb_content').animate({right: '-350'},500, function(){ $('#kb_content').hide(); $('#kb_open_btn').show(); }); });

    $(document).on('click', '.kb_article', function (){ show_kb_article($(this).attr('id').replace('kb_article_','')); });
	
	
	$('input[name="kb_search"]').keyup(function(){ var tmp_kb_search = $(this); kb_search_delay(function(){ show_kb_articles(); },800)});

	$('textarea.autogrow').autoGrow();
	
    // Department support
	$('select[name="Sex"], select[name="InvoiceSex"], select[name="CustomerSex"]').change(function(){
        var InitialsField;
        
        if($(this).attr('name') == 'InvoiceSex') {
            InitialsField = 'InvoiceInitials'; 
        } else if($(this).attr('name') == 'CustomerSex') {
            InitialsField = 'CustomerInitials'; 
        } else {
            InitialsField = 'Initials';
		}
        
		if($(this).val() == 'd')
		{
			$('input[name="' + InitialsField + '"]').val(__LANG_DEPARTMENT);
		}	
	});

    $(document).on('click', 'input[name="radio_send_invoicemethod"]', function()
	{
		// Check if we need to show the design div
		if($(this).hasClass('design_div_toggle'))
		{
			// Show design div
			$(this).parent().parent().find('.design_div').show();
			$(this).parent().find('.design_div').show();
		}
		else
		{
			// Hide design div
			$(this).parent().parent().find('.design_div').hide();
			$(this).parent().find('.design_div').hide();
		}
	});
	
	var kb_search_delay = (function(){
		var kb_search_timer = 0;
		return function(callback, ms){
		clearTimeout (kb_search_timer);
		kb_search_timer = setTimeout(callback, ms);
		};
	})();

	
	if($('.autocomplete_search').html() != null)
	{
		$('.autocomplete_search').each(function(index,element)
		{
			$('body').append(element);
		});
		
		$('input[name="AutoCompleteSearch[]"]').each(function(index,element)
		{
			createAutoCompleteChangeListener(element);
		});


        $(document).on('focus', 'input[name="AutoCompleteSearch[]"]', function(){
			// Set initiator
			if(AutoCompleteIsLoading == true || (CurrentAutoCompleteSelector != null && $(this).data('inputfieldname') == $(CurrentAutoCompleteSelector).data('inputfieldname')))
			{
				// Focus on same field, so do not empty (and nothing else). Also wait for loading to be complete
				return false;
			}
			else
			{
				CurrentAutoCompleteSelector = $(this);
				AutoCompleteLastSearch = '';
				$(this).val('');
				$('.autocomplete_search').hide();
			}
			
			// Fix position
			var SelectOffset = $(CurrentAutoCompleteSelector).offset();
			
			
			// Initial load of all data
			if(AutoCompleteData[$(this).data('type')] == undefined)
			{
				AutoCompleteIsLoading = true;
				$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_arrow').hide();
				$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_loader').show();
				
				$.post("XMLRequest.php", { action: 'autocomplete_search', search_type: $(this).data('type'), search_for: '', filter: $(this).data('filter') }, function(data)
				{
					$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_arrow').show();
					$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_loader').hide();
					AutoCompleteIsLoading = false;

                    if (data.ajaxResponse !== undefined && data.ajaxResponse === 'csrf') {
                        // CRSF token will be set again on higher level.
                        CurrentAutoCompleteSelector = null;
                        return;
                    }

                    AutoCompleteData[data.search_type] = data;
										
					$('#div_for_autocomplete_' + $(CurrentAutoCompleteSelector).data('type')).css('top',SelectOffset.top + 24).css('left',SelectOffset.left).show(0);
					showAutoCompleteDiv(data.search_type);
				}, "json");
			}								
			else
			{
				$('#div_for_autocomplete_' + $(this).data('type')).css('top',SelectOffset.top + 24).css('left',SelectOffset.left).show(0);
				showAutoCompleteDiv($(this).data('type'));
			}
		});

        $(document).on('click', '.autocomplete_search_input_arrow, input[name="AutoCompleteSearch[]"]', function()
		{
			$(this).parent().find('input[name="AutoCompleteSearch[]"]').focus();
		});
		
		// Focus on other fields? hide it
        $(document).on('focus', 'textarea, input, select', function()
		{
			if($(this).attr('name') == 'AutoCompleteSearch[]' || $(this).parents('.autocomplete_search').html())
			{
				//Inside the search, so do nothing
				return;
			}

			closeAutoCompleteDiv(true);
		});
		
		// Resets the autocomplete input
        $(document).on('click', '.item_reset', function(){
			
			if(CurrentAutoCompleteSelector != null)
			{
				$('input[name="'+$(CurrentAutoCompleteSelector).data('inputfieldname')+'"]').val('').change();
				$(CurrentAutoCompleteSelector).val('');
				CurrentAutoCompleteSelector = null;
			}
			
			$('.autocomplete_search').hide();
		});
		
		// Search for content
        $(document).on('keyup', 'input[name="AutoCompleteSearch[]"]', function(){
			
			// Already loaded?
			if(AutoCompleteData[$(this).data('type')] == undefined)
			{
				$(this).focus();
				return;
			}
			
			input = $(this);
			if(AutoCompleteLastSearch != input.val()){
				showAutoCompleteDiv($(input).data('type'));
				AutoCompleteLastSearch = input.val();
			}
		});

		// Opens a group and show div with items in that group
        $(document).on('click', '.autocomplete_search_results_groups li', function(){
			groupID = $(this).data('groupid');
			if(groupID >= 0)
			{
				// Opens a group and shows the items in it
				$('.group_items').remove();
				
				var search_type = $(this).parents('.autocomplete_search').data('type');
				
				var HTML = '';
				$(AutoCompleteData[search_type].search_groups).each(function(index,element){
				
					if(element.id == groupID)
					{
						HTML += buildAutoCompleteList(search_type, element.children);
					}
				});

				if(HTML)
				{
					HTML = '<ul class="group_items">' + HTML + '</ul>';
				}

				$('#div_for_autocomplete_' + search_type).find('.autocomplete_search_results').append(HTML);

				$('.autocomplete_search_results_groups').addClass('autocomplete_marginleft');	
				$('.autocomplete_header .toptext').hide();
				$('.autocomplete_header .goback').show();
			}
		});
		
		// If an item is selected
        $(document).on('click', '.autocomplete_search_results .group_items li', function() {
			
			selectAutoCompleteItem($(this).parents('.autocomplete_search').data('type'), $(this).data('id'), $(this).attr('data-code'), htmlspecialchars_decode($(this).find('span').html()));
		});

        $(document).on('click', '.autocomplete_header .goback', function(){
			$('.autocomplete_search_results_groups').removeClass('autocomplete_marginleft');
			$('.autocomplete_header .toptext').hide();
			$('.autocomplete_header .group_label').show();
			$('.group_items').delay(300).hide(0);
		});

        $(document).on('change', '.group_filter', function()
		{
			var search_type = $(this).parents('.autocomplete_search').data('type');
			$(this).parents('.autocomplete_search').data('group_filter', (($(this).is(':checked') == true) ? 'yes' : 'no'));	
			showAutoCompleteDiv(search_type);
				
			// Update the the setting
			$.post('XMLRequest.php', { action: 'autocomplete_save_setting', type: search_type,value: (($(this).is(':checked') == true) ? 'yes' : 'no') });		
		});
		
		// When click outside dialog, close
        $(document).on('mouseenter', '.autocomplete_search, input[name="AutoCompleteSearch[]"]', function () {
            mouse_is_inside_autocomplete = true;
        });
        $(document).on('mouseleave', '.autocomplete_search, input[name="AutoCompleteSearch[]"]', function () {
            mouse_is_inside_autocomplete = false;
        });
		$('body').mouseup(function(){ 
			if(! mouse_is_inside_autocomplete){
				closeAutoCompleteDiv(false);
			}
		}); 
	}

    $(document).on('click', '.modal_link', function(event){
        event.preventDefault();

        openModal($(this).data('href'));
    });

    $(document).on('submit', '.modal_box form', function(){
        $.post($(this).attr('action'), $(this).serialize(), function(data){
            if(data == 'reload')
            {
                location.href=location.href;
            }
            else
            {
                $('.modal_box').html(data);
            }
        }, "html");

        return false;
    });

    // Prevent double click
    $(document).on('click', '#submit_button', function(){
        if($(this).data('already_clicked') == true)
        {
            return true;
        }

        $(this).data('already_clicked', true);

        $(this).parents('form').submit();
    });

    $(document).on('click', '.has_loading_button', function(){

        if($(this).parent().find('.loading_btn').html() != undefined)
        {
            $(this).addClass('hide');
            $(this).parent().find('.loading_btn').removeClass('hide');
        }
    });

    // Prepare loading button
    if($('#submit_button').html() != undefined && $('#submit_button').hasClass('has_loading_button'))
    {
        $('#submit_button').after('<div class="loading_btn hide"><img src="images/loadinfo.gif">' + $('#submit_button').data('loading') + '</div>');
    }

    // Show loader
    $('.button_has_loader').click(function(){

        $(this).next('.button_loader_span').removeClass('hide');
        $(this).remove();
    });

    // Initial screen with reference/data column
    createFlexibleReferenceAndDateColumn();
});


function print_r(o){
	return JSON.stringify(o,null,'\t').replace(/\n/g,'<br>').replace(/\t/g,'&nbsp;&nbsp;&nbsp;'); 
}

function containsFiles(event) {
	if (event.dataTransfer.types) {
		for (var i = 0; i < event.dataTransfer.types.length; i++) {
			if (event.dataTransfer.types[i] == "Files") {
				return true;
			}
		}
	}
	
	return false;
}

/**
 * Common invoice functions
 */
 
function getDaysInMonth(year, month){
	return new Date(year, month, 0).getDate();
}

 
function calculatePeriod(Periods, Periodic, StartPeriod, EndPeriod){	
	if($(StartPeriod).val() != ""){
		rewritedate = $(StartPeriod).val()
	}else{
		today = new Date();
		rewritedate = rewrite_date_db2site(today.getFullYear(),today.getMonth()+1,today.getDate());
		todayRewrite = rewritedate;
	}
	rewritedate = rewrite_date_site2db(rewritedate);
	
	var startdate = new Date();
	var day = rewritedate.substr(6,2);
	if(day.substr(0,1) == "0"){
		day = day.substr(1);
	}
	day = parseInt(day);
	var month = rewritedate.substr(4,2);
	if(month.substr(0,1) == "0"){
		month = month.substr(1);
	}
	month = parseInt(month)-1;		
	var year = parseInt(rewritedate.substr(0,4));
	
	startdate.setFullYear(year);
	startdate.setDate(1);
	startdate.setMonth(month);
	
	Periods = parseInt(Periods);
	
	switch(Periodic){
		case 'd': var str = startdate.setDate(day + Periods); break;
		case 'w': var str = startdate.setDate(day + 7*Periods); break;
		case 'm': var str = startdate.setMonth(month + Periods); break;
		case 'k': var str = startdate.setMonth(month + 3*Periods); break;
		case 'h': var str = startdate.setMonth(month + 6*Periods); break;
		case 'j': var str = startdate.setFullYear(year + Periods); break;
		case 't': var str = startdate.setFullYear(year + 2*Periods); break;
	} 
	
	if(Periodic != 'd' && Periodic != 'w')
	{
		var daysInMonth = getDaysInMonth(startdate.getFullYear(), startdate.getMonth()+1);
		if(daysInMonth < day)     
		{
			startdate.setDate(daysInMonth);	
		}
		else
		{
			startdate.setDate(day);	
		}
	} 
	
	var day = (startdate.getDate()) + "";
	var month = (startdate.getMonth()+1) + "";
	var year = (startdate.getFullYear());
	while(month.length < 2){
		month = "0" + month;
	}
	while(day.length < 2){
		day = "0" + day;
	}
	
	
	if($(StartPeriod).val() == ""){		
		$(StartPeriod).val(todayRewrite);
	}
	
	$('#form_NextDate_value').html($(StartPeriod).val());
	$('#formJQ-NextDate').val($(StartPeriod).val());
	
	if(!isNaN(year+month+day)){
		$(EndPeriod).val(rewrite_date_db2site(year,month,day));
	}else{
		$(EndPeriod).val("");
	} 

}

function formatAsMoney(mnt, dec) {
    if(mnt != mnt*1){
    	return '-';
    }
	
	mnt -= 0;
    if(dec == undefined)
        dec = 1;
     dec = Math.max(2,dec);

    var rounding_fix = (mnt >= 0) ? 0.00000000001 : -0.00000000001;
    var negative_length = (mnt >= 0) ? 0 : 1;
    mnt = (Math.round(mnt*(Math.pow(10,dec)) + rounding_fix)/(Math.pow(10,dec)));
    
    mnt = ((mnt == Math.round(mnt)) ? mnt + AMOUNT_DEC_SEPERATOR + '00' 
              : ( (mnt*10 == Math.round(mnt*10)) ? 
                       str_replace('.',AMOUNT_DEC_SEPERATOR,mnt) + '0' : str_replace('.',AMOUNT_DEC_SEPERATOR,mnt)));
    var DecimalLenght = (mnt.length - (strrpos(mnt, AMOUNT_DEC_SEPERATOR) + 1))+1;

    if(mnt.length >= (DecimalLenght+negative_length+4) && mnt.length < (DecimalLenght+negative_length+7)){
		var sRegExp = new RegExp('(-?[0-9]+)([0-9]{3})');
    	mnt = mnt.replace(sRegExp, '$1'+AMOUNT_THOU_SEPERATOR+'$2');
    }else if(mnt.length >= (DecimalLenght+negative_length+7)){
    	var sRegExp = new RegExp('(-?[0-9]+)([0-9]{3})([0-9]{3}?)');
    	mnt = mnt.replace(sRegExp, '$1'+AMOUNT_THOU_SEPERATOR+'$2'+AMOUNT_THOU_SEPERATOR+'$3');
   	}
    return mnt;
}

function formatAsFloat(mnt, dec) {
    if(mnt != mnt*1){
    	return '-';
    }
	
	mnt -= 0;
    if(dec == undefined){
        dec = 0;
   	}

    var rounding_fix = (mnt >= 0) ? 0.00000000001 : -0.00000000001;
    mnt = (Math.round(mnt*(Math.pow(10,dec)) + rounding_fix)/(Math.pow(10,dec)));
    
    mnt = ((mnt == Math.round(mnt)) ? mnt 
              : ( (mnt*10 == Math.round(mnt*10)) ? 
                       mnt + '0' : mnt));
    
    return mnt;
}

function formatAsFloatVat(mnt, dec) {
    if(mnt != mnt*1){
    	return '-';
    }
	
	mnt -= 0;
    if(dec == undefined){
        dec = 0;
   	}

    var rounding_fix = (mnt >= 0) ? 0.00000000001 : -0.00000000001;
    mnt = (Math.round(mnt*(Math.pow(10,dec)) + rounding_fix)/(Math.pow(10,dec)));
    
    return mnt;
}

function deformatAsMoney(Amount){
	if(Amount == undefined)
	{
		return 0;
	}

	// Case: both, thou - dec
	if(AMOUNT_THOU_SEPERATOR && AMOUNT_DEC_SEPERATOR && strrpos(Amount, AMOUNT_THOU_SEPERATOR) !== false && strrpos(Amount, AMOUNT_DEC_SEPERATOR) !== false && strrpos(Amount, AMOUNT_THOU_SEPERATOR) < strrpos(Amount, AMOUNT_DEC_SEPERATOR))
	{
		Amount = str_replace(AMOUNT_THOU_SEPERATOR,'',Amount);
		return str_replace(AMOUNT_DEC_SEPERATOR,'.',Amount);
	}
	// Case: both, dec - thou (mixed up)
	else if(AMOUNT_THOU_SEPERATOR && AMOUNT_DEC_SEPERATOR && strrpos(Amount, AMOUNT_THOU_SEPERATOR) !== false && strrpos(Amount, AMOUNT_DEC_SEPERATOR) !== false && strrpos(Amount, AMOUNT_THOU_SEPERATOR) > strrpos(Amount, AMOUNT_DEC_SEPERATOR))
	{
		Amount = str_replace(AMOUNT_DEC_SEPERATOR,'',Amount);
		return str_replace(AMOUNT_THOU_SEPERATOR,'.',Amount);
	}
	// Case: only dec
	else if(strrpos(Amount, AMOUNT_THOU_SEPERATOR) === false && AMOUNT_DEC_SEPERATOR && strrpos(Amount, AMOUNT_DEC_SEPERATOR) !== false)
	{
		return str_replace(AMOUNT_DEC_SEPERATOR,'.',Amount);
	}
	// Case: no signs or only thousand sign
	else
	{
		// Always remove thousand sign, except for . and ,
		if(AMOUNT_THOU_SEPERATOR != '.' && AMOUNT_THOU_SEPERATOR != ','){ 
			Amount = str_replace(AMOUNT_THOU_SEPERATOR,'',Amount); 
		}else{
			// If more than one thousand sign, remove all except last
			if((Amount.split(AMOUNT_THOU_SEPERATOR).length - 1) > 1){
				Amount = str_replace(AMOUNT_THOU_SEPERATOR,'',Amount.substr(0,strrpos(Amount, AMOUNT_THOU_SEPERATOR))) + '' + Amount.substr(strrpos(Amount, AMOUNT_THOU_SEPERATOR));
			}
			
			// How many decimals do we have? If not 3, make it a decimal instead of thousand sign
			//if((Amount.length - (strrpos(Amount, AMOUNT_THOU_SEPERATOR) + 1)) != 3){
				Amount = str_replace(AMOUNT_THOU_SEPERATOR,AMOUNT_DEC_SEPERATOR,Amount);
			//}else{
			//	Amount = str_replace(AMOUNT_THOU_SEPERATOR,'',Amount);
			//}
		}
	
		return str_replace(AMOUNT_DEC_SEPERATOR,'.',Amount); 
	}		
}

function rewrite_date_db2site(year, month, day){
	day = ""+day;
	month = ""+month;	
	
	if(day.length == 1){ day = "0"+day; }
	if(month.length == 1){ month = "0"+month; }
	
  var format = DATEFORMAT;
  format = format.replace("%d",day);
  format = format.replace("%m",month);
  format = format.replace("%Y",year);
  year += "";

  format = format.replace("%y",year.substring(2));

  return format;
}
    
function rewrite_date_site2db(date){
	var format = DATEFORMAT;

	var day, month = "00";
	var year = "0000";
  	if(date != null){
        if(format.match("%Y")){ year = date.substring(format.indexOf("%Y"),format.indexOf("%Y")+4); format = format.replace("%Y", year);}
		if(format.match("%d")){ day = date.substring(format.indexOf("%d"),format.indexOf("%d")+2); format = format.replace("%d", day); }
		if(format.match("%m")){ month = date.substring(format.indexOf("%m"),format.indexOf("%m")+2); format = format.replace("%m", month);}
        if(format.match("%y")){ year = '20' + date.substring(format.indexOf("%y"),format.indexOf("%y")+2);format = format.replace("%y", year); }
	}
  
	return year+""+month+""+day;
}
 
function removeElement(LineID){
	
	if($('#InvoiceElements tr:visible').length > 1){

		$('input[name="Number['+LineID+']"]').parent().parent().hide();
		$('input[name="Number['+LineID+']"]').val('0');
	}else{
		$('#add_new_element').click();
		
		$('input[name="Number['+LineID+']"]').parent().parent().hide();
		$('input[name="Number['+LineID+']"]').val('0');	
	}
	
	getLineTotal(LineID);
}

var CustomPriceObject = new Array();
var ProductPeriodPriceObject = new Array();

function getProductData(LineID, ProductCode){
	if(ProductCode){
		$.post("XMLRequest.php", { action: 'get_product', line: LineID, productcode: ProductCode }, function(data){
			
			if(data.HasCustomPrice == 'period')
			{
				CustomPriceObject[ProductCode] = data.CustomPrices;
			}
			
			// Save product period price info
			ProductPeriodPriceObject[ProductCode] = new Array();
			ProductPeriodPriceObject[ProductCode]['PricePeriod'] = data.PricePeriod;
			ProductPeriodPriceObject[ProductCode]['PriceIncl'] = data.PriceIncl;
			ProductPeriodPriceObject[ProductCode]['PriceExcl'] = data.PriceExcl;

			$('textarea[name="Description['+data.LineID+']"]').val(data.Description).autoGrow();

            var Number = extractNumberAndSuffix($('input[name="Number['+LineID+']"]').val());
            var Suffix = $('input[name="Number['+LineID+']"]').val().substring(Number.length);

            if(data.NumberSuffix == '')
            {
                $('input[name="Number['+data.LineID+']"]').val(Number);
            }
            else
            {
                $('input[name="Number['+data.LineID+']"]').val(Number + ' ' + data.NumberSuffix);
            }

			if(data.PricePeriod){
				$('#PeriodicType-'+data.LineID+'-period').prop('checked', true).click();
				$('select[name="Periodic['+data.LineID+']"]').val(data.PricePeriod);
				
				// If we don't have a period
				if($('input[name="StartPeriod['+LineID+']"]').html() == null){
					$('#formJQ-Period-' + LineID).html($('input[name="Periods['+LineID+']"]').val() + ' ' + $('select[name="Periodic['+LineID+']"] option:selected').text());
				}else{
					calculatePeriod($('input[name="Periods['+LineID+']"]').val(), $('select[name="Periodic['+LineID+']"]').val(), $('input[name="StartPeriod['+LineID+']"]'), $('input[name="EndPeriod['+LineID+']"]'));
					$('#formJQ-Period-' + LineID).html($('input[name="StartPeriod['+LineID+']"]').val() + ' ' + __LANG_TILL + ' ' + $('input[name="EndPeriod['+LineID+']"]').val());
					$('#EndPeriod-' + LineID).html(__LANG_TILL + ' ' + $('input[name="EndPeriod['+LineID+']"]').val());
				}
			}else{
				$('#PeriodicType-'+data.LineID+'-once').prop('checked', true).click();
				$('#formJQ-Period-' + LineID).html(__LANG_ONCE);
				$('#EndPeriod-' + LineID).html(__LANG_TILL + ' ' + $('input[name="EndPeriod['+LineID+']"]').val());
			}
			
			$('input[name="PriceExcl['+data.LineID+']"]').val(data.PriceExcl);
			$('input[name="PriceIncl['+data.LineID+']"]').val(data.PriceIncl);
			
			vatshifthelper = true; // Only hide if already shown
			if($('#formJQ-Taxable').val() == 'true'){
				$('#formJQ-TaxPercentage-'+data.LineID).val(data.TaxPercentage);
				if(data.TaxPercentage == '0.00'){ data.TaxPercentage = 0; }
				$('input[name="TaxRadio['+data.LineID+']"][value="'+data.TaxPercentage+'"]').prop('checked',true);
				$('#formJQ-TaxPercentageText-'+data.LineID).html(vat(data.TaxPercentageLong) + "%");
				$('#taxadjuster-' + data.LineID).removeClass('hide');
				
				if(parseFloat($('#formJQ-TaxPercentage-'+data.LineID).val()) > 0){
					vatshifthelper = false;
				}
				
			}else if($('input[name="TaxRate1"]').val() != ""){
				data.TaxPercentage = $('input[name="TaxRate1"]').val();
				//$('#formJQ-TaxPercentage-'+data.LineID).val(data.TaxPercentage);
				if(data.TaxPercentage == '0.00'){ data.TaxPercentage = 0; }
				//$('input[name="TaxRadio['+data.LineID+']"][value="'+data.TaxPercentage+'"]').prop('checked', true);
				$('#formJQ-TaxPercentageText-'+data.LineID).html(vat(data.TaxPercentage * 100) + "%");
				$('#taxadjuster-' + data.LineID).addClass('hide');
				
				if(data.TaxPercentage > 0){
					vatshifthelper = false;	
				}
			}else{
				$('#formJQ-TaxPercentage-'+data.LineID).val(data.TaxPercentage);
				$('#formJQ-TaxPercentageText-'+data.LineID).html("0%");
				$('#taxadjuster-' + data.LineID).addClass('hide');
			}
			
			//Total tax
			if($('input[name="TaxRate2"]').val() != '' || parseInt($('input[name="TaxRate2"]').val()) == $('input[name="TaxRate2"]').val()){	
				if($('input[name="TaxRate2"]').val() > 0)
				{
					vatshifthelper = false;
				}
			}else{
				if($('input[name="TotalTaxRadio"]:checked').val() > 0){
					vatshifthelper = false;
				}
			}
			
			if(vatshifthelper){
				// Keep current state
			}else{
				// Hide
				$('#shiftvat_div').hide();
				$('input[name="VatShift_helper"]').val('');
				$('input[name="VatShift"]').prop('checked',false);
			}
			
			getLineTotal(data.LineID);		
		}, "json");
	}else{
		if($('#formJQ-Taxable').val() == 'true'){
			$('#taxadjuster-' + LineID).show();
		}else{
			$('#taxadjuster-' + LineID).hide();
		}
	}

}

function showCustomPeriodPrice(LineID, SelectObject)
{
	// Only for productcodes			               
	if(LineID == 'subscription')
	{
		if(SelectObject != undefined)
		{
			var ProductCode = $(SelectObject).val();
		}
		else
		{
			var ProductCode = $('input[name="subscription[Product]"]').val();
		}
	}
	else if(LineID == 'hostingimport')
	{
		var ProductCode = $('input[name="Product"]').val();
	}
	else
	{
		var ProductCode = $('input[name="ProductCode[' + LineID + ']"]').val()
	}

	if(ProductCode && ProductPeriodPriceObject[ProductCode] == undefined)
	{
		// Information not available, get it and then check again
		$.post("XMLRequest.php", { action: 'get_product', line: LineID, productcode: ProductCode }, function(data){

			if(data.HasCustomPrice == 'period')
			{
				CustomPriceObject[ProductCode] = data.CustomPrices;
			}
			
			// Save product period price info
			ProductPeriodPriceObject[ProductCode] = new Array();
			ProductPeriodPriceObject[ProductCode]['PricePeriod'] = data.PricePeriod;
			ProductPeriodPriceObject[ProductCode]['PriceIncl'] = data.PriceIncl;
			ProductPeriodPriceObject[ProductCode]['PriceExcl'] = data.PriceExcl;
			
			// Do again
			showCustomPeriodPrice(LineID, SelectObject);
		}, "json");
		// Exit call
		return false;
	}

	
	var ParentPeriodDiv = (LineID == 'subscription' || LineID == 'hostingimport') ? $('#subscription_div_period') : $('#Periodic-period-'+LineID);
	
	$(ParentPeriodDiv).find('.custom_period_price_div').remove();
	
	if(ProductCode && CustomPriceObject[ProductCode] != undefined)
	{
		
		var Temp = $.parseJSON(CustomPriceObject[ProductCode]);
		
		if(Temp.period != undefined)
		{	
			var HTML = '<div class="custom_period_price_div" style="display:inline-block;border:1px dotted #ccc; padding: 3px 5px;margin:10px 0px 0px 0px;line-height:17px;">'+ Temp.periodHTML + '</div>';
		
			if(LineID == 'subscription' || LineID == 'hostingimport')
			{
				// Service page
				$(ParentPeriodDiv).append(HTML);								
			}	
			else
			{
				// Invoice / Pricequote / Order pages								
				$('select[name="Periodic['+LineID+']"]').after(HTML);
			}
			
			if((LineID == 'subscription' && VAT_CALC_METHOD == 'incl') || (LineID != 'subscription' && $('input[name="VatCalcMethod"]').val() == "incl"))
			{
				$(ParentPeriodDiv).find('.custom_price_excl').hide();
				$(ParentPeriodDiv).find('.custom_price_incl').show();
			}
			else
			{
				$(ParentPeriodDiv).find('.custom_price_incl').hide();
				$(ParentPeriodDiv).find('.custom_price_excl').show();
			}			
			return true;
		}
		
	}
	return false;
}

function selectCustomPeriodPrice(Object_link, Periods, Periodic)
{
	if($(Object_link).parents('#tab-subscription').html() != null)
	{
		$(Object_link).parents('#tab-subscription').find('input[name="subscription[Periods]"]').val(Periods);
		$(Object_link).parents('#tab-subscription').find('select[name="subscription[Periodic]"]').val(Periodic).focus().change();
	}
	else if($(Object_link).parents('#account_periodic').html() != null)
	{
		$(Object_link).parents('#account_periodic').find('input[name="Periods"]').val(Periods);
		$(Object_link).parents('#account_periodic').find('select[name="Periodic"]').val(Periodic).focus().change();
	}
	else
	{
		$(Object_link).parents('td').find('input[name^="Periods["]').val(Periods);
		$(Object_link).parents('td').find('select[name^="Periodic["]').val(Periodic).focus().change();
	}
}

function checkCustomPeriodPrice(LineID, FromChange)
{
	// Only for productcodes
	if(LineID == 'subscription')
	{
		var ProductCode = $('input[name="subscription[Product]"]').val();
	}
	else if(LineID == 'hostingimport')
	{
		var ProductCode = $('input[name="Product"]').val();
	}
	else
	{
		var ProductCode = $('input[name="ProductCode[' + LineID + ']"]').val()
	}

	if(!ProductCode && FromChange != 'Periodic')
	{
		if(LineID != 'subscription' && LineID != 'hostingimport')
		{
			// Update line total
			getLineTotal(LineID);
		}
		return false;
	}
	
	if(ProductCode && ProductPeriodPriceObject[ProductCode] == undefined)
	{
		// Information not available, get it and then check again
		$.post("XMLRequest.php", { action: 'get_product', line: LineID, productcode: ProductCode }, function(data){

			if(data.HasCustomPrice == 'period')
			{
				CustomPriceObject[ProductCode] = data.CustomPrices;
			}
			
			// Save product period price info
			ProductPeriodPriceObject[ProductCode] = new Array();
			ProductPeriodPriceObject[ProductCode]['PricePeriod'] = data.PricePeriod;
			ProductPeriodPriceObject[ProductCode]['PriceIncl'] = data.PriceIncl;
			ProductPeriodPriceObject[ProductCode]['PriceExcl'] = data.PriceExcl;
			
			// Do again
			checkCustomPeriodPrice(LineID, FromChange);
		}, "json");
		// Exit call
		return false;
	}
	
	var ReCalcPeriod = (FromChange == 'Periodic') ? true : false;
	
	if(ProductCode && CustomPriceObject[ProductCode] != undefined)
	{
		var Temp = $.parseJSON(CustomPriceObject[ProductCode]);
		
		if(LineID == 'subscription')
		{
			var CurrentPeriods =  $('input[name="subscription[Periods]"]').val();
			var CurrentPeriodic = $('select[name="subscription[Periodic]"]').val();
		}
		else if(LineID == 'hostingimport')
		{
			var CurrentPeriods =  $('input[name="Periods"]').val();
			var CurrentPeriodic = $('select[name="Periodic"]').val();
		}
		else
		{
			var CurrentPeriods =  $('input[name="Periods['+LineID+']"]').val();
			var CurrentPeriodic = $('select[name="Periodic['+LineID+']"]').val();
		}
		

		if(Temp.period != undefined && Temp.period[CurrentPeriods + '-' + CurrentPeriodic] != undefined)
		{
			var CustomPrice = Temp.period[CurrentPeriods + '-' + CurrentPeriodic];
			
			if(LineID == 'subscription')
			{
				$('input[name="subscription[PriceIncl]"]').val(formatAsMoney(CustomPrice['PriceIncl'],5)).change();
				$('input[name="subscription[PriceExcl]"]').val(formatAsMoney(CustomPrice['PriceExcl'],5)).change();
			}
			else if(LineID == 'hostingimport')
			{
					$('input[name="PriceExcl"]').val(formatAsMoney(CustomPrice['PriceExcl'],5)).change();
			}
			else
			{
				$('input[name="PriceIncl['+LineID+']"]').val(formatAsMoney(CustomPrice['PriceIncl'],5)).change();
				$('input[name="PriceExcl['+LineID+']"]').val(formatAsMoney(CustomPrice['PriceExcl'],5)).change();
				
				// Update line total
				getLineTotal(LineID);
			}
			return true;
		}
		else if(Temp.period != undefined)
		{
			// Set default
			if(LineID == 'subscription')
			{
				$('input[name="subscription[PriceIncl]"]').val(formatAsMoney(Temp.period['default']['PriceIncl'],5)).change();
				$('input[name="subscription[PriceExcl]"]').val(formatAsMoney(Temp.period['default']['PriceExcl'],5)).change();
				$('select[name="subscription[Periodic]"]').attr('prev', ProductPeriodPriceObject[ProductCode]['PricePeriod']);
			}
			else if(LineID == 'hostingimport')
			{
				$('input[name="PriceExcl"]').val(formatAsMoney(Temp.period['default']['PriceExcl'],5)).change();
				$('select[name="Periodic"]').attr('prev', ProductPeriodPriceObject[ProductCode]['PricePeriod']);
			}
			else
			{
				$('input[name="PriceIncl['+LineID+']"]').val(formatAsMoney(Temp.period['default']['PriceIncl'],5)).change();
				$('input[name="PriceExcl['+LineID+']"]').val(formatAsMoney(Temp.period['default']['PriceExcl'],5)).change();
				$('select[name="Periodic['+LineID+']"]').attr('prev', ProductPeriodPriceObject[ProductCode]['PricePeriod']);
			}
			ReCalcPeriod = true;
		}
	}

	if(ReCalcPeriod)
	{
		if(LineID == 'subscription')
		{
			var PeriodicSelectObject = $('select[name="subscription[Periodic]"]');
			var PriceInclInputObject = $('input[name="subscription[PriceIncl]"]');
			var PriceExclInputObject = $('input[name="subscription[PriceExcl]"]');
			var TaxInputObject = $('select[name="subscription[TaxPercentage]"]');
		}
		else if(LineID == 'hostingimport')
		{
			var PeriodicSelectObject = $('select[name="Periodic"]');
			var PriceExclInputObject = $('input[name="PriceExcl"]');
			var TaxInputObject = $('input[name="TaxPercentage');
		}
		else
		{
			var PeriodicSelectObject = $('select[name="Periodic['+LineID+']"]');
			var PriceInclInputObject = $('input[name="PriceIncl['+LineID+']"]');
			var PriceExclInputObject = $('input[name="PriceExcl['+LineID+']"]');
			var TaxInputObject = $('input[name="TaxPercentage['+LineID+']"]');
		}
		
		var CurrentPeriodic = $(PeriodicSelectObject).val();
		var ProductPricePeriod = ($(PeriodicSelectObject).attr('prev') && $(PeriodicSelectObject).attr('prev') != 'new') ? $(PeriodicSelectObject).attr('prev') : ((ProductCode) ? ProductPeriodPriceObject[ProductCode]['PricePeriod'] : $(PeriodicSelectObject).val());
	
		$(PeriodicSelectObject).attr('prev', CurrentPeriodic);
		var OldPrice = deformatAsMoney(((LineID == 'subscription' && VAT_CALC_METHOD == 'incl') || (LineID != 'subscription' && $('input[name="VatCalcMethod"]').val() == "incl")) ? $(PriceInclInputObject).val(): $(PriceExclInputObject).val());

		if(CurrentPeriodic != ProductPricePeriod)
		{
			switch(CurrentPeriodic){
				case 't':
					if(ProductPricePeriod == 'j'){ OldPrice = OldPrice * 2; }
					else if(ProductPricePeriod == 'h'){ OldPrice = OldPrice * 4; }
					else if(ProductPricePeriod == 'k'){ OldPrice = OldPrice * 8; }
					else if(ProductPricePeriod == 'm'){ OldPrice = OldPrice * 24; }
					else if(ProductPricePeriod == 'w'){ OldPrice = OldPrice * 104; }
					else if(ProductPricePeriod == 'd'){ OldPrice = OldPrice * 365 * 2; }
					break;
				case 'j':
					if(ProductPricePeriod == 't'){ OldPrice = OldPrice / 2; }
					else if(ProductPricePeriod == 'h'){ OldPrice = OldPrice * 2; }
					else if(ProductPricePeriod == 'k'){ OldPrice = OldPrice * 4; }
					else if(ProductPricePeriod == 'm'){ OldPrice = OldPrice * 12; }
					else if(ProductPricePeriod == 'w'){ OldPrice = OldPrice * 52; }
					else if(ProductPricePeriod == 'd'){ OldPrice = OldPrice * 365; }
					break;
				case 'h':
					if(ProductPricePeriod == 't'){ OldPrice = OldPrice / 4; }
					else if(ProductPricePeriod == 'j'){ OldPrice = OldPrice / 2; }
					else if(ProductPricePeriod == 'k'){ OldPrice = OldPrice * 4 / 2 ; }
					else if(ProductPricePeriod == 'm'){ OldPrice = OldPrice * 12 / 2 ; }
					else if(ProductPricePeriod == 'w'){ OldPrice = OldPrice * 52 / 2 ; }
					else if(ProductPricePeriod == 'd'){ OldPrice = OldPrice * 365 / 2 ; }
					break;
				case 'k':
					if(ProductPricePeriod == 't'){ OldPrice = OldPrice / 8; }
					else if(ProductPricePeriod == 'j'){ OldPrice = OldPrice / 4; }
					else if(ProductPricePeriod == 'h'){ OldPrice = OldPrice / 2; }
					else if(ProductPricePeriod == 'm'){ OldPrice = OldPrice * 12 / 4 ; }
					else if(ProductPricePeriod == 'w'){ OldPrice = OldPrice * 52 / 4 ; }
					else if(ProductPricePeriod == 'd'){ OldPrice = OldPrice * 365 / 4 ; }
					break;
				case 'm':
					if(ProductPricePeriod == 't'){ OldPrice = OldPrice / 24; }
					else if(ProductPricePeriod == 'j'){ OldPrice = OldPrice / 12; }
					else if(ProductPricePeriod == 'h'){ OldPrice = OldPrice / 6; }
					else if(ProductPricePeriod == 'k'){ OldPrice = OldPrice / 3; }
					else if(ProductPricePeriod == 'w'){ OldPrice = OldPrice * 52 / 12; }
					else if(ProductPricePeriod == 'd'){ OldPrice = OldPrice * 365 / 12; }
					break;
				case 'w':
					if(ProductPricePeriod == 't'){ OldPrice = OldPrice / 104; }
					else if(ProductPricePeriod == 'j'){ OldPrice = OldPrice / 52; }
					else if(ProductPricePeriod == 'h'){ OldPrice = OldPrice / 26; }
					else if(ProductPricePeriod == 'k'){ OldPrice = OldPrice *4 / 52; }
					else if(ProductPricePeriod == 'm'){ OldPrice = OldPrice * 12 / 52; }
					else if(ProductPricePeriod == 'd'){ OldPrice = OldPrice * 7; }
					break;
				case 'd':
					if(ProductPricePeriod == 't'){ OldPrice = OldPrice / (2 * 365); }
					else if(ProductPricePeriod == 'j'){ OldPrice = OldPrice / 365; }
					else if(ProductPricePeriod == 'h'){ OldPrice = OldPrice * 2 / 365; }
					else if(ProductPricePeriod == 'k'){ OldPrice = OldPrice * 4 / 365; }
					else if(ProductPricePeriod == 'm'){ OldPrice = OldPrice * 12 / 365; }
					else if(ProductPricePeriod == 'w'){ OldPrice = OldPrice / 7 ; }
					break;
			}
			
			var NumberOfDecimals = (formatAsFloat(OldPrice, 2) == formatAsFloat(OldPrice, 4)) ? 2 : 5;
			
			if((LineID == 'subscription' && VAT_CALC_METHOD == 'incl') || (LineID != 'subscription' && $('input[name="VatCalcMethod"]').val() == "incl")){
				$(PriceInclInputObject).val(formatAsMoney(OldPrice,NumberOfDecimals)).change();
				$(PriceExclInputObject).val(formatAsMoney(OldPrice / (1 + ($(TaxInputObject).val() * 1)),NumberOfDecimals)).change();
			}else{
				$(PriceExclInputObject).val(formatAsMoney(OldPrice,NumberOfDecimals)).change();
				$(PriceInclInputObject).val(formatAsMoney(OldPrice * (1 + ($(TaxInputObject).val() * 1)),NumberOfDecimals)).change();
			}
		}
	}
	
	if(LineID != 'subscription' && LineID != 'hostingimport')
	{
		// Update line total
		getLineTotal(LineID);
	}
	return false;
}

function getLineTotal(LineID, DebtorChanged){

	// Calculate overall total
	if($('#formJQ-Taxable').val() == 'true' || ($('input[name="TaxRate1"]').val() != "" && $('input[name="TaxRate1"]').val() > 0)){
		Tax = true;
	}else{
		Tax = false;
	}
	
	if(Tax && $('input[name="VatCalcMethod"]').val() == "incl"){
		$('input[name^="PriceIncl"]').show();
		$('input[name^="PriceExcl"]').hide();
		$('.span_vat_incl').show(); $('.span_vat_excl').hide();
	}else{
		$('input[name^="PriceIncl"]').hide();
		$('input[name^="PriceExcl"]').show();
		$('.span_vat_incl').hide(); $('.span_vat_excl').show();
	}
	
	// Number
	Number = deformatAsMoney(extractNumberAndSuffix($('input[name="Number['+LineID+']"]').val()));
	// PriceExcl
	PriceExcl = deformatAsMoney($('input[name="PriceExcl['+LineID+']"]').val());
	PriceIncl = deformatAsMoney($('input[name="PriceIncl['+LineID+']"]').val());
	// Periods
	Periods = deformatAsMoney($('input[name="Periods['+LineID+']"]').val());
	// Vat
	if(Tax && $('input[name="TaxRate1"]').val() != ""){
		TaxPercentage = $('input[name="TaxRate1"]').val();
	}else if(Tax && $('input[name="TaxPercentage['+LineID+']"]').val() != undefined){
		TaxPercentage = $('input[name="TaxPercentage['+LineID+']"]').val();
	}else{
		TaxPercentage = '0';
	}
	
	if(DebtorChanged === true)
	{
		$('input[name="PriceIncl['+LineID+']"]').val(formatAsMoney(deformatAsMoney($('input[name="PriceExcl['+LineID+']"]').val()) * (1+ 1 * TaxPercentage)));
		PriceIncl = deformatAsMoney($('input[name="PriceIncl['+LineID+']"]').val());
	}
	
		
	// DiscountPercentage
	var tmp_discount_value = deformatAsMoney($('input[name="DiscountPercentage['+LineID+']"]').val())*1;
	// Max 2 decimals
	tmp_discount_value = Math.round(tmp_discount_value * 100) / 100;
	DiscountPercentage = 1-(tmp_discount_value / 100);
	
    var discountPrefix = ' ';    
	if($('input[name="VatCalcMethod"]').val() == "incl" && Tax){
		$('#formJQ-LineTotal-' + LineID).html(formatAsMoney(Number * PriceIncl * Periods));
        var discountPriceIncl = Number * PriceIncl * Periods * (1-DiscountPercentage);        
        if(discountPriceIncl > 0)
        {
			discountPrefix = '- ';
		}        
		$('#formJQ-LineTotal-' + LineID).next('div.discount_helper').find('span').html(discountPrefix + formatAsMoney(Math.abs(discountPriceIncl)));                
	}else{
		$('#formJQ-LineTotal-' + LineID).html(formatAsMoney(Number * PriceExcl * Periods));
        var discountPriceExcl = Number * PriceExcl * Periods * (1-DiscountPercentage);        
        if(discountPriceExcl > 0){
			discountPrefix = '- ';
		}        
		$('#formJQ-LineTotal-' + LineID).next('div.discount_helper').find('span').html(discountPrefix + formatAsMoney((Math.abs(discountPriceExcl))));                
	}
	
	Lines = 1*$('input[name=NumberOfElements]').val();
	TotalExcl = TotalIncl = 0;
    NoDiscountTotalExcl = NoDiscountTotalIncl = 0;
    var TotalPerTaxRate = new Object();
    var input_vat_calc_method = document.querySelector('input[name="VatCalcMethod"]');

	$('.linetax_tr').hide();

    Discount = (deformatAsMoney($('input[name="Discount"]').val()) / 100);

	for(i = 0; i <= Lines; i++){
        var line_tr = document.querySelector('input[name="Number['+i+']"]').closest( 'tr');
        var line_total_span = document.querySelector('#formJQ-LineTotal-' + i);

        if(LineID != i && line_total_span.dataset.TotalExcl != undefined)
        {
            NoDiscountTotalExcl += parseFloat(line_total_span.dataset.TotalExcl);
            NoDiscountTotalIncl += parseFloat(line_total_span.dataset.TotalIncl);

            if((TotalPerTaxRate[line_total_span.dataset.TaxPercentage.replace('.','_')]) == undefined){
                TotalPerTaxRate[line_total_span.dataset.TaxPercentage.replace('.','_')] = 0;
            }
            TotalPerTaxRate[line_total_span.dataset.TaxPercentage.replace('.','_')] += parseFloat(line_total_span.dataset.TaxableAmount);

        }
        else
        {
            if(line_tr.style.display === 'none'){
                delete line_total_span.dataset.TotalExcl;
                delete line_total_span.dataset.TotalIncl;
                delete line_total_span.dataset.TaxPercentage;
                delete line_total_span.dataset.TaxableAmount;
                continue;
            }


            var input_number = line_tr.querySelector('input[name="Number['+i+']"]');
            var input_price_excl = line_tr.querySelector('input[name="PriceExcl['+i+']"]');
            var input_price_incl = line_tr.querySelector('input[name="PriceIncl['+i+']"]');
            var input_periods = line_tr.querySelector('input[name="Periods['+i+']"]');
            var input_taxrate_1 = line_tr.querySelector('input[name="TaxRate1"]');
            var input_taxpercentage = line_tr.querySelector('input[name="TaxPercentage['+i+']"]');
            var input_discount_percentage = line_tr.querySelector('input[name="DiscountPercentage['+i+']"]');

            // Number
            Number = deformatAsMoney(extractNumberAndSuffix(input_number.value));
            // PriceExcl
            PriceExcl = deformatAsMoney(input_price_excl.value);
            PriceIncl = deformatAsMoney(input_price_incl.value);
            // Periods
            Periods = deformatAsMoney(input_periods.value);
            // Vat
            if(Tax && input_taxrate_1 !== null && input_taxrate_1.value != ""){
                TaxPercentage = input_taxrate_1.value;
            }else if(Tax && input_taxpercentage.value != undefined){
                TaxPercentage = input_taxpercentage.value;
            }else{
                TaxPercentage = '0';
            }

            // Discount per line
            var tmp_discount_value = deformatAsMoney(input_discount_percentage.value)*1;
            // Max 2 decimals
            tmp_discount_value = Math.round(tmp_discount_value * 100) / 100;

            DiscountPercentage = (tmp_discount_value / 100);

            var offset = ((Number * PriceExcl * Periods ) >= 0) ? 0.000000001 : -0.000000001;

            // Calculate
            if(input_vat_calc_method.value === "incl" && Tax){
                var LineTotalIncl = Math.round(((Math.round(((Number * PriceIncl * Periods)+offset)*100)/100) - (Math.round(((Number * PriceIncl * Periods * DiscountPercentage)+offset)*100)/100)) * 100) / 100;
                var LineTotalExcl = (LineTotalIncl / (Math.round((1+1*TaxPercentage)*10000) / 10000));
            }else{
                var LineTotalExcl = Math.round(((Math.round(((Number * PriceExcl * Periods)+offset)*100)/100) - (Math.round(((Number * PriceExcl * Periods * DiscountPercentage)+offset)*100)/100)) * 100) / 100;

                var LineTotalIncl = (LineTotalExcl * (Math.round((1+1*TaxPercentage)*10000) / 10000));
            }

            NoDiscountTotalExcl += LineTotalExcl;
            line_total_span.dataset.TotalExcl = LineTotalExcl;
            NoDiscountTotalIncl += LineTotalIncl;
            line_total_span.dataset.TotalIncl = LineTotalIncl;

            // Show corresponding Tax label
            if(input_vat_calc_method.value === "incl" && Tax){

                line_total_span.dataset.TaxPercentage = TaxPercentage;
                line_total_span.dataset.TaxableAmount = LineTotalIncl;

            }else{
                line_total_span.dataset.TaxPercentage = TaxPercentage;
                line_total_span.dataset.TaxableAmount = LineTotalExcl;
            }

            if((TotalPerTaxRate[line_total_span.dataset.TaxPercentage.replace('.','_')]) == undefined){
                TotalPerTaxRate[line_total_span.dataset.TaxPercentage.replace('.','_')] = 0;
            }
            TotalPerTaxRate[line_total_span.dataset.TaxPercentage.replace('.','_')] += parseFloat(line_total_span.dataset.TaxableAmount);
        }
    }

    $.each(TotalPerTaxRate, function(index, taxable_amount){

        var TaxRateLocal = index.replace('_','.') * 1;
        var offset = (taxable_amount > 0) ? 0.000001 : -0.000001;

        if(input_vat_calc_method.value == "incl" && Tax){

            LineAmountIncl = Math.round((taxable_amount - (taxable_amount * Discount)) * 100) / 100;
            TotalPerTaxRate[index] = Math.round(((LineAmountIncl / (1+TaxRateLocal) * TaxRateLocal)+offset) * 100) / 100; // Tax amount will be showed, so round it

            // Sum up for totals
            TotalExcl -= TotalPerTaxRate[index]; // Only contains tax right now
            TotalIncl += LineAmountIncl;
        }
        else
        {
            LineAmountExcl = Math.round((taxable_amount - (taxable_amount * Discount)) * 100) / 100;
            TotalPerTaxRate[index] = Math.round(((LineAmountExcl * TaxRateLocal)+offset) * 100) / 100; // Tax amount will be showed, so round it

            // Sum up for totals
            TotalIncl += TotalPerTaxRate[index]; // Only contains tax right now
            TotalExcl += LineAmountExcl;
        }
    });

    if(input_vat_calc_method.value == "incl" && Tax){
        // Round totals als determine the opposite value of the vat calc method
        TotalIncl = Math.round(TotalIncl * 100)/100;
        TotalExcl = Math.round((TotalExcl + TotalIncl) * 100)/100;

        // Show discount
        $('#total-discount').html(formatAsMoney((NoDiscountTotalIncl - TotalIncl)));
    }
    else
    {
        // Round totals als determine the opposite value of the vat calc method
        TotalExcl 	= Math.round(TotalExcl * 100)/100;
        TotalIncl 	= Math.round((TotalExcl + TotalIncl) * 100) / 100;

        // Show discount
        $('#total-discount').html(formatAsMoney((NoDiscountTotalExcl - TotalExcl)));
    }

    // Show tax labels
    $.each(TotalPerTaxRate, function(index, element){
        if(index != 0)
        {
            $('#total-tax-' + index).html(formatAsMoney(element)).parent().show();
        }
    });

    $('#total-excl').html(formatAsMoney(TotalExcl));

    if($('input[name="TaxRate"]').val() > 0 || $('input[name="TaxRate2"]').val() != '')
    {
        var TotalTax = 0;
        var tmp_TaxRate = 0;

        if($('input[name="TaxRate2"]').val() != ''){
            tmp_TaxRate = $('input[name="TaxRate2"]').val();
        }else{
            tmp_TaxRate = $('input[name="TaxRate"]').val();
        }

        if($('input[name="TaxCompound['+tmp_TaxRate+']"]').val() == 'yes'){
            TotalTax = TotalIncl * tmp_TaxRate;
        }else{
            TotalTax = TotalExcl * tmp_TaxRate;
        }

        $('#total-tax-level2').html(formatAsMoney(TotalTax));
        TotalIncl = Math.round((TotalIncl + TotalTax)*100)/100;
    }
    else
    {
        $('#total-tax-level2').html(formatAsMoney(0));
    }

    $('#total-incl').html(formatAsMoney(TotalIncl));
}

/**
 * JS htmlspecialchars function
 */
 
function checkInvoicelineForShiftTax(){
	vatshifthelper = true;
	
	if($('#formJQ-Taxable').val() == "true")
	{
		InvoiceLines = parseInt($('input[name=NumberOfElements]').val());
		for(x = 0; x <= InvoiceLines; x++){
			// Check if the invoiceline is visible and tax is > 0
			if($('#formJQ-TaxPercentage-'+x).closest('tr').is(':visible') && parseFloat($('#formJQ-TaxPercentage-'+x).val()) > 0){
				vatshifthelper = false;
			}
		}
	}else if($('input[name="TaxRate1"]').val() != '' && $('input[name="TaxRate1"]').val() != 0){
		vatshifthelper = false;
	}

	if($('input[name="TaxRate2"]').val() != '')
	{
		if($('input[name="TaxRate2"]').val() > 0)
		{
			vatshifthelper = false;
		}
	}
	else if($('input[name="TotalTaxRadio"]:checked').val() > 0){
		vatshifthelper = false;
	}
	
	if(vatshifthelper){
		$('#shiftvat_div').show();
		$('input[name="VatShift_helper"]').val('true');
	}else{
		$('#shiftvat_div').hide();
		$('input[name="VatShift_helper"]').val('');
	}
}

function htmlspecialchars(str) {
	if (typeof(str) == "string") {
		str = str.replace(/&/g, "&amp;"); // must do &amp; first
		str = str.replace(/"/g, "&quot;");
		str = str.replace(/'/g, "&#039;");
		str = str.replace(/</g, "&lt;");
		str = str.replace(/>/g, "&gt;");
	}
	return str;
}
function htmlspecialchars_decode(str) {
	if (typeof(str) == "string") {
		str = str.replace(/&amp;/g, "&"); // must do &amp; first
		str = str.replace(/&quot;/g, '"');
		str = str.replace(/&#039;/g, "'");
		str = str.replace(/&lt;/g, "<");
		str = str.replace(/&gt;/g, ">");
	}
	return str;
}

/**
 * Table reloads
 */
function save(arg1, arg2, arg3, redirect){

	$.post("ajax.php", { arg1: arg1, arg2: arg2, arg3: arg3 }, function(data){
		if(redirect){
			location.href=redirect;
		}else if(EventTarget != null){
			location.href=EventTarget;
		} 
	}, "html");
}

function ajaxSave(arg1, arg2, arg3, subtable, page_url){
	$.post("ajax.php", { arg1: arg1, arg2: arg2, arg3: arg3 }, function(data){
		if(subtable){
			loadPage(subtable,page_url, 1);
		}
	}, "html");
}

function loadPage(tableID, pageURL, page, results_per_page){

	$('#SubTable_' + tableID).load(pageURL + ' #MainTable_'+tableID, {ajaxPage: page, ajaxResultsPerPage: results_per_page, tableID: tableID}, function(responseText, textStatus, XMLHttpRequest){ 
		subtableLoaded(); 
		if($('.pagetotals').html() != null){
			$('.pagetotals').html($(responseText).find('.pagetotals').html());
		}
	});
	
	
}

function subtableLoaded(){
	if($('input[name=Groups]').val() != null ){
		$('.GroupBatch').each(function(){
			if($('input[name=Groups]').val().indexOf(',' + $(this).val() + ',') >= 0){
				$(this).prop('checked',true);
			} 			
		});
	}	
}

// Helpful functions
function popUp(URL){
	day=new Date();
	id=day.getTime();
	eval("page"+id+" = window.open(URL, '"+id+"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=500,height=500,left = 490,top = 262');");
}

function str_replace(Search, Replace, subject) {
    return String(subject).split(String(Search)).join(String(Replace));
}

function strrpos (haystack, needle, offset) {
    var i = -1;
    if (offset) {
        i = (haystack+'').slice(offset).lastIndexOf(needle); // strrpos' offset indicates starting point of range till end,        // while lastIndexOf's optional 2nd argument indicates ending point of range from the beginning
        if (i !== -1) {
            i += offset;
        }
    }    else {
        i = (haystack+'').lastIndexOf(needle);
    }
    return i >= 0 ? i : false;
}

// 
function changePeriodCalc(Periodic, Periods, CurStartDate) {
	var StartDate = new Date();
	
	if(CurStartDate){
        CurStartDate = rewrite_date_site2db(CurStartDate);
    }else{
        day = (StartDate.getDate()) + "";
		month = (StartDate.getMonth()+1) + "";
		year = (StartDate.getFullYear());
		while(month.length < 2){month = "0" + month;}
        while(day.length < 2){day = "0" + day;}	
        CurStartDate = year + "" + month + "" + day;
    }

	if(CurStartDate){
		var day = CurStartDate.substr(6,2);
		if(day.substr(0,1) == "0"){
			day = day.substr(1);
		}
		day = parseInt(day);

		var month = CurStartDate.substr(4,2);
		if(month.substr(0,1) == "0"){
			month = month.substr(1);
		}
		month = parseInt(month)-1;	
		var year = parseInt(CurStartDate.substr(0,4));

        StartDate.setDate(1);
		StartDate.setFullYear(year);
		StartDate.setMonth(month);
  	
  	    month2 =(month+1)+""; day2 = day+""; year2 = year+"";

      	while(month2.length < 2){month2 = "0" + month2;}
        while(day2.length < 2){day2 = "0" + day2; }
      	
		switch(Periodic){
            case '': var x = new Array(); x[0] = ""; x[1] = ""; return x; break;
			case 'd': var str = StartDate.setDate(day + parseInt(Periods)); break;
			case 'w': var str = StartDate.setDate(day + 7*parseInt(Periods)); break;
			case 'm': var str = StartDate.setMonth(month + parseInt(Periods)); break;
			case 'k': var str = StartDate.setMonth(month + 3*parseInt(Periods)); break;
			case 'h': var str = StartDate.setMonth(month + 6*parseInt(Periods)); break;
			case 'j': var str = StartDate.setFullYear(year + parseInt(Periods)); break;
			case 't': var str = StartDate.setFullYear(year + 2*parseInt(Periods)); break;
		}
		
		if(Periodic != 'd' && Periodic != 'w')
		{
			var daysInMonth = getDaysInMonth(StartDate.getFullYear(), StartDate.getMonth()+1);
			if(daysInMonth < day)     
			{
				StartDate.setDate(daysInMonth);	
			}
			else
			{
				StartDate.setDate(day);	
			}
		} 
		 
	}

				
    day = (StartDate.getDate()) + "";
	month = (StartDate.getMonth()+1) + "";
	year = (StartDate.getFullYear());
	while(month.length < 2){month = "0" + month;}
    while(day.length < 2){day = "0" + day;}	

    var result = new Array();
    result[0] = rewrite_date_db2site(year2,month2,day2);
    result[1] =  rewrite_date_db2site(year,month,day); 
    return result;
}
// timechecker
function timechecker(time_string){
	if(time_string.indexOf(':') != -1){
		time_string = time_string.replace(':','');
	}else if(time_string.indexOf('.') != -1){
		time_string = time_string.replace('.','');
	}else if(time_string.indexOf(',') != -1){
		time_string = time_string.replace(',','');
	}

	// Catch: (9, 14, 930, 1400 )
	if(time_string.length == 1 || (time_string.length == 2 && time_string <= 24) && !isNaN(time_string)){
		if(time_string.length == 1){
			time_string = '0' + time_string + ':00';
		}else{
			time_string = time_string + ':00';
		}
	}else if(time_string.length == 3 && !isNaN(time_string)){
		time_string = '0' + time_string[0] + ':' + time_string[1] + time_string[2];
	}else if(time_string.length == 4 && (time_string[0] <= 2 && (time_string[0] < 2 || time_string[1] <= 4) && time_string[2] <= 5) && !isNaN(time_string)){
		time_string = time_string[0] + time_string[1] + ':' + time_string[2] + time_string[3];
	}else{
		time_string = '';
	}
	return time_string;
}

function EndPeriod(CurStartDate, Periodic, Periods){
		
		var StartDate = new Date();
		
		if(CurStartDate)
            CurStartDate = rewrite_date_site2db(CurStartDate);
        else{
            day = (StartDate.getDate()) + "";
    		month = (StartDate.getMonth()+1) + "";
    		year = (StartDate.getFullYear());
    		while(month.length < 2){month = "0" + month;}
            while(day.length < 2){day = "0" + day;}	
            CurStartDate = year + "" + month + "" + day;
        }

		if(CurStartDate){
			var day = CurStartDate.substr(6,2);
			if(day.substr(0,1) == "0"){
				day = day.substr(1);
			}
			day = parseInt(day);

			var month = CurStartDate.substr(4,2);
			if(month.substr(0,1) == "0"){
				month = month.substr(1);
			}
			month = parseInt(month)-1;	
			var year = parseInt(CurStartDate.substr(0,4));

            StartDate.setDate(1);
			StartDate.setFullYear(year);
			StartDate.setMonth(month);
            StartDate.setDate(day);
      	
      	    month2 =(month+1)+"";day2 = day+"";

          	while(month2.length < 2){month2 = "0" + month2;}
            while(day2.length < 2){day2 = "0" + day2; }
                      	
			switch(Periodic){
				case 'd': var str = StartDate.setDate(day + parseInt(Periods)); break;
				case 'w': var str = StartDate.setDate(day + 7*parseInt(Periods)); break;
				case 'm': var str = StartDate.setMonth(month + parseInt(Periods)); break;
				case 'k': var str = StartDate.setMonth(month + 3*parseInt(Periods)); break;
				case 'h': var str = StartDate.setMonth(month + 6*parseInt(Periods)); break;
				case 'j':case 'y': var str = StartDate.setFullYear(year + parseInt(Periods)); break;
				case 't': var str = StartDate.setFullYear(year + 2*parseInt(Periods)); break;
			} 
		}

					
        day = (StartDate.getDate()) + "";
		month = (StartDate.getMonth()+1) + "";
		year = (StartDate.getFullYear());
		while(month.length < 2){month = "0" + month;}
        while(day.length < 2){day = "0" + day;}	
		
        return rewrite_date_db2site(year,month,day);
}

function deleteFile(){
	$.post("XMLRequest.php", { action: 'delete_session_file'}, function(data){
	}, "json");
}

// Create random password
function generatePassword(html_element) { 
    $.post('XMLRequest.php', { action: 'generate_hosting_password'}, function(data){
        html_element.val(data);
	},"html");     
}

// Load packages
function getServerPackages(server_id, type, also_existing)
{   
	// Skip function if not needed
	if(server_id == undefined || server_id == 0){
		return true;
	}
	
	$('select[name=TemplateName]').hide();
	$('#hosting_div_template').html('<img src="images/indicator.gif" alt="" />').show();
	
	$.post("XMLRequest.php", { action: "package_list", id: server_id, type: type, also_existing: also_existing},
		function(data){
			
			if(also_existing == true && data.existing != undefined && data.existing.length > 0){
				var options = '<optgroup label="'+__LANG_PACKAGE_GROUP_IN_SOFTWARE+'">';
				for (var i = 0; i < data.existing.length; i++) {
					options += '<option value="ex:' + data.existing[i][0] + '">' + data.existing[i][1] + '</option>';
				}
				
				options += '</optgroup><optgroup label="'+__LANG_PACKAGE_GROUP_ON_SERVER+'">';
				if(data.newtemplate.resultSet != undefined){
	                for (var i = 0; i < data.newtemplate.resultSet.length; i++) {
	                  options += '<option value="' + data.newtemplate.resultSet[i] + '">' + data.newtemplate.resultSet[i] + '</option>';
	                }
                }
                options += '</optgroup>';
            
            	$('select[name=TemplateName]').html(options).show();
				$('#hosting_div_template').hide();
				
				// If a templatename exists, fill
            	if($('input[type=hidden][name="TemplateNameHidden"]').val()){
            		$('select[name=TemplateName]').val($('input[type=hidden][name="TemplateNameHidden"]').val()).change();
            	}else{
            		$('select[name=TemplateName]').change();
            	}							
			}else{
				// If we also want existing packages, but they don't exist
				if(also_existing == true){
					data = data.newtemplate;
				}
				
				// Only new elements
				if(data.resultSet == undefined && data.errorSet != undefined) {
					
					if(data.errorSet[0].nopanel == true){
						$('#hosting_div_template').html(data.errorSet[0].Message).show();	
						$('select[name=TemplateName]').html('').hide();
						
						$('#properties_hosting_package_none').hide();
						$('#properties_hosting_package_custom').show();
						$('#properties_hosting_package').hide();
						
					}else{
						$('#hosting_div_template').html(data.errorSet[0].Message).show();				
						$('select[name=TemplateName]').html('').hide();
						
						$('#properties_hosting_package_none').show();
						$('#properties_hosting_package_custom').hide();
						$('#properties_hosting_package').hide();
					}					
				}else if(data.errorSet == undefined){
	                var options = '';
	                for (var i = 0; i < data.resultSet.length; i++) {
	                  options += '<option value="' + data.resultSet[i] + '">' + data.resultSet[i] + '</option>';
	                }
	            
	            	$('select[name=TemplateName]').html(options).show();
	            	$('#hosting_div_template').hide();
	            	
	            	// If a templatename exists, fill
	            	if($('input[type=hidden][name="TemplateNameHidden"]').val()){
	            		$('select[name=TemplateName]').val($('input[type=hidden][name="TemplateNameHidden"]').val()).change();
	            	}else{
	            		$('select[name=TemplateName]').change();
	            	}
					
				}
			}
	}, "json");
}

function checkURL(url, id){
	
	$('#' + id).html('<img src="images/icon_circle_loader_grey.gif" style="margin:6px 0 6px 6px;" />');
	
	// For online payment URL, do other kind of check
	if(id == 'ideal-email-url-img'){
		$.post("XMLRequest.php", { action: 'check_ideal_email', url: url },function(data){
			if(data != 'OK'){
				$('#' + id).html('<span class="loading_red">' + __LANG_URL_NOT_VALID + '</span>');
			}else{
				$('#' + id).html('<img src="images/ico_check.png" style="margin:6px 0 6px 6px;" />');
			}
		});
		return;
	}
	
	$.post("XMLRequest.php", { action: 'check_url', url: url },function(data){	
		
		if(data.content_type != null){		
			switch(id){
				case 'customerpanel-logo-url-img':
					if(data.content_type.indexOf('image') !== -1){
						if(url.indexOf('http') == -1){
							url = 'http://' + url;
                            if($('input[name=CLIENTAREA_LOGO_URL]').length > 0)
                            {
                                $('input[name=CLIENTAREA_LOGO_URL]').val(url);
                            }
						}
						$('#' + id).html('<img src="images/ico_check.png" style="margin:6px 0 6px 6px;" />');
	
						$('#customerpanel_logo_helper_div').show();
						$('#customerpanel_logo_helper_img').attr('src',url);
					}else{
						$('#customerpanel_logo_helper_div').hide();
						$('#' + id).html('<span class="loading_red">' + __LANG_URL_IS_NOT_AN_IMAGE + '</span>');
					}
					break;
				case 'customerpanel-terms-url-img':
					if(data.http_code != '404'){
						$('#' + id).html('<img src="images/ico_check.png" style="margin:6px 0 6px 6px;" />');
					}else{
						$('#' + id).html('<span class="loading_red">' + __LANG_URL_NOT_VALID + '</span>');
					}
					break;
				case 'orderform-terms-url-img':
					if(data.http_code != '404'){
						$('#' + id).html('<img src="images/ico_check.png" style="margin:6px 0 6px 6px;" />');
					}else{
						$('#' + id).html('<span class="loading_red">' + __LANG_URL_NOT_VALID + '</span>');
					}
					break;
				case 'backoffice-url-img':
				case 'clientarea-url-img':
				case 'orderform-url-img':
				case 'whoisform-result-url':
					if(data.http_code != '404'){
						$('#' + id).html('<img src="images/ico_check.png" style="margin:6px 0 6px 6px;" />');
					}else{
						$('#' + id).html('<span class="loading_red">' + __LANG_URL_NOT_VALID + '</span>');
					}
					break;
			}
		}else{
			$('#' + id).html('<span class="loading_red">' + __LANG_URL_NOT_VALID + '</span>');
			// Hide logo if invalid url
			if(id == 'customerpanel-logo-url-img'){
				$('#customerpanel_logo_helper_div').hide();
			}
		}
	},'json');
}

function vat_checker(vat_number, country_code, object){
	if(vat_number != ''){
	
		$.post('XMLRequest.php', { action: 'vat_check', vat: vat_number, cc: country_code }, function(data){
			if(data == 'OK'){
				$(object).html('<span class="loading_green">' + __LANG_VAT_NUMBER_IS_VALID + '</span>');
			}else if(data == 'BAD'){
				$(object).html('<span class="loading_red">' + __LANG_VAT_NUMBER_IS_NOT_VALID + '</span>');
			}else if(data == 'UNAVAILABLE'){
				$(object).html('<span class="loading_orange">' + __LANG_CHECK_TEMPORARILY_UNAVAILABLE + '</span>');
			}else{
				$(object).html('');
			}
		});
		
		return '<img src="images/icon_circle_loader_grey.gif" style="margin:6px 0 6px 6px;" />';
	}else{
		return '';
	}
}

function get_uploaded_creditinvoice_file(){

	$.post("XMLRequest.php", { action: 'get_uploaded_files' }, function(data){
		if(data[0].FileName != undefined)
        {
            // redirect if user tries to upload an UBL file on the add creditinvoice page
            if(data[0].FileType == 'creditinvoice' && data[0].FileExtension == 'xml' && data[0].redirect != undefined && $('input[name="id"]').val() == '')
            {
                location.href = data[0].redirect;
            }

			$('input[name="File[]"]').val(data[0].FilePath);
			$('#creditinvoice_file').html('<div class="file ico inline file_' + getFileType(data[0].FileName) + '">&nbsp;</div>' + data[0].FileName);
			
			$('#creditinvoice_file_link').show();
		}
	}, "json");	
}

function get_uploaded_files(from_file_type){
	$.post("XMLRequest.php", { action: 'get_uploaded_files' }, function(data){
		if(data.length){
			
			$.each(data, function(key,value){
				if($('#files_list_ul').html() != null){
					$('#files_list_ul').append('<li><input type="hidden" name="File[]" value="' + value.FilePath + '" /><div class="delete_cross not_visible file_delete">&nbsp;</div><div class="file ico inline file_' + getFileType(value.FileName) + '">&nbsp;</div>' + value.FileName + '<div class="filesize">' + value.FileSize + ' ' + value.FileSizeUnit + '</div></li>');
				}
				
				if($('.files_select').html() != null){
					if((from_file_type == 'pdf_files') && (value.FileName.indexOf('.pdf') < 0))
					{
						// Invalid filetype to select
					}
					else
					{
						$('.files_select').append($('<option></option>').attr('value',value.FilePath).text(value.FileName));
					}
				}

                if($('.block_value_img').html() != null)
                {
                    if((from_file_type == 'template_image_files') && (value.FileName.toLowerCase().indexOf('.jpg') < 0) && (value.FileName.toLowerCase().indexOf('.gif') < 0) && (value.FileName.toLowerCase().indexOf('.png') < 0))
                    {
                        // Invalid filetype to select
                    }
                    else
                    {
                        $('.block_value_img input[name="block_value"]').val(value.FilePath);
                        $('#image_helper_img').attr('src', value.FilePath + '?rand=' + Math.random());
                        $('#image_helper_div').show();
                    }
                }
			});
			
			if($('#files_list_ul').html != null){
				$('#files_list').show();
				$('#files_none').hide();
				calc_files_total();
			}
			
		}
	}, "json");
}

function calc_files_total(){
	$.post("XMLRequest.php", { action: 'get_files_total', files: $('input[name="File[]"]').serialize() }, function(data){
		$('#files_total').html(data.Counter + ' (' + data.FileSize + ' ' + data.FileSizeUnit + ')');
		
		if(data.Counter > 0){
			$('#files_list').show();
			$('#files_none').hide();
		}
		
	}, "json");
}

function getFileType(filename){
	var array_filetypes = ['bmp','doc','gif','jpg','pdf','png','ppt','rar','txt','xls','zip'];

	parts = filename.split('.');
	filetype = parts[parts.length - 1].toLowerCase();
	
	if($.inArray(filetype,array_filetypes) != -1){
		return filetype;
	}else{
		return 'unknown';
	}
}

/* jquery autogrowtextarea plugin */
jQuery.fn.autoGrow = function(){
    return this.each(function(){

        //Functions
        var keyPress = function(event) {

            // reset height for delete and backspace key
            if([8, 46].indexOf(event.keyCode) != -1)
            {
                // Fix scolling issues.
                var top  = window.pageYOffset || document.documentElement.scrollTop;
                var left  = window.pageXOffset || document.documentElement.scrollLeft;
                $(this).css('height', '0px');

                $(this).css('height', $(this).get(0).scrollHeight + 'px');

                window.scrollTo(left, top);
            } else {
                $(this).css('height', $(this).get(0).scrollHeight + 'px');
            }
        }

        // Initial settings
        var init = function(element) {

            var height =  $(element).get(0).scrollHeight;

            if(height == undefined || height == 0)
            {
                // Clone to a visible part, for retrieving the real scrollheight
                var ClonedElement = $(element).clone().appendTo('body');
                height = $(ClonedElement).get(0).scrollHeight;
                $(ClonedElement).remove();

                if(height == undefined || height == 0)
                {
                    height = $(element).height();
                }
            }

            $(element).css('height', height + 'px');
        }

        $(this).css('resize', 'none');
        $(this).css('overflow-y', 'hidden');
        $(this).css('height', '30px');
        $(this).attr('row', '1');

        // Bind events
        this.onkeyup = keyPress;
        this.onfocus = keyPress;
        this.onblur = keyPress;
        this.onchange = keyPress;

        var element = this;
        init(element);
    });
};

function show_kb_articles(){
	$('#kb_scroll2').hide();
	$('#kb_scroll').show();
	$('#kb_results').html('<center><strong>'+__LANG_KB_PLEASE_WAIT+'</strong></center><div class="hr"></div><center><br /><img src="images/loadinfo.gif" alt=""/></center>').show();
	
	
	$.post('XMLRequest.php', { action: 'kb_get_articles', article_search: $('input[name="kb_search"]').val(), page1: $('input[name="kb_page1"]').val(), page2: $('input[name="kb_page2"]').val()},function(data){
        // Detect new CSRF token, then do not parse response.
        if (detectAjaxCsrfResponse(data)) {
            $('#kb_results').html('');
            return;
        }

		$('#kb_results').html(data);
		$('#kb_scroll').tinyscrollbar_update();	
	});
}

function show_kb_article(article_id){
	$('#kb_scroll').hide();
	$('#kb_scroll2').show();
	$('#kb_article').html('<center><strong onclick="$(\'#kb_scroll\').show();$(\'#kb_scroll2\').hide();">'+__LANG_KB_BACK_TO_RESULTS+'</strong></center><div class="hr"></div><center><br /><img src="images/loadinfo.gif" alt=""/></center>').show();

	$.post('XMLRequest.php', { action: 'kb_get_article', article_id: article_id},function(data){
        // Detect new CSRF token, then do not parse response.
        if (detectAjaxCsrfResponse(data)) {
            return;
        }

		$('#kb_article').html(data);
		setTimeout(function(){$('#kb_scroll2').tinyscrollbar_update(); },1000);		
		
		if($('#kb_article a.fancybox').html() != null){
			$("#kb_article a.fancybox").fancybox();
		}
		if($('#kb_article a.fancybox_video').html() != null){
			$("#kb_article a.fancybox_video").click(function() { 
				$.fancybox({'href': this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),'type': 'swf','swf': {'wmode': 'transparent','allowfullscreen': 'true'}});
				return false;
			});
		}
	});
}

function getCountryStates(countrycode, prefixName, value){
	// If no country is selected
	if(countrycode == '')
	{
		$('input[name="'+prefixName+'State"]').show();
		$('select[name="'+prefixName+'StateCode"]').val('').hide();
		return false;
	}
	
	$.post("XMLRequest.php", { action: "get_states", countrycode: countrycode},function(data){
			if(data.type == 'select')
			{
				$('input[name="'+prefixName+'State"]').val('').hide();
				$('select[name="'+prefixName+'StateCode"]').html(data.options).show();
				$('select[name="'+prefixName+'StateCode"]').val(value);
			}	
			else
			{
				$('input[name="'+prefixName+'State"]').val(value).show();
				$('select[name="'+prefixName+'StateCode"]').html('').val('').hide();
			}
	},'json');
}

function loadCustomClientFields(customfields_list, object_array, show_label, form_id, prefix){
	$(customfields_list).each(function(custom_field_index, custom_field){
		// Update label
		if(show_label == true)
		{
			if(object_array.custom[custom_field.FieldCode])
			{
				$('#label_'+prefix+'_'+custom_field.FieldCode).html(htmlspecialchars(object_array.custom[custom_field.FieldCode]).replace(/\n/g,'<br />'));
			}
			else
			{
				$('#label_'+prefix+'_'+custom_field.FieldCode).html('-');
			}
		}
		
		// Update form-element
		if(!object_array.customvalues[custom_field.FieldCode])
		{
			object_array.customvalues[custom_field.FieldCode] = '';
		}
		
		switch(custom_field.LabelType)
		{
			case 'date':
				$('#'+form_id+' input[name="'+prefix+'custom['+custom_field.FieldCode+']"]').val(object_array.custom[custom_field.FieldCode]);
				break;
			case 'input':
				$('#'+form_id+' input[name="'+prefix+'custom['+custom_field.FieldCode+']"]').val(object_array.customvalues[custom_field.FieldCode]);
				break;
			case 'textarea':
				$('#'+form_id+' textarea[name="'+prefix+'custom['+custom_field.FieldCode+']"]').val(object_array.customvalues[custom_field.FieldCode]);
				break;
			case 'select':
				$('#'+form_id+' select[name="'+prefix+'custom['+custom_field.FieldCode+']"]').val(object_array.customvalues[custom_field.FieldCode]);
				break;
			case 'radio':
				$('#'+form_id+' input[name="'+prefix+'custom['+custom_field.FieldCode+']"][value="'+object_array.customvalues[custom_field.FieldCode]+'"]').click();
				break;
			case 'checkbox':
				$('#'+form_id+' input[name="'+prefix+'custom['+custom_field.FieldCode+'][]"]').prop('checked',false);
				$($.parseJSON(object_array.customvalues[custom_field.FieldCode])).each(function(option_key, option_val){
					$('#'+form_id+' input[name="'+prefix+'custom['+custom_field.FieldCode+'][]"][value="'+option_val+'"]').prop('checked',true);
				});
				break;
		}
		
	});
}

function extractNumberAndSuffix(Number)
{
	if(Number == undefined){ return ''; }
	returnNumber = Number.match(/^[\+\-|]?(\s)?([0-9]+[\.\,\s|]?)*([0-9]*)?/);
	if(returnNumber != null){
		return returnNumber[0].replace(' ','');
	}else{
		return '';
	}
}

// Custom table actions
function wf_table_sort_by(table_id, sort_by, parameters, callback_function)
{
	$('#SubTable_' + table_id).load('modules.php?page=ajax #MainTable_'+table_id, {
		action:'table_reload',
		table_id: table_id,
		sort_by: sort_by,
		parameters: parameters, 
	}, function(){ 
		if($('#MainTable_' + table_id).attr('data-total-placeholder') != undefined){ $('#' + 	$('#MainTable_' + table_id).attr('data-total-placeholder')).html($('#MainTable_' + table_id).attr('data-total-results')).show(); }
        if($('#MainTable_' + table_id).attr('data-total-text-placeholder') != undefined){ $('#' + 	$('#MainTable_' + table_id).attr('data-total-text-placeholder')).html($('#MainTable_' + table_id).attr('data-total-text-results')).show(); }
		if(callback_function != undefined && typeof window[callback_function] == 'function'){ window[callback_function](); }
	 });
}

function wf_table_filter(table_id, filter, parameters, callback_function)
{
	$('#SubTable_' + table_id).load('modules.php?page=ajax #MainTable_'+table_id, {
		action:'table_reload',
		table_id: table_id,
		filter: filter,
		parameters: parameters
	}, function(){ 
		if($('#MainTable_' + table_id).attr('data-total-placeholder') != undefined){$('#' + 	$('#MainTable_' + table_id).attr('data-total-placeholder')).html($('#MainTable_' + table_id).attr('data-total-results')).show(); }
        if($('#MainTable_' + table_id).attr('data-total-text-placeholder') != undefined){$('#' + 	$('#MainTable_' + table_id).attr('data-total-text-placeholder')).html($('#MainTable_' + table_id).attr('data-total-text-results')).show(); }
		if(callback_function != undefined && typeof window[callback_function] == 'function'){ window[callback_function](); } 
	});
}

function wf_table_pagination(table_id, page_number, results_per_page, parameters, callback_function){

	$('#SubTable_' + table_id).load('modules.php?page=ajax #MainTable_'+table_id, {
		action:'table_reload',
		table_id: table_id,
		page_number: page_number,
		results_per_page: results_per_page,  
		parameters: parameters, 
	}, function(){ 
		if($('#MainTable_' + table_id).attr('data-total-placeholder') != undefined){ $('#' + 	$('#MainTable_' + table_id).attr('data-total-placeholder')).html($('#MainTable_' + table_id).attr('data-total-results')).show(); }
        if($('#MainTable_' + table_id).attr('data-total-text-placeholder') != undefined){ $('#' + 	$('#MainTable_' + table_id).attr('data-total-text-placeholder')).html($('#MainTable_' + table_id).attr('data-total-text-results')).show(); }
		if(callback_function != undefined && typeof window[callback_function] == 'function'){ window[callback_function](); }
	});
}

function getDaysFromPeriod(startdate, enddate)
{
    var start_date_object   = new Date(startdate); // "7/11/2010"
    var end_date_object     = new Date(enddate); // "12/12/2010"
    var MS_PER_DAY         = 1000 * 60 * 60 * 24;
    
    // Discard the time and time-zone information.
    var utc1 = Date.UTC(start_date_object.getFullYear(), start_date_object.getMonth(), start_date_object.getDate());
    var utc2 = Date.UTC(end_date_object.getFullYear(), end_date_object.getMonth(), end_date_object.getDate());
    
    return Math.floor((utc2 - utc1) / MS_PER_DAY);
}

function vat(Amount)
{
	if(strrpos(Amount, '.') > strrpos(Amount, ',')){
		return str_replace('.',',',str_replace(',','',Amount)) 
	}else{
		return str_replace('.','',Amount);
	}
}

function createAutoCompleteChangeListener(element)
{
	// Create listener for other fields changing this item	
    $(document).on('change', 'input[name="' + $(element).data('inputfieldname') + '"]', function(){
		if($('.autocomplete_search').is(':visible') == false)
		{
			CurrentAutoCompleteSelector = $('input[name="AutoCompleteSearch[]"][data-inputfieldname="' + $(this).attr('name') + '"]');
			var selected_value = $(this).val();
			
			if(AutoCompleteData[$(CurrentAutoCompleteSelector).data('type')] == undefined)
			{
				$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_arrow').hide();
				$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_loader').show();
				AutoCompleteIsLoading = true;
				$.post("XMLRequest.php", { action: 'autocomplete_search', search_type: $(CurrentAutoCompleteSelector).data('type'), search_for: '', filter: $(CurrentAutoCompleteSelector).data('filter') }, function(data)
				{
					$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_arrow').show();
					$(CurrentAutoCompleteSelector).parent('.autocomplete_search_input').find('.autocomplete_search_input_loader').hide();
                    AutoCompleteIsLoading = false;

                    if (data.ajaxResponse !== undefined && data.ajaxResponse === 'csrf') {
                        // CRSF token will be set again on higher level.
                        CurrentAutoCompleteSelector = null;
                        return;
                    }

                    AutoCompleteData[data.search_type] = data;
					
					// Select the product
					selectAutoCompleteItem($(CurrentAutoCompleteSelector).data('type'), selected_value, selected_value, false, false);
				}, "json");
			}
			else
			{
				selectAutoCompleteItem($(CurrentAutoCompleteSelector).data('type'), selected_value, selected_value, false, false);
			}
		}
	});
}

function selectAutoCompleteItem(search_type, selected_id, selected_code, selected_text, execute_change)
{
	
	if($('#div_for_autocomplete_' + search_type).data('return') == 'id'){
		SelectedValue = selected_id;
	}else{
		SelectedValue = selected_code;
	}
	
	if(!SelectedValue)
	{
		return;
	}
	
	if(CurrentAutoCompleteSelector != null)
	{
		if(selected_text == false && AutoCompleteData[search_type] != undefined && AutoCompleteData[search_type] != undefined)
		{
			$(AutoCompleteData[search_type].search_results).each(function(index,sub_element){
				if(	($('#div_for_autocomplete_' + search_type).data('return') == 'id' && sub_element.id == SelectedValue) ||
					($('#div_for_autocomplete_' + search_type).data('return') == 'code' && sub_element.code == SelectedValue))
				{
					$(CurrentAutoCompleteSelector).val(sub_element.code + ' ' + sub_element.title);
					$(CurrentAutoCompleteSelector).data('label',sub_element.code + ' ' + sub_element.title);
					return true;
				}
			});
		}
		else
		{
			$(CurrentAutoCompleteSelector).val(selected_text);
			$(CurrentAutoCompleteSelector).data('label',selected_text);
		}
		
		// Change hidden input
		$('input[name="'+$(CurrentAutoCompleteSelector).data('inputfieldname')+'"]').val(SelectedValue);
		if(execute_change == undefined || execute_change == true)
		{
			$('input[name="'+$(CurrentAutoCompleteSelector).data('inputfieldname')+'"]').change();
		}
		
		CurrentAutoCompleteSelector = null;
	}
	
	$('#div_for_autocomplete_' + search_type).hide();
}

function showAutoCompleteDiv(search_type)
{
	$('.autocomplete_header .toptext').hide();
	var search_at = '';
	if(CurrentAutoCompleteSelector != null)
	{
		search_at = $(CurrentAutoCompleteSelector).val().toLowerCase();
	}
	
	$('#div_for_autocomplete_' + search_type).find('.autocomplete_search_results').html('');
	var group_filter = $('#div_for_autocomplete_' + search_type).data('group_filter');
	
	if(group_filter == 'yes')
	{
		var HTML = '<ul class="autocomplete_search_results_groups animation-left-right">';
		var HTML2 = '';
		
		// Do we have a limited set of products?
		if(search_at)
		{
			var FilteredProductIDs = new Array();
			$(AutoCompleteData[search_type].search_results).each(function(index,sub_element){
				if(search_at && searchAutoCompleteElement(sub_element.searchable, search_at))
				{
					FilteredProductIDs.push(parseInt(sub_element.id));
				}
			});
		}
		
		// Filtered by group
		$(AutoCompleteData[search_type].search_groups).each(function(index,element){
			var ProductCounter = element.children.length;
			if(search_at)
			{
				ProductCounter = 0;
				$(element.children).each(function(index,group_element)
				{
					if($.inArray(parseInt(group_element), FilteredProductIDs) > -1)
					{
						ProductCounter++;
					}
					
					if(ProductCounter > AutoCompleteMaxResults)
					{
						return false;
					}
				});
			}
			
			if(ProductCounter > AutoCompleteMaxResults)
			{
				ProductCounter = AutoCompleteMaxResults + '+';
			}
			
			if(!search_at || ProductCounter != 0)
			{
				HTML2 += '<li data-groupid="' + element.id  + '"><i></i><span class="autocomplete_li_span">'  + htmlspecialchars(element.title) + ' (' +  ProductCounter +  ')</span><br style="clear:both;" /></li>';	
			}		
		});
		
		if(HTML2 == '')
		{
			HTML += '<li class="noHover"><span style="font-style:italic;">' + __LANG_NO_RESULTS_FOUND + '</span></li>';	
		}
		else
		{
			HTML += HTML2;
		}
		
		$('.autocomplete_header .group_label').show();
	}
	else
	{
		var HTML = '<ul class="group_items">';
		// Not filtered by group
		HTML += buildAutoCompleteList(search_type, false);
		
		$('.autocomplete_header .all_label').show();
	}
	
	HTML += '</ul>';
	
	
	$('#div_for_autocomplete_' + search_type).find('.autocomplete_search_results').html(HTML).show();
	$('#div_for_autocomplete_' + search_type).show();
	
	// If we alreadye closed the autocomplete
	if(CloseCurrentAutoCompleteSelector)
	{
		CloseCurrentAutoCompleteSelector = false;
		closeAutoCompleteDiv(false);
	}
}

function buildAutoCompleteList(search_type, group_ids)
{

	var HTML = '';
	var ProductCounter = 0;
	var search_at = '';
	var VatCalcShow = ($('input[name="VatCalcMethod"]').val() == undefined) ? VAT_CALC_METHOD : $('input[name="VatCalcMethod"]').val();

	if(CurrentAutoCompleteSelector != null)
	{
		search_at = $(CurrentAutoCompleteSelector).val().toLowerCase();
	}
	
	$(AutoCompleteData[search_type].search_results).each(function(index,sub_element){
		
		// First search
		if(search_at && searchAutoCompleteElement(sub_element.searchable, search_at) === false)
		{
			return;
		}
		
		if(group_ids === false || $.inArray(parseInt(sub_element.id), group_ids) > -1)
		{
			ProductCounter++;
			
			if(ProductCounter > AutoCompleteMaxResults)
			{
				return false;
			}
			
			HTML += 
				'<li data-id="' + sub_element.id + '" data-code="' + sub_element.code +  '">' +
					'<span class="autocomplete_li_span">' + 
					htmlspecialchars(sub_element.code + ' ' + sub_element.title) +  '</span>';
					
			if(sub_element.priceperiod != undefined)
			{
				
				HTML += '<div class="autocomplete_price">'
							+ '<div style="width:25px;text-align:left;">' + sub_element.priceperiod + '</div>'
							+ '<div style="width:60px;" class="price_incl' + ((VatCalcShow == 'excl') ? ' hide' : '') + '">' + sub_element.priceincl + '</div>'
							+ '<div style="width:60px;" class="price_excl' + ((VatCalcShow == 'incl') ? ' hide' : '') + '">' + sub_element.priceexcl + '</div>'
							+ '<div style="width:10px;">' + CURRENCY_SIGN_LEFT + '</div>'
						+ '</div>';
			}
					
					
			HTML += '<br style="clear:both;" /></li>';
		}
	});
	
	if(HTML == '')
	{
		HTML = '<li class="noHover"><span style="font-style:italic;">' + __LANG_NO_RESULTS_FOUND + '</span></li>';	
	}	
	
	if(ProductCounter > AutoCompleteMaxResults)
	{
		var str = __LANG_AUTOCOMPLETE_RESULTS_LIMITED;
		HTML += '<li class="noHover" style="text-align:center;color:#b5b5b5;font-style:italic;">' + str.replace('%d', (AutoCompleteData[search_type].search_results.length - AutoCompleteMaxResults)) + '</li>';
	}
			
	return HTML;
}

function closeAutoCompleteDiv(check_for_one_result)
{
	if(CurrentAutoCompleteSelector != null && AutoCompleteData[$(CurrentAutoCompleteSelector).data('type')] != undefined)
	{
		var search_at = $(CurrentAutoCompleteSelector).val().toLowerCase();
		var search_result = false;
			
		if(search_at && check_for_one_result)
		{
			
			var search_counter = 0;
			
			// Look for number of results
			$(AutoCompleteData[$(CurrentAutoCompleteSelector).data('type')].search_results).each(function(index,sub_element){

				// First search
				if(search_at && searchAutoCompleteElement(sub_element.searchable, search_at) === false)
				{
					return;
				}
				search_counter++;
				
				// Store result
				search_result = (search_counter == 1) ? sub_element : false;
				
			});
			
			if(search_result)
			{
				// Select this last item
				selectAutoCompleteItem($(CurrentAutoCompleteSelector).data('type'), search_result.id, search_result.code, search_result.code + ' ' + search_result.title);
			}
		}
		
		// Back to original item
		if(!search_result && $('input[name="'+$(CurrentAutoCompleteSelector).data('inputfieldname')+'"]').val() != '')
		{
			$(CurrentAutoCompleteSelector).val($(CurrentAutoCompleteSelector).data('label'));
		}
		else if(!search_result)
		{
			$(CurrentAutoCompleteSelector).val('');
		}

		CurrentAutoCompleteSelector = null;
	}
	else if(CurrentAutoCompleteSelector != null)
	{
		CloseCurrentAutoCompleteSelector = true;
	}
	
	$('.autocomplete_search').hide();
}

function searchAutoCompleteElement(string, search_for)
{
	// Allow searching on multiple words
	var search_for_array = search_for.split(' ');
	
	var is_match = true;
	
	$(search_for_array).each(function(index,element){

		if(element && string.indexOf(element) == -1)
		{
			is_match = false;
		}
	});
	
	return is_match;
}

function changeCustomFields(CustomFields, Custom)
{
    $.each(CustomFields, function(field, value)
    {
        if($('input[name="custom['+field+']"][type="text"]').html() != undefined)
        {
            // Date input field
            if($('input[name="custom['+field+']"]').hasClass('datepicker'))
            {
                $('input[name="custom['+field+']"]').val(Custom[field]);
            }
            // Normal input field
            else
            {
                $('input[name="custom['+field+']"]').val(value);
            }
        }
        // Radio input
        else if($('input[name="custom['+field+']"][type="radio"]').html() != undefined)
        {
            $('input[name="custom['+field+']"]').prop('checked', false);
            $('input[name="custom['+field+']"][value="'+value+'"]').prop('checked', true);
        }
        // Checkboxes
        else if($('input[name="custom['+field+'][]"][type="checkbox"]').html() != undefined)
        {
            $('input[name="custom['+field+'][]"]').prop('checked', false);
            $(value).each(function(index, value2){
                $('input[name="custom['+field+'][]"][value="'+value2+'"]').prop('checked', true);
            });
        }
        // Select
        else if($('select[name="custom['+field+']"]').html() != undefined)
        {
            $('select[name="custom['+field+']"]').val(value);
        }
        // Textarea
        else if($('textarea[name="custom['+field+']"]').html() != undefined)
        {
            $('textarea[name="custom['+field+']"]').val(value).autoGrow();
        }
        // Else is does not exists


    });
}


function openModal(dialog_content)
{
    if($('.modal_box').html() == undefined)
    {
        // First initate the modal
        $('body').prepend('<div class="modal_box width_510px"></div><div class="modal_overlay"></div>');
    }

    $('.modal_box').load(dialog_content, function(data){

        if(data == 'reload')
        {
            location.href=location.href;
        }
        else
        {
            // Open the modal
            $('.modal_overlay, .modal_box').addClass('show');

            // Scoll to modal
            $('html, body').scrollTop('.modal_box', $(".modal_box").offset().top);

            $(".container").delay(300).queue(function(){
                $(this).addClass("blur").dequeue();
            });

            // Prepare loading button
            if($('.modal_box #submit_button').html() != undefined && $('.modal_box #submit_button').hasClass('has_loading_button'))
            {
                $('.modal_box #submit_button').after('<div class="loading_btn hide"><img src="images/loadinfo.gif">' + $('.modal_box #submit_button').data('loading') + '</div>');
            }
        }
    });
}

function closeModal()
{
    $(".container").removeClass('blur');
    $('.modal_overlay, .modal_box').removeClass('show');
}

function openInlineModal(dialog_div, dialog_content)
{
	if($('.universal_modal_box').html() == undefined)
	{
		// First initate the modal
		$('body').prepend('<div class="universal_modal_box"></div><div class="universal_modal_overlay"></div>');
	}

	// Copy HTML
	if($('#' + dialog_div).html() != undefined)
	{
		if($('#' + dialog_div).data('modal-width') != undefined)
		{
			$('.universal_modal_box').css('width', $('#' + dialog_div).data('modal-width') + 'px').css('marginLeft', '-' + parseInt($('#' + dialog_div).data('modal-width') / 2) + 'px');
		}
		else
		{
			// Restore to default
			$('.universal_modal_box').css('width', '520px').css('marginLeft', '-255px');
		}

		// Current position, wrap with placeholder, so we can place it back in DOM
		$('#' + dialog_div).wrap('<div id="universal_modal_box_placeholder"></div>');

		// Always empty current modal_box, before continue
		$('.universal_modal_box').html('');
		$('.universal_modal_box').data('dialog_div',dialog_div).prepend($('#' + dialog_div).removeClass('hide'));

		// Open the model
		$('.universal_modal_overlay, .universal_modal_box').addClass('show');
	}
	else if(dialog_content != undefined)
	{
		$('.universal_modal_box').load(dialog_content, function(data)
		{
			if(data == 'reload')
			{
				$('.universal_modal_overlay, .universal_modal_box').removeClass('show');
			}
			else
			{
				// Open the model
				$('.universal_modal_overlay, .universal_modal_box').addClass('show');
			}
		});
	}

	var scrollTo = Math.max(
		$('html').scrollTop(),
		$('body').scrollTop()
	);

	if(scrollTo > $('.universal_modal_box').offset().top)
	{
		// Put universal modal box in visible part
		$('.universal_modal_box').offset({ top: scrollTo + 40 });
	}

	// Add escape key listener
	if(CloseInlineModalOnEscapeKey == false)
	{
		$(document).on('keydown', function (e){
			if(e.keyCode === 27 && $('.universal_modal_box').hasClass('show')) { // ESC
				closeInlineModal();
			}
		});

		CloseInlineModalOnEscapeKey = true;
	}
}

function closeInlineModal()
{
	// Unwrap element and place back in placeholder again
	var dialog_div = $('.universal_modal_box').data('dialog_div');
	$('#universal_modal_box_placeholder').prepend($('#' + dialog_div).addClass('hide'));
	$('#' + dialog_div).unwrap();
	$('.universal_modal_overlay, .universal_modal_box').removeClass('show');

	// Set default offset
	jQuery('.universal_modal_box').offset({ top: 160 });
}

function batch_schedule_invoice_draft()
{
    // Hide warning of manual sending
    $('.schedule_draft_invoices_manual').addClass('hide');

    // Look for other invoicemethods than e-mail
    var NumberOfNonMailMethods = 0;
    $('form[name="' + BatchForm + '"] input[name="ids[]"]:checked').each(function(index,element){

        if($(element).parents('tr').find('a[data-inv-method][data-inv-method!="0"]').length > 0)
        {
            NumberOfNonMailMethods++;
        }
    });

    // Show manual message if needed
    if(NumberOfNonMailMethods > 0)
    {
        $('#batch_confirm_text .schedule_draft_invoices_manual').html($('#batch_confirm_text .schedule_draft_invoices_manual').html().replace('{count_manual}', NumberOfNonMailMethods));
        $('.schedule_draft_invoices_manual').removeClass('hide');
    }
}

function createFlexibleReferenceAndDateColumn()
{
    $('table .table_reference_th').each(function(){

        var HasReference = 0;
        $(this).parents('table').find('td.table_reference_td').each(function(){
            if($(this).html() != '' && $(this).html() != '&nbsp;')
            {
                HasReference++;
            }
        });

        if(HasReference == 0)
        {
            // Make column smaller
            $(this).css('width', '60px');
        }
    });

    $('table .table_date_th').each(function(){

        var HasPlannedDate = 0;
        $(this).parents('table').find('td.table_date_td span').each(function(){
            if($(this).html() != '' && $(this).html() != '<span>&nbsp;</span>')
            {
                HasPlannedDate++;
            }
        });

        if(HasPlannedDate > 0)
        {
            // Make column wider
            $(this).css('width', '100px');

            // Show time
            $(this).parents('table').find('td.table_date_td span span[data-role="time"]').removeClass('hide');
        }
    });

    // Originates from tablet.php
    if($(window).width() <= 1260)
    {
        autogrowLineHeight = 18;
    }
}
