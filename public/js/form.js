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
			form.find('.alert').empty().removeClass('alert-error alert-success');
			form.find('.help-inline').empty();
			if (data.error) {
				$.each(data.error.validate, function(index, value) {
					if (value[0]) {
						if (value[0] == 'parse') {
							form.find('.alert').addClass('alert-error').html(value[1]).slideDown();
						}
						else {
							form.addClass('error').find('.field[name^="' + value[0] + '"]').closest('.control-group').addClass('error');
						}
					}
					if (value[1]) {
						form.find('.field[name^="' + value[0] + '"]').closest('.control-group').find('.help-inline').html(value[1]).fadeIn();
					}
				});
				if (form.find('.recaptcha-challenge').length) {
					Recaptcha.reload('t');
				}
			}
			else {
				form.find('.help-inline').fadeOut();
				if (data.message) {
					form.find('.alert').addClass('alert-success').html(data.message).slideDown(function() {
						if (data.callback) {
							eval('(' + data.callback + ')');
						}
					});
				}
				else {
					if (data.callback) {
						eval('(' + data.callback + ')');
					}
				}
			}
			if (data.success) {
				$.each(data.success.validate, function(index, value) {
					if (value[0]) {
						form.addClass('error').find('.field[name^="' + value[0] + '"]').closest('.control-group').addClass('success');
					}
					if (value[1]) {
						form.find('.field[name^="' + value[0] + '"]').closest('.control-group').find('.help-inline').html(value[1]).fadeIn();
					}
				});
			}
		});
		return false;
	});
});
