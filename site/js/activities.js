"use strict";

function handle_error(logger) {
    return function (text) {
        if (typeof text === "object") {
            if (typeof text.responseJson !== "undefined") {
                text = JSON.stringify(text.responseJson);
            }
            else
                text = text.responseText;
        }
        logger.log(new SimpleMessage(JSON.stringify(text), {type: "danger"}));
    };
}

function handle_success(logger, message) {
    return function () {
        logger.log(message)
    }
}

function accumulate_data(logger, data) {
    let ajax_data = {};
    for (let i = 0; i < data.length; ++i) {
        let val = data[i];
        if (val.input.val() === '') {
            logger.log(new ExceptionMessage("Valeur invalide pour le champ : " + val.field));
            return
        }
        ajax_data[val.name] = val.input.val();
    }
    return ajax_data;
}

function Activity(logger, node) {
    this.logger = logger;
    node = node || $('<DIV>');
    this.location = node;
    this.location.toggle(false);
}

Activity.prototype.rebindToHtml = function (new_location) {
    if (this.location.parent())
        this.location.detach();
    new_location.append(this.location);
};
Activity.prototype.getTitle = function () {
    return 'Stub';
};
Activity.prototype.toggle = function (arg) {
    this.location.toggle(arg);
};
Activity.prototype.is = function (arg) {
    this.location.is(arg);
};
Activity.prototype.print = function () {
};
Activity.prototype.reprint = function () {
    this.location.empty();
    this.print();
};

function LoginActivity(logger, route_login) {
    Activity.call(this, logger);
    this.route_login = route_login;
    this.location.addClass('container');
    this.register_link = undefined;
    this.print();
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
        return item.makeInput(self.constructor.name + '-' + (++counter));
    });
    let data_items = data.map(function(item){
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
            success: handle_success(self.logger, "Connexion Réussie")
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

function RegisterActivity(logger, route_register) {
    Activity.call(this, logger);
    this.location.addClass('container');
    this.route = route_register;
    this.print();
}

RegisterActivity.prototype = Object.create(Activity.prototype);
RegisterActivity.prototype.constructor = RegisterActivity;
RegisterActivity.prototype.getTitle = function () {
    return 'Register'
};

RegisterActivity.prototype.print = function () {
    let counter = 0;
    let data = this.route.data;
    let self = this;
    let collection = data.map(function (item) {
        return item.makeInput(self.constructor.name + '-' + (++counter));
    });
    let data_items = data.map(function(item){
        return item.getFormatedData();
    });
    collection.unshift($('<H1>').text('Register').addClass('primary'));
    collection.push($('<BUTTON>').addClass('btn btn-lg col-12 theme-accent theme-icons').text('Se connecter').attr('tab-index', 0).on('click', function () {
        let ajax_data = accumulate_data(self.logger, data_items);
        if (typeof ajax_data === "undefined")
            return;
        self.logger.log(new SimpleMessage("Inscription en cours"));
        self.route.run({
            data: ajax_data,
            error: handle_error(self.logger),
            success: handle_success(self.logger, "Inscription Réussie")
        });
    }));

    this.location.append(collection);
};

function RouteActivity(logger, routes, printer, group) {
    Activity.call(this, logger);
    this.routes = routes;
    this.printer = printer;
    this.group = group;
    this.print();
}

RouteActivity.prototype = Object.create(Activity.prototype);
RouteActivity.prototype.constructor = RouteActivity;
RouteActivity.prototype.getTitle = function () {
    return 'Route'
};
RouteActivity.prototype.setGroup = function (group) {
    this.group = group;
};
RouteActivity.prototype.print = function () {
    let self = this;
    let elements = this.routes.filter(function (route) {
        return route.groups.indexOf(self.group) !== -1;
    }).map(function (i) {
        return self.printer.print(i)
    });
    this.location.append(elements);
};
