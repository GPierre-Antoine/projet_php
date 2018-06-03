"use strict";

function RouteActivity(logger, thread, routes, printer, group) {
    Activity.call(this, logger, thread, [0, 1]);
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

