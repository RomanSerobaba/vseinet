module.exports.url = function(source, data) {
    return 'http://merlion.com/b2b/' + data.ccode + '/'
}
module.exports.parse = function(source, data, callback) {
    var url = null 
    var next_url = null 
    var result = null 
    var $name = document.querySelector('h1.product')
    if (null !== $name) {
        result = {
            breadcrumbs: [{name: 'Merlion', url: 'merlion'}],
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        url = window.location.href
        var $images = document.querySelectorAll('#item_big_photo img')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
/*                /catalog/product/watermark.php?cat=100&w=1&item=1000308&image=v01_m.jpg
                https://images.merlion.ru/100/1000308/1000308_v01_b.jpg*/
                var $chunks = $images[i].getAttribute('src').trim().replace(/.*\?/ui, '').split('&'),
                    $params = { }
            
                for (var j = 0; j < $chunks.length; j++) {
                    var p = $chunks[j].split('=')
                    $params[p[0]] = p[1]
                }
                result.images.push('https://images.merlion.ru/' + $params.cat + '/' + $params.item + '/' + $params.item + '_' + $params.image.replace(/_m/ui, '_b'))
            }
        }
        var $details = document.querySelectorAll('.property')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                details.push({
                    name: $details[i].textContent.trim(),
                    value: $details[i].nextElementSibling.textContent.trim()
                })
            }
            result.details.push({
                name: 'Основное',
                details: details
            })
        }       
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []
