module.exports.url = function(source, data) {
    return data.next_url || source.site + '/shop/?q=' + encodeURIComponent(data.name)
}
module.exports.parse = function(source, data, callback) {
    var url = null 
    var next_url = null 
    var result = null 
    var $name = document.querySelector('[itemprop=name] h1')
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
        var $breadcrumbs = document.querySelectorAll('.breadcrumb a')
        var len = $breadcrumbs.length 
        if (len) {
            for (var i = 1; i < len; i++) {
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: source.site + $breadcrumbs[i].getAttribute('href').trim()
                })
            }
        }
        var $image = document.querySelector('.picture-box .j-fancybox')
        if (null !== $image) {
            result.images.push(source.site + $image.getAttribute('href').trim())
        }
        var $description = document.querySelector('[itemprop=description]')
        if (null !== $description) {
            result.description = $description.textContent.trim()
        }
    }
    else {
        var $links = document.querySelectorAll('[itemprop=name]')
        if (1 == $links.length) {
            var $link = $links[0].closest('a')
            if (null !== $link) {
                next_url = url = source.site + $link.getAttribute('href').trim()
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