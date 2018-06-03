"use strict";

function Activity(logger, thread, groups) {
    this.logger = logger;
    this.thread = thread;
    this.groups = groups;
    this.location = $('<DIV>');
    this.location.toggle(false);
    this.tag = undefined;
}

Activity.prototype.unbind = function () {
    if (this.location.parent())
        this.location.detach();
};

Activity.prototype.bindTag = function (item) {
    this.tag = item;
};

Activity.prototype.getTag = function () {
    return this.tag;
};

Activity.prototype.rebindToHtml = function (new_location) {
    this.unbind();
    new_location.append(this.location);
};

Activity.prototype.getTitle = function () {
    return 'Stub';
};

Activity.prototype.toggle = function (arg) {
    this.location.toggle(arg);
};

Activity.prototype.is = function (arg) {
    this.location.is(arg);
};

Activity.prototype.print = function () {
};

Activity.prototype.reprint = function () {
    this.location.empty();
    this.print();
};

Activity.prototype.checkGroup = function (group) {
    return this.groups.indexOf(group) !== -1
};

