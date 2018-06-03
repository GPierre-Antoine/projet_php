"use strict";

function RegisterActivity(logger, thread, route_register, loginactivity) {
    Activity.call(this, logger, thread, [0]);
    this.location.addClass('container');
    this.route_register = route_register;
    this.loginactivity = loginactivity;
    this.print();
}

RegisterActivity.prototype = Object.create(Activity.prototype);
RegisterActivity.prototype.constructor = RegisterActivity;
RegisterActivity.prototype.getTitle = function () {
    return 'Register'
};
RegisterActivity.prototype.print = function () {
    let counter = 0;
    let data = this.route_register.data;
    let self = this;
    let collection = data.map(function (item) {
        return item.makeInput(self.constructor.name + '-' + (item.id));
    });
    let data_items = data.map(function (item) {
        return item.getFormatedData();
    });
    collection.unshift($('<H1>').text('Register').addClass('primary'));
    collection.push($('<BUTTON>').addClass('btn btn-lg col-12 theme-accent theme-icons').text('Se connecter').attr('tab-index', 0).on('click', function () {
        let ajax_data = accumulate_data(self.logger, data_items);
        if (typeof ajax_data === "undefined")
            return;
        self.logger.log(new SimpleMessage("Inscription en cours"));
        self.route_register.run({
            data: ajax_data,
            error: handle_error(self.logger),
            success: function () {
                self.logger.log(new SimpleMessage("Inscription Réussie."));
                let login = self.loginactivity.location;
                login.find('input[name=login]').val(self.location.find('input[name=login]').val());
                login.find('input[name=password]').val(self.location.find('input[name=password]').val());
                login.find('button').click();

            }
        });
    }));

    this.location.append(collection);
};

