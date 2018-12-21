$(function() {
    var status = $('#Status');
    if (status.is('.popup')) {
        status.dialog({
            open: function() {
                status.find('.request').show().next().hide();
            }
        });
    }
    status.form({
        submit: function(xhr) {
            var loading = status.find('.loading').show();
            xhr.done(function(data) {
                if (data.html) {
                    var request = status.find('.request').hide();
                    request.next().show().find('.row:first').html(data.html);
                    var reset = status.find('.reset').one('click', function() {
                        request.show().next().hide();
                    });
                    var row = reset.closest('.row').hide();
                    setTimeout(function() {
                        row.show();
                    }, 3000);
                }
            }).always(function() {
                loading.hide();
            }).fail(function() {
                status.find('[name*=submit]').click();
            });
        }
    });
    status.find('[name*=submit]').click(function() {
        status.form('submit');
    });
    
    var lfs = $('[name$="[userData][fullname]"]');

    if (lfs.length) {
        var timer = null,
            lfsHelp = $('#lfsHelp'),
            help = lfs
                    .prop('placeholder')
                    .split(' ');

        lfs.prop('placeholder','');
        lfs.keydown(function(e){
            clearTimeout(timer);
            timer = setTimeout(function() {
                var len = 0, 
                    val = lfs
                            .val()
                            .replace(/\s+/ig,' '), 
                    pf = (val && val.charAt(val.length - 1)!=' ') ? '&nbsp;' : '';

                if (val) {
                    len = $.trim(val).split(' ').length;
                }

                var pl = help
                            .slice(len)
                            .join(' '), 
                    lfsWidth = lfs.textWidth();

                lfsHelp
                    .html(pf + pl)
                    .css('left', lfsWidth + 262 + 'px')
                    .width(lfs.outerWidth() - lfsWidth - 24 + 'px');
            }, 2);
        });
        lfs.trigger('keydown');
    }
});