"use strict";

function handle_error(logger) {
    /**
     * @param text
     * @param text.responseJSON
     * @param text.responseText
     */
    return function (text) {
        if (typeof text === "object") {
            if (typeof text.responseJSON !== "undefined") {
                text = JSON.stringify(text.responseJSON);
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
            logger.log(new SimpleMessage("Valeur invalide pour le champ <b>" + val.field + "</b>", {
                type: 'danger',
                html: true
            }));
            return
        }
        ajax_data[val.name] = val.input.val();
    }
    return ajax_data;
}