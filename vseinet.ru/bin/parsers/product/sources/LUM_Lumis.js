module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/search/?q=' + encodeURIComponent(data.code)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    if (null !== document.querySelector('.tovar_big')) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $breadcrumbs = document.querySelectorAll('.navigation_block a')
        var len = $breadcrumbs.length
        if (len) {
            for (var i = 2; i < len; i++) {
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: source.site + $breadcrumbs[i].getAttribute('href').trim()
                })
            }
        }
        var $name = document.querySelector('h2')
        if (null !== $name) {
            result.name = $name.textContent.replace('оптом', '').trim()
        }
        var $model = document.querySelector('.scu')
        if (null !== $model) {
            result.model = $model.textContent.replace('Арт. ', '')
        }
        var $image = document.querySelector('.tovar_big img')
        if (null !== $image) {
            result.images.push(source.site + $image.getAttribute('src').trim())
        }
        var $details = document.querySelectorAll('.tovar_tbl .first_st')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var name = $details[i].textContent.trim().replace(':', '')
                var value = $details[i].nextElementSibling.textContent.trim()
                if ('Марка' == name) {
                    result.brand = value
                }
                details.push({
                    name: name, 
                    value: value
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
    else {
        var $element = document.querySelector('.product_name a')
        if (null !== $element) {
            url = next_url = source.site + $element.getAttribute('href').trim()
        }    
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []