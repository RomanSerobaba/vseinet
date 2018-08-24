$(function() {
    $('.cheaper-request').ajaxcontent({
        dialog: {
            title: 'Опция "Нашли дешевле"',
            minWidth: 860
        },
        load: function() {
            var form = this.find('#cheaper-request-form');

            form.on('submit', function(e) {
                e.preventDefault();

                sp.post(form.prop('action'), form.serializeArray()).then(function(response) {
                    console.log(response);
                });
            });
        }
    });
});