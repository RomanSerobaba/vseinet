$(function() {
    if (!sp.mobile) {
        var scroller = $('#scroller').click(function(e) {
            $('body,html').animate({ scrollTop: 0 }, 400);
            e.preventDefault();
        });
        $(window).scroll(function() {
            scroller[$(this).scrollTop() > 0 ? 'fadeIn' : 'fadeOut']();
        });
    }
});