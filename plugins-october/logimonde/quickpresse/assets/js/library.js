$('.flex-images').flexImages({rowHeight: 225});

var $upload = $('#upload-image-library');
var $form = $upload.closest('form');

uploadFileOnLibrary();

function uploadFileOnLibrary() {
    $('#upload-image-library, #upload-image-library-2').fileupload({
        dataType: 'json',
        maxFileSize: 3000000,
        done: function (e, data) {
            loadImageList(data.result);
        },
        always: function (e, data) {
            if (data.result !== undefined) {
                toastr.success(data.result.success, null, opts);
            } else {
                if (data.jqXHR.responseJSON !== undefined) {
                    toastr.error(data.jqXHR.responseJSON.result, null, opts);
                } else {
                    var message = data.jqXHR.responseText.split(/"/);
                    if (message[0] !== '') {
                        toastr.error(message[0] + ' ' + message[1], null, opts);
                    } else {
                        toastr.error(message[1], null, opts);
                    }
                }
            }
        },
    });
}


function loadImageList(data) {
    $.request('onLoadImageList', {
        //update: {'Library::_image_grid': '#image-list'},
        data: data,
        complete: function () {
            $('#tools-bar').show();
            $('.flex-images').flexImages({rowHeight: 225});
        }
    });
}

// $("#grid-view img").each(function(){
//     this.style.marginTop = (250 - $(this).height()) / 2 + "px";
// });

var $li = $('#view-type button').click(function() {
    $li.removeClass('active');
    $(this).addClass('active');
});

function deleteImage(element) {
    var id = $(element).data('id');
    var view = $(element).data('view-type');
    var confirm = $(element).data('confirm');
    var message = $(element).data('message');
    $.request('onDeleteImage', {
        data: {id: id, view_type: view },
        confirm: confirm,
        complete: function () {
            $('.flex-images').flexImages({rowHeight: 225});
            toastr.success(message, null, opts);
            if ($('.empty-message').length > 0) {
                $('#tools-bar').hide();
            }
        }
    });
}


