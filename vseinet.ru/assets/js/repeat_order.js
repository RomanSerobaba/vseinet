$(function() {
    $('.repeat_order').click(function(event) {
        event.preventDefault();
        var a = $(this), url = a.prop('href');
        sp.post(url).done(function(response) {
            if (200 === response.status) {
                window.location = Routing.generate('cart');
            }
        });
    });
});
