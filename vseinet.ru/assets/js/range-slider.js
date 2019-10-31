$.widget('sp.range', {
    options: {
        min: 0,
        max: 10,
        step: 1,
        inputs: [],
        change: function(event, ui) {}
    },
    _create: function() {
        $.extend(this.options, this.element.data());
        this.inputs = [];
        this._bindInput(0);
        this._bindInput(1);
        this.element.find('.clear').click($.proxy(function(event) {
            this.inputs[0].val('').change();
            this.inputs[1].val('').change();
            this.options.change.call(this);
            event.preventDefault();
        }, this));
        this.slider = $('<div>', {
            'class': 'slider'
        }).appendTo(this.element).slider({
            range: true,
            min: this.options.min,
            max: this.options.max,
            step: this.options.step,
            slide: $.proxy(function(event, ui) {
                this.inputs[0].val(ui.values[0] > this.options.min ? ui.values[0] : '');
                this.inputs[1].val(ui.values[1] < this.options.max ? ui.values[1] : '');
            }, this),
            stop: $.proxy(function(event, ui) {
                this.options.change.call(this);
            }, this),
            values: [this._getValue(0), this._getValue(1)]
        });
        this.facets = $('<div>', {
            'class': 'facets'
        }).prependTo(this.slider);
        this.multi = 100 / (this.options.max - this.options.min);
    },
    _bindInput: function(index) {
        this.inputs[index] = this.options.inputs[index] ? $(this.options.inputs[index]) :
        this.element.find('input').eq(index);
        this.inputs[index].change($.proxy(function() {
            this.slider.slider('values', index, this._getValue(index));
        }, this));
    },
    _getValue: function(index) {
        var input = this.inputs[index],
            value = input.val();
        if (value == '')
            value = input.prop('placeholder');
        return value.toNumber();
    },
    range: function(min, max) {
        this.inputs[0].prop('placeholder', min || this.options.min);
        this.inputs[1].prop('placeholder', max || this.options.max);
        this.slider.slider('option', 'values', [this._getValue(0), this._getValue(1)]);
        var left = (min - this.options.min) * this.multi;
        this.facets.css({
            left: left + '%',
            width: ((max - this.options.min) * this.multi - left) + '%'
        });
    }
});