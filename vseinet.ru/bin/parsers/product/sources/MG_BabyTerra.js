module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/?iscode=on&search=' + encodeURIComponent(data.code) 
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $breadcrumbs = document.querySelectorAll('.breadcrumb a')
    var len = $breadcrumbs.length
    if (len) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        for (var i = 1; i < len; i++) {
            result.breadcrumbs.push({
                name: $breadcrumbs[i].textContent.replace('/', '').trim(),
                url: $breadcrumbs[i].getAttribute('href').trim()
            })
        }
        var $name = document.querySelector('.js-good-title')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $model = document.querySelector('.desc-features')
        if (null !== $model) {
            result.model = $model.textContent.trim()
        }
        var $images = document.querySelectorAll('.added-img')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push(source.site + $images[i].getAttribute('data-url').trim())
            }
        }
        if (0 == result.images.length) {    
            var $image = document.querySelector('.m-image img')
            if (null !== $image) {
                result.images.push(source.site + $image.getAttribute('src').trim())
            }
        }
        var $description = document.querySelector('.desc')
        if (null !== $description) {
            result.description = $description.textContent.replace('Производитель оставляет за собой право изменить комплектацию товара без предварительного уведомления', '').replace(/\s+/g, ' ').trim()
        }
        var $details = document.querySelectorAll('.first-descfeat')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var $row = $details[i].children
                if ($row.length) {
                    details.push({
                        name: $row[0].textContent.replace(':', '').trim(),
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
        }
    }
    else {
        var $element = document.querySelector('.list-goods-content a');
        if (null !== $element) {
            url = next_url = source.site + $element.getAttribute('href').trim()
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []