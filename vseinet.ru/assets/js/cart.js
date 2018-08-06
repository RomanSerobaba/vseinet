$(function() {
    if (!sp.mobile) {
        var cart = $('#products.cart').sticky({
            topSpacing: 45,
            bottomSpacing: 245
        });
        if (cart.length) {
            var hw, hc, hs, hp, timer = null,
                summary = cart.find('.summary .wrapper'),
                product = cart.find('.product:first'),
                wrapper = $('#products-sticky-wrapper');
            sp.window.bind('resize.cart', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    var w = wrapper.width();
                    hw = sp.window.height();
                    hc = cart.outerWidth(w).height();
                    summary.outerWidth(w).parent().height(hs = summary.outerHeight());
                    hp = product.outerHeight();
                    sp.window.triggerHandler('scroll.cart');
                },100);
            }).triggerHandler('resize.cart');
            sp.window.bind('scroll.cart', function() {
                var dw = hw + sp.window.scrollTop(),
                    dc = hc + cart.offset().top,
                    dp = hp + product.offset().top;
                summary[(dw < dc && dw - dp > hs) ? 'addClass' : 'removeClass']('fixed');
            });
        }
    }
    var carttop = $('#top .cart');
    var favorites = $('#top .favorite b');
    sp.cartupdate = function(data) {
        if (data.cart.total) {
            carttop.html(data.cart.total.declension('товар;товара;товаров') + ' на сумму ' + data.cart.amount.formatPrice() + ' <span class="RUR">Р</span>');
        }
        else {
            carttop.html('Корзина пустая');
        }
        if (data.favorites) {
            favorites[data.favorites.count ? 'show' : 'hide']().html(data.favorites.count);
        }
    }
    $('body').on('click', '.cart-add', function(event) {
        var a = $(this),
            input = a.closest('.sale-btns').find('.cart-quantity'),
            quantity = input.val();
        if (0 >= +quantity) {
            quantity = 1;
        }

        if($(this).hasClass('cart-add-conditioner')) {
            var changeTypeId = 0;
            swal({
                title: "Требуется ли вам установка кондиционера?",
                showCancelButton: true,
                confirmButtonColor: "#6bc900",
                confirmButtonText: "Да",
                cancelButtonText: "Нет",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {
                    changeTypeId = 11;
                }
                sp.get(a.prop('href'), { quantity: quantity, changeTypeId: changeTypeId }).done(function(response) {
                    var id = a.closest('.product').data('id');
                    var sale = a.closest('.sale').toggleClass('to-cart in-cart');
                    sale.find('.sale-btns.in-cart .cart-quantity')
                        .val(response.cart.products[id].quantity);
                    sp.cartupdate(response);
                });
            });
        } else {
            sp.get(a.prop('href'), { quantity: quantity }).done(function(response) {
                var id = a.closest('.product').data('id');
                var sale = a.closest('.sale').toggleClass('to-cart in-cart');
                sale.find('.sale-btns.in-cart .cart-quantity')
                    .val(response.cart.products[id].quantity);
                sp.cartupdate(response);
            });
        }

        event.preventDefault();
    });
    var timer = null;
    $('body').on('change', '.sale-btns.in-cart .cart-quantity', function (event) {
        clearTimeout(timer);
        timer = setTimeout(function() {
            var input = $(event.target),
                quantity = input.val(),
                id = input.closest('.product').data('id');
            if (0 > +quantity)
                quantity = 0;
            sp.get(Routing.generate('cart_set_quantity', { id: id }), { quantity: quantity }).done(function(response) {
                var input = $(event.target);
                if (response.cart.products && response.cart.products[id]) {
                    input.val(response.cart.products[id].quantity);
                }
                else {
                    var sale = input.closest('.sale').toggleClass('in-cart to-cart');
                    var input = sale.find('.sale-btns.to-cart .cart-quantity');
                    input.val(input.data('min-quantity'));
                }
                sp.cartupdate(response);
            });
        }, 500);
    });
    $('body').on('click', '.cart-del', function(event) {
        var a = $(this);
        sp.get(a.prop('href')).done(function(response) {
            var sale = a.closest('.sale').toggleClass('in-cart to-cart');
            var input = sale.find('.sale-btns.to-cart .cart-quantity');
            input.val(input.data('min-quantity'));
            sp.cartupdate(response);
        });
        event.preventDefault();
    });
});