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
}

function Slot(id, meeting, time, interval) {
    this.id = id;
    this.meeting = meeting;
    this.time = time;
    this.interval = interval;
}

function Vote(id, slot, name) {
    this.id = id;
    this.slot = slot;
    this.name = name;
}