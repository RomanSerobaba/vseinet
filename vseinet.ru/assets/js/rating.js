$.widget('sp.rating', {
    options: {
        width: 13,
        margin: 3,
        step: .5
    },
    _create: function() {
        if (this.element.get(0).tagName.toLowerCase() == 'input') {
            this.input = this.element;
            this.element = $('<div class="rating"/>').insertAfter(this.input);
            this._on({
                mousemove: function(event) {
                    this._move(event);
                },
                mouseleave: function() {
                    this._update(parseFloat(this.input.val()) || 0);
                },
                click: function(event) {
                    if (this.input.val()) {
                        this._on({mousemove: function(event) {
                            this._move(event);
                        }});
                        this.input.val('');
                        this._move(event);
                    }
                    else if (this.value) {
                        this._off(this.element, 'mousemove');
                        this.input.val(this.value);
                    }
                }
            });
        }
        this.stars = $('<div/>').appendTo(this.element);
        this.colors = $('<div/>').appendTo(this.stars);
        this._update(parseFloat(this.element.data('rating')) || 0);""
    },
    _update: function(value) {
        this.value = Math.floor(value / this.options.step) * this.options.step;
        var x = this.value * (this.options.width + this.options.margin);
        this.colors.css({width: Math.ceil(x) + 'px'});
    },
    _move: function(event) {
        var x = event.pageX - this.element.offset().left + this.options.margin;
        this._update(x / (this.options.width + this.options.margin));
    }
});
$(function() {
    $('.rating').rating();
});