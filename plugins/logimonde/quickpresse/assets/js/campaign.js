/**
 * Collapse manage Campaign
 * Component: Manage
 */

$(document).ready(function () {

    $('#stepOne').on('hidden.bs.collapse', function () {
        $('#headingOne h5 span i').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
    });
    $('#stepOne').on('shown.bs.collapse', function () {
        $('#headingOne h5 span i').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
    });
    $('#stepTwo').on('hidden.bs.collapse', function () {
        $('#headingTwo h5 span i').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
    });
    $('#stepTwo').on('shown.bs.collapse', function () {
        $('#headingTwo h5 span i').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
    });
    $('#stepThree').on('hidden.bs.collapse', function () {
        $('#headingThree h5 span i').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
    });
    $('#stepThree').on('shown.bs.collapse', function () {
        $('#headingThree h5 span i').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
    });

    if ($('#campaign-not-active').length) {
        $('#campaign-activation').modal('show');
    }
});


/*
 *     Manage campaign settings
 */

function getCompanyIntro(element) {
    var $form = $(element).closest('form');

    $form.request('onCompanyIntro', {
        update: {
            'CampaignForm::_intro_options': '#intro-options'
        },
        complete: function () {
            requiredFields();
            $('#form-options').empty();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

}

function getFormOptions(element) {
    var $form = $(element).closest('form');
    var type = $(element).data('option');
    $form.request('onCompanyOptions', {
        data: {type: type},
        update: {
            'CampaignForm::_form_options': '#form-options'
        },
        beforeUpdate: function (data) {
            $('#intro-options').empty();
        },
        complete: function () {
            requiredFields();
        }
    });

}


function validateContract(element) {
    var $form = $(element).closest('form');
    $form.request('onValidateContract', {
        update: {
            'CampaignForm::_contract_options': '#contract-options'
        },
        beforeUpdate: function (data) {
            if (data.message !== null) {
                toastr.warning(data.message, null, opts);
            }

        },
        error: function (e, data) {
            if (e.responseJSON === undefined) {
                toastr.error(e.responseText, null, opts);
            } else {
                toastr.error(e.responseJSON.error, null, opts);
            }
            $('#contract-options').empty();
        }
    });

}

function getProductBalance(element) {
    var $form = $(element).closest('form');
    var id = $(element).val();
    $form.request('onGetProductBalance', {
        update: {
            'CampaignForm::_product_balance': '#product-balance'
        },
        beforeUpdate: function (data) {
            if (data.product === undefined) {
                $('#btn-submit-campaign').prop('disabled', true);
            } else {
                $('#btn-submit-campaign').prop('disabled', false);
            }
        },
        error: function (e, data) {
            if (e.responseJSON === undefined) {
                toastr.error(e.responseText, null, opts);
            } else {
                toastr.error(e.responseJSON.error, null, opts);
            }
            $('#btn-submit-campaign').prop('disabled', true);
        }
    });

}

function checkProductContract(element) {
    var $form = $(element).closest('form');
    $form.request('onCheckProductContract', {
        success: function (data) {
            if (data.item !== null) {
                confirmProductContract(element)
            }
        }
    });
}

function confirmProductContract(element) {
    var confirm = $(element).data('confirm');
    var url = $(element).data('url');
    var $modal = $('#modal-confirm');
    $modal.modal('show');
    $modal.find('.confirm-message').text(confirm);
    $modal.find('#cancel-button').click(function (e) {
        window.location.href = url;
    });
}

function validateCustomlist(element) {
    var $form = $(element).closest('form');
    $form.request('onValidateCustomList', {
        success: function (data) {
            toastr.info(data.message, null, opts);
        },
        error: function (e, data) {
            if (e.responseJSON === undefined) {
                toastr.error(e.responseText, null, opts);
            } else {
                toastr.error(e.responseJSON.error, null, opts);
            }
            $(element).val('');
        }
    });
}

function sendFormCampaignSettings(element) {
    var $form = $(element).closest('form');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onCreateCampaign', {});
    }

}

function updateFormCampaignSettings(element) {
    var $form = $(element).closest('form');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onUpdateCampaign', {});
    }

}

function setPublishedCampaign(element) {
    var $form = $(element).closest('form');
    var $checkbox = $(element), chkValue, message;
    if ($checkbox.is(':checked')) {
        chkValue = 1;
        message = $checkbox.data('message-yes');
    } else {
        chkValue = 0;
        message = $checkbox.data('message-no');
    }
    $form.request('onChangeStatusCampaign', {
        data: {active: chkValue},
        update: {
            'Manage::_summary': '#summary-campaign'
        },
        complete: function () {
            appAjaxCallBackEvents();
            if (chkValue === 1) {
                toastr.success(message, null, opts);
                $('.manage-edit').addClass('disabled');
                $('.alert-success').remove();
            } else {
                toastr.warning(message, null, opts);
                $('.manage-edit').removeClass('disabled');
                $form.find('a').click(function (e) {
                    location.href = this.href;
                });
            }

        }
    });
}

function setIncludeCampaign(element) {
    var $form = $(element).closest('form');
    var $checkbox = $(element), chkValue, message;
    if ($checkbox.is(':checked')) {
        chkValue = 1;
        message = $checkbox.data('message-yes');
        $('#days-pax-section').removeClass('d-none').addClass('d-inline-block')
    } else {
        chkValue = 0;
        message = $checkbox.data('message-no');
        $('#days-pax-section').removeClass('d-inline-block').addClass('d-none')
    }
    $form.request('onIncludeCampaignSection', {
        data: {qp_section: chkValue},
        success: function () {
            if (chkValue === 1) {
                toastr.success(message, null, opts);
            } else {
                toastr.warning(message, null, opts);
            }
            appAjaxCallBackEvents();
        }
    });
}

function setDaysPaxSection(element) {
    var $form = $(element).closest('form'), message, days;
    message = $(element).data('message');
    days = $(element).data('days');
    $form.request('onSetDaysPaxSection', {
        success: function (data) {
            var fullMessage = message + ' ' + data.days_pax + ' ' + days;
            toastr.success(fullMessage, null, opts);
            appAjaxCallBackEvents();
        }
    });
}

function selectDistributionList(element) {
    var $form = $(element).closest('form');
    var $switch = $(element), switchType;
    if ($switch.is(':checked')) {
        switchType = 'regular';
    } else {
        switchType = 'agreement';
    }
    $form.request('onSelectDistributionList', {
        data: {switch_type: switchType},
        update: {
            'CampaignForm::_plans_list': '#qp_list'
        },
        complete: function () {
            appAjaxCallBackEvents();
        }
    });
}

function selectAgreement(element) {
    var agreement = $(element).find("option:selected").data('agreement');
    $('input[name="agreement_id"]').val(agreement);
}

function sendTestByEmail(element) {
    var $form = $(element).closest('form');
    var send = $(element).data('send-id');
    var confirm = $(element).data('send-confirm');
    var message = $(element).data('sent-message');
    $form.request('onSendTestByEmail', {
        data: {sendId: send},
        success: function (data) {
            toastr.info(message + data.email, null, opts);
            appAjaxCallBackEvents();
        }
    });
}

function sendAdminTestByEmail(element) {
    var $form = $(element).closest('form');
    var send = $(element).data('send-id');
    var confirm = $(element).data('send-confirm');
    var message = $(element).data('sent-message');
    $form.request('onSendAdminTestByEmail', {
        data: {sendId: send},
        //confirm: confirm,
        success: function (data) {
            toastr.info(message, null, opts);
            appAjaxCallBackEvents();
        }
    });
}

function setActiveCampaign(element) {
    const campaign = $(element).data('campaign');
    $('#campaign-activation').modal('hide');
    $.request('onChangeStatusCampaign', {
        data: {campaign_id: campaign, active: 1, redirect: 'redirect'},

    });
}