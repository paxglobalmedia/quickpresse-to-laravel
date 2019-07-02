/*
 *   Campaign Schedule
 */

scheduleDatetimePicker();

function addDateCampaign(element) {
    var $form = $(element).closest('form');
    var validate = $form.parsley().validate();
    if (validate) {
        $form.request('onScheduleCampaign', {
            update: {
                'CampaignSchedule::_list': '#schedule-list',
                'CampaignSchedule::_form': '#schedule-form'
            },
            beforeUpdate: function(data) {
                if (data.balance > 0) {
                    $form.find('input[name=date]').val('');
                    $form.find('input[name=time]').val('');
                    $('#schedule-form').addClass('d-block').removeClass('d-none');
                } else {
                    $('#schedule-form').addClass('d-none').removeClass('d-block');
                }
            },
            complete: function () {
                appAjaxCallBackEvents();
                scheduleDatetimePicker();
            }
        });
    }

}

function scheduleDatetimePicker() {
    var today = new Date().toISOString().slice(0, 10);
    if (lang == 'en') {
        $('.datepicker').pickadate({
            format: 'mm-dd-yyyy',
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
            min: today,
        });
        $('.timepicker').pickatime({
            twelvehour: false
        });

    } else {
        $('.datepicker').pickadate({
            format: 'dd-mm-yyyy',
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
            min: today,

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