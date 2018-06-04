"use strict";

function VoteActivity(logger, event, check_survey, vote, ref) {
    Activity.call(this, logger, event, [0, 1]);
    this.check = check_survey;
    this.vote = vote;
    let regex = /\/vote\/([0-9]+)/;
    this.ref = regex.exec(ref);
    this.location.addClass('container');
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
    if (this.ref === null) {
        location.append($('<P>').text('Aucun vote en cours').addClass('primary'));
        return;
    }

    let data = this.vote.data;
    let collection = data.map(function (item) {
        return item.clone().makeInput(self.constructor.name + '-' + item.id);
    });
    let data_items = data.map(function (item) {
        return item.getFormatedData();
    });
    this.location.append(collection);

    let name = this.location.find('#VoteActivity-name');
    let select = this.location.find('select');
    select.attr('multiple', true);
    self.logger.log(new SimpleMessage('Information en cours de récupération'));

    let button = $('<BUTTON>').addClass('btn btn-lg col-12 theme-accent theme-icons').text('Voter').attr('tab-index', 0).on('click', function () {
        if (name.val() === '') {
            self.logger.log(new SimpleMessage("Le champ <b>nom</b> n'est pas valué", {type: 'danger', html: true}));
            return;
        }
        let subdata = {name: name.val()};
        let values = select.val();
        if (!values.length) {
            self.logger.log(new SimpleMessage("Aucun horaire n'a été choisi", {type: 'danger', html: true}));
            return
        }
        for (let i = 0; i < values.length; ++i) {
            subdata.slot = values[i];
            self.vote.run({
                data: subdata,
                error: handle_error(self.logger),
                success: function () {
                    self.logger.log(new SimpleMessage("Vote Enregistré"));
                }
            });
        }
    });

    location.append(button);
    let meeting = this.ref[1];

    const options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric'};
    this.check.run({
        data: {meeting: meeting},
        error: handle_error(self.logger),
        success: function (data) {
            let factory = new MeetingFactory();
            let meeting = factory.make(data.data);
            select.append(meeting.getSlots().map(function (data) {
                return $('<OPTION>').text(data.time.toLocaleDateString('fr-Fr', options)).val(data.id)
            }))

            ;
        }
    });


};