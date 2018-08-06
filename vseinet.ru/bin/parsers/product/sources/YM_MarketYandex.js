module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/search?text=' + encodeURIComponent(data.name) + '&cvredirect=2'
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    if (/market.yandex.ru\/product/i.test(window.location.href)) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            description: '',
            details: []
        }
        var $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $image = document.querySelector('.n-gallery__image.image')
        if (null !== $image) {
            result.images = ['http:' + $image.getAttribute('src').trim().replace('&size=9', '')]
        }
        var $details = document.querySelectorAll('.product-spec-wrap__body')
        var len = $details.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var $group = $details[i].querySelector('h2')
                if (null !== $group) {
                    (function($group, $details) {
                        var details = []
                        var len = $details.length
                        for (var i = 0; i < len; i++) {
                            details.push({
                                name: $details[i].querySelector('.product-spec__name-inner').firstChild.textContent.trim(),
                                value: $details[i].querySelector('.product-spec__value-inner').firstChild.textContent.trim()
                            })
                        }
                        result.details.push({
                            name: $group.textContent.trim(),
                            details: details
                        })
                    })($group, $details[i].querySelectorAll('.product-spec'))
                }
            }
        }
        else {
            next_url = window.location.href.replace(window.location.search, '') + '/spec' + window.location.search
        }
        if (!data.url) {
            url = window.location.href
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []