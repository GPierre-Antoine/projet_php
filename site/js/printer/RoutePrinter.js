"use strict";


function LineRoutePrinter() {
    Printer.call(this);
}

LineRoutePrinter.prototype = Object.create(Printer.prototype);
LineRoutePrinter.prototype.constructor = LineRoutePrinter;

LineRoutePrinter.prototype.print = function (data) {
    return $('<DIV>').addClass('primary border-divider alert').append($('<DIV>').text(data.title), $('<DIV>').addClass('secondary').append(
        $('<SPAN>').text(data.abstract),
        $('<SPAN>').html(" (<b>" + data.id + "</b>)"),
    ))
};

function BlockRoutePrinter() {
    Printer.call(this);
}

BlockRoutePrinter.prototype = Object.create(Printer.prototype);
BlockRoutePrinter.prototype.constructor = BlockRoutePrinter;

BlockRoutePrinter.prototype.print = function (data) {
    return $('<DIV>').addClass('primary border-divider col-4 float-left block fixed-height').append($('<DIV>').text(data.title), $('<DIV>').addClass('secondary').text(data.abstract))
};