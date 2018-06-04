"use strict";

function CreateMeetingActivity(logger, thread, route_create_meeting, route_add_slot_to_survey) {
    Activity.call(this, logger, thread, [1]);
    this.location.addClass('container');
    this.create_meeting = route_create_meeting;
    this.add_slot = route_add_slot_to_survey;
    this.print();
}

CreateMeetingActivity.prototype = Object.create(Activity.prototype);
CreateMeetingActivity.prototype.constructor = CreateMeetingActivity;

CreateMeetingActivity.prototype.getTitle = function () {
    return 'Organiser une réunion';
};

CreateMeetingActivity.prototype.print = function () {
    let self = this;
    this.location.append($('<H1>').text('Plannifier une réunion').addClass('primary'));
    let data = this.create_meeting.data;

    let collection = data.map(function (item) {
        return item.makeInput(self.constructor.name + '-' + item.id);
    });
    this.location.append(collection);

    let add_date = this.add_slot.data.filter(function (item) {
        return item.id === 'date';
    })[0].clone();

    add_date.setName("Ajouter une date");

    this.location.append(add_date.makeInput('base_date'));

    let date_location = $('<DIV>').addClass('scrollx form-group flo flo-row flo-noshrink col-12 mb-16');
    this.location.append(date_location);

    function sortObject(o) {
        return Object.keys(o).sort().reduce((r, k) => (r[k] = o[k], r), {});
    }

    let date_list = {};


    let default_hours = [];
    let checkbox_template = $('<input>').attr('type', 'checkbox');
    for (let i = 8; i < 21; i += 2) {
        let hour = i.toString().padStart(2, '0');
        default_hours.push($("<LABEL>").append(checkbox_template.clone().attr('name', hour), $('<SPAN>').text(' ' + hour + 'h')));
    }

    let options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};

    function print_date(id, content) {
        let date = new Date(id);
        return $('<DIV>').addClass('col-2 border-divider alert mr-20').append(
            $('<HEADER>').addClass('theme-accent capitalize').text(date.toLocaleDateString('fr-FR', options)),
            $('<SECTION>').addClass('flo flo-col padding-20').append(content),
            $('<FOOTER>').append(
                $('<BUTTON>').addClass('btn btn-danger col-12').text('Supprimer le jour').on('click', function () {
                    delete date_list[id];
                    reprint();
                }),
            )
        );
    }

    function reprint() {
        date_location.empty();
        sortObject(date_list);
        let loc = [];
        for (let i in date_list) {
            if (!date_list.hasOwnProperty(i))
                continue;
            let date = date_list[i];
            loc.push(print_date(i, date));
        }
        date_location.append(loc);
    }

    function get_checkbox(label) {
        return label.find('input')
    }

    function checkbox_checked(checkbox) {
        return checkbox.is(':checked');
    }

    function validate_create_meeting() {
        let whole = [];
        for (let i in date_list) {
            if (!date_list.hasOwnProperty(i))
                continue;
            let checkbox = date_list[i].map(get_checkbox).filter(checkbox_checked);
            if (!checkbox.length) {
                let date = new Date(i);
                self.logger.log(new SimpleMessage("Aucun horaire n'est coché pour la date du <b>" + date.toLocaleDateString('fr-FR', options) + "</b>.<br>Supprimez la date ou selectionnez un horaire.", {
                    type: "danger",
                    html: true
                }));
                return;
            }
            for (let j in checkbox) {
                if (!checkbox.hasOwnProperty(j)) {
                    continue;
                }
                whole.push(i + ' ' + checkbox[j].attr('name') + ':00:00')
            }
        }

        let accumulation = accumulate_data(self.logger, data.map(function (item) {
            return item.getFormatedData();
        }));
        if (typeof accumulation === "undefined") {
            return;
        }

        if (whole.length < 2) {
            if (whole.length < 1) {
                self.logger.log(new SimpleMessage("Aucun horaire n'a été sélectionné.", {type: 'danger'}));
                return;
            }
            self.logger.log(new SimpleMessage("Selectionnez au moins deux horaires.", {type: 'danger'}));
            return;
        }

        let thread = self.thread;
        self.logger.log(new SimpleMessage('Création du sondage ...'));
        self.create_meeting.run({
            data: accumulation,
            success: function (feedback) {
                let counter = 0;
                let counter_total = 0;
                let last_message;
                for (let i = 0; i < whole.length; ++i) {
                    self.add_slot.run(
                        {
                            data: {meeting: feedback.data.id, date: whole[i]},
                            complete: function (feedback2) {
                                if (typeof feedback2.responseJSON !== "undefined" && typeof feedback2.responseJSON.status !== "undefined" && feedback2.responseJSON.status === true) {
                                    counter += 1;
                                }
                                else {
                                    last_message = feedback2;
                                }
                                counter_total += 1;
                                if (counter_total === whole.length) {
                                    if (counter === counter_total) {
                                        self.logger.log(new SimpleMessage("Sondage créé"));
                                        let url = "/localhost/vote/" + feedback.data.id;
                                        self.logger.log(new SimpleMessage("Pour voter sur le sondage, suivez le lien suivant : <a href='"+url+"'>"+url+"</a>", {html: true}));
                                        let ev = events.survey_created;
                                        ev.flag = feedback.data.id;
                                        thread.fire(ev);
                                    }
                                    else {
                                        handle_error(self.logger)(last_message);
                                    }
                                }
                            }
                        });
                }

            },
            error: handle_error(self.logger)
        })

    }

    add_date.getFormatedData().input.on('change', function () {
        if (typeof date_list[this.value] === "undefined") {
            date_list[this.value] = default_hours.map(function (element) {
                return element.clone()
            });
        }
        reprint();
    });

    this.location.append($('<BUTTON>').addClass('btn btn-lg col-12 theme-accent theme-icons').text("Créer le sondage").on('click', validate_create_meeting))

};