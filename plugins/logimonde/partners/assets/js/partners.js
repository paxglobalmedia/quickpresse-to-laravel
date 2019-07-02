$('#carousel-partners').carousel();

$(function () {
    detectScreenSize();

    // $(window).resize(function () {
    //     detectScreenSize();
    // });
});

function detectScreenSize() {
    $.request('onDetectScreenSize', {
        data: {width: screen.width, height: screen.height},
        complete: function () {
            $('#carousel-partners').carousel();
        }
    });
}

