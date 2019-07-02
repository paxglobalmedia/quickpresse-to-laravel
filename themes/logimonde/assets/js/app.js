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
    // Initialize collapse button
    $(".button-collapse").sideNav();
    // Initialize collapsible (uncomment the line below if you use the dropdown variation)
    //$('.collapsible').collapsible();

    $('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="popover"]').popover({animation: true, delay: {show: 100, hide: 100}});



    if ($('#flash').length > 0) {
        var message = $('#flash').data('message');
        var type = $('#flash').data('type');
        if (type === 'success') {
            toastr.success(message, null, opts);
        } else if (type === 'error') {
            toastr.error(message, null, opts);
        } else if (type === 'warning') {
            toastr.warning(message, null, opts);
        } else {
            toastr.info(message, null, opts);
        }

    }
    appAjaxCallBackEvents();

});

function appAjaxCallBackEvents() {

    $('.disabled').click(function (e) {
        e.preventDefault();
    });

    datetimePickers();

    requiredFields();

    dropdownSelect();
}


function datetimePickers() {
    var today = new Date().toISOString().slice(0, 10);
    if (lang == 'en') {
        $('.datepicker').pickadate({
            format: 'mm-dd-yyyy',
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
        });
        $('.timepicker').pickatime({
            twelvehour: false
        });

    } else {
        $('.datepicker').pickadate({
            format: 'dd-mm-yyyy',
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year

            labelMonthNext: 'Mois prochain',
            labelMonthPrev: 'Mois précédent',

            // The title label to use for the dropdown selectors
            labelMonthSelect: 'Sélectionnez un mois',
            labelYearSelect: 'Sélectionnez une année',

            // Months and weekdays
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Au', 'Sep', 'Oct', 'Nov', 'Dec'],
            weekdaysFull: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],

            // Materialize modified
            weekdaysLetter: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],

            // Today and clear
            today: "Auj.",
            clear: 'Effacer',
            close: 'Fermer'
        });
        $('.timepicker').pickatime({
            twelvehour: false,
            donetext: 'Terminé'
        });

    }
}

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

$('form input.get-city').each(function () {

    var self = this;
    var $form = $(self).closest('form');
    var result = function (city, state, country) {
        $form.find('input[name="city"]').val(city);
        $form.find('input[name="state"]').val(state);
        $form.find('input[name="country"]').val(country);
        console.log(country);
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

$('form input.get-company').each(function () {

    var self = this;
    var $form = $(self).closest('form');
    var result = function log(message) {
        $('input[name$="company_id"]', $form).val(message);
        if (!$(self).hasClass('not-trigger')) {
            $(self).closest('.input-group').find('button').trigger('click')
        }
    };

    $(this).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: site_url + 'search/company',
                dataType: "json",
                data: {company: request.term},
                type: 'get',
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            id: item.id,
                            value: item.name
                        };
                    }));
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            result(ui.item ? ui.item.id : "");
        },
        open: function () {
            $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
        },
        close: function () {
            $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
        }
    });
});


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
 * Pagination Input
 */
var $pagination = $('.pagination');

if ($pagination.length > 0) {
    $('body').on('keypress', '.pagination .page-input', function (e) {
        //console.log(e);
        var self = $(e.currentTarget);
        var value = String.fromCharCode(e.which);

        if (e.which != '8') {
            $('body .pagination .page-input').not(self).val(value);
        } else {
            $('body .pagination .page-input').not(self).val('');
        }

    });

}

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

/*
 *  Dropdown select
 */

function dropdownSelect() {
    $(document).on('click', '.btn-group .dropdown-menu .dropdown-item', function () {
        var $self = $(this);
        if (!$self.hasClass('exclude')) {
            var $form = $(this).closest('form');
            var $container = $self.closest('.btn-group');
            var action = $self.data('action');
            var text = $self.text();
            $container.find('input.dropdown-input').val(action);
            $container.find('.dropdown-toggle').text(text);
            $form.submit();
        }
    });
}

/*
 * Redirect User Profile page
 */

function redirectToProfile(url) {

    window.location = (url);
}

/*
*   Reset input group search
 */
function resetInputGroupSearch(element) {
    var $wrap = $(element).closest('.input-group');
    $wrap.find('input').val('');
    $(element).closest('form').submit();

}