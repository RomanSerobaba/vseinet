$(function() {
    $('#user-account').tabs({
        active: 2,
        beforeActivate: function(event, ui) {
            window.location = ui.newTab.find('a').prop('href');
            return false;
        }
    });
});