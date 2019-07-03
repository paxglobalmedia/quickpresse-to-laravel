function setPrintAction(element) {
    var $form = $(element).closest('form');
    var redirect = $(element).data('redirect');
    $form.submit(function (event) {
        event.preventDefault();
    });
    var data = $form.serialize() + '&print=print';
    window.open(redirect + '?&' + data);
    console.log(data);

}

$('input[name="end_date"]').on('change', function (e) {
    var $form = $(this).closest('form');
    $form.submit();
});

$('#content-clicks-modal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const id = button.data('eblast');
    $.request('onLoadClicksContent', {
        data: {eblast_id: id},
        update: {'Statistics::_clicks_detail': '#content-clicks-body'},

    });
});