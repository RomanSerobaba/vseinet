var sp = window.sp = {
    security: {

    },
    _xhr: null,
    get: function(url, data, context) {
        return sp._xhr = $.ajax({
            url: url,
            data: data || {},
            dataType: 'json',
            context: context || window
        });
    },
    post: function(url, data, context) {
        return sp._xhr = $.ajax({
            url: url,
            type: 'post',
            data: $.extend(data || {}, sp.security),
            dataType: 'json',
            context: context || window
        });
    },
    abort: function() {
        if (sp._xhr) {
            var xhr = sp._xhr;
            sp._xhr = null;
            xhr.abort();
        }
    }
};

sp.getSelectedText = function() {
    var text = '';
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection) {
        var range = document.selection.createRange();
        range = range.text || range;
        text = range.toString ? range.toString() : '';
    }
    return text;
};

sp.clearSelection = function() {
    if (document.selection && document.selection.empty) {
        document.selection.empty();
    } else if (window.getSelection) {
        window.getSelection().removeAllRanges();
    }
};

$.fn.textWidth = function() {
    var element = $(this),
        clone = $('<div>').css({
            fontFamily: element.css('fontFamily'),
            fontSize: element.css('fontSize'),
            fontStyle: element.css('fontStyle'),
            fontWeight: element.css('fontWeight'),
            display: 'table-cell',
            visibility: 'hidden'
        }).appendTo('body');
    clone[0].innerHTML = (element.val() || element[0].innerHTML).replace(/\s/g, '&nbsp;');
    var width = clone.width();
    clone.remove();
    return width;
};

sp.dialog = function(width) {
    return $('<div class="loading"></div>').appendTo('body').dialog({
        close: function() {
            $(this).remove();
        },
        closeText: 'Закрыть',
        draggable: false,
        modal: true,
        minWidth: width || 600,
        position: {
            using: function(pos) {
                pos.top = 45 + $(window).scrollTop();
                $(this).css(pos);
            }
        },
        resizable: false
    });
};

$(function() {
    sp.window = $(window);
    sp.document = $(document);
    sp.mobile = navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i);
    if (!sp.mobile) {
        $('#top').sticky();
    }
});


String.prototype.toNumber = function(def) {
    var s = this.replace(',', '.').replace(/\s+/g, '');
    return s === '0' || (s/s) ? +s : def || 0;
}

sp.declension = function(count, forms) {
    if (typeof forms === 'string')
        forms = forms.split(';');
    switch (forms.length) {
        case 1:
                forms[1] = forms[0];
        case 2:
                forms[2] = forms[1];
    }
    var mod100 = count % 100;
    switch (count % 10) {
        case 1:
             if (mod100 == 11)
                 return forms[2];
             else
                 return forms[0];
         case 2:
         case 3:
         case 4:
             if (mod100 > 10 && mod100 < 20)
                 return forms[2];
             else
                 return forms[1];
    }
    return forms[2];
}

Number.prototype.declension = function(forms) {
    return this + ' ' + sp.declension(this, forms);
}

Number.prototype.formatMoney = function(decimals, decPoint, thousandsSep) {
    var n = this,
        c = isNaN(decimals) ? 0 : Math.abs(decimals),
        d = decPoint || ',',
        t = (typeof thousandsSep === 'undefined') ? ' ' : thousandsSep,
        sign = (n < 0) ? '-' : '',
        i = parseInt(n = Math.abs(n).toFixed(c)) + '',
        j = ((j = i.length) > 3) ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
};

Number.prototype.formatPrice = function(decimals) {
    var value = Math.abs(this),
        price = Math.floor(value / 100).formatMoney(0),
        hide_zero_k = isNaN(decimals);
    if (hide_zero_k)
        decimals = 2;
    if (1 == decimals || 2 == decimals) {
        var k = value % 100;
        if (k || !hide_zero_k) {
            if (1 == decimals)
                k = Math.round(k / 10);
            price += '.' + (k / 100).toFixed(decimals).substr(2);
        }
    }
    if (0 > this) {
        price = '-' + price;
    }
    return price;
}

$.extend($.ui.keyCode, {
    CTRL: 17,
    NUMPAD_LOCK: 144
});