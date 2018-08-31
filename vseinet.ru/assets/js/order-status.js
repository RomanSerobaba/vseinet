$(function() {
    $('[data-popup=status-form]').ajaxcontent({
        url: Routing.generate('order_status'),
        dialog: {
            title: 'Проверить заказ',
            minWidth: 400
        },
        load: function() {
            var form = this.find('#order-status-form').addClass('popup');
            var request = form.find('.request');
            var row = form.find('#get_status_form_number').closest('.row');
            var reset = form.find('.reset').click(function() {
                request.show().next().hide();
            });
            var rowReset = reset.closest('.row');

            form.on('submit', function(e) {
                e.preventDefault();
                sp.post(Routing.generate('order_status'), form.serializeArray()).then(function(response) {
                    if (response.errors) {
                        row.addClass('error').append('<div>' + response.errors.number[0] + '</div>');
                    } else {
                        row.removeClass('error').find('error').remove();
                        request.hide().next().show().find('.row:first').html(response.html);
                        reset.one('click', function() {
                            request.show().next().hide();
                        });
                        rowReset.hide();
                        setTimeout(function() {
                            rowReset.show();
                        }, 3000);
                    }
                });
            });

            form.find('#get_status_form_tracker').click(function() {
                form.off('submit').submit();
            });
        }
    });
});