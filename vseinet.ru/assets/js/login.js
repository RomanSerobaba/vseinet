$(function() {
    $('#login-form-trigger').ajaxcontent({
        url: Routing.generate('login'),
        dialog: {
            title: 'Вход на сайт',
            minWidth: 400
        },
        load: function() {
            var form = this.find('#login-form').addClass('popup');

            form.toggleState = function(error) {
                form.find('.error').toggleClass('hidden', !error);
                form.find('.loading').toggleClass('hidden', error);   
            }

            form.on('submit', function(e) {
                e.preventDefault();
                form.toggleState();
                sp.post(Routing.generate('login'), form.serializeArray()).then(function(response) {
                    if (response.errors) {
                        form.toggleState(true);   
                    } else {
                        window.location.reload();
                    }
                }).fail(form.toggleState);
            });
        }
    });
});