module.exports.url = function(source, data) {
    return data.next_url || source.site + '/products/?search=' + encodeURIComponent(data.name)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $gallery = document.querySelector('.b-product__img-wrap.i-zk-gallery')
    if (null !== $gallery) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $name = document.querySelector('h1[itemprop=name]')
        if (null !== $name) {
            result.name = $name.textContent.trim()
            var $images = $gallery.querySelectorAll('.b-product__additional-pics img')
            var len = $images.length
            if (len) {
                for (var i = 0; i < len; i++) {
                    result.images.push('http:' + $images[i].getAttribute('data-source').trim())
                }
            }
            else {
                var $image = $gallery.querySelector('.b-product__img-figure img')
                if (null !== $image) {
                    var attr = $image.getAttribute('data-source')
                    if (null !== attr) {
                        result.images.push('http:' + attr.trim())
                    }
                }
            }
            if (0 == result.images.length) {
                result = null
            }
        }
    }
    else {
        $sorting = document.querySelector('.b-sorting')
        if (null !== $sorting) {
            $link = document.querySelector('.b-products-gallery__img-wrap.b-products-gallery__img-link')
            if (null !== $link) {
                next_url = url = source.site + $link.getAttribute('href').trim()
            }
            else {
                url = ''
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