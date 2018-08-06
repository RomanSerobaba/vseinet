module.exports.url = function(source, data) {
    return data.next_url ||  
        source.site + '/Catalog/showPage/20/0/?&f[name]=' + encodeURIComponent(data.name)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('h1')
    if (null !== $name) {
        result = {
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        $image = document.querySelector('.img-container img')
        if (null !== $image) {
            var imageurl = $image.getAttribute('src').trim()
            if (!/nophoto\.png/.test(imageurl)) {
                result.images.push(imageurl)
            }
        }
        $details = document.querySelectorAll('.table-good tr')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                if ($details[i].classList.contains('table-price')) {
                    continue
                }
                var $name = $details[i].querySelector('[scope="row"]')
                if (null !== $name) {
                    var $value = $name.nextElementSibling
                    if (null !== $value) {
                        var name = $name.textContent.replace(':', '').trim()
                        if ('Бренд' == name) {
                            result.brand = $value.textContent.trim()
                        } 
                        else {
                            details.push({
                                name: name,
                                value: $value.textContent.trim()
                            })
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
        }
    }
    else {
        var $link = document.querySelectorAll('.items .td-name a')
        if (1 == $link.length) {
            url = next_url = $link[0].getAttribute('href').trim()
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []