function shareQuickPresse(element) {
    var $form = $(element).closest('form');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onShareEmail', {
            success: function (data) {
                toastr.success(data.message, null, opts);
            }
        });
    }
}