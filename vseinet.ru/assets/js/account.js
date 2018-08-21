$(function() {
    var form = $('#user-edit-form');
    form.find('#edit_birthday').datepicker({
        changeMonth: true,
        changeYear: true,
    });
});