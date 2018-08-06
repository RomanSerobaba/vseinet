$.widget('sp.slideshow', {
    options: {
        index: 0,
        duration: 800,
        autoplay: 10000
    },
    _create: function() {
        this.index = this.options.index;
        this.sheetsContainer = this.element.find('.sheets');
        this.sheets = this.sheetsContainer.children();
        this.sheetsContainer
                .prepend(this.sheets.filter(':last').clone())
                .css('left', '-100%')
                .append(this.sheets.eq(0).clone());

        this.tabsContainer = this.element.find('.tabs');

        if (this.tabsContainer.length) {
            this.tabs = this.tabsContainer.children();
            this.tabsFixed = this.tabs.filter('.fixed');
            this.tabsFixedGap = this.tabsFixed.width() - this.tabs.width();
            this.tabWidth = this.tabs.width()|0;
            this.tabsWidth = this.tabWidth * this.tabs.length;
            this.tabsContainer.width(this.tabsWidth * 1.1);
            this.tab = this.tabs.eq(this.index).addClass('active');
            this.tabsPos = 0;
            this.cursor = $('<div class="cursor"/>').appendTo(this.tabsContainer);

            $(window).on('resize.slideshow', $.proxy(function() {
                this._calculate(this.index);
                var width = this.element.width(), tabsFixedWidth = this.tabsFixed.length * this.tabWidth;
                if ( ! this.tab.is('.fixed') && width - tabsFixedWidth < this.cursorPos - this.tabWidth)
                    this.tabsPos = tabsFixedWidth - this.index * this.tabWidth;
                else if (width - this.tabsPos > this.tabsWidth)
                    this.tabsPos = width - this.tabsWidth;
                this.tabsFixedPos = width - this.tabsWidth - this.tabsPos;
                this.tabsContainer.css({left: this.tabsPos});
                this.tabsFixed.css({left: this.tabsFixedPos});
                this.cursor.css({left: this.cursorPos});
            }, this)).triggerHandler('resize.slideshow');
        }

        this._on({
            'click .tab': function(event) {
                this.slide($(event.target).index());
                event.preventDefault();
            },
            mouseenter: function() {
                this.stop();
            },
            mouseleave: function(event) {
                this.start();
                this._move(event, true);
            },
//            'click a': function(event) {
//                event.preventDefault();
//            },
            'mousedown .sheets': function(event) {
                if (event.button == 0)
                    this._hold(event);
                event.preventDefault();
            },
            mousemove: function(event) {
                this._move(event);
            },
            mouseup: function(event) {
                if (this.x) {
                    if (Math.abs(this._offset(event) - this.x) < 10) {
                        var a = $(event.target).closest('a');
                        if (a.length) {
                            window.location = a.prop('href');
                            return false;
                        }
                        this.x = null;
                        this.sheetsContainer.css({left: this.left});
                    }
                    else
                        this._move(event, true);
                }
            }
        });
        if ('ontouchstart' in window)
            this._on({
                touchstart: function() {
                    this.stop();
                },
                'touchstart .sheets': function(event) {
                    this._hold(event);
                },
                touchmove: function(event) {
                    this._move(event);
                },
                'touchend .sheets': function(event) {
                    this._move(event, true);
                },
                touchend: function(event) {
                    this.start();
                },
                'touch .tab': function(event) {
                    this.slide($(event.originalEvent.target).index());
                    event.preventDefault();
                },
            });
    },
    _init: function() {
        this.start();
    },
    _hold: function(event) {
        var o = this._offset(event);
        this.x = o.x;
        this.y = o.y;        
        if (!o.touch) {
            event.preventDefault();      
        }
        this.left = this.sheetsContainer.position().left;
    },
    _move: function(event, slide) {
            if (this.x) {
                var o = this._offset(event);
                var dx = o.x - this.x;
                var dy = o.y - this.y;
                var a = $(event.target).closest('a');
                
                if (a.length && Math.abs(dx) < 5 && Math.abs(dy) < 5) {
                    window.location = a.prop('href');
                    event.preventDefault();
                    return false;
                }
                else if (o.touch && Math.abs(dx) < 1.5 * Math.abs(dy)) {
                    this.x = null;
                } 
                else {
                    event.preventDefault();
                    var w = this.element.width();
                    if (dx < -w) dx = -w;
                    else if (dx > w) dx = w;
                    if (slide) {
                        this[dx > 0 ? 'prev' : 'next'](150);
                        this.x = null;
                    }
                    else this.sheetsContainer.css({left: this.left + dx});
                }
            }
    },
    _offset: function(event) {
        var e = event.originalEvent;
        if (e.touches || e.changedTouches) {
            var touch = e.touches[0] || e.changedTouches[0];
            return {
                x: touch.pageX,
                y: touch.pageY,
                touch: true
            }    
        }
        return {
            x: event.pageX,
            y: event.pageY
        }
    },
    _calculate: function(index) {
        if (index < 0) {
            index = this.sheets.length - 1;
            this.sheetIndex = 0;
        }
        else if (index > this.sheets.length - 1) {
            index = 0;
            this.sheetIndex = this.sheets.length + 1;
        }
        else this.sheetIndex = index + 1;
        if (this.tabs.length) {
            var tab = this.tabs.eq(index), tabPos = tab.position().left|0, width = this.element.width();
            if (tab.is('.fixed'))
                this.cursorPos = tabPos;
            else if (this.tab.is('.fixed')) {
                if (index == 0)
                    this.tabsPos = 0;
                if (tabPos > this.cursorPos)
                    this.tabsPos = width - this.tabsWidth;
                this.cursorPos = tabPos;
            }
            else {
                if (this.index < index && this.tabsWidth + this.tabsPos > width) {
                    this.tabsPos -= this.tabWidth;
                }
                if (this.index > index && this.tabsPos < 0) {
                    if (this.interval && index == 0)
                        this.tabsPos = 0;
                    else
                        this.tabsPos += this.tabWidth;
                }
                this.cursorPos = tabPos;
            }
            this.tabsFixedPos = width - this.tabs.length * this.tabWidth - this.tabsPos - this.tabsFixedGap;
        }
        this.index = index;
    },
    slide: function(index, duration) {
        this._calculate(index);
        this.element.find(':animated').stop();
        if (this.tabs) {
            this.tab.removeClass('active');
            this.tabsContainer.animate({left: this.tabsPos}, 250);
            this.tabsFixed.animate({left: this.tabsFixedPos}, 250);
            this.cursor.addClass('slide').animate({left: this.cursorPos}, 250, $.proxy(function() {
                this.tab = this.tabs.eq(this.index).addClass('active');
                this.cursor.removeClass('slide').width(this.tab.width() - 8);
            }, this));
        }
        if (typeof(duration) === 'undefined') duration = this.options.duration;
        this.sheetsContainer.animate({left: (-100 * this.sheetIndex) + '%'}, duration, 'swing', $.proxy(function() {
                if (this.sheetIndex == 0)
                    this.sheetsContainer.css('left', (-100 * this.sheets.length) + '%');
                else
                if (this.sheetIndex == this.sheets.length + 1)
                    this.sheetsContainer.css('left', '-100%');
        }, this));
    },
    start: function() {
        if (this.options.autoplay)
            this.interval = setInterval($.proxy(this.next, this), this.options.autoplay);
    },
    stop: function() {
        clearInterval(this.interval);
    },
    next: function(duration) {
        this.slide(this.index + 1, duration);
    },
    prev: function(duration) {
        this.slide(this.index - 1, duration);
    }
});

$(function () {
    $('#slideshow').slideshow();
    setBcgPos()
});
function setBcgPos() {
    var itemWidth = $('.sheet').width();
    $('.sheet .photoBlock').each(function (index) {
        var backgroundPos = parseInt($(this).attr('data-pos-x'));
        if(itemWidth > 750){
            var x = (845 - itemWidth) / 2;
            backgroundPos -= x;
            $(this).css('backgroundPositionX', backgroundPos + 'px');
        } else if ((itemWidth < 750) && (itemWidth > 480)){
            var x = (750 - itemWidth) / 2;
            backgroundPos -= x;
            $(this).css('backgroundPositionX', backgroundPos + 'px');
        } else if ((itemWidth < 480) && (itemWidth > 320)){
            var x = (480 - itemWidth) / 2;
            backgroundPos -= x;
            $(this).css('backgroundPositionX', backgroundPos + 'px');
        }
    });
}
$(window).resize(function () {
    setBcgPos()
});