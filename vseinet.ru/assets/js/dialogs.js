sp.message = new function() {
    return $.extend(this, {
        error: function(text, title) {
            this.dialog('error', text, title || 'Ошибка');
        },
        notice: function(text, title) {
            this.dialog('notice', text, title || 'Внимание');
        },
        dialog: function(type, text, title, options) {
            if (type)
                text = '<div class="' + type + '"><div class="icon"/>' + text + '</div>';
            var _dialog = $('<div/>').html(text).hide().appendTo('body');
            setTimeout(function() {
                _dialog.dialog($.extend({
                    close: function(event, ui) {
                        $(this).remove();
                    },
                    closeText: 'Закрыть',
                    minWidth: 400,
                    modal: true,
                    title: title
                }, options || {}));
            }, 1);
            return _dialog;
        }
    });
};