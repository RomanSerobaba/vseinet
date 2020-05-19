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
    container.on('click', '.admin-panel .move', function(e) {
        e.preventDefault();
        sp.openAjaxDialog($(this), {
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
                                a.closest('.product').remove();
                            }
                            dialog.dialog('close');
                        }
                    });
                });
            }
        });
    });

    // info pane
    container.on('click', '.admin-panel .competitor', function(e) {
        e.preventDefault();
        var panel = $(this).closest('.admin-panel');
        var pane = panel.find('.info-pane');
        if (pane.is('.hidden')) {
            pane.removeClass('hidden');

            var remains = pane.find('.supplier-remains');
            if (remains.is('.loading')) {
                sp.get(Routing.generate('admin_supplier_remains'), { baseProductId: panel.data('id') }).then(function(response) {
                    remains.html(response.html).removeClass('loading');
                });
            }

            var reserves = pane.find('.reserves');
            if (reserves.is('.loading')) {
                sp.get(Routing.generate('admin_reserves'), { baseProductId: panel.data('id') }).then(function(response) {
                    reserves.html(response.html).removeClass('loading');
                });
            }

            var revisions = pane.find('.competitor-revisions');
            if (revisions.is('.loading')) {
                sp.get(Routing.generate('admin_competitor_revisions'), { baseProductId: panel.data('id') }).then(function(response) {
                    revisions.html(response.html).removeClass('loading');
                });
            }

        } else {
            pane.addClass('hidden');
        }
    });

    // pricetags
    container.on('change', '.admin-panel .pricetag-quantity', function(e) {
        e.preventDefault();
        var a = this;
        if (a.classList.contains('busy')) {
            return;
        }
        sp.post(a.dataset.link, {
            quantity: a.value,
            baseProductId: a.dataset.baseProductId,
            geoPointId: a.dataset.geoPointId
        }).then(function(response) {
            a.classList.remove('busy');
            var i = container.find('.admin-panel .pricetag');
            if (response.isActive) {
                i.addClass('active');
            } else {
                i.removeClass('active');
            }
        });
        a.classList.add('busy');
    });

    // supplier remains
    container.on('click', '.admin-panel .supplier-unlink', function(e) {
        e.preventDefault();
        sp.post(this.href, {
            baseProductId: this.closest('.admin-panel').dataset.id,
            partnerProductId: this.dataset.id
        }).then(function(response) {
            e.target.style.display = 'none';
            e.target.nextElementSibling.style.display = 'inline-block';
        });
    });
    container.on('click', '.admin-panel .supplier-restore', function(e) {
        e.preventDefault();
        sp.post(this.href, {
            baseProductId: this.closest('.admin-panel').dataset.id,
            partnerProductId: this.dataset.id
        }).then(function(response) {
            e.target.style.display = 'none';
            e.target.previousElementSibling.style.display = 'inline-block';
        });
    });
    container.on('click', '.admin-panel .supplier-set-not-available', function(e) {
        e.preventDefault();
        sp.post(this.href, {
            partnerProductId: this.dataset.id
        }).then(function(response) {
            e.target.closest('.supplier-product').querySelector('.supplier-availability').style.color = 'red';
            e.target.remove();
        });
    });

    // competitor revisions
    container.on('click', '.admin-panel .revision-add, .admin-panel .revision-edit', function(e) {
        e.preventDefault();
        var a = $(this);
        sp.openAjaxDialog(a, {
            dialog: {
                minWidth: 800
            },
            data: function() {
                return {
                    baseProductId: a.closest('.admin-panel').data('id')
                };
            },
            load: function() {
                var dialog = this;
                var form = dialog.find('form').submit(function(e) {
                    e.preventDefault();
                    sp.post(form.prop('action'), form.serializeArray()).then(function(response) {
                        form.find('.row .error').remove();
                        if (response.errors) {
                            for (var key in response.errors) {
                                form.find('.row .' + key)
                                    .closest('.row')
                                    .addClass('error')
                                    .append('<div class="error">' + response.errors[key][0] + '</div>');
                            }
                        } else {
                            dialog.dialog('close');
                            a.closest('.admin-panel').find('.competitor-revisions').html(response.html);
                        }
                    });
                });
                dialog.dialog('option', 'title', form.prop('title'));
            }
        });
    });

    container.on('click', '.admin-panel .revision-delete', function(e) {
        e.preventDefault();
        if (confirm('Удалить товар конкурента?')) {
            sp.post(this.href).then(function(response) {
                e.target.closest('.revision').remove();
            });
        }
    });
    container.on('click', '.admin-panel .revision-request', function(e) {
        e.preventDefault();
        sp.post(this.href).then(function(response) {
            e.target.closest('.revision').classList.add('requested');
            e.target.remove();
        });
    });

    // prices
    container.on('click', '.set-price', function(e) {
        e.preventDefault();
        var a = $(this);
        sp.openAjaxDialog(a, {
            dialog: {
                minWidth: 600,
                title: 'Установить ручную цену'
            },
            load: function() {
                var dialog = this;
                var form = dialog.find('form').submit(function(e) {
                    e.preventDefault();
                    sp.post(form.prop('action'), form.serializeArray()).then(function() {
                        dialog.dialog('close');
                        container.find('.price-wrapper').addClass('loading');
                        setTimeout(getProduct, 100, container.is('#product-list') ? a.closest('.product') : container);
                    });
                });
            }
        });
    });
    container.on('click', '.handmade-pricetag-edit', function(e) {
        e.preventDefault();
        var a = $(this);
        sp.openAjaxDialog(a, {
            dialog: {
                minWidth: 600,
                title: 'Установить ручной ценник'
            },
            load: function() {
                var dialog = this;
                var form = dialog.find('form').submit(function(e) {
                    e.preventDefault();
                    sp.post(form.prop('action'), form.serializeArray()).then(function() {
                        dialog.dialog('close');
                        a.next('span').html(form.find('[name="set_pricetag_form[price]"]').val());
                    });
                });
            }
        });
    });
    container.on('click', '.reset-price', function(e) {
        e.preventDefault();
        container.find('.price-wrapper').addClass('loading');
        var a = $(this);
        sp.post(a.prop('href')).then(function() {
            setTimeout(getProduct, 100, container.is('#product-list') ? a.closest('.product') : container);
        });
    });
    function getProduct(product) {
        sp.get(Routing.generate('product_get_price', { id: product.data('id') })).then(function(response) {
            product.find('.price-value').html(response.product.price.formatPrice()).removeClass().addClass('price-value price-'+response.product.type);
            product.find('.reset-price').css('display', response.product.isManualPrice ? 'inline-block' : 'none');
            product.find('.price-wrapper').removeClass('loading');
        });
    }
});
