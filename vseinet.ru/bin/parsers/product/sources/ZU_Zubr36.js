module.exports.url = function(source, data) {
    return source.site + '/products/' + data.artikul.replace(/ /g, '_')
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $product = document.querySelector('div.product')
    if (null !== $product) {
        result = {
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $image = $product.querySelector('.image img')
        if (null !== $image) {
            result.images.push($image.getAttribute('src').trim())
            $product.removeChild($image.closest('.image'))
        }
        var $images = $product.querySelector('.more-img')
        if (null !== $images) {
            $product.removeChild($images)
        }
        var $cl = $product.querySelector('.cl')
        if (null !== $cl) {
            $product.removeChild($cl)
            var $description = $product.querySelector('.description')
            if (null !== $description) {
                $product.removeChild($description)
            }
            var $h2 = $product.querySelector('h2')
            if (null !== $h2) {
                $product.removeChild($h2)
            }
            var $features = $product.querySelector('.features')
            if (null !== $features) {
                var $details = $features.querySelectorAll('li span')
                var len = $details.length
                if (len) {
                    var details = [];
                    for (var i = 0; i < len; i++) {
                        var items = $details[i].innerHTML.split('<br>')
                        var len2 = items.length
                        if (len2) {
                            for (var j = 0; j < len2; j++) {
                                var item = items[j]
                                if ('-' == item[0]) {
                                    item = item.slice(1)
                                    var parts = item.split(': ')
                                    details.push({
                                        name: parts[0].trim(),
                                        value: (parts[1] || '+').trim()
                                    })
                                }
                                else {
                                    var $name = $details[i].previousElementSibling
                                    if (null !== $name) {
                                        details.push({
                                            name: $name.textContent.trim(),
                                            value: item.trim()
                                        })
                                    }
                                }
                            }
                        }
                    }
                    if (details.length) {
                        result.details.push({
                            name: 'Основное',
                            details: details
                        })
                    }
                    $product.removeChild($features)
                }
            }
            var $details = $product.querySelector('table')
            if (null !== $details) {
                var details = []
                var $items = $details.querySelectorAll('tr')
                var len = $items.length
                if (len) {
                    for (var i = 0; i < len; i++) {
                        var $row = $items[i].querySelectorAll('td')
                        details.push({
                            name: $row[0].textContent.trim(),
                            value: $row[1].textContent.trim()
                        }) 
                    }
                }
                if (details.length) {
                    result.details.push({
                        name: 'Основное',
                        details: details
                    })
                }
                $product.removeChild($details)
            }
            var $iframe = $product.querySelector('iframe')
            if (null !== $iframe) {
                $product.removeChild($iframe.parentNode)
            }
            var $p = $product.querySelectorAll('p')
            var len = $p.length
            if (len) {
                for (var i = 0; i < len; i++) {
                    if ('' == $p[i].textContent.trim()) {
                        $product.removeChild($p[i])
                    }
                }
            }
            var $back = $product.querySelector('#back_forward')
            if (null !== $back) {
                $product.removeChild($back)
            }
            result.description = $product.innerHTML.replace(/<!--[\s\S]*?-->/g, '').trim()
        }
        url = window.location.href
    }

    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []