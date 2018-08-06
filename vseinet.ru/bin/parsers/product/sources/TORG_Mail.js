module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/search/?q=' + encodeURIComponent(data.name)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('h1')
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
        var $breadcrumbs = document.querySelectorAll('.breadcrumbs a')
        var len = $breadcrumbs.length
        if (len) {
            for (var i = 1; i < len; i++) {
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: source.site + $breadcrumbs[i].getAttribute('href').trim()
                })
            }
        }
        var $brand = $name.querySelector('[itemprop=brand]')
        if (null !== $brand) {
            result.brand = $brand.textContent.trim()
        }
        var $model = $name.querySelector('[itemprop=model]')
        if (null !== $model) {
            result.model = $model.textContent.trim()
        }
        var $images = document.querySelectorAll('.goods_image_listing__preview_on img')
        var len = $images.length 
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push($images[i].getAttribute('data-lazy-src').trim().replace('230x230', '450x450'))
            }
        }
        var $groups = document.querySelectorAll('.good__charectistic__full h3')
        var len = $groups.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var $details = $groups[i].nextElementSibling.querySelectorAll('th')
                var len2 = $details.length 
                if (len2) {
                    var details = []
                    for (var j = 0; j < len2; j++) {
                        var $info = $details[j].querySelector('a')
                        if (null !== $info) {
                            $info.closest('b').removeChild($info)
                        }
                        details.push({
                            name: $details[j].textContent.trim(),
                            value: $details[j].nextElementSibling.textContent.trim()
                        })
                    }
                    result.details.push({
                        name: $groups[i].textContent.trim(),
                        details: details
                    })    
                }
            }
        }
    }
    else {
        var $searchResults = document.querySelectorAll('.search-results .preview-card-line__title a')
        var len = $searchResults.length 
        if (len) {
            for (var i = 0; i < len; i++) {
                var href = $searchResults[i].getAttribute('href').trim()
                if (!/\/go\//.test(href)) {
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