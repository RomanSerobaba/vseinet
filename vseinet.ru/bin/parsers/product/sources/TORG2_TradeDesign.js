module.exports.url = function(source, data) {
    return data.next_url || data.url || source.site
}
module.exports.parse = function(source, data, callback) {
    var url = null 
    var next_url = null 
    var result = null 
    var type = 'catalog'
    var $card = document.querySelector('[id^=' + type + '_item_]')
    if (null === $card) {
        type = 'zip'
        $card = document.querySelector('[id^=' + type + '_item_]')
    }
    if (null !== $card) {
        var id = $card.getAttribute('id').replace(type + '_item_', '')
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $breadcrumbs = document.querySelectorAll('.paths a')
        var len = $breadcrumbs.length
        if (len) {
            for (var i = 1; i < len; i++) {
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: source.site + $breadcrumbs[i].getAttribute('href').trim()
                })
            }
        }
        var $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $images = ru.mastudio.IBlockViewer.getById(type + '_item_' + id)
        if (null !== $images) {
            var len = $images.images.length 
            if (len) {
                for (var i = 0; i < len; i++) {
                    var image = $images.images[i].popup
                    if (-1 == image.indexOf(source.site)) {
                        image = source.site + image
                    }
                    result.images.push(image)
                }
            }
        }
        var $details = document.querySelectorAll('.features .line')
        var len = $details.length 
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var $name = $details[i].querySelector('.name')
                var $value = $details[i].querySelector('.value')
                if (null !== $name && null !== $value) {
                    details.push({
                        name: $name.textContent.trim(),
                        value: $value.textContent.trim()
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
        var $tabs = document.querySelectorAll('.tbbpad')
        var len = $tabs.length
        if (len) {
            for (var i = 0; i < len; i++) {
                if ('Описание' == $tabs[i].textContent.trim()) {
                    var num = $tabs[i].nextElementSibling.id.substr(-1) 
                    $description = document.querySelector('#taber_catalog_item_' + id + '_body' + num + ' .textout div')
                    if (null !== $description) {
                        result.description = $description.textContent.trim()
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
    else {
        var payload = new FormData()
        payload.append('index', data.ccode)
        payload.append('token', ru.mastudio.heap.get('token'))
        payload.append('date', (new Date()).toString())
        payload.append('nc', ru.mastudio.random())
        var xhr = new XMLHttpRequest()
        xhr.open('POST', '/kernel/modules/jx.lifesearch.php', true)
        xhr.onreadystatechange = function() {
            if (4 == xhr.readyState && 200 == xhr.status) {
                if (xhr.responseText) {
                    var response = JSON.parse(xhr.responseText)
                    if (response && response.data && response.data.length) {
                        for (var i = 0, len = response.data.length; i < len; i++) {
                            var product = response.data[i]
                            if (-1 < product.name.indexOf(data.ccode + ' ')) {
                                next_url = url = source.site + product.link
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
        }
        xhr.send(payload)
    }
}
module.exports.cookies = []