"use strict";


function AbstractMessage() {
}

AbstractMessage.prototype.accept = function (messageVisitor) {
};

function AbstractLogger() {
}

AbstractLogger.prototype.log = function (message) {
};

function AbstractMessageVisitor() {
}

AbstractMessageVisitor.prototype.visitSimple = function (Message) {
};
AbstractMessageVisitor.prototype.visitException = function (Exception) {
};

function SimpleMessage(message, options) {
    options = options || {};
    AbstractMessage.call(this);
    this.message = message;
    this.type = options.type;
}

SimpleMessage.prototype = Object.create(AbstractMessage.prototype);
SimpleMessage.prototype.constructor = SimpleMessage;
SimpleMessage.prototype.getMessage = function () {
    return this.message;
};
SimpleMessage.prototype.accept = function (messageVisitor) {
    messageVisitor.visitSimple(this);
};
SimpleMessage.prototype.getType = function () {
    return this.type;
};

function ExceptionMessage(message, code, file, line, stacktrace, column) {
    AbstractMessage.call(this);
    this.message = message;
    this.fileName = file;
    this.lineNumber = line;
    this.stack = stacktrace;
    this.column = column;
}

ExceptionMessage.prototype = Object.create(AbstractMessage.prototype);
ExceptionMessage.prototype.constructor = ExceptionMessage;
ExceptionMessage.prototype.getMessage = function () {
    return this.message;
};
ExceptionMessage.prototype.getCode = function () {
    return this.code;
};
ExceptionMessage.prototype.getFile = function () {
    return this.fileName;
};
ExceptionMessage.prototype.getLine = function () {
    return this.lineNumber;
};
ExceptionMessage.prototype.getStacktrace = function () {
    return this.stack;
};
ExceptionMessage.prototype.getColumn = function () {
    return this.column;
};
ExceptionMessage.prototype.accept = function (visitor) {
    visitor.visitException(this);
};
ExceptionMessage.prototype.fromError = function (error) {
    return new ExceptionMessage(error.message, 0, error.fileName, error.lineNumber, error.stack, error.columnNumber);
};

function ExceptionMessageLogger(fragment) {
    this.fragment = fragment;
}

function SimpleMessageLogger(fragment, options) {
    this.fragment = fragment;
    this.options = options || {};
    this.options.timeout = this.options.timeout || 1500;
    this.options.default_theme = this.options.default_theme || 'success';
    this.options.notimeout = typeof options.notimeout === 'undefined'
        ? true
        : options.notimeout;
}

function LogDispatcher(fragment, options) {
    AbstractMessageVisitor.call(this);
    options = options || {};
    if (typeof options.debug === 'undefined') {
        options.debug = false;
    }
    this.exceptionLogger = new ExceptionMessageLogger(fragment, options);
    this.simpleMessageLogger = new SimpleMessageLogger(fragment, options);
}

LogDispatcher.prototype = Object.create(AbstractMessageVisitor.prototype);
LogDispatcher.prototype.constructor = LogDispatcher;

LogDispatcher.prototype.visitException = function (message) {
    this.exceptionLogger.log(message);
};
LogDispatcher.prototype.visitSimple = function (message) {
    this.simpleMessageLogger.log(message);
};


function GeneralLogger(emplacement, options) {
    AbstractLogger.call(this);
    options = options || {};
    this.dispatcher = new LogDispatcher(emplacement, options);
}

GeneralLogger.prototype = Object.create(AbstractLogger.prototype);
GeneralLogger.prototype.constructor = GeneralLogger;

GeneralLogger.prototype.log = function (message) {
    message.accept(this.dispatcher);
};

ExceptionMessageLogger.prototype.log = function (ex) {
    let alert = $('<DIV>').addClass('alert alert-danger sans-serif');
    alert.append($('<BUTTON>')
        .html('&times;')
        .addClass('push-right close')
        .on('click', function () {
            alert.remove();
        }));

    let summary = $('<SUMMARY>')
        .append($('<EM>')
            .addClass('closed')
            .text('Afficher plus ...'));

    let inner_div = $('<DIV>');
    let details = $('<DETAILS>')
        .addClass('progressive-disclosure word-wrap')
        .append(summary)
        .append(inner_div);


    let message = null;
    if (ex.getMessage().length > 50) {
        message = $('<DETAILS>')
            .addClass('progressive-disclosure')
            .append($('<SUMMARY>')
                .append(
                    $('<EM>').addClass('opened').text('Cacher'),
                    $('<EM>')
                        .addClass('closed')
                        .text(ex.getMessage()
                            .substr(0, 40) + '...')))
            .append($('<DIV>')
                .addClass('limit-height monospace')
                .text(ex.getMessage()));
    } else {
        message = $('<PRE>')
            .text(ex.getMessage());
    }
    message.addClass('monospace');

    inner_div.append($('<SPAN>')
        .text('Message : '))
        .append(message);

    if (typeof ex.getCode() !== 'undefined') {
        inner_div.append($('<DIV>')
            .append($('<SPAN>')
                .text('Code    : '))
            .append($('<SPAN>')
                .text(ex.getCode())));
    }

    if (typeof ex.getFile() !== 'undefined') {
        inner_div.append($('<DIV>')
            .append($('<SPAN>')
                .text('Location: '))
            .append($('<SPAN>')
                .text(ex.getFile())));
    }

    if (typeof ex.getLine() !== 'undefined' && ex.getLine() !== 0) {
        let col_text = '';
        if (typeof ex.getColumn() !== 'undefined' && ex.getColumn() !== 0) {
            col_text = ':' + ex.getColumn();
        }
        inner_div.append($('<DIV>')
            .append($('<SPAN>')
                .text('Line    : '))
            .append($('<SPAN>')
                .text(ex.getLine() + col_text)));
    }

    if (typeof ex.getStacktrace() !== 'undefined') {

        let trace = ex.getStacktrace()
            .split('#');
        trace.shift();

        if (!trace.length) {
            trace = ex.getStacktrace()
                .split('\n');
        }

        details.append($('<DETAILS>')
            .append($('<SUMMARY>')
                .append($('<SPAN>')
                    .text('Click to show stack')))
            .append($('<UL>')
                .addClass('limit-height monospace')
                .append(trace.filter(function (value) {
                    return value !== '';
                }).map(function (value) {
                    return $('<LI>').addClass('monospace')
                        .text(value);
                }))));
    }

    this.fragment.prepend(alert.append($('<DIV>')
        .append($('<SPAN>')
            .text('Une erreur est survenue !'))
        .append(details)));
};

SimpleMessageLogger.prototype.log = function (ex) {
    let self = this;
    let type = ex.getType();
    if (typeof type === "undefined") {
        type = this.options.default_theme;
    }
    let alert = $('<DIV>').addClass('alert alert-' + type);

    function erase() {
        alert.remove();
        if (timer) {
            clearTimeout(timer);
        }
    }

    let timer = 0;
    let button = $('<BUTTON>')
        .addClass('pull-right close theme-icons')
        .on('click', erase)
        .html('&times;');
    let text = $('<DIV>')
        .addClass('word-wrap')
        .append($('<SPAN>')
            .text('Message : '))
        .append($('<SPAN>')
            .text(ex.getMessage()));
    alert
        .append(button)
        .append(text);
    if (!this.options.notimeout) {
        alert.on('mouseout', function () {
            if (timer) {
                clearTimeout(timer);
            }
            timer = setTimeout(erase, self.options.timeout);
        });
    }
    this.fragment.prepend(alert);
};