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

    function attachMasks() {
        Inputmask("+7 (999) 999-99-99").mask($('[name$="[userData][phone]"]'));
        
        var lfs = $('[name$="[userData][fullname]"]'),
            lfsHelp = $('#lfsHelp');
    
        lfsHelp.click(function(e){
            lfs.focus();
        });
    
        if (lfs.length) {
            var timer = null,
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
    }

    var wrapper = $('#content');
    
    wrapper.on('click', '[name="create_form[typeCode]"]', function(e){
        $.ajax({
            url: window.location.href + '?typeCode=' + $('[name="create_form[typeCode]"]:checked').val(),
            method: 'GET',
            dataType: 'json',
            complete: function (jqXHR, status) {
                var response = jqXHR.responseJSON;

                if (response === undefined || response.hasOwnProperty('errors') || response.hasOwnProperty('error')) {
                    // if (response.hasOwnProperty('error') && response.error.hasOwnProperty('error_code') && response.error.error_code == 17) {
                    //     window.open(response.error.redirect_uri);
                    // } else {
                    //     $('form#order-creation-form').prepend('<div id=\'sn-login-error\' class=\'row error\'>Произошла ошибка. Попробуйте войти позднее.</div>');
                    //     console.warn(jqXHR);
                        return false;
                    // }
                }

                if (response.hasOwnProperty('html')) {
                    $('#create_form_wrapper').html(response.html);
                    attachMasks();
                }
            }
        });
    });

    attachMasks();
});