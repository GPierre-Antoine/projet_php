"use strict";

function Main() {
    let body = $('body');
    let alerts;

    let body_width='col-10';
    let trail_width='col-2';

    let fragment = [
        $('<header>').addClass('col-12 fli-nogrow header flo flo-col').append(
            $('<DIV>').addClass('row').append(
                this.title=$('<DIV>').addClass(body_width),
                $('<H1>').addClass(trail_width).text('Messages')
            )
        ),
        this.section = $('<section>').addClass('col-12 flo flo-row').append(
            $('<DIV>').addClass(body_width),
            alerts=$('<div>').addClass(trail_width+' theme-divider')
        ),
        $('<footer>').addClass('col-12 fli-nogrow footer').append(
            $('<DIV>').addClass('row').append(
                this.footer=$('<DIV>').addClass(body_width),
                $('<SPAN>').addClass(trail_width).text('Développé par Pierre-Antoine GUILLAUME')
            )
        )
    ];
    body.append(fragment).addClass('flo flo-col');
    this.logger = new GeneralLogger(alerts, {default_theme: 'theme'});
}


Main.prototype.start = function (data) {

};


function MeetingApp() {
    Main.call(this);
}

MeetingApp.prototype = Object.create(Main.prototype);
MeetingApp.prototype.constructor = MeetingApp;

MeetingApp.prototype.start = function () {
    try {
        this.title.append($('<H1>').text('Meeting Planner'));
        this.section.append()
    } catch (e) {
        this.logger.log(ExceptionMessage.prototype.fromError(e))
    }
};