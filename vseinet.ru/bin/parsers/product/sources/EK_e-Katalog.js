module.exports.url = function(source, data) {
    return data.next_url || data.url || 
        source.site + '/ek-list.php?search_=' + encodeURIComponent(data.name) + '&search_but_='
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('h1[itemprop=name]')
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
        var $image = document.querySelector('.img200 img')
        if (null !== $image) {
            result.images.push(source.site + $image.getAttribute('src').replace('/jpg/', '/jpg_zoom1/'))
        }
        var $images = document.querySelectorAll('.i15-img')
        var len = $images.length 
        if (len) {
            for (var i = 0;  i < len; i ++) {
                var image = $images[i].getAttribute('style')
                if (image) {
                    m = image.match(/\(\"(.+)\"\)/)
                    if (m && m[1]) {
                        result.images.push(m[1].replace('/120/', '/big/'))
                    }
                }
            }
        }
        var $description = document.querySelector('.desc-conf-descr')
        if (null !== $description) {
            result.description = $description.textContent.trim()
        }
        var $details = document.querySelectorAll('.item-block table table tr')
        var len = $details.length 
        if (len) {
            var group = ''
            var details = []
            for (var i = 0; i < len; i++) {
                var $group = $details[i].querySelector('.op1-title')
                if (null !== $group) {
                    if (details.length) {
                        result.details.push({
                            name: group, 
                            details: details 
                        })
                    }
                    group = $group.textContent.trim()
                    details = []
                }
                else { 
                    var $name = $details[i].querySelector('.op1')
                    var $value = $details[i].querySelector('.op3')
                    if (null !== $name && null !== $value) {
                        var value = ''
                        var $plus = $value.querySelector('img')
                        if (null !== $plus && $plus.getAttribute('src').trim() == '/img/icons/bul_141.gif') {
                            value = '+'
                        }
                        else {
                            value = $value.textContent.replace(/&nbps;/g, '').trim()
                        }
                        details.push({
                            name: $name.textContent.trim(), 
                            value: value
                        })
                    }
                }
            }
            if (group && details.length) {
                result.details.push({
                    name: group, 
                    details: details 
                })    
            }
        }
        var $menu = document.querySelectorAll('.desc-menu a')
        var len = $menu.length 
        if (len) {
            for (var i = 0; i < len; i++) {
                if ($menu[i].classList.contains('active') && $menu[i].textContent.trim() == 'Описание') {
                    url = window.location
                    next_url = source.site + $menu[i + 1].getAttribute('href').trim()
                    break
                }
            }
        }
    }
    else {
        var $tags = document.querySelectorAll('.list-filter-param a')
        var len = $tags.length 
        if (len) {
            for (var i = 0; i < len; i++) {
                if ($tags[i].textContent.trim() == data.name) {
                    var $link = document.querySelector('.model-short-title')
                    if (null !== $link) {
                        url = next_url = source.site + $link.getAttribute('href').trim()
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