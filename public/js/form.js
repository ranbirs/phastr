$(function () {

	$('form').submit(function () {
		var fid = $(this).attr('id');
		$('body, form').css('cursor', 'wait');
		$(this).find('.form-actions button').attr('disabled', 'disabled').addClass('disabled').css('cursor', 'wait');
		$.post($(this).attr('action'), $(this).serialize(), function (data) {
			$('body, form').css('cursor', '');
			$('#' + fid).find('.form-actions button').removeAttr('disabled').removeClass('disabled').css('cursor', '');
			$('#' + fid).removeClass('error success').find('.control-group').removeClass('error success');
			$('#' + fid).find('.alert').empty().removeClass('alert-error alert-success');
			$('#' + fid).find('.help-inline').empty();
			if (data.error) {
				$.each(data.error.validate, function(index, value) {
					if (value[0]) {
						if (value[0] == 'parse') {
							$('#' + fid).find('.alert').addClass('alert-error').html(value[1]).slideDown();
						}
						else {
							$('#' + fid).addClass('error').find('.field[name^="' + value[0] + '"]').closest('.control-group').addClass('error');
						}
					}
					if (value[1]) {
						$('#' + fid).find('.field[name^="' + value[0] + '"]').closest('.control-group').find('.help-inline').html(value[1]).fadeIn();
					}
				});
				if ($('#' + fid + ' .recaptcha-challenge').length) {
					Recaptcha.reload('t');
				}
			}
			else {
				$('#' + fid).find('.help-inline').fadeOut();
				if (data.message) {
					$('#' + fid).find('.alert').addClass('alert-success').html(data.message).slideDown(function() {
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
						$('#' + fid).addClass('error').find('.field[name^="' + value[0] + '"]').closest('.control-group').addClass('success');
					}
					if (value[1]) {
						$('#' + fid).find('.field[name^="' + value[0] + '"]').closest('.control-group').find('.help-inline').html(value[1]).fadeIn();
					}
				});
			}
		});
		return false;
	});
});
