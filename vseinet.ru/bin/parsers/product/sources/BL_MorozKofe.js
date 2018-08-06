module.exports.url = function(source, data) {
    return data.next_url || data.url || 
        source.site + '/katalog?' + require('querystring').stringify({
            pf: 1,
            flt_force_values: 1,
            action: 'search',
            search_subcats: 1,
            search_text: data.name
        })
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $breadcrumbs = document.querySelectorAll('.category-path__link')
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
                name: $breadcrumbs[i].textContent.trim(),
                url: $breadcrumbs[i].getAttribute('href').trim()
            })
        }
        var $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $model = document.querySelector('.id_mk')
        if (null !== $model) {
            result.model = $model.textContent.replace(/\D+/g, '')
        }
        var $images = document.querySelectorAll('.eshop-item-detailed__img')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push($images[i].getAttribute('src').trim())
            }
        }
        var $description = document.querySelector('.eshop-item-detailed__description .desc_mk')
        if (null !== $description) {
            result.description = $description.textContent.replace(/\s+/g, ' ').trim()
        }
        var $details = document.querySelectorAll('.table_mk tr')
        var len = $details.length
        if (len) {
            var group = 'Основное'
            var details = []
            for (var i = 0; i < len; i++) {
                var $row = $details[i].querySelectorAll('td')
                switch ($row.length) {
                    case 1:
                        if (details.length) {
                            result.details.push({
                                name: group,
                                details: details
                            })
                        }
                        group = $row[0].textContent.trim()
                        details = []
                        break
                    case 2:
                        var available = ''
                        var check = $row[1].querySelector('img')
                        if (null !== check) {
                            available = /check/.test(check.getAttribute('src')) ? 'Есть ' : 'Нет '
                        }
                        details.push({
                            name: $row[0].textContent.trim(),
                            value: available + $row[1].textContent.trim()
                        })
                        break
                }
            }
            if (details.length) {
                result.details.push({
                    name: group,
                    details: details
                })    
            }
        }
    } 
    else {
        var $element = document.querySelector('.eshop-item-small__title')
        if (null !== $element) {
            var link = $element.getAttribute('href').trim()
            if ('/' != link) {
                link = '/' + link
            }
            url = next_url = source.site + link
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []