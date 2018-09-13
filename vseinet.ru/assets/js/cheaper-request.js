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
        }
    });
});