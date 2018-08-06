module.exports.url = function(source, data) {
    return source.site + '/ns/orion_kartochka.php?kod=' + data.code
}
module.exports.parse = function(source, data, callback) {
    var url = null 
    var next_url = null 
    var result = null 
    var $name = document.querySelector('.showcase h5')
    if (null !== $name) {
        result = {
            breadcrumbs: [],
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        url = window.location.href
        var $breadcrumbs = document.querySelectorAll('.path a')
        var len = $breadcrumbs.length 
        if (2 < len) {
            for (var i = 1; i < len - 1; i++) {
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: source.site + '/ns/' + $breadcrumbs[i].getAttribute('href').trim()
                })
            }
        }
        var $image = document.querySelector('.showcase .box img')
        if (null !== $image) {
            result.images.push(source.site + '/ns/' + $image.getAttribute('src').trim())
        }
        var $description = document.querySelector('.description')
        if (null !== $description) {
            result.description = $description.textContent.trim().replace('Краткое описание:', '')
        }
        var $details = document.querySelectorAll('.features .parameter')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var $value = $details[i].nextElementSibling
                if (null !== $value) {
                    details.push({
                        name: $details[i].textContent.trim(),
                        value: $value.textContent.trim()
                    })
                }
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