$(function()
{
    $('.two-factor-auth-link').click(function()
    {
        $('.two-factor-auth-link').css('visibility', 'hidden');

        $.get($(this).data('baseurl'),
            function(data)
            {
                $('#two-factor-auth-code .auth_key').text(data.auth_key);
                $('#two-factor-auth-code input[name="authenticator_key"]').val(data.auth_key);
                $('#two-factor-auth-code img.qr_code').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&chld=M|0&cht=qr&chl=' + data.qr_url);
                $('#two-factor-auth-code input[name="verify_result"]').val('');
                $('#two-factor-auth-code').removeClass('hide');
            }
            , "json"
        );
    });

    $('input[name=authCode]').keyup( function()
    {
       if($(this).val().length >= 6)
       {
           $('#submit_accept_button').removeClass('disabled');
       }
       else
       {
           $('#submit_accept_button').addClass('disabled');
       }
    });
});