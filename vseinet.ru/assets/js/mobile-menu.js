$(function(){
    $('.mob-top-btn').click(function(e){
        e.preventDefault();
        $('.mobile-collapsed').toggleClass('in');
        if (!$('#ProductFilter').hasClass('showfilter')) {
            $('body').toggleClass('ml250');
        }
        else{
            $('#ProductFilter').removeClass('showfilter');
        }

        return false;
    });
    $('nav.sort').click(function(e){
        e.preventDefault();
        if ($(window).width() < 992){
            $(this).addClass('choose-filt');
        }
    });

    /* Показать фильтры */
    $('.mobile-filter-btn a').click(function(e){
        e.preventDefault();
        $('#ProductFilter').addClass('showfilter');
        $('body').addClass('ml250');
        if($('.mobile-collapsed').hasClass('in')){
            $('.mobile-collapsed').removeClass('in')
        }
        return false;
    });
    $('.close_mob_filter').click(function(e){
        e.preventDefault();
        $('#ProductFilter').removeClass('showfilter');
        $('.filter button.total').fadeOut();
        $('body').removeClass('ml250');
        return false;
    });

    /* Показать ещё популярные товары */
    $('#populars .see-more').click(function(e){
        e.preventDefault();
        $('#populars .mobile-hide').fadeIn(400);
        $(this).fadeOut(500);
        return false;
    });

    /* Изменить информацию в оформлении */
    $('.change_info').click(function(e){
        e.preventDefault();
        $(this).parent('.order_txt_info').next('fieldset.block').fadeIn('400');
        $(this).parent('.order_txt_info').fadeOut('fast');
        return false;
    });

    if ($(window).width() < 768){
        $('#category .product .sale .cart-add').html('В корзину');
    };
});