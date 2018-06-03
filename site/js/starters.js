"use strict";

function Main() {
    let body = $('body');
    let alerts;

    let body_width = 'col-10';
    let trail_width = 'col-2';

    let fragment = [
        $('<header>').addClass('col-12 fli-nogrow fli-noshrink header flo flo-col').append(
            $('<DIV>').addClass('row').append(
                this.title = $('<DIV>').addClass(body_width),
                $('<H1>').addClass(trail_width).text('Messages')
            )
        ),
        $('<section>').addClass('col-12 flo flo-row').append(
            this.section = $('<DIV>').addClass(body_width + ' scrolly'),
            alerts = $('<div>').addClass(trail_width + ' theme-divider scrolly')
        ),
        $('<footer>').addClass('col-12 fli-nogrow fli-noshrink footer').append(
            $('<DIV>').addClass('row').append(
                this.footer = $('<DIV>').addClass(body_width + ' row'),
                $('<SPAN>').addClass(trail_width).text('Développé par Pierre-Antoine GUILLAUME')
            )
        )
    ];
    body.append(fragment).addClass('flo flo-col');
    let self = this;
    let fix_height = function () {
        self.section.height('auto');
        self.section.height(self.section.height());
        alerts.height('auto');
        alerts.height(alerts.height());
    };

    fix_height();
    $(window).resize(fix_height);

    this.logger = new GeneralLogger(alerts, {default_theme: 'theme'});
    this.activities = [];
    this.thread = new EventThread(this.logger);
    this.current_group = 0;
}

Main.prototype.toggleActivities = function () {
    for (let i = 0; i < this.activities.length; ++i) {
        let activity = this.activities[i];
        activity.getTag().toggle(activity.checkGroup(this.current_group));
    }
};

Main.prototype.addActivity = function (activity) {
    this.activities.push(activity);
    activity.rebindToHtml(this.section);
    let self = this;
    let item;
    this.footer.append(
        item = $('<SPAN>').addClass('btn theme-accent theme-icons clickable').text(activity.getTitle()).on('click', function () {
            if (!activity.is(":visible")) {
                self.activities.forEach(function (item) {
                    item.toggle(false);
                });
                activity.toggle();
            }
        })
    );

    activity.bindTag(item);
    item.toggle(activity.checkGroup(this.current_group));
    return item;
};

Main.prototype.start = function (data) {

};

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

        let base = this.addActivity(route_activity);
        this.addActivity(login_activity);
        let register_click = this.addActivity(register_activity);
        this.addActivity(logout_activity);
        this.addActivity(make_meeting_activity);

        login_activity.setRegisterLink(register_click);

        route_activity.toggle(true);

        let event = events.status_changed;

        new EventListener(event, this.thread, function (event) {
            self.current_group = event.flag;
            self.toggleActivities();
            route_activity.group = event.flag;
            route_activity.reprint();
            base.click();
        })
    } catch (e) {
        this.logger.log(ExceptionMessage.prototype.fromError(e))
    }
};