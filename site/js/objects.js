"use strict";

function User(id, lastname, firstname, login, password) {
    this.id = id;
    this.firstname = firstname;
    this.lastname = lastname;
    this.login = login;
    this.password = password;
}

function Meeting(id, user, name) {
    this.id = id;
    this.user = user;
    this.name = name;
    this.slots = [];
}

Meeting.prototype.addSlot = function (slot) {
    this.slots.push(slot);
};

Meeting.prototype.getSlots = function () {
    return this.slots;
};

function Slot(id, time) {
    this.id = id;
    this.time = time;
    this.votes = [];
}

Slot.prototype.getVotes = function () {
    return this.votes;
};

Slot.prototype.addVote = function (vote) {
    this.votes.push(vote);
};

function Vote(id, name) {
    this.id = id;
    this.name = name;
}


function Type(id, name) {
    this.id = id;
    this.name = name;
    this.input = undefined;
}

Type.prototype.setName = function (new_name) {
    this.name = new_name;
};

Type.prototype.makeInput = function (id) {
};
Type.prototype.applyDefault = function ($, id) {
    this.input = $;
    return $.attr('name', this.id).attr('type', this.kind).attr('id', id).addClass('form-control')
};
Type.prototype.getFormatedData = function () {
    return {name: this.id, field: this.name, input: this.input};
};
Type.prototype.clone = function () {
    return jQuery.extend(true, {}, this)
};

function ScalarType(id, name, kind) {
    Type.call(this, id, name);
    this.kind = kind;
}

ScalarType.prototype = Object.create(Type.prototype);
ScalarType.prototype.constructor = ScalarType;

ScalarType.prototype.makeInput = function (id) {
    return $('<DIV>').addClass('form-group').append($('<LABEL>').addClass('capitalize').text(this.name).attr('for', id), this.applyDefault($('<INPUT>'), id));
};

function RemoteType(id, name, origin) {
    Type.call(this, id, name);
    this.origin = origin;
}

RemoteType.prototype = Object.create(Type.prototype);
RemoteType.prototype.constructor = RemoteType;

RemoteType.prototype.makeInput = function (id) {
    return $('<DIV>').addClass('form-group').append($('<LABEL>').addClass('capitalize').text(this.name).attr('for', id), this.applyDefault($('<SELECT>'), id));
};