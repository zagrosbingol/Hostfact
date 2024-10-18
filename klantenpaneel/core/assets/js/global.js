/*!
 * This is a core JS file. For customization, create a .js file in the folder /custom/assets/js/.
 * All JS files in this folder will automatically be loaded. Overwrite a file by giving it the same filename, but be aware of the need to merge files after updates (not recommended)
 */

$(function()
{
    // Call some default functions for initialising
    init_csrf_protection();
    init_live_search();

    $('textarea.autogrow').autoGrow();

    // Validate input fields
    if($('input[type="hidden"][name="validate_input"]').val())
    {
        init_validate_input();
    }

    $('#mainMenuNav').on('shown.bs.collapse', function () {
        // do something
        $('#mainMenuNav .dropdown-item.active').parents('.dropdown').addClass('open');
    });

    // Prevent double submits on forms
    $(document).on('submit', 'form', function(){

        // Prevent double submit
        if($(this).data('already_submitted') == true)
        {
           // Prevent posting form
           return false;
        }

        // Set attribute
        $(this).data('already_submitted', true);
        return true;
    });

    // Prevent double clicks on a-buttons
    $(document).on('click', 'a.btn:not(.no-disable)', function(event) {

        // Prevent double click
        if($(this).data('already_clicked') == true)
        {
            // Prevent default action
            event.preventDefault();
            return false;
        }

        // Set attribute
        $(this).data('already_clicked', true);

        // Make spinner
        if($(this).data('spinner'))
        {
            $(this).find('.fa').addClass('fa-spinner fa-pulse');
        }
        return true;
    });

    // Post modal forms with AJAX
    $(document).on('submit', '.modal form', function(){

        var ModalObject = $(this).parents('.modal');

        $.post($(this).attr('action'), $(this).serialize(), function(data){
            if(data == 'reload')
            {
                // Reload page
                location.href=location.href;
            }
            else
            {
                // Refill modal content
                $(ModalObject).find('.modal-content').html($(data).find('.modal-content').html());

                // Check for form errors
                init_validate_input();

                // Check for textarea grows
                $('textarea.autogrow').autoGrow();
            }
        }, "html");

        // Prevent normal posting
        return false;
    });

    // Redirect to download-link
    if($('#force_download').html() != null){ setTimeout(function(){location.href = $('#force_download').attr('href')},300); }

    // Calculate height of scrollable sidebar list
    setTimeout(function(){ init_scrollable_sidebar(); }, 100);
    $( window ).resize(function() {
        init_scrollable_sidebar();
    });

});

function init_csrf_protection()
{
    // Adjust all <form>-tags
    $('form').each(function(index){
        $(this).prepend('<input type="hidden" name="CSRFtoken" value="' + CSRFtoken + '" />');
    });

    // Adjust all AJAX calls
    $(document).bind("ajaxSend", function(elm, xhr, s){
        if(s.type == "POST")
        {
            if(s.context)
            {
                s.context.data = (s.context.data.indexOf('CSRFtoken=') == -1) ? 'CSRFtoken=' + CSRFtoken + '&' + s.context.data : s.context.data;
            }
            else
            {
                s.data = (s.data.indexOf('CSRFtoken=') == -1) ? 'CSRFtoken=' + CSRFtoken + '&' + s.data : s.data;
            }
        }
    });
}

function init_live_search()
{
    // Place icon in search input
    $('#list-search').after('<span class="fas fa-times fa-lg fa-fw hide"></span>');

    // Prevent submitting form
    $('#list-search').parents('form').submit(function(){ event.preventDefault(); return false; })

    // Depending on current search value, show searching class.
    var searchTerm = $('#list-search').val();
    if(searchTerm != undefined && searchTerm != '')
    {
        searchTerm = searchTerm.toLowerCase();
        $('#list-search').addClass('searching');
    }

    // live search for list
    $('#list-search').on('keyup change', function(event)
    {
        var searchTerm = $(this).val();
        var resultCount = 0;

        if(searchTerm == '')
        {
            // Hide searching class
            $(this).removeClass('searching');

            // Hide extended search link (if in DOM)
            $('a[data-event="extended-search"]').parent().addClass('hide');

            // Search on response view. collapse again
            $('#list-items .list-group').removeClass('in');
        }
        else
        {
            // Show searching class, user is able to delete search input
            $(this).addClass('searching');

            // Show extended search link (if in DOM)
            $('a[data-event="extended-search"]').parent().removeClass('hide');

            // Search on response view, expand collapse option
            $('#list-items .list-group').addClass('in');
        }

        // When we are in the extended mode, no filtering
        if($('#list-search').parents('form').data('searchtype') == 'extended')
        {
            // Reset the extended search when input field is complete try
            if(searchTerm == '')
            {
                $('a[data-event="extended-search"]').click();
            }
            return;
        }

        // loop through list for filtering
        $('#list-items .list-group a').each(function()
        {
            // Skip noresult text and list items without search term
            if($(this).attr('id') == 'list-search-noresult' || $(this).is('[data-search-term]') === false)
            {
                return;
            }

            // Search in data attribute
            if(searchTerm.length < 1 || $(this).filter('[data-search-term *= ' + searchTerm.toLowerCase() + ']').length > 0)
            {
                $(this).show();
                resultCount++;
            }
            else
            {
                $(this).hide();
            }
        });

        if(resultCount == 0)
        {
            $('#list-items #list-search-noresult').removeClass('hide');
        }
        else
        {
            $('#list-items #list-search-noresult').addClass('hide');
        }

        $('#list-items .result-count').html(resultCount);

        // Store search after onchange, so reloading page keeps result
        if(event.type == 'change')
        {
            $.post($('#list-search').parents('form').attr('action') + '/ajaxSaveFilter', { listID: $('#list-items .list-group').attr('id'), search: searchTerm});
        }
    });
    // Pasting search takes some time
    $('#list-search').on('paste', function(e)
    {
        var self = this;
        setTimeout(function(e) {
            $(self).change();
        }, 0);
    });

    $('#list-search + span').on('click', function()
    {
        // Empty search field and trigger keyup
        $('#list-search').val('');

        if($('#list-search').parents('form').data('searchtype') == 'extended')
        {
            $('a[data-event="extended-search"]').click();
        }
        else
        {
            $('#list-search').change();
        }
    });

    // When we have input in search field and are on filter mode, initialize filtering
    if($('#list-search').val() != '' && $('#list-search').parents('form').data('searchtype') != 'extended')
    {

        // Already filter
        $('#list-search').keyup();
    }
    // On extended search, remove collapse option
    else if($('#list-search').val() != '')
    {
        // Search on response view, expand collapse option
        $('#list-items .list-group').addClass('in');
    }

    // Extended search listener
    $(document).on('click', 'a[data-event="extended-search"]', function(){

        // Store search term and list, so after a reload, the search is still there
        var searchTerm = $('#list-search').val();
        var listID = $('#list-items .list-group').attr('id');

        // Look for active item, so we can highlight it after searching
        var activeID = ($('#list-items .list-group a.active').attr('id')) ? $('#list-items .list-group a.active').attr('id').replace('item', '') : false;

        // Clear current search results and show loader...
        $('#' + listID).html('<a class="list-group-item text-xs-center"><span class="fas fa-spinner fa-lg fa-pulse"></span></a>');

        // Hide extended search link
        $(this).parent().addClass('hide');

        // Change extended field, based on input value
        if(searchTerm == '')
        {
            // Switch back to filtering mode, showing all results
            $('#list-search').parents('form').data('searchtype', 'filter');
        }
        else
        {
            // Switch/stay on extended mode, no filtering allowed
            $('#list-search').parents('form').data('searchtype', 'extended');
        }

        // Save and store search input in cache and reload div
        $.post($('#list-search').parents('form').attr('action') + '/ajaxExtendedSearch', { listID: listID, search: searchTerm, activeID: activeID}, function(data){

            // Change the HTML in the search container.
            $('#list-search-container').html($(data).find('#list-search-container').html());

            // On response view, expand collapse option
            $('#list-items .list-group').addClass('in');

            // Refresh the scrollable sidebar
            setTimeout(function(){ init_scrollable_sidebar(); }, 100);

        }, 'html');

    });
}

function init_validate_input()
{
    if($('input[type="hidden"][name="validate_input"]').length > 0)
    {
        var fields = $('input[type="hidden"][name="validate_input"]').val().split(',');

        $(fields).each(function(index, fieldname)
        {
            // Add danger class
            $('input[name="'+fieldname+'"][type!="checkbox"][type!="radio"], textarea[name="'+fieldname+'"], select[name="'+fieldname+'"]').parent().addClass('has-danger');

            // Add danger class on checkboxes
            $('input[name="'+fieldname+'"][type="checkbox"], input[name="'+fieldname+'"][type="radio"]').parents('.checkbox').addClass('has-danger');
        });
    }
}

function init_scrollable_sidebar()
{
    if($('.sidebar').find('.scrollable-list').html() == undefined)
    {
        return;
    }

    var ScrollableList = $('.sidebar').find('.scrollable-list');
    // Get height for content part and calculate offset of current item
    var Height = $('.main-content').parent('div').height();
    var Offset = ScrollableList.offset().top - $('.sidebar').offset().top;

    // If content div is smaller than window size, max-size will be window size
    var WindowHeight = $(window).height() - ScrollableList.offset().top - (1*$('.sidebar').parents('.container').css('margin-bottom').replace('px', '')) - 1;

    // If height of content div is too small, look for window height
    if((Height - Offset) < WindowHeight)
    {
        ScrollableList.css('max-height', WindowHeight + 'px');
    }
    else
    {
        ScrollableList.css('max-height', (Height - Offset) + 'px');
    }

    if(ScrollableList.children('.active').length == 1)
    {
        // only scroll to active item when it is below the fold, this prevents scrolling when one of the top items is active
        if(ScrollableList.children('.active').offset().top - ScrollableList.offset().top > ScrollableList.height())
        {
            // scroll to active item
            ScrollableList.scrollTop(
                ScrollableList.children('.active').offset().top - ScrollableList.offset().top + ScrollableList.scrollTop()
            );
        }
    }
}

function normalize(str)
{
    if(typeof(str) == "string")
    {
        str = str.replace(/&/g, "&amp;"); // must do &amp; first
        str = str.replace(/"/g, "&quot;");
        str = str.replace(/'/g, "&#039;");
        str = str.replace(/</g, "&lt;");
        str = str.replace(/>/g, "&gt;");
    }
    return str;
}

function generatePassword(element, url)
{
    $.get(url,
        function(data)
        {
            if(element.is('input'))
            {
                element.val(data.password);
                if(element.attr('type') == 'password')
                {
                    element.attr('type', 'text');
                }
            }
            else
            {
                element.text(data.password);
            }
        }
        , "json"
    );
    return false;
}

/* jquery autogrowtextarea plugin */
jQuery.fn.autoGrow = function(){
    return this.each(function(){

        //Functions
        var keyPress = function(event) {

            // reset height for delete and backspace key
            if([8, 46].indexOf(event.keyCode) != -1)
            {
                $(this).css('min-height', '0px');
            }

            $(this).css('min-height', $(this).get(0).scrollHeight + 'px');

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

            $(element).css('min-height', height + 'px');
        }

        $(this).css('resize', 'none');
        $(this).css('overflow-y', 'hidden');
        $(this).css('min-height', '30px');
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