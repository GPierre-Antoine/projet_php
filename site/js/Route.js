"use strict";


function Route(id, title, abstract, url, data, groups, accept, type) {
    this.id = id;
    this.title = title;
    this.abstract = abstract;
    this.url = url;
    this.data = data;
    this.groups = groups;
    this.type = type;
    this.accept = accept;
}

Route.prototype.run = function (data) {
    data.url = this.url;
    data.headers = {
        "Accept": this.accept,
        "Content-Type": "application/x-www-form-urlencoded"
    };
    data.type = this.type;
    return $.ajax(data);
};

Route.prototype.clone = function () {
    return jQuery.extend(true, {}, this);
};

