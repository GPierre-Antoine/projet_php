"use strict";


const events = {
    status_changed: new CustomEvent('pag.status.changed'),
    survey_created: new CustomEvent('pag.survey.created'),
};

function EventListener(event, eventthread, tell) {
    eventthread.bind(this, event);
    this.tell = tell;
}

function EventThread(logger) {
    this.logger = logger;
    this.bindings = {};
}

EventThread.prototype.bind = function (object, event) {
    if (typeof this.bindings[event.type] === "undefined") {
        this.bindings[event.type] = [];
    }
    this.bindings[event.type].push(object);
};

EventThread.prototype.fire = function (event) {
    let array = this.bindings[event.type];
    if (typeof array !== "undefined") {
        for (let i = 0; i < array.length; ++i) {
            array[i].tell(event);
        }
    }
};

