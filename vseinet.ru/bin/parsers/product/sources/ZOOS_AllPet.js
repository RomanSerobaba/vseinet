module.exports.url = function(source, data) {
    return data.next_url ||
        source.site + '/search/?query=' + encodeURIComponent(data.artikul)  
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('h1 [itemprop=name]')
    if (null !== $name) {
        result = {
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $image = document.querySelector('#product-image')
        if (null !== $image) {
            result.images.push(source.site + $image.parentNode.getAttribute('href').trim())
        }
        var $description = document.querySelector('#product-description font')
        if (null !== $description) {
            result.description = $description.innerHTML.trim()
        }
        var $details = document.querySelectorAll('.features tr')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                details.push({
                    name: $details[i].querySelector('.name').textContent.trim(),
                    value: $details[i].querySelector('.value').textContent.trim()
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
        var $links = document.querySelectorAll('[itemprop=name]')
        if (1 == $links.length) {
            url = next_url = source.site + $links[0].closest('a').getAttribute('href').trim()
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []