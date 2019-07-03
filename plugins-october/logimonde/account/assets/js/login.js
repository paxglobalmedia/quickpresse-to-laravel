function loginUser(element) {
    var $form = $(element).closest('form');
    var login = $form.find('input[name="login"]').val();
    var message1 = $(element).data('activate1');
    var message2 = $(element).data('activate2');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onSignin', {
            error: function (e, data) {
                var error, cms = "as they are not activated";
                if (e.responseJSON === undefined) {
                    error = e.responseText;
                } else {
                    error = e.responseJSON.error;
                }

                if (error.indexOf(cms) !== -1) {
                    var activate = message1 + ' ' + login + ' ' + message2;
                    toastr.error(activate, null, opts);
                } else {
                    toastr.error(error, null, opts);
                }
            }
        });
    }

}