module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/search/?stxt=' + encodeURIComponent(data.artikul)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $breadcrumbs = document.querySelectorAll('.links a')
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
        for (var i = 2; i < len; i++) {
            result.breadcrumbs.push({
                name: $breadcrumbs[i].querySelector('span').textContent.trim(),
                url: source.site + $breadcrumbs[i].getAttribute('href').trim()
            })
        }
        var $name = document.querySelector('h1 span')
        if (null !== $name) {
            result.name = $name.textContent.replace('оптом', '').trim()
        }
        var $model = document.querySelector('#articul')
        if (null !== $model) {
            result.model = $model.textContent.replace(/\D+/g, '')
        }
        var $image = document.querySelector('#imgGoodB')
        if (null !== $image) {
            result.images.push(source.site + $image.getAttribute('src').trim())
        }
        var $images = document.querySelectorAll('#otherImg a')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var src = $images[i].getAttribute('onclick').split("'")
                result.images.push(source.site + '/files/' + src[1].trim())
            }
        }
        var $description = document.querySelector('.fs_content')
        if (null !== $description) {
            result.description = $description.textContent.replace(/\s+/g, ' ').trim()
        }
        var $details = document.querySelectorAll('.table_descr_column tr')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var $row = $details[i].querySelectorAll('td')
                if ($row.length) {
                    var name = $row[0].textContent.trim()
                    var value = $row[1].textContent.trim()
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
    else {
        var $element = document.querySelector('.catalog_list .tip_holder')
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