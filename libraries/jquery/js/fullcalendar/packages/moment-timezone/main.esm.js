/*!
FullCalendar Moment Timezone Plugin v4.4.2
Docs & License: https://fullcalendar.io/
(c) 2019 Adam Shaw
*/

import * as momentNs from 'moment';
import 'moment-timezone/builds/moment-timezone-with-data';
import { createPlugin, NamedTimeZoneImpl } from '@fullcalendar/core';

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
/* global Reflect, Promise */

var extendStatics = function(d, b) {
    extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return extendStatics(d, b);
};

function __extends(d, b) {
    extendStatics(d, b);
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var moment = momentNs; // the directly callable function
var MomentNamedTimeZone = /** @class */ (function (_super) {
    __extends(MomentNamedTimeZone, _super);
    function MomentNamedTimeZone() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    MomentNamedTimeZone.prototype.offsetForArray = function (a) {
        return moment.tz(a, this.timeZoneName).utcOffset();
    };
    MomentNamedTimeZone.prototype.timestampToArray = function (ms) {
        return moment.tz(ms, this.timeZoneName).toArray();
    };
    return MomentNamedTimeZone;
}(NamedTimeZoneImpl));
var main = createPlugin({
    namedTimeZonedImpl: MomentNamedTimeZone
});

export default main;
