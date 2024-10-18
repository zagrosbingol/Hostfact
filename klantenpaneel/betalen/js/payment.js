$(function () {
    // Show extra info
    $("input[name='payment_method']").click(function () {

        // Hide all extra info from all methods
        $('.payment_extra_tr').hide();

        // Show only selected extra info
        if ($("input[name='payment_method']:checked").parent().parent().parent().children('.payment_extra_tr').html() != null) {
            $("input[name='payment_method']:checked").parent().parent().parent().children('.payment_extra_tr').show();
        }
    });

    $("#submit_btn").click(function () {
        if ($(this).hasClass('already_clicked')) {
            // Already clicked, skip 'second' payment
            return false;
        }

        // Add already_clicked class
        $(this).addClass('already_clicked');

        // Submit form
        $('#payment_form').submit();
        return true;
    });
});