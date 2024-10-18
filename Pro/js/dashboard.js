$(function(){
	
	if($('#diagram_circle').length){
		createDiagramCircle();
	}

	$('#edit_widgets').click(function(){
		$(this).hide();
		$('#edit_widgets_done').show();
		$('.widget').addClass('edit');
		$('.widget:not(.add)').addClass('widget_edit');
		$('#widgets').show();
		$('.widget.add').show();

		$('#widgets').sortable({
			//axis : 'x',
			placeholder: 'ui-state-highlight',
			forcePlaceholderSize: true,
			start: function(e, ui){
				ui.placeholder.css({'float':'left','width':ui.item.width(),'margin':'0 21px 21px 0','padding':'8px','border-radius':'5px'});
			},
			items: '> li:not(.add)',
			beforeStop: function(event,ui){
				$.post('XMLRequest.php', {action: 'widgets_save', order: $('#widgets').sortable('serialize')}, function(){
					
				});	
			}
		});
        $('#widgets').sortable('enable');
		//$("#widgets").disableSelection();
		
	});
	
	$('#edit_widgets_done').click(function(){
		$(this).hide();
		$('#edit_widgets').show();
		$('.widget').removeClass('edit');
		$('.widget').removeClass('widget_edit');
		$(".widget.add").hide();
		$('#widgets').sortable('disable');
		
		if($('#widgets li').length == 1){
			$('#widgets').hide();	
		}
	});

    $(document).on('click', '.widget_edit',function(){
		$('#dialog_widgets_edit input[name=widgetID]').val($(this).find('.widgetID').val());
		$('#dialog_widgets_edit #widget_name').html($(this).find('.widgetName').val());
		getWidgetOptions($(this).find('.widgetID').val());
		$('#dialog_widgets_edit').dialog('open');
	});
	
	$('.widget.add').click(function(){
		$('#dialog_widgets_add').dialog('open');
	});
	
	$('#dialog_widgets_edit #widget_edit').click(function(){
		$('#dialog_widgets_edit input[name=action]').val('editwidget');
		document.form_widgets.submit();
	});		
	
	$('#dialog_widgets_edit #widget_remove').click(function(){
		$('#dialog_widgets_edit input[name=action]').val('removewidget');
		document.form_widgets.submit();
	});
	
	$('#widgets_select').change(function(){
		addWidgetOptions($(this).val());
	});
	
	$('#dialog_widgets_add, #dialog_widgets_edit').dialog({modal: true, autoOpen: false, resizable: false, width: 400});
	
	$('#indexCronjob').dialog({modal: true, autoOpen: true, resizable: false, width: 700});
	// Start Cronjob
	if($('#indexCronjob').html() != null){
		try{ 
			AJAX_cronjob(0); 
			
			$('.ui-dialog-titlebar a').click(function(){
				if(runScript == 'false'){
					window.location.reload();
				}else{
					runScript = 'false';
				} 
		   });
		}catch(e){ 
			$('#indexCronjob').dialog('close');
		}
	}
	
    //Agenda
	if($('#agendaperiod').html() != null){
	   
		$('select[name="period"]').change(function(){
			if($(this).val() == 'd'){
				$('#agendaperiod_custom').show();
			}else{
				save('agenda.overview','period',$(this).val(), $('#current_url').val());
			}
		});
		
		$('#agendaperiodselect').click(function(){
			$('#agendaperiod').show();
		});
		
		var mouse_is_inside_agendaperiod = false;

        $(document).on('mouseenter', '#agendaperiod, #ui-datepicker-div', function () {
            mouse_is_inside_agendaperiod = true;
        });
        $(document).on('mouseleave', '#agendaperiod, #ui-datepicker-div', function () {
            mouse_is_inside_agendaperiod = false;
        });
		
		$('body').mouseup(function(){
	
			if($('#agendaperiod').html() != null){
				if(!mouse_is_inside_agendaperiod){
		        	$('#agendaperiod').hide();
				}
			}
	    });
		
		$('#agendaperiod_submit_btn').click(function(){
			save('agenda.overview','period','d_'+$('#start_date').val()+'_'+$('#end_date').val(), $('#current_url').val());
		});
		
		$('#agendaDialog').dialog({ autoOpen: false, width: 600, modal: true, resizable: false, open: function(event, ui) { $('textarea[name="Description"]').focus(); } });
		$('#agendaDialog.autoopen').dialog('open');

        $(document).on('click', '#WholeDay',function(){

			if($(this).prop('checked')){
				$('.timetype_time').hide();
			}else{
				$('.timetype_time').show();
			}
		});

        $(document).on('click', '#EmailNotify',function(){

			if($(this).prop('checked')){
				$('#emailnotify_days').show();
			}else{
				$('#emailnotify_days').hide();
			}
		});
		
		$('#agenda_dialog_btn').click(function(){
			$('form[name="AgendaForm"]').submit();
		});

		if($('#today').html() != null){
			location.href = '#today';
		}
		
		$('input[name="AgendaSearch"]').keypress(function(event){
			if(event.keyCode == 13){
				save('agenda.overview','search',$(this).val(), $('#current_url').val());
			}
		});
        
        $('input[name="AgendaSearch"]').keypress(function(event){
			if(event.keyCode == 13){
				save('agenda.overview','search',$(this).val(), $('#current_url').val());
			}
		});
        
        // Add a agenda item \\
        
        // Open add dialog
        $(document).on('click', '.agendaDialogNewItem',function(){
            
            var newDate = $(this).attr('rel');
            
            $('#agenda_dialog_container').load('agenda.php?page=add #agendaDialog', function() {
                
                if(newDate){
                    $('input[name="Date"]').val(newDate);
                }
                
                $('#agendaDialog').dialog({ autoOpen: false, width: 600, modal: true, resizable: false,
                    open: function(event, ui) { 
                        $('textarea[name="Description"]').focus(); 
                    },
                    close: function(event, ui) {
                        $(this).remove();
                        $('input[name="agendaIdEdit"]').val(0);
                    }
                });
                $('#agendaDialog').dialog('open');
            });
        });
        
        
        // Edit agenda item \\
        
        // Save fields
        $(document).on('click', '#agenda_dialog_btn',function(){
            var agendaId = $('input[name="id"]').val();
            var formFields = $('form[name="AgendaForm"]').serialize();
            
            $.post('agenda.php', { action: 'agenda_save_item', agendaId: agendaId, fields: formFields }, function(agendaEditResult){
                
				if(agendaEditResult['result'] == 'OK'){
				    $('#agendaEditStatus').html('<span class="loading_green">&nbsp;&nbsp;'+agendaEditResult['msg']+'</span>');
                    if(agendaId > 0){                    
                        setTimeout(function(){
                            $('#agendaDialog').dialog('close');
                            $('#agenda_container').load('agenda.php #agenda_table');
                        }, 800);
                    }else{
                        $('#agendaDialog').dialog('close');
                        $('#agenda_container').load('agenda.php #agenda_table');
                    }           
				}else if(agendaEditResult['result'] == 'BAD'){
					$('#agendaEditStatus').html('<span class="loading_red">&nbsp;&nbsp;'+agendaEditResult['msg']+'</span>');
				}else{
					$('#agendaEditStatus').html('');
				}
			}, 'json');
        });
        
        // Open dialog
        $(document).on('click', '.agendaDialogEditItem',function(){
            var agendaId = $(this).attr('rel');
            
            // Get dialog form
            $('#agenda_dialog_container').load('agenda.php?page=edit&id='+agendaId+' #agendaDialog', function() {
                // Prepare edit dialog
                $('#agendaDialog').dialog({ autoOpen: false, width: 600, modal: true, resizable: false,
                    open: function(event, ui) { 
                        $('textarea[name="Description"]').focus(); 
                    },
                    close: function(event, ui) {
                        $(this).remove();
                        $('input[name="agendaIdEdit"]').val(0);
                    }
                });
                
                $('#agendaDialog').dialog('open');
            });
        });
        
        
        // Remove calendar item \\
        
        // Prepare dialog
        $('#agendaDialogDeleteItem').dialog({ autoOpen: false, width: 600, modal: true, resizable: false,
            open:  function(event, ui) {
                $('#agendaRemoveStatus').html('');
            },
            close: function(event, ui) {
                $('input[name="agendaId"]').val(0);
            } 
        });
        
        // Open dialog
        $(document).on('click', '.agendaDialogDeleteItem',function(){
            $('input[name="agendaId"]').val($(this).attr('rel'));
            $('#agendaDialogDeleteItem').dialog('open');
        });
        
        // Agenda remove button check
        $('#agenda_dialog_remove_btn').click(function(){
		    var agendaId = $('input[name=agendaId]').val();
			
            $('#agendaRemoveStatus').html('');
            
            // Remove agenda item
            $.post('agenda.php', { action: 'agenda_remove_item', agendaId: agendaId }, function(agendaRemoveResult){
                
				if(agendaRemoveResult['result'] == 'OK'){
                    $('#agenda_container').load('agenda.php #agenda_table');
                    $('#agendaDialogDeleteItem').dialog('close');
				}else if(agendaRemoveResult['result'] == 'BAD'){
					$('#agendaRemoveStatus').html('<span class="loading_red">&nbsp;&nbsp;'+agendaRemoveResult['msg']+'</span>');
				}
			}, 'json');
		});
        
	}
});

var runScript;

function AJAX_cronjob(IndexCronjobCounter){
	
	$.post('index_cronjob.php', {IndexCronjobCounter: IndexCronjobCounter}, function(data){
		if(data){
			if(data.substr(0,1) == "0" || isNaN(data.substr(0,1))){
				runScript = "false";	
				if(data != "0"){
					$('#indexCronjob').html('<strong>'+ __LANG_INDEX_CRONJOB_RESULT_TITLE+'</strong><br /><br \/>' 
											+ data.substr(1) 
											+ '<br /><a href="#" onclick="window.location.reload();" class="button1 alt1"><span>'+ __LANG_INDEX_CRONJOB_RESULT_CLOSE+'</span></a>');
					return false;
				}
			}else{	
				todo_array = data.split('|');
				$('#indexCronjob_progress #ic_count_mails').html(todo_array[1]);
				$('#indexCronjob_progress #ic_count_hosting').html(todo_array[2]);
				$('#indexCronjob_progress #ic_count_domain').html(todo_array[3]);
				
				$('#indexCronjob_progress').slideDown();
			}
			
			if(runScript != "false"){
				AJAX_cronjob(IndexCronjobCounter+1);
				return true;
			}else{
				window.location.reload();
			}
        }
    }).fail(function (jqXHR) {
        if(jqXHR.responseText != undefined) {
            // On failure, we want to show the error message.
            $('#indexCronjob').html('<strong>' + __LANG_INDEX_CRONJOB_RESULT_TITLE + '</strong><br /><br \/>'
                + '<div class="mark alt3"><p>' + jqXHR.responseText + '</p></div>'
                + '<br /><a href="#" onclick="window.location.reload();" class="button1 alt1"><span>' + __LANG_INDEX_CRONJOB_RESULT_CLOSE + '</span></a>');
        }
        return false;
    });
}

function addWidgetOptions(widget){
	if(widget != ''){
		$.post('XMLRequest.php', {action: 'widgets_getoptions', widget: widget}, function(data){
			if(data){
				$('.widget_options').html(data);
			}
		});
	}else{
		$('.widget_options').html('');
	}
}

function getWidgetOptions(widgetID){
	$.post('XMLRequest.php', {action: 'widgets_getoptions', widgetID: widgetID}, function(data){
		if(data){
			$('.widget_options').html(data);
		}
	});
}

function createDiagramCircle(id, percentage){
	
	var thickness = 15;
	var size = 100;
	var radius = (size - thickness) / 2;
	var center = size / 2;
	var offset = 0;
	current_x = size / 2;
    current_y = thickness / 2;
	
	var billable_text = $('#diagram_circle h3').html();
	//var percentage = parseFloat(billable_text.replace(/%/, ''));
	
	var angle = ((percentage + offset) *  2*Math.PI) / 100 + Math.PI/2;
    var x = (size / 2) - radius * Math.cos(angle);
    var y = (size / 2) - radius * Math.sin(angle);
	
	var paper = new Raphael(id, size, size);
	var diagram = paper.circle(center,center,radius).attr({stroke: '#B5E5A5', 'stroke-width': thickness});
	
	var path = [
      ['M', current_x, current_y],
      ['A', radius, radius, 0, percentage > 50 ? 1 : 0, 1, x, y]];
    var arc = paper.path(path).attr({stroke: '#41B419', 'stroke-width': thickness});
	current_x = x;
    current_y = y;
    
}

function createDiagramBar(id,values,units,label, values_lastyear){
	var thickness = ($(values_lastyear).length > 0) ? 11 : 25;
	var size = 88;
		
	if(values.length > 6){
		var width = 358;
	}else{
		var width = 160;	
	}

	var max = Math.max(Math.max.apply(Math, values), Math.max.apply(Math, values_lastyear));
	var r = new Raphael(id, width, size+15);
	var ratio = size/max;
	
	var q = 1;
	var xpos = 10;
	var xpos_offset = ($(values_lastyear).length > 0) ? 12 : 0;
	var colors = ['#B5E5A5','#9EDC89','#87D26D','#70C851','#41B419']
	/*values.sort();*/
	
	$.each(values,function(index,value){
		//if(typeof value != 'undefined'){

			if($(values_lastyear).length > 0)
			{
				var stroke_ly = r.path("M" + (xpos+1) + " 88L" + (xpos+1) + " " + ((size - (values_lastyear[index] * ratio)) - 1)).attr({stroke: '#B5E5A5', 'stroke-width': thickness});
			}

			var stroke = r.path("M" + (xpos + xpos_offset) + " 88L" + (xpos + xpos_offset) + " " + ((size - (value * ratio)) - 1)).attr({stroke: '#87D26D', 'stroke-width': thickness});
			
			
			if(value > 0 || values_lastyear[index] > 0){					
				var popup_start_x = (xpos);
				var popup_start_y = ((size - (value * ratio)) - 10);
				
				if(popup_start_y < 10){
					popup_start_y = ((size - (value * ratio)) - 1) + 10;
				}
				
				var last_year_text = ($(values_lastyear).length > 0 && values_lastyear[index]) ? '\n' + __LANG_LAST_YEAR + ': ' + ((CURRENCY_SIGN_LEFT) ? CURRENCY_SIGN_LEFT + ' ' : '') + formatAsMoney(values_lastyear[index]) + ((CURRENCY_SIGN_RIGHT) ? ' ' + CURRENCY_SIGN_RIGHT : '') : '';
				
				var popup = r.text(popup_start_x,popup_start_y,label + ': ' + ((CURRENCY_SIGN_LEFT) ? CURRENCY_SIGN_LEFT + ' ' : '') + formatAsMoney(value) + ((CURRENCY_SIGN_RIGHT) ? ' ' + CURRENCY_SIGN_RIGHT : '') + last_year_text).attr({'fill': '#414042'}).hide();
				
				if(popup_start_x + (popup.getBBox().width / 2) > width){
					popup.attr({'x': width - (popup.getBBox().width / 2) });
				}else if(popup_start_x - (popup.getBBox().width / 2) < 0){
					popup.attr({'x': (popup.getBBox().width / 2)  });
				}

				var popup_background = r.path("M" + popup.getBBox().x + " " + popup_start_y + "L" + (popup.getBBox().x+popup.getBBox().width) + " " + popup_start_y).attr({stroke: '#eee', 'stroke-width': ((last_year_text) ? 30 : 15)}).hide();
				
				popup.hover(function(){  popup_background.show().toFront(); popup.show().toFront(); }, function(){ popup.hide(); popup_background.hide(); });
				
				if(value > 0){
					stroke.hover(function(){  popup_background.show().toFront(); popup.show().toFront(); }, function(){ popup.hide(); popup_background.hide(); });
				}
				
				if($(values_lastyear).length > 0)
				{
					stroke_ly.hover(function(){ popup_background.show().toFront(); popup.show().toFront();  }, function(){ popup.hide(); popup_background.hide(); });
				}
			}
			
			if(typeof units != 'undefined' && units[index] != ''){
				r.text(xpos + (xpos_offset / 2),96,units[index]);
			}

			xpos = xpos + 26;
			q++;
		//}
	});
}

function createLineGraph(){
	
	var labels = [],
		labels_lastyear = [],
        data = [],
		data_lastyear = [];
    $("#data tfoot th").each(function () {
        labels.push($(this).html());
    });
    $("#data_previous tfoot th").each(function () {
        labels_lastyear.push($(this).html());
    });
    $("#data tbody td").each(function () {
        data.push($(this).html());
    });
    $("#data_previous tbody td").each(function () {
        data_lastyear.push($(this).html());
    });
    
    // Draw
    var width = 306,
        height = 120,
        leftgutter = -15,
        bottomgutter = 20,
        topgutter = 20,
        colorhue = .6 || Math.random(),
        color = "hsl(" + [colorhue, .5, .5] + ")",
        r = Raphael("holder", width, height),
        txt = {font: '12px Helvetica, Arial', fill: "#414042"},
        txt1 = {font: '10px Helvetica, Arial', fill: "#fff"},
        txt2 = {font: '12px Helvetica, Arial', fill: "#000"},
        X = (width - leftgutter) / labels.length,
        max = Math.max.apply(Math, $.merge(data,data_lastyear)),
        Y = (height - bottomgutter - topgutter) / max;
    
    var path_lastyear = r.path().attr({stroke: '#C5D5EC', "stroke-width": 2, "stroke-linejoin": "round"}),
		bgp_lastyear = r.path().attr({stroke: "none", opacity: .3, fill: color}),
		path = r.path().attr({stroke: color, "stroke-width": 2, "stroke-linejoin": "round"}),
		bgp = r.path().attr({stroke: "none", opacity: .6, fill: color}),
        label = r.set(),
        lx = 0, ly = 0,
        is_label_visible = false,
        leave_timer,
        blanket = r.set();
    label.push(r.text(60, 12, "dummy").attr(txt));
    label.push(r.text(60, 27, "dummy").attr(txt1).attr({fill: color}));
    label.hide();
    var frame = r.popup(100, 100, label, "right").attr({fill: "#EEEEEE", stroke: "#414042", "stroke-width": 1, "fill-opacity": .9}).hide();

    var p, p_lastyear, bgpp, bgpp_lastyear;
    for (var i = 0, ii = labels.length; i < ii; i++) {
        var y = Math.round(height - bottomgutter - Y * data[i]),
        	y_lastyear = Math.round(height - bottomgutter - Y * data_lastyear[i]),
            x = Math.round(leftgutter + X * (i + .5)),
            t = r.text(x, height - 6, labels[i]).attr(txt).hide().toBack();
		if (!i) {
            p = ["M", x, y, "C", x, y];
            bgpp = ["M", leftgutter + X * .5, height - bottomgutter, "L", x, y, "C", x, y];
            p_lastyear = ["M", x, y_lastyear, "C", x, y_lastyear];
            bgpp_lastyear = ["M", leftgutter + X * .5, height - bottomgutter, "L", x, y_lastyear, "C", x, y_lastyear];
        }
        if (i && i < ii - 1) {
            var Y0 = Math.round(height - bottomgutter - Y * data[i - 1]),
            	Y0_lastyear = Math.round(height - bottomgutter - Y * data_lastyear[i - 1]),
                X0 = Math.round(leftgutter + X * (i - .5)),
                Y2 = Math.round(height - bottomgutter - Y * data[i + 1]),
                Y2_lastyear = Math.round(height - bottomgutter - Y * data_lastyear[i + 1]),
                X2 = Math.round(leftgutter + X * (i + 1.5));
            var a = getAnchors(X0, Y0, x, y, X2, Y2);
            var a_lastyear = getAnchors(X0, Y0_lastyear, x, y_lastyear, X2, Y2_lastyear);
			
			p = p.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
            bgpp = bgpp.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
            p_lastyear = p_lastyear.concat([a_lastyear.x1, a_lastyear.y1, x, y_lastyear, a_lastyear.x2, a_lastyear.y2]);
            bgpp_lastyear = bgpp_lastyear.concat([a_lastyear.x1, a_lastyear.y1, x, y_lastyear, a_lastyear.x2, a_lastyear.y2]);
        }
        var dot = r.circle(x, y, 4).attr({fill: "#333", stroke: color, "stroke-width": 2}).hide();
        blanket.push(r.rect(leftgutter + X * i, 0, X, height - bottomgutter).attr({stroke: "none", fill: "#fff", opacity: 0}));
        var rect = blanket[blanket.length - 1];
        (function (x, y, data, data_lastyear, lbl, lbl_lastyear, dot) {
            var timer, i = 0;
            rect.hover(function () {
                clearTimeout(leave_timer);
                var side = "right";
                if (x + frame.getBBox().width > width) {
                    side = "left";
                }
                
                var ppp = r.popup(x, y, label, side, 1),
                    anim = Raphael.animation({
                        path: ppp.path,
                        transform: ["t", ppp.dx, ppp.dy]
                    }, 200 * is_label_visible);
				lx = label[0].transform()[0][1] + ppp.dx;
                ly = label[0].transform()[0][2] + ppp.dy;
                frame.show().stop().animate(anim);
                label[0].attr({text: data_lastyear + " / " + data}).show().stop().animateWith(frame, anim, {transform: ["t", lx, ly]}, 200 * is_label_visible);
                label[1].attr({text: (lbl_lastyear != lbl) ? lbl_lastyear + " / " + lbl : lbl }).show().stop().animateWith(frame, anim, {transform: ["t", lx, ly]}, 200 * is_label_visible);
                dot.attr("r", 6);
                is_label_visible = true;
            }, function () {
                dot.attr("r", 4);
                leave_timer = setTimeout(function () {
                    frame.hide();
                    label[0].hide();
                    label[1].hide();
                    is_label_visible = false;
                }, 1);
            });
        })(x, y, data[i], data_lastyear[i], labels[i], labels_lastyear[i], dot);
    }
    p = p.concat([x, y, x, y]);
    bgpp = bgpp.concat([x, y, x, y, "L", x, height - bottomgutter, "z"]);
    p_lastyear = p_lastyear.concat([x, y_lastyear, x, y_lastyear]);
	bgpp_lastyear = bgpp_lastyear.concat([x, y_lastyear, x, y_lastyear, "L", x, height - bottomgutter, "z"]);
    path.attr({path: p});
    path_lastyear.attr({path: p_lastyear});
    bgp.attr({path: bgpp});
    bgp_lastyear.attr({path: bgpp_lastyear});
    frame.toFront();
    label[0].toFront();
    label[1].toFront();
    blanket.toFront();

}

function getAnchors(p1x, p1y, p2x, p2y, p3x, p3y) {
    var l1 = (p2x - p1x) / 2,
        l2 = (p3x - p2x) / 2,
        a = Math.atan((p2x - p1x) / Math.abs(p2y - p1y)),
        b = Math.atan((p3x - p2x) / Math.abs(p2y - p3y));
    a = p1y < p2y ? Math.PI - a : a;
    b = p3y < p2y ? Math.PI - b : b;
    var alpha = Math.PI / 2 - ((a + b) % (Math.PI * 2)) / 2,
        dx1 = l1 * Math.sin(alpha + a),
        dy1 = l1 * Math.cos(alpha + a),
        dx2 = l2 * Math.sin(alpha + b),
        dy2 = l2 * Math.cos(alpha + b);
    return {
        x1: p2x - dx1,
        y1: p2y + dy1,
        x2: p2x + dx2,
        y2: p2y + dy2
    };
}