sp.errorReport = function() {
    var text = sp.getSelectedText();
    if (text.length) {
        if(text.length < 10)
            return sp.message.error('Вы выбрали слишком короткий текст!');
        if (text.length > 250)
            return sp.message.error('Вы выбрали слишком большой объем текста!');
        var url = location.href, node = 'page';
        if (window.getSelection) {
            var n = $(window.getSelection().anchorNode), p = n.closest('.product');
            if (p.length) {
                url = '/product/' + p.data('id') + '/';
                if (n.closest('h1, h4').length) node = 'name';
                else if (n.closest('.content, .description').length) node = 'desc';
                else if (n.closest('.details').length) node = 'prop';
            }
        }
        sp.post('/errorReport/add/', {url: url, node: node, text: text}).done(function(data) {
            if (data.errors)
                for (var key in data.errors)
                    return sp.message.error(data.errors[key][0]);
            sp.message.notice('Ваше замечание принято!', 'Спасибо');
        });
    }
};
$(function() {
    var ctrl = false;
    sp.document.focus();
    sp.document.keyup(function(event) {
        if (event.which == $.ui.keyCode.CTRL) ctrl = false;
    }).keydown(function(event) {
        if (event.which == $.ui.keyCode.CTRL) ctrl = true;
        if(ctrl && event.which == $.ui.keyCode.ENTER)
            sp.errorReport();
    });
});