"use strict";

function VoteActivity(logger, event, check_survey, vote, ref) {
    Activity.call(this, logger, event, [0, 1]);
    this.check = check_survey;
    this.vote = vote;
    this.ref = ref;
    this.location.addClass('container');

    this.print();
}

VoteActivity.prototype = Object.create(Activity.prototype);
VoteActivity.prototype.constructor = VoteActivity;

VoteActivity.prototype.getTitle = function () {
    return 'Vote'
};

VoteActivity.prototype.print = function () {
    let self = this;
    let location = this.location;
    location.append($('<H1>').text('Vote').addClass('primary'));

};