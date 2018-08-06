$(function() {
    var top = $('#top .favorite b');
    $('[data-favorite]').click(function(event) {
        event.preventDefault();
        var a = $(this), active = a.is('.active'), url = a.prop('href');
        sp.get(url).done(function(response) {
            a.prop('href', url.replace(active ? 'del' : 'add', active ? 'add' : 'del'))
                .text('в избранно' + (active ? 'е' : 'м')).toggleClass('active');
            var count = response.favorites.count;
            top.text(count)[count ? 'show' : 'hide']();
            if (response.cart) {
                sp.cartupdate(response);
            }
        });
    });
});