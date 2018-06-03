"use strict";


function Factory (){

}


Factory.prototype.make = function(data){

};


function UserFactory (){
    Factory.call(this);
}

UserFactory.prototype = Object.create(Factory.prototype);
UserFactory.prototype.constructor = UserFactory;

UserFactory.prototype.make = function(data){
    return new User(data.id, data.lastname, data.firstname, data.login);
};

