/**
 * Created by jclora on 2017-05-09.
 */

/**
 *  Plans Options
 */

$(document).on('change', 'input[name$="public"]', function () {
    var $self = $(this);
    if ($self.is(':checked')) {
        $('#collapse-company').collapse('hide');
    } else {
        $('#collapse-company').collapse('show');
    }
});

$(document).on('change', 'input[name$="specific"]', function () {
    var $self = $(this);
    if ($self.is(':checked')) {
        $('#collapse-destination').collapse('show');
    } else {
        $('#collapse-destination').collapse('hide');
    }
});

function sendFormPlan(element) {
    var $form = $(element).closest('form');
    var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onSavePlan', {
            success: function(data) {
                toastr.success(data.message, null, opts);
            }
        });
    }
}

/**
 *  Contract Options
 */

$('#contracts-list').on('change', 'input[name$="active"]', function () {
    var $self = $(this);
    var $form = $self.closest('form');
    $form.submit();
});

$('#form-contract').on('change', 'input[name$="name_company"]', function () {
    var $self = $(this);
    var $form = $self.closest('form');
    $form.request('onLoadPlans', {
        update: {'ContractForm::_plan_select': '#plan-list'}
    });
});

$('#form-contract').on('change', 'select[name$="plan_id"], input[name$="minimum"]', function () {
    var $self = $(this);
    var $form = $self.closest('form');
    $form.request('onLoadSummary', {
        update: {'ContractForm::_summary': '#contract-summary'}
    });
});

function sendFormContract(element) {
    var $form = $(element).closest('form');
    var redirect = $form.data('redirect');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onSaveContract', {
            success: function(data) {
                toastr.success(data.message, null, opts);
            }
        });
    }
}