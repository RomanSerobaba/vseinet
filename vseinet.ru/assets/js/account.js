$(function() {
    $('#account_edit_birthday').datepicker({
        changeMonth: true,
        changeYear: true,
    });

    $('[name*=geoCityName]').geoCity({
        selectorId: '[name*=geoCityId]'
    });
    $('[name*=geoStreetName]').geoStreet({
        selectorId: '[name*=geoStreetId]',
        selectorGeoCityId: '[name*=geoCityId]'
    });

    var contacts = $('#user-contacts').on('click', '.contact-edit', function(e) {
        e.preventDefault();
        var target = $(this);
        var dialog = sp.dialog();
        sp.get(target.prop('href')).then(function(response) {
            dialog.append(response.html).removeClass('loading');
            var form = dialog.find('form').on('submit', function(e) {
                e.preventDefault();
                sp.post(target.prop('href'), form.serializeArray()).then(function(response) {
                    var tr = target.closest('tr');
                    if (tr.length) {
                        tr.replaceWith(response.html);
                    } else {
                        contacts.find('tbody').append(response.html);
                    }
                    dialog.dialog('close');
                });
            });
            dialog.dialog('option', 'title', form.prop('title'));
        });
    });
    contacts.on('click', '.contact-delete', function(e) {
        e.preventDefault();
        if (confirm('Подтвердите удаление контакта')) {
            sp.get(e.target.href).then(function() {
                $(e.target).closest('tr').remove();
            });
        }
    });

    var addresses = $('#user-addresses').on('click', '.address-edit', function(e) {
        e.preventDefault();
        var target = $(this);
        var dialog = $('<div class="loading"></div>').appendTo('body').dialog({
            closeText: 'Закрыть',
            modal: true,
            minWidth: 600,
            position: {
                using: function(pos) {
                    pos.top = 45 + $(window).scrollTop();
                    $(this).css(pos);
                }
            }
        });
        sp.get(target.prop('href')).then(function(response) {
            dialog.append(response.html).removeClass('loading');
            var form = dialog.find('form').on('submit', function(e) {
                e.preventDefault();
                sp.post(target.prop('href'), form.serializeArray()).then(function(response) {
                    var tr = target.closest('tr');
                    if(tr.length) {
                        tr.replaceWith(response.html);
                    } else {
                        addresses.find('tbody').append(response.html);
                    }
                    dialog.dialog('close').remove();
                });
            });
            dialog.dialog('option', 'title', form.prop('title'));
            setTimeout(function() {
                form.find('[name*=geoCityName]').geoCity({
                    appendTo: dialog,
                    selectorId: '[name*=geoCityId]'
                });
                form.find('[name*=geoStreetName]').geoStreet({
                    appendTo: dialog,
                    selectorId: '[name*=geoStreetId]',
                    selectorGeoCityId: '[name*=geoCityId]'
                });
            }, 4);
        });
    });
    addresses.on('click', '.address-delete', function(e) {
        e.preventDefault();
        if (confirm('Подтвердите удаление адреса доставки')) {
            sp.get(e.target.href).then(function() {
                $(e.target).closest('tr').remove();
            });
        }
    });
});