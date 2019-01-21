$.widget('sp.form', {
    options: {
        submit: function (xhr) {
            var element = this;
            xhr.done(function (data) {
                if (!data.errors || 0 === data.errors.length) {
                    element.form('submit');
                }
            });
        },
        error: $.noop,
        beforeSubmit: $.noop,
        afterResponse: $.noop,
        validate: $.noop,
        action: ''
    },
    _create: function () {
        this._on({
            submit: function (e) {
                e.preventDefault();
                this._validate(true);
            }
        });

        var inputs = this.element.find('input, textarea, select');
        $.map(inputs, $.proxy(function (input) {
            if (input.name) {
                this._add(input);
            }
        }, this));

        this.element.on('click', '.error .status', function () {
            $(this).closest('div').removeClass('error').find('.error').remove();
        });

        Inputmask("+7 (999) 999-99-99").mask(this.element.find('input.phone'));

        setTimeout($.proxy(function() {
            this._on({
                change: function(e) {
                    var input = e.target;
                    if (input.id) {
                        this._add(input, true);
                        if (this.element.is(':visible')) {
                            this._validate();
                        }
                    }
                }
            });
        }, this), 250);
    },
    _add: function (input, change) {
        var inputs = this.element.data('inputs') || {};
        inputs[input.id] = {id: input.id, change: change || (input.value ? true : false)};
        this.element.data('inputs', inputs);
    },
    _validate: function (submit) {
        var form = this.element, callback;
        if (submit) {
            callback = this.options.submit;
            this.options.beforeSubmit.call(form);
        } else {
            callback = this.options.validate;
        }
        var data = form.serializeArray();
        if (submit) {
            data.push({name: 'submit', value: 1});
        }
        var trigger = $(document.activeElement);
        if (trigger.is('[type=submit]')) {
            var name = trigger.prop('name');
            if (name)
                data.push({name: name, value: 1});
        }
        var xhr = sp.post(form.prop('action'), data);
        callback.call(this.element, xhr);
        var inputs = this.element.data('inputs');
        var that = this;
        xhr.done(function (data) {
            var errors = [];
            for (var id in inputs) {
                var input = form.find('#' + inputs[id].id), row = input.closest('div');
                if (submit || inputs[id].change) {
                    var error = false;
                    if (data.errors)
                        error = data.errors[id];
                    if (error) {
                        row.addClass('error').removeClass('ok');
                        var message = row.find('.error');
                        if (message.length == 0)
                            row.append(message = $('<div class="error"/>'));
                        message.html(error[0]);
                        errors.push({
                            field: id,
                            message: error[0]
                        });
                    } else {
                        row.removeClass('error').find('.error').remove();
                        if (input.val()) {
                            row.addClass('ok');
                        } else {
                            row.removeClass('ok');
                        }
                    }
                } else {
                    row.removeClass('ok');
                }
            }
            if (errors.length) {
                that.options.error.call(that.element, errors, submit);
            }
            that.options.afterResponse.call(that.element, data);
        });
    },
    submit: function () {
        this._off(this.element, 'submit');
        this.element.submit();
    }
});
$(function () {
    $('form.validate').form();

    $('form').on('click', '.error .status', function() {
        $(this).closest('div').removeClass('error').find('.error').remove();
    });
});
