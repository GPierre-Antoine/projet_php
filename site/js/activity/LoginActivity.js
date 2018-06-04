"use strict";

function LoginActivity(logger, thread, route_login) {
    Activity.call(this, logger, thread, [0]);
    this.route_login = route_login;
    this.location.addClass('container');
    this.register_link = undefined;
}

LoginActivity.prototype = Object.create(Activity.prototype);
LoginActivity.prototype.constructor = LoginActivity;
LoginActivity.prototype.getTitle = function () {
    return 'Login'
};

LoginActivity.prototype.setRegisterLink = function (link) {
    this.register_link = link;
};

LoginActivity.prototype.print = function () {
    let counter = 0;
    let data = this.route_login.data;
    let self = this;
    let collection = data.map(function (item) {
        return item.makeInput(self.constructor.name + '-' + item.id);
    });
    let data_items = data.map(function (item) {
        return item.getFormatedData();
    });
    collection.unshift($('<H1>').text('Connexion').addClass('primary'));
    collection.push($('<BUTTON>').addClass('btn btn-lg col-12 theme-accent theme-icons').text('Se connecter').attr('tab-index', 0).on('click', function () {
        let ajax_data = accumulate_data(self.logger, data_items);
        if (typeof ajax_data === "undefined")
            return;
        self.logger.log(new SimpleMessage("Tentative de connexion"));
        self.route_login.run({
            data: ajax_data,
            error: handle_error(self.logger),
            success: function () {
                self.logger.log(new SimpleMessage("Connexion réussie"));
                let ev = events.status_changed;
                ev.flag = 1;
                self.thread.fire(ev);
            }
        });
    }));

    function handle_success(logger, message) {
        return function () {
            logger.log(new SimpleMessage(message))
        }
    }

    collection.push($('<SPAN>').addClass('clickable').text("Pas inscrit ? S'inscire").on('click', function () {
        self.register_link.click();
    }));
    this.location.append(collection);
};

