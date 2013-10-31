/* German initialisation for the jQuery UI date picker plugin. */
/* Written by Milian Wolff (mail@milianw.de). */
jQuery(function($){
	$.datepicker.regional['de'] = {
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
	$.datepicker.setDefaults($.datepicker.regional['de']);
});
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
