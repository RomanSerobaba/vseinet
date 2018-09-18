$(function() {
    var container = $('#product, #product-list');

    /* product merger */
    +function() {
        var source = null;
        var data = localStorage.getItem('product-merge');
        if (data) {
            source = JSON.parse(data);
            if ('undefined' == typeof source.expired || (+new Date()) > source.expired) {
                localStorage.removeItem('product-merge'); 
            }
            else {
                var $product = document.querySelector('#product-' + source.id);
                if (null !== $product) {
                    $product.classList.add('marked');
                }
            }
        }
        container.on('click', '.admin-panel .merge', function(e) {
            e.preventDefault();
            var $product = this.closest('.product');
            var recipient = {
                id: $product.dataset.id,
                sku: $product.querySelector('.title .sku').textContent.trim(),
                name: $product.querySelector('.title a, .title h1').textContent.trim()   
            };
            if (source) {
                if (source.id == recipient.id) {
                    $product.classList.remove('marked');
                    localStorage.removeItem('product-merge');
                    source = null;
                }
                else {
                    var message = 'Вы уверены, что хотите объединить эти товары?\n\n'
                        + source.name + ', код: ' + source.id + '\n\n'
                        + recipient.name + ', код: ' + recipient.id + '\n\n';
                    if (confirm(message)) {
                        $product.classList.add('loading');
                        sp.post(Routing.generate('admin_product_merge'), {
                            sourceId: source.id,
                            recipientId: recipient.id
                        })
                        .done(function() {
                            if (null !== document.querySelector('#product-' + source.id)) {
                                document.querySelector('#product-' + source.id).remove();
                            }
                            $product.classList.remove('loading');
                            localStorage.removeItem('product-merge');
                            localStorage.removeItem('product-merge-done', source.id);
                            source = null;  
                        });
                    }
                }
            }
            else {
                $product.classList.add('marked');
                var date = new Date();
                date.setHours(date.getHours() + 1);
                recipient.expired = date.getTime();
                localStorage.setItem('product-merge', JSON.stringify(source = recipient));
            }
        });
        window.addEventListener('storage', function(e) {
            if ('product-merge' == e.key) {
                if (e.newValue) {
                    source = JSON.parse(e.newValue);
                    var $product = document.querySelector('#product-' + source.id);
                    if (null !== $product) {
                        $product.classList.add('marked');
                    }
                }
                else {
                    var $product = document.querySelector('.product.marked');
                    if (null !== $product) {
                        $product.classList.remove('marked');
                    }
                    source = null;
                }
            }
            else if ('product-merge-done' == e.key) {
                if (e.newValue) {
                    var $product = document.querySelector('#product-' + e.newValue);
                    if (null !== $product) {
                        $product.remove();
                    }
                    localStorage.removeItem('product-merge-done');
                }
                source = null;
            }
        });
    }();

    // product move
    container.ajaxcontent({
        dialog: {
            title: 'Переместить',
            minWidth: 600
        },
        target: '.admin-panel .move',
        load: function() {
            var dialog = this;
            var form = dialog.find('form').submit(function(e) {
                e.preventDefault();
                sp.post(form.prop('action'), form.serializeArray()).then(function(response) {
                    if (response.errors) {
                        var errors = [];
                        for (var key in response.errors) {
                            errors.push(response.errors[key][0]);
                        }
                        form.find('.row.product').addClass('error').append('<div class="error">' + errors.join('<br>') + '</div>');
                    } else {
                        form.find('.row.product .error').remove();
                        if (container.is('#product-list')) {
                            el.closest('.product').remove();
                        }
                        dialog.dialog('close');
                    }
                });
            });
        }
    });

});