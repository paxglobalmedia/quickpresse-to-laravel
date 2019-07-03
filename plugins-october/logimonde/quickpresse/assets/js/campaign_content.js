/**
 * Created by jclora on 2017-06-26.
 */

/*
 *   Manage campaign content
 */
var $properties = $('.slide-out-settings');
var $handler = $('#campaign-content .slide-out-settings .handle');
var templates = [];
var $templatesCard = $('#template-list').closest('.card');
var $contentPreview = $('#content-preview').closest('.card');
var specs = (lang === 'en' ? " Please check the specifications" : " Veuillez vérifier les spécifications");

$(function () {

    slideOutTab();
    uploadFileContent();
    listTemplates();
    addTargelink();
});

function listTemplates() {
    $('.template-item').each(function (i) {
        var id = $(this).data('template-id');
        templates[id] = "CampaignContent::" + $(this).data('template-partial');
    });
}

function colorPicker() {
    $(".color-picker").spectrum({
        showInput: true,
        preferredFormat: "hex",
    });
}

function triggerHandler() {
    $handler.prop('disabled', true);
    $('#content-settings').empty();
    if ($properties.hasClass('open')) {
        $handler.prop('disabled', true).trigger('click');
    }
}

function getTemplate(element) {
    var $form = $(element).closest('form');
    var $self = $(element);
    var id = $self.data('template-id');
    var campaign = $self.data('campaign-id');
    $handler.prop('disabled', true);
    if ($properties.hasClass('open')) {
        $handler.prop('disabled', true).trigger('click');
    }
    $.request('onLoadTemplatePreview', {
        data: {template_id: id, campaign_id: campaign},
        complete: function (e, data) {
            if (data !== 'error') {
                $self.addClass('selected');
                $self.parent().siblings().find('.template-item').removeClass('selected');
            }
        },
        error: function (e, data) {
            if (e.responseJSON === undefined) {
                toastr.error(e.responseText, null, opts);
            } else {
                toastr.error(e.responseJSON.error, null, opts);
            }
        }
    });
}

//if ($('.block-file').length > 0) {
$(document).on('mouseover', '.block-content', function () {
    $(this).find('.block-file-header').show();
}).on('mouseout', '.block-content', function () {
    $(this).find('.block-file-header').hide();
});

//}


function getBlockContent(element) {
    showSpinner();
    $handler.prop('disabled', false).trigger('click');
    var $form = $(element).closest('form');
    var partial = '', methode = '', data = {};
    var $block = $(element).closest('.block-content');
    var blockId = $block.attr('id');
    var type = $block.data('file-type');
    var imageWidth = $block.data('image-size');
    var id = $block.find('.block-file-item').attr('id');
    var blockType = $block.data('block-type');
    if (type === 'text/html') {
        partial = {'CampaignContent::settings/_html_setup': '#content-settings'};
        methode = 'onLoadContentSettings';
        data = {block: blockId, id: id, block_type: blockType};
    } else if (type === 'image/jpeg' || type === 'image/png' || type === 'image/gif') {
        partial = {'CampaignContent::settings/_image_setup': '#content-settings'};
        methode = 'onLoadContentSettings';
        data = {block: blockId, id: id, block_type: blockType, image_width: imageWidth};
    } else if (type === 'text/plain') {
        partial = {'CampaignContent::settings/_edit_object': '#content-settings'};
        methode = 'onLoadContentSettings';
        data = {block: blockId, id: id, block_type: blockType};
    } else {
        partial = {'CampaignContent::settings/_add_object': '#content-settings'};
        methode = 'onAddObject';
        data = {block: blockId, block_type: blockType, image_width: imageWidth};
    }
    //$block.css('border', '1px solid #555555');
    $form.request(methode, {
        data: data,
        update: partial,
        beforeUpdate: function (data) {
            $('#content-settings').empty();
        },
        complete: function () {
            $('.flex-images').flexImages({rowHeight: 100});
            uploadFileContent();
            textEditor();
            colorPicker()
        }
    });
}

function showSpinner() {
    var spiner = '<div class="text-center pt-5">' +
        '<i class="fa fa-spinner fa-spin fa-4x fa-fw"></i>' +
        '</div>';
    $('#content-settings').empty().append(spiner);
}

function uploadFileContent() {
    $('#upload-file-content, #upload-file-content-html').fileupload({
        dataType: 'json',
        maxFileSize: 4000000,
        submit: function (e, data) {
            var form = $(this).closest('form');
            var id = $(this).data('id');
            if (id !== undefined) {
                form = form.serializeArray();
                form = form.concat([
                    {name: "content_id", value: id}
                ]);
                data.formData = form;
            }
        },
        done: function (e, data) {
            $(data.result.preview.element).html(data.result.preview.html);
            $(data.result.settings.element).html(data.result.settings.html);
            //triggerHandler();
            uploadFileContent();
            hideTemplateList(data.result.template);
            addTargelink();
            loadSettingsAfterLoadImage(data.result.file);
            $('#btn-save-close').prop('disabled', false);
            $('#btn-save').prop('disabled', false);
            window.onbeforeunload = function () {
                return '';
            }
        },
        always: function (e, data) {
            if (data.result !== undefined) {
                toastr.success(data.result.success, null, opts);
            } else {
                if (data.jqXHR.responseJSON !== undefined) {
                    toastr.error(data.jqXHR.responseJSON.result, null, opts);
                } else {
                    var message = data.jqXHR.responseText.split(/"/);
                    var errorMessage = message[1] + specs;
                    toastr.error(errorMessage, null, opts);
                }
            }
        },
    });
}

function loadSettingsAfterLoadImage(file) {
    var $form = $('#' + file.block).closest('form');
    var partial, blockType;
    if (file.mime === 'text/html') {
        partial = {'CampaignContent::settings/_html_setup': '#content-settings'};
        blockType = 'html';
    } else {
        partial = {'CampaignContent::settings/_image_setup': '#content-settings'};
        blockType = 'image';
    }
    $form.request('onLoadContentSettings', {
        data: {block: file.block, id: file.content_id, block_type: blockType, image_width: file.imageWidth},
        update: partial,
        beforeUpdate: function (data) {
            $('#content-settings').empty();
        },
        complete: function () {
            uploadFileContent();
        }
    });
}

function slideOutTab() {
    if ($properties.length) {
        $properties.tabSlideOut({
            tabHandle: '.handle',                     //class of the element that will become your tab
            pathToTabImage: $('.handle').data('url'), //path to the image for the tab //Optionally can be set using css
            imageHeight: '176px',                     //height of tab image           //Optionally can be set using css
            imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
            tabLocation: 'right',                      //side of screen where tab lives, top, right, bottom, or left
            speed: 300,                               //speed of animation
            action: 'click',                          //options: 'click' or 'hover', action to trigger animation
            topPos: '135px',                          //position from the top/ use if tabLocation is left or right
            //leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
            fixedPosition: true                      //options: true makes it stick(fixed position) on scroll
        });
    }
}

function updateProperties(element) {
    var $form = $(element).closest('form');
    var id = $(element).data('id');
    var template_id = $("input[name='template_id']").val();
    var template_name = templates[template_id];
    var partial = {};
    partial[template_name] = '#content-preview';
    $form.request('onUpdateProperties', {
        data: {content_id: id},
        update: partial,
        beforeUpdate: function (data) {
            triggerHandler();
            hideTemplateList(data.template);
            if (data.message !== undefined) {
                toastr.info(data.message, null, opts);
            }
        },
        complete: function () {
            uploadFileContent();
            $('#btn-save-close').prop('disabled', false);
            $('#btn-save').prop('disabled', false);
        }
    });
}

function removeFileContent(element) {
    var $form = $(element).closest('form');
    var id = $(element).data('id');
    var template_id = $("input[name='template_id']").val();
    var template_name = templates[template_id];
    var partial = {};
    partial[template_name] = '#content-preview';
    $form.request('onRemoveFileContent', {
        data: {content_id: id},
        update: partial,
        confirm: $(element).data('confirm'),
        beforeUpdate: function (data) {
            triggerHandler();
            if (data.message !== undefined) {
                toastr.warning(data.message, null, opts);
            }
        },
        complete: function (data) {
            uploadFileContent();
            showTemplateList(data.responseJSON);
            $('#btn-save-close').prop('disabled', true);
            $('#btn-save').prop('disabled', true);
            window.onbeforeunload = null;
        }
    });
}

function selectImageFromLibrary(element) {
    var $form = $(element).closest('form');
    var image = $(element).data('id');

    $form.request('onSelectImageFromLibrary', {
        data: {image_id: image},
        success: function (data) {
            //triggerHandler();
            $('#content-preview').html(data.html);
            var file = {block: data.content.block, content_id: data.content.id, imageWidth: data.imageWidth}
            uploadFileContent();
            hideTemplateList(data.template);
            loadSettingsAfterLoadImage(file);
            $('#btn-save-close').prop('disabled', false);
            $('#btn-save').prop('disabled', false);
            window.onbeforeunload = function () {
                return '';
            }
        }
    });
}

function addPageImage(element) {
    var $form = $(element).closest('form');
    var counter = $(element).data('block-counter');
    $form.request('onAddNewPageImage', {
        data: {counter: counter},
        success: function (data) {
            $('#image-pages').append(data.html);
            uploadFileContent();
            $('#btn-save-close').prop('disabled', true);
            $('#btn-save').prop('disabled', true);
        }
    });
}

function hideTemplateList(template) {
    if ($templatesCard.length) {
        $templatesCard.closest('.col').css('display', 'none');
        $contentPreview.closest('.col').removeClass('col-lg-10');
        $('#template-title').css('visibility', 'visible').find('span').html(template.name);

    }
}

function showTemplateList(data) {
    if (data.contents === 0) {
        $templatesCard.closest('.col').css('display', 'block');
        $contentPreview.closest('.col').addClass('col-lg-10');
        $('#template-title').css('visibility', 'hidden').find('span').empty();
    }
}

function saveCampaignContent(element) {
    var $form = $(element).closest('form');

    $form.request('onUpdateCampaignContent', {
        success: function (data) {
            toastr.success(data.message, null, opts);
        }
    });

}

function saveCloseCampaignContent(element) {
    var $form = $(element).closest('form');
    var redirect = $(element).data('redirect');
    window.onbeforeunload = null;
    $form.request('onUpdateCampaignContent', {
        data: {redirect: true},
        redirect: redirect
    });

}

/*
 * Edit links in HTML file
 * Add target _blank
*/

function addTargelink() {
    $('#content-preview').find('a').attr('target', '_blank');
}

