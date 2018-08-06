$(function() {
    $('[data-popup]').click(function(event) {
        var trigger = $(this), popup = $('.popup#' + trigger.data('popup'));
        if (popup.length) {
            popup.dialog('open');
            event.preventDefault();
        }
    });
    var popups = $('.popup').each(function() {
        var popup = $(this);
        popup.dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            closeText: 'Закрыть',
            minWidth: popup.data('minWidth')|0 || 400,
            dialogClass: popup.data('class') || ''
        });
    });
});