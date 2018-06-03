"use strict";

function ListMeetingActivity(logger, eventhread, list_meeting_route, list_vote, delete_meeting) {
    Activity.call(this, logger, eventhread, [1]);
    this.list_meeting = list_meeting_route;
    this.list_vote = list_vote;
    this.delete_meeting = delete_meeting;
    this.location.addClass('container');
    this.print();
}

ListMeetingActivity.prototype = Object.create(Activity.prototype);
ListMeetingActivity.prototype.constructor = ListMeetingActivity;

ListMeetingActivity.prototype.getTitle = function () {
    return "Voir les sondages";
};

ListMeetingActivity.prototype.print = function () {
    let self = this;
    let printer = new MeetingPrinter(this.logger, this.eventthread, this.list_vote, this.delete_meeting);
    let location = self.location;
    this.location.append($('<H1>').text('Liste des sondages').addClass('primary'));
    this.logger.log(new SimpleMessage('Mise à jour de la liste des sondages ...'));
    this.list_meeting.run({
        data: {},
        error: handle_error(self.logger),
        success: function (data) {
            let factory = new MeetingFactory();
            let col = data.data.map(function (data) {
                return factory.make(data);
            });
            for (let i = 0; i < col.length; ++i) {
                location.append(printer.print(col[i]));
            }
        }
    });
};
