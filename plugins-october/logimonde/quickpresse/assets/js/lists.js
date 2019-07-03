$(function () {
    importFileContacts();

});

function sendFormList(element) {
    var $form = $(element).closest('form');
    var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onSaveListSettings', {
            redirect: redirect
        });
    }
}

function importFileContacts() {
    if ($('#import-file-contacts').length > 0) {
        var buttonText = $('#browse-file-text').data('start-message');

        $('#import-file-contacts').fileupload({
            dataType: 'json',
            maxFileSize: 3000000,
            start: function () {
                $('#browse-file-text').text(buttonText);
                $('#import-file-contacts').prop('disabled', true);
                $('.btn-file').removeClass('btn-primary').addClass('btn-secondary');
                $('.btn-file i.fa').removeClass('fa-upload').addClass('fa-spinner fa-pulse');
            },
            done: function (e, data) {
                $(data.result.fields.element).html(data.result.fields.html);
                $('#browse-file-text').text(buttonText);
                $('#import-file-contacts').prop('disabled', false);
                $('.btn-file').removeClass('btn-secondary').addClass('btn-primary');
                $('.btn-file i.fa').removeClass('fa-spinner fa-pulse').addClass('fa-upload');
            },
            always: function (e, data) {
                if (data.result !== undefined) {
                    toastr.success(data.result.success, null, opts);
                } else {
                    var message = data.jqXHR.responseText.split(/"/)[1];
                    toastr.error(message, null, opts);
                }
                $('#browse-file-text').text(buttonText);
                $('.btn-file').removeClass('btn-secondary').addClass('btn-primary');
                $('.btn-file i.fa').removeClass('fa-spinner fa-pulse').addClass('fa-upload');
            }
        });
    }
}

function saveImportedData(element) {
    var $form = $(element).closest('form');
    var redirect = $form.data('redirect');
    var message = $(element).data('message');
    $('#loader').css('display', 'table').show().find('.indicator-message').text(message);
    $form.on('ajaxError ajaxErrorMessage ajaxSuccess ajaxFail', function() {
        $('#loader').hide();
    });
    $form.request('onSaveImportedData', {
        redirect: redirect,
        error: function (e, data) {
            $('#loader').hide();
            if (e.responseJSON === undefined) {
                toastr.error(e.responseText, null, opts);
            } else {
                toastr.error(e.responseJSON.error, null, opts);
            }
        }
    });
}

function saveContactForm(element) {
    var $form = $(element).closest('form');
    var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onSaveContactSettings', {
            redirect: redirect
        });
    }
}

function setIncludeField(element) {
    var $wrap = $(element).closest('.column-field');
    var value = $(element).val();
    if (value !== '') {
        $wrap.find('.checkbox-include').prop('disabled', false);
    } else {
        $wrap.find('.checkbox-include').prop('disabled', true);
    }
}