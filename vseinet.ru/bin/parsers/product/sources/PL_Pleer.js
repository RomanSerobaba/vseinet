module.exports.url = function(source, data) {
    if (data.url) {
        if (!/pleer\.ru/.test(data.url)) {
            data.url = source.site + data.url
        }
        return data.next_url || data.url
    }
    return source.site + '/search_' + data.code + '.html'
}
module.exports.parse = function(source, data, callback) {
    window.onbeforeunload = null
    var url = null
    var next_url = null
    var result = null
    if (/\/search_/i.test(window.location.href)) {
        var $model = document.querySelector('.product_price_wrap p')
        if (null !== $model) {
            if ('Товар №:' != $model.textContent.substr(0, 8)) {
                $model = $model.nextElementSibling
            }
            if (data.code == $model.textContent.replace('Товар №: ', '').trim()) {
                var $section = $model.closest('.section_item')
                if (null !== $section) {
                    var $link = $section.querySelector('.item_link a')
                    if (null !== $link) {
                        url = next_url = source.site + '/' + $link.getAttribute('href').trim()
                    }
                }
            }
        } 
    }
    else {
        var $breadcrumbs = document.querySelectorAll('.brandcat .catalog_tree_open, .brandcat .catalog_tree_selected')
        var len = $breadcrumbs.length
        if (len) {
            url = window.location.href
            result = {
                breadcrumbs: [],
                name: '',
                brand: '',
                model: '',
                images: [],
                description: '',
                details: []
            }
            for (var i = 0; i < len; i++) {
                var $link = $breadcrumbs[i].querySelector('.dotted a')
                if (null !== $link) {
                    var link = $link.getAttribute('href').trim()
                    result.breadcrumbs.push({
                        name: $link.textContent.trim(),
                        url: /javascript/i.test(link) ? null : source.site + link
                    })
                }
            }
        }
        var $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $breadcrumbs = document.querySelectorAll('.address_bar a')
        var len = $breadcrumbs.length
        if (len) {
            var brand = $breadcrumbs[len - 1].textContent.trim()
            var re = new RegExp(brand, 'i')
            if (re.test(result.name)) {
                result.brand = brand
            }
        }
        var $model = document.querySelector('.product_price_wrap p')
        if (null !== $model) {
            if ('Товар №:' != $model.textContent.substr(0, 8)) {
                $model = $model.nextElementSibling
            }
            result.model = $model.textContent.replace('Товар №: ', '').trim()
        }
        var $description = document.querySelector('#s_desc_' + result.model)
        if (null !== $description) {
            result.description = $description.textContent.replace(/\s+/g, ' ').trim()
        }
        var $details = document.querySelectorAll('.text_m .text3 li')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var row = $details[i].textContent.split(':')
                details.push({
                    name: row[0].trim(),
                    value: 1 < row.length ? row[1].trim() : '+'
                })
            }
            if (details.length) {
                result.details.push({
                    name: 'Основное',
                    details: details
                })
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