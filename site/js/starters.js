"use strict";

function Main() {
    let body = $('body');
    let alerts;

    let body_width = 'col-10';
    let trail_width = 'col-2';

    let fragment = [
        $('<header>').addClass('col-12 fli-nogrow header flo flo-col').append(
            $('<DIV>').addClass('row').append(
                this.title = $('<DIV>').addClass(body_width),
                $('<H1>').addClass(trail_width).text('Messages')
            )
        ),
        $('<section>').addClass('col-12 flo flo-row').append(
            this.section = $('<DIV>').addClass(body_width + ' overflow-scroll'),
            alerts = $('<div>').addClass(trail_width + ' theme-divider overflow-scroll')
        ),
        $('<footer>').addClass('col-12 fli-nogrow footer').append(
            $('<DIV>').addClass('row').append(
                this.footer = $('<DIV>').addClass(body_width + ' row'),
                $('<SPAN>').addClass(trail_width).text('Développé par Pierre-Antoine GUILLAUME')
            )
        )
    ];
    body.append(fragment).addClass('flo flo-col');
    this.section.height(this.section.height());
    alerts.height(alerts.height());
    this.logger = new GeneralLogger(alerts, {default_theme: 'theme'});
    this.activities = [];
}

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
        this.title.append($('<H1>').text('Meeting Planner'));
        let route_factory = new RouteFactory(data.types);
        let routes = {};
        for (let key in data.routes){
            if (!data.routes.hasOwnProperty(key))
                continue;
            routes[key] = route_factory.make(data.routes[key]);
        }

        let route_activity = new RouteActivity(this.logger, Object.values(routes), new LineRoutePrinter(), data.group);
        let login_activity = new LoginActivity(this.logger, routes.login);
        let register_activity = new RegisterActivity(this.logger, routes.register);

        this.addActivity(route_activity);
        this.addActivity(login_activity);
        let register_click = this.addActivity(register_activity);

        login_activity.setRegisterLink(register_click);

        route_activity.toggle(true);
    } catch (e) {
        this.logger.log(ExceptionMessage.prototype.fromError(e))
    }
};