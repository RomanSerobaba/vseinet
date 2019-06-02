$(function() {
    $('.receipts-of-product').ajaxcontent({
         dialog: {
            title: 'Сообщить о начале продаж'
        },
        load: function() {
            var that = this;
            var form = this.find('#receipts-of-product-form');

            form.on('submit', function(e) {
                e.preventDefault();

                sp.post(form.prop('action'), form.serializeArray()).then(function(response) {
                    form.find('.error .error').remove();
                    form.find('.error').removeClass('error');

                    if (response.errors) {
                        var id, row;
                        for (id in response.errors) {
                            row = form.find('#' + id).closest('.row');
                            row.addClass('error').append('<div class="error">' + response.errors[id][0] + '</div>');
                        }
                    } else {
                        that.dialog('close');
                        sp.message.notice(response.notice, 'Спасибо');
                    }
                });
            });

            Inputmask("+7 (999) 999-99-99").mask(form.find('input.phone'));

            var lfs = form.find('[name="receipts_of_product_form[userData][fullname]"]');
            var lfsHelp = form.find('#lfsHelpUser');
            lfsHelp.click(function(e) {
                lfs.focus();
            });
            var help = lfs.prop('placeholder').split(' ');
            var timer = null;

            lfs.prop('placeholder', '');
            lfs.keydown(function(e){
                lfsKeydown();
            });
            lfsKeydown();

            function lfsKeydown() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    var len = 0,
                        val = lfs
                                .val()
                                .replace(/\s+/ig,' '),
                        pf = (val && val.charAt(val.length - 1) != ' ') ? '&nbsp;' : '';

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
        }
    });
});
