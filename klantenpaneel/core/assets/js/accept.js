var PricequoteAccept =
{
	// Check if all mandatory inputs are filled in.
	checkInputs: function()
	{
		var name = $('input[name=name]').val();
		var email = $('input[name=email]').val();
		var signatureModified = $('#signature').jSignature("isModified");
        var terms = ($('input[name=terms]').val() != undefined) ? $('input[name=terms]').is(':checked') : true;

		if(name.length !== 0 && email.length !== 0 && signatureModified == true && terms == true)
		{
			$('form[name="accept_pricequote"] #submit_accept_button').removeClass('disabled').removeAttr('disabled');
		}
		else
		{
			$('form[name="accept_pricequote"] #submit_accept_button').addClass('disabled').attr('disabled', 'disabled');
		}
	},

	// Resize function
	resizeCanvas: function ()
	{
		$('#signature').jSignature("reset");
		PricequoteAccept.checkInputs();
	}
};

$(function()
{
	if(document.getElementById('signature') != null)
	{
		// Init
		// Remove background, signature line and set height
		$('form[name="accept_pricequote"] #signature').jSignature({
			'background-color': 'transparent',
			'decor-color': 'transparent',
			'height' : '155',
			'width' : '100%'
		});

		$('form[name="accept_pricequote"] #clear').click(function()
		{
			$('#signature').jSignature("reset");
			$('.signature-clear').hide();
			$('.signature-placeholder').show();
			PricequoteAccept.checkInputs();
		});

		var originalWindowWidth = $(window).width();
		$(window).resize(function() {
			if ($(window).width() != originalWindowWidth) {
				PricequoteAccept.resizeCanvas();
				originalWindowWidth = $(window).width();
			}
		});

		$('form[name="accept_pricequote"] #submit_accept_button').click(function()
		{
			var svg_data = $('#signature').jSignature("getData","image/svg+xml");

			if($('input[name="signature"]').val(svg_data[1]))
			{
				// Get canvas size to fix svg document size
				$('input[name="signature_size"]').val($('#signature').width() + ';' + $('#signature').height());

				$('form[name="accept_pricequote"]').submit();
			}
		});

		$('form[name="accept_pricequote"] #signature').bind('click mousedown touchstart', function(e)
		{
			if($('#signature').jSignature("isModified"))
			{
				$('.signature-clear').show();
				$('.signature-placeholder').hide();
				PricequoteAccept.checkInputs();
			}
			else
			{
				$('.signature-clear').hide();
				$('.signature-placeholder').show();
				PricequoteAccept.checkInputs();
			}
		});

        $('form[name="accept_pricequote"] input[name="name"], form[name="accept_pricequote"] input[name="email"]').keyup(function () {
            PricequoteAccept.checkInputs();
        });

        $('form[name="accept_pricequote"] input[name="terms"]').change(function () {
            PricequoteAccept.checkInputs();
        });
	}
});