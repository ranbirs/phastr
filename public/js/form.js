$(function () {

	$('form').submit(function () {
		var form = $(this);
		$('body, form').css('cursor', 'wait');
		form.find('.form-actions button').attr('disabled', 'disabled').addClass('disabled').css('cursor', 'wait');

		var request = $.ajax({url: form.attr('action'), type: form.attr('method'), data: form.serialize()});
		request.done(function (data) {
			$('body, form').css('cursor', '');
			form.find('.form-actions button').removeAttr('disabled').removeClass('disabled').css('cursor', '');
			form.removeClass('error success').find('.control-group').removeClass('error success');
			form.find('.alert').empty().removeClass('alert-error alert-success').hide();
			form.find('.help').empty().hide();

			if (data.result == false) {
				if (data.validation.error) {
					$.each(data.validation.error, function(index, value) {
						if (value[0]) {
							form.addClass('error').find('.field[name^="' + value[0] + '"]').closest('.control-group').addClass('error');
						}
						if (value[1]) {
							form.find('.field[name^="' + value[0] + '"]').closest('.control-group').find('.help').html(value[1]).show();
						}
					});
					if (form.find('.recaptcha-challenge').length) {
						Recaptcha.reload('t');
					}
				}
				if (data.validation.success) {
					$.each(data.validation.success, function(index, value) {
						if (value[0]) {
							form.addClass('error').find('.field[name^="' + value[0] + '"]').closest('.control-group').addClass('success');
						}
						if (value[1]) {
							form.find('.field[name^="' + value[0] + '"]').closest('.control-group').find('.help').html(value[1]).show();
						}
					});
				}
				if (data.message) {
					form.find('.alert').addClass('alert-error').html(data.message).slideDown();
				}
			}
			else {
				if (data.message) {
					form.find('.alert').addClass('alert-success').html(data.message).slideDown();
				}
			}
			if (data.callback) {
				eval('(' + data.callback + ')');
			}
		});
		return false;
	});
});
