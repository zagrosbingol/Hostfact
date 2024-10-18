$(function()
{
    $('#decline_modification').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
    $('#cancel_modification').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});

    // International version only
    if(IS_INTERNATIONAL == 'true' && $('form#clientarea_change_whois').length > 0)
    {
        // support for states, switch the input/select (and fill the select with states) based on the chosen country
        $(document).on('change', 'select[name="Owner[Country]"], select[name="Admin[Country]"], select[name="Tech[Country]"]', function()
        {
            var handletype = $(this).data('handletype');

            // no need to do a ajax call to get states if country has none
            if($(this).children('option:selected').data('states') != undefined && $(this).children('option:selected').data('states') == false)
            {
                $('input[name="' + handletype + '[State]"]').val('').show();
                $('select[name="' + handletype + '[StateCode]"]').html('').val('').hide();
            }
            else
            {
                $.post("XMLRequest.php", { action: "get_states", countrycode: $('select[name="' + handletype + '[Country]"]').val()}, function(data)
                {
                    if(data != undefined && data.type != undefined && data.type == 'select')
                    {
                        $('input[name="' + handletype + '[State]"]').val('').hide();
                        $('select[name="' + handletype + '[StateCode]"]').html(data.options).show();
                        $('select[name="' + handletype + '[StateCode]"]').val('');
                    }
                    else
                    {
                        $('input[name="' + handletype + '[State]"]').val('').show();
                        $('select[name="' + handletype + '[StateCode]"]').html('').val('').hide();
                    }
                }, 'json');
            }
        });

        $('select[name="Country"], select[name="InvoiceCountry"]').change(function(){

            var NamePrefix = $(this).attr('name').replace('Country', '');
            getCountryStates($(this).val(), NamePrefix, '');
        });
    }
	
});