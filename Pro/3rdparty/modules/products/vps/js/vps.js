// Load packages
function getNodePackages(node_id, also_existing, package_id)
{
	// Skip function if not needed
	if(node_id == undefined || node_id == 0)
    {
		return true;
	}
	
	$('select[name="module[vps][TemplateName]"]').hide();
	$('#vps_div_template').html('<img src="images/indicator.gif" alt="" />').show();

    $.post('modules.php?module=vps&page=ajax_get_node_templates', { id: node_id, also_existing: also_existing },
		function(data)
        {
            // used by products
			if(also_existing == true)
            {
                if(data.existing != undefined && data.existing.length > 0)
                {
    				var options = '<optgroup label="'+__LANG_PACKAGE_GROUP_IN_SOFTWARE+'">';
    				for (var i = 0; i < data.existing.length; i++)
                    {
    					options += '<option value="ex:' + data.existing[i].id + '">' + data.existing[i].PackageName + '</option>';
    				}
                }
				
				options += '</optgroup>';
				if(data.newtemplate.resultSet != undefined)
                {
                    options += '<optgroup label="'+__LANG_PACKAGE_GROUP_ON_SERVER+'">';
	                for (var i = 0; i < data.newtemplate.resultSet.length; i++) 
                    {
	                  options += '<option value="' + data.newtemplate.resultSet[i].templateid + '">' + data.newtemplate.resultSet[i].templatename + '</option>';
	                }
                    options += '</optgroup>';
                }
                
                // if node has no packages/templates
                if((data.newtemplate.resultSet == undefined || data.newtemplate.resultSet.length == 0) && (data.existing == undefined || data.existing.length == 0))
                {
                    $('#vps_div_no_templates').show();
                    $('select[name="module[vps][TemplateName]"]').hide();    
                }
                else
                {
                    $('#vps_div_no_templates').hide();
                    $('select[name="module[vps][TemplateName]"]').html(options).show();    
                }
            	
				$('#vps_div_template').hide();

				if($('input[type=hidden][name="module[vps][TemplateID]"]').val())
                {
                    var current_package_name = $('select[name="module[vps][TemplateName]"]').find('[data-id="' + $('input[type=hidden][name="module[vps][TemplateID]"]').val() + '"]').html();
            		$('select[name="module[vps][TemplateName]"]').val(current_package_name).change();
            	}
                else
                {
            		$('select[name="module[vps][TemplateName]"]').change();
            	}

                getNodeImages(node_id);
			}
            else
            {
				// If we also want existing packages, but they don't exist
				if(also_existing == true)
                {
					data = data.newtemplate;
				}
				
				// Only new elements
				if(data.resultSet == undefined && data.errorSet != undefined)
                {
					$('#vps_div_template').html(data.errorSet[0].Message).show();	
					$('select[name="module[vps][TemplateName]"]').html('').hide();
                    
                    if(data.CustomPackageFields != undefined)
                    {
                        if(package_id == undefined || package_id == 0)
                        {
                            $('#extra_custom_fields').html(buildCustomFieldsHtml(data.CustomPackageFields));
                        }
                        else
                        {
                            // get package extra custom fields data
                            $.post('modules.php?module=vps&page=ajax_get_package_info', { package_id: package_id },
                    		function(data2)
                            {
                                $('#extra_custom_fields').html(buildCustomFieldsHtml(data.CustomPackageFields, data2.CustomFields));
                                
                            },'json');    
                        }
                    }
					
					$('#properties_vps_package_none').hide();
					$('#properties_vps_package_custom').show();
					$('#properties_vps_package').hide();					
				}
                else if(data.errorSet == undefined)
                {
	                var options = '';
	                for (var i = 0; i < data.resultSet.length; i++)
                    { 
                        options += '<option value="' + data.resultSet[i].templatename + '" data-id="' + data.resultSet[i].templateid + '">';
                        options += data.resultSet[i].templatename + '</option>';
	                }
	            
	            	$('select[name="module[vps][TemplateName]"]').html(options).show();
	            	$('#vps_div_template').hide();
                    
                    if($('input[type=hidden][name="module[vps][TemplateID]"]').val())
                    {
                        var current_package_name = $('select[name="module[vps][TemplateName]"]').find('[data-id="' + $('input[type=hidden][name="module[vps][TemplateID]"]').val() + '"]').html();
	            		$('select[name="module[vps][TemplateName]"]').val(current_package_name).change();
	            	}
                    else
                    {
	            		$('select[name="module[vps][TemplateName]"]').change();
	            	}
				}
			}
	   }
    , "json");
}

function getNodeImages(node_id)
{
    // Skip function if not needed
	if(node_id == undefined || node_id == 0)
    {
		return true;
	}
    
    $('select[name="module[vps][DefaultImage]"]').html('').hide();
    $('#vps_div_image').html('<img src="images/indicator.gif" alt="" />').show();
	
    $.post('modules.php?module=vps&page=ajax_get_node_images', { node_id: node_id}, function(data)
    {
        if(data.message)
        {
            $('#vps_div_image').html(data.message);
        }
        else
        {
            $('select[name="module[vps][DefaultImage]"]').append('<option value="">' + __LANG_PLEASE_CHOOSE + '</option>');
            $.each(data, function( key, image ) 
            {
                $('select[name="module[vps][DefaultImage]"]').append('<option value="' + image.Key + '">' + image.ImageName + '</option>');
            });
            
            $('#vps_div_image').hide();
            $('select[name="module[vps][DefaultImage]"]').show();
        }
    },'json');
}

/**
 * buildCustomFieldsHtml
 * builds string with html form fields based on the version.php of the node integration
 * 
 * @param json      fields          contains the json string from the node integration version.php (custom_package_fields)
 * @param json      package_data    optional - contains the data from a package (eg on edit page)   
 * @return string   fields_html     string which contains html form fields
 */
function buildCustomFieldsHtml(fields, package_data)
{    
    var fields_html = '';
 
    $.each(fields, function( key, field ) 
    {
        // set the data if we are on an edit page
        if(package_data != undefined)
        {
            var input_value = package_data[key];                                        
        }
        else
        {
            var input_value = (field.default) ? field.default : '';    
        }
                
        switch(field.type)
        {
            case 'input':
                fields_html += '<strong class="title2_input">' + field.label + '</strong>';
                fields_html += '<span class="title2_value">';
                fields_html += '<input type="text" class="text1 size2" name="module[vps][Custom][' + key + ']" value="' + input_value + '" />';
                fields_html += '</span>';
                if(field.description)
                {
                    fields_html += '<strong class="title2_input">&nbsp;</strong><span class="title2_value">' + field.description + '</span>';
                }
            break;
            
            case 'select':                
                fields_html += '<strong class="title2_input">' + field.label + '</strong>';
                fields_html += '<span class="title2_value">';
                fields_html += '<select class="text1 size4f" name="module[vps][Custom][' + key + ']">';
                $.each(field.options, function( value, label ) 
                {
                    var selected = (input_value == value) ? 'selected="selected"' : '';
                    fields_html += '<option value="' + value + '" ' + selected + '>' + label + '</option>';
                });
                fields_html += '</select>';
                fields_html += '</span>';
                if(field.description)
                {
                    fields_html += '<strong class="title2_input">&nbsp;</strong><span class="title2_value">' + field.description + '</span>';
                }
            break;
        }
    });
    
    return fields_html;
}