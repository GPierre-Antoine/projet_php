"use strict";

function MeetingPrinter(logger, eventthread, list_vote, delete_meeting) {
    Printer.call(this);
    this.logger = logger;
    this.eventthread = eventthread;
    this.list_vote = list_vote;
    this.delete_meeting = delete_meeting;
}

MeetingPrinter.prototype = Object.create(Printer.prototype);
MeetingPrinter.prototype.constructor = MeetingPrinter;


MeetingPrinter.prototype.print = function (data) {
    let self = this;
    let viewer;
    let once = false;
    let element = $('<DIV>').append($('<DIV>').addClass('col-12 alert border-divider flo flo-row').append(
        $('<H3>').text(data.name),
        $('<H3>').addClass('fli-nogrow fas fa-eye col-1 clickable align-right').on('click', function () {
            if (!once) {
                self.list_vote.run({
                    data: {meeting: data.id},
                    error: handle_error(self.logger),
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