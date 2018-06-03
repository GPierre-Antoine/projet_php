"use strict";

function MeetingApp() {
    Main.call(this);
}

MeetingApp.prototype = Object.create(Main.prototype);
MeetingApp.prototype.constructor = MeetingApp;

MeetingApp.prototype.start = function (data) {
    try {
        let self = this;
        this.current_group = data.group;
        if (typeof data.user !== "undefined") {
            this.logger.log(new SimpleMessage("Bonjour, " + data.user.firstname))
        }
        this.title.append($('<H1>').text('Meeting Planner'));
        let route_factory = new RouteFactory(data.types);
        let routes = {};
        for (let key in data.routes) {
            if (!data.routes.hasOwnProperty(key))
                continue;
            routes[key] = route_factory.make(key, data.routes[key]);
        }

        let route_activity = new RouteActivity(this.logger, this.thread, Object.values(routes), new LineRoutePrinter(), this.current_group);
        let login_activity = new LoginActivity(this.logger, this.thread, routes.login);
        let register_activity = new RegisterActivity(this.logger, this.thread, routes.register, login_activity);
        let logout_activity = new LogoutActivity(this.logger, this.thread, routes.logout);
        let make_meeting_activity = new CreateMeetingActivity(this.logger, this.thread, routes.create_meeting, routes.add_slot_to_survey);
        let list_meeting = new ListMeetingActivity(this.logger, this.thread, routes.list_meetings, routes.list_votes);


        let base = this.addActivity(route_activity);
        this.addActivity(login_activity);
        let register_click = this.addActivity(register_activity);
        this.addActivity(logout_activity);
        this.addActivity(make_meeting_activity);
        let list_meeting_click = this.addActivity(list_meeting);

        login_activity.setRegisterLink(register_click);

        route_activity.toggle(true);

        new EventListener(events.status_changed, this.thread, function (event) {
            self.current_group = event.flag;
            self.toggleActivities();
            route_activity.group = event.flag;
            route_activity.reprint();
            base.click();
        });

        new EventListener(events.survey_created, this.thread, function (event) {
            list_meeting.reprint();
            list_meeting_click.click();
        });

    } catch (e) {
        this.logger.log(ExceptionMessage.prototype.fromError(e))
    }
};