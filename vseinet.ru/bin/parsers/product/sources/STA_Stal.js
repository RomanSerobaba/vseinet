module.exports.url = function(source, data) {
    return data.next_url || data.url ||
        source.site + '/catalog/'
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    if (null !== document.querySelector('.goods-page')) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $breadcrumbs = document.querySelectorAll('.paginator a')
        var len = $breadcrumbs.length
        if (len) {
            for (var i = 1; i < len; i++) {
                result.breadcrumbs.push({
                    name: $breadcrumbs[i].textContent.trim(),
                    url: source.site + $breadcrumbs[i].getAttribute('href').trim()
                })
            }
        }
        var $name = document.querySelector('.main-title .float-l')
        if (null !== $name) {
            result.name = $name.textContent.trim()
        }
        var $images = document.querySelectorAll('.goods-page-slider-main-img .fancybox')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push(source.site + $images[i].getAttribute('href').trim())
            }
        }
        var $content = document.querySelector('.tabs-content-info')
        if (null !== $content) {
            var $uls = $content.querySelectorAll('ul')
            if ($uls.length) {
                var $details = $uls[0].querySelectorAll('.prop_name')
                var len = $details.length 
                if (len) {
                    for (var i = 0; i < len; i++) {
                        var name = $details[i].textContent.trim().replace(':', '')
                        if ('Бренд' == name) {
                            result.brand = $details[i].nextElementSibling.textContent.trim()
                        }
                    }
                }
                $uls[0].parentNode.removeChild($uls[0])
                if (2 == $uls.length) {
                    var $details = $uls[1].querySelectorAll('li')
                    var len = $details.length
                    if (len) {
                        var details = []
                        for (var i = 0; i < len; i++) {
                            var detail = $details[i].textContent.split(':')
                            if (2 == detail.length) {
                                details.push({
                                    name: detail[0].trim(),
                                    value: detail[1].trim()
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
                     $uls[1].parentNode.removeChild($uls[1])
                }
                var $h3 = $content.querySelector('h3')
                if (null !== $h3) {
                    $h3.parentNode.removeChild($h3)
                }
                var description = $content.textContent.trim()
                if (description) {
                    result.description = description
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
        BX.ready(function() {
            BX.ajax.post('/catalog/', {
                ajax_call: 'y',
                INPUT_ID: 'title-search-input',
                q: data.name,
                l: 2
            }, function(html) {
                var re = /href=\"([^\"]+)\"/g
                var match = re.exec(html)
                if (match && 1 < match.length) {
                    next_url = url = source.site + match[1]
                }    
                callback({
                    url: url,
                    next_url: next_url,
                    data: result
                })
            })
        })        
    }
}
module.exports.cookies = []