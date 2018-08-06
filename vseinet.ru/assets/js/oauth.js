$(function () {
    var oAuthProviders = {
        redirect_uri: 'http://' + window.location.hostname + '/oauth2.html',
        yandex: {
            url: 'https://oauth.yandex.ru/authorize',
            params: {
                client_id: 'df5d3495a53f43d6bddb41d6a75b80de',
                response_type: 'code'
            }
        },
        vk: {
            url: 'https://oauth.vk.com/authorize',
            params: {
                client_id: '5625789',
                display: 'popup',
                response_type: 'code',
                scope: 'email',
            }
        },
        facebook: {
            url: 'https://www.facebook.com/dialog/oauth',
            params: {
                client_id: '174014883030647',
                display: 'popup',
                response_type: 'code',
                scope: 'email',
            }
        },
        odnoklassniki: {
            url: 'https://connect.ok.ru/oauth/authorize',
            params: {
                client_id: '1248078592',
                layout: 'w',
                response_type: 'code',
                scope: 'GET_EMAIL',
            }
        },
        moimir: {
            url: 'https://connect.mail.ru/oauth/authorize',
            params: {
                client_id: '747637',
                response_type: 'code',
                scope: '',
            }
        }
    };

    $('a.oauth-login').click(function (event) {
        event.preventDefault();
        var provider = $(this).data('oauth-login');
        if (provider !== undefined && provider !== '') {
            $('form#Login div#sn-login-error').remove();
            var providerInstance = new oAuth2(oAuthProviders[provider], oAuthProviders.redirect_uri,
                    function (response) {
                        $.ajax({
                            url: '/user/oauth/',
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                provider: provider,
                                code: response.code
                            },
                            complete: function (jqXHR, status) {
                                var response = jqXHR.responseJSON;

                                if (response === undefined || response.hasOwnProperty('errors') || response.hasOwnProperty('error')) {
                                    if (response.hasOwnProperty('error') && response.error.hasOwnProperty('error_code') && response.error.error_code == 17) {
                                        window.open(response.error.redirect_uri);
                                    } else {
                                        $('form#Login').prepend('<div id="sn-login-error" class="row error">Произошла ошибка. Попробуйте войти позднее.</div>');
                                        console.warn(jqXHR);
                                        return false;
                                    }
                                }

                                console.log(response);
                                if (response.hasOwnProperty('result') && response.result === 'ok') {
                                    document.location.reload();
                                }
                            }
                        });
                        return false;
                    },
                    function (error) {
                        $('form#Login').prepend('<div id="sn-login-error" class="row error">Не удалось войти! ' + (error ? error.error_description : '') + '</div>');
                        console.warn(error);
                        return false;
                    });
        } else {
            console.warn('Could`t find oAuth2 provider name!');
        }
        return false;
    });

    function submitLogin(event) {
        var self = this,
                data = $(this).serializeArray();

        event.preventDefault();

        // $.each(data, function (index, item) {
        //     if (item.name === 'user_login[password]' && item.value !== undefined && item.value !== '') {
        //         item.value = CryptoJS.SHA512(item.value).toString();
        //     }
        // });

        $(self).find('div.row.error').each(function (index, item) {
            $(item).removeClass('error').addClass('ok').find('.error').remove();
        });

        $.ajax({
            url: self.action,
            method: 'POST',
            dataType: 'json',
            data: data,
            complete: function (jqXHR, status) {
                var response = jqXHR.responseJSON;

                if (response === undefined) {
                    $('form#Login').prepend('<div class="row error">Произошла ошибка на сервере. Попробуйте войти позднее.</div>');
                    console.warn(jqXHR, status);
                    return false;
                }

                if (response.hasOwnProperty('result') && response.result === 'ok') {
                    document.location.reload();
                }

                if (response !== undefined && response.hasOwnProperty('errors')) {
                    var loginForm = $('form#Login');

                    $.each(response.errors, function (index, error) {
                        var row = loginForm.find('input#Login-' + index).closest('div'),
                                message = row.find('.error');

                        row.addClass('error').removeClass('ok');
                        if (message.length == 0) {
                            row.append(message = $('<div class="error"/>'));
                        }
                        message.html(error[0]);
                    });
                }
            }
        });
        return false;
    }

    var oAuth2 = function (options, redirect_uri, success, fail) {
        var self = this,
                options = options,
                popup = null,
                poolInterval = null;

        options.params.redirect_uri = encodeURIComponent(redirect_uri);

        openPopup(options);

        function openPopup(options) {
            var width = 500,
                    height = 500,
                    popupOptions = {
                        width: width,
                        height: height,
                        left: ((screen.width / 2) - (width / 2)),
                        top: ((screen.height / 2) - (height / 2))
                    },
            url = options.url,
                    title = options.title || 'Вход через соцсеть';

            if (options.hasOwnProperty('params')) {
                url += '?' + $.map(options.params, function (value, key) {
                    return key + '=' + value;
                }).join('&');
            }

            popup = window.open(url, title, stringifyOptions(popupOptions));

            poolInterval = setInterval(function () {
                if (popup.closed) {
                    clearInterval(poolInterval);
                    var response = getResponse();
                    if (response !== null && response.hasOwnProperty('code')) {
                        success(response);
                    } else {
                        fail(response);
                    }
                }
            }, 250);
        }

        function getResponse() {
            var response;

            if (isLocalStorage()) {
                response = localStorage.getItem('__oAuth2');
                localStorage.removeItem('__oAuth2');
            } else {
                response = getCookie('__oAuth2');
                document.cookie = '__oAuth2=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }
            return JSON.parse(response);
        }

        function stringifyOptions(options) {
            var optionsStrings = [];

            for (var key in options) {
                if (options.hasOwnProperty(key)) {
                    var value;

                    switch (options[key]) {
                        case true:
                            value = '1';
                            break;
                        case false:
                            value = '0';
                            break;
                        default:
                            value = options[key];
                    }

                    optionsStrings.push(key + "=" + value);
                }
            }
            return optionsStrings.join(',');
        }

        function isLocalStorage() {
            try {
                return 'localStorage' in window && window['localStorage'] !== null;
            } catch (e) {
                return false;
            }
        }

        function getCookie(name) {
            var matches = document.cookie.match(new RegExp(
                    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                    ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }
    }

    $('form#Login').off('change submit');
    $('form#Login input').on('change', $.noop());
    $('form#Login').on('submit', submitLogin);
});