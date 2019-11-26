$(function() {
    var cache = {},
        current = '';
    $.fn.geoCity = function(options) {
        options = $.extend({
            appendTo: 'body',
            select: $.noop,
            selectorId: null
        }, options || {});

        return this.each(function() {
            var input = $(this).autocomplete({
                appendTo: options.appendTo,
                minLength: 2,
                autoFocus: true,
                select: function(e, ui) {
                    if (options.selectorId) {
                        $(options.selectorId).val(ui.item.id);
                        current = ui.item.value;
                    }
                    options.select(e, ui);
                    input.trigger('change');
                },
                source: function(request, response) {
                    var term = $.ui.autocomplete.escapeRegex(request.term);
                    if (term in cache) {
                        response(cache[term]);
                        return false;
                    }
                    sp.post(Routing.generate('search_geo_city'), { q: request.term }).done(function(data) {
                        var regexp = new RegExp('(' + term + ')', 'ig');
                        cache[term] = $.map(data.geoCities, function(item) {
                            item.value = item.name;
                            item.label = '<small>' + item.unit + '</small> ' + item.name.replace(regexp, '<b>$1</b>') + ' <small>(' +  item.regionName + ')</small>';
                            return item;
                        });
                        response(cache[term]);
                    });
                }
            }).on('blur', function(){
                if (options.selectorId) {
                    $(this).val(current);
                }
            });
            input.data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>').append('<a>' + item.label + '</a>').appendTo(ul);
            };
        });
    };
});

$(function() {
    var cache = {},
        current = '';
    $.fn.geoStreet = function(options) {
        options = $.extend({
            appendTo: 'body',
            select: $.noop,
            selectorId: null,
            selectorGeoCityId: '[name*=geoCityId]'
        }, options || {});

        return this.each(function() {
            var inputGeoCityId = $(options.selectorGeoCityId);
            var input = $(this).autocomplete({
                appendTo: options.appendTo,
                minLength: 2,
                autoFocus: true,
                select: function(e, ui) {
                    if (options.selectorId) {
                        $(options.selectorId).val(ui.item.id);
                        current = ui.item.value;
                    }
                    options.select(e, ui);
                    input.trigger('change');
                },
                source: function(request, response) {
                    var term = $.ui.autocomplete.escapeRegex(request.term);
                    if (term in cache) {
                        response(cache[term]);
                        return false;
                    }
                    sp.post(Routing.generate('search_geo_street'), { q: request.term, geoCityId: inputGeoCityId.val() }).done(function(data) {
                        var regexp = new RegExp('(' + term + ')', 'ig');
                        cache[term] = $.map(data.geoStreets, function(item) {
                            item.value = item.name;
                            item.label = '<small>' + item.unit + '</small> ' + item.name.replace(regexp, '<b>$1</b>');
                            return item;
                        });
                        response(cache[term]);
                    });
                }
            }).on('blur', function(){
                if (options.selectorId) {
                    $(this).val(current);
                }
            });
            input.data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>').append('<a>' + item.label + '</a>').appendTo(ul);
            };
        });
    };
});

$(function() {
    $('[data-id=City]').ajaxcontent({
        url: Routing.generate('geo_cities'),
        dialog: {
            title: 'Выбор города',
            minWidth: 860
        },
        load: function() {
            var form = this.find('form');
            form.on('submit', function(e) {
                e.preventDefault();
            });
            var dialog = this.closest('.ui-dialog');
            var title = this.find('.title');
            var regions = this.find('.regions > div');
            var cities = this.find('.cities > div');

            var win = $(window).on('resize.cities', function(e) {
                var h = win.height() - dialog.height() + regions.height() - 90;
                regions.add(cities).height(Math.max(10, Math.floor(h / 28) * 28));
                regions.scrollTop((regions.find('.current').index() - 1) * 28);
            });
            win.trigger('resize.cities');

            regions.on('click', '.region', function(e) {
                e.preventDefault();
                var r = $(e.target).closest('.region');
                regions.find('.current').removeClass('current');
                cities.html('').addClass('loading');
                sp.post(Routing.generate('select_geo_region'), { geoRegionId: r.data('id') }).done(function(data) {
                    r.addClass('current');
                    cities.html(data.html);
                }).always(function() {
                    cities.removeClass('loading');
                });
            });

            cities.on('click', '.city', function(e) {
                e.preventDefault();
                var c = $(e.target).closest('.city');
                title.addClass('loading');
                sp.post(Routing.generate('select_geo_city'), { id: c.data('id') }).then(function() {
                    if (window.localStorage) {
                        localStorage.setItem('city-id', c.data('id'));
                    }
                    window.location.reload();
                });
            });

            this.find('.txt').geoCity({
                appendTo: '#City.popup',
                select: function(e, ui) {
                    title.addClass('loading');
                    sp.post(Routing.generate('select_geo_city'), { id: ui.item.id }).then(function() {
                        if (window.localStorage) {
                            localStorage.setItem('city-id', ui.item.id);
                        }
                        window.location.reload();
                    });
                }
            });
        }
    });
});


$(function() {
    var geo = $('#geo');
    if (!geo.data('geoCityId')) {
        if (window.localStorage) {
            var id = localStorage.getItem('city-id');
            if (id) {
                sp.post(Routing.generate('select_geo_city'), { id: id }).then(function() {
                    window.location.reload();
                }).catch(function() {
                    checkRequest();
                });
            } else {
                var now = checkRequest();
            }
        } else {
            showRequest();
        }
        geo.on('click', 'button', function() {
            if (window.localStorage) {
                localStorage.setItem('request-city-date', now.toString());
                if ($(this).is('.ok')) {
                    var id = geo.data('detectGeoCityId');
                    sp.post(Routing.generate('select_geo_city'), { id: id });
                    localStorage.setItem('city-id', id);
                }
            }
            hideRequest();
        });
    }
    function checkRequest() {
        var rcd = new Date(localStorage.getItem('request-city-date'));
        var now = new Date();
        rcd.setDate(rcd.getDate() + 1);
        if (rcd.getTime() < now.getTime()) {
            showRequest();
        }
        return now;
    }
    function showRequest() {
        if ($(window).width() < 992) {
            geo.slideDown(250);
        } else {
            $('#header .holder').append(geo.removeClass('hidden'));
        }
    }
    function hideRequest() {
        if ($(window).width() < 992) {
            geo.slideUp(250);
        } else {
            geo.addClass('hidden');
        }
    }
});