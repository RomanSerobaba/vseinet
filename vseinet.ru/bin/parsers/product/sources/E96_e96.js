module.exports.url = function(source, data) {
    return data.next_url || data.url || 
        source.site + '/index?Search%5Bq%5D=' + encodeURIComponent(data.name)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('meta[itemprop=name]')
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
        var $images = document.querySelectorAll('.imglisting-list-container a')
        var len = $images.length 
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push($images[i].dataset.href.trim())
            }
        }
        else {
            var $image = document.querySelector('#imglisting img')
            if (null !== $image) {
                result.images.push($image.getAttribute('src').trim())
            }
        }
        var $description = document.querySelector('.description')
        if (null !== $description) {
            result.description = $description.textContent.trim()
        }
        var $detailCols = document.querySelectorAll('.features > div')
        var len = $detailCols.length 
        if (len) {
            for (var c = 0; c < len; c++) {
                var $details = $detailCols[c].children
                var len2 = $details.length 
                if (len2) {
                    var group = ''
                    var details = []
                    for (var i = 0; i < len2; i++) {
                        if ($details[i].classList.contains('feature_item')) {
                            var $name = $details[i].querySelector('.param')
                            var $value = $details[i].querySelector('.value')
                            details.push({
                                name: $name.textContent.trim(), 
                                value: $value.textContent.trim()
                            })    
                        }
                        else {
                            if (details.length) {
                                result.details.push({
                                    name: group, 
                                    details: details 
                                })
                            }
                            group = $details[i].textContent.trim()
                            details = []
                        }
                    }
                    if (group && details.length) {
                        result.details.push({
                            name: group, 
                            details: details 
                        })    
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