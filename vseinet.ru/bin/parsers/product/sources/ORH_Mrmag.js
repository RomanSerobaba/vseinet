module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/search/?q=' + encodeURIComponent(data.name)  
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $breadcrumbs = document.querySelectorAll('.breadcrumb a')
    var len = $breadcrumbs.length
    if (1 < len) {
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
                name: $breadcrumbs[i].textContent.trim(),
                url: source.site + $breadcrumbs[i].getAttribute('href').trim()
            })
        }
        var $name = document.querySelector('.product-heading')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $brand = document.querySelector('.brand-name')
        if (null !== $brand) {
            result.brand = $brand.textContent.trim()
        }
        var $images = document.querySelectorAll('.carousel-indicators img')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
            	var image = $images[i].getAttribute('data-src').trim()
            	if (!/img\/noimg/i.test(image)) {
	                result.images.push(source.site + image)
            	}
            }
        }
        var $description = document.querySelector('.product-descr')
        if (null !== $description) {
            result.description = $description.textContent.replace('Описание:', '').replace(/\s+/g, ' ').replace('Более точную информацию о товаре уточняйте у операторов и консультантов торгового зала. Изображение может отличаться от представленного на витрине.', '').trim()
        }
        var $details = document.querySelectorAll('.specifications-group')
        var len = $details.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var $group = $details[i].querySelector('.heading')
                var group = null !== $group ? $group.textContent.trim() : 'Основные'
                var $rows = $details[i].querySelectorAll('tr')
                var lenrows = $rows.length
                if (lenrows) {
                    var details = []
                    for (var j = 0; j < lenrows; j++) {
                        var $row = $rows[j].querySelectorAll('td')
                        if (2 == $row.length) {
                            details.push({
                                name: $row[0].textContent.trim(),
                                value: $row[1].textContent.trim()
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
        }
    }
    else {
        var $elements = document.querySelectorAll('.price-retail')
        if ($elements.length > 0 && data.code.length > 0) {
            for (var i = 0; i < $elements.length; i++) {
                if ($elements[i].textContent.trim() === data.code.replace(/-/ui, '')) {
                    var $element = $elements[i].closest('.product-item')
                    
                    if (null !== $element) {
                        $a = $element.querySelector('.prod-name a')
                        if (null !== $a) {
                            url = next_url = source.site + $a.getAttribute('href').trim()
                        }
                    }
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