"use strict";


function Route(title, abstract, url, data, groups, accept, type) {
    this.title = title;
    this.abstract = abstract;
    this.url = url;
    this.data = data;
    this.groups = groups;
    this.type = type;
    this.accept = accept;
}

Route.prototype.run = function (data) {
    data.url = this.url;
    data.headers = {
        "Accept": this.accept,
        "Content-Type": "application/x-www-form-urlencoded"
    };
    data.type = this.type;
    return $.ajax(data);
};

function RoutePrinter() {

}

RoutePrinter.prototype.print = function (data) {

};

function LineRoutePrinter() {
    RoutePrinter.call(this);
}

LineRoutePrinter.prototype = Object.create(RoutePrinter.prototype);
LineRoutePrinter.prototype.constructor = LineRoutePrinter;

LineRoutePrinter.prototype.print = function (data) {
    return $('<DIV>').addClass('primary border-divider alert').append($('<DIV>').text(data.title), $('<DIV>').addClass('secondary').text(data.abstract))
};

function BlockRoutePrinter() {
    RoutePrinter.call(this);
}

BlockRoutePrinter.prototype = Object.create(RoutePrinter.prototype);
BlockRoutePrinter.prototype.constructor = BlockRoutePrinter;

BlockRoutePrinter.prototype.print = function (data) {
    return $('<DIV>').addClass('primary border-divider col-4 float-left block fixed-height').append($('<DIV>').text(data.title), $('<DIV>').addClass('secondary').text(data.abstract))
};