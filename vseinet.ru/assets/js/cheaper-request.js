$(function() {
    $('.cheaper-request').ajaxcontent({
        dialog: {
            title: 'Опция "Нашли дешевле"',
            minWidth: 860
        },
        load: function() {
            var that = this;
            var form = this.find('#cheaper-request-form');

            form.on('submit', function(e) {
                e.preventDefault();

                sp.post(form.prop('action'), form.serializeArray()).then(function(response) {
                    form.find('.error .error').remove();
                    form.find('.error').removeClass('error');

                    if (response.errors) {
                        var id, row;
                        for (id in response.errors) {
                            row = form.find('#cheaper_request_form_' + id).closest('.row');
                            row.addClass('error').append('<div class="error">' + response.errors[id][0] + '</div>');
                        }
                    } else {
                        that.dialog('close');
                        sp.message.notice('Ваш запрос отправлен!', 'Спасибо');
                    }
                });
            });


            var lfs = form.find('[name="cheaper_request_form[userData][fullname]"]');
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