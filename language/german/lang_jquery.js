/* German initialisation for the jQuery UI date picker plugin. */
/* Written by Milian Wolff (mail@milianw.de). */
(function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define([ "../datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}(function( datepicker ) {
	datepicker.regional['de'] = {
		closeText: 'Schließen',
		prevText: '&#x3C;Zurück',
		nextText: 'Vor&#x3E;',
		currentText: 'Heute',
		monthNames: ['Januar','Februar','März','April','Mai','Juni',
		'Juli','August','September','Oktober','November','Dezember'],
		monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
		'Jul','Aug','Sep','Okt','Nov','Dez'],
		dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
		dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		weekHeader: 'KW',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	datepicker.setDefaults(datepicker.regional['de']);

	return datepicker.regional['de'];

}));
/* German initialisation for the jQuery UI multiselect plugin. */
/* Written by Sven Tatter (sven.tatter@gmail.com). */

(function ( $ ) {

$.extend($.ech.multiselect.prototype.options, {
	checkAllText: 'Alle auswählen',
	uncheckAllText: 'Alle abwählen',
	noneSelectedText: 'Nichts ausgewählt',
	selectedText: '# ausgewählt'
});

})( jQuery );
/* German initialisation for the jQuery UI multiselect plugin. */
/* Written by Sven Tatter (sven.tatter@gmail.com). */

(function ( $ ) {

$.extend($.ech.multiselectfilter.prototype.options, {
	label: "Suchen:",
	placeholder: "Stichwort eingeben"
});

})( jQuery );
/* German translation for the jQuery Timepicker Addon */
/* Written by Marvin */
(function($) {
	$.timepicker.regional['de'] = {
		timeOnlyTitle: 'Zeit Wählen',
		timeText: 'Zeit',
		hourText: 'Stunde',
		minuteText: 'Minute',
		secondText: 'Sekunde',
		millisecText: 'Millisekunde',
		microsecText: 'Mikrosekunde',
		timezoneText: 'Zeitzone',
		currentText: 'Jetzt',
		closeText: 'Fertig',
		timeFormat: 'HH:mm',
		amNames: ['vorm.', 'AM', 'A'],
		pmNames: ['nachm.', 'PM', 'P'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['de']);
})(jQuery);
/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: DE (German, Deutsch)
 */
(function ($) {
	$.extend($.validator.messages, {
		required: "Dieses Feld ist ein Pflichtfeld.",
		maxlength: $.validator.format("Geben Sie bitte maximal {0} Zeichen ein."),
		minlength: $.validator.format("Geben Sie bitte mindestens {0} Zeichen ein."),
		rangelength: $.validator.format("Geben Sie bitte mindestens {0} und maximal {1} Zeichen ein."),
		email: "Geben Sie bitte eine gültige E-Mail Adresse ein.",
		url: "Geben Sie bitte eine gültige URL ein.",
		date: "Bitte geben Sie ein gültiges Datum ein.",
		number: "Geben Sie bitte eine Nummer ein.",
		digits: "Geben Sie bitte nur Ziffern ein.",
		equalTo: "Bitte denselben Wert wiederholen.",
		range: $.validator.format("Geben Sie bitte einen Wert zwischen {0} und {1} ein."),
		max: $.validator.format("Geben Sie bitte einen Wert kleiner oder gleich {0} ein."),
		min: $.validator.format("Geben Sie bitte einen Wert größer oder gleich {0} ein."),
		creditcard: "Geben Sie bitte eine gültige Kreditkarten-Nummer ein."
	});
}(jQuery));/*
	jQuery Colorbox language configuration
	language: German (de)
	translated by: wallenium
*/
jQuery.extend(jQuery.colorbox.settings, {
	current: "Bild {current} von {total}",
	previous: "Zurück",
	next: "Vor",
	close: "Schließen",
	xhrError: "Dieser Inhalt konnte nicht geladen werden.",
	imgError: "Dieses Bild konnte nicht geladen werden.",
	slideshowStart: "Slideshow starten",
	slideshowStop: "Slideshow anhalten"
});// Spectrum Colorpicker
// German (de) localization
// https://github.com/bgrins/spectrum

(function ( $ ) {

    var localization = $.spectrum.localization["de"] = {
        cancelText: "Abbrechen",
        chooseText: "Wählen"
    };

    $.extend($.fn.spectrum.defaults, localization);

})( jQuery );

$.fullCalendar.lang("de", {
	buttonText: {
		month: "Monat",
		week: "Woche",
		day: "Tag",
		list: "Terminübersicht"
	},
	allDayText: "Ganztägig"
});// moment.js language configuration
// language : german (de)
// author : lluchs : https://github.com/lluchs
// author: Menelion Elensúle: https://github.com/Oire

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['moment'], factory); // AMD
    } else if (typeof exports === 'object') {
        module.exports = factory(require('../moment')); // Node
    } else {
        factory(window.moment); // Browser global
    }
}(function (moment) {
    function processRelativeTime(number, withoutSuffix, key, isFuture) {
        var format = {
            'm': ['eine Minute', 'einer Minute'],
            'h': ['eine Stunde', 'einer Stunde'],
            'd': ['ein Tag', 'einem Tag'],
            'dd': [number + ' Tage', number + ' Tagen'],
            'M': ['ein Monat', 'einem Monat'],
            'MM': [number + ' Monate', number + ' Monaten'],
            'y': ['ein Jahr', 'einem Jahr'],
            'yy': [number + ' Jahre', number + ' Jahren']
        };
        return withoutSuffix ? format[key][0] : format[key][1];
    }

    return moment.lang('de', {
        months : "Januar_Februar_März_April_Mai_Juni_Juli_August_September_Oktober_November_Dezember".split("_"),
        monthsShort : "Jan._Febr._Mrz._Apr._Mai_Jun._Jul._Aug._Sept._Okt._Nov._Dez.".split("_"),
        weekdays : "Sonntag_Montag_Dienstag_Mittwoch_Donnerstag_Freitag_Samstag".split("_"),
        weekdaysShort : "So._Mo._Di._Mi._Do._Fr._Sa.".split("_"),
        weekdaysMin : "So_Mo_Di_Mi_Do_Fr_Sa".split("_"),
        longDateFormat : {
            LT: "H:mm [Uhr]",
            L : "DD.MM.YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd, D. MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[Heute um] LT",
            sameElse: "L",
            nextDay: '[Morgen um] LT',
            nextWeek: 'dddd [um] LT',
            lastDay: '[Gestern um] LT',
            lastWeek: '[letzten] dddd [um] LT'
        },
        relativeTime : {
            future : "in %s",
            past : "vor %s",
            s : "ein paar Sekunden",
            m : processRelativeTime,
            mm : "%d Minuten",
            h : processRelativeTime,
            hh : "%d Stunden",
            d : processRelativeTime,
            dd : processRelativeTime,
            M : processRelativeTime,
            MM : processRelativeTime,
            y : processRelativeTime,
            yy : processRelativeTime
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
