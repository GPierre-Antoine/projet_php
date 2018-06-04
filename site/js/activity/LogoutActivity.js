"use strict";


function LogoutActivity(logger, thread, route_logout) {
    Activity.call(this, logger, thread, [1]);
    this.route_logout = route_logout;
    this.location.addClass('container');
}

LogoutActivity.prototype = Object.create(Activity.prototype);
LogoutActivity.prototype.constructor = LogoutActivity;
LogoutActivity.prototype.getTitle = function () {
    return 'Logout'
};

LogoutActivity.prototype.print = function () {
    let self = this;
    let collection = [$('<H1>').text('Déconnexion').addClass('primary'),
        $('<BUTTON>').addClass('btn btn-lg col-12 theme-accent theme-icons').text('Se déconnecter').attr('tab-index', 0).on('click', function () {
            self.logger.log(new SimpleMessage("Déconnexion en cours"));
            self.route_logout.run({
                data: {},
                error: handle_error(self.logger),
                success: function () {
                    self.logger.log(new SimpleMessage("Déconnexion réussie"));
                    let ev = events.status_changed;
                    ev.flag = 0;
                    self.thread.fire(ev);
                }
            });
        })];
    this.location.append(collection);
};


