module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/search/?search=' + encodeURIComponent(data.artikul)    
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    if (null !== document.querySelector('.product-page')) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $breadcrumbs = document.querySelectorAll('.breadcrumb a')
        var len = $breadcrumbs.length
        if (len) {
            for (var i = 1; i < len; i++) {
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: $breadcrumbs[i].getAttribute('href').trim()
                })
            }
        }
        var $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $model = document.querySelector('meta[itemprop=model]')
        if (null !== $model) {
            result.model = $model.getAttribute('content').trim()
        }
        var $description = document.querySelector('.description-content')
        if (null !== $description) {
            result.description = $description.textContent.replace(/\s+/g, ' ').trim()
        }
        var $details = document.querySelectorAll('.left-attr')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var $row = $details[i].closest('tr')
                if (null !== $row) {
                    var $value = $row.querySelector('.right-attr')
                    if (null !== $value) {
                        details.push({
                            name: $details[i].textContent.trim(),
                            value: $value.textContent.trim()
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
    }
    else {
        var $element = document.querySelector('h3 a')
        if (null !== $element) {
            url = next_url = $element.getAttribute('href').trim()
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []