$(function() {
    var status = $('#Status');
    if (status.is('.popup')) {
        status.dialog({
            open: function() {
                status.find('.request').show().next().hide();
            }
        });
    }
    status.form({
        submit: function(xhr) {
            var loading = status.find('.loading').show();
            xhr.done(function(data) {
                if (data.html) {
                    var request = status.find('.request').hide();
                    request.next().show().find('.row:first').html(data.html);
                    var reset = status.find('.reset').one('click', function() {
                        request.show().next().hide();
                    });
                    var row = reset.closest('.row').hide();
                    setTimeout(function() {
                        row.show();
                    }, 3000);
                }
            }).always(function() {
                loading.hide();
            }).fail(function() {
                status.find('[name*=submit]').click();
            });
        }
    });
    status.find('[name*=submit]').click(function() {
        status.form('submit');
    });

    var timer = null,
        help = {};

    function lfsKeydown() {
        var lfs = $('[name$="[userData][fullname]"]'),
            lfsHelp = $('#lfsHelp');

        clearTimeout(timer);
        timer = setTimeout(function() {
            var len = 0,
                val = lfs
                        .val()
                        .replace(/\s+/ig,' '),
                pf = (val && val.charAt(val.length - 1)!=' ') ? '&nbsp;' : '';

            if (val) {
                len = $.trim(val).split(' ').length;
            }

            var pl = help
                        .slice(len)
                        .join(' '),
                lfsWidth = lfs.textWidth();

            lfsHelp
                .html(pf + pl)
                .css('left', lfsWidth + 262 + 'px')
                .width(lfs.outerWidth() - lfsWidth - 24 + 'px');
        }, 2);
    }

    function attachMasks() {
        Inputmask("+7 (999) 999-99-99").mask($('[name$="[userData][phone]"]'));

        var lfs = $('[name$="[userData][fullname]"]'),
            lfsHelp = $('#lfsHelp');

        lfsHelp.click(function(e){
            lfs.focus();
        });

        if (lfs.length) {
            help = lfs
                        .prop('placeholder')
                        .split(' ');
            timer = null;

            lfs.prop('placeholder','');
            lfs.keydown(function(e){
                lfsKeydown();
            });
            lfsKeydown();
        }
    }

    function attachUserAutocomplete() {
        var search = $('#header .search'),
            indicator = search.find('.indicator'),
            isRenderUsers,
            isRenderComusers,
            cacheUsers = {};

        $('[name$="[userData][fullname]"].autocomplete,[name$="[userData][phone]"].autocomplete').autocomplete({
            create: function() {
                $(this).data('ui-autocomplete').widget().menu({
                    focus: function(event, ui) {
                        ui.item.addClass('ui-state-focus');
                    },
                    blur: function() {
                        $(this).find('.ui-state-focus').removeClass('ui-state-focus');
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $('[name$="[userData][fullname]"]').val(ui.item.fullname);
                $('[name$="[userData][phone]"]').val(ui.item.phone);
                $('[name$="[userData][additionalPhone]"]').val(ui.item.additionalPhone);
                $('[name$="[userData][email]"]').val(ui.item.email);

                if ('user' === ui.item.type) {
                    $('[name$="[userData][userId]"]').val(ui.item.id);
                } else {
                    $('[name$="[userData][comuserId]"]').val(ui.item.id);
                }

                lfsKeydown();
            },
            source: function(request, response) {
                var q = '',
                    fieldName = $(this).attr('element').attr('name').replace(/create_form\[userData\]\[/gi, '').replace(/\]/gi, ''),
                    term = $.ui.autocomplete.escapeRegex(request.term) + fieldName;

                if (term in cacheUsers) {
                    response(cacheUsers[term]);
                    return false;
                }

                isRenderUsers = false;
                isRenderComusers = false;
                indicator.removeClass('search_clear').addClass('loading');

                if ('phone' == fieldName) {
                    q = request.term.replace(/[^\d]+/giu, '').replace(/^7/giu, '');
                } else {
                    q = request.term.replace(/[^\wа-яА-Я\s]+/giu, '');
                }

                sp.get(Routing.generate('user_search_autocomplete'), {q: q, field: fieldName}).done(function(data) {
                    cacheUsers[term] = $.map(data.users||[], function(item) {
                        item.label = item.fullname;
                        item.value = 'phone' == fieldName ? item.phone : item.fullname;
                        item.label += (item.formattedPhone.length > 0 ? ', <small>тел.</small> ' + item.formattedPhone : '') + (item.email.length > 0 ? ', <small>эл. почта</small> ' + item.email : '') + (item.isEmployee ? ' <small>(сотрудник)</small>' : '');

                        return item;
                    });
                    indicator.removeClass('loading').addClass('search_clear');
                    response(cacheUsers[term]);
                });
            }
        });

        function showAutocompleteChoices(ul, item) {
            var li = $('<li>').append('<a>' + item.label + '</a>');

            if (item.type == 'user' && ! isRenderUsers) {
                ul.append('<li class="legend"><span>Пользователи</span></li>');
                isRenderUsers = true;
            }
            else if (item.type == 'comuser' && ! isRenderComusers) {
                ul.append('<li class="legend"><span>Гостевые пользователи</span></li>');
                isRenderComusers = true;
            }

            return li.appendTo(ul);
        }

        if ($('[name$="[userData][fullname]"].autocomplete').length > 0) {
            $('[name$="[userData][fullname]"].autocomplete').data('ui-autocomplete')._renderItem = showAutocompleteChoices;
        }

        if ($('[name$="[userData][phone]"].autocomplete').length > 0) {
            $('[name$="[userData][phone]"].autocomplete').data('ui-autocomplete')._renderItem = showAutocompleteChoices;
        }
    }

    var wrapper = $('#content');

    wrapper.on('click', '[name="create_form[typeCode]"],[name="create_form[deliveryTypeCode]"]', function(e){
        $.ajax({
            url: window.location.href + '?refreshOnly=1',
            method: 'POST',
            dataType: 'json',
            data: $('[name="create_form"]').serializeArray(),
            complete: function (jqXHR, status) {
                var response = jqXHR.responseJSON;

                if (response === undefined || response.hasOwnProperty('errors') || response.hasOwnProperty('error')) {
                    // if (response.hasOwnProperty('error') && response.error.hasOwnProperty('error_code') && response.error.error_code == 17) {
                    //     window.open(response.error.redirect_uri);
                    // } else {
                    //     $('form#order-creation-form').prepend('<div id=\'sn-login-error\' class=\'row error\'>Произошла ошибка. Попробуйте войти позднее.</div>');
                    //     console.warn(jqXHR);
                        return false;
                    // }
                }

                if (response.hasOwnProperty('html')) {
                    $('#create_form_wrapper').html(response.html);
                    attachMasks();
                    attachUserAutocomplete();
                }
            }
        });
    });

    attachMasks();
    attachUserAutocomplete();
});
