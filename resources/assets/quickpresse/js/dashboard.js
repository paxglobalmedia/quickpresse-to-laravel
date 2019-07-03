$(document).ready(function () {
    if ($('#active-qp-panel').length) {
        setInterval('refreshActive()', 20000);
    }
    incompleteList();
    if ($('#launch-modal-incomplete').length) {
        $('#incomplete-list').modal('show');
    }
});


function refreshActive() {
    $.request('onDetectNewEblasts', {
        success: function (data) {
            if (data.new === true) {
                toastr.info(data.message, null, opts);
            }
        },
        error: function (e, data) {
            if (e.responseJSON === undefined) {
                toastr.error(e.responseText, null, opts);
            } else {
                toastr.error(e.responseJSON.error, null, opts);
            }
        }
    })
}

function changeStatus(element) {
    var id = $(element).data('send-id');
    $.request('onUpdateStatus', {
        data: {id: id},
        success: function (data) {
            if (data.status === '1') {
                $(element).addClass('btn-success').removeClass('btn-warning');
                $(element).find('i.fa').addClass('fa-check-circle').removeClass('fa-circle-o');
            } else {
                $(element).addClass('btn-warning').removeClass('btn-success');
                $(element).find('i.fa').addClass('fa-circle-o').removeClass('fa-check-circle');
            }
        },
        error: function (e, data) {
            if (e.responseJSON === undefined) {
                toastr.error(e.responseText, null, opts);
            } else {
                toastr.error(e.responseJSON.error, null, opts);
            }
            $(element).addClass('btn-warning').removeClass('btn-success');
            $(element).find('i.fa').addClass('fa-circle-o').removeClass('fa-check-circle');
        }
    })

}


function setQpSection(element) {
    var campaign = $(element).data('campaign-id');
    var value = $(element).data('qp-section');
    value = (value === 0) ? 1 : 0;
    $.request('onIncludeCampaignSection', {
        data: {campaign_id: campaign, qp_section: value},
        success: function () {
            if (value === 1) {
                $(element).find('i').addClass('fa-check').removeClass('fa-circle-o');
            } else {
                $(element).find('i').addClass('fa-circle-o').removeClass('fa-check');
            }
            appAjaxCallBackEvents();
        }
    });
}


function showAdminQuickPresse(element) {
    var id = $(element).data('send-id');
    var url = $(element).data('url');
    var $tr = $(element).closest('tr');
    $tr.find('#status-' + id).prop('disabled', false);
    $tr.find('td').removeClass('grey lighten-4');
    $tr.find('span.new-qp').remove();
    $.request('onShowAdminQp', {
        data: {id: id, url: url},
        //redirect: url,
        success: function () {
            window.open(
                url,
                '_blank' // <- This is what makes it open in a new window.
            );
        }
    })
}

$('#contracts-list').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    //if (id !== undefined) {
    $.request('onShowContractList', {
        update: {'Dashboard::_contracts_list': '#contracts-content'},
        complete: function () {
            requiredFields();
        }
    });
    //}
});

function incompleteList() {
    $('#incomplete-list').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        //if (id !== undefined) {
        $.request('onGetIncompleteList', {
            update: {'Dashboard::_incomplete_list': '#incomplete-content'},
            complete: function () {
                requiredFields();
            }
        });
        //}
    });
}

function resendEblast(element) {
    const eblast = $(element).data('eblast');
    const confirm = $(element).data('message');
    $.request('onResendEblast', {
        data: {eblast: eblast},
        confirm: confirm,
    });
}


$('#modal-edit-date').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const id = button.data('send-id');
    $.request('onLoadEblastData', {
        data: {id: id},
        update: {'Dashboard::_edit_date': '#edit-date-content'},
        complete: function () {
            appAjaxCallBackEvents();
        }
    });

});

function editEblastDate(element) {

    const $form = $(element).closest('form');
    $form.request('onUpdateEblastDate', {});

}