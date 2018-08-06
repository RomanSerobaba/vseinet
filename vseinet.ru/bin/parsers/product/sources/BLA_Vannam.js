module.exports.url = function(source, data) {
    return data.next_url ||  
        source.site + '/catalogue/search?string=' + encodeURIComponent(data.artikul)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('h1.fn')
    if (null !== $name) {
        result = {
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $images = document.querySelectorAll('.jGalleryThumb')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push(source.site + $images[i].getAttribute('href').trim())
            }
        }
        $description = document.querySelector('.captionItem.fullwidth')
        if (null !== $description) {
            var $p = $description.querySelectorAll('p')
            var len = $p.length
            if (len) {
                for (var i = 0; i < len; i++) {
                    if ('Характеристики:' == $p[i].textContent.trim()) {
                        var $details = $p[i].nextElementSibling
                        if ('ul' == $details.tagName.toLowerCase()) {
                            var $rows = $details.querySelectorAll('li')
                            var len2 = $rows.length
                            if (len2) {
                                var details = []
                                for (var j = 0; j < len2; j++) {
                                    var parts = $rows[j].textContent.split(': ')
                                    details.push({
                                        name: parts[0].trim(),
                                        value: parts[1].trim()
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
                        $description.removeChild($details)
                        $description.removeChild($p[i])
                    }
                }
            }
            result.description = $description.innerHTML.trim()
        }
    }
    else {
        var $link = document.querySelectorAll('.ActionsProduct')
        if (1 == $link.length) {
            url = next_url = source.site + $link[0].querySelector('.ProductTitle a').getAttribute('href').trim()
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []