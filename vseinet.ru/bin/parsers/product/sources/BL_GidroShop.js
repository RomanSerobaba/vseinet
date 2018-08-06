module.exports.url = function(source, data) {
    return data.next_url ||  
        source.site + '/advanced_search_result.php?x=40&y=7&keywords=' + encodeURIComponent(data.artikul)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $cart = document.querySelector('#cart_quantity')
    if (null !== $cart) {
        result = {
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $rows = $cart.children[0].children[0].children
        if ($rows.length) {
            var $name = $rows[0].querySelector('font')
            if (null !== $name) {
                result.name = $name.textContent.trim()
            }
            var $image = $rows[2].querySelector('[rel="gallery-plants"]')
            if (null !== $image) {
                result.images.push(source.site + '/' + $image.getAttribute('href').trim())
            }
            var $details = $rows[2].children[2].querySelectorAll('tr')
            var len = $details.length
            if (len) {
                for (var i = 0; i < len; i++) {
                    var $brand = $details[i].children[0]
                    if (null !== $brand && 'Производитель:' == $brand.textContent.trim()) {
                        result.brand = $details[i].children[1].textContent.trim()
                        break
                    }
                }
            }
            var $images = $rows[3].querySelectorAll('a')
            var len = $images.length 
            if (len) {
                for (var i = 0; i < len; i++) {
                    result.images.push(source.site + '/' + $images[i].getAttribute('href').trim())
                }
            }
            var $description = $rows[4].querySelector('table').querySelector('td')
            if (null !== $description) {
                result.description = $description.innerHTML
            }
        }
    }
    else {
        var $link = document.querySelectorAll('td.contentBoxContents1')
        if (1 == $link.length) {
            url = next_url = $link[0].querySelector('td.contents a').getAttribute('href').trim()
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []