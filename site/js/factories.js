"use strict";


function Factory() {

}


Factory.prototype.make = function (data) {

};


function UserFactory() {
    Factory.call(this);
}

UserFactory.prototype = Object.create(Factory.prototype);
UserFactory.prototype.constructor = UserFactory;

UserFactory.prototype.make = function (data) {
    return new User(data.id, data.lastname, data.firstname, data.login);
};


function TypeFactory() {
    Factory.call(this);
}

TypeFactory.prototype = Object.create(Factory.prototype);
TypeFactory.prototype.constructor = TypeFactory;
TypeFactory.prototype.make = function (id, data) {
    if (data.type === 'remote') {
        return new RemoteType(id, data.name, data.origin)
    }
    else {
        return new ScalarType(id, data.name, data.kind)
    }
};

function RouteFactory(types) {
    Factory.call(this);
    let typefactory = new TypeFactory();
    this.types = {};
    for (let key in types) {
        if (!types.hasOwnProperty(key))
            continue;
        this.types[key] = typefactory.make(key, types[key]);
    }
}

RouteFactory.prototype = Object.create(Factory.prototype);
RouteFactory.prototype.constructor = RouteFactory;

RouteFactory.prototype.make = function (id, data) {
    let types = this.types;
    let fields = data.data.map(function (item) {
        return types[item].clone();
    });
    return new Route(id, data.title, data.abstract, data.url, fields, data.groups, data.accepts, data.type);
};


function MeetingFactory() {
    Factory.call(this);
}

MeetingFactory.prototype = Object.create(Factory.prototype);
MeetingFactory.prototype.constructor = MeetingFactory;

MeetingFactory.prototype.make = function (data) {
    return new Meeting(data.id, data.user, data.name);
};