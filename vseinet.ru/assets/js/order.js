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

    function refreshFormEvents()
    {
        var orderForm = $('#order-creation-form');

        orderForm.form({
            afterResponse: function(data) {
                if ('undefined' !== typeof data.html && data.html.length > 0) {
                    $('#products').html(data.html);
                }
            },
            error: function(errors, submit) {
                var errorRow = $(this).find('.error').first();
                destination = errorRow.offset().top;

                if ($(window).width() > 992){
                    $('body, html ').animate( { scrollTop: destination - 45}, 500 );
                }
                else{
                    $('body, html ').animate( { scrollTop: destination - 10}, 500 );
                }
            },
            onSuccess: function(data) {
                if (data.isInnerOrder) {
                    window.location = Routing.generate('authority', { targetUrl: '/admin/orders/?id=' + data.id });
                } else {
                    window.location = Routing.generate('order_created_page', { id: data.id });
                }
            }
        });
    }

    var timer = null,
        help = {};

    function attachDatePicker()
    {
        $('[name="create_form[passport][issuedAt]"]').datepicker({
            changeMonth: true,
            changeYear: true,
            changeDay: true,
            maxDate: "+0d",
            minDate: "01.01.1991",
            yearRange: "1991:+0",
        });
    }

    function lfsKeydown() {
        var lfs = $('[name="create_form[client][fullname]"]'),
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
                .css('left', lfsWidth + 162 + 'px')
                .width(lfs.outerWidth() - lfsWidth - 24 + 'px');
        }, 2);
    }

    function attachMasks() {
        Inputmask("+7 (999) 999-99-99").mask($('[name="create_form[client][phone]"]'));
        Inputmask("email").mask($('[name="create_form[client][email]"]'));

        var lfs = $('[name="create_form[client][fullname]"]'),
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

        $('[name="create_form[client][fullname]"].autocomplete,[name="create_form[client][phone]"].autocomplete').autocomplete({
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
                $('[name="create_form[client][fullname]"]').val(ui.item.fullname);
                $('[name="create_form[client][phone]"]').val(ui.item.phone);
                $('[name="create_form[client][additionalPhone]"]').val(ui.item.additionalPhone);
                $('[name="create_form[client][email]"]').val(ui.item.email);

                if ('user' === ui.item.type) {
                    $('[name="create_form[client][userId]"]').val(ui.item.id);
                } else {
                    $('[name="create_form[client][comuserId]"]').val(ui.item.id);
                }

                lfsKeydown();
            },
            source: function(request, response) {
                var q = '',
                    fieldName = $(this).attr('element').attr('name').replace(/create_form\[client\]\[/gi, '').replace(/\]/gi, ''),
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
                    var regexp = new RegExp('(' + $.ui.autocomplete.escapeRegex(request.term) + ')', 'ig');
                    cacheUsers[term] = $.map(data.users||[], function(item) {
                        item.label = item.fullname.replace(regexp, '<b>$1</b>');
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

        if ($('[name="create_form[client][fullname]"].autocomplete').length > 0) {
            $('[name="create_form[client][fullname]"].autocomplete').data('ui-autocomplete')._renderItem = showAutocompleteChoices;
        }

        if ($('[name="create_form[client][phone]"].autocomplete').length > 0) {
            $('[name="create_form[client][phone]"].autocomplete').data('ui-autocomplete')._renderItem = showAutocompleteChoices;
        }
    }

    function attachCityAutocomplete()
    {
        var cacheGeoCities = {};
        var txt = $('[name="create_form[geoCityName]"].autocomplete');

        if (txt.length > 0) {
            txt.autocomplete({
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
                    $('[name="create_form[geoCityId]"]')
                        .val(ui.item.id)
                        .trigger('change');
                },
                source: function(request, response) {
                    var term = $.ui.autocomplete.escapeRegex(request.term);
                    if (term in cacheGeoCities) {
                        response(cacheGeoCities[term]);
                        return false;
                    }
                    sp.post(Routing.generate('search_geo_city'), { q: request.term }).done(function(data) {
                        var regexp = new RegExp('(' + term + ')', 'ig');
                        cacheGeoCities[term] = $.map(data.geoCities, function(item) {
                            item.value = item.name;
                            item.label = '<small>' + item.unit + '</small> ' + item.name.replace(regexp, '<b>$1</b>') + ' <small>(' +  item.regionName + ')</small>';
                            return item;
                        });
                        response(cacheGeoCities[term]);
                    });
                }
            })
            txt.data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>').append('<a>' + item.label + '</a>').appendTo(ul);
            };
        }
    }

    function attachStreetAutocomplete()
    {
        var cacheGeoStreets = {};

        var txt = $('[name="create_form[address][geoStreetName]"].autocomplete');

        if (txt.length > 0) {
            var txt = $('[name="create_form[address][geoStreetName]"].autocomplete').autocomplete({
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
                    $('[name="create_form[address][geoStreetId]"]').val(ui.item.id);
                },
                source: function(request, response) {
                    var term = $.ui.autocomplete.escapeRegex(request.term);
                    if (term in cacheGeoStreets) {
                        response(cacheGeoStreets[term]);
                        return false;
                    }
                    sp.post(Routing.generate('search_geo_street'), { geoCityId: $('[name="create_form[geoCityId]"]').val(), q: request.term }).done(function(data) {
                        var regexp = new RegExp('(' + term + ')', 'ig');
                        cacheGeoStreets[term] = $.map(data.geoStreets, function(item) {
                            item.value = item.name;
                            item.label = '<small>' + item.unit + '</small> ' + item.name.replace(regexp, '<b>$1</b>');
                            return item;
                        });
                        response(cacheGeoStreets[term]);
                    });
                }
            })
            txt.data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>').append('<a>' + item.label + '</a>').appendTo(ul);
            };
        }
    }

    function attachBankAutocomplete()
    {
        var cacheBanks = {};

        var txt = $('[name="create_form[organizationDetails][bic]"].autocomplete');

        if (txt.length > 0) {
            var txt = $('[name="create_form[organizationDetails][bic]"].autocomplete').autocomplete({
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
                    event.preventDefault();
                    $('[name="create_form[organizationDetails][bankId]"]').val(ui.item.id);
                    $('[name="create_form[organizationDetails][bankName]"]').val(ui.item.name);
                    $('[name="create_form[organizationDetails][bic]"]').val(ui.item.bic);
                },
                source: function(request, response) {
                    var term = $.ui.autocomplete.escapeRegex(request.term);
                    if (term in cacheBanks) {
                        response(cacheBanks[term]);
                        return false;
                    }
                    sp.post(Routing.generate('search_bank'), { q: request.term }).done(function(data) {
                        var regexp = new RegExp('(' + term + ')', 'ig');
                        cacheBanks[term] = $.map(data.banks, function(item) {
                            item.value = item.name;
                            item.label = item.name.replace(regexp, '<b>$1</b>');
                            return item;
                        });
                        response(cacheBanks[term]);
                    });
                }
            })
            txt.data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>').append('<a>' + item.label + '</a>').appendTo(ul);
            };
        }
    }

    var wrapper = $('#content');

    wrapper.on('change', '[name="create_form[typeCode]"],[name="create_form[deliveryTypeCode]"],[name="create_form[geoCityId]"]', function(e){
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
                    refreshFormEvents();
                    attachMasks();
                    attachUserAutocomplete();
                    attachCityAutocomplete();
                    attachStreetAutocomplete();
                    attachDatePicker();
                }
            }
        });
    }).on('change', '[name="create_form[organizationDetails][bic]"]', function(e){
        if ('' !== $(this).val()) {
            $.ajax({
                url: Routing.generate('get_bank'),
                method: 'GET',
                dataType: 'json',
                data: { 'bic': $(this).val() },
                complete: function (jqXHR, status) {
                    var response = jqXHR.responseJSON;

                    if (response === undefined || !response.hasOwnProperty('data') || null === response.data) {
                            $('[name="create_form[organizationDetails][bankName]"]').val('');
                            $('[name="create_form[organizationDetails][bankId]"]').val('');
                            return false;
                    }

                    $('[name="create_form[organizationDetails][bankName]"]').val(response.data.name);
                    $('[name="create_form[organizationDetails][bankId]"]').val(response.data.id);
                }
            });
        } else {
            $('[name="create_form[organizationDetails][bankName]"]').val('');
            $('[name="create_form[organizationDetails][bankId]"]').val('');
        }
    }).on('change', '[name="create_form[isCallNeeded]"]', function(e){
        $('[name="create_form[callNeedComment]"]').parent('.row')[1 == $(this).val() ? 'show' : 'hide']();
    }).on('change', '[name="create_form[paymentTypeCode]"]', function(e){
        $('#client_contact_info')['retail' !== $('[name="create_form[typeCode]"]:checked').val() || 'credit' === $('[name="create_form[paymentTypeCode]"]:checked').val() || 'installment' === $('[name="create_form[paymentTypeCode]"]:checked').val() ? 'show' : 'hide']();
    });

    refreshFormEvents();
    attachMasks();
    attachUserAutocomplete();
    attachCityAutocomplete();
    attachStreetAutocomplete();
    attachDatePicker();
});
