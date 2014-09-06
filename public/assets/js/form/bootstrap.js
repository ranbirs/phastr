$(function () {

    $('form').submit(function () {

        var form = $(this);
        var request = $.ajax({url: form.attr('action'), type: form.attr('method'), data: form.serialize()});

        form.css('cursor', 'wait');
        form.find('.form-actions button').attr('disabled', 'disabled').addClass('disabled').css('cursor', 'wait');

        request.done(function (response) {

            form.css('cursor', '');
            form.find('.form-actions button').css('cursor', '');

            if (!response.expire) {
                form.find('.form-actions button').removeAttr('disabled').removeClass('disabled');
            }
            form.find('.form-group').removeClass('has-error has-success').find('input, select').removeAttr('title').tooltip('destroy');
            form.find('.message .alert').alert('close');

            if (!response.status) {
                if (response.message) {
                    form.find('.message').html('<div class="alert"></div>').find('.alert').addClass('alert-danger').html(response.message).alert();
                }
                if ('validation' in response) {
                    if ('error' in response.validation) {
                        $.each(response.validation.error, function (index, value) {
                            if (value.id) {
                                form.find('[name^="' + value.id + '"]').closest('.form-group').addClass('has-error');
                            }
                            if (value.message) {
                                form.find('[name^="' + value.id + '"]').attr('title', value.message);
                            }
                        });
                        if (form.find('.recaptcha-challenge').length) {
                            Recaptcha.reload('t');
                        }
                    }
                    if ('success' in response.validation) {
                        $.each(response.validation.success, function (index, value) {
                            if (value.id) {
                                form.find('[name^="' + value.id + '"]').closest('.form-group').addClass('has-success');
                            }
                            if (value.message) {
                                form.find('[name^="' + value.id + '"]').attr('title', value.message);
                            }
                        });
                    }
                }
            }
            else {
                if (response.message) {
                    form.find('.message').html('<div class="alert"></div>').find('.alert').addClass('alert-success').html(response.message).alert();
                }
            }
            if (response.callback) {
                window[response.callback.name].apply(null, response.callback.args);
            }
            form.find('input[title], select[title]').tooltip();
        });
        return false;
    });

});

function form_callback(args) {
    alert(args);
}
