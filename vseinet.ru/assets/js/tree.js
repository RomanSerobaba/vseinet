$(function() {
    var tree = $('#tree'),
        menu1 = tree.children('ul').menuAim({
            activate: function(element) {
                var root = $(element);
                if ($(window).innerWidth() > 992){
                    $('#subtree-' + root.data('id')).show();
                    root.find(' > a').addClass('active');
                };
            },
            deactivate: function(element) {
                var root = $(element);
                if ($(window).innerWidth() > 992){
                $('#subtree-' + root.data('id')).hide();
                root.find(' > a').removeClass('active');
            };
            },
            exitMenu: function() {
                return ! sp.mobile;
            }
        }),
        trigger = $('#tree-trigger'),
        overlay = null;

    menu1.find('> li > span').not('.special, .go-to-category').click(function() {
        return false;
    });
    $('.subtree').css({minHeight: menu1.outerHeight() - 50});
    if (sp.mobile)
        sp.document.click(function(event) {
            if ($(event.target).closest('#tree').length == 0) {
                // console.log(event.target, menu1)
                menu1.data('exit')();
            }
        });

    var mobMenuPageTrigger = function(){
        if ($(window).width() > 992) {
            if (trigger.length) {
                overlay = $('<div/>').css({
                    background: '#000',
                    display: 'none',
                    height: '100%',
                    left: 0,
                    opacity: 0,
                    position: 'fixed',
                    top: 0,
                    width: '100%',
                    zIndex: 10000
                }).appendTo('body');
                var height = tree.height();
                tree.addClass('page_tree');
                tree.css({
                    height: 35,
                    position: 'absolute',
                    top: trigger.offset().top,
                    zIndex: 10001
                }).mouseleave(function() {
                    tree.animate({height: 35}, 100, function() {
                        tree.hide();
                    });
                    overlay.animate({opacity: 0}, 100, function() {
                        overlay.hide();
                    });
                    trigger.removeClass('expand');
                });
                trigger.click(function() {
                    if ( ! trigger.is('.expand')) {
                        overlay.stop().show().animate({opacity: .2}, 150);
                        tree.stop().css('left', trigger.offset().left).show().animate({height: height}, 150, function() {
                            $('.subtree').css({minHeight: menu1.outerHeight() - 50});
                        });
                        trigger.addClass('expand');
                    }
                    return false;
                });
                var timer = null;
                trigger.mouseenter(function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        trigger.triggerHandler('click');
                    }, 300);
                }).mouseleave(function() {
                    clearTimeout(timer);
                });
            }
        }
        else{
            var nav_mobile = $('nav.page_nav').html();
            $('nav.page_nav').remove();

            $('#header .holder').append(nav_mobile);
            $('#header .holder #tree').addClass('page_tree').attr('style', '');
        };
    };
    mobMenuPageTrigger();



        var hint = $('<a class="hint"/>').mouseleave(function() {
            hint.stop().hide();
        });
        $('.subtree li').not('.top').find('a').mouseenter(function() {
            var a = $(this), li = a.closest('li'), ul = li.closest('ul'), st = ul.closest('.subtree');
            hint.text(a.text())[a.parent().is('h4') ? 'addClass' : 'removeClass']('bold').appendTo(ul).show().prop('href', a.prop('href')).css('width', 'auto');
            var hw = hint.width(), lw = li.width();
            if (hw > lw - 10) {
                hint.css({left: 13, top: li.position().top}).width(lw);
                var params = {width: Math.max(hw, lw) + 5};
                if (ul.position().left + hw > st.width())
                    params.left = lw - hw + 13;
                hint.animate(params, 150);
            }
            else hint.hide();
        });
        $('.subtree ul').hover(function() {
            $(this).css('zIndex', 2);
        }, function() {
            $(this).css('zIndex', 1);
        });
        $('#tree .tree-title').click(function(){
            if ($(window).width() < 992) {
                $('#tree > ul').slideToggle(500);
                $(this).toggleClass('mob_trigger_open');
                if($('#tree ul li').hasClass('opened')){
                    $('#tree ul li').removeClass('opened');
                }
                if($('.subtree').is(':visible')){
                    $('.subtree').slideUp('500');
                }
            }
        });
        $('#tree ul li[data-id]').children('a').click(function(e){
            if (!$(e.target).is('.go-to-category')) {
                if ($(window).width() < 992){

                    $('#tree ul li').not($(this).parent('li')).removeClass('opened');
                    $('.subtree').not($(this).next('.subtree')).slideUp('500');
                    $('.subtree ul').removeClass('third-menu-open');

                    if ($(this).next('.subtree').is(':visible')) {
                        $(this).next('.subtree').slideUp('500');
                    }
                    else{
                        $(this).next('.subtree').slideDown(500);
                    };

                    if($(this).parent('li').hasClass('opened')){
                        $(this).parent('li').removeClass('opened');
                    }
                    else{
                        $(this).parent('li').addClass('opened');
                        var gt = $(this).find('.go-to-category');
                        gt.css('lineHeight', 10 + gt.outerHeight() + 'px');
                    };
                                        return false;
                }
            }
        });
        $('.subtree .top').children().children().not('.go-to-category').click(function(e){
            if (!$(e.target).is('.go-to-category')) {
                if ($(window).width() < 992){
                    $(this).closest('ul').toggleClass('third-menu-open');
                    var gt = $(this).find('.go-to-category');
                    gt.css('lineHeight', gt.outerHeight() + 'px');
                    return false;
                }
            }
        });
});
