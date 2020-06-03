/*!
FullCalendar RRule Plugin v4.4.2
Docs & License: https://fullcalendar.io/
(c) 2019 Adam Shaw
*/

import { rrulestr, RRule } from 'rrule';
import { createPlugin, refineProps, createDuration } from '@fullcalendar/core';

/*! *****************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */

var __assign = function() {
    __assign = Object.assign || function __assign(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};

var EVENT_DEF_PROPS = {
    rrule: null,
    duration: createDuration
};
var recurring = {
    parse: function (rawEvent, leftoverProps, dateEnv) {
        if (rawEvent.rrule != null) {
            var props = refineProps(rawEvent, EVENT_DEF_PROPS, {}, leftoverProps);
            var parsed = parseRRule(props.rrule, dateEnv);
            if (parsed) {
                return {
                    typeData: parsed.rrule,
                    allDayGuess: parsed.allDayGuess,
                    duration: props.duration
                };
            }
        }
        return null;
    },
    expand: function (rrule, framingRange) {
        // we WANT an inclusive start and in exclusive end, but the js rrule lib will only do either BOTH
        // inclusive or BOTH exclusive, which is stupid: https://github.com/jakubroztocil/rrule/issues/84
        // Workaround: make inclusive, which will generate extra occurences, and then trim.
        return rrule.between(framingRange.start, framingRange.end, true)
            .filter(function (date) {
            return date.valueOf() < framingRange.end.valueOf();
        });
    }
};
var main = createPlugin({
    recurringTypes: [recurring]
});
function parseRRule(input, dateEnv) {
    var allDayGuess = null;
    var rrule;
    if (typeof input === 'string') {
        rrule = rrulestr(input);
    }
    else if (typeof input === 'object' && input) { // non-null object
        var refined = __assign({}, input); // copy
        if (typeof refined.dtstart === 'string') {
            var dtstartMeta = dateEnv.createMarkerMeta(refined.dtstart);
            if (dtstartMeta) {
                refined.dtstart = dtstartMeta.marker;
                allDayGuess = dtstartMeta.isTimeUnspecified;
            }
            else {
                delete refined.dtstart;
            }
        }
        if (typeof refined.until === 'string') {
            refined.until = dateEnv.createMarker(refined.until);
        }
        if (refined.freq != null) {
            refined.freq = convertConstant(refined.freq);
        }
        if (refined.wkst != null) {
            refined.wkst = convertConstant(refined.wkst);
        }
        else {
            refined.wkst = (dateEnv.weekDow - 1 + 7) % 7; // convert Sunday-first to Monday-first
        }
        if (refined.byweekday != null) {
            refined.byweekday = convertConstants(refined.byweekday); // the plural version
        }
        rrule = new RRule(refined);
    }
    if (rrule) {
        return { rrule: rrule, allDayGuess: allDayGuess };
    }
    return null;
}
function convertConstants(input) {
    if (Array.isArray(input)) {
        return input.map(convertConstant);
    }
    return convertConstant(input);
}
function convertConstant(input) {
    if (typeof input === 'string') {
        return RRule[input.toUpperCase()];
    }
    return input;
}

export default main;
