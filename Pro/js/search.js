$(function(){
	var selectedUrl = '';	
	patchAutocomplete();

	$('#SearchInput').catcomplete({
		delay: 500,
		minLength: 1,
		source: 'search.php?page=autocomplete',
		search: function( event, ui ) { $('.search_block_loading').show(); },
		position: {
	        my: 'right top',
	        at: 'right bottom',
	        of: '.search_block'
    	},
		close: function(){
			
			if($('#SearchInput').val() == __LANG_SEARCH_VALUE)
			{
				$('#SearchInput').val('');
				return false;
			}else if($('li.autocomp_item').length > 1 && selectedUrl && selectedUrl.label == $('#SearchInput').val()){
				location.href = selectedUrl.url;
				return false;
			}
		},
		focus: function(event, ui){
			selectedUrl = ui.item;
		}
	});
	
	$('input[name=SearchInput]').keypress(function(event){
		if(event.keyCode == 13){
			// If we only have one item, go directly to it
			if($('li.autocomp_item').length == 1)
			{
				location.href = LastSearchedItem.url;
				return false;
			}
			return true;
		}
	});
    
    $('input[name=SearchInput]').focus( function()
    {
        if($(this).val() != '')
        {
            $('#SearchInput').catcomplete('widget').show();    
        }
    });

});

$.widget('custom.catcomplete', $.ui.autocomplete, {
    _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category):not(.ui-autocomplete-searchall):not(.ui-autocomplete-no-results)" );
    },
    _renderMenu: function(ul, items){
		var self = this, currentCategory = '';

		$('.search_block_loading').hide();
		
		count = 0;
		subindex = 0;
		
		// Hide if we backspaced the input
		if($('#SearchInput').val() == '')
        {
            $('.ui-autocomplete').catcomplete('widget').hide();   
			return false; 
        }

		if(items[0] && items[0].error != undefined)
		{
			location.href = 'login.php';
		}
		else if(items[0] && items[0].category == 'no results'){
			$('.ui-menu').css('cssText','background:#FFF !important');
			
			ul.append('<li class="ui-autocomplete-no-results">' + items[0].label + '</li>');
			
			ul.append('<li class="ui-autocomplete-searchall"><a class="pointer" onclick="document.HeaderSearchForm.submit()">' + __LANG_SEARCH_ALL_RESULTs + '</a></li>');
		}else{
			$('.ui-menu').css('cssText','background: #F9F9F9 !important');

			$.each(items, function(index, item){
				subindex++;
				
				var seoCategory = item.category.replace(' ','');
				
				if((item.category != currentCategory) && items.length > 1){
					if(currentCategory != ''){
                        ul.append('<li class="ui-autocomplete-spacer_left">&nbsp;</li>');
                        ul.append('<li class="ui-autocomplete-spacer">&nbsp;</li>');
					}
					
					ul.append('<li class="ui-autocomplete-category" id="search-category-' + seoCategory + '"></li>');
					currentCategory = item.category;
					
					subindex = 0;
					count = 0;
					$('#search-category-' + seoCategory).html(item.category + ' (' + item.category_count + ')');
				}else{
					if(items.length == 1){
						subindex = 0;
					}
					
					ul.append('<li class="ui-autocomplete-category" id="search-category-' + seoCategory + '"></li>');
					
					$('#search-category-' + seoCategory).html(item.category + ' (' + item.category_count + ')');
				}
				
				self._renderItemData( ul, item, subindex);

				if(index == items.length-1){
					
					ul.append('<li class="ui-autocomplete-searchall"><a class="pointer" onclick="document.HeaderSearchForm.submit()">' + __LANG_SEARCH_ALL_RESULTs + '</a></li>');
				}
			});	
		}
	}

});

var LastSearchedItem;
function patchAutocomplete() {


	$.ui.autocomplete.prototype._renderItem = function( ul, item, subindex) {
		var re = new RegExp('^' + escape('[' + this.term + ']'), 'i');
		var t = item.label.replace(re,'<span style="color:#058ac4;">' + this.term + '</span>');
		
		// Keep track of last found item
		LastSearchedItem = item;

		if(subindex == 0){
			return $('<li class="first autocomp_item"></li>')
				.data( 'item.autocomplete', item )
				.append( '<a onclick="location.href = \'' + item.url + '\'">' + htmlspecialchars(t) + '</a>' )
				.appendTo( ul );
		}else{
			return $('<li class="autocomp_item"></li>')
				.data( 'item.autocomplete', item )
				.append( '<a onclick="location.href = \'' + item.url + '\'">' + htmlspecialchars(t) + '</a>' )
				.appendTo( ul );
		}
  };
}
