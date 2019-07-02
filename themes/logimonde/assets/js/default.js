/**
 * Created by jclora on 2016-12-14.
 */

var opts = {
    "closeButton": true,
    "debug": false,
    "positionClass": "toast-top-right",
    "onclick": null,
    "showDuration": "800",
    "hideDuration": "1000",
    "timeOut": "8000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
var star = '<span class="field-required">&nbsp;</span>';
var $formSearch = $('#form-search');
var loadingIndicatorOverlay = '';

$(document).ready(function () {


    $('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="popover"]').popover({animation: true, delay: {show: 100, hide: 100}});

    if ($('#flash').length > 0) {
        var message = $('#flash').data('message');
        var type = $('#flash').data('type');
        if (type == 'success') {
            toastr.success(message, null, opts);
        } else if (type == 'error') {
            toastr.error(message, null, opts);
        } else if (type == 'warning') {
            toastr.warning(message, null, opts);
        } else {
            toastr.info(message, null, opts);
        }

    }

    requiredFields();
    getCity();
});

/*
 *   Required fields Function
 */
function requiredFields() {
    $('input, textarea, select', 'form').each(function () {
        if ($(this).prop('required')) {
            var $wrap = $(this).closest('.form-group');
            if ($wrap.find('span.field-required').length === 0) {
                $wrap.find('label').append(star);
            }
        }
    });
}


//Check to see if the window is top if not then display button
$(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
        $('.scrollToTop').fadeIn();
    } else {
        $('.scrollToTop').fadeOut();
    }
});


/*
 * Custom Confirm Modal
 */

$(window).on('ajaxConfirmMessage', function (event, message) {
    // Stop the default confirm dialog
    event.preventDefault();
    $('#modal-confirm').modal('show');
    $('#modal-confirm').find('.confirm-message').html(message);
    // Okay Button
    $('#okay-button').click(function () {
        // Resolve the deferred object, this will trigger whatever was being confirmed
        event.promise.resolve();
    });

    // Cancel Button
    $('#cancel-button').click(function () {
        // Reject the object, this will cancel whatever was being confirmed
        event.promise.reject();
    });

    // Return a value so the framework knows we're handling the confirm
    return true;
});


/*
 * Search city function
 */

function getCity() {
    $('form input.get-city').each(function () {

        var self = this;
        var $form = $(self).closest('form');
        var result = function log(city, state, country) {
            $form.find('input[name="city"]').val(city);
            $form.find('input[name="state"]').val(state);
            $form.find('input[name="country"]').val(country);
        };

        $(this).autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: site_url + 'search/city',
                    dataType: "json",
                    data: {city: request.term},
                    type: 'get',
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                city_id: item.city_id,
                                state_id: item.state_id,
                                country_id: item.country_id,
                                value: item.city_name + ' (' + item.state_name + ' - ' + item.country_name + ')'
                            };
                        }));
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {
                if (ui.item) {
                    result(ui.item.city_id, ui.item.state_id, ui.item.country_id);
                }

            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });
    });
}


String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

/*
 *  Dropdown Content - Remains open on click inside
 */

$('body').on('click', 'li.dropdown.mega-dropdown a', function (event) {
    $(this).parent().toggleClass("show");
});

$('body').on('click', function (e) {
    if (!$('li.dropdown.mega-dropdown').is(e.target) && $('li.dropdown.mega-dropdown').has(e.target).length === 0 && $('.open').has(e.target).length === 0) {
        $('li.dropdown.mega-dropdown').removeClass('show');
    }
});

/*
 * Menu on Top
 */

if ($(window).width() > 700) {

    $(window).scroll(function () { //on scroll,
        var scrollt = window.scrollY; //get the amount of scrolling

        if (scrollt >= 100) { //if you scrolled past it,
            $(".navbar").css("opacity", "0.9").css("padding", "0 1rem");
            $(".navbar").find('.logo-desktop').removeClass('d-sm-block');
            $(".navbar").find('.logo-mobile').addClass('d-sm-block');
            //make it sticky
        } else {
            $(".navbar").css("opacity", "1").css("padding", "0.5rem 1rem");
            $(".navbar").find('.logo-desktop').addClass('d-sm-block');
            $(".navbar").find('.logo-mobile').removeClass('d-sm-block');
        }
    });
}