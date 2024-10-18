$(function(){

	if($('#delete_template')){
		$('#delete_template').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
		
		$('input[name=imsure]').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				$('#delete_template_btn').removeClass('button2').addClass('button1');
			}
			else
			{
				$('#delete_template_btn').removeClass('button1').addClass('button2');
			}
		});
		$('#delete_template_btn').click(function(){
			if($('input[name=imsure]:checked').val() != null)
			{
				document.form_delete.submit();
			}	
		});
	}
	
	if($('#add_template')){
		$('#add_template').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

		$('input[name="new_type"]').click(function(){
			if($('input[name="new_type"]:checked').val() == 'clone')
			{
				$('#add_template_clone').show();
			}	
			else
			{
				$('#add_template_clone').hide();
			}
		});

		$('#add_template_btn').click(function(){
			document.form_template.submit();	
		});
	}
	
	/* Advanced */
	$('#add_element').click( function() {
		
		$('#loader_saving1').hide();$('#loader_saving2').hide();
		
		$.post("XMLRequest.php", { action: 'add_element', layout_id: $('#layout_id').val() }, function(data){
			$('.edit_element').removeClass('clicked')
			
			if($('#layout_elements_list_ul').html() == null){
				$('#layout_elements_none').before('<div id="layout_elements_list" class="height3 overflow-y"><ul id="layout_elements_list_ul" class="emaillist"></ul><br /></div>');
				$('#layout_elements_none').remove();
			}
			
			
			$('#layout_elements_list_ul').prepend('<li class="edit_element clicked"><div id="ElementID-' + data + '" class="delete_cross not_visible delete_element">&nbsp;</div><span>Nieuwe regel</span></li>');
			
			edit_layout_line(data);
		});
	});

    $(document).on('click', '.edit_element', function() {
		$('#loader_saving1').hide();$('#loader_saving2').hide();
		
		ElementID = $(this).find('div').attr('id').replace('ElementID-','');
		$('.edit_element').removeClass('clicked');
		$(this).addClass('clicked');
		
		edit_layout_line(ElementID);
	});
	
	
	$('#layout_elements_list_ul').find('li:first').addClass('clicked');
	if($('#layout_elements_list_ul li').length){
		edit_layout_line($('#layout_elements_list_ul').find('li:first div').attr('id').replace('ElementID-',''));	
	}
	
	$('#delete_element_dialog').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

    $(document).on('click', '.delete_element', function(event) {
		event.stopPropagation();
		
		ElementID = $(this).attr('id').replace('ElementID-','');
		$('input[name=DeleteElementID]').val(ElementID);
		$('#DeleteElementName').html($(this).parent().find('span').html());
		
		$('#delete_element_dialog').dialog('open');
	});
	
	$('#delete_element_btn').click(function(){
		ElementID = $('input[name=DeleteElementID]').val();
		
		$('input[name=DeleteElementID]').val('');
		$('#delete_element_dialog').dialog('close');

		$.post("XMLRequest.php", { action: 'delete_element', element: ElementID }, function(data){
			li = $('#ElementID-'+ElementID).parent();
			li.remove();
			
			if($('#layout_elements_list_ul').html() == ""){
				$('#layout_elements_list').before('<div id="layout_elements_none">'+__LANG_LAYOUT_NO_ELEMENTS+'</div>');
				$('#layout_elements_list').remove();
				$('#tabs').hide();
			}else{	
				$('#layout_elements_list_ul').find('li:first').addClass('clicked');
				edit_layout_line($('#layout_elements_list_ul').find('li:first div').attr('id').replace('ElementID-',''));
			}
		});
	});
	
	$('select[name=Type]').change(function(){
		ElementID = $('input[name=LineIdentifier]').val();
		
		change_type_layout_line(ElementID, $(this).val());
	});
	
	$('#add_pdf_download_dialog').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto', beforeClose: function(event, ui){ $('#restore_pdf_download_span').show(); get_uploaded_files_pdf('download');}});
	$('#add_pdf_print_dialog').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto', beforeClose: function(event, ui){ $('#restore_pdf_print_span').show(); get_uploaded_files_pdf('print');}});

    $(document).on('click', '#add_pdf_download_link',function(){
		$('#add_pdf_download_dialog').dialog('open');
	});

    $(document).on('click', '#add_pdf_print_link',function(){
		$('#add_pdf_print_dialog').dialog('open');
	});

    $(document).on('click', '#delete_pdf_download_link',function(){
		$.post("XMLRequest.php", { action: 'empty_location', Identifier: $('input[name=template_id]').val() }, function(data){
			$('#delete_pdf_download_link').hide();
			$('#file_pdf_download').html('geen bestand');
		});
	});

    $(document).on('click', '#delete_pdf_print_link',function(){
		$.post("XMLRequest.php", { action: 'empty_location', Identifier: $('input[name=template_id]').val(), type: 'print' }, function(data){
			$('#delete_pdf_print_link').hide();
			$('#file_pdf_print').html('geen bestand');
		});
	});

    $(document).on('click', '#restore_pdf_download_link',function(){
		$.post("XMLRequest.php", { action: 'restore_location', Identifier: $('input[name=template_id]').val() }, function(data){
			$('#delete_pdf_download_link').show();
			$('#file_pdf_download').html(__LANG_STANDARD_TEMPLATE_FILENAME);
		});
	});

    $(document).on('click', '#restore_pdf_print_link',function(){
		$.post("XMLRequest.php", { action: 'restore_location', Identifier: $('input[name=template_id]').val(), type: 'print' }, function(data){
			$('#delete_pdf_print_link').show();
			$('#file_pdf_print').html(__LANG_STANDARD_TEMPLATE_FILENAME);
		});
	});
	
	$('.save_advanced_layout').click( function() {
		id = $(this).attr('id');
		
		$.post("XMLRequest.php", { 
			action: 'edit_element', 
			Template: $('#layout_id').val(),
			Identifier: $('input[name=LineIdentifier]').val(), 
			Name: $('input[name=ElementName]').val(),
		 	Type: $('select[name=Type]').val(),
		 	Visible: $('select[name=Visible]').val(),
		 	X: $('input[name=X]').val(),
		 	Y: $('input[name=Y]').val(),
		 	Value: $('textarea[name=Value]').val(),
		 	Font: $('select[name=Font]').val(),
		 	FontSize: $('select[name=FontSize]').val(),
		 	FontStyle: $('select[name=FontStyle]').val(),
		 	Align: $('select[name=Align]').val(),
		 	Page: $('input[name=Page]').val(),
		 	Width: $('input[name=Width]').val(),
		 	Height: $('input[name=Height]').val()
		}, function(data){
			$('#ElementID-' + $('input[name=LineIdentifier]').val()).parent().find('span').html($('input[name=Name]').val());
			
			$('#layout_elements_list').load(location.href + ' #layout_elements_list_ul', function(){
				$('#ElementID-' + $('input[name=LineIdentifier]').val()).parent().addClass('clicked');
			});
		});

		if(id == 'save_1'){
			$('#loader_saving1').show();
		}
		if(id == 'save_2'){
			$('#loader_saving2').show();
		}
		
		setTimeout("$('#loader_saving1').fadeOut();",2000);
		setTimeout("$('#loader_saving2').fadeOut();",2000);
	});
	
});

function get_uploaded_files_pdf(upload_type){

	$.post("XMLRequest.php", { action: 'get_uploaded_files', type: 'PDF' }, function(data){
		if(data.FileName != undefined){
			if(upload_type != undefined){
				$('input[name=file_id_pdf_'+upload_type+']').val(data.FileID);
				$('#file_pdf_'+upload_type).html(data.FileName);
				
				$('#add_pdf_'+upload_type+'_dialog').load(location.href + ' #add_pdf_' + upload_type + '_dialog iframe');
				
				$('#delete_pdf_'+upload_type+'_link').show();
			}else{
				$('input[name=file_id_pdf]').val(data.FileID);
				$('#file_pdf').html(data.FileName);
				
				$('#add_pdf_dialog').load(location.href + ' #add_pdf_dialog iframe');
				
				$('#delete_pdf_link').show();
			}
		}
	}, "json");	
}

function change_type_layout_line(ElementID, newType){
	$.post("XMLRequest.php", { action: 'get_element', element: ElementID }, function(data){
		$('textarea[name=Value]').val(data.Value);
	}, "json");	
}

function edit_layout_line(ElementID){
	$.post("XMLRequest.php", { action: 'get_element', element: ElementID }, function(data){
		$('.layout_details_none').hide();
		$('.layout_details_list').show();
		$('#tabs').show();
		$('#tabs').tabs("option", "active", 0);
		
		$('input[name=LineIdentifier]').val(ElementID);
		$('input[name=ElementName]').val(data.Name);
		$('select[name=Type]').val(data.Type);
		$('select[name=Visible]').val(data.Visible);
		$('input[name=Page]').val(data.Page);
		
		$('#advanced_text_block').show();
		$('textarea[name=Value]').val(data.Value);
		
		$('select[name=Font]').val(data.Font);
		$('select[name=FontSize]').val(data.FontSize);
		$('select[name=FontStyle]').val(data.FontStyle);
		$('select[name=Align]').val(data.Align);
		
		$('input[name=X]').val(data.X);
		$('input[name=Y]').val(data.Y);
		$('input[name=Width]').val(data.Width);
		$('input[name=Height]').val(data.Height);
	}, "json");	
}

function get_layout_lines(){
	$.post("XMLRequest.php", { get: 'get_layout_elements' }, function(data){
		for(i = 0; i < data.length; i++){
			if(data[i].Template != undefined){
				$('#layout_elements_list_ul').append('<li><div id="ElementID-'+data[i].id+'" class="delete_cross not_visible attachment_delete">&nbsp;</div>' + data[i].id + '</li>');
			}
		}
		
		if($('input[name=Layout]').val()){
			$('#layout_elements_list').show();
			$('#layout_elements_none').hide();
		}
	}, "json");
}