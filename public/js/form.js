$(function () {

	$('form').submit(function () {
		var form = $(this);
		$('body, form').css('cursor', 'wait');
		form.find('.form-actions button').attr('disabled', 'disabled').addClass('disabled').css('cursor', 'wait');

		var request = $.ajax({url: form.attr('action'), type: form.attr('method'), data: form.serialize()});
		request.done(function (data) {
			$('body, form').css('cursor', '');
			form.find('.form-actions button').removeAttr('disabled').removeClass('disabled').css('cursor', '');
			form.find('.form-group').removeClass('has-error has-success');
			form.find('.message .alert').alert('close');
			form.find('.form-group').find('.controls[data-toggle="popover"]').popover('destroy');
			//form.find('.help').empty().hide();

			if (data.result == false) {
				if (data.message) {
					form.find('.message').html('<div class="alert"></div>').find('.alert').addClass('alert-danger').html(data.message).alert();
				}
				if ('validation' in data) {
					if ('error' in data.validation) {
						$.each(data.validation.error, function(index, value) {
							if (value[0]) {
								form.find('.form-control[name^="' + value[0] + '"]').closest('.form-group').addClass('has-error');
							}
							if (value[1]) {
								form.find('.form-control[name^="' + value[0] + '"]').closest('.form-group').find('.controls').attr('data-content', value[1]).popover('show');
							}
						});
						if (form.find('.recaptcha-challenge').length) {
							Recaptcha.reload('t');
						}
					}
					if ('success' in data.validation) {
						$.each(data.validation.success, function(index, value) {
							if (value[0]) {
								form.find('.form-control[name^="' + value[0] + '"]').closest('.form-group').addClass('has-success');
							}
							if (value[1]) {
								form.find('.form-control[name^="' + value[0] + '"]').closest('.form-group').find('.controls').attr('data-content', value[1]).popover('show');
							}
						});
					}
				}
			}
			else {
				if (data.message) {
					form.find('.message').html('<div class="alert"></div>').find('.alert').addClass('alert-success').html(data.message).alert();
				}
			}
			if (data.callback) {
				eval('(' + data.callback + ')');
			}
		});
		return false;
	});
});
