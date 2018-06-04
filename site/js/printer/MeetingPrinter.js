"use strict";

function MeetingPrinter(logger, eventthread, list_vote, delete_meeting) {
    Printer.call(this);
    this.logger = logger;
    this.eventthread = eventthread;
    this.list_vote = list_vote;
    this.delete_meeting = delete_meeting;
    this.factory = new MeetingFactory();
    this.extenderPrinter = new ExtendedMeetingPrinter();
}

MeetingPrinter.prototype = Object.create(Printer.prototype);
MeetingPrinter.prototype.constructor = MeetingPrinter;


MeetingPrinter.prototype.print = function (data) {
    let self = this;
    let viewer;
    let once = false;
    let url = '/vote/' + data.id;
    let element = $('<DIV>').addClass('col-12 alert border-divider').append($('<DIV>').addClass('flo flo-row').append(
        $('<H3>').text(data.name),
        $('<H3>').addClass('fli-nogrow fas fa-link col-1 clickable align-right').on('click', function () {
            self.logger.log(new SimpleMessage("Votez a l'adresse suivante : <a href='" + url + "'>" + url + "</a>", {html: true}))
        }),
        $('<H3>').addClass('fli-nogrow fas fa-eye col-1 clickable align-right').on('click', function () {
            if (!once) {
                self.list_vote.run({
                    data: {meeting: data.id},
                    error: handle_error(self.logger),
                    success: function (data) {
                        let r = self.extenderPrinter.print(self.factory.make(data.data));

                        viewer.append(r);

                    }
                });
                once = true;
            }
            viewer.toggle();
        }),
        $('<H3>').addClass('fli-nogrow fas fa-trash col-1 clickable align-right').on('click', function () {
            element.remove();
            self.delete_meeting.run({
                data: {meeting: data.id},
                error: handle_error(self.logger),
                success: handle_success(self.logger, new SimpleMessage("Element supprimé avec succes", {
                    type: 'info',
                    timeout: 3000
                }))
            })
        })
    ), viewer = $('<DIV>'));

    viewer.toggle(false);
    return element;
};


function ExtendedMeetingPrinter() {
    Printer.call(this);
}

ExtendedMeetingPrinter.prototype = Object.create(Printer.prototype);
ExtendedMeetingPrinter.prototype.constructor = ExtendedMeetingPrinter;

ExtendedMeetingPrinter.prototype.print = function (meeting) {
    let self = this;
    let slots = meeting.getSlots().sort(function (a, b) {
        return b.time - a.time;
    });
    let dates = {};

    for (let i = 0; i < slots.length; ++i) {
        let slot = slots[i];
        let time = slot.time;

        if (typeof dates[time.getFullYear()] === "undefined") {
            dates[time.getFullYear()] = {}
        }
        let years = dates[time.getFullYear()];

        if (typeof years[time.getMonth()] === "undefined") {
            years[time.getMonth()] = {}
        }
        let monthes = years[time.getMonth()];

        if (typeof monthes[time.getDate()] === "undefined") {
            monthes[time.getDate()] = {}
        }
        let days = monthes[time.getDate()];

        if (typeof days[time.getHours()] === "undefined") {
            days[time.getHours()] = slot
        }
    }

    let content = [];


    function print_slot(hour, slot) {
        console.log(slot);
        let x = $('<DIV>').text(hour + 'h');
        let votes = slot.getVotes();
        let k = $('<DIV>').addClass('flo flo-col').append($('<SPAN>').text('Votants : ' + votes.length));
        for (let i = 0; i < votes.length; ++i) {
            k.append($('<SPAN>').text(votes[i].name))
        }
        return $('<DIV>').append(x, k);
    }

    function print_hour(j, k) {
        let c = $('<DIV>').addClass('flo flo-row');
        for (let i in k) {
            if (!k.hasOwnProperty(i))
                continue;
            c.append($('<DIV>').append(print_slot(i, k[i])));
        }
        return $('<DIV>').addClass('flo flo-col').append($('<SPAN>').text(j), c);
    }

    function print_day(j, k) {
        let c = $('<DIV>').addClass('flo flo-row');
        for (let i in k) {
            if (!k.hasOwnProperty(i))
                continue;
            c.append($('<DIV>').append(print_hour(i, k[i])));
        }
        return $('<DIV>').addClass('flo flo-col').append($('<SPAN>').text(j), c);
    }

    function print_month(j, k) {
        let c = $('<DIV>').addClass('flo flo-row');
        for (let i in k) {
            if (!k.hasOwnProperty(i))
                continue;
            c.append(print_day(i, k[i]));
        }
        return $('<DIV>').addClass('flo flo-col').append($('<SPAN>').text(j), c);
    }

    for (let i in dates) {
        if (!dates.hasOwnProperty(i))
            continue;
        content.push($('<DIV>').append(print_month(i, dates[i])));
    }
    console.log(dates);
    return content;
};