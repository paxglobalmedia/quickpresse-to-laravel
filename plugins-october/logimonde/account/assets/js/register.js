uploadLogo();

/*
 * Preview image on upload
 */

function readURL(input, image) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(image).attr('src', e.target.result);

        };

        reader.readAsDataURL(input.files[0]);
    }
}

function saveRegisterStep1(element) {
    var $form = $(element).closest('form');
    //var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onRegisterUser', {
            update: {'UserRegister::_step2': '#step2'},
            beforeUpdate: function () {
                $('#nav-item-step-2').removeClass('disabled').find('a.nav-link').removeClass('disabled');
                $('#user-tab a[href="#step2"]').tab('show');
                $('#nav-item-step-1').addClass('disabled').find('a.nav-link').addClass('disabled');
            },
            complete: function () {
                window.onbeforeunload = function () {
                    return '';
                }
                requiredFields();
            }
        });
    }

}

function saveRegisterStep2(element) {
    var $form = $(element).closest('form');
    //var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onUpdateUserData', {
            update: {'UserRegister::_step3': '#step3'},
            beforeUpdate: function () {
                $('#nav-item-step-3').removeClass('disabled').find('a.nav-link').removeClass('disabled');
                $('#user-tab a[href="#step3"]').tab('show');
                $('#nav-item-step-2').addClass('disabled').find('a.nav-link').addClass('disabled');
            },
            complete: function () {
                requiredFields();
                uploadLogo();
                getCity();
            }
        });
    }
}

function uploadLogo() {
    $('#upload-logo-file-en, #upload-logo-file-fr').fileupload({
        dataType: 'json',
        maxFileSize: 3000000,
        submit: function (e, data) {
            var form = $(this).closest('form');
            var lang = $(this).data('lang');
            if (lang !== undefined) {
                form = form.serializeArray();
                form = form.concat([
                    {name: "logo_lang", value: lang}
                ]);
                data.formData = form;
            }
        },
        done: function (e, data) {
            //console.log(data);
            var reader = new FileReader();
            reader.readAsDataURL(data.files[0]);
            reader.onload = function (e) {
                $('.upload-box figure img.logo-' + data.result.lang).removeClass('d-none')
                    .attr('title', data.files[0].name)
                    .attr('src', e.target.result)
                    .attr('width', '100%');
            };
            $('.upload-box figure figcaption.logo-' + data.result.lang).hide();
        },
        always: function (e, data) {
            if (data.result !== undefined) {
                $('.upload-box input[name="logo_name_' + data.result.lang + '"]').val(data.result.path + data.result.filename);
                toastr.success(data.result.success, null, opts);
            } else {
                var message = data.jqXHR.responseText.split(/"/)[1];
                toastr.error(message, null, opts);
            }
        }
    });

}

function saveRegisterStep3(element) {
    var $form = $(element).closest('form');
    var logo_en = $form.find('input[name="logo_name_en"]').val();
    var logo_fr = $form.find('input[name="logo_name_fr"]').val();
    var message = $(element).data('message-logo');

    if (logo_en !== '' || logo_fr !== '') {
        var validate = $form.parsley().validate();
        if (validate) {
            $form.request('onCompanyData', {
                update: {'UserRegister::_final_step': '#profile-body'},
                complete: function () {
                    window.onbeforeunload = null;
                }

            });
        }
    } else {
        toastr.error(message, null, opts);
    }
}