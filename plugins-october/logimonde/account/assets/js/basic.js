function saveRegisterUser(element) {
    var $form = $(element).closest('form');
    //var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onRegisterUser', {
            update: {'BasicRegister::_final_step': '#profile-body'}
        });
    }

}
