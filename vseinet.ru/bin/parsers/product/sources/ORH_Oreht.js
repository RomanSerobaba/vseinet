module.exports.url = function(source, data) {
    return data.next_url || 
        source.site + '/modules.php?name=orehtPriceLS&op=Search&stext=' + encodeURIComponent(data.code)  
}
module.exports.parse = function(source, data, callback) {
    var url = window.location.href
    var next_url = null
    var result = {
        breadcrumbs: [],
        name: data.name,
        brand: '',
        model: data.code,
        images: [],
        description: '',
        details: []
    }
    var $image = document.querySelector('#' + data.code + ' img')
    if (null !== $image) {
        result.images.push(source.site + '/' + $image.getAttribute('src').trim())    
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []