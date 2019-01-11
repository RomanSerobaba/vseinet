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
                    window.location.reload();
                });
            });

            var cacheGeoCities = {};
            var txt = this.find('.txt').autocomplete({
                appendTo: '#City.popup',
                minLength: 2,
                select: function(event, ui) {
                    title.addClass('loading');
                    sp.post(Routing.generate('select_geo_city'), { id: ui.item.id }).then(function() {
                        window.location.reload();
                    });
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
    });
});
