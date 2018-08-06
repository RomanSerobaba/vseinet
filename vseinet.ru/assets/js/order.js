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
});