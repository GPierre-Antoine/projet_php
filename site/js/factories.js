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


function VoteFactory() {
    Factory.call(this);
}

VoteFactory.prototype = Object.create(Factory.prototype);
VoteFactory.prototype.constructor = VoteFactory;

VoteFactory.prototype.make = function (data) {
    return new Vote(data.id, data.name)
};


function SlotFactory() {
    Factory.call(this);
    this.vote_factory = new VoteFactory();
}

SlotFactory.prototype = Object.create(Factory.prototype);
SlotFactory.prototype.constructor = SlotFactory;
SlotFactory.prototype.make = function (data) {
    let slot = new Slot(data.id, new Date(data.time * 1000));
    for (let i = 0; i < data.votes.length; ++i) {
        slot.addVote(this.vote_factory.make(data.votes[i]));
    }
    return slot;
};

function MeetingFactory() {
    Factory.call(this);
    this.slot_factory = new SlotFactory();
}

MeetingFactory.prototype = Object.create(Factory.prototype);
MeetingFactory.prototype.constructor = MeetingFactory;

MeetingFactory.prototype.make = function (data) {
    let m = new Meeting(data.id, data.user, data.name);
    if (typeof data.slots !== "undefined")
        for (let i = 0; i < data.slots.length; ++i) {
            m.addSlot(this.slot_factory.make(data.slots[i]));
        }
    return m;
};