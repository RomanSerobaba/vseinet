$.widget('sp.ajaxcontent', {
    options: {
        data: function() {
            return {};
        },
        dialog: {
            close: function(e, ui) {
                $(this).remove();
            },
            closeText: 'Закрыть',
            modal: true,
            minWidth: 600,
            position: {
                using: function(pos) {
                    pos.top = 45 + $(window).scrollTop();
                    $(this).css(pos);
                }
            }
        },
        target: null,
        load: $.noop
    },
    _create: function() {
        var target = this.options.target ? $(this.options.target) : this.element;
        this._on(target, {
            'click': function(e) {
                e.preventDefault();
                this.load(this.options.url || e.target.dataset.url || e.target.href, this.options.data.call(e.target));
            }
        });
    },
    load: function(url, data) {
        var dialog = $('<div class="loading"></div>').hide().appendTo('body').dialog(this.options.dialog);
        var load = this.options.load;
        sp.get(url, data).then(function(response) {
            load.call(dialog.append(response.html));
        }).always(function() {
            dialog.removeClass('loading');
        }).fail(function() {
            dialog.html('404. Страница не найдена.');
        });
    },
    _setOption: function(key, value) {
        if ('dialog' === key) {
            $.widget.extend(this.options.dialog, value);
        } else {
            this._super(key, value);
        }
    }
});

sp.openAjaxDialog = function(a, options) {
    var fake = a.clone().appendTo('body').css({ display: 'none' });
    options.dialog.close = function() {
        $(this).remove();
        fake.remove();
    };
    fake.ajaxcontent(options).click();
}