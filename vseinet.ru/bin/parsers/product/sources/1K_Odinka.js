module.exports.url = function(source, data) {
    return data.next_url || data.url || 
        source.site + '/products/search?' + require('querystring').stringify({
            s_keywords: data.name,
            searchFor : 'products',
            's_categoryid': 0
        })
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('.b-page-ttl h1')
    if (null !== $name) {
        result = {
            breadcrumbs: [],
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $description = document.querySelectorAll('.product_params')
        if (null !== $description) {
            result.description = $description.textContent.trim()
        }
        var $detailGroups = document.querySelectorAll('.b-pr-tech')
        var len = $detailGroups.length 
        if (len) {
            for (var i = 0; i < len; i++) {
                var $group = $detailGroups[i].querySelector('caption.pr-tech_ttl1')
                if (null !== $group) {
                    var $small = $group.querySelector('small')
                    if (null !== $small) {
                        $group.removeChild($small)
                    }
                    var $details = $detailGroups[i].querySelectorAll('tr')
                    var len2 = $details.length 
                    if (len2) {
                        var details = []
                        for (var j = 0; j < len2; j++) {
                            var $name = $details[j].querySelector('th')
                            var $value = $details[j].querySelector('td')
                            if (null !== $name && null !== $value) {
                                details.push({
                                    name: $name.textContent.trim(),
                                    value: $value.textContent.trim(),
                                })
                            }
                        }
                        if (details.length) {
                            result.details.push({
                                name: $group.textContent.trim(),
                                details: details
                            })
                        }
                    } 
                }
            }
        }
    }
    else {
        var $names = document.querySelectorAll('.search-result_body .pr-line_name')
        var len = $names.length
        if (len) {
            for (var i = 0; i < len; i++) {
                if ($names[i].textContent.trim() == data.name) {
                    url = next_url = $names[i].parentNode.getAttribute('href').trim()
                    break
                }
            }
        }
    }
    callback({
        url: url,
        next_url: url,
        data: result
    })
}
module.exports.cookies = []