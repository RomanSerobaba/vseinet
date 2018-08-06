module.exports.url = function(source, data) {
    return data.next_url || 
        source.site + '/search/?q=' + encodeURIComponent(data.code) 
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $breadcrumbs = document.querySelectorAll('.breadcrumbs a')
    var len = $breadcrumbs.length
    if (2 < len) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        for (var i = 2; i < len; i++) {
            result.breadcrumbs.push({
                name: $breadcrumbs[i].textContent.trim(),
                url: source.site + $breadcrumbs[i].getAttribute('href').trim()
            })
        }
        var $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $brand = document.querySelector('.brand-logo')
        if (null !== $brand) {
            result.brand = $brand.getAttribute('title').trim()
        }
        var $images = document.querySelectorAll('.js-preview-img a img')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var src = $images[i].getAttribute('src').trim()
                if (/400x400/.test(src)) {
                    result.images.push('http:' + src)
                }
            }
        }
        var $description = document.querySelector('#product-detail-text')
        if (null !== $description) {
            result.description = $description.textContent
        }
        var $features = document.querySelector('#product-features')
        if (null !== $features) {
            result.description += ' ' + $features.textContent
        }
        if (result.description) {
            result.description = result.description.replace(/\s+/g, ' ').trim()
        }
    }
    else {
        var $element = document.querySelector('#products-wrapper .j_product-link')
        if (null !== $element) {
        	var link = $element.getAttribute('href').trim();
        	if ('/' == link[0]) {
        		link = source.site + link 
        	}
            url = next_url = link
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []