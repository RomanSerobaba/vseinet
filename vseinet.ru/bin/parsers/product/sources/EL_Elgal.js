module.exports.url = function(source, data) {
    return data.next_url || 'http://go.mail.ru/search?' + require('querystring').stringify({
        fm: 1,
        q: data.name + ' site:www.elgal.ru'
    })
}
module.exports.parse = function(source, data, callback) {
    var url = null 
    var next_url = null 
    var result = null 
    if (/elgal\.ru/.test(window.location.host)) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $name = document.querySelector('h1[itemprop=name]')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $breadcrumbs = document.querySelectorAll('.navmenu .navpoint')
        var len = $breadcrumbs.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var href = $breadcrumbs[i].getAttribute('href')
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: href ? href.trim() : source.site 
                })
            }
        }
        var $brand = document.querySelector('[itemprop=brand]')
        if (null !== $brand) {
            result.brand = $brand.textContent.trim()
        }
        var $image = document.querySelector('[itemprop=image]')
        if (null !== $image) {
            $image = $image.closest('a')
            if (null !== $image) {
                result.images.push($image.getAttribute('href').trim())
            }
        }
        var $description = document.querySelector('.spoiler-body')
        if (null !== $description) {
            result.description = $description.textContent.trim()
        }
        else {
            var $description = document.querySelector('[itemprop=description]')
            if (null !== $description) {
                result.description = $description.textContent.trim()
            }
        }
        var $names = document.querySelectorAll('td[itemprop=name]')
        var len = $names.length
        if (len) {
            var name, original = data.name.replace('NEW!!', '').replace('подмят', '').trim()
            for (var i = 0; i < len; i++) {
                name = $names[i].textContent.replace('NEW!!', '').replace('подмят', '').trim()
                if (name == original) {
                    result.name = name 
                    var $row = $names[i].closest('tr')
                    if (null !== $row) {
                        var $model = $row.querySelector('[itemprop=serialNumber]')
                        if (null !== $model) {
                            result.model = $model.textContent.trim()
                        }
                        var $color = $row.querySelector('.colorblock')
                        if (null !== $color) {
                            var m = $color.getAttribute('style').match(/#([0-F]{6})/)
                            if (m && 2 == m.length) {
                                result.details.push({
                                    name: 'Основное',
                                    details: [{
                                        name: 'Цвет',
                                        value: m[1]
                                    }]
                                })
                            }
                        }
                    }
                    break
                }
            }
        }
    }
    else {
        var $links = document.querySelectorAll('.block-info-serp__link')
        var len = $links.length 
        if (len) {
            var href
            for (var i = 0; i < len; i++) {
                href = $links[i].getAttribute('href').trim()
                if (/item\/\d+/.test(href)) {
                    next_url = url = href
                    break
                }
            }
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []
