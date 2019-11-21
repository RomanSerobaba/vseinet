$(function() {
    var search = $('#header .search'),
        input = search.find('.txt'),
        indicator = search.find('.indicator'),
        cache = {},
        renderCategories,
        renderProducts;

    input.on('input', function() {
        indicator.toggleClass('search_clear', '' == input.val());
    });

    indicator.on('click', function() {
        input.val('').trigger('change');
        indicator.removeClass('search_clear');
    });

    input.autocomplete({
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
            window.location = Routing.generate('catalog_' + ui.item.type, { id: ui.item.id, q: encodeURIComponent(ui.item.name) });
        },
        source: function(request, response) {
            var term = $.ui.autocomplete.escapeRegex(request.term);
            renderCategories = false;
            renderProducts = false;
            if (term in cache) {
                response(cache[term]);
            } else {
                indicator.removeClass('search_clear').addClass('loading');
                sp.get(Routing.generate('catalog_search_autocomplete'), {q: request.term}).done(function(data) {
                    cache[term] = $.map(data.result||[], function(item) {
                        item.value = item.name;
                        if (item.type == 'product') {
                            item.label += ', <small>код товара: <b>' + item.id + '</b></small>';
                        }
                        return item;
                    });
                    indicator.removeClass('loading').addClass('search_clear');
                    response(cache[term]);
                });
            }
        }
    }).data('ui-autocomplete')._renderItem = function(ul, item) {
        var li = $('<li>').append('<a>' + item.label + '</a>');
        if (item.type == 'category') {
            if ( ! renderCategories) {
                ul.append('<li class="legend"><span>Категории</span></li>');
                renderCategories = true;
            }
            li.append('<div>' + $.map(item.breadcrumbs, function(category) {
                return '<a href="' + Routing.generate('catalog_category', { id: category.id }) + '">' + category.name + '</a>';
            }).reverse().join(' / ') + '</div>');
        }
        else if ( ! renderProducts) {
            ul.append('<li class="legend"><span>Товары</span></li>');
            renderProducts = true;
        }
        return li.appendTo(ul);
    };
});
