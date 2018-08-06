module.exports.url = function(source, data) {
    return data.next_url || data.url || 
        source.site + '/catalog/' + encodeURIComponent(data.ccode)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $breadcrumbs = document.querySelectorAll('.breadcrumbs a')
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
        for (var i = 2; i < len - 1; i++) {
            result.breadcrumbs.push({
                name: $breadcrumbs[i].textContent.trim(),
                url: source.site + $breadcrumbs[i].getAttribute('href').trim()
            })
        }
        url = window.location.href
        result.brand = $breadcrumbs[len - 1].textContent.trim()
        var $name = document.querySelectorAll('.breadcrumbs span')
        var len = $name.length
        if (len) {
            result.name = $name[len-1].textContent.trim()
        }
        var $model = document.querySelector('.product_id')
        if (null !== $model) {
            result.model = $model.textContent.trim()
        }
        var $description = document.querySelector('.short_description')
        if (null !== $description) {
            result.description = $description.textContent.replace(/\s+/g, ' ').trim()
        }
        var $details = document.querySelectorAll('.product_features tr')
        var len = $details.length
        if (len) {
            var group = 'Основное'
            var details = []
            for (var i = 0; i < len; i++) {
                if (/header_row/.test($details[i].className)) {
                    if (details.length) {
                        result.details.push({
                            name: group,
                            details: details
                        })
                    }
                    group = ''
                    var $group = $details[i].querySelector('.h3')
                    if (null !== $group) {
                        group = $group.textContent.trim()
                    }
                    if (!group) {
                        group = 'Прочее'
                    }
                    details = []
                }
                else {
                    details.push({
                        name: $details[i].querySelector('.property_name').textContent.trim(),
                        value: $details[i].querySelector('td').textContent.trim()
                    })
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
        var $link = document.querySelectorAll('.link_gtm-js')
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