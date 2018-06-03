"use strict";

function MeetingPrinter() {
    Printer.call(this);
}

MeetingPrinter.prototype = Object.create(Printer.prototype);
MeetingPrinter.prototype.constructor = MeetingPrinter;


MeetingPrinter.prototype.print = function (data) {
    return $('<DIV>').addClass('col-12 alert border-divider').append(
        $('<DIV>').append($('<H3>').text(data.name)),
    )
};