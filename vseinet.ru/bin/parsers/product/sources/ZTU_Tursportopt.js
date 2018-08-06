module.exports.url = function(source, data) {
    if (!data.artikul) {
        return null
    }
    return data.next_url || 
        source.site + '/search/?searchstring=' + encodeURIComponent(data.artikul)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('.cpt_product_name h1')
    if (null !== $name) {
        result = {
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }        
        var $image = document.querySelector('#img-current_picture')
        if (null !== $image) {
            var $bigimage = $image.parentNode
            if (null !== $bigimage && 'A' == $bigimage.tagName) {
                result.images.push(source.site + $bigimage.getAttribute('href').trim())
            }
            else {
                result.images.push(source.site + $image.getAttribute('src').trim())    
            }
        }
        var $images = document.querySelectorAll('.small_img a')
        var len = $images.length 
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push(source.site + $images[i].getAttribute('href').trim())
            }
        }
        var $description = document.querySelector('.description')
        if (null !== $description) {
            result.description = $description.textContent.replace(/\s+/g, ' ').trim()
        }
        var $details = document.querySelectorAll('.cpt_product_params_selectable tr, .cpt_product_params_fixed tr')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var $row = $details[i].querySelectorAll('td')
                if (2 == $row.length) {
                    var name = $row[0].textContent.replace(':', '').trim()
                    var value = $row[1].textContent.trim()
                    if ('Производитель' == name) {
                        result.brand = value
                    }
                    else if ('Баз. опт' != name && 'Опт1' != name && 'Опт2' != name && 'Опт3' != name) {
                        details.push({
                            name: name,
                            value: value
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
        var $link = document.querySelector('.catalog .item .title a')
        if (null !== $link) {
            url = next_url = source.site + $link.getAttribute('href').trim()
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []