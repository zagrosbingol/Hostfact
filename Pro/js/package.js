$(function(){
    /**
     * Package pages
     */

    $('select[name="EmailTemplate"]').change(function(){
        if($(this).val() > 0){
            $('#hosting_pdf_email_div').slideDown();
        }else{
            $('#hosting_pdf_email_div').slideUp();
        }
    });
});