/**
 * Created by jclora on 2016-02-09.
 */
function saveUserForm(element) {
    var $form = $(element).closest('form');
    var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onUpdate', {
            //redirect: redirect
        });
    }

}

function saveUpdatePassword(element) {
    var $form = $(element).closest('form');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onPasswordUpdate', {
        });
    }

}