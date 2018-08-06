module.exports.url = function(source, data) {
    return data.url
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $breadcrumbs = document.querySelectorAll('.nav a')
    var len = $breadcrumbs.length
    if (len) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        for (var i = 1; i < len; i++) {
            result.breadcrumbs.push({
                name: $breadcrumbs[i].textContent.trim(),
                url: source.site + $breadcrumbs[i].getAttribute('href').trim()
            })
        }
    }
    var $name = document.querySelector('h1')
    if (null !== $name) {
        result.name = $name.textContent.trim()
    }
    var $images = document.querySelectorAll('.small_img a')
    var len = $images.length
    if (len) {
        for (var i = 0; i < len; i++) {
            result.images.push(source.site + $images[i].getAttribute('href').trim())
        }
    }
    var $description = document.querySelector('.description')
    if (null !== $description) {
        result.description = $description.textContent.replace(/\s+/g, ' ').trim()
    }
    var $details = document.querySelectorAll('.cpt_product_params_selectable tr, .cpt_product_params_fixed tr')
    var len = $details.length
    if (len) {
        var details = []
        for (var i = 0; i < len; i++) {
            var $row = $details[i].querySelectorAll('td')
            if (2 == $row.length) {
                var name = $row[0].textContent.replace(':', '').trim()
                var value = $row[1].textContent.trim()
                if ('Производитель' == name) {
                    result.brand = value
                }
                else {
                    details.push({
                        name: name,
                        value: value
                    })
                }
            }
        }
        if (details.length) {
            result.details.push({
                name: 'Основное',
                details: details
            })
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []