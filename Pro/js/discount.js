$(function(){
	/**
	 * Discount pages
 	 */
	if($('#DiscountForm').html() != null){
		
		// Edit only
		if($('input[name="id"]').val() != ""){

			
		}
		
		$('input[name="DiscountType"]').change(function(){
			switch($(this).val()){
				case 'TotalAmount':
					$('#type_amount').show();
					$('#type_percentage').hide();
					$('#type_product').hide();
					$('#type_partialrestrictedpercentage').hide();
					$('#type_partialpercentage').hide();
					
					$('#MaxPerInvoiceHolder').hide();
					
					$('.pricecol').hide();
					break;
				case 'TotalPercentage':
					$('#type_amount').hide();
					$('#type_percentage').show();
					$('#type_product').hide();
					$('#type_partialrestrictedpercentage').hide();
					$('#type_partialpercentage').hide();
					
					$('#MaxPerInvoiceHolder').hide();
					
					$('.pricecol').hide();
					break;
				case 'PartialRestrictedPercentage':
					$('#type_amount').hide();
					$('#type_percentage').hide();
					$('#type_product').hide();
					$('#type_partialrestrictedpercentage').show();
					$('#type_partialpercentage').hide();
					
					$('#MaxPerInvoiceHolder').show();
					
					$('.pricecol').hide();
					break;
				case 'PartialPercentage':
					$('#type_amount').hide();
					$('#type_percentage').hide();
					$('#type_product').hide();
					$('#type_partialrestrictedpercentage').hide();
					$('#type_partialpercentage').show();
					
					$('#MaxPerInvoiceHolder').show();
					
					$('.pricecol').show();
					break;
				case 'PartialAmount':
					$('#type_amount').hide();
					$('#type_percentage').hide();
					$('#type_product').show();
					$('#type_partialrestrictedpercentage').hide();
					$('#type_partialpercentage').hide();
					
					$('#MaxPerInvoiceHolder').hide();
					
					$('select[name="product_restriction"]').val('yes').change();
					$('.pricecol').show();
					break;
			}
		});
		
		$('select[name="DebtorRestriction"]').change(function(){
			switch($(this).val()){
				case 'group':
					$('#debtor_group').show();
					$('#debtor_debtor').hide();
					break;
				case 'debtor':
					$('#debtor_group').hide();
					$('#debtor_debtor').show();
					break;
				default:
					$('#debtor_group').hide();
					$('#debtor_debtor').hide();
					break;				
			}
		});
		
		$('select[name="Period"]').change(function(){
			switch($(this).val()){
				case 'always':
					$('#period_startdate').hide();
					$('#period_enddate').hide();
					break;
				case 'till':
					$('#period_startdate').hide();
					$('#period_enddate').show();
					break;
				case 'between':
					$('#period_startdate').show();
					$('#period_enddate').show();
					break;
			}
		});
		
		$('select[name="product_restriction"]').change(function(){
			switch($(this).val()){
				case 'yes':
					$('#ProductRestrictionTable').show();
					break;
				case 'no':
					$('#ProductRestrictionTable').hide();
					break;
			}	
		});
		
		$('.productrestriction').change(function(){
			
			var RestrictionNumber = $(this).attr('name').substring(0,12);
			
			switch($(this).val()){
				case 'product':
					$('input[name="' + RestrictionNumber + 'Product"]').parent('.restriction_product').show();
					$('select[name="' + RestrictionNumber + 'Group"]').hide();
					break;
				case 'group':
					$('input[name="' + RestrictionNumber + 'Product"]').parent('.restriction_product').hide();
					$('select[name="' + RestrictionNumber + 'Group"]').show();
					break;
				case 'none':
					$('input[name="' + RestrictionNumber + 'Product"]').parent('.restriction_product').hide();
					$('select[name="' + RestrictionNumber + 'Group"]').hide();
					break;				
			}	
		});
		
		if($('#delete_discount')){
			$('#delete_discount').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});
			$('input[name="agree_delete_discount"]').click(function(){
				if($('input[name="agree_delete_discount"]:checked').val() != null)
				{
					$('#delete_discount_btn').removeClass('button2').addClass('button1');
				}
				else
				{
					$('#delete_discount_btn').removeClass('button1').addClass('button2');
				}
			});
			$('#delete_discount_btn').click(function(){
				if($('input[name="agree_delete_discount"]:checked').val() != null)
				{
					document.form_delete.submit();
				}	
			});
		}
	}
});