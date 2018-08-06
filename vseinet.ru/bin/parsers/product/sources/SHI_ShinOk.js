var execSync = require('child_process').execSync

module.exports.encode1251 = function(text) {
    var str = ''
    var buffer = execSync('echo "' + text.replace(/"/g, '\\"') + '" | iconv -t CP1251//TRANSLIT -f UTF-8', function(error, stdout, stderr) {
        return stdout
    })
    for(var i = 0; i < buffer.length; i++) {
        str += buffer[i] != 0xa ? '%' + buffer[i].toString(16).toUpperCase() : ''
    }
    return str
}
module.exports.url = function(source, data) {
    return data.next_url ||  
        source.site + '/catalog/search/?q=' + this.encode1251(data.name)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $search = document.querySelector('.search-page')
    if (null !== $search) {
        var $items = $search.querySelectorAll('h4 a')
        var len = $items.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var name = $items[i].textContent.trim()
                if (name.replace(/\s+/g, '').toLowerCase() == data.name.replace(/\s+/g, '').toLowerCase()) {
                    url = source.site + $items[i].getAttribute('href').trim()
                    if (/\/diski_/.test(url)) {
                        url = url.replace(/\/diski_[^\/]+/, '')
                    }
                    next_url = url
                    break
                }
            }
        }
    }
    else {
        var $product = document.querySelector('.article-product')
        if (null !== $product) {
            result = {
                name: data.name,
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
            var $images = $product.querySelectorAll('.main-fotos a')
            var len = $images.length
            if (len) {
                for (var i = 0; i < len; i++) {
                    result.images.push(source.site + $images[i].getAttribute('href').trim())
                }
            }
            var $details = $product.querySelectorAll('.options li .key')
            var len = $details.length
            if (len) {
                var details = []
                for (var i = 0; i < len; i++) {
                    var $value = $details[i].nextElementSibling
                    if (null !== $value) {
                        var name = $details[i].textContent.trim()
                        var value = $value.textContent.trim()
                        if ('Производитель' == name) {
                            result.brand = value
                        }
                        else {
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
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []
