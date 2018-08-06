module.exports.url = function(source, data) {
    return data.next_url || 
        source.site + '/search/?query=' + encodeURIComponent(data.artikul)  
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $product = document.querySelector('#oneTovTable')
    if (null !== $product) {
        result = {
            name: '',
            brand: '',
            model: data.artikul,
            images: [],
            description: '',
            details: []
        }
        $name = document.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
            var $description = $name.parentNode
            $description.removeChild($name)
            result.description = $description.innerHTML
        }
        var $image = $product.querySelector('#fotocen a')
        if (null !== $image) {
            result.images.push(source.site + '/' + $image.getAttribute('href').trim())
        }
        var $details = $product.querySelector('td[colspan="2"]')
        if (null !== $details) {
            var details = $details.innerHTML.split('<br>')
            var len = details.length
            for (var i = 0; i < len; i++) {
                if (/Производитель:/.test(details[i])) {
                    result.brand = details[i].split(':')[1].trim()
                    break
                }
            }
        }
    }
    else {
        var $links = document.querySelectorAll('.SimplyTable .cat7')
        var len = $links.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var artikul = $links[i].parentNode.previousElementSibling.textContent.trim()
                if (data.artikul == artikul) {
                    url = next_url = source.site + '/' + $links[i].querySelector('a').getAttribute('href').trim()    
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