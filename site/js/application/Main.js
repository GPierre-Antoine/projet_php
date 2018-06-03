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